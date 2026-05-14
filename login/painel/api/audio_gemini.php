<?php

/**
 * Salva um áudio decodificado de base64 em uma pasta temporária.
 *
 * @param string $audioBase64 O áudio em formato base64.
 * @return string O caminho absoluto do arquivo de áudio salvo.
 */
function salvar_audio_temporario($audioBase64) {
    $audioDecodificado = base64_decode($audioBase64);

    $pasta = __DIR__ . '/audio/';

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $nomeArquivo = $pasta . uniqid('audio_', true);

    file_put_contents($nomeArquivo, $audioDecodificado);

    return $nomeArquivo;
}

$caminho_audio_local = salvar_audio_temporario($audio_recebido);

$audioParaGeminiBase64 = base64_encode(file_get_contents($caminho_audio_local));

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeTypeAudio = $finfo->file($caminho_audio_local);

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent?key=" . $chave;

$dados = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "Apenas transcreva o áudio em texto. Não adicione comentários, ruídos, observações ou qualquer informação extra. Somente o que foi falado no áudio."
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

$opcoes = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($dados)
];

$ch = curl_init();
curl_setopt_array($ch, $opcoes);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $msg = 'Erro ao fazer requisição para Gemini: ' . curl_error($ch);
    echo $msg;
} else {
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($codigo_http == 200) {
        $resultado = json_decode($response, true);
        try {
            $transcricao = $resultado['candidates'][0]['content']['parts'][0]['text'];
            $msg = $transcricao;
            echo $msg;

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

curl_close($ch);

?>
