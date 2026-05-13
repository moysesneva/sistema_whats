<?php
/**
 * Arquivo: funcoes_midia.php
 * Descrição: Conjunto de funções para baixar, processar e enviar mídias
 * para uma API de mensagens. Inclui uma lógica inteligente
 * para decidir entre enviar uma URL direta ou um arquivo em Base64.
 * Autor: Gemini com base no código fornecido.
 */

// ==================================================================
// CONFIGURAÇÃO
// ==================================================================

/**
 * Defina o tamanho máximo em Megabytes para um arquivo ser convertido para Base64.
 * Arquivos maiores que isso serão enviados como URL direta para a API, o que é
 * mais rápido e consome menos memória do seu servidor.
 */
define('TAMANHO_MAXIMO_MB_PARA_BASE64', 2);


// ==================================================================
// FUNÇÕES PRINCIPAIS
// ==================================================================

/**
 * 🔥 FUNÇÃO ADAPTATIVA: PONTO DE ENTRADA PRINCIPAL.
 * Esta é a função que seu cron deve chamar. Ela analisa o arquivo
 * e escolhe a melhor estratégia de envio (URL ou Base64).
 *
 * @param string $url A URL do arquivo de mídia a ser processado.
 * @param string $tipo Uma string descritiva do tipo de mídia (ex: 'imagem', 'vídeo'). Usado para logs.
 * @return string|false Retorna a URL do arquivo (para arquivos grandes) ou uma string Base64
 * (para arquivos pequenos). Retorna `false` em caso de falha.
 */
function baixar_midia_inteligente($url, $tipo = 'mídia') {
    echo "🔍 Analisando arquivo de $tipo: $url\n";
    
    // 1. Verifica o tamanho do arquivo primeiro, sem baixar o conteúdo completo.
    $tamanho_bytes = verificar_tamanho_arquivo($url);
    
    if ($tamanho_bytes) {
        $tamanho_mb = $tamanho_bytes / (1024 * 1024);
        echo "📊 Tamanho detectado: " . number_format($tamanho_mb, 2) . " MB\n";
        
        // 2. Se for maior que o limite definido, usa a URL direta.
        if ($tamanho_mb > TAMANHO_MAXIMO_MB_PARA_BASE64) {
            echo "⚡ Arquivo grande. Enviando como URL direta (estratégia mais rápida).\n";
            return $url; // Retorna a própria URL, não o conteúdo em base64.
        }
    } else {
        echo "⚠️ Não foi possível detectar o tamanho do arquivo. Tentando baixar diretamente...\n";
    }
    
    // 3. Se for pequeno ou se o tamanho não pôde ser verificado, baixa e converte para Base64.
    echo "📥 Arquivo pequeno/médio. Baixando conteúdo para o servidor...\n";
    return baixar_midia_e_converter_base64($url, $tipo);
}

/**
 * 🚀 FUNÇÃO DE ENVIO FINAL: Envia os dados para a API de mensagens.
 *
 * @param string $user_id      ID do usuário/instância na API.
 * @param string $servidor     IP ou domínio do servidor da API.
 * @param string $porta        Porta do servidor da API.
 * @param string $token        Token de autenticação.
 * @param string $telefone     Número de telefone do destinatário.
 * @param string $tipo         Tipo da mídia ('image', 'video', 'audio', 'document').
 * @param string $arquivo      O conteúdo da mídia (URL ou string Base64).
 * @param string|null $legenda Texto que acompanhará a mídia.
 * @return array               A resposta da API decodificada como um array.
 */
function enviar_midia($user_id, $servidor, $porta, $token, $telefone, $tipo, $arquivo, $legenda = null) {
    $url = "https://{$servidor}:{$porta}";
    
    $dados = json_encode([
        'action' => 'EnviarMidia',
        'usuario' => $user_id,
        'token' => $token,
        'message' => [
            'telefone' => $telefone,
            'tipo' => $tipo,
            'arquivo' => $arquivo,
            'legenda' => $legenda
        ]
    ]);
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $dados,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 300, // Timeout de 5 minutos para dar tempo de a API processar
        CURLOPT_SSL_VERIFYPEER => false, // Ignora a verificação do certificado SSL (comum em APIs internas)
        CURLOPT_SSL_VERIFYHOST => false, // Ignora a verificação do host SSL
    ]);
    
    $resposta = curl_exec($curl);
    $erro = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($erro) {
        return ['erro' => "Erro de cURL: " . $erro];
    }
    
    if ($http_code != 200) {
        return ['erro' => "Erro de API. Servidor respondeu com código HTTP $http_code. Resposta: $resposta"];
    }
    
    $resultado = json_decode($resposta, true);
    return $resultado ?: ['erro' => 'A resposta da API não é um JSON válido. Resposta: ' . $resposta];
}


// ==================================================================
// FUNÇÕES AUXILIARES (INTERNAS)
// ==================================================================

/**
 * 🛠️ FUNÇÃO AUXILIAR: Verifica o tamanho de um arquivo em uma URL sem baixá-lo.
 * Usa um método HEAD para ser rápido e eficiente.
 *
 * @param string $url A URL do arquivo.
 * @return int|false O tamanho do arquivo em bytes, ou `false` se não conseguir detectar.
 */
function verificar_tamanho_arquivo($url) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_NOBODY => true, // Essencial: Só pega os cabeçalhos (headers), não o corpo (body).
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true, // Necessário para ler os headers de resposta.
        CURLOPT_FOLLOWLOCATION => true, // Segue redirecionamentos.
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    curl_exec($curl);
    $tamanho = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    // Retorna o tamanho apenas se a requisição foi bem-sucedida e o tamanho é válido.
    if ($http_code == 200 && $tamanho > 0) {
        return $tamanho;
    }
    
    return false;
}

/**
 * 🛠️ FUNÇÃO AUXILIAR: Baixa o conteúdo de uma URL e o codifica em Base64.
 * Inclui uma barra de progresso para o console.
 *
 * @param string $url A URL do arquivo a ser baixado.
 * @param string $tipo Descrição do tipo de arquivo para os logs.
 * @return string|false A string Base64 do arquivo, ou `false` em caso de erro.
 */
function baixar_midia_e_converter_base64($url, $tipo = 'mídia') {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', // Simula um navegador
        CURLOPT_NOPROGRESS => false, // Habilita a função de progresso
        CURLOPT_PROGRESSFUNCTION => function($resource, $download_size, $downloaded, $upload_size, $uploaded) {
            if ($download_size > 0) {
                $percent = round(($downloaded / $download_size) * 100);
                // \r (retorno de carro) faz a linha ser sobrescrita no console.
                echo "\r📊 Progresso do download: $percent% (" . number_format($downloaded / 1024, 0) . " KB de " . number_format($download_size / 1024, 0) . " KB)";
            }
        },
    ]);
    
    $conteudo = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $erro = curl_error($curl);
    curl_close($curl);
    
    echo "\n"; // Adiciona uma nova linha após a conclusão da barra de progresso.
    
    if ($erro) {
        echo "❌ Erro ao baixar o arquivo: $erro\n";
        return false;
    }
    
    if ($http_code != 200) {
        echo "❌ Falha no download. O servidor da mídia respondeu com o código HTTP: $http_code\n";
        return false;
    }
    
    echo "✅ Download concluído: " . number_format(strlen($conteudo) / 1024, 2) . " KB. Convertendo para Base64...\n";
    return base64_encode($conteudo);
}

?>