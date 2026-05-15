<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
if (!isset($_SESSION['login']) || !isset($_SESSION['autorizado']) || $_SESSION['autorizado'] != 2) {
    header('Location: login_adm.php');
    exit();
}
include 'conn.php';

// ── AJAX: atualizar célula ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar_celula') {
    header('Content-Type: application/json');
    $tabela = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['tabela'] ?? '');
    $pk_col = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['pk_col'] ?? '');
    $pk_val = $_POST['pk_val'] ?? '';
    $coluna = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['coluna'] ?? '');
    $valor  = $_POST['valor'] ?? '';

    if (!$tabela || !$pk_col || !$coluna) {
        echo json_encode(['ok' => false, 'erro' => 'Dados incompletos']);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE `{$tabela}` SET `{$coluna}` = ? WHERE `{$pk_col}` = ?");
    mysqli_bind_param($stmt, 'ss', $valor, $pk_val);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['ok' => true, 'afetadas' => mysqli_stmt_affected_rows($stmt)]);
    } else {
        echo json_encode(['ok' => false, 'erro' => mysqli_stmt_error($stmt)]);
    }
    mysqli_stmt_close($stmt);
    exit();
}

// ── AJAX: deletar linha ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'deletar_linha') {
    header('Content-Type: application/json');
    $tabela = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['tabela'] ?? '');
    $pk_col = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['pk_col'] ?? '');
    $pk_val = $_POST['pk_val'] ?? '';

    if (!$tabela || !$pk_col) {
        echo json_encode(['ok' => false, 'erro' => 'Dados incompletos']);
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM `{$tabela}` WHERE `{$pk_col}` = ?");
    mysqli_bind_param($stmt, 's', $pk_val);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'erro' => mysqli_stmt_error($stmt)]);
    }
    mysqli_stmt_close($stmt);
    exit();
}

// ── Página normal ──────────────────────────────────────────────────────────
$msg = '';
$resultado = null;
$colunas = [];
$query_exec = '';
$pk_col = '';
$tabela_sel = '';

$tables_res = mysqli_query($conn, "SHOW TABLES;");
$tabelas = [];
while ($row = mysqli_fetch_array($tables_res, MYSQLI_NUM)) {
    $tabelas[] = $row[0];
}

// Descobrir PK de uma tabela
function descobrir_pk($conn, $tabela) {
    $res = mysqli_query($conn, "SHOW KEYS FROM `{$tabela}` WHERE Key_name = 'PRIMARY'");
    if ($res && $row = mysqli_fetch_assoc($res)) return $row['Column_name'];
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['query'])) {
    $query_exec = trim($_POST['query']);
    $res = mysqli_query($conn, $query_exec);
    if ($res === false) {
        $msg = 'ERRO: ' . mysqli_error($conn);
    } elseif ($res === true) {
        $affected = mysqli_affected_rows($conn);
        $msg = "OK — {$affected} linha(s) afetada(s).";
    } else {
        $resultado = $res;
        $fields = mysqli_fetch_fields($resultado);
        foreach ($fields as $f) $colunas[] = $f->name;
        mysqli_data_seek($resultado, 0);
    }
}

if (!empty($_GET['tabela'])) {
    $tabela_sel = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['tabela']);
    $pk_col = descobrir_pk($conn, $tabela_sel) ?? '';
    $query_exec = "SELECT * FROM `{$tabela_sel}` LIMIT 200";
    $res = mysqli_query($conn, $query_exec);
    if ($res) {
        $resultado = $res;
        $fields = mysqli_fetch_fields($resultado);
        foreach ($fields as $f) $colunas[] = $f->name;
        mysqli_data_seek($resultado, 0);
    }
}

