<?php
require_once __DIR__ . '/api_auth.php';
include '../conn.php';
include '../funcoes.php';
#include 'api_funcao.php';
include '../config_dados.php';
include 'editacodigo.php';
include 'salvajson.php';


date_default_timezone_set('America/Sao_Paulo'); // Definindo o fuso horário do Brasil



function gerarDataHoraCompleta() {
    // Definir o fuso horário para o Brasil
    date_default_timezone_set('America/Sao_Paulo');
    
    // Gerar a data e hora atuais no formato completo
    $dataHoraCompleta = date('Y-m-d H:i:s');
    
    return $dataHoraCompleta;
}






// Recebe os dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Extrai os dados
$telefone      = trim($data['telefone'] ?? 'desconhecido');
$telefone = preg_replace('/\D/', '', $telefone);


function isTelefoneValido($numero) {
    // Aceita números como +5511999999999 ou apenas 5511999999999
    return preg_match('/^\+?[1-9]\d{7,14}$/', $numero);
}

// Exemplo de uso
$input = $telefone ?? '';

if (!isTelefoneValido($input)) {
    http_response_code(400); // Bad Request
    exit();
}

if (strlen($telefone) < 11 || strlen($telefone) > 13 || !ctype_digit($telefone)) {
    exit;
}



$msg           = trim($data['texto'] ?? '');
#$id            = trim($data['id_mensagem'] ?? '');
$de            = trim($data['de'] ?? '');
$usuario_api   = trim($data['usuario'] ?? '');
$timestamp     = trim($data['timestamp'] ?? time());
$_POST['codigo'] = 'msg';
$_POST['msg '] = $msg ;
$user_id   = $usuario_api ;
$token   = 'NOME_Victor_Teste_CPF_010101';
$mediaBase64  = isset($data['media']['data'])     ? $data['media']['data']     : null;
$tipo_midia = isset($data['media']['type'])  ? $data['media']['type'] : null;

$linha = date('Y-m-d H:i:s')
      . ' | dados: '
      . print_r($data, true)  // retorna o dump em formato de string
    . "\n";

// Anexa ao arquivo
file_put_contents('log_mida.txt', $linha, FILE_APPEND);

// 🔥 Aqui você pode, se quiser, salvar no banco MySQL, enviar notificação, disparar eventos...

#echo json_encode(["status" => "✅ Mensagem recebida com sucesso"]);


$arquivo = "dadosovictor.txt";

// Lê todo o conteúdo enviado via POST (raw)
$inputJSON = file_get_contents("php://input");

// Salva o conteúdo no arquivo (adiciona no final)
file_put_contents($arquivo, $inputJSON . PHP_EOL, FILE_APPEND);

// Retorna uma resposta de sucesso
header('Content-Type: application/json');
echo json_encode(["status" => "sucesso", "mensagem" => "Dados salvos com sucesso."]);





$stmt_lista = $conn->prepare("SELECT * FROM lista_negra WHERE telefone = ? AND usuario_api = ?");
$stmt_lista->bind_param("ss", $telefone, $usuario_api);
$stmt_lista->execute();
$query_lista = $stmt_lista->get_result();
$total_lista = $query_lista->num_rows;
$stmt_lista->close();



if($total_lista == 1){
    exit();
}






if (isset($_POST['codigo'])){
if(!$data){    
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
$telefone = preg_replace('/\D/', '', $telefone);





// Telefones internacionais costumam ter entre 10 e 15 dígitos
$tamanho = strlen($telefone);
if ($tamanho < 10 || $tamanho > 15) {
    exit("❌ Não é um número de telefone válido (tamanho inválido).");
}

// DDI válidos (códigos internacionais mais usados)
$ddis_validos = [
    '1','20','27','30','31','32','33','34','36','39','44','49','52','54','55','56',
    '57','58','60','61','62','63','64','65','66','81','82','84','86','90','91','92',
    '93','94','95','98','212','213','216','218','220','221','222','223','224','225',
    '226','227','228','229','230','231','232','233','234','235','236','237','238',
    '239','240','241','242','243','244','245','246','247','248','249','250','251',
    '252','253','254','255','256','257','258','260','261','262','263','264','265',
    '266','267','268','269','290','291','297','298','299'
];

// Verifica se começa com um DDI válido
$eh_telefone = false;
foreach ($ddis_validos as $ddi) {
    if (strpos($telefone, $ddi) === 0) {
        $eh_telefone = true;
        break;
    }
}

// Se não for telefone real, encerra
if (!$eh_telefone) {
    exit("❌ Não é um número de telefone válido (DDI inválido).");
}

// Se chegou aqui, o número é considerado um telefone real.
// A variável $telefone continua com o valor validado.














$usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
}else{
$telefone      = trim($data['telefone'] ?? 'desconhecido');
$telefone = preg_replace('/\D/', '', $telefone);

$usuario_api   = trim($data['usuario'] ?? '');
    
}
   /// DADOS SERVIDOR/////

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = preg_replace('#^https?://#i', '', trim($rows_config['ip_vps']));
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token_bd  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
} 
    

    
    
