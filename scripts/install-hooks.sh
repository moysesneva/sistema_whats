#!/bin/bash
# Configura os hooks de Git do projeto apontando core.hooksPath para scripts/.
# Normalmente não é necessário rodar manualmente — o start.sh executa isso
# automaticamente. Use este script apenas em clones externos (fora do Replit):
#   bash scripts/install-hooks.sh

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "Aviso: não é um repositório Git — hooks não configurados." >&2
    exit 0
fi

if ! git config core.hooksPath "$SCRIPT_DIR" 2>/dev/null; then
    echo "Aviso: não foi possível configurar core.hooksPath (ambiente sem permissão de escrita no .git/config — normal em CI/deploy)." >&2
    exit 0
fi

echo "Hooks configurados: core.hooksPath=$SCRIPT_DIR"
echo "O hook scripts/pre-commit será executado automaticamente antes de cada commit."
