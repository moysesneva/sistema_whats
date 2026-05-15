<?php
include 'editacodigo.php';

// === Dados principais ===
$servidor  = '206.183.131.225';
$porta     = '443';
$user_id   = 'agenda_553184767331';
$token     = 'NOME_Victor_Teste_CPF_010101';
$telefone  = '553184767330';
$id_msg    = '123';
$msg       = 'OI TESTE';

// === Função para formatar texto da enquete ===
function formatarTexto($texto) {
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    $texto .= "\nSim\nNão";
    return $texto;
}

// === Verifica qual ação foi requisitada ===
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];

    if ($acao == 'mensagem') {
        $resposta = enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg);
        echo "<p><strong>Resposta da função enviarMensagem:</strong> $resposta</p>";
    }

    if ($acao == 'enquete') {
        $textoEnquete = formatarTexto('Você confirma o agendamento?');
        $resposta = EscreverEnquete($servidor, $porta, $user_id, $token, $telefone, $textoEnquete, $id_msg);
        echo "<p><strong>Resposta da função EscreverEnquete:</strong> $resposta</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Teste de Funções WhatsApp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        button {
            padding: 15px 30px;
            margin: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Teste de Envio WhatsApp</h1>

    <form method="get">
        <button type="submit" name="acao" value="mensagem">Enviar Mensagem</button>
        <button type="submit" name="acao" value="enquete">Enviar Enquete</button>
    </form>

</body>
</html>
