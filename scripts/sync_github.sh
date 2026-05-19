#!/bin/bash
# sync_github.sh — Sincroniza Replit → GitHub.
#
# Fluxo principal:
#   1. fetch origin/main
#   2. merge origin/main -X theirs --no-edit  (GitHub vence em conflitos)
#   3. push origin main --force-with-lease
#
# O token GitHub pode nao ter scope "workflow". Se o push for recusado por
# modificacao em .github/workflows/, o script encerra com erro explícito.
# Nesse caso, certifique-se de que o token possua o scope "workflow".
#
# Uso: bash scripts/sync_github.sh

set -euo pipefail

export GIT_PAGER=cat
export GIT_TERMINAL_PROMPT=0
export GIT_AUTHOR_NAME="MoysesNet Replit"
export GIT_AUTHOR_EMAIL="replit@moysesnet.com"
export GIT_COMMITTER_NAME="MoysesNet Replit"
export GIT_COMMITTER_EMAIL="replit@moysesnet.com"

REMOTE="origin"
BRANCH="main"
REPO="moysesneva/sistema_whats"
CLEAN_URL="https://github.com/$REPO.git"
CRED_FILE="/tmp/.git-credentials-sync-$$"
SELF="scripts/sync_github.sh"

# Extrai token do remote URL atual
CURRENT_URL=$(git remote get-url "$REMOTE" 2>/dev/null || true)
GH_TOKEN=$(printf '%s' "$CURRENT_URL" | sed -n 's|https://x-access-token:\([^@]*\)@.*|\1|p')

if [ -z "$GH_TOKEN" ]; then
    echo "ERRO: Nenhum token encontrado no remote URL."
    echo "Configure com: git remote set-url origin https://x-access-token:<TOKEN>@github.com/$REPO.git"
    exit 1
fi

# Limpeza garantida ao sair (não usa set -e para não mascarar erros do main flow)
cleanup() {
    rm -f "$CRED_FILE"
    git remote set-url "$REMOTE" "$CURRENT_URL" 2>/dev/null || true
    # Abortar merge pendente se ainda estiver aberto
    if [ -f ".git/MERGE_HEAD" ]; then
        git merge --abort 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo "============================================"
echo "  Sincronizacao GitHub -- MoysesNet"
echo "  https://github.com/$REPO"
echo "  Branch: $BRANCH | $(date '+%d/%m/%Y %H:%M:%S')"
echo "============================================"

# -- 1. Limpar locks residuais -----------------------------------------------
echo ""
echo "--- Limpando estado anterior ---"
rm -f .git/config.lock .git/index.lock .git/ORIG_HEAD \
      .git/MERGE_HEAD.lock .git/packed-refs.lock .git/HEAD.lock 2>/dev/null || true
if [ -f ".git/MERGE_HEAD" ]; then
    git merge --abort 2>/dev/null || true
    echo "  Merge pendente abortado."
fi
echo "  Limpeza concluida."

# Garantir que estamos em main
CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "")
if [ "$CURRENT_BRANCH" != "$BRANCH" ]; then
    git checkout -f "$BRANCH"
fi

# -- 2. Estado atual ----------------------------------------------------------
echo ""
echo "--- Estado atual ---"
LAST_COMMIT=$(git log --oneline -1 2>/dev/null || echo "(nenhum commit)")
echo "Ultimo commit: $LAST_COMMIT"

# -- 3. Credencial temporaria + fetch ----------------------------------------
printf 'https://x-access-token:%s@github.com\n' "$GH_TOKEN" > "$CRED_FILE"
chmod 600 "$CRED_FILE"
git remote set-url "$REMOTE" "$CLEAN_URL"

echo ""
echo "--- Buscando estado do GitHub ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH"

AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
BEHIND=$(git rev-list --count HEAD.."$REMOTE/$BRANCH" 2>/dev/null || echo "?")
echo "Local: $AHEAD commit(s) a frente | $BEHIND commit(s) atras do GitHub"

# -- 4. Merge origin/main preferindo versao do GitHub em conflitos -----------
echo ""
echo "--- Incorporando commit remoto (GitHub vence em conflitos) ---"
if [ "$BEHIND" = "0" ]; then
    echo "  Ja atualizado com o GitHub. Nenhum merge necessario."
else
    git -c "credential.helper=store --file=$CRED_FILE" \
        merge "$REMOTE/$BRANCH" -X theirs --no-edit \
        -m "merge: incorporando commit remoto do GitHub (theirs)"
    echo "  Merge concluido."
fi

# -- 5. Commit de arquivos pendentes (se houver) ------------------------------
if ! git diff --cached --quiet 2>/dev/null; then
    echo ""
    echo "--- Commitando resolucao de conflitos ---"
    git commit --no-verify -m "fix: resolvendo conflitos de merge pre-sync"
fi

# -- 6. Propagar versao do disco do proprio script (idempotente) --------------
git add "$SELF"
if ! git diff --cached --quiet 2>/dev/null; then
    git commit --no-verify -m "chore: atualizar $SELF com versao do disco"
fi

# -- 7. Push para o GitHub ----------------------------------------------------
echo ""
echo "--- Enviando para o GitHub ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    push "$REMOTE" "$BRANCH" --force-with-lease

PUSHED_COMMIT=$(git log --oneline -1)
echo ""
echo "============================================"
echo "  Push concluido com sucesso!"
echo "  Commit: $PUSHED_COMMIT"
echo "  Repositorio: https://github.com/$REPO"
echo "============================================"

# -- 8. Verificacao pos-push --------------------------------------------------
echo ""
echo "--- Verificacao pos-push ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH"

REMAINING_AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
echo "  local/main == origin/main: $(git log --oneline -1)"
echo "  Commits a frente do remote: $REMAINING_AHEAD (deve ser 0)"

if [ "$REMAINING_AHEAD" != "0" ]; then
    echo "AVISO: ainda ha $REMAINING_AHEAD commit(s) a frente do remote."
    exit 1
fi

echo "  Sincronizacao verificada com sucesso."
