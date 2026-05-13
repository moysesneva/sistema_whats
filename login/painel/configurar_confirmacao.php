<?php
session_start();
include 'conn.php';
include 'funcoes.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit;
}


function removerCaracteresEspeciais($string) {
    // Expressão regular para manter apenas caracteres alfanuméricos, espaços, pontuações básicas
    $regex = '/[^\p{L}\p{N}\p{P}\p{Z}]/u';
    
    // Substituir os caracteres que não correspondem ao regex por uma string vazia
    return preg_replace($regex, '', $string);
}
$login = $_SESSION['login']; // Pega o login do usuário da sessão

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $solicitarConfirmacao = $_POST['solicitarConfirmacao'] ?? 'nao';
    $mensagemEnquete = $_POST['mensagemEnquete'] ?? '';
    $tempoAntesAgendamento = $_POST['tempoAntesAgendamento'] ?? 0;
$mensagemEnquete = removerCaracteresEspeciais($mensagemEnquete);
    // Define a consulta SQL com base no valor de solicitarConfirmacao
    if ($solicitarConfirmacao == 'nao') {
        // Configuração para "não"
        $sql_update = "UPDATE login SET 
                        solicitar_confirmacao = 'nao',
                        agenda_confirma = '',
                        tempo_verifica = 0
                       WHERE login = '$login'";
    } else {
        // Configuração para "sim"
        $sql_update = "UPDATE login SET 
                        solicitar_confirmacao = 'sim',
                        agenda_verfica = '$mensagemEnquete',
                        tempo_verifica = $tempoAntesAgendamento
                       WHERE login = '$login'";
    }

    // Executa a atualização e verifica o resultado
    if (mysqli_query($conn, $sql_update)) {
        // Redireciona para msg_config.php após atualização
        VaiPara('msg_config.php');
        exit;
    } else {
        echo "Erro ao atualizar: " . mysqli_error($conn);
    }
}
?>
