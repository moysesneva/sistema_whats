<?php
function salvar_imagem_na_pasta($imagemBase64) {
    // Decodificar a imagem base64
    $imagemDecodificada = base64_decode($imagemBase64);

    // Definir a pasta onde a imagem será salva
    $pasta = __DIR__ . '/img/';
    
    // Criar a pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Gerar um nome de arquivo único
    $nomeArquivo = uniqid('imagem_', true) . '.png';
    $caminhoCompleto = $pasta . $nomeArquivo;

    // Salvar a imagem na pasta
    file_put_contents($caminhoCompleto, $imagemDecodificada);

    // Retornar o caminho relativo da imagem salva
    return 'img/' . $nomeArquivo;
}

#################################################################
#################################################################
#################################################################





// Decodificar a imagem base64
$imagemDecodificada = base64_decode($imagemBase64);

// Definir a pasta onde a imagem será salva
$pasta = __DIR__ . '/img/';

// Criar a pasta se não existir
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

// Gerar um nome de arquivo único
$nomeArquivo = uniqid('imagem_', true) . '.png';
$caminhoCompleto = $pasta . $nomeArquivo;

// Salvar a imagem na pasta
file_put_contents($caminhoCompleto, $imagemDecodificada);

// Retornar o caminho relativo da imagem salva
$caminhoRelativo = 'img/' . $nomeArquivo;

// Exemplo de uso do caminho relativo
$caminhoImagem = $caminhoRelativo;




   $apiKey = $chave;
   #$caminhoImagem = upload_imagem_freeimagehost($imagemBase64);
   #$caminhoImagem = salvar_imagem_na_pasta($imagemBase64);
    $urlImagem = $webhook.'/login/painel/api/'. $caminhoImagem;
    #sleep(5);
    #$resultado = descrever_imagem($urlImagem, $apiKey);
    
    
    
    
    
    
    
    


    // URL da API OpenAI
    $url = 'https://api.openai.com/v1/chat/completions';

    // Dados da requisição
    $dados = [
        "model" => "gpt-4o-mini",
        "messages" => [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                       "text" => "descreva em detalhes imagem é essa?"

                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => $urlImagem
                        ]
                    ]
                ]
            ]
        ],
        "max_tokens" => 300
    ];

    // Configuração do cURL
    $opcoes = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
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
        echo 'Erro:' . curl_error($ch);
    } else {
        // Processar a resposta da API
        $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($codigo_http == 200) {
            $resultado = json_decode($resposta, true);
            $msg = 'IMG= ' . $resultado['choices'][0]['message']['content'];  // Imprime a descrição da imagem
        } else {
            $msg = 'Erro na requisição: ' . $resposta . $urlImagem ;
        }
    }

    // Fechar a conexão cURL
    curl_close($ch);


?>