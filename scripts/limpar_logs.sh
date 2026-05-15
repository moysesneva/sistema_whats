#!/bin/bash
#
# Remove arquivos de log antigos para evitar consumo excessivo de disco.
# Roda em segundo plano a partir do start.sh.
#
# Variáveis de ambiente configuráveis:
#   LOG_SWEEP_INTERVAL_SECONDS  — intervalo entre varreduras       (padrão: 86400 = 24 h)
#   LOG_MAX_AGE_DAYS            — máximo de dias para log diário   (padrão: 7)
#   LOG_MAX_SIZE_MB             — tamanho máximo de log único (MB) (padrão: 10)

# --- Validação numérica: garante valores inteiros positivos ---
_valid_int() {
    local val="$1" default="$2"
    if [[ "$val" =~ ^[0-9]+$ ]] && [ "$val" -gt 0 ]; then
        echo "$val"
    else
        echo "$default"
    fi
}

INTERVALO=$(_valid_int "${LOG_SWEEP_INTERVAL_SECONDS}" 86400)
MAX_DAYS=$(_valid_int "${LOG_MAX_AGE_DAYS}" 7)
MAX_MB=$(_valid_int "${LOG_MAX_SIZE_MB}" 10)
MAX_BYTES=$(( MAX_MB * 1024 * 1024 ))

BASE="$(dirname "$0")/../login/painel/api"
LOGS_DIR="$BASE/logs"
SINGLE_LOGS=(
    "$BASE/log_processamento.txt"
    "$BASE/log_recebidos.txt"
)
STATUS_FILE="$BASE/status_limpar_logs.json"

run_sweep() {
    local ts
    ts="$(date '+%Y-%m-%d %H:%M:%S')"
    local removed=0
    local truncados=0

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

    # --- Gravar arquivo de status para o painel ---
    cat > "$STATUS_FILE" <<EOF
{"ultima_varredura":"${ts}","arquivos_removidos":${removed},"truncamentos":${truncados},"max_age_dias":${MAX_DAYS},"max_size_mb":${MAX_MB}}
EOF
}

echo "[$(date '+%Y-%m-%d %H:%M:%S')] limpar_logs: varredura iniciada (intervalo=${INTERVALO}s, max_age=${MAX_DAYS}d, max_size=${MAX_MB}MB)"

run_sweep

while true; do
    sleep "$INTERVALO"
    run_sweep
done
