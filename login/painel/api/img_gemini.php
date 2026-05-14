<?php

/**
 * Salva uma imagem decodificada de base64 em uma pasta local.
 *
 * @param string $imagemBase64 A imagem em formato base64.
 * @return string O caminho relativo da imagem salva.
 */
function salvar_imagem_na_pasta($imagemBase64) {
    // Decodifica a imagem base64
    $imagemDecodificada = base64_decode($imagemBase64);

    // Define a pasta onde a imagem será salva
    $pasta = __DIR__ . '/img/';
    
    // Cria a pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Gera um nome de arquivo único
    $nomeArquivo = uniqid('imagem_', true) . '.png';
    $caminhoCompleto = $pasta . $nomeArquivo;

    // Salva a imagem na pasta
    file_put_contents($caminhoCompleto, $imagemDecodificada);

    // Retorna o caminho relativo da imagem salva
    return 'img/' . $nomeArquivo;
}

// Salva a imagem na pasta local e obtém o caminho relativo
$caminhoRelativo = salvar_imagem_na_pasta($imagemBase64);

// Para a API Gemini, não usamos URL da imagem diretamente do servidor,
// mas sim o conteúdo da imagem em base64.
// Então, o $urlImagem não será usado para a requisição ao Gemini,
// mas pode ser útil se você precisar exibir a imagem localmente ou em outro lugar.
$urlImagem = $webhook . '/login/painel/api/' . $caminhoRelativo;

// Sua chave da API do Gemini
$apiKey = $chave; // Certifique-se que a variável $chave está definida com sua API Key do Gemini

// Conteúdo da imagem em Base64 para ser enviado ao Gemini
// Você precisa ler o arquivo que acabou de salvar e convertê-lo novamente para Base64,
// pois a função salvar_imagem_na_pasta decodifica e salva, mas o Gemini precisa da versão Base64.
$imagemParaGeminiBase64 = base64_encode(file_get_contents(__DIR__ . '/' . $caminhoRelativo));

// URL da API Gemini Vision
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent?key=" . $apiKey;

// Dados da requisição para o Gemini
$dados = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "descreva em detalhes imagem é essa?" // Seu prompt para o Gemini
                ],
                [
                    "inlineData" => [
                        "mimeType" => "image/png", // O tipo MIME da sua imagem
                        "data" => $imagemParaGeminiBase64
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
$resposta = curl_exec($ch);

// Verificar se houve erro
if (curl_errno($ch)) {
    echo 'Erro ao fazer requisição para Gemini: ' . curl_error($ch);
} else {
    // Processar a resposta da API
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($codigo_http == 200) {
        $resultado = json_decode($resposta, true);
        try {
            $msg = 'IMG= ' . $resultado['candidates'][0]['content']['parts'][0]['text'];
            echo $msg; // Imprime a descrição da imagem
        } catch (Exception $e) {
            $msg = 'Erro ao processar resposta do Gemini: ' . $e->getMessage();
            echo $msg . ' Resposta completa: ' . $resposta;
        }
    } else {
        $msg = 'Erro na requisição Gemini (HTTP ' . $codigo_http . '): ' . $resposta;
        echo $msg;
    }
}

// Fechar a conexão cURL
curl_close($ch);

?>