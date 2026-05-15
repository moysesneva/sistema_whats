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
    $cancela_prof = $_POST['mensagemCancelamentoProfissional'] ?? '';
    $stmt = $conn->prepare("UPDATE login SET cancela_prof = ? WHERE login = ?");
    $stmt->bind_param("ss", $cancela_prof, $login);
    if ($stmt->execute()) {
        $stmt->close();
        VaiPara('msg_config.php');
        exit;
    }
    $stmt->close();
}

?>
