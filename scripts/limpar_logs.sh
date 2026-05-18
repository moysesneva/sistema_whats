#!/bin/bash
#
# Remove arquivos de log antigos para evitar consumo excessivo de disco.
# Roda em segundo plano a partir do start.sh.
#
# Variáveis de ambiente configuráveis:
#   LOG_SWEEP_INTERVAL_SECONDS   — intervalo entre varreduras             (padrão: 86400 = 24 h)
#   LOG_MAX_AGE_DAYS             — máximo de dias para log diário         (padrão: 7)
#   LOG_MAX_SIZE_MB              — tamanho máximo de log único (MB)       (padrão: 10)
#   DB_FAILURES_MAX_SIZE_MB      — tamanho máximo de db_failures.log (MB) (padrão: 1)
#   DB_FAILURES_MAX_AGE_DAYS     — máximo de dias para entradas JSONL     (padrão: 30)

# --- Validação numérica: garante valores inteiros positivos ---
_valid_int() {
    local val="$1" default="$2"
    if [[ "$val" =~ ^[0-9]+$ ]] && [ "$val" -gt 0 ]; then
        echo "$val"
    else
        echo "$default"
    fi
}

# --- Lê overrides do JSON gravado pelo painel (env var > JSON > padrão) ---
CLEANUP_JSON="$(dirname "$0")/../login/painel/api/cleanup_config.json"

_json_val() {
    local key="$1" default="$2"
    if [ -f "$CLEANUP_JSON" ] && command -v grep >/dev/null 2>&1; then
        local v
        v=$(grep -o "\"${key}\":[[:space:]]*[0-9]*" "$CLEANUP_JSON" 2>/dev/null | grep -o '[0-9]*$')
        if [[ "$v" =~ ^[0-9]+$ ]] && [ "$v" -gt 0 ]; then
            echo "$v"
            return
        fi
    fi
    echo "$default"
}

# JSON (banco) > env var > padrão embutido
# (valor salvo no painel tem prioridade sobre variável de ambiente)
_resolve_thr() {
    local envVal="$1" jsonKey="$2" default="$3"
    local jsonVal
    jsonVal=$(_json_val "$jsonKey" "")
    if [[ "$jsonVal" =~ ^[0-9]+$ ]] && [ "$jsonVal" -gt 0 ]; then
        echo "$jsonVal"
    elif [[ "$envVal" =~ ^[0-9]+$ ]] && [ "$envVal" -gt 0 ]; then
        echo "$envVal"
    else
        echo "$default"
    fi
}

INTERVALO=$(_valid_int "${LOG_SWEEP_INTERVAL_SECONDS}" 86400)
MAX_DAYS=$(_resolve_thr "${LOG_MAX_AGE_DAYS}" "log_max_age_days" 7)
MAX_MB=$(_resolve_thr "${LOG_MAX_SIZE_MB}" "log_max_size_mb" 10)
MAX_BYTES=$(( MAX_MB * 1024 * 1024 ))

DB_FAILURES_MAX_MB=$(_resolve_thr "${DB_FAILURES_MAX_SIZE_MB}" "db_failures_max_mb" 1)
DB_FAILURES_MAX_BYTES=$(( DB_FAILURES_MAX_MB * 1024 * 1024 ))
DB_FAILURES_MAX_AGE=$(_resolve_thr "${DB_FAILURES_MAX_AGE_DAYS}" "db_failures_max_days" 30)

BASE="$(dirname "$0")/../login/painel/api"
PAINEL_BASE="$(dirname "$0")/../login/painel"
LOGS_DIR="$BASE/logs"
SINGLE_LOGS=(
    "$BASE/log_processamento.txt"
    "$BASE/log_recebidos.txt"
)
DB_FAILURES_LOG="$PAINEL_BASE/logs/db_failures.log"
ADMIN_ACTIONS_LOG="$LOGS_DIR/admin_actions.log"
STATUS_FILE="$BASE/status_limpar_logs.json"

