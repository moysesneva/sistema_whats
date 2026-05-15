<?php
session_start();
include 'conn.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}

$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $solicitarConfirmacao = $_POST['solicitarConfirmacao'] ?? '';
    if($solicitarConfirmacao == 'nao'){
        $solicitarConfirmacao = 'nao';
        $stmt = $conn->prepare("UPDATE login SET solicitar_confirmacao = ?, agenda_confirma = '', tempo_verifica = '' WHERE login = ?");
        $stmt->bind_param("ss", $solicitarConfirmacao, $login);
        if ($stmt->execute()) {
            $stmt->close();
            VaiPara('msg_config.php');
            exit;
        } else {
            echo "Erro ao atualizar: " . $conn->error;
            $stmt->close();
        }
    }

    $mensagemConfirmacao = $_POST['mensagemConfirmacao'] ?? '';
    $stmt = $conn->prepare("UPDATE login SET agenda_confirma = ? WHERE login = ?");
    $stmt->bind_param("ss", $mensagemConfirmacao, $login);
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
