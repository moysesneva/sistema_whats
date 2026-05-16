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
    $servidor  = $rows_config['ip_vps'];
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
$stmt_bu = $conn->prepare("SELECT * FROM login WHERE usuario_api = ? AND tipo = '2'");
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
    $id_agendamento = $rows_clientes['id_agendamento'];
    $situacao = $rows_clientes['situacao'];
    $nome = $rows_clientes['nome'];
    $time_reposta = $rows_clientes['time_resposta'];
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
if (strtotime($time_reposta) >= strtotime($dataHoraAtual)) {
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
    
    

    
    
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$telefone      = trim($data['telefone'] ?? 'desconhecido');
$telefone = preg_replace('/\D/', '', $telefone);

$msg           = trim($data['texto'] ?? '');
$id            = trim($data['id_mensagem'] ?? '');
$de            = trim($data['de'] ?? '');
$usuario_api   = trim($data['usuario'] ?? '');
$timestamp     = trim($data['timestamp'] ?? time());
$user_id   = $usuario_api ;
$token   = 'NOME_Victor_Teste_CPF_010101';






  

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