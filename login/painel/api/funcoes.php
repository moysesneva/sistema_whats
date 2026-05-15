<?php

function substituirEnv($token,$token_novo, $porta_nova, $servidor_novo,$vps) {
    // URL da API para substituir os valores no .env
    $url = $vps . '/substituir_env';

    // Dados a serem enviados no formato JSON
    $dados = [
        'token_recebido' => $token,
        'token_novo' => $token_novo,
        'porta_nova' => $porta_nova,
        'servidor_novo' => $servidor_novo
    ];

    // Inicializa o cURL
    $ch = curl_init($url);

    // Configurações do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

    // Define o cabeçalho Content-Type como application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Executa a requisição e armazena a resposta
    $resposta = curl_exec($ch);

    // Verifica se houve erro
    if (curl_errno($ch)) {
        $erro = 'Erro: ' . curl_error($ch);
        curl_close($ch);
        return $erro;
    } else {
        curl_close($ch);
        return $resposta;
    }
}

// Exemplo de uso



function salvar_dados_resquest() {

// Nome do arquivo onde os dados serão salvos
$filename = 'post.txt';

// Verifica se a requisição é POST e se contém dados JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados JSON do corpo da requisição POST
    $jsonData = file_get_contents('php://input');

    // Decodifica o JSON para um array associativo
    $decodedData = json_decode($jsonData, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Se a decodificação for bem-sucedida, verifica se há dados na chave "opcoes"
        if (isset($decodedData['opcoes'])) {
            // Decodifica a string JSON dentro de 'opcoes' duas vezes para resolver a questão de caracteres escapados
            $decodedOpcoes = json_decode($decodedData['opcoes'], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                // Substitui a string escapada pelo array decodificado
                $decodedData['opcoes'] = $decodedOpcoes;
            } else {
                // Se a decodificação falhar, mantém o valor original
                $decodedData['opcoes'] = $decodedData['opcoes'];
            }
        }

        // Formata os dados como JSON para salvar
        $data = json_encode([
            'GET' => $_GET,
            'POST' => $decodedData
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        // Se a decodificação falhar, armazena os dados brutos
        $data = json_encode([
            'GET' => $_GET,
            'POST' => $_POST
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} else {
    // Se não for um POST, processa GET e POST normalmente
    $data = json_encode([
        'GET' => $_GET,
        'POST' => $_POST
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Salva os dados no arquivo 'qrcode.txt' com codificação UTF-8
file_put_contents($filename, $data);

// Mensagem de confirmação
echo "Dados salvos em $filename com sucesso!";

}




?>
