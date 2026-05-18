#!/bin/bash
# sync_github.sh — Sincroniza o Replit -> GitHub preservando .github/ do remote.
#
# Estrategia: branch temporario a partir do remote/main.
# scripts/sync_github.sh e excluido de AMBOS os passos de remocao e checkout
# para que a versao do disco (em execucao) seja sempre propagada ao commit de sync.
# Apos o push, reseta local/main para origin/main eliminando divergencia.
#
# Por que NAO fetch+merge+push direto:
#   Token nao tem scope "workflow". Push de commits que ADICIONEM ou MODIFIQUEM
#   .github/workflows/ e recusado pela API do GitHub. Branch temp reutiliza os
#   blobs de .github/ ja existentes no remote -- GitHub nao detecta modificacao.
#
# Uso: bash scripts/sync_github.sh

set -uo pipefail

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
SELF="scripts/sync_github.sh"

# Extrai token do remote URL atual
CURRENT_URL=$(git remote get-url "$REMOTE" 2>/dev/null || true)
GH_TOKEN=$(printf '%s' "$CURRENT_URL" | sed -n 's|https://x-access-token:\([^@]*\)@.*|\1|p')

if [ -z "$GH_TOKEN" ]; then
    echo "ERRO: Nenhum token encontrado no remote URL."
    echo "Configure com: git remote set-url origin https://x-access-token:<TOKEN>@github.com/$REPO.git"
    exit 1
fi

# Limpeza garantida ao sair
cleanup() {
    rm -f "$CRED_FILE"
    if [ "$(git branch --show-current 2>/dev/null)" != "$BRANCH" ]; then
        git checkout -f "$BRANCH" 2>/dev/null || true
    fi
    git branch -D "$TEMP_BRANCH" 2>/dev/null || true
    git remote set-url "$REMOTE" "$CURRENT_URL" 2>/dev/null || true
}
trap cleanup EXIT

echo "============================================"
echo "  Sincronizacao GitHub -- MoysesNet"
echo "  https://github.com/$REPO"
echo "  Branch: $BRANCH | $(date '+%d/%m/%Y %H:%M:%S')"
echo "============================================"

# -- 1. Limpar estado anterior ------------------------------------------------
echo ""
echo "--- Limpando estado anterior ---"
rm -f .git/config.lock .git/index.lock .git/ORIG_HEAD.lock \
      .git/MERGE_HEAD.lock .git/COMMIT_EDITMSG.lock \
      .git/packed-refs.lock .git/HEAD.lock 2>/dev/null || true

if [ -f ".git/MERGE_HEAD" ]; then
    git merge --abort 2>/dev/null || true
    echo "  Merge pendente abortado."
fi

git branch 2>/dev/null | grep "temp-gh-sync-" | while read -r b; do
    git branch -D "$b" 2>/dev/null || true
done

git stash list 2>/dev/null | grep "sync-stash-before-merge" | while read -r entry; do
    REF=$(echo "$entry" | cut -d: -f1)
    git stash drop "$REF" 2>/dev/null || true
    echo "  Stash antigo removido: $REF"
done
echo "  Limpeza concluida."

# -- 2. Auto-resolver conflitos no indice (exceto .github/) ------------------
CONFLICTED=$(git ls-files -u 2>/dev/null | cut -f2 | sort -u | grep -v "^\.github/")
if [ -n "$CONFLICTED" ]; then
    echo ""
    echo "--- Auto-resolvendo conflitos no indice (exceto .github/) ---"
    echo "$CONFLICTED" | while read -r f; do
        [ -z "$f" ] && continue
        if [ -f "$f" ]; then
            git add "$f" 2>/dev/null && echo "  resolvido: $f" || echo "  erro: $f"
        else
            git rm --cached -f -- "$f" 2>/dev/null && echo "  removido do indice: $f" || true
        fi
    done
fi

CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "")
if [ "$CURRENT_BRANCH" != "$BRANCH" ]; then
    git checkout -f "$BRANCH" || { echo "ERRO: nao foi possivel voltar para $BRANCH"; exit 1; }
fi

# -- 3. Commit de arquivos em staging apos resolucao -------------------------
if ! git diff --cached --quiet 2>/dev/null; then
    echo ""
    echo "--- Commitando resolucao de conflitos ---"
    git commit --no-verify -m "fix: resolvendo conflitos de merge pre-sync" 2>&1 || true
fi

# -- 4. Estado atual ----------------------------------------------------------
echo ""
echo "--- Estado atual ---"
LAST_COMMIT=$(git log --oneline -1 2>/dev/null || echo "(nenhum commit)")
echo "Ultimo commit: $LAST_COMMIT"