///buscar configuracoes de login
#$sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'AND (tipo = '2' OR tipo = '1')";
$stmt_bu = $conn->prepare("SELECT * FROM login WHERE usuario_api = ? AND (tipo = '2' OR tipo = '1')");
$stmt_bu->bind_param("s", $usuario_api);
$stmt_bu->execute();
$query_busca_usuario = $stmt_bu->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
$stmt_bu->close();

while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {

    $qr_quantidade  = $rows_usuarios['qr_quantidade'];
    $tempo_code  = $rows_usuarios['tempo_code'];
    $funcao = $rows_usuarios['funcao'];
    $IA_boas_vindas = $rows_usuarios['IA_boas_vindas'];
    $IA_prompt = $rows_usuarios['IA_prompt'];
    $IA_despedida = $rows_usuarios['IA_despedida'];
    $tempo_final = $rows_usuarios['tempo_final'];
    $creditos = $rows_usuarios['creditos'];
    $plano = $rows_usuarios['plano'];
    $modo_atuante = $rows_usuarios['modo_atuante'];

}
////////////////////////////////////////////////
//// busca de planos
$stmt_planos = $conn->prepare("SELECT * FROM chave_ia_geral WHERE plano = ? ORDER BY RAND() LIMIT 1");
$stmt_planos->bind_param("s", $plano);
$stmt_planos->execute();
$query_busca_planos = $stmt_planos->get_result();
$stmt_planos->close();

if ($query_busca_planos && $query_busca_planos->num_rows > 0) {
    $rows_planos = $query_busca_planos->fetch_array();
    $chave = $rows_planos['chave'];
    $ia_nome = $rows_planos['nome'];
} 


//// buscar clientes


$stmt_buc = $conn->prepare("SELECT * FROM clientes WHERE usuario_api = ? AND telefone = ?");
$stmt_buc->bind_param("ss", $usuario_api, $telefone);
$stmt_buc->execute();
$query_busca_clientes = $stmt_buc->get_result();
$total_busca_clientes = $query_busca_clientes->num_rows;
$stmt_buc->close();

while($rows_clientes = $query_busca_clientes->fetch_array()) {
    $id_agendamento   = $rows_clientes['id_agendamento'];
    $situacao         = $rows_clientes['situacao'];
    $nome             = $rows_clientes['nome'];
    $time_reposta     = $rows_clientes['time_resposta'];
    $modo_atendimento = $rows_clientes['modo_atendimento'] ?? 'ia';
    $depto_atual      = $rows_clientes['depto_atual'] ?? null;
    $atendente_atual  = $rows_clientes['atendente_atual'] ?? null;
    $cliente_id_db    = $rows_clientes['id'] ?? null;
    $depto_pendente   = isset($rows_clientes['depto_pendente']) ? (int)$rows_clientes['depto_pendente'] : 0;
}

}///if (isset($_POST['codigo'])){



$dataHoraAtual = gerarDataHoraCompleta();

// Função para somar 20 minutos
function somaVinteMinutos($time) {
    $data = new DateTime($time);
    $data->modify('+20 minutes');
    return $data->format('Y-m-d H:i:s');
}

