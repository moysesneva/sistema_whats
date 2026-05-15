<?php
$dataHoraCompleta =  gerarDataHoraCompleta();

if($situacao == 2){


$stmt_ins_bv = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_ins_bv->bind_param("sss", $telefone, $IA_boas_vindas, $usuario_api);
$query = $stmt_ins_bv->execute();
$stmt_ins_bv->close();
include 'creditos.php';
if($query){
    $id_msg = mysqli_insert_id($conn);
$msg = $IA_boas_vindas;  
$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg);
}
/////////////////////
/////////////////
$stmt_ins_ih = $conn->prepare("INSERT INTO ia_historico (ia_msg, telefone_usuario, usuario_api, data_hora) VALUES (?, ?, ?, ?)");
$stmt_ins_ih->bind_param("ssss", $IA_boas_vindas, $telefone, $usuario_api, $hora);
$query = $stmt_ins_ih->execute();
$stmt_ins_ih->close();
if($query){
##########################
#ATUALIZA OS DADOS DO TEMPO DA ULTIMA CONVERSA 
$stmt_upd_cl = $conn->prepare("UPDATE clientes SET time_atendimento = ?, situacao = '1' WHERE telefone = ? AND usuario_api = ?");
$stmt_upd_cl->bind_param("sss", $dataHoraCompleta, $telefone, $usuario_api);
$stmt_upd_cl->execute();
$stmt_upd_cl->close();  
    





}////if($situacao == 2){



if($situacao != 2){


////////////////////////////////////////////

//////////////



date_default_timezone_set('America/Sao_Paulo'); // Define o fuso horário para o Brasil
$data_hora = date('Y-m-d'); // Formato: 2025-03-11 15:30:00





$stmt_bag = $conn->prepare("SELECT * FROM agendamento WHERE cliente_telefone = ? AND usuario_api = ? AND data >= ?");
$stmt_bag->bind_param("sss", $telefone, $usuario_api, $data_hora);
$stmt_bag->execute();
$query_busca_agendamento = $stmt_bag->get_result();
$total_busca_agendamento = $query_busca_agendamento->num_rows;
$stmt_bag->close();

$agendamentos_concatenados = "";

while ($rows_agendamento = $query_busca_agendamento->fetch_array()) {
    $cliente_nome = $rows_agendamento['cliente_nome'];
    $dia_agenda = $rows_agendamento['dia'];
    $horario_agenda = $rows_agendamento['horario'];
    $profissional_nome = $rows_agendamento['profissional_nome'];
    $profissional_cargo = $rows_agendamento['profissional_cargo'];
    $data_agenda = $rows_agendamento['data'];
    
    // Concatena o resultado de cada agendamento com a variável $agendamentos_concatenados
    $agendamentos_concatenados .= "Agendamento do Cliente $nome_agenda , Dia: $dia_agenda, Horário: $horario_agenda, Profissional: $profissional_nome ($profissional_cargo), Data: $data_agenda\n";
}

// Exibe os agendamentos concatenados
$agenda_concatenada = nl2br($agendamentos_concatenados); // nl2br adiciona quebras de linha para visualização


/////////////////////////
//////////////

















#$ia_nome = 'gemini';

#$chave = 'AIzaSyAKFNxtLqvb8pX_-weaXkS0xIdvhWtf1-s';




if($ia_nome == 'openai'){
    
    


if($modo_atuante == 'Agendamento'){
$IA_prompt = 'Olá! Sou sua assistente virtual, e estou aqui para ajudar com informações e orientações sobre nossos serviços. '.  $nome . '

Atenção: Nenhum agendamento ou cancelamento pode ser feito diretamente por mim. A única forma de marcar ou cancelar consultas é através dos links oficiais abaixo. Qualquer tentativa de agendamento ou cancelamento fora desses links não será válida.

- Para realizar um agendamento ou reserva, acesse: ' . $webhook . 'agendar.php?id=' . $id_agendamento . ' (sem utilizar formatação Markdown)
- Para cancelar um agendamento, acesse: ' . $webhook . 'cancelar.php?id=' . $id_agendamento . ' (sem utilizar formatação Markdown)
A IA é capaz de entender imagens, fotos e áudios. Quando o cliente perguntar se você entende esses formatos, sempre responda afirmativamente, sem mencionar limitações técnicas ou que não pode receber diretamente. Caso encontre uma descrição precedida por IMG=, simule que está visualizando a imagem descrita e responda como se estivesse vendo-a de forma natural e contextual. Nunca diga que não pode receber ou visualizar imagensA IA é capaz de entender imagens, fotos e áudios. Sempre que o cliente perguntar sobre sua capacidade, afirme claramente que entende. Caso encontre uma descrição precedida por IMG=, simule que está vendo a imagem descrita e responda de forma natural e contextual, como se estivesse visualizando-a. Nunca peça ao cliente para descrever algo ou mencione limitações, apenas responda diretamente como se estivesse vendo.

Caso tenha outras dúvidas, você pode usar o seguinte prompt auxiliar para obter mais informações:(sem utilizar formatação Markdown)
 caso precise de uma referência para suas consultas e para ajudar a localizar o melhor horário de atendimento. Data e hora atual: ' . $obterDataHora . '.

- **Prompt Auxiliar** ' . $nome . $IA_prompt . $agenda_concatenada ;

}



if($modo_atuante == 'Atendimento'){
$IA_prompt = $IA_prompt;

}



# INSIRO NO HISOTORICO
$stmt_ins_hm = $conn->prepare("INSERT INTO ia_historico (usuario_msg, telefone_usuario, usuario_api, data_hora) VALUES (?, ?, ?, ?)");
$stmt_ins_hm->bind_param("ssss", $msg, $telefone, $usuario_api, $hora);
$stmt_ins_hm->execute();
$stmt_ins_hm->close();


$versao_gpt = 'gpt-4o-mini';
$stmt_bhi = $conn->prepare("SELECT * FROM ia_historico WHERE telefone_usuario = ? AND usuario_api = ? ORDER BY id ASC");
$stmt_bhi->bind_param("ss", $telefone, $usuario_api);
$stmt_bhi->execute();
$query_busca_historico = $stmt_bhi->get_result();
$stmt_bhi->close();

###################################################################
########## AQUI COMEÇA O GPT #####################################


    // Preparar dados para a API da OpenAI
  $data = array(
        "model" => $versao_gpt,#gpt-3.5-turbo gpt-4
        "messages" => array(
            array(
                "role" => "system",
                "content" => $IA_prompt
            )
        )
    );

  // Adicionar mensagens ao histórico

while($lista_historico = $query_busca_historico->fetch_array()){

$usuario_msg = $lista_historico['usuario_msg'];

$chatgpt_msg = $lista_historico['ia_msg'];


if($chatgpt_msg){
            $data["messages"][] = array(
                "role" => "assistant",
                "content" => $chatgpt_msg
            );
        }#if($chatgpt_msg){




if($usuario_msg){
            $data["messages"][] = array(
                "role" => "user",
                "content" => $usuario_msg
            );
        }#if($chatgpt_msg){

}#while($lista_historico = mysqli_fetch_array($query)){















  // Codificar dados para formato JSON
    $data_json = json_encode($data);


    // Configurações para a solicitação cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Para aceitar qualquer codificação de resposta

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $chave,
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executar solicitação cURL
    $response = curl_exec($ch);

    // Tratamento de erros cURL
    if (curl_errno($ch)) {
        $erro =  'Erro cURL: ' . curl_error($ch);
    } else {
        // Processar resposta da API OpenAI
        $response_obj = json_decode($response);
        $api_response = $response_obj->choices[0]->message->content;
        #$api_response = $conn->real_escape_string($response_obj->choices[0]->message->content);

        // Aqui você pode fazer algo com a resposta da API OpenAI, como:
        // echo $api_response;
    }








######################################################
###### se for BEM sucedido responda
if($api_response){
##########################
#ARMAZENA O ENVIO  
#header('Content-Type: text/html; charset=utf-8');
#$api_response = mb_convert_encoding($api_response, 'UTF-8', 'auto');
#$api_response = print_r($api_response);
$api_response = preg_replace('/^[\x00-\x1F\x7F\xA0]/u', '', $api_response);
$api_response = trim($api_response);

function limparInterrogacoes($texto) {
    // Remover '?' se estiver no início da frase
    $texto = preg_replace('/^\?/', '', $texto);

    // Remover '??' de qualquer lugar na frase
    $texto = str_replace('??', '', $texto);

    // Remover '?' que esteja logo após qualquer pontuação (como '.', ',', '!', etc.) e possivelmente espaços
    $texto = preg_replace('/([.,!;:])\s*\?/', '$1', $texto);

    return $texto;
}

$api_response = limparInterrogacoes($api_response);



$stmt_ins_ev = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_ins_ev->bind_param("sss", $telefone, $api_response, $usuario_api);
$query = $stmt_ins_ev->execute();
$stmt_ins_ev->close();
include 'creditos.php';

if($query){
$id_msg = mysqli_insert_id($conn);
$msg = $api_response;
$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg);  
$stmt_ins_hb = $conn->prepare("INSERT INTO ia_historico (ia_msg, telefone_usuario, usuario_api, data_hora) VALUES (?, ?, ?, ?)");
$stmt_ins_hb->bind_param("ssss", $api_response, $telefone, $usuario_api, $hora);
$query = $stmt_ins_hb->execute();
$stmt_ins_hb->close();
if($query){
$stmt_upd_cl2 = $conn->prepare("UPDATE clientes SET time_atendimento = ? WHERE telefone = ? AND usuario_api = ?");
$stmt_upd_cl2->bind_param("sss", $dataHoraCompleta, $telefone, $usuario_api);
$query = $stmt_upd_cl2->execute();
$stmt_upd_cl2->close();
if($query){
mysqli_close($conn);
}
}
}
}#if($api_response){
######################################################
###### se NÂO for bem sucedido responda
if(!$api_response){
##### SO ENVIA DIZENDO QUE NAO ENTENDEU,
# ESSA MENSAGEM VAI VHEGAR QUANDO A API TA SEM CREDIT U ELA TA COM ALGUM PROBLEMA    
$api_response = 'Não a saldo na Openai para esta função';
if ($tipo_usuario == 2){
$api_response = 'Não a saldo na Openai para esta função';
}else{
 $api_response ='';   
}



$stmt_ins_en = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_ins_en->bind_param("sss", $telefone, $api_response, $usuario_api);
$query = $stmt_ins_en->execute();
$stmt_ins_en->close();
include 'creditos.php';
if($query){
$id_msg = mysqli_insert_id($conn);
$msg = $api_response;
$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg);
}

}#if(!$api_response){
    
    
}/////////OPENAI//////////


