#!/bin/bash
# sync_github.sh — Sincroniza arquivos do Replit → GitHub preservando .github/ remoto.
#
# Estratégia: parte do remote/main (que contém .github/ criado via UI do GitHub),
# aplica por cima todos os arquivos locais (exceto .github/), e envia como commit
# normal — GitHub não exige scope "workflow" para arquivos .github/ não alterados.
# Alterações não commitadas no disco (ex: CRLF fix) são incluídas via git stash.
#
# Uso: bash scripts/sync_github.sh

set -uo pipefail   # sem -e: erros de pathspec (nomes com espaços) não abortam

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
CRED_FILE="/tmp/.git-credentials"
TEMP_BRANCH="temp-gh-sync-$$"
HAS_STASH=false

# ── Extrai token do remote URL atual ──────────────────────────────────────────
CURRENT_URL=$(git remote get-url "$REMOTE" 2>/dev/null || true)
GH_TOKEN=$(printf '%s' "$CURRENT_URL" | sed -n 's|https://x-access-token:\([^@]*\)@.*|\1|p')

if [ -z "$GH_TOKEN" ]; then
    echo "ERRO: Nenhum token encontrado no remote URL."
    echo "Configure com: git remote set-url origin https://x-access-token:<TOKEN>@github.com/$REPO.git"
    exit 1
fi

# ── Limpeza garantida ao sair ─────────────────────────────────────────────────
cleanup() {
    rm -f "$CRED_FILE"
    git checkout -f "$BRANCH" 2>/dev/null || true
    git branch -D "$TEMP_BRANCH" 2>/dev/null || true
    git remote set-url "$REMOTE" "$CURRENT_URL" 2>/dev/null || true
    if [ "$HAS_STASH" = "true" ]; then
        git stash pop 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo "============================================"
echo "  Sincronização GitHub — MoysesNet"
echo "  https://github.com/$REPO"
echo "  Branch: $BRANCH | $(date '+%d/%m/%Y %H:%M:%S')"
echo "============================================"

# ── Remove locks residuais ────────────────────────────────────────────────────
rm -f .git/config.lock .git/index.lock 2>/dev/null || true
git branch | grep "temp-gh-sync-" | xargs -r git branch -D 2>/dev/null || true

# ── Garante branch main ───────────────────────────────────────────────────────
CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || true)
if [ "$CURRENT_BRANCH" != "$BRANCH" ]; then
    git checkout -f "$BRANCH" || { echo "ERRO: não foi possível voltar para $BRANCH"; exit 1; }
fi

# ── Credencial temporária ─────────────────────────────────────────────────────
printf 'https://x-access-token:%s@github.com\n' "$GH_TOKEN" > "$CRED_FILE"
chmod 600 "$CRED_FILE"
git remote set-url "$REMOTE" "$CLEAN_URL"

# ── Informação do commit atual ────────────────────────────────────────────────
echo ""
echo "--- Estado atual ---"
LAST_COMMIT=$(git log --oneline -1)
echo "Último commit local: $LAST_COMMIT"

# Conta alterações não commitadas no disco
UNCOMMITTED=$(git diff --name-only HEAD 2>/dev/null | wc -l | tr -d ' ')
echo "Alterações não commitadas no disco: $UNCOMMITTED arquivo(s)"

# ── 1. Stash de alterações não commitadas (para não bloquear checkout) ─────────
if ! git diff --quiet HEAD 2>/dev/null || ! git diff --cached --quiet 2>/dev/null; then
    echo "Stashando alterações do disco..."
    git stash push --include-untracked -m "gh-sync-$$" && HAS_STASH=true || true
fi

# ── 2. Fetch do remote ────────────────────────────────────────────────────────
echo ""
echo "--- Buscando estado do GitHub ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH" 2>&1

AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
BEHIND=$(git rev-list --count HEAD.."$REMOTE/$BRANCH" 2>/dev/null || echo "?")
echo "Local: $AHEAD commit(s) à frente | $BEHIND commit(s) atrás do GitHub"

# ── 3. Cria branch temporário a partir do remote (preserva .github/ remoto) ───
echo ""
echo "--- Preparando commit (preservando .github/ do GitHub) ---"
git checkout -b "$TEMP_BRANCH" "$REMOTE/$BRANCH"

# ── 4. Remove arquivos do remote que não são .github/ ─────────────────────────
echo "  Removendo arquivos antigos do índice..."
REMOVE_COUNT=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    git rm --cached -q -f -- "$f" 2>/dev/null && rm -f -- "$f" 2>/dev/null || true
    REMOVE_COUNT=$((REMOVE_COUNT + 1))
done < <(git ls-tree -r --name-only HEAD | grep -v "^\.github/" || true)
echo "  $REMOVE_COUNT arquivo(s) removido(s) do índice."

# ── 5. Aplica arquivos do branch local main (exceto .github/) ─────────────────
echo "  Aplicando arquivos do commit local..."
ADD_COUNT=0
ADD_ERRORS=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    if git checkout "$BRANCH" -- "$f" 2>/dev/null; then
        ADD_COUNT=$((ADD_COUNT + 1))
    else
        ADD_ERRORS=$((ADD_ERRORS + 1))
    fi
done < <(git ls-tree -r --name-only "$BRANCH" | grep -v "^\.github/" || true)
echo "  $ADD_COUNT aplicado(s) | $ADD_ERRORS erro(s) de pathspec ignorado(s)."

# ── 6. Aplica alterações não commitadas (stash pop) ───────────────────────────
# Isso inclui: correções CRLF, novo sync_github.sh, etc.
if [ "$HAS_STASH" = "true" ]; then
    echo "  Restaurando alterações do disco (stash)..."
    git stash pop 2>/dev/null && HAS_STASH=false || echo "  aviso: stash pop com conflito (continuando)"
fi

# ── 7. Stage tudo e faz o commit ──────────────────────────────────────────────
git add -A
STAGED=$(git diff --cached --name-only 2>/dev/null | wc -l | tr -d ' ')
echo "  $STAGED arquivo(s) no stage."

SYNC_MSG="GitHub sync: $(echo "$LAST_COMMIT" | cut -d' ' -f2-)"
if [ "$UNCOMMITTED" -gt 0 ] 2>/dev/null; then
    SYNC_MSG="$SYNC_MSG (+$UNCOMMITTED alterações do disco)"
fi
git commit --no-verify --allow-empty -m "$SYNC_MSG"

SYNC_COMMIT=$(git log --oneline -1)
echo "Commit de sync: $SYNC_COMMIT"

# ── 8. Push ────────────────────────────────────────────────────────────────────
echo ""
echo "--- Enviando para o GitHub ---"
if git -c "credential.helper=store --file=$CRED_FILE" \
    push --force-with-lease "$REMOTE" "$TEMP_BRANCH:$BRANCH" 2>&1; then
    echo ""
    echo "============================================"
    echo "  Push concluído com sucesso!"
    echo "  Commit: $SYNC_COMMIT"
    echo "  Repositório: https://github.com/$REPO"
    echo "============================================"
else
    echo ""
    echo "ERRO: Push falhou. Verifique os logs acima."
    exit 1
fi
# cleanup via trap EXIT
