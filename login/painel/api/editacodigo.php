<?php


$fator = 'editajs';



if($fator == 'editapy'){
// Configurações globais – ajuste conforme necessário

/**
 * Função para abrir a instância (modo padrão)
 */
function abrir_instancia($user_id,$servidor,$porta,$token) {
    $url = "{$servidor}:{$porta}/";
    $payload = [
        "action"  => "AbrirInstancia",
        "usuario" => $user_id,
        "token"   => $token
     
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Erro no cURL: " . $error;
    }
    curl_close($ch);
    return $response;
}

/**
 * Função para abrir a instância em modo terminal (headless)
 */
function abrir_instancia_terminal($user_id, $servidor,$porta,$token)  {
    global $servidor, $porta, $token;
    $url = "{$servidor}:{$porta}/";
    $payload = [
        "action"  => "AbrirInstanciaTerminal",
        "usuario" => $user_id,
        "token"   => $token
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Erro no cURL: " . $error;
    }
    curl_close($ch);
    return $response;
}

/**
 * Função para fechar a instância
 */
function fechar_instancia($user_id,$servidor,$porta,$token) {
    global $servidor, $porta, $token;
    $url = "{$servidor}:{$porta}/";
    $payload = [
        "action"  => "FecharInstancia",
        "usuario" => $user_id,
        "token"   => $token
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Erro no cURL: " . $error;
    }
    curl_close($ch);
    return $response;
}


#(driver, user_id, servidor, token=None)
function puxar_img($user_id,$servidor,$pagina_recebe,$porta,$token)  {
    global $servidor, $porta, $token;
    $url = "{$servidor}:{$porta}/";
    $payload = [
        "action"  => "PuxaImg",
        "usuario" => $user_id,
        "token"   => $token,
        "pagina_recebe"   => $pagina_recebe
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Erro no cURL: " . $error;
    }
    curl_close($ch);
    return $response;
}
?>

<?php
function atualizarClassesGlobais($servidor,$porta, $user_id,$token) {
    // Dados da requisição em formato JSON
    $data = array(
        "action"  => "AtualizarClasses",
        "usuario" => "todos", // Para atualizar todos os usuários ativos
         "usuario" => $user_id,
        "token"   => $token


    );

    //$payload = json_encode($data);
     $url = "{$servidor}:{$porta}/";
    // Inicializa a sessão cURL com a URL do seu servidor
     $payload = json_encode($data);
    
    // Inicializa a sessão cURL com a URL do seu servidor
    $ch = curl_init($url);
    
    // Configurações da requisição
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ));
    
    // Executa a requisição
    $response = curl_exec($ch);
    
    // Verifica se houve algum erro
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "Erro ao atualizar classes: " . $error_msg;
    }
    
    curl_close($ch);
    return $response;
}


?>

<?php
 //* Função para requisitar o QR Code de uma instância pelo bot.py via cURL.

function gerarQrcode($servidor, $porta, $user_id, $token) {
    // Define a carga útil com a ação, usuário e token
    $payload = [
        "action"  => "GerarQrcode",
        "usuario" => $user_id,
        "token"   => $token
    ];
 $url = "{$servidor}:{$porta}/";

    // Inicializa a sessão cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Retorna a resposta ou o erro, se houver
    if ($error) {
        return "Erro cURL: " . $error;
    } else {
        return $response;
    }
}
?>

<?php

function enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg) {
    // Monta o payload conforme esperado pelo bot.py
    $payload = [
        'action'  => 'EnviarMsg',
        'usuario' => $user_id,
        'token'   => $token,
        'message' => [
            'telefone' => $telefone,
            'msg'      => $msg,
            'id_msg'   => $id_msg
        ]
    ];
     $url = "{$servidor}:{$porta}/";

    // Inicializa a sessão cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Executa a requisição e captura a resposta
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);
    
    // Retorna a resposta ou uma mensagem de erro
    if ($error) {
        return "Curl Error: " . $error;
    } else {
        return $response;
    }
}
/*
// Exemplo de uso:
$url       = 'http://127.0.0.1:5000/'; // Ajuste a URL conforme necessário
$usuario   = '123';                    // Mesmo usuário que abriu a instância
$token     = 'CPF_13535958709_NOME_VICTOR_NERY';
$telefone  = '553184767330';           // Número de destino (c/DDD e país)
$msg       = 'Olá, Como vai você?😅';
$id_msg    = '123';                    // ID de controle (caso seu sistema use)

$resultado = enviarMensagem($url, $user_id, $token, $telefone, $msg, $id_msg);
echo "Resposta do bot: " . $resultado;
*/
?>
<?php


