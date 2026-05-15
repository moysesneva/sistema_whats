<?php
// 🔧 Dados de conexão (Ajuste conforme sua VPS e token)
$servidor = "206.183.131.225";
$porta    = "443";
$token    = "NOME_Victor_Teste_CPF_010101";
$user_id  = "123";

// ✅ Função genérica para enviar requisição
function enviar_requisicao($payload, $servidor, $porta) {
    $url = "https://{$servidor}:{$porta}/";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    // ⚠️ Desativa a verificação SSL (obrigatório para IP com SSL autoassinado)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["status" => "❌ Erro no cURL: $error"];
    }

    curl_close($ch);

    // ✅ Tenta interpretar o JSON da resposta
    $data = json_decode($response, true);

    if ($data) {
        return $data;
    } else {
        return ["status" => "❌ Resposta inválida ou vazia"];
    }
}

//////////////////////////////////////
// 🚀 Funções específicas
//////////////////////////////////////

// 🔹 Abrir Instância
function abrir_instancia($user_id, $servidor, $porta, $token) {
    $payload = [
        "action"  => "AbrirInstancia",
        "usuario" => $user_id,
        "token"   => $token
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Abrir Instância Terminal (sem interface)
function abrir_instancia_terminal($user_id, $servidor, $porta, $token) {
    $payload = [
        "action"  => "AbrirInstancia",
        "usuario" => $user_id,
        "token"   => $token
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Fechar Instância
function fechar_instancia($user_id, $servidor, $porta, $token) {
    $payload = [
        "action"  => "FecharInstancia",
        "usuario" => $user_id,
        "token"   => $token
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Gerar QR Code
function gerarQrcode($servidor, $porta, $user_id, $token) {
    $payload = [
        "action"  => "GerarQrcode",
        "usuario" => $user_id,
        "token"   => $token
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Puxar Print da Instância
function puxar_img($user_id, $servidor, $pagina_recebe, $porta, $token) {
    $payload = [
        "action"        => "PuxaImg",
        "usuario"       => $user_id,
        "token"         => $token,
        "pagina_recebe" => $pagina_recebe
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Atualizar Classes Globais (Forçar atualização de cache interno)
function atualizarClassesGlobais($servidor, $porta, $user_id, $token) {
    $payload = [
        "action"  => "AtualizarClasses",
        "usuario" => $user_id,
        "token"   => $token
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Enviar Mensagem Texto
function enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg) {
    $payload = [
        "action"  => "EnviarMsg",
        "usuario" => $user_id,
        "token"   => $token,
        "message" => [
            "telefone" => $telefone,
            "msg"      => $msg,
            "id_msg"   => $id_msg
        ]
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Enviar Mensagem com Mídia (Imagem, PDF, Áudio, Vídeo)
function enviarMensagemMidia($servidor, $porta, $user_id, $token, $telefone, $media_url, $tipo, $msg, $id_msg) {
    $payload = [
        "action"  => "EnviarMsgMidia",
        "usuario" => $user_id,
        "token"   => $token,
        "message" => [
            "telefone" => $telefone,
            "url"      => $media_url,
            "tipo"     => $tipo,  // exemplo: image, audio, video, document
            "msg"      => $msg,
            "id_msg"   => $id_msg
        ]
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}

// 🔹 Escrever Enquete
function EscreverEnquete($servidor, $porta, $user_id, $token, $telefone, $enquete, $id_msg) {
    $payload = [
        "action"  => "EscreverEnquete",
        "usuario" => $user_id,
        "token"   => $token,
        "message" => [
            "telefone" => $telefone,
            "enquete"  => $enquete,
            "id_msg"   => $id_msg
        ]
    ];
    return enviar_requisicao($payload, $servidor, $porta);
}
?>
