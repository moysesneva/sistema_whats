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

echo "[$(date '+%Y-%m-%d %H:%M:%S')] limpar_uploads: varredura iniciada (intervalo=${INTERVALO}s)"

php "$SCRIPT"

while true; do
    sleep "$INTERVALO"
    php "$SCRIPT"
done
