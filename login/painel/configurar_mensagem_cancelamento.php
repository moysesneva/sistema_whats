<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'conn.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}

$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensagemCancelamento = $_POST['mensagemCancelamento'] ?? '';
    $stmt = $conn->prepare("UPDATE login SET agenda_cancela = ? WHERE login = ?");
    $stmt->bind_param("ss", $mensagemCancelamento, $login);
    if ($stmt->execute()) {
        $stmt->close();
        VaiPara('msg_config.php');
        exit;
    } else {
        echo "Erro ao atualizar: " . $conn->error;
        $stmt->close();
    }
}

?>