$linhas = [];
if ($resultado) {
    while ($row = mysqli_fetch_assoc($resultado)) $linhas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>DB Admin — MoysesNet</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #001f3f; color: #e0e0e0; min-height: 100vh; }
header { background: #001225; padding: 14px 24px; display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #FF5500; }
header h1 { font-size: 18px; color: #FF5500; }
header a { color: #aaa; font-size: 13px; text-decoration: none; }
header a:hover { color: #FF5500; }
.layout { display: flex; min-height: calc(100vh - 55px); }
.sidebar { width: 210px; background: #001225; padding: 16px 0; border-right: 1px solid #0a3060; flex-shrink: 0; overflow-y: auto; max-height: calc(100vh - 55px); position: sticky; top: 0; }
.sidebar h3 { font-size: 11px; text-transform: uppercase; color: #FF5500; padding: 0 16px 8px; letter-spacing: 1px; }
.sidebar a { display: block; padding: 7px 16px; font-size: 13px; color: #ccc; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sidebar a:hover, .sidebar a.ativo { background: #0a2a50; color: #FF5500; }
.main { flex: 1; padding: 24px; overflow-x: auto; }
.box { background: #001225; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #0a3060; }
.box h2 { font-size: 14px; color: #FF5500; margin-bottom: 12px; }
textarea { width: 100%; background: #001f3f; color: #e0e0e0; border: 1px solid #0a4080; border-radius: 4px; padding: 10px; font-family: monospace; font-size: 13px; resize: vertical; }
.btn { background: #FF5500; color: #fff; border: none; padding: 9px 22px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-top: 8px; }
.btn:hover { background: #e04a00; }
.msg { padding: 10px 14px; border-radius: 4px; margin-bottom: 14px; font-size: 13px; }
.msg.ok { background: #0a3020; border: 1px solid #1a6040; color: #6deba0; }
.msg.err { background: #3a0a0a; border: 1px solid #6a1a1a; color: #ff8080; }
.tbl-wrap { overflow-x: auto; }
table { border-collapse: collapse; font-size: 12px; width: 100%; }
th { background: #0a2a50; color: #FF5500; padding: 8px 10px; text-align: left; white-space: nowrap; border-bottom: 1px solid #0a3060; position: sticky; top: 0; z-index: 2; }
td { padding: 0; border-bottom: 1px solid #081830; vertical-align: middle; max-width: 260px; }
td .cell-inner { padding: 6px 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer; display: block; min-height: 30px; }
td .cell-inner:hover { background: #0a2040; }
td input.edit-input { width: 100%; background: #001f3f; color: #fff; border: 1px solid #FF5500; padding: 5px 8px; font-size: 12px; outline: none; display: none; }
td.editing .cell-inner { display: none; }
td.editing input.edit-input { display: block; }
.btn-del { background: none; border: none; color: #ff4444; cursor: pointer; font-size: 14px; padding: 4px 8px; opacity: 0.5; }
.btn-del:hover { opacity: 1; }
.badge { display: inline-block; background: #0a3060; color: #aaa; font-size: 11px; padding: 2px 7px; border-radius: 10px; }
.pk-badge { background: #FF5500; color: #fff; font-size: 10px; padding: 1px 5px; border-radius: 3px; margin-left: 4px; vertical-align: middle; }
.toast { position: fixed; bottom: 24px; right: 24px; background: #0a3020; border: 1px solid #1a6040; color: #6deba0; padding: 10px 18px; border-radius: 6px; font-size: 13px; display: none; z-index: 999; }
.toast.err { background: #3a0a0a; border-color: #6a1a1a; color: #ff8080; }
</style>
</head>
<body>
<header>
    <h1>&#128447; DB Admin — MoysesNet</h1>
    <a href="config_adm.php?pagina_nome=1">&#8592; Voltar ao Painel</a>
</header>
<div class="layout">
    <div class="sidebar">
        <h3>Tabelas</h3>
        <?php foreach ($tabelas as $t): ?>
            <a href="?tabela=<?= urlencode($t) ?>" class="<?= ($tabela_sel === $t ? 'ativo' : '') ?>">
                <?= htmlspecialchars($t) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="main">
        <div class="box">
            <h2>&#9998; Executar SQL</h2>
            <form method="POST">
                <textarea name="query" rows="4" placeholder="SELECT * FROM login LIMIT 10;"><?= htmlspecialchars($query_exec) ?></textarea>
                <button class="btn" type="submit">Executar</button>
            </form>
        </div>

        <?php if ($msg): ?>
            <div class="msg <?= str_starts_with($msg, 'ERRO') ? 'err' : 'ok' ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if (count($linhas) > 0): ?>
            <div class="box">
                <h2>
                    <?php if ($tabela_sel): ?>
                        Tabela: <span class="badge"><?= htmlspecialchars($tabela_sel) ?></span>
                        <?php if ($pk_col): ?><span style="font-size:11px;color:#888;margin-left:8px">PK: <b style="color:#FF5500"><?= htmlspecialchars($pk_col) ?></b> — clique numa célula para editar</span><?php endif; ?>
                    <?php else: ?>
                        Resultado da query
                    <?php endif; ?>
                </h2>
                <div class="tbl-wrap">
                    <table id="tabela-dados"
                        data-tabela="<?= htmlspecialchars($tabela_sel) ?>"
                        data-pk="<?= htmlspecialchars($pk_col) ?>">
                        <thead>
                            <tr>
                                <?php foreach ($colunas as $c): ?>
                                    <th><?= htmlspecialchars($c) ?><?= ($c === $pk_col) ? '<span class="pk-badge">PK</span>' : '' ?></th>
                                <?php endforeach; ?>
                                <?php if ($tabela_sel && $pk_col): ?><th style="width:36px"></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($linhas as $row): ?>
                            <tr data-pk-val="<?= htmlspecialchars((string)($row[$pk_col] ?? '')) ?>">
                                <?php foreach ($colunas as $c): ?>
                                    <td data-col="<?= htmlspecialchars($c) ?>" data-orig="<?= htmlspecialchars((string)$row[$c]) ?>">
                                        <span class="cell-inner" title="<?= htmlspecialchars((string)$row[$c]) ?>"><?= htmlspecialchars((string)$row[$c]) ?></span>
                                        <input class="edit-input" type="text" value="<?= htmlspecialchars((string)$row[$c]) ?>">
                                    </td>
                                <?php endforeach; ?>
                                <?php if ($tabela_sel && $pk_col): ?>
                                    <td style="max-width:36px;text-align:center">
                                        <button class="btn-del" title="Deletar linha" onclick="deletarLinha(this)">&#10005;</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="badge" style="margin-top:10px"><?= count($linhas) ?> linha(s)</div>
            </div>
        <?php elseif ($tabela_sel || ($_SERVER['REQUEST_METHOD'] === 'POST' && !$msg)): ?>
            <div class="msg ok">Nenhum resultado.</div>
        <?php endif; ?>
    </div>
</div>
<div class="toast" id="toast"></div>

<script>
const tabelaDados = document.getElementById('tabela-dados');
const tabela = tabelaDados ? tabelaDados.dataset.tabela : '';
const pkCol  = tabelaDados ? tabelaDados.dataset.pk : '';

function showToast(msg, err = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (err ? ' err' : '');
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.style.display = 'none', 2800);
}

// Edição inline
document.querySelectorAll('td[data-col]').forEach(td => {
    if (!pkCol) return;
    const col  = td.dataset.col;
    const inner = td.querySelector('.cell-inner');
    const input = td.querySelector('.edit-input');
    const tr    = td.closest('tr');

    inner.addEventListener('click', () => {
        td.classList.add('editing');
        input.focus();
        input.select();
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter')  salvarCelula(td, tr, col, input);
        if (e.key === 'Escape') cancelarEdicao(td, input);
    });

    input.addEventListener('blur', () => {
        if (input.value !== td.dataset.orig) {
            salvarCelula(td, tr, col, input);
        } else {
            cancelarEdicao(td, input);
        }
    });
});

function cancelarEdicao(td, input) {
    input.value = td.dataset.orig;
    td.classList.remove('editing');
}

function salvarCelula(td, tr, col, input) {
    const pkVal = tr.dataset.pkVal;
    const novoVal = input.value;

    fetch('db_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ acao: 'editar_celula', tabela, pk_col: pkCol, pk_val: pkVal, coluna: col, valor: novoVal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            td.dataset.orig = novoVal;
            td.querySelector('.cell-inner').textContent = novoVal;
            td.querySelector('.cell-inner').title = novoVal;
            td.classList.remove('editing');
            showToast('Salvo!');
        } else {
            showToast('Erro: ' + data.erro, true);
            cancelarEdicao(td, input);
        }
    })
    .catch(() => { showToast('Falha na conexão', true); cancelarEdicao(td, input); });
}

function deletarLinha(btn) {
    const tr = btn.closest('tr');
    const pkVal = tr.dataset.pkVal;
    if (!confirm('Deletar este registro (PK=' + pkVal + ')?')) return;

    fetch('db_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ acao: 'deletar_linha', tabela, pk_col: pkCol, pk_val: pkVal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) { tr.remove(); showToast('Linha deletada!'); }
        else showToast('Erro: ' + data.erro, true);
    })
    .catch(() => showToast('Falha na conexão', true));
}
</script>
</body>
</html>