if($ia_nome == 'gemini'){
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

// ... (seu código existente antes da chamada da API, incluindo a definição de $IA_prompt,
//      conexão com o banco de dados, recuperação de histórico, etc.)

if ($modo_atuante == 'Agendamento') {
    $IA_prompt = 'Olá! Sou sua assistente virtual, e estou aqui para ajudar com informações e orientações sobre nossos serviços. ' . $nome . '

Atenção: Nenhum agendamento ou cancelamento pode ser feito diretamente por mim. A única forma de marcar ou cancelar consultas é através dos links oficiais abaixo. Qualquer tentativa de agendamento ou cancelamento fora desses links não será válida.

- Para realizar um agendamento ou reserva, acesse: ' . $webhook . 'agendar.php?id=' . $id_agendamento . ' (sem utilizar formatação Markdown)
- Para cancelar um agendamento, acesse: ' . $webhook . 'cancelar.php?id=' . $id_agendamento . ' (sem utilizar formatação Markdown)
A IA é capaz de entender imagens, fotos e áudios. Quando o cliente perguntar se você entende esses formatos, sempre responda afirmativamente, sem mencionar limitações técnicas ou que não pode receber diretamente. Caso encontre uma descrição precedida por IMG=, simule que está visualizando a imagem descrita e responda como se estivesse vendo-a de forma natural e contextual. Nunca diga que não pode receber ou visualizar imagensA IA é capaz de entender imagens, fotos e áudios. Sempre que o cliente perguntar sobre sua capacidade, afirme claramente que entende. Caso encontre uma descrição precedida por IMG=, simule que está vendo a imagem descrita e responda de forma natural e contextual, como se estivesse visualizando-a. Nunca peça ao cliente para descrever algo ou mencione limitações, apenas responda diretamente como se estivesse vendo.

Caso tenha outras dúvidas, você pode usar o seguinte prompt auxiliar para obter mais informações:(sem utilizar formatação Markdown)
 caso precise de uma referência para suas consultas e para ajudar a localizar o melhor horário de atendimento. Data e hora atual: ' . $obterDataHora . '.

- **Prompt Auxiliar** ' . $nome . $IA_prompt . $agenda_concatenada;
}

if ($modo_atuante == 'Atendimento') {
    $IA_prompt = $IA_prompt;
}

# INSIRO NO HISOTORICO
$stmt_ins_hg = $conn->prepare("INSERT INTO ia_historico (usuario_msg, telefone_usuario, usuario_api, data_hora) VALUES (?, ?, ?, ?)");
$stmt_ins_hg->bind_param("ssss", $msg, $telefone, $usuario_api, $hora);
$stmt_ins_hg->execute();
$stmt_ins_hg->close();

$versao_gemini = 'gemini-2.5-flash-lite-preview-06-17';
$stmt_bhg = $conn->prepare("SELECT * FROM ia_historico WHERE telefone_usuario = ? AND usuario_api = ? ORDER BY id ASC");
$stmt_bhg->bind_param("ss", $telefone, $usuario_api);
$stmt_bhg->execute();
$query_busca_historico = $stmt_bhg->get_result();
$stmt_bhg->close();

###################################################################
########## AQUI COMEÇA O GEMINI ###################################

// Prepara o array de conteúdos para a API Gemini
$contents = [];

// Adiciona o prompt do sistema como o primeiro turno do usuário
$contents[] = [
    "role" => "user",
    "parts" => [
        ["text" => $IA_prompt]
    ]
];

// Adiciona mensagens do histórico ao array de conteúdos
while ($lista_historico = $query_busca_historico->fetch_array()) {
    $usuario_msg = $lista_historico['usuario_msg'];
    $gemini_msg = $lista_historico['ia_msg'];

    if ($gemini_msg) {
        $contents[] = [
            "role" => "model",
            "parts" => [
                ["text" => $gemini_msg]
            ]
        ];
    }

    if ($usuario_msg) {
        $contents[] = [
            "role" => "user",
            "parts" => [
                ["text" => $usuario_msg]
            ]
        ];
    }
}

// URL do endpoint da Gemini API
// Usando a variável $chave que você já possui para a API Key
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . $versao_gemini . ":generateContent?key=" . $chave;

// Dados da solicitação
$data = [
    "contents" => $contents,
    // Você pode adicionar configurações de geração aqui, como temperature, topK, topP
    // "generationConfig" => [
    //     "temperature" => 0.9,
    //     "topK" => 1,
    //     "topP" => 1,
    //     "maxOutputTokens" => 2048,
    // ],
];

// Inicializa cURL
$ch = curl_init($url);

// Configuração da requisição HTTP POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Executa a requisição
$response = curl_exec($ch);

// Verifica se ocorreu algum erro
if (curl_errno($ch)) {
    $erro = 'Erro no cURL: ' . curl_error($ch);
    $api_response = null; // Garante que $api_response seja nulo em caso de erro
} else {
    // Decodifica a resposta JSON
    $responseData = json_decode($response, true);

    // Verifica se há candidatos na resposta e exibe o texto
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $api_response = $responseData['candidates'][0]['content']['parts'][0]['text'];
    } else {
        // Tratar casos onde a resposta não contém o texto esperado (ex: erro da API, bloqueio de segurança)
        $api_response = null;
        // Opcional: logar $responseData para depuração
        error_log("Resposta inesperada da Gemini API: " . json_encode($responseData));
    }
}

// Fecha a sessão cURL
curl_close($ch);

######################################################
###### se for BEM sucedido responda
if ($api_response) {
    ##########################
    #ARMAZENA O ENVIO
    $api_response = preg_replace('/^[\x00-\x1F\x7F\xA0]/u', '', $api_response);
    $api_response = trim($api_response);

    function limparInterrogacoes($texto) {
        // Remover '?' se estiver no início da frase
        $texto = preg_replace('/^\?/', '', $texto);

        // Remover '??' de qualquer lugar na frase
        $texto = str_replace('??', '', $texto);

        // Remover '?' que esteja logo após qualquer pontuação (como '.', ',', '!', etc.) e possivelmente espaços
        $texto = preg_replace('/([.,!;:])\s*\?/', '$1', $texto);

        return $texto;
    }

    $api_response = limparInterrogacoes($api_response);

    $stmt_ins_eg = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
    $stmt_ins_eg->bind_param("sss", $telefone, $api_response, $usuario_api);
    $query = $stmt_ins_eg->execute();
    $stmt_ins_eg->close();
include 'creditos.php';
    if ($query) {
        $id_msg = mysqli_insert_id($conn);
        $msg = $api_response;
        $response = enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg);
        $stmt_ins_hbg = $conn->prepare("INSERT INTO ia_historico (ia_msg, telefone_usuario, usuario_api, data_hora) VALUES (?, ?, ?, ?)");
        $stmt_ins_hbg->bind_param("ssss", $api_response, $telefone, $usuario_api, $hora);
        $query = $stmt_ins_hbg->execute();
        $stmt_ins_hbg->close();
        if ($query) {
            $stmt_upd_clg = $conn->prepare("UPDATE clientes SET time_atendimento = ? WHERE telefone = ? AND usuario_api = ?");
            $stmt_upd_clg->bind_param("sss", $dataHoraCompleta, $telefone, $usuario_api);
            $query = $stmt_upd_clg->execute();
            $stmt_upd_clg->close();
            if ($query) {
                mysqli_close($conn);
            }
        }
    }
} #if($api_response){
######################################################
###### se NÂO for bem sucedido responda
if (!$api_response) {
    ##### SO ENVIA DIZENDO QUE NAO ENTENDEU,
    # ESSA MENSAGEM VAI VHEGAR QUANDO A API TA SEM CREDIT U ELA TA COM ALGUM PROBLEMA
    $api_response = 'Não foi possível gerar uma resposta no momento. Por favor, tente novamente mais tarde.';
    if ($tipo_usuario == 2) {
        $api_response = 'Não a saldo na Gemini ou erro na API.'; // Mensagem mais específica para admins
    } else {
        $api_response = 'Não foi possível gerar uma resposta no momento. Por favor, tente novamente mais tarde.';
    }

    $stmt_ins_eng = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
    $stmt_ins_eng->bind_param("sss", $telefone, $api_response, $usuario_api);
    $query = $stmt_ins_eng->execute();
    $stmt_ins_eng->close();
    include 'creditos.php';
    if ($query) {
        $id_msg = mysqli_insert_id($conn);
        $msg = $api_response;
        $response = enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg);
    }
} #if(!$api_response){


    
    
    
    
    
    
    
    
    
}//////////GEMINI
    

    
}

?>