#!/bin/bash
#
# Executa a limpeza periódica da pasta de uploads temporários.
# Roda em segundo plano a partir do start.sh.
#
# Intervalo configurável via variável de ambiente:
#   UPLOADS_SWEEP_INTERVAL_SECONDS  (padrão: 1200 = 20 minutos)
#
# Threshold de idade configurável via variável de ambiente:
#   UPLOADS_MAX_AGE_SECONDS         (padrão: 3600 = 1 hora, lido pelo script PHP)

INTERVALO="${UPLOADS_SWEEP_INTERVAL_SECONDS:-1200}"
SCRIPT="$(dirname "$0")/../login/painel/api/limpar_uploads.php"
STATUS_FILE="$(dirname "$0")/../login/painel/api/status_limpar_uploads.json"

run_sweep() {
    local ts
    ts="$(date '+%Y-%m-%d %H:%M:%S')"
    local output
    output=$(php "$SCRIPT" 2>&1)
    local removed=0
    # O script PHP emite uma linha "REMOVED_COUNT=N" para leitura estruturada
    local parsed
    parsed=$(echo "$output" | grep -m1 '^REMOVED_COUNT=' | cut -d= -f2)
    if [[ "$parsed" =~ ^[0-9]+$ ]]; then
        removed="$parsed"
    fi
    echo "[$ts] limpar_uploads: varredura concluída ($removed arquivo(s) removido(s))"

    # --- Gravar arquivo de status para o painel ---
    cat > "$STATUS_FILE" <<EOF
{"ultima_varredura":"${ts}","arquivos_removidos":${removed}}
EOF
}

echo "[$(date '+%Y-%m-%d %H:%M:%S')] limpar_uploads: varredura iniciada (intervalo=${INTERVALO}s)"

run_sweep

while true; do
    sleep "$INTERVALO"
    run_sweep
done
