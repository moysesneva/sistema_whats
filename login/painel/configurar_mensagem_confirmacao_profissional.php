<?php
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}

$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $confirma_prof = $_POST['mensagemConfirmacaoProfissional'] ?? '';
    $stmt = $conn->prepare("UPDATE login SET confirma_prof = ? WHERE login = ?");
    $stmt->bind_param("ss", $confirma_prof, $login);
    if ($stmt->execute()) {
        $stmt->close();
        VaiPara('msg_config.php');
        exit;
    }
    $stmt->close();
}

?>