function enviarMensagemMidia($servidor, $porta, $user_id, $token, $telefone, $media_url, $tipo, $msg, $id_msg) {
    // Monta a URL usando as variáveis $servidor e $porta
    $url = "{$servidor}:{$porta}/";

    // Monta o payload conforme esperado pelo bot.py
    $payload = [
        'action'  => 'EnviarMsgMidia',
        'usuario' => $user_id,
        'token'   => $token,
        'message' => [
            'telefone' => $telefone,
            'url'      => $media_url,
            'tipo'     => $tipo,
            'msg'      => $msg,
            'id_msg'   => $id_msg
        ]
    ];

    // Inicializa e configura a sessão cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição e obtém a resposta
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Retorna a resposta ou a mensagem de erro
    if ($error) {
        return "Curl Error: " . $error;
    } else {
        return $response;
    }
}





?>


<?php

function EscreverEnquete($servidor,$porta , $user_id, $token, $telefone, $enquete, $id_msg) {
    // Monta o payload conforme esperado pelo bot.py
    $payload = [
        'action'  => 'EscreverEnquete',
        'usuario' => $user_id,
        'token'   => $token,
        'message' => [
            'telefone' => $telefone,
            'enquete'      => $enquete,
            'id_msg'   => $id_msg
        ]
    ];
     $url = "{$servidor}:{$porta}/";

    // Inicializa a sessão cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Executa a requisição e captura a resposta
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);
    
    // Retorna a resposta ou uma mensagem de erro
    if ($error) {
        return "Curl Error: " . $error;
    } else {
        return $response;
    }
}
/*
// Exemplo de uso:
$url       = 'http://127.0.0.1:5000/'; // Ajuste a URL conforme necessário
$usuario   = '123';                    // Mesmo usuário que abriu a instância
$token     = 'CPF_13535958709_NOME_VICTOR_NERY';
$telefone  = '553184767330';           // Número de destino (c/DDD e país)
$msg       = 'Olá, Como vai você?😅';
$id_msg    = '123';                    // ID de controle (caso seu sistema use)

$resultado = enviarMensagem($url, $user_id, $token, $telefone, $msg, $id_msg);
echo "Resposta do bot: " . $resultado;
*/






}////editapy
?>


<?php

if($fator == 'editajs'){
 
 

function gerarQrcode($servidor, $porta, $user_id, $token) {
    $payload = [
        "action"  => "GerarQrcode",
        "usuario" => $user_id,
        "token"   => $token
    ];

    $url = "https://{$servidor}:{$porta}/";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    // ⚠️ Desativa verificação SSL (se usar IP com certificado autoassinado)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "❌ Erro no cURL: $error";
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['qrcode'])) {
        return $data['qrcode']; // ✅ Retorna só o QR direto
    } elseif (isset($data['error'])) {
        return "❌ Erro API: " . $data['error'];
    } else {
        return "❌ Resposta inválida ou vazia: " . json_encode($data);
    }
}


function abrir_instancia_terminal($user_id, $servidor, $porta, $token) {
    $payload = [
        "action"  => "AbrirInstancia",
        "usuario" => $user_id,
        "token"   => $token
    ];

    $url = "https://{$servidor}:{$porta}/";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    // ⚠️ Se usa SSL autoassinado, desativa verificação
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "❌ Erro no cURL: $error";
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['status'])) {
        return $data['status']; // ✅ Retorna status direto
    } elseif (isset($data['error'])) {
        return "❌ Erro API: " . $data['error'];
    } else {
        return "❌ Resposta inválida ou vazia: " . json_encode($data);
    }
}


 
 



function enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $mensagem, $id_msg) {
    // Monta o payload
    $payload = [
        "action"  => "EnviarMsg",
        "usuario" => $user_id,
        "token"   => $token,
        "message" => [
            "telefone" => $telefone, // Ex.: 5511999999999
            "msg"      => $mensagem, // Texto da mensagem
            "id_msg"   => $id_msg     // ID para controle
        ]
    ];

    // Monta a URL
    $url = "https://{$servidor}:{$porta}/";

    // Inicializa cURL
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    // ⚠️ Ignora SSL (caso use SSL autoassinado)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "❌ Erro no cURL: $error";
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['status'])) {
        return $data['status']; // ✅ Retorna status de sucesso
    } elseif (isset($data['error'])) {
        return "❌ Erro API: " . $data['error'];
    } else {
        return "❌ Resposta inválida ou vazia: " . json_encode($data);
    }
} 
 
 


?>





<?php

/**
 * Fecha uma instância do WhatsApp através da API.
 *
 * @param string $user_id  O ID do usuário/instância que será fechada.
 * @param string $servidor O endereço do servidor da API (ex: 'localhost' ou 'api.meusite.com').
 * @param int    $porta    A porta em que a API está rodando (ex: 8000).
 * @param string $token    O token de autenticação (atualmente não utilizado por esta ação específica, mas incluído para consistência).
 * @return array Um array com o resultado da requisição.
 */
function fechar_instancia(string $user_id, string $servidor, int $porta, string $token): array
{
    // Constrói a URL completa da API
    $apiUrl = "https://{$servidor}:{$porta}";

    // Dados a serem enviados no corpo da requisição (payload)
    $payload = [
        'action'  => 'FecharInstancia',
        'usuario' => $user_id
    ];

    // Converte o array para uma string JSON
    $jsonPayload = json_encode($payload);

    // Inicializa a sessão cURL
    $ch = curl_init();

    // Configura as opções do cURL
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    // IMPORTANTE: Para certificados SSL autoassinados em desenvolvimento.
    // Em produção com um certificado válido, estas linhas podem ser removidas.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Executa a requisição
    $response = curl_exec($ch);

    // Verifica por erros no cURL
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'message' => 'Erro cURL: ' . $error_msg,
            'response' => null
        ];
    }

    // Obtém o código de status HTTP e fecha a sessão
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Retorna o resultado
    return [
        'success'    => ($httpCode === 200),
        'statusCode' => $httpCode,
        'response'   => json_decode($response, true)
    ];
}

// --- Exemplo de Uso ---



?>


<?php



function EscreverEnquete($servidor, $porta, $user_id, $token, $telefone, $pergunta, array $opcoes, $id_msg) {
    // Monta o payload exatamente igual ao index.js espera
    $payload = [
        "action"  => "EnviarEnquete",
        "usuario" => $user_id,
        "token"   => $token,
        "message" => [
            "telefone" => $telefone,
            "pergunta" => $pergunta,
            "opcoes"   => array_values($opcoes),
            "id_msg"   => $id_msg
        ]
    ];

    $url = "https://{$servidor}:{$porta}/";
    $ch  = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        return "❌ Erro no cURL: $err";
    }
    curl_close($ch);

    $data = @json_decode($response, true);
    if (!is_array($data)) {
        return "❌ Resposta inválida: $response";
    }
    if (isset($data['status'])) {
        return $data['status'];
    }
    if (isset($data['error'])) {
        return "❌ Erro API: " . $data['error'];
    }
    return "❌ Resposta inesperada: " . json_encode($data);
}

?>




















<?php

/**
 * ARQUIVO: api_funcoes.php
 * Contém todas as funções para interagir com a API de automação.
 */


// ===================================================================
// FUNÇÃO CENTRAL DE REQUISIÇÃO (A BASE DE TUDO)
// ===================================================================

/**
 * Função central e privada para fazer todas as requisições à API.
 * Evita repetição de código e centraliza a lógica de comunicação e erro.
 *
 * @param string $servidor O IP ou domínio do servidor da API.
 * @param int    $porta    A porta da API.
 * @param array  $payload  Os dados a serem enviados em formato de array.
 * @return array Um array com o resultado da requisição ['success', 'message', 'data'].
 */
function _fazerRequisicaoAPI(string $servidor, int $porta, array $payload): array
{
    $url = "https://{$servidor}:{$porta}/";
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_TIMEOUT        => 300, // Timeout de 5 minutos para uploads/downloads grandes
        // ⚠️ ATENÇÃO: Em produção, com um certificado SSL válido, remova as 2 linhas abaixo!
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'message' => "Erro de conexão cURL: $error", 'data' => null];
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    
    // Considera erro se o código HTTP não for 200 ou se a API retornar um campo 'error'
    if ($httpCode !== 200 || (isset($data['error']) && !empty($data['error']))) {
        $errorMessage = $data['error'] ?? 'Resposta inesperada da API.';
        return ['success' => false, 'message' => "Erro da API (HTTP {$httpCode}): {$errorMessage}", 'data' => $data];
    }

    return ['success' => true, 'message' => 'Requisição bem-sucedida.', 'data' => $data];
}


// ===================================================================
// FUNÇÕES DE AJUDA (INTERNAS) PARA ENVIO DE MÍDIA
// ===================================================================

/**
 * (Helper) Verifica o tamanho de um arquivo remoto sem baixá-lo.
 * @return int|false O tamanho em bytes ou false em caso de falha.
 */
function _verificarTamanhoRemoto(string $url)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_NOBODY         => true, // Apenas cabeçalhos, sem corpo
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, // Segue redirecionamentos
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false, // Desativado para compatibilidade
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200 && $size > 0) ? $size : false;
}

/**
 * (Helper) Baixa o conteúdo de uma URL.
 * @return string|false O conteúdo do arquivo ou false em caso de falha.
 */
function _baixarConteudoDaUrl(string $url)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 300,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false, // Desativado para compatibilidade
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200 && $content) ? $content : false;
}


// ===================================================================
// FUNÇÃO PRINCIPAL E PÚBLICA PARA ENVIAR MÍDIA DE UMA URL
// ===================================================================

/**
 * Envia uma mídia a partir de uma URL.
 * A função decide de forma inteligente se envia a URL diretamente (para arquivos grandes)
 * ou se baixa o arquivo, converte para base64 e envia o conteúdo (para arquivos pequenos).
 *
 * @param string      $servidor       O IP ou domínio do servidor da API.
 * @param int         $porta          A porta da API.
 * @param string      $user_id        O ID do usuário/instância.
 * @param string      $token          O token de autenticação.
 * @param string      $telefone       O número de destino (formato internacional, ex: 5511999998888).
 * @param string      $url_do_arquivo A URL pública do arquivo a ser enviado.
 * @param string      $tipo_de_midia  'image', 'video', 'audio', ou 'document'.
 * @param string|null $legenda        A legenda para a mídia (opcional).
 * @param int         $limite_mb      O limite em MB para decidir entre baixar ou usar a URL (padrão 2MB).
 * @return array                      Um array com o resultado da requisição.
 */
function enviarMidiaDeUrl(
    string $servidor,
    int $porta,
    string $user_id,
    string $token,
    string $telefone,
    string $url_do_arquivo,
    string $tipo_de_midia,
    ?string $legenda = null,
    int $limite_mb = 2
): array {
    // Por padrão, o arquivo a ser enviado é a própria URL.
    $arquivo_para_enviar = $url_do_arquivo; 
    
    // Tenta verificar o tamanho para otimizar.
    $tamanho_bytes = _verificarTamanhoRemoto($url_do_arquivo);

    // Se o arquivo for menor que o limite, baixa e converte para base64.
    if ($tamanho_bytes !== false && ($tamanho_bytes / (1024 * 1024)) <= $limite_mb) {
        $conteudo = _baixarConteudoDaUrl($url_do_arquivo);
        if ($conteudo !== false) {
            $arquivo_para_enviar = base64_encode($conteudo);
        }
    }
    
    // Monta o payload final para a API.
    $payload = [
        'action'  => 'EnviarMidia',
        'usuario' => $user_id,
        'token'   => $token,
        'message' => [
            'telefone' => $telefone,
            'tipo'     => $tipo_de_midia,
            'arquivo'  => $arquivo_para_enviar,
            'legenda'  => $legenda,
        ]
    ];
    
    // Usa a função central para fazer a requisição e retorna o resultado.
    return _fazerRequisicaoAPI($servidor, $porta, $payload);
}





?>







 
<?php 
 
}

?>