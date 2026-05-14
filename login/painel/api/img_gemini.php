<?php

/**
 * Salva uma imagem decodificada de base64 em uma pasta local.
 *
 * @param string $imagemBase64 A imagem em formato base64.
 * @return string O caminho relativo da imagem salva.
 */
function salvar_imagem_na_pasta($imagemBase64) {
    $imagemDecodificada = base64_decode($imagemBase64);

    $pasta = __DIR__ . '/img/';

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $nomeArquivo = uniqid('imagem_', true) . '.png';
    $caminhoCompleto = $pasta . $nomeArquivo;

    file_put_contents($caminhoCompleto, $imagemDecodificada);

    return 'img/' . $nomeArquivo;
}

$caminhoRelativo = salvar_imagem_na_pasta($imagemBase64);

$apiKey = $chave;

$caminhoAbsoluto = __DIR__ . '/' . $caminhoRelativo;
$imagemParaGeminiBase64 = base64_encode(file_get_contents($caminhoAbsoluto));

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeTypeImagem = $finfo->file($caminhoAbsoluto);

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent?key=" . $apiKey;

$dados = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "descreva em detalhes imagem é essa?"
                ],
                [
                    "inlineData" => [
                        "mimeType" => $mimeTypeImagem,
                        "data" => $imagemParaGeminiBase64
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
$resposta = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Erro ao fazer requisição para Gemini: ' . curl_error($ch);
} else {
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($codigo_http == 200) {
        $resultado = json_decode($resposta, true);
        try {
            $msg = 'IMG= ' . $resultado['candidates'][0]['content']['parts'][0]['text'];
            echo $msg;
        } catch (Exception $e) {
            $msg = 'Erro ao processar resposta do Gemini: ' . $e->getMessage();
            echo $msg . ' Resposta completa: ' . $resposta;
        }
    } else {
        $msg = 'Erro na requisição Gemini (HTTP ' . $codigo_http . '): ' . $resposta;
        echo $msg;
    }
}

curl_close($ch);

if (file_exists($caminhoAbsoluto)) {
    unlink($caminhoAbsoluto);
}

?>
