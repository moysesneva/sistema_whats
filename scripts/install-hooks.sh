#!/bin/bash
# Instala os hooks de Git do projeto no clone local.
# Execute uma vez após clonar o repositório:
#   bash scripts/install-hooks.sh

set -e

HOOKS_DIR="$(git rev-parse --git-dir)/hooks"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

install_hook() {
    local name="$1"
    local src="$SCRIPT_DIR/$name"
    local dst="$HOOKS_DIR/$name"

    if [ ! -f "$src" ]; then
        echo "Aviso: $src não encontrado, ignorado."
        return
    fi

    cp "$src" "$dst"
    chmod +x "$dst"
    echo "Hook instalado: $dst"
}

install_hook "pre-commit"

echo "Hooks instalados com sucesso."
