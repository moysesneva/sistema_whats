#!/bin/bash
# sync_github.sh — Sincroniza o Replit → GitHub preservando .github/ do remote.
#
# Estratégia: branch temporário a partir do remote/main.
# Não usa stash — evita conflito auto-referencial com o próprio script.
# Conflitos pré-existentes no índice são resolvidos automaticamente.
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

# Extrai token do remote URL atual
CURRENT_URL=$(git remote get-url "$REMOTE" 2>/dev/null || true)
GH_TOKEN=$(printf '%s' "$CURRENT_URL" | sed -n 's|https://x-access-token:\([^@]*\)@.*|\1|p')

if [ -z "$GH_TOKEN" ]; then
    echo "ERRO: Nenhum token encontrado no remote URL."
    echo "Configure com: git remote set-url origin https://x-access-token:<TOKEN>@github.com/$REPO.git"
    exit 1
fi

# Limpeza garantida ao sair (sempre volta para main e apaga branch temp)
cleanup() {
    rm -f "$CRED_FILE"
    git checkout -f "$BRANCH" 2>/dev/null || true
    git branch -D "$TEMP_BRANCH" 2>/dev/null || true
    git remote set-url "$REMOTE" "$CURRENT_URL" 2>/dev/null || true
}
trap cleanup EXIT

echo "============================================"
echo "  Sincronização GitHub — MoysesNet"
echo "  https://github.com/$REPO"
echo "  Branch: $BRANCH | $(date '+%d/%m/%Y %H:%M:%S')"
echo "============================================"

# ── 1. Limpar estado anterior ─────────────────────────────────────────────────
echo ""
echo "--- Limpando estado anterior ---"
rm -f .git/config.lock .git/index.lock .git/ORIG_HEAD.lock \
      .git/MERGE_HEAD.lock .git/COMMIT_EDITMSG.lock \
      .git/packed-refs.lock .git/HEAD.lock 2>/dev/null || true

# Abortar merge em andamento
if [ -f ".git/MERGE_HEAD" ]; then
    git merge --abort 2>/dev/null || true
    echo "  Merge pendente abortado."
fi

# Remover branches temporários de syncs anteriores
git branch 2>/dev/null | grep "temp-gh-sync-" | while read -r b; do
    git branch -D "$b" 2>/dev/null || true
done

# Dropar stashes deixados por syncs anteriores (evita pop-conflict em próxima execução)
git stash list 2>/dev/null | grep "sync-stash-before-merge" | while read -r entry; do
    REF=$(echo "$entry" | cut -d: -f1)
    git stash drop "$REF" 2>/dev/null || true
    echo "  Stash antigo removido: $REF"
done

# ── 2. Auto-resolver conflitos pré-existentes no índice ───────────────────────
CONFLICTED=$(git ls-files -u 2>/dev/null | cut -f2 | sort -u)
if [ -n "$CONFLICTED" ]; then
    echo ""
    echo "--- Auto-resolvendo conflitos no índice (versão do disco) ---"
    echo "$CONFLICTED" | while read -r f; do
        [ -z "$f" ] && continue
        if [ -f "$f" ]; then
            git add "$f" 2>/dev/null && echo "  ✔ resolvido: $f" || echo "  ✗ erro ao resolver: $f"
        else
            git rm --cached -f -- "$f" 2>/dev/null && echo "  ✔ removido: $f" || true
        fi
    done
fi

# Garantir que estamos no branch main
CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "")
if [ "$CURRENT_BRANCH" != "$BRANCH" ]; then
    git checkout -f "$BRANCH" || { echo "ERRO: não foi possível voltar para $BRANCH"; exit 1; }
fi

# ── 3. Commit de arquivos ainda em staging (pós-resolução de conflitos) ───────
if ! git diff --cached --quiet 2>/dev/null; then
    echo ""
    echo "--- Commitando resolução de conflitos ---"
    git commit --no-verify -m "fix: resolvendo conflitos de merge pré-sync" 2>&1 || true
fi

# ── 4. Estado atual ───────────────────────────────────────────────────────────
echo ""
echo "--- Estado atual ---"
LAST_COMMIT=$(git log --oneline -1 2>/dev/null || echo "(nenhum commit)")
echo "Último commit: $LAST_COMMIT"