run_sweep() {
    local ts
    ts="$(date '+%Y-%m-%d %H:%M:%S')"
    local removed=0
    local truncados=0
    local db_failures_action="none"

    # --- Apagar arquivos de log diários mais antigos que LOG_MAX_AGE_DAYS ---
    if [ -d "$LOGS_DIR" ]; then
        removed=$(find "$LOGS_DIR" -maxdepth 1 -type f \
            \( -name "*.log" -o -name "*.txt" \) \
            -mtime +"$MAX_DAYS" -print -delete 2>/dev/null | wc -l)
        echo "[$ts] limpar_logs: $removed arquivo(s) antigo(s) removido(s) de logs/ (threshold=${MAX_DAYS}d)"
    fi

    # --- Truncar logs únicos que ultrapassaram LOG_MAX_SIZE_MB ---
    for f in "${SINGLE_LOGS[@]}"; do
        if [ -f "$f" ]; then
            local size
            size=$(wc -c < "$f" 2>/dev/null || echo 0)
            if [ "$size" -gt "$MAX_BYTES" ]; then
                : > "$f"
                truncados=$(( truncados + 1 ))
                echo "[$ts] limpar_logs: $(basename "$f") truncado (era ${size} bytes, limite=${MAX_MB}MB)"
            fi
        fi
    done

    # --- Rotacionar db_failures.log: truncar se > limite de tamanho, senão filtrar entradas antigas ---
    if [ -f "$DB_FAILURES_LOG" ]; then
        local df_size
        df_size=$(wc -c < "$DB_FAILURES_LOG" 2>/dev/null || echo 0)
        if [ "$df_size" -gt "$DB_FAILURES_MAX_BYTES" ]; then
            : > "$DB_FAILURES_LOG"
            truncados=$(( truncados + 1 ))
            db_failures_action="truncado"
            echo "[$ts] limpar_logs: db_failures.log truncado (era ${df_size} bytes, limite=${DB_FAILURES_MAX_MB}MB)"
        else
            # Filtrar entradas JSONL mais antigas que DB_FAILURES_MAX_AGE_DAYS
            local tmp_file="${DB_FAILURES_LOG}.tmp"
            if DAYS="$DB_FAILURES_MAX_AGE" perl -ne '
                BEGIN {
                    use POSIX qw(mktime);
                    $cutoff = time() - ($ENV{DAYS} * 86400);
                }
                if (/"ts":"(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})"/) {
                    my $epoch = mktime($6+0, $5+0, $4+0, $3+0, $2-1, $1-1900);
                    print if $epoch >= $cutoff;
                } else {
                    print;
                }
            ' "$DB_FAILURES_LOG" > "$tmp_file" 2>/dev/null && mv "$tmp_file" "$DB_FAILURES_LOG"; then
                db_failures_action="filtrado"
                echo "[$ts] limpar_logs: db_failures.log filtrado (entradas > ${DB_FAILURES_MAX_AGE}d removidas)"
            else
                rm -f "$tmp_file"
                db_failures_action="erro_filtragem"
                echo "[$ts] limpar_logs: AVISO — falha ao filtrar db_failures.log (arquivo mantido intacto)"
            fi
        fi
    fi

    # --- Truncar admin_actions.log se ultrapassar LOG_MAX_SIZE_MB ---
    if [ -f "$ADMIN_ACTIONS_LOG" ]; then
        local aa_size
        aa_size=$(wc -c < "$ADMIN_ACTIONS_LOG" 2>/dev/null || echo 0)
        if [ "$aa_size" -gt "$MAX_BYTES" ]; then
            : > "$ADMIN_ACTIONS_LOG"
            truncados=$(( truncados + 1 ))
            echo "[$ts] limpar_logs: admin_actions.log truncado (era ${aa_size} bytes, limite=${MAX_MB}MB)"
        fi
    fi

    # --- Gravar arquivo de status para o painel ---
    cat > "$STATUS_FILE" <<EOF
{"ultima_varredura":"${ts}","arquivos_removidos":${removed},"truncamentos":${truncados},"max_age_dias":${MAX_DAYS},"max_size_mb":${MAX_MB},"db_failures_action":"${db_failures_action}","db_failures_max_size_mb":${DB_FAILURES_MAX_MB},"db_failures_max_age_dias":${DB_FAILURES_MAX_AGE}}
EOF
}

echo "[$(date '+%Y-%m-%d %H:%M:%S')] limpar_logs: varredura iniciada (intervalo=${INTERVALO}s, max_age=${MAX_DAYS}d, max_size=${MAX_MB}MB, db_failures_max=${DB_FAILURES_MAX_MB}MB/${DB_FAILURES_MAX_AGE}d)"

run_sweep

while true; do
    sleep "$INTERVALO"
    run_sweep
done
