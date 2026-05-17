#!/bin/bash
# sync_github.sh — Sincroniza o repositório local com o GitHub
# Uso: bash scripts/sync_github.sh
#
# Estratégia: force-with-lease push
#   O repositório local tem 167+ commits do projeto real.
#   O GitHub pode ter commits de origem diferente (histórico não relacionado).
#   Nesse caso, fazemos push forçado (--force-with-lease) para garantir que
#   o GitHub receba o estado correto do código.

set -euo pipefail

# Desabilita paginador git (evita travar em `less` sem TTY)
export GIT_PAGER=cat
export GIT_TERMINAL_PROMPT=0

# Identidade para commits automáticos
export GIT_AUTHOR_NAME="MoysesNet Replit"
export GIT_AUTHOR_EMAIL="replit@moysesnet.com"
export GIT_COMMITTER_NAME="MoysesNet Replit"
export GIT_COMMITTER_EMAIL="replit@moysesnet.com"

REMOTE="origin"
BRANCH="main"

echo "============================================"
echo "  Sincronização GitHub — MoysesNet"
echo "  $(git remote get-url $REMOTE | sed 's/x-access-token:[^@]*@//')"
echo "  Branch: $BRANCH | $(date '+%d/%m/%Y %H:%M:%S')"
echo "============================================"

# 1. Limpar qualquer estado de merge/cherry-pick pendente
if [ -f ".git/MERGE_HEAD" ]; then
    echo "AVISO: Merge pendente detectado — abortando merge anterior..."
    git merge --abort 2>/dev/null || true
    echo "Merge anterior abortado."
fi

if [ -f ".git/CHERRY_PICK_HEAD" ]; then
    git cherry-pick --abort 2>/dev/null || true
fi

# 2. Verificar arquivos em conflito e limpar index
CONFLICTS=$(git --no-optional-locks diff --name-only --diff-filter=U 2>/dev/null | wc -l)
if [ "$CONFLICTS" -gt 0 ]; then
    echo "AVISO: $CONFLICTS arquivo(s) com conflito no index — restaurando versão local..."
    git checkout HEAD -- . 2>/dev/null || git restore --staged . 2>/dev/null || true
fi

# 3. Status do branch
echo ""
echo "--- Último commit local ---"
git log --oneline -1

# 4. Buscar estado do remote (sem integrar)
echo ""
echo "--- Buscando estado do GitHub ---"
git fetch "$REMOTE" "$BRANCH" 2>&1

AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
BEHIND=$(git rev-list --count HEAD.."$REMOTE/$BRANCH" 2>/dev/null || echo "?")

echo "Local: $AHEAD commit(s) à frente | $BEHIND commit(s) atrás do GitHub"

# 5. Push com force-with-lease
echo ""
echo "--- Enviando commits para o GitHub ---"
echo "  Usando --force-with-lease para sobrescrever histórico incompatível..."

if git push "$REMOTE" "$BRANCH" --force-with-lease 2>&1; then
    echo ""
    echo "============================================"
    echo "  Push concluído com sucesso!"
    echo "  Commit enviado: $(git log --oneline -1)"
    echo "============================================"
else
    # Fallback: se force-with-lease falhar (ex: remote atualizou entre fetch e push),
    # tenta push normal primeiro
    echo "  force-with-lease falhou. Tentando push normal..."
    if git push "$REMOTE" "$BRANCH" 2>&1; then
        echo "Push normal concluído."
    else
        echo ""
        echo "ERRO: Push falhou. Verifique as credenciais e tente novamente."
        exit 1
    fi
fi
