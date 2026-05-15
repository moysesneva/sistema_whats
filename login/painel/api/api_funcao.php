<?php

function substituirEnv($token,$token_novo, $porta_nova, $servidor_novo,$vps) {
    // URL da API para substituir os valores no .env
    $url = $vps . '/substituir_env';

    // Dados a serem enviados no formato JSON
    $dados = [
        'token_recebido' => $token,
        'token_novo' => $token_novo,
        'porta_nova' => $porta_nova,
        'servidor_novo' => $servidor_novo
    ];

    // Inicializa o cURL
    $ch = curl_init($url);

    // Configurações do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

    // Define o cabeçalho Content-Type como application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Executa a requisição e armazena a resposta
    $resposta = curl_exec($ch);

    // Verifica se houve erro
    if (curl_errno($ch)) {
        $erro = 'Erro: ' . curl_error($ch);
        curl_close($ch);
        return $erro;
    } else {
        curl_close($ch);
        return $resposta;
    }
}

// Exemplo de uso

###############################################################
###############################################################
###############################################################
###############################################################
###############################################################


function criarUsuarioViaWebhook($usuario, $token, $url) {
    // Dados para criar um novo usuário
    $data = [
        'usuario' => $usuario, // Nome do usuário desejado
        'token' => $token,      // Token correto do arquivo .env
        'action' => 'CriarUsuario' // Ação específica para criar um usuário
    ];

    // Inicializar cURL
    $ch = curl_init($url);

    // Configurar opções do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Executar a requisição
    $response = curl_exec($ch);

    // Verificar erros
    if (curl_errno($ch)) {
        $erro = 'Erro: ' . curl_error($ch);
        curl_close($ch);
        return $erro;
    } else {
        curl_close($ch);
        return 'Resposta do servidor: ' . $response;
    }
}


function GerarQrcode($usuario, $servidor, $token) {
    // Estrutura de dados para chamar o endpoint
    $data = [
        'action' => 'GerarQrcode',
        'usuario' => $usuario,
        'token' => $token,
    ];

    // Inicializando cURL
    $ch = curl_init($servidor);

    // Configurando opções do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Executando a solicitação cURL
    $result = curl_exec($ch);

    // Verificando erros na execução do cURL
    if ($result === FALSE) {
        // Manipulação de erro
        $error = curl_error($ch);
        echo 'Erro cURL: ' . $error;
    } else {
        #echo 'Resposta: ' . $result;
    }

    // Fechando a conexão cURL
    curl_close($ch);
}


function Fecharsessao($usuario, $servidor, $token) {
    // Estrutura de dados para chamar o endpoint
    $data = [
        'action' => 'Fechar_sessao',
        'usuario' => $usuario,
        'token' => $token,
    ];

    // Inicializando cURL
    $ch = curl_init($servidor);

    // Configurando opções do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Executando a solicitação cURL
    $result = curl_exec($ch);

    // Verificando erros na execução do cURL
    if ($result === FALSE) {
        // Manipulação de erro
        $error = curl_error($ch);
        echo 'Erro cURL: ' . $error;
    } else {
        #echo 'Resposta: ' . $result;
    }

    // Fechando a conexão cURL
    curl_close($ch);
}


















function EnviarMsg($telefone, $msg, $id_msg = 0, $usuario, $token, $servidor) {
    if ($id_msg === null) {
        $id_msg = 0; // Define o valor padrão
    }
    
    // Estrutura de dados da mensagem
    $data = [
        'action' => 'EnviarMsg',
        'usuario' => $usuario,
        'token' => $token, // Adiciona o token ao array de dados
        'message' => [
            'telefone' => $telefone,
            'msg' => $msg,
            'id_msg' => $id_msg
        ]
    ];

    // Inicializando cURL
    $ch = curl_init($servidor);

    // Configurando opções do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Executando a solicitação cURL
    $result = curl_exec($ch);

    // Verificando erros na execução do cURL
    if ($result === FALSE) {
        // Handle error
        $error = curl_error($ch);
        // Log ou manipulação do erro conforme necessário
        echo 'Erro cURL: ' . $error;
    } else {
        echo 'Resposta: ' . $result;
    }

    // Fechando a conexão cURL
    curl_close($ch);
}

?>
