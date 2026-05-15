<?php
session_start();
include 'conn.php';
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


#print_r($_REQUEST);
include 'config_dados.php';
// Incluindo a conexão com o banco de dados


if (isset($_GET['id'])) {
    // Obtendo o ID da chave a ser deletada
    $id_chave = $_GET['id'];

    // Verificação básica para garantir que o ID é um número
    if (is_numeric($id_chave)) {
        // Consulta SQL para deletar a chave com base no ID
        $sql_deletar_chave = "DELETE FROM chave WHERE id = '$id_chave'";
        $resultado_delecao = mysqli_query($conn, $sql_deletar_chave);

        // Verificando se a deleção foi bem-sucedida
        if ($resultado_delecao) {
            // Redireciona de volta para a página da lista de chaves com uma mensagem de sucesso
            VaiPara('chave_ia.php?mensagem=deletado');
        } else {
            // Redireciona de volta para a página da lista de chaves com uma mensagem de erro
            VaiPara('chave_ia.php?mensagem=erro');
        }
    } else {
        // Redireciona se o ID não for um valor válido
        VaiPara('chave_ia.php?mensagem=erro');
    }
} else {
    // Se o ID não foi passado, redireciona de volta para a página principal
    VaiPara('chave_ia.php');
}
?>

