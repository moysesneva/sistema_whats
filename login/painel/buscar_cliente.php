<?php
session_start();
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];






include 'conn.php';
include 'config_dados.php';


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $usuario_api  = Priletra($rows_usuarios['usuario_api']);


}

// Obtendo os valores dos parâmetros GET
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';
$telefone = isset($_GET['telefone']) ? $_GET['telefone'] : '';

// Construindo a consulta SQL para buscar os clientes
$sql_busca_clientes = "SELECT * FROM clientes WHERE 1=1 AND usuario_api = '$usuario_api'";

// Adicionando filtros conforme os parâmetros enviados
if (!empty($nome)) {
    $nome = mysqli_real_escape_string($conn, $nome);
    $sql_busca_clientes .= " AND nome LIKE '%$nome%'";
}

if (!empty($telefone)) {
    $telefone = mysqli_real_escape_string($conn, $telefone);
    $sql_busca_clientes .= " AND telefone LIKE '%$telefone%'";
}

$query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);

// Verificando se a consulta retornou algum resultado
if (mysqli_num_rows($query_busca_clientes) > 0) {
    // Exibindo os resultados da busca
    echo "<ul class='list-group'>";
    while ($cliente = mysqli_fetch_assoc($query_busca_clientes)) {
        // Adicionando a função de clique para preencher os campos de nome e telefone
        $nome_cliente = htmlspecialchars($cliente['nome'], ENT_QUOTES);
        $telefone_cliente = htmlspecialchars($cliente['telefone'], ENT_QUOTES);
        echo "<li class='list-group-item' onclick=\"preencherCampos('$nome_cliente', '$telefone_cliente')\">";
        echo "Nome: " . $cliente['nome'] . " - Telefone: " . $cliente['telefone'];
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='alert alert-warning'>Nenhum cliente encontrado.</div>";
}
?>
