<?php

/**
 * Salva um áudio decodificado de base64 em uma pasta temporária.
 *
 * @param string $audioBase64 O áudio em formato base64.
 * @return string O caminho relativo do arquivo de áudio salvo.
 */
function salvar_audio_temporario($audioBase64) {
    // Decodifica o áudio base64
    $audioDecodificado = base64_decode($audioBase64);

    // Define a pasta onde o áudio será salvo
    $pasta = __DIR__ . '/audio/';
    
    // Cria a pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Gerar um nome de arquivo único
    $nomeArquivo = $pasta . uniqid('audio_', true) . '.wav'; // Você pode ajustar a extensão se souber o formato
    
    // Salva o áudio na pasta
    file_put_contents($nomeArquivo, $audioDecodificado);

    return $nomeArquivo;
}


// Salva o arquivo de áudio temporariamente
$caminho_audio_local = salvar_audio_temporario($audio_recebido);

// Lê o conteúdo do arquivo de áudio e o codifica em Base64 para enviar ao Gemini
$audioParaGeminiBase64 = base64_encode(file_get_contents($caminho_audio_local));

// Define o tipo MIME do áudio (ex: audio/mp3, audio/wav, audio/ogg)
// Você precisará garantir que este MIME type corresponde ao formato real do seu áudio.
// Para este exemplo, estou assumindo 'audio/mp3', mas ajuste conforme necessário.
$mimeTypeAudio = 'audio/mp3'; 

// URL da API Gemini (modelo gemini-1.5-flash para transcrição)
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent?key=" . $chave;

// Dados da requisição para o Gemini
$dados = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "Apenas transcreva o áudio em texto. Não adicione comentários, ruídos, observações ou qualquer informação extra. Somente o que foi falado no áudio." // Seu prompt para o Gemini
                ],
                [
                    "inlineData" => [
                        "mimeType" => $mimeTypeAudio,
                        "data" => $audioParaGeminiBase64
                    ]
                ]
            ]
        ]
    ]
];

// Configuração do cURL para o Gemini
$opcoes = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($dados)
];

// Executar a requisição cURL
$ch = curl_init();
curl_setopt_array($ch, $opcoes);
$response = curl_exec($ch);

// Verificar se houve erro
if (curl_errno($ch)) {
    $msg = 'Erro ao fazer requisição para Gemini: ' . curl_error($ch);
    echo $msg;
} else {
    // Processar a resposta da API
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($codigo_http == 200) {
        $resultado = json_decode($response, true);
        try {
            $transcricao = $resultado['candidates'][0]['content']['parts'][0]['text'];
            $msg =  $transcricao;
            echo $msg;
            
            // Opcional: remover o arquivo de áudio temporário após a transcrição
            unlink($caminho_audio_local);

        } catch (Exception $e) {
            $msg = 'Erro ao processar resposta do Gemini: ' . $e->getMessage();
            echo $msg . ' Resposta completa: ' . $response;
        }
    } else {
        $msg = 'Erro na requisição Gemini (HTTP ' . $codigo_http . '): ' . $response;
        echo $msg;
    }
}

// Fechar a conexão cURL
curl_close($ch);

?>