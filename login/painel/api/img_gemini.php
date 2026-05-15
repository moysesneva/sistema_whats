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

/**
 * Remove arquivos temporários de imagem com mais de $idadeMaximaSegundos segundos.
 * Serve como limpeza de segurança para arquivos deixados por processos que
 * terminaram de forma inesperada.
 *
 * @param string $pasta        Caminho absoluto da pasta de imagens temporárias.
 * @param int    $idadeMaxima  Idade máxima em segundos (padrão: 3600 = 1 hora).
 */
function limpar_imagens_antigas($pasta, $idadeMaxima = 3600) {
    if (!is_dir($pasta)) {
        return;
    }

    $agora = time();

    foreach (glob($pasta . 'imagem_*.png') as $arquivo) {
        if (is_file($arquivo) && ($agora - filemtime($arquivo)) > $idadeMaxima) {
            unlink($arquivo);
        }
    }
}

$caminhoRelativo = salvar_imagem_na_pasta($imagemBase64);

$apiKey = $chave;

$caminhoAbsoluto = __DIR__ . '/' . $caminhoRelativo;

// Limpa arquivos temporários antigos deixados por processos que falharam anteriormente.
limpar_imagens_antigas(__DIR__ . '/img/');

try {
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
} finally {
    // Garante que o arquivo temporário seja removido ao término do bloco try,
    // incluindo exceções e saídas antecipadas. Arquivos deixados por encerramentos
    // abruptos do processo (SIGKILL, OOM) são cobertos pela varredura periódica
    // de limpar_imagens_antigas() executada no início de cada requisição.
    if (file_exists($caminhoAbsoluto)) {
        unlink($caminhoAbsoluto);
    }
}

?>
