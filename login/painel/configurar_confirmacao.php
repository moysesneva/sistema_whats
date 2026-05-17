<?php
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}


function removerCaracteresEspeciais($string) {
    $regex = '/[^\p{L}\p{N}\p{P}\p{Z}]/u';
    return preg_replace($regex, '', $string);
}
$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $solicitarConfirmacao = $_POST['solicitarConfirmacao'] ?? 'nao';
    $mensagemEnquete = $_POST['mensagemEnquete'] ?? '';
    $tempoAntesAgendamento = (int)($_POST['tempoAntesAgendamento'] ?? 0);
    $mensagemEnquete = removerCaracteresEspeciais($mensagemEnquete);

    if ($solicitarConfirmacao == 'nao') {
        $stmt = $conn->prepare("UPDATE login SET solicitar_confirmacao = 'nao', agenda_confirma = '', tempo_verifica = 0 WHERE login = ?");
        $stmt->bind_param("s", $login);
    } else {
        $stmt = $conn->prepare("UPDATE login SET solicitar_confirmacao = 'sim', agenda_verfica = ?, tempo_verifica = ? WHERE login = ?");
        $stmt->bind_param("sis", $mensagemEnquete, $tempoAntesAgendamento, $login);
    }

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
