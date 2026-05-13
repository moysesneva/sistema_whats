<?php
session_start();
#print_r($_REQUEST);
#exit();
include 'conn.php';
include 'funcoes.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}

$login = $_SESSION['login'];
#echo $login; 
#print_r($_REQUEST);

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$solicitarConfirmacao = $_POST['mensagemCancelamentoProfissional'];    


    // Prepara a consulta para atualizar os dados com base no usuario_api
    $sql_update = "UPDATE login SET 
                    	cancela_prof = '$solicitarConfirmacao'
                   WHERE login = '$login'";

    if (mysqli_query($conn, $sql_update)) {
        // Redireciona para msg_config.php após atualização
        #header("Location: msg_config.php");
        VaiPara('msg_config.php');
        exit;
    }
}

// Consulta para obter os dados atuais do usuário para exibir no formulário

?>