<?php
/**
 * Este arquivo reúne, em um único lugar, todos os “métodos” que capturam
 * dados via POST (sem nenhuma lógica de banco de dados ou envio de mensagens).
 * Cada bloco verifica o valor de $_POST['codigo'] e extrai apenas as variáveis.
 */

date_default_timezone_set('America/Sao_Paulo');

// =====================================================================================
// 1) BLOCO GERAL DE CAPTURA (executa para qualquer POST que contenha 'codigo')
// =====================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    // Captura geral (pode ser usado em todos os outros métodos, se necessário)
    $codigo      = $_POST['codigo'];      // Identificador de sub-rotina
    $telefone    = isset($_POST['telefone'])    ? $_POST['telefone']    : null;
    $usuario_api = isset($_POST['usuario'])     ? $_POST['usuario']     : null;
    $msg         = isset($_POST['msg'])         ? $_POST['msg']         : null;
    $token       = isset($_POST['token'])       ? $_POST['token']       : null;
    // $user_id é redundante, pois equivale a $usuario_api
    $user_id     = $usuario_api;
}

// =====================================================================================
// 2) MÉTODO "ENQUETE": captura dados quando 'codigo' == 'enquete'
// =====================================================================================
if (isset($_POST['codigo']) && $_POST['codigo'] === 'enquete') {
    $usuario_api = isset($_POST['usuario'])  ? $_POST['usuario']  : null;
    $telefone    = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $opcoes      = isset($_POST['opcoes'])   ? $_POST['opcoes']   : null;
    
    // A função verificaSimNao() não será chamada aqui,
    // pois o objetivo é apenas mostrar a captura das variáveis.
    // Para uso real, bastaria aplicar:
    // $texto_normalizado = verificaSimNao($opcoes);
}

// =====================================================================================
// 3) MÉTODO "QRCODE": captura dados quando 'codigo' == 'qrcode'
// =====================================================================================
if (isset($_POST['codigo']) && $_POST['codigo'] === 'qrcode') {
    $qrcode      = isset($_POST['qrcode'])   ? $_POST['qrcode']   : null;
    $usuario_api = isset($_POST['usuario'])  ? $_POST['usuario']  : null;
    // Caso fosse necessário, poderia chamar uma função hora():
    // $hora = hora();
}

// =====================================================================================
// 4) MÉTODO "AUDIO": captura dados quando 'codigo' == 'audio'
// =====================================================================================
if (isset($_POST['codigo']) && $_POST['codigo'] === 'audio') {
    $telefone       = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $usuario_api    = isset($_POST['usuario'])  ? $_POST['usuario']  : null;
    $audio_recebido = isset($_POST['audio'])    ? $_POST['audio']    : null;
    
    // Aqui bastaria passar $audio_recebido para a rotina de gravação
    // e eventual chamada à API de transcrição.
}

// =====================================================================================
// 5) MÉTODO "IMG": captura dados quando 'codigo' == 'img'
// =====================================================================================
if (isset($_POST['codigo']) && $_POST['codigo'] === 'img') {
    $telefone     = isset($_POST['telefone'])  ? $_POST['telefone']  : null;
    $usuario_api  = isset($_POST['usuario'])   ? $_POST['usuario']   : null;
    $imagemBase64 = isset($_POST['anexo'])     ? $_POST['anexo']     : null;
    
    // A partir de $imagemBase64, basta chamar função que salva ou envia para descrição.
}

// =====================================================================================
// 6) MÉTODO "MSG": captura dados quando 'codigo' == 'msg'
// =====================================================================================
if (isset($_POST['codigo']) && $_POST['codigo'] === 'msg') {
    $telefone    = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $msg_texto   = isset($_POST['msg'])      ? $_POST['msg']      : null;
    $usuario_api = isset($_POST['usuario'])  ? $_POST['usuario']  : null;
    
    // Com $msg_texto e $telefone, bastaria seguir para lógica de processamento.
}

// =====================================================================================
// 7) LEITURA DE JSON PURO VIA "php://input" (quando POST não vier em application/x-www-form-urlencoded)
// =====================================================================================
$inputJSON = file_get_contents('php://input');
$data      = json_decode($inputJSON, true);

if ($data !== null) {
    // Extração de campos de um JSON arbitrário
    $usuario_json = isset($data['usuario'])  ? $data['usuario']  : null;
    $message_json = isset($data['message'])  ? $data['message']  : null;
    
    // Outros campos podem ser capturados conforme a necessidade:
    // $outro_campo = isset($data['campo']) ? $data['campo'] : null;
}

?>




<?php
/**
 * processa_requisicoes.php
 *
 * Contém os métodos que processam cada tipo de requisição recebida via POST,
 * usando as variáveis já capturadas (por exemplo: $codigo, $telefone, $usuario_api, etc.).
 * Ao final, há um dispatch que chama cada método com base em $codigo.
 *
 * Presume-se que, antes de incluir este arquivo, já existam:
 *  - $conn         : conexão mysqli ao banco de dados
 *  - $servidor     : endereço/IP do servidor de envio de mensagens
 *  - $porta        : porta do servidor de envio
 *  - $token        : token de autenticação para API de envio
 *  - $chave        : chave de API para chamadas externas (Whisper, ChatGPT, etc.)
 *  - As variáveis gerais capturadas:
 *      $codigo, $telefone, $usuario_api, $msg_texto, $opcoes,
 *      $qrcode, $audio_recebido, $imagemBase64
 *  - Funções auxiliares:
 *      enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $mensagem, $id_msg)
 *      hora()               — retorna hora atual (string “HH:MM:SS”)
 *      verificaSimNao($txt) — normaliza “sim” / “não”
 *      salvar_dados_resquest() — (opcional) salva dados brutos em log
 *      primeiro_contato.php  — fluxo inicial para novos clientes IA
 *      segundo_contato_ia.php — fluxo contínuo de IA para clientes existentes
 */

date_default_timezone_set('America/Sao_Paulo');

/**
 * 1) PROCESSA ENQUETE
 *
 * - $usuario_api: identificador do usuário da API (origem)
 * - $telefone   : telefone do cliente que respondeu
 * - $opcoes     : texto digitado pelo cliente (ex.: "s" ou "n")
 *
 * Fluxo:
 *   1. Normalizar $opcoes para "sim"/"não"
 *   2. Se "sim": atualizar agendamento (confirmacao=1, lembrete=3), inserir envio com mensagem de confirmação
 *   3. Se "não": atualizar agendamento (confirmacao=2, lembrete=3), inserir envio idem
 *   4. Caso não haja agendamento pendente, enviar mensagem genérica
 */
function processarEnquete()
{
    global $conn, $servidor, $porta, $token;
    // As variáveis já foram definidas antes de incluir este arquivo:
    // $usuario_api, $telefone, $opcoes

    // 1. Normaliza o texto para "sim" ou "não"
    $resposta = verificaSimNao($opcoes); // ex.: "sim" ou "não"

    if ($resposta === 'sim') {
        // 2a. Atualizar agendamento: confirmacao = 1, lembrete = 3
        $sql = "UPDATE agendamento
                   SET confirmacao = '1',
                       lembrete     = '3'
                 WHERE usuario_api    = '{$usuario_api}'
                   AND cliente_telefone = '{$telefone}'
                   AND lembrete         = '2'";
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            error_log("Erro SQL (enquete/sim): " . mysqli_error($conn));
        }

        // 2b. Verificar quantas linhas foram afetadas
        if (mysqli_affected_rows($conn) > 0) {
            $textoEnvio = "*Agendamento:* processado";
        } else {
            $textoEnvio = "Nenhum agendamento pendente para processar no momento.";
        }
    } elseif ($resposta === 'não') {
        // 3a. Atualizar agendamento: confirmacao = 2, lembrete = 3
        $sql = "UPDATE agendamento
                   SET confirmacao = '2',
                       lembrete     = '3'
                 WHERE usuario_api    = '{$usuario_api}'
                   AND cliente_telefone = '{$telefone}'
                   AND lembrete         = '2'";
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            error_log("Erro SQL (enquete/nao): " . mysqli_error($conn));
        }

        // 3b. Verificar resultado
        if (mysqli_affected_rows($conn) > 0) {
            $textoEnvio = "*Agendamento:* processado";
        } else {
            $textoEnvio = "Nenhum agendamento pendente para processar no momento.";
        }
    } else {
        // Opção inválida
        $textoEnvio = "Resposta inválida. Por favor, responda 'Sim' ou 'Não'.";
    }

    // 4. Inserir na fila de envio e chamar API
    $sqlInsere = "INSERT INTO envio 
                     (comando, telefone, msg, status, usuario_api)
                  VALUES
                     ('MsgTexto', '{$telefone}', '{$textoEnvio}', '2', '{$usuario_api}')";
    mysqli_query($conn, $sqlInsere);
    $id_msg = mysqli_insert_id($conn);

    // Enviar mensagem via API externa
    enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $textoEnvio, $id_msg);
}

/**
 * 2) PROCESSA QRCODE
 *
 * - $usuario_api: identificador do usuário da API
 * - $qrcode     : dado do QR code recebido (string/texto/Base64)
 *
 * Fluxo:
 *   1. Atualizar tabela "login": qrcode = '{$qrcode}', tempo_code = hora(), situacao = 'ativado'
 *   2. Limpar tabela 'envio' status=1 para este usuário, se existir
 *   3. Incrementar/descontar quantidade de QRs (lógica de contagem), se aplicável
 *   4. Em caso de erro ou ausência de usuário, retornar mensagem de erro
 */
function processarQrcode()
{
    global $conn;
    // Variáveis preexistentes: $usuario_api, $qrcode

    // 1. Atualizar login
    $horaAtual = hora(); // Função que retorna a string "HH:MM:SS"
    $sql = "UPDATE login
               SET qrcode   = '{$qrcode}',
                   tempo_code = '{$horaAtual}',
                   situacao   = 'ativado'
             WHERE usuario_api = '{$usuario_api}'";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        error_log("Erro SQL (qrcode/update): " . mysqli_error($conn));
        echo "Erro ao atualizar QR code.";
        return;
    }

    // 2. Se foi bem sucedido e $qrcode não está vazio, limpa pendências de envio
    if (!empty($qrcode)) {
        $sqlDel = "DELETE FROM envio
                     WHERE usuario_api = '{$usuario_api}'
                       AND status      = '1'";
        mysqli_query($conn, $sqlDel);
    }

    // 3. Lógica de contagem de QRs (opcional, conforme regras internas):
    //    Exemplo: buscar qr_quantidade, incrementar até um limite
    $sqlBusca = "SELECT qr_quantidade
                   FROM login
                  WHERE usuario_api = '{$usuario_api}'";
    $resBusca = mysqli_query($conn, $sqlBusca);
    if ($resBusca && mysqli_num_rows($resBusca) === 1) {
        $row = mysqli_fetch_assoc($resBusca);
        $qtde = intval($row['qr_quantidade']);

        if ($qtde >= 900) {
            // Se atingiu 900, resetar
            $novaQtde = 0;
        } else {
            $novaQtde = $qtde + 1;
        }

        $sqlAtualiza = "UPDATE login
                          SET qr_quantidade = '{$novaQtde}',
                              qr_data       = CURDATE()
                        WHERE usuario_api   = '{$usuario_api}'";
        mysqli_query($conn, $sqlAtualiza);
    } else {
        // Usuário não encontrado ou erro na consulta
        error_log("Erro SQL (qrcode/seleção login): " . mysqli_error($conn));
        echo "Usuário não encontrado.";
    }
}

/**
 * 3) PROCESSA AUDIO
 *
 * - $usuario_api    : identificador do usuário da API
 * - $telefone       : telefone do cliente que enviou o áudio
 * - $audio_recebido : string Base64 do áudio (MP3)
 *
 * Fluxo:
 *   1. Decodificar Base64 e salvar em arquivo temporário (.mp3)
 *   2. Montar requisição cURL para Whisper (OpenAI) e obter transcrição
 *   3. Caso obtenha resposta, incluir rotinas de IA (segundo_contato_ia.php)
 */
function processarAudio()
{
    global $conn, $chave; // $chave é a API key do OpenAI

    // Variáveis capturadas: $usuario_api, $telefone, $audio_recebido

    // 1. Função interna para salvar áudio base64 em disco
    function salvarAudioTemp($base64)
    {
        $binario = base64_decode($base64);
        $nome    = 'audio/' . uniqid('audio_', true) . '.mp3';
        file_put_contents($nome, $binario);
        return $nome;
    }

    // Salvar em disco
    $caminhoArquivo = salvarAudioTemp($audio_recebido);
    if (!file_exists($caminhoArquivo)) {
        error_log("Falha ao salvar áudio temporário.");
        return;
    }

    // 2. Preparar cURL para chamar Whisper API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => 'https://api.openai.com/v1/audio/transcriptions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'file'  => new CURLFILE($caminhoArquivo),
            'model' => 'whisper-1'
        ],
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $chave
        ],
    ]);

    $resposta = curl_exec($curl);
    if (curl_errno($curl)) {
        error_log("cURL Whisper erro: " . curl_error($curl));
        curl_close($curl);
        return;
    }
    curl_close($curl);

    // 3. Se tiver retornado algo, carregar fluxo IA
    if (!empty($resposta)) {
        // Transcrição está em $resposta (JSON). Em geral:
        // $json = json_decode($resposta, true);
        // $textoTranscricao = $json['text'] ?? '';
        // Mas a rotina de segundo contato de IA poderá ler direto do JSON.
        include 'segundo_contato_ia.php';
    } else {
        error_log("Resposta Whisper vazia ou inválida.");
    }
}

/**
 * 4) PROCESSA IMAGEM
 *
 * - $usuario_api  : identificador do usuário da API
 * - $telefone     : telefone do cliente que enviou a imagem
 * - $imagemBase64 : string Base64 contendo a imagem (PNG/JPG)
 *
 * Fluxo:
 *   1. Decodificar Base64 e salvar em pasta /img
 *   2. Gerar URL pública (usando $webhook + caminho relativo)
 *   3. Montar payload para ChatGPT com imagem e texto de descrição
 *   4. Enviar cURL para OpenAI Chat com parâmetros adequados
 *   5. Se receber resposta, tratar via IA (segundo_contato_ia.php)
 */
function processarImagem()
{
    global $conn, $webhook, $chave; // $webhook: URL base para servir imagens

    // Capturadas previamente: $usuario_api, $telefone, $imagemBase64

    // 1. Função para salvar imagem Base64 no disco
    function salvarImagemTemp($base64)
    {
        $binario = base64_decode($base64);
        $pasta   = __DIR__ . '/img/';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }
        $nome = uniqid('imagem_', true) . '.png';
        $caminhoCompleto = $pasta . $nome;
        file_put_contents($caminhoCompleto, $binario);
        return 'img/' . $nome; // Retorna caminho relativo
    }

    // Salvar imagem e montar URL
    $caminhoRelativo = salvarImagemTemp($imagemBase64);
    $urlImagem       = rtrim($webhook, '/') . '/' . $caminhoRelativo;

    // 2. Preparar payload para OpenAI Chat (gpt-4o-mini)
    $url = 'https://api.openai.com/v1/chat/completions';
    $dados = [
        "model"    => "gpt-4o-mini",
        "messages" => [
            [
                "role"    => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => "Por favor, descreva em detalhes a imagem a seguir:"
                    ],
                    [
                        "type"     => "image_url",
                        "image_url" => [
                            "url" => $urlImagem
                        ]
                    ]
                ]
            ]
        ],
        "max_tokens" => 300
    ];

    // 3. Executar cURL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($dados),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $chave,
            'Content-Type: application/json'
        ]
    ]);

    $resposta = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("cURL ChatGPT erro (imagem): " . curl_error($ch));
        curl_close($ch);
        return;
    }
    curl_close($ch);

    // 4. Se houver resposta válida, executar fluxo IA
    if (!empty($resposta)) {
        // A rotina segundo_contato_ia.php processará a resposta JSON para extrair o texto
        include 'segundo_contato_ia.php';
    } else {
        error_log("Resposta ChatGPT vazia ou inválida (imagem).");
    }
}

/**
 * 5) PROCESSA MENSAGEM DE TEXTO
 *
 * - $usuario_api: identificador do usuário da API
 * - $telefone   : telefone do cliente que enviou a mensagem
 * - $msg_texto  : texto digitado pelo cliente
 *
 * Fluxo:
 *   1. Gravar dados brutos (opcional) com salvar_dados_resquest()
 *   2. Buscar na tabela 'clientes' se o par (usuario_api, telefone) existe
 *   3. Se existir:
 *        - Se funçãodo cliente é "IA": incluir segundo_contato_ia.php
 *        - Se função é "ENQUETE": inserir notificação de enquete simples
 *   4. Se não existir:
 *        - Se função é "IA": incluir primeiro_contato.php, enviar mensagem de boas-vindas
 *        - Se função é "ENQUETE": (lógica de enquete para novo cliente, se houver)
 */
function processarMsgTexto()
{
    global $conn, $servidor, $porta, $token;
    // Variáveis definidas: $usuario_api, $telefone, $msg_texto, $funcao, $IA_boas_vindas

    // 1. Gravar dados brutos (opcional)
    salvar_dados_resquest();

    // 2. Verificar existência do cliente
    $sqlBusca = "SELECT id_agendamento, situacao, nome
                   FROM clientes
                  WHERE usuario_api = '{$usuario_api}'
                    AND telefone     = '{$telefone}'";
    $resBusca = mysqli_query($conn, $sqlBusca);
    if (!$resBusca) {
        error_log("Erro SQL (msg/seleção clientes): " . mysqli_error($conn));
        return;
    }

    $total = mysqli_num_rows($resBusca);

    if ($total === 1) {
        // Cliente já existe
        $row = mysqli_fetch_assoc($resBusca);
        // Dependendo da função atribuída ao cliente (IA ou ENQUETE):
        if ($funcao === "IA") {
            include 'segundo_contato_ia.php';
        } elseif ($funcao === "ENQUETE") {
            // Exemplo simples: notificar sobre enquete
            $textoEnvio = "Você ainda não respondeu à enquete. Por favor, responda 'Sim' ou 'Não'.";
            $sqlIns = "INSERT INTO envio 
                          (comando, telefone, msg, status, usuario_api)
                       VALUES
                          ('Enquete', '{$telefone}', '{$textoEnvio}', '2', '{$usuario_api}')";
            mysqli_query($conn, $sqlIns);
            $id_msg = mysqli_insert_id($conn);
            enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $textoEnvio, $id_msg);
        }
    } else {
        // Cliente não existe
        if ($funcao === "IA") {
            include 'primeiro_contato.php'; // Define $IA_boas_vindas
            // Envia mensagem de boas-vindas
            $textoEnvio = $IA_boas_vindas;
            $sqlIns = "INSERT INTO envio
                          (comando, telefone, msg, status, usuario_api)
                       VALUES
                          ('MsgTexto', '{$telefone}', '{$textoEnvio}', '2', '{$usuario_api}')";
            $queryIns = mysqli_query($conn, $sqlIns);
            if ($queryIns) {
                $id_msg = mysqli_insert_id($conn);
                enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $textoEnvio, $id_msg);
            }
        } elseif ($funcao === "ENQUETE") {
            // Exemplo: primeiro envio de enquete para novo cliente
            $textoEnvio = "Olá! Por favor, responda à enquete: 'Você confirma seu agendamento? Sim/Não'";
            $sqlIns = "INSERT INTO envio
                          (comando, telefone, msg, status, usuario_api)
                       VALUES
                          ('Enquete', '{$telefone}', '{$textoEnvio}', '2', '{$usuario_api}')";
            mysqli_query($conn, $sqlIns);
            $id_msg = mysqli_insert_id($conn);
            enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $textoEnvio, $id_msg);
        }
    }
}

/**
 * 6) PROCESSA JSON PURO VIA php://input
 *
 * - $data: array resultante de json_decode(file_get_contents('php://input'), true)
 *
 * Fluxo:
 *   1. Extrair campos necessários de $data (ex.: usuario, message)
 *   2. Aplicar lógica adicional conforme estrutura do JSON (não detalhada aqui)
 */
function processarJSONPuro()
{
    // Supondo que $data já tenha sido definido lá no início do arquivo:
    global $data, $conn;

    // 1. Extrair campos
    $usuario_api = isset($data['usuario']) ? $data['usuario'] : null;
    $mensagem    = isset($data['message']) ? $data['message'] : null;

    // 2. Exemplo de uso: simplesmente logar ou tratar
    if ($usuario_api !== null && $mensagem !== null) {
        // Exemplo de inserção em tabela de log (comentado aqui)
        // $textoSQL = "INSERT INTO log_json (usuario_api, texto_json) VALUES ('{$usuario_api}', '{$mensagem}')";
        // mysqli_query($conn, $textoSQL);
        // ... ou chamar outra rotina de processamento
    }
}

// =====================================================================================
// DISPATCH: chama o método apropriado com base em $codigo
// =====================================================================================

if (isset($codigo)) {
    switch ($codigo) {
        case 'enquete':
            processarEnquete();
            break;

        case 'qrcode':
            processarQrcode();
            break;

        case 'audio':
            processarAudio();
            break;

        case 'img':
            processarImagem();
            break;

        case 'msg':
            processarMsgTexto();
            break;

        default:
            // Se for outro código ou não reconhecido, poderia chamar processarJSONPuro()
            processarJSONPuro();
            break;
    }
}
?>