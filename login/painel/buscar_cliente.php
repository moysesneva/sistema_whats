<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';

$stmt_user = mysqli_prepare($conn, "SELECT usuario_api FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_user, "s", $login);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

if (mysqli_num_rows($result_user) == 0) {
    VaiPara('login.php');
}

$row_user = mysqli_fetch_assoc($result_user);
$usuario_api = $row_user['usuario_api'];

$nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';

$sql_base = "SELECT * FROM clientes WHERE usuario_api = ?";
$params = [$usuario_api];
$types = "s";

if (!empty($nome)) {
    $sql_base .= " AND nome LIKE ?";
    $params[] = '%' . $nome . '%';
    $types .= "s";
}

if (!empty($telefone)) {
    $sql_base .= " AND telefone LIKE ?";
    $params[] = '%' . $telefone . '%';
    $types .= "s";
}

$stmt = mysqli_prepare($conn, $sql_base);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$query_busca_clientes = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($query_busca_clientes) > 0) {
    echo "<ul class='list-group'>";
    while ($cliente = mysqli_fetch_assoc($query_busca_clientes)) {
        $nome_cliente = htmlspecialchars($cliente['nome'], ENT_QUOTES);
        $telefone_cliente = htmlspecialchars($cliente['telefone'], ENT_QUOTES);
        echo "<li class='list-group-item' onclick=\"preencherCampos('$nome_cliente', '$telefone_cliente')\">";
        echo "Nome: " . htmlspecialchars($cliente['nome']) . " - Telefone: " . htmlspecialchars($cliente['telefone']);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='alert alert-warning'>Nenhum cliente encontrado.</div>";
}
?>
