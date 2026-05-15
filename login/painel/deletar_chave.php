<?php
session_start();
include 'conn.php';
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
$login = $_SESSION['login'];

include 'config_dados.php';

if (isset($_GET['id'])) {
    $id_chave = (int)$_GET['id'];

    if ($id_chave > 0) {
        $stmt = $conn->prepare("DELETE FROM chave WHERE id = ?");
        $stmt->bind_param("i", $id_chave);
        $resultado_delecao = $stmt->execute();
        $stmt->close();

        if ($resultado_delecao) {
            VaiPara('chave_ia.php?mensagem=deletado');
        } else {
            VaiPara('chave_ia.php?mensagem=erro');
        }
    } else {
        VaiPara('chave_ia.php?mensagem=erro');
    }
} else {
    VaiPara('chave_ia.php');
}
?>

