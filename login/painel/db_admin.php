<?php
session_start();
if (!isset($_SESSION['login']) || !isset($_SESSION['autorizado']) || $_SESSION['autorizado'] != 2) {
    header('Location: login_adm.php');
    exit();
}
include 'conn.php';

$msg = '';
$resultado = null;
$colunas = [];
$query_exec = '';

$tables_res = mysqli_query($conn, "SHOW TABLES;");
$tabelas = [];
while ($row = mysqli_fetch_array($tables_res, MYSQLI_NUM)) {
    $tabelas[] = $row[0];
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
        $colunas = [];
        $fields = mysqli_fetch_fields($resultado);
        foreach ($fields as $f) $colunas[] = $f->name;
        mysqli_data_seek($resultado, 0);
    }
}

$tabela_sel = '';
if (!empty($_GET['tabela'])) {
    $tabela_sel = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['tabela']);
    $query_exec = "SELECT * FROM `{$tabela_sel}` LIMIT 200";
    $res = mysqli_query($conn, $query_exec);
    if ($res) {
        $resultado = $res;
        $colunas = [];
        $fields = mysqli_fetch_fields($resultado);
        foreach ($fields as $f) $colunas[] = $f->name;
        mysqli_data_seek($resultado, 0);
    }
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
.sidebar { width: 210px; background: #001225; padding: 16px 0; border-right: 1px solid #0a3060; flex-shrink: 0; overflow-y: auto; }
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
th { background: #0a2a50; color: #FF5500; padding: 8px 10px; text-align: left; white-space: nowrap; border-bottom: 1px solid #0a3060; position: sticky; top: 0; }
td { padding: 6px 10px; border-bottom: 1px solid #081830; vertical-align: top; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
tr:hover td { background: #0a2040; }
.badge { display: inline-block; background: #0a3060; color: #aaa; font-size: 11px; padding: 2px 7px; border-radius: 10px; margin-top: 6px; }
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
                <textarea name="query" rows="5" placeholder="SELECT * FROM login LIMIT 10;"><?= htmlspecialchars($query_exec) ?></textarea>
                <button class="btn" type="submit">Executar</button>
            </form>
        </div>

        <?php if ($msg): ?>
            <div class="msg <?= str_starts_with($msg, 'ERRO') ? 'err' : 'ok' ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="box">
                <h2>Resultado <?php if($tabela_sel): ?><span class="badge"><?= htmlspecialchars($tabela_sel) ?></span><?php endif; ?></h2>
                <div class="tbl-wrap">
                    <table>
                        <thead><tr><?php foreach ($colunas as $c): ?><th><?= htmlspecialchars($c) ?></th><?php endforeach; ?></tr></thead>
                        <tbody>
                        <?php
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($resultado)):
                            $count++;
                        ?>
                            <tr><?php foreach ($colunas as $c): ?><td title="<?= htmlspecialchars((string)$row[$c]) ?>"><?= htmlspecialchars((string)$row[$c]) ?></td><?php endforeach; ?></tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="badge" style="margin-top:10px"><?= $count ?> linha(s)</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