UNCOMMITTED=$(git diff --name-only HEAD 2>/dev/null | wc -l | tr -d ' ')
echo "Alterações não commitadas no disco: $UNCOMMITTED arquivo(s)"

# ── 5. Credencial temporária e fetch ──────────────────────────────────────────
printf 'https://x-access-token:%s@github.com\n' "$GH_TOKEN" > "$CRED_FILE"
chmod 600 "$CRED_FILE"
git remote set-url "$REMOTE" "$CLEAN_URL"

echo ""
echo "--- Buscando estado do GitHub ---"
git -c "credential.helper=store --file=$CRED_FILE" \
    fetch "$REMOTE" "$BRANCH" 2>&1

AHEAD=$(git rev-list --count "$REMOTE/$BRANCH"..HEAD 2>/dev/null || echo "?")
BEHIND=$(git rev-list --count HEAD.."$REMOTE/$BRANCH" 2>/dev/null || echo "?")
echo "Local: $AHEAD commit(s) à frente | $BEHIND commit(s) atrás do GitHub"

# ── 6. Criar branch temporário a partir do remote (preserva .github/) ─────────
echo ""
echo "--- Preparando commit (preservando .github/ do GitHub) ---"
git checkout -b "$TEMP_BRANCH" "$REMOTE/$BRANCH"

# ── 7. Remover tudo que não é .github/ do índice do branch temp ───────────────
echo "  Removendo arquivos antigos do índice..."
REMOVE_COUNT=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    git rm --cached -q -f -- "$f" 2>/dev/null && rm -f -- "$f" 2>/dev/null || true
    REMOVE_COUNT=$((REMOVE_COUNT + 1))
done < <(git ls-tree -r --name-only HEAD 2>/dev/null | grep -v "^\.github/" || true)
echo "  $REMOVE_COUNT arquivo(s) removido(s) do índice."

# ── 8. Aplicar arquivos do branch local main (exceto .github/) ─────────────────
echo "  Aplicando arquivos do HEAD local..."
ADD_COUNT=0
ADD_ERRORS=0
while IFS= read -r f; do
    [ -z "$f" ] && continue
    if git checkout "$BRANCH" -- "$f" 2>/dev/null; then
        ADD_COUNT=$((ADD_COUNT + 1))
    else
        ADD_ERRORS=$((ADD_ERRORS + 1))
    fi
done < <(git ls-tree -r --name-only "$BRANCH" 2>/dev/null | grep -v "^\.github/" || true)
echo "  $ADD_COUNT arquivo(s) aplicado(s) | $ADD_ERRORS erro(s) ignorado(s)."

# ── 9. Incluir alterações não commitadas do disco (exceto scripts de sync) ────
if [ "$UNCOMMITTED" -gt 0 ] 2>/dev/null; then
    echo "  Aplicando $UNCOMMITTED alteração(ões) não commitada(s) do disco..."
    git diff --name-only HEAD 2>/dev/null | while read -r f; do
        [ -z "$f" ] && continue
        [ "$f" = "scripts/sync_github.sh" ] && continue
        if [ -f "$f" ]; then
            cp "$f" "/tmp/sync_patch_$(basename "$f")" 2>/dev/null || true
            git checkout "$BRANCH" -- "$f" 2>/dev/null || true
            cp "/tmp/sync_patch_$(basename "$f")" "$f" 2>/dev/null || true
            git add "$f" 2>/dev/null || true
        fi
    done
fi

# ── 10. Stage e commit ────────────────────────────────────────────────────────
git add -A
STAGED=$(git diff --cached --name-only 2>/dev/null | wc -l | tr -d ' ')
echo "  $STAGED arquivo(s) no stage."

SYNC_MSG="GitHub sync: $(echo "$LAST_COMMIT" | cut -d' ' -f2-)"
git commit --no-verify --allow-empty -m "$SYNC_MSG"

SYNC_COMMIT=$(git log --oneline -1)
echo "Commit de sync: $SYNC_COMMIT"

# ── 11. Push ──────────────────────────────────────────────────────────────────
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