# -- 5. Credencial temporaria e fetch ----------------------------------------
printf 'https://x-access-token:%s@github.com\n' "$GH_TOKEN" > "$CRED_FILE"
chmod 600 "$CRED_FILE"
git remote set-url "$REMOTE" "$CLEAN_URL"

echo ""
echo "--- Buscando estado do GitHub ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH" 2>&1

AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
BEHIND=$(git rev-list --count HEAD.."$REMOTE/$BRANCH" 2>/dev/null || echo "?")
echo "Local: $AHEAD commit(s) a frente | $BEHIND commit(s) atras do GitHub"

# -- 6. Branch temporario a partir do remote (preserva .github/ identico) ----
echo ""
echo "--- Preparando commit de sync (preservando .github/ do GitHub) ---"
git checkout -b "$TEMP_BRANCH" "$REMOTE/$BRANCH"

# -- 7. Remover arquivos do indice (exceto .github/ e este script) ------------
# IMPORTANTE: excluir $SELF aqui para que o arquivo no disco nao seja apagado
# antes de ser staged no passo 8.
echo "  Removendo arquivos antigos do indice..."
REMOVE_COUNT=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    [ "$f" = "$SELF" ] && continue
    git rm --cached -q -f -- "$f" 2>/dev/null && rm -f -- "$f" 2>/dev/null || true
    REMOVE_COUNT=$((REMOVE_COUNT + 1))
done < <(git ls-tree -r --name-only HEAD 2>/dev/null | grep -v "^\.github/" || true)
echo "  $REMOVE_COUNT arquivo(s) removido(s) do indice."

# -- 8. Aplicar arquivos do branch local main (exceto .github/ e este script) -
# IMPORTANTE: excluir $SELF aqui para preservar a versao do disco (em execucao).
echo "  Aplicando arquivos do HEAD local..."
ADD_COUNT=0
ADD_ERRORS=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    [ "$f" = "$SELF" ] && continue
    if git checkout "$BRANCH" -- "$f" 2>/dev/null; then
        ADD_COUNT=$((ADD_COUNT + 1))
    else
        ADD_ERRORS=$((ADD_ERRORS + 1))
    fi
done < <(git ls-tree -r --name-only "$BRANCH" 2>/dev/null | grep -v "^\.github/" || true)
# Propaga a versao do disco (versao em execucao pelo bash) ao commit de sync.
git add "$SELF" 2>/dev/null && echo "  script propagado do disco: $SELF" || true
echo "  $ADD_COUNT arquivo(s) aplicado(s) | $ADD_ERRORS erro(s) ignorado(s)."

# -- 9. Stage e commit de sync ------------------------------------------------
git add -A
STAGED=$(git diff --cached --name-only 2>/dev/null | wc -l | tr -d ' ')
echo "  $STAGED arquivo(s) no stage."

SYNC_MSG="GitHub sync: $(echo "$LAST_COMMIT" | cut -d' ' -f2-)"
git commit --no-verify --allow-empty -m "$SYNC_MSG"
SYNC_COMMIT=$(git log --oneline -1)
echo "Commit de sync: $SYNC_COMMIT"

# -- 10. Push para o GitHub ---------------------------------------------------
echo ""
echo "--- Enviando para o GitHub ---"
if git -c "credential.helper=store --file=$CRED_FILE" \
    push --force-with-lease "$REMOTE" "$TEMP_BRANCH:$BRANCH" 2>&1; then
    echo ""
    echo "============================================"
    echo "  Push concluido com sucesso!"
    echo "  Commit: $SYNC_COMMIT"
    echo "  Repositorio: https://github.com/$REPO"
    echo "============================================"
else
    echo ""
    echo "ERRO: Push falhou. Verifique os logs acima."
    exit 1
fi

# -- 11. Sincronizar local/main com remote (elimina divergencia) --------------
echo ""
echo "--- Sincronizando branch local com remote ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH" 2>&1 || true
if [ "$(git branch --show-current 2>/dev/null)" != "$BRANCH" ]; then
    git checkout -f "$BRANCH" 2>/dev/null || true
fi
if git merge --ff-only "$REMOTE/$BRANCH" 2>/dev/null; then
    echo "  local/main avancado para $(git log --oneline -1) (fast-forward)"
else
    git reset --hard "$REMOTE/$BRANCH" 2>/dev/null && \
        echo "  local/main resetado para $(git log --oneline -1) (reset hard)" || \
        echo "  aviso: nao foi possivel sincronizar local com remote"
fi
echo "  local/main == origin/main: $(git log --oneline -1)"
FINAL_AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
echo "  Commits a frente do remote: $FINAL_AHEAD (deve ser 0)"
# cleanup via trap EXIT