// Se a chave 'de' existir e o valor for 'sim'
if (isset($data['de']) && strtolower(trim($data['de'])) == 'sim') {

    // Soma 20 minutos no time atual
    $novo_time = somaVinteMinutos($dataHoraAtual);

    // Atualiza no banco de dados
    $stmt_upd_tr = $conn->prepare("UPDATE clientes SET time_resposta = ? WHERE telefone = ? AND usuario_api = ?");
    $stmt_upd_tr->bind_param("sss", $novo_time, $telefone, $usuario_api);
    $query = $stmt_upd_tr->execute();
    $stmt_upd_tr->close();

    // Encerra o script após atualizar
    exit();
}

// Verifica se o time já expirou


// Verifica se o tempo já expirou
if (strtotime($time_resposta) >= strtotime($dataHoraAtual)) {
    // Tempo expirou, encerra o processo
    exit();
}



  if (isset($data['poll_id'])) {
      
      
        // Captura o voto (Sim ou Não)
        $opcoes_enquete = $data['opcao_escolhida'] ?? 'Voto não identificado';
            include 'enquete.php';
            exit();
}


if($funcao == 'IA'){
    
    
    


if ((isset($data['telefone']) && $data['texto'] )) {
    #$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
   # $msg = isset($_POST['msg']) ? $_POST['msg'] : 'N/A';
    #$usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
    #$token_env = isset($_POST['token']) ? $_POST['token'] : 'N/A';
    #$token = $token_env;
    #$user_id =$usuario_api;
    
    

    
    
// Variáveis já lidas no início do script — não reler php://input (stream consumido)
$user_id = $usuario_api;
$token   = $token_bd;






  

    // Você pode imprimir ou usar $dadoCapturado aqui
    // echo $dadoCapturado;
    
    
    
  #include 'teste/qrcode.php';
#$response =  enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg);

// ✅ Log simples em arquivo
file_put_contents('log_mensagens.txt', date('Y-m-d H:i:s') . " | Tel: {$telefone} | Msg: {$msg} | total config: {$total_config} | busca usuario: {$total_busca_usuario}| busca cliente: {$total_busca_clientes}|  Usuario: {$usuario_api}\n", FILE_APPEND);


$_POST['codigo'] = '1';

}



if ((isset($_POST['codigo']) && $_POST['codigo'] == 'msg')) {
    #$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
   # $msg = isset($_POST['msg']) ? $_POST['msg'] : 'N/A';
    #$usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
    #$token_env = isset($_POST['token']) ? $_POST['token'] : 'N/A';
    #$token = $token_env;
    #$user_id =$usuario_api;
    
    
    
    



    $dadoCapturado = "Tipo: Mensagem de Texto\n";
    $dadoCapturado .= "Telefone: " . $telefone . "\n";
    $dadoCapturado .= "Usuário API: " . $usuario_api . "\n";
    $dadoCapturado .= "Token: " . $token_env . "\n";
    $dadoCapturado .= "Mensagem: " . $msg . "\n";
    

    // Você pode imprimir ou usar $dadoCapturado aqui
    // echo $dadoCapturado;
    
    
    
  #  include 'teste.php';

}





?>

<?php


if (!empty($data) && !empty($mediaBase64) && strpos($tipo_midia, 'audio') !== false) {
salvar();     

$audio_recebido = $mediaBase64;
if($ia_nome == 'openai'){   
include 'audio_openai.php';
#include 'teste.php';
}


if($ia_nome == 'gemini'){   
include 'audio_gemini.php';
#include 'teste.php';
}


}





if (isset($_POST['codigo']) && $_POST['codigo'] == 'audio') {
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
    $telefone = preg_replace('/\D/', '', $telefone);

    $usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
    $audio_recebido = isset($_POST['audio']) ? $_POST['audio'] : 'N/A';
    $token_env = isset($_POST['token']) ? $_POST['token'] : 'N/A';

    $token = $token_env;
    $user_id =$usuario_api;

    $dadoCapturado = "Tipo: Áudio\n";
    $dadoCapturado .= "Telefone: " . $telefone . "\n";
    $dadoCapturado .= "Usuário API: " . $usuario_api . "\n";
    $dadoCapturado .= "Áudio Recebido: " . $audio_recebido . "\n";

    // Você pode imprimir ou usar $dadoCapturado aqui
    // echo $dadoCapturado;
     
        
salvar();        
if($ia_nome == 'openai'){   
include 'audio_openai.php';
#include 'teste.php';
}


if($ia_nome == 'gemini'){   
include 'audio_gemini.php';
#include 'teste.php';
}



}



?>



<?php




if (isset($_POST['codigo']) && $_POST['codigo'] == 'img') {
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
    $telefone = preg_replace('/\D/', '', $telefone);

    $usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
    $imagemBase64 = isset($_POST['anexo']) ?  $_POST['anexo'] : 'N/A';
    $token_env = isset($_POST['token']) ? $_POST['token'] : 'N/A';
    $token = $token_bd;
    $user_id = $usuario_api;

    $dadoCapturado = "Tipo: Imagem\n";
    $dadoCapturado .= "Telefone: " . $telefone . "\n";
    $dadoCapturado .= "Usuário API: " . $usuario_api . "\n";
    $dadoCapturado .= "Imagem Recebida: " . $imagemBase64 . "\n";

    // Você pode imprimir ou usar $dadoCapturado aqui
    // echo $dadoCapturado;



  
if($ia_nome == 'openai'){   
include 'img_openai.php';
}
if($ia_nome == 'gemini'){   
include 'img_gemini.php';
}




}





if (!empty($data) && !empty($mediaBase64) && strpos($tipo_midia, 'image') !== false) {
salvar();     

$imagemBase64 = $mediaBase64;
if($ia_nome == 'openai'){   
include 'img_openai.php';
}
if($ia_nome == 'gemini'){   
include 'img_gemini.php';
}

}

?>



<?php
if (isset($_POST['codigo']) && $_POST['codigo'] == 'enquete') {
    $usuario_api = isset($_POST['usuario']) ? $_POST['usuario'] : 'N/A';
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : 'N/A';
    $telefone = preg_replace('/\D/', '', $telefone);

    $opcoes_enquete = isset($_POST['opcoes']) ? $_POST['opcoes'] : 'N/A';
    $token_env = isset($_POST['token']) ? $_POST['token'] : 'N/A';
    $token = $token_env;
    $user_id = $usuario_api;

    $dadoCapturado = "Tipo: Resposta de Enquete\n";
    $dadoCapturado .= "Usuário API: " . $usuario_api . "\n";
    $dadoCapturado .= "Telefone: " . $telefone . "\n";
    $dadoCapturado .= "Opção Selecionada: " . $opcoes_enquete . "\n";

    // Você pode imprimir ou usar $dadoCapturado aqui
    // echo $dadoCapturado;
    include 'enquete.php';
    #include 'teste.php';

}



?>

<?php
if (isset($msg)) {
/////////////////////////////////////////
/////////////////////////////////////////
/////////////////////////////////////////


/////////////////
////////////





// ── HORÁRIO DE ATENDIMENTO HUMANO ───────────────────────────────────────────
// Seg–Sex 08h–18h | Sáb 08h–12h | Dom/Fer: fechado
// Fora do horário: bloqueia transferência para fila e avisa o cliente.
function dentro_horario_atendimento(): bool {
    $agora = new DateTime('now', new DateTimeZone('America/Campo_Grande'));
    $dow   = (int)$agora->format('N'); // 1=seg … 7=dom
    $hora  = (int)$agora->format('H') * 60 + (int)$agora->format('i'); // minutos desde meia-noite
    if ($dow >= 1 && $dow <= 5) return ($hora >= 480 && $hora < 1080); // 08:00–18:00
    if ($dow === 6)             return ($hora >= 480 && $hora < 720);  // 08:00–12:00
    return false; // domingo
}

// ── ROTEAMENTO MULTI-ATENDENTE ──────────────────────────────────────────────
// Se o cliente existe e está em modo humano ou fila: salva mensagem e para aqui.
if ($total_busca_clientes == 1 && isset($modo_atendimento) && $modo_atendimento !== 'ia') {
    $hora_hist = date('Y-m-d H:i:s');
    $stmt_ihm = $conn->prepare("INSERT INTO ia_historico (ia_msg, usuario_msg, telefone_usuario, usuario_api, login_historico, data_hora, tipo_remetente) VALUES ('', ?, ?, ?, '', ?, 'cliente')");
    $stmt_ihm->bind_param("ssss", $msg, $telefone, $usuario_api, $hora_hist);
    $stmt_ihm->execute();
    $stmt_ihm->close();
    exit; // Não aciona a IA — aguarda atendente humano
}

// Verifica se alguma palavra-chave de departamento foi acionada.
// Aplica tanto para clientes existentes em modo IA ($total_busca_clientes == 1)
// quanto para novos contatos ($total_busca_clientes == 0), de forma que "falar com atendente"
// funcione mesmo na primeira mensagem.
$transferido_depto = false;
$eh_modo_ia = ($total_busca_clientes == 1 && isset($modo_atendimento) && $modo_atendimento === 'ia');
$eh_novo    = ($total_busca_clientes == 0);
$fora_do_horario = !dentro_horario_atendimento();

if (($eh_modo_ia || $eh_novo) && !empty($msg)) {
    $stmt_deps = $conn->prepare("SELECT id, nome, palavras_chave, msg_transferencia, notificar_atendentes, proximo_atendente FROM departamentos WHERE usuario_api=? AND ativo=1 AND palavras_chave IS NOT NULL AND palavras_chave != ''");
    $stmt_deps->bind_param("s", $usuario_api);
    $stmt_deps->execute();
    $res_deps = $stmt_deps->get_result();
    $stmt_deps->close();
    while ($dep_row = $res_deps->fetch_assoc()) {
        $palavras  = array_map('trim', explode(',', strtolower($dep_row['palavras_chave'])));
        $msg_lower = mb_strtolower($msg, 'UTF-8');
        foreach ($palavras as $pal) {
            if ($pal !== '' && mb_strpos($msg_lower, $pal) !== false) {
                // ── Fora do horário: bot conduz em modo SPIN, não transfere agora ────────
                if ($fora_do_horario) {
                    $hora_spin = date('Y-m-d H:i:s');
                    if ($eh_novo) {
                        // Cria cliente em modo IA com depto_pendente registrado
                        $id_agenda_spin = strtoupper(dechex(mt_rand(0, 0xFFFF)));
                        $stmt_ins_spin = $conn->prepare("INSERT INTO clientes (telefone, usuario_api, time_atendimento, id_agendamento, modo_atendimento, depto_pendente) VALUES (?, ?, ?, ?, 'ia', ?)");
                        $stmt_ins_spin->bind_param("ssssi", $telefone, $usuario_api, $hora_spin, $id_agenda_spin, $dep_row['id']);
                        $stmt_ins_spin->execute();
                        $stmt_ins_spin->close();
                        $cliente_id_db = (int)$conn->insert_id;
                    } elseif (!empty($cliente_id_db)) {
                        // Cliente já existente: registra depto_pendente e mantém modo IA
                        $stmt_upd_spin = $conn->prepare("UPDATE clientes SET depto_pendente=?, modo_atendimento='ia', time_atendimento=? WHERE id=? AND usuario_api=?");
                        $stmt_upd_spin->bind_param("isis", $dep_row['id'], $hora_spin, $cliente_id_db, $usuario_api);
                        $stmt_upd_spin->execute();
                        $stmt_upd_spin->close();
                    }
                    // Salva mensagem do cliente no histórico
                    $stmt_ihm_spin = $conn->prepare("INSERT INTO ia_historico (usuario_msg, telefone_usuario, usuario_api, data_hora, tipo_remetente) VALUES (?, ?, ?, ?, 'cliente')");
                    $stmt_ihm_spin->bind_param("ssss", $msg, $telefone, $usuario_api, $hora_spin);
                    $stmt_ihm_spin->execute();
                    $stmt_ihm_spin->close();
                    // Mensagem de introdução: informa fora do horário e apresenta a Ana
                    $msg_intro_spin = "Olá! 😊 Nosso horário de atendimento humano é *segunda a sexta das 8h às 18h* e *sábados das 8h às 12h*.\n\nMas não se preocupe — *Ana*, nossa assistente virtual, está disponível agora e pode te ajudar com todas as informações sobre matrícula! Para começar, pode me contar um pouco sobre você?";
                    $stmt_ev_spin = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
                    $stmt_ev_spin->bind_param("sss", $telefone, $msg_intro_spin, $usuario_api);
                    $stmt_ev_spin->execute();
                    $id_spin = (int)$conn->insert_id;
                    $stmt_ev_spin->close();
                    enviarMensagem($servidor, $porta, $usuario_api, $token_bd, $telefone, $msg_intro_spin, $id_spin);
                    // Salva mensagem de introdução no histórico como mensagem da IA
                    $stmt_ih_spin = $conn->prepare("INSERT INTO ia_historico (ia_msg, telefone_usuario, usuario_api, data_hora, tipo_remetente) VALUES (?, ?, ?, ?, 'ia')");
                    $stmt_ih_spin->bind_param("ssss", $msg_intro_spin, $telefone, $usuario_api, $hora_spin);
                    $stmt_ih_spin->execute();
                    $stmt_ih_spin->close();
                    exit; // Aguarda próxima mensagem do lead para iniciar SPIN
                }
                // ─────────────────────────────────────────────────────────────────────
                $hora_hist = date('Y-m-d H:i:s');
                if ($eh_novo) {
                    // Novo contato: criar registro de cliente já em modo 'fila'
                    $id_agenda = strtoupper(dechex(mt_rand(0, 0xFFFF)));
                    $stmt_ins_c = $conn->prepare("INSERT INTO clientes (telefone, usuario_api, time_atendimento, id_agendamento, modo_atendimento, depto_atual) VALUES (?, ?, ?, ?, 'fila', ?)");
                    $stmt_ins_c->bind_param("ssssi", $telefone, $usuario_api, $hora_hist, $id_agenda, $dep_row['id']);
                    $stmt_ins_c->execute();
                    $novo_cli_id = mysqli_insert_id($conn);
                    $stmt_ins_c->close();
                    // Salva a mensagem do cliente no histórico
                    $stmt_ihm_n = $conn->prepare("INSERT INTO ia_historico (ia_msg, usuario_msg, telefone_usuario, usuario_api, login_historico, data_hora, tipo_remetente) VALUES ('', ?, ?, ?, '', ?, 'cliente')");
                    $stmt_ihm_n->bind_param("ssss", $msg, $telefone, $usuario_api, $hora_hist);
                    $stmt_ihm_n->execute();
                    $stmt_ihm_n->close();
                } elseif (!empty($cliente_id_db)) {
                    // Cliente existente em modo IA: persiste a mensagem que acionou a transferência
                    $hora_trig = date('Y-m-d H:i:s');
                    $stmt_ihm_e = $conn->prepare("INSERT INTO ia_historico (ia_msg, usuario_msg, telefone_usuario, usuario_api, login_historico, data_hora, tipo_remetente) VALUES ('', ?, ?, ?, '', ?, 'cliente')");
                    $stmt_ihm_e->bind_param("ssss", $msg, $telefone, $usuario_api, $hora_trig);
                    $stmt_ihm_e->execute();
                    $stmt_ihm_e->close();
                    // Muda para fila
                    $stmt_upd_t = $conn->prepare("UPDATE clientes SET modo_atendimento='fila', depto_atual=?, atendente_atual=NULL WHERE id=? AND usuario_api=?");
                    $stmt_upd_t->bind_param("iis", $dep_row['id'], $cliente_id_db, $usuario_api);
                    $stmt_upd_t->execute();
                    $stmt_upd_t->close();
                }
                // ── Round-robin: pré-atribui lead ao próximo atendente (se dept configurado) ──
                if (!empty($dep_row['proximo_atendente'])) {
                    $_rr_atual  = $dep_row['proximo_atendente'];
                    $_rr_cli_id = $eh_novo ? (int)($novo_cli_id ?? 0) : (int)($cliente_id_db ?? 0);
                    if ($_rr_cli_id > 0) {
                        // Descobre o próximo da fila (outro atendente vinculado ao depto)
                        $_s_rr = $conn->prepare("SELECT login_atendente FROM atendentes_depto WHERE depto_id=? AND usuario_api=? AND login_atendente != ? ORDER BY id ASC LIMIT 1");
                        if ($_s_rr) {
                            $_s_rr->bind_param("iss", $dep_row['id'], $usuario_api, $_rr_atual);
                            $_s_rr->execute();
                            $_r_rr = $_s_rr->get_result();
                            $_s_rr->close();
                            $_rr_proximo = ($_r_rr && $_r_rr->num_rows > 0) ? $_r_rr->fetch_assoc()['login_atendente'] : $_rr_atual;
                        } else {
                            $_rr_proximo = $_rr_atual;
                        }
                        // Pré-atribui o cliente ao atendente atual
                        $conn->query("UPDATE clientes SET atendente_atual='" . $conn->real_escape_string($_rr_atual) . "' WHERE id=" . $_rr_cli_id . " AND usuario_api='" . $conn->real_escape_string($usuario_api) . "'");
                        // Avança o ponteiro (otimista: só atualiza se ainda for o mesmo valor)
                        $conn->query("UPDATE departamentos SET proximo_atendente='" . $conn->real_escape_string($_rr_proximo) . "' WHERE id=" . (int)$dep_row['id'] . " AND proximo_atendente='" . $conn->real_escape_string($_rr_atual) . "'");
                        unset($_s_rr, $_r_rr, $_rr_proximo, $_rr_cli_id, $_rr_atual);
                    }
                }
                // Envia mensagem de transferência para o cliente
                // Fora do horário: substitui pela mensagem de aviso (lead entra na fila silenciosamente)
                if ($fora_do_horario) {
                    $msg_transf = "Recebemos sua mensagem! 😊 Nosso horário de atendimento humano é *segunda a sexta das 8h às 18h* e *sábados das 8h às 12h*.\n\nSua conversa foi registrada e nosso atendente entrará em contato assim que retornar. 🕐";
                    $_notif_notificar_override = 0; // Suprime notificação WhatsApp ao atendente
                } else {
                    $msg_transf = !empty($dep_row['msg_transferencia'])
                        ? $dep_row['msg_transferencia']
                        : 'Aguarde, vou transferir você para um de nossos atendentes. Em breve retornaremos! 😊';
                }
                $stmt_ev_t = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
                $stmt_ev_t->bind_param("sss", $telefone, $msg_transf, $usuario_api);
                $stmt_ev_t->execute();
                $id_msg_t = mysqli_insert_id($conn);
                $stmt_ev_t->close();
                enviarMensagem($servidor, $porta, $user_id, $token_bd, $telefone, $msg_transf, $id_msg_t);
                // Salva resposta de transferência no histórico
                $hora_hist2 = date('Y-m-d H:i:s');
                $stmt_ih_t = $conn->prepare("INSERT INTO ia_historico (ia_msg, usuario_msg, telefone_usuario, usuario_api, login_historico, data_hora, tipo_remetente) VALUES (?, '', ?, ?, '', ?, 'ia')");
                $stmt_ih_t->bind_param("ssss", $msg_transf, $telefone, $usuario_api, $hora_hist2);
                $stmt_ih_t->execute();
                $stmt_ih_t->close();
                // Captura dados do depto para notificar atendentes logo após os loops
                $_notif_depto_id     = (int)$dep_row['id'];
                $_notif_depto_nome   = $dep_row['nome'];
                // Fora do horário: suprime notificação WhatsApp (atendente vê na fila ao abrir o painel)
                $_notif_notificar    = isset($_notif_notificar_override) ? 0 : (int)($dep_row['notificar_atendentes'] ?? 1);
                $_notif_cli_tel      = $telefone;
                $_notif_cli_nome     = isset($nome) && $nome !== '' ? $nome : $telefone;
                $transferido_depto = true;
                break 2; // Sai dos dois loops
            }
        }
    }
}
// ── Notificação WhatsApp para atendentes do setor (tarefa #141) ──────────────
if ($transferido_depto && !empty($_notif_depto_id) && !empty($_notif_notificar)) {
    // Busca atendentes do setor que têm telefone_notif cadastrado e estão ativos
    $_stmt_nat = $conn->prepare(
        "SELECT l.telefone_notif FROM atendentes_depto ad
         INNER JOIN login l ON l.login = ad.login_atendente
         WHERE ad.depto_id=? AND ad.usuario_api=?
           AND l.autorizado='2'
           AND l.telefone_notif IS NOT NULL
           AND l.telefone_notif != ''
         LIMIT 20"
    );
    if ($_stmt_nat) {
        $_stmt_nat->bind_param("is", $_notif_depto_id, $usuario_api);
        $_stmt_nat->execute();
        $_res_nat = $_stmt_nat->get_result();
        $_stmt_nat->close();
        $_msg_notif = "🔔 *Nova conversa na fila!*\n*Setor:* {$_notif_depto_nome}\n*Cliente:* {$_notif_cli_nome}\n\nAcesse o painel para atender.";
        while ($_row_nat = $_res_nat->fetch_assoc()) {
            $_tel_at = preg_replace('/\D/', '', $_row_nat['telefone_notif']);
            if (strlen($_tel_at) >= 10) {
                $_stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
                if ($_stmt_env) {
                    $_stmt_env->bind_param("sss", $_tel_at, $_msg_notif, $usuario_api);
                    $_stmt_env->execute();
                    $_id_env = mysqli_insert_id($conn);
                    $_stmt_env->close();
                    enviarMensagem($servidor, $porta, $user_id, $token_bd, $_tel_at, $_msg_notif, $_id_env);
                }
            }
        }
        unset($_res_nat, $_msg_notif, $_row_nat, $_tel_at, $_stmt_env, $_id_env);
    }
    unset($_stmt_nat, $_notif_depto_id, $_notif_depto_nome, $_notif_notificar, $_notif_cli_tel, $_notif_cli_nome);
}
if ($transferido_depto) exit;
// ── FIM ROTEAMENTO MULTI-ATENDENTE ──────────────────────────────────────────

// ── SPIN Selling: augmenta o prompt quando lead está em modo fora do horário ─
// Carrega depto_pendente se necessário
if (!isset($depto_pendente)) $depto_pendente = 0;

if ($depto_pendente > 0 && $fora_do_horario) {
    // Busca nome do departamento para contextualizar o SPIN
    $stmt_dn = $conn->prepare("SELECT nome FROM departamentos WHERE id=? AND usuario_api=?");
    $stmt_dn->bind_param("is", $depto_pendente, $usuario_api);
    $stmt_dn->execute();
    $res_dn = $stmt_dn->get_result();
    $stmt_dn->close();
    $nome_depto_spin = ($res_dn && $res_dn->num_rows > 0) ? $res_dn->fetch_assoc()['nome'] : '';

    $IA_prompt .= "\n\n--- INSTRUÇÃO ESPECIAL (MODO FORA DO HORÁRIO) ---\n"
        . "Você está atendendo um lead que solicitou falar com a equipe humana do setor \"{$nome_depto_spin}\", mas estamos fora do horário de atendimento presencial.\n"
        . "Seu papel agora é conduzir a conversa usando a metodologia SPIN Selling: faça perguntas curtas e objetivas que explorem a Situação, o Problema, a Implicação e a Necessidade de solução do lead, com o objetivo de guiá-lo em direção à matrícula.\n"
        . "Regras:\n"
        . "- Use APENAS informações contidas no seu prompt — não invente dados, valores ou datas.\n"
        . "- Faça no máximo uma pergunta por mensagem.\n"
        . "- Seja calorosa, objetiva e focada em entender as necessidades do lead.\n"
        . "- Quando a conversa chegar a uma conclusão natural (lead demonstrou interesse claro, pediu para ser contactado, ou encerrou a conversa), inclua EXATAMENTE este marcador no FINAL da sua última mensagem (sem exibi-lo ao usuário, sem espaço antes ou depois): [##TRANSFERIR##]\n"
        . "--- FIM DA INSTRUÇÃO ESPECIAL ---";
}

if($creditos > 0){
////se o cliente nao existe
if($total_busca_clientes == 0){
    include 'primeiro_contato.php';
}

if($total_busca_clientes == 1){
    include 'segundo_contato_ia.php';
}

}///if($creditos > 0){




}///if (isset($_POST['codigo'])) {///

?>


<?php

}////if($funcao == 'desativado');{

?>
<?php

#include 'cron.php';

?>