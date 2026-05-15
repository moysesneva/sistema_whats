<?php
include '../conn.php';
include '../funcoes.php';
#include 'api_funcao.php';
include '../config_dados.php';
include 'editacodigo.php';
include 'salvajson.php';


date_default_timezone_set('America/Sao_Paulo'); // Definindo o fuso horário do Brasil
#header('Content-Type: text/html; charset=utf-8');
#salvar_dados_resquest();



// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verifica se os parâmetros necessários foram enviados
    if (isset($_POST['codigo'])) {

        // Recebe os dados do POST e faz, se necessário, a sanitização
        $codigo   = $_POST['codigo'];
        $telefone = $_POST['telefone'];
        $usuario_api  = $_POST['usuario'];
        $msg      = $_POST['msg'];
        $token    = $_POST['token'];
        $user_id = $_POST['usuario'];
        
      
}}


?>












<?php



// Consulta SQL para buscar uma chave aleatória que tenha `usuario_api` preenchido
$sql_busca_chave_aleatoria = "SELECT * FROM chave WHERE usuario_api  = '$usuario_api' ORDER BY RAND() LIMIT 1";


$query_busca_chave_aleatoria = mysqli_query($conn, $sql_busca_chave_aleatoria);

// Verificando se a consulta retornou algum resultado
if (mysqli_num_rows($query_busca_chave_aleatoria) > 0) {
    $chave_aleatoria = mysqli_fetch_assoc($query_busca_chave_aleatoria);
    
    // Obtendo os dados da chave
    $chave = $chave_aleatoria['chave'];
    #$usuario_api = $chave_aleatoria['usuario_api'];
    
   
}


$apiKey = $chave ;




$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $webhook  = $rows_config['webhook'];
    $validade  = $rows_config['validade'];
    $link_pagamento =$rows_config['link_pagamento'];
     $preco  = $rows_config['preco'];
      $telefone  = $rows_config['telefone'];

}


$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = $rows_config['ip_vps'];
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}









function gerarDataHoraCompleta() {
    // Definir o fuso horário para o Brasil
    date_default_timezone_set('America/Sao_Paulo');
    
    // Gerar a data e hora atuais no formato completo
    $dataHoraCompleta = date('Y-m-d H:i:s');
    
    return $dataHoraCompleta;
}


function obterDataHora($fusoHorario = 'America/Sao_Paulo', $formatoData = 'd/m/Y', $formatoHora = 'H:i:s') {
    date_default_timezone_set($fusoHorario); // Define o fuso horário desejado
    
    // Array de dias da semana em português
    $diasDaSemana = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];
    
    // Obtém o dia da semana em inglês e o converte para português
    $diaSemanaIngles = date('l'); // Retorna o dia da semana em inglês
    $diaSemanaPortugues = $diasDaSemana[$diaSemanaIngles]; // Traduz para português

    // Formata a data e hora
    $data = date($formatoData);
    $hora = date($formatoHora);

    // Retorna a frase completa
    return "O dia é $diaSemanaPortugues, a data é $data e a hora atual é $hora.";
}

// Exemplo de uso



$obterDataHora = obterDataHora(); // Data e hora do Brasil

$obterDataHora = 'Para consultas e referencias para jaudar os cliente:'. $obterDataHora;




function verificaSimNao($texto) {
    // Converte o texto para minúsculas para garantir a comparação correta
    $texto = strtolower($texto);

    // Verifica se a string contém 's' ou 'n'
    if (strpos($texto, 's') !== false) {
        return "sim";
    } elseif (strpos($texto, 'n') !== false) {
        return "não";
    } else {
        return "Valor não encontrado";
    }
}


if ($_POST['codigo'] == 'enquete') {
$usuario_api = $_POST['usuario'];    
$telefone = $_POST['telefone'];     
$texto  = $_POST['opcoes'];
$texto  =   verificaSimNao($texto) ; 
 
 if($texto == 'sim'){



$sql = "UPDATE agendamento SET confirmacao = '1',lembrete = '3' WHERE usuario_api='$usuario_api' AND cliente_telefone = '$telefone' AND lembrete = '2'";

$query = mysqli_query($conn,$sql);

if (!$query) {
    die("Erro na atualização: " . mysqli_error($conn));
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$resultado','2','$usuario_api')";
$query = mysqli_query($conn,$sql);  
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $resultado, $id_msg);



} else {
 $resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

 $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $resultado, $id_msg);
}











 }
 
 
 if($texto == 'não'){



$sql = "UPDATE agendamento SET confirmacao = '2',lembrete = '3' WHERE usuario_api='$usuario_api' AND cliente_telefone = '$telefone' AND lembrete = '2'";

$query = mysqli_query($conn,$sql);

if (!$query) {
    die("Erro na atualização: " . mysqli_error($conn));
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$resultado','2','$usuario_api')";
$query = mysqli_query($conn,$sql);  
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $resultado, $id_msg);



} else {

$resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $resultado, $id_msg);
}




 }
 
 
}




// Verifica se os dados foram recebidos corretamente
if (isset($_POST['codigo']) == 'qrcode') {
    // Atribui cada dado a uma variável
    $qrcode = $_POST['qrcode'];
    $usuario_api = $_POST['usuario'];
$hora = hora();
  
    // Salva o conteúdo no arquivo post.txt




$sql_busca_config = "SELECT * FROM config ";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {

    $webhook  = $rows_config['webhook'];
}



$usuario_api = $_POST['usuario'];
$sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {

    $qr_quantidade  = $rows_usuarios['qr_quantidade'];
    $tempo_code  = $rows_usuarios['tempo_code'];
    $funcao = $rows_usuarios['funcao'];
    $IA_boas_vindas = ($rows_usuarios['IA_boas_vindas']);
    $IA_prompt = ($rows_usuarios['IA_prompt']);
    $IA_despedida = ($rows_usuarios['IA_despedida']);
    $tempo_final = $rows_usuarios['tempo_final'];


}


$sql = "UPDATE login SET qrcode = '$qrcode',tempo_code = '$hora',situacao = 'ativado' WHERE usuario_api='$usuario_api'";
$query_qrcode = mysqli_query($conn,$sql);



if($query_qrcode && $qrcode){

$sql = "DELETE FROM envio WHERE usuario_api='$usuario_api' AND status = '1'";    
$query = mysqli_query($conn,$sql);

}



if($total_busca_usuario == 1){
        // Atualiza o valor de 'qr_quantidade' no banco de dados
 $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);

#$qr_quantidade = 0 ;  
 // Incrementa somente se 'qr_quantidade' for menor que 200
    if ($qr_quantidade == 900) {
        
        $usuario_api = $_POST['usuario'];       
 #$sql = "INSERT INTO gerenciador (usuario_api,comando) VALUES ('$usuario_api','stop_conta')";
#$query = mysqli_query($conn,$sql);    
    

$nova_quantidade = 0;

        // Atualiza o valor de 'qr_quantidade' no banco de dados
 $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);

#$qr_quantidade = 0 ;       


}







    // Incrementa somente se 'qr_quantidade' for menor que 200
    if ($qr_quantidade <= 900) {
        $nova_quantidade = $qr_quantidade + 1;

        // Atualiza o valor de 'qr_quantidade' no banco de dados
       $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);


        // Verifica se a atualização foi bem-sucedida
        if ($query_atualiza) {
             $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);

        } else {
            echo "Erro ao atualizar a quantidade.";
             $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);

        }
    } else {
       echo "Quantidade máxima de 200 atingida.";
        $sql_atualiza = "UPDATE login SET  qrcode = '$qrcode',qr_data = '$data_atual' WHERE usuario_api = '$usuario_api'";
$query_atualiza = mysqli_query($conn, $sql_atualiza);

    }
} else {
    echo "Usuário não encontrado.";
}

}














if ($_POST['codigo'] == 'audio') {
  $telefone = $_POST['telefone'];
  $usuario_api = $_POST['usuario'];
   $audio_recebido = $_POST['audio'];

function salvar_audio_temporario($audioBase64) {
    // Decodificar o áudio base64
    $audioDecodificado = base64_decode($audioBase64);

    // Gerar um nome de arquivo temporário
    $nomeArquivo = 'audio/' . uniqid('audio_', true) . '.mp3';

    // Salvar o áudio na página 'audio'
    file_put_contents($nomeArquivo, $audioDecodificado);

    return $nomeArquivo;
}

$caminho_audio = salvar_audio_temporario($audio_recebido);


$audio_file_path = $caminho_audio;


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.openai.com/v1/audio/transcriptions',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'file' => new CURLFILE($audio_file_path),
    'model' => 'whisper-1'
  ),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $chave
  ),
));

$response = curl_exec($curl);

curl_close($curl);


$msg =  $response;
#$msg2 =  'audio recebido: '.$response;
#$sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$telefone', '$msg', '1', '$usuario_api')";
#mysqli_query($conn, $sql);


if($response){
       include 'segundo_contato_ia.php';

    
}

}#if ($_POST['codigo'] == 'img') {














if ($_POST['codigo'] == 'img') {

    $telefone = $_POST['telefone'];
    $usuario_api = $_POST['usuario'];
    #$msg = 'reciba a imagem'. $caminhoImagem;
    $imagemBase64 = $_POST['anexo'];

    #$sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$telefone', '$msg', '1', '$usuario_api')";
   # mysqli_query($conn, $sql);

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











#################################################################
#################################################################
#################################################################







function upload_imagem_freeimagehost($imagemBase64) {
    // Sua chave de API do FreeImageHost
    $api_key = '6d207e02198a847aa98d0a2a901485a5';

    // Configuração do cURL para fazer o upload da imagem
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://freeimage.host/api/1/upload");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'key' => $api_key,
        'action' => 'upload',
        'source' => $imagemBase64,
        'format' => 'json'
    ]);

    // Executa o cURL e armazena a resposta
    $resposta = curl_exec($ch);
    curl_close($ch);

    // Decodifica a resposta JSON
    $resposta_array = json_decode($resposta, true);

    // Verifica se a resposta contém a URL da imagem
    if (isset($resposta_array['image']['url'])) {
        return $resposta_array['image']['url']; // Retorna a URL da imagem
    } else {
        // Caso haja erro, retorna a mensagem de erro da resposta
        return "Erro ao fazer upload da imagem: " . ($resposta_array['error']['message'] ?? 'Erro desconhecido');
    }
}
// Exemplo de uso da função
#$urlImagem = upload_imagem_freeimagehost($imagemBase64);
#echo "URL da Imagem: " . $urlImagem;















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
            echo 'Erro na requisição: ' . $resposta;
        }
    }

    // Fechar a conexão cURL
    curl_close($ch);


// Exemplo de uso
#$urlImagem  = "https://mygpt4.store/login/painel/api/img/imagem_673691552d0a62.16618956.jpg";
#$resultado = descrever_imagem($urlImagem, $apiKey);
#sleep(5);
    
    
    
    
    
    
    
    
    
    #$descricaoImagem = descrever_imagem($caminhoImagem, $apiKey);

    // Remover a imagem temporária
    

    // Continuar com a inserção da descrição
 #if($resultado)  {
 #  $msg = 'descrição da imagem: ' . $urlImagem .' DADOS DA IAMGEM ' .  $msg  ;
 #       $sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$telefone', '$msg', '1', '$usuario_api')";
#       mysqli_query($conn, $sql);
#}

    // Consulta clientes
    $sql_busca_clientes = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND telefone = '$telefone'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    $total_busca_clientes = mysqli_num_rows($query_busca_clientes);

    if ($total_busca_clientes > 0) {
        while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
            $id_agendamento = $rows_clientes['id_agendamento'];
            $situacao = $rows_clientes['situacao'];
            $nome = $rows_clientes['nome'];
        }
    }









   include 'segundo_contato_ia.php';
}


























if ($_POST['codigo'] == 'msg') {
    salvar_dados_resquest();


#$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
#$servidor = barra($servidor_recebido);
$telefone = $_POST['telefone'];
$msg = $_POST['msg'];
$usuario_api = $_POST['usuario'];
#$telefone = '553184767330';
#$usuario = 'agenda_3184767330';
#$id_msg = 0;
#$msg = 'Bom dia como voce esta....';
#EnviarMsg($telefone,$msg,$id_msg,$usuario, $token, $servidor);



#VAMOS BUSCAR SE O CLIENTE EXISTE
$sql_busca_clientes = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND telefone = '$telefone'";
$query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
$total_busca_clientes = mysqli_num_rows($query_busca_clientes);

while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
    
    $id_agendamento = $rows_clientes['id_agendamento'];
         $situacao = $rows_clientes['situacao'];
         $nome = $rows_clientes['nome'];

}




#SE EXISTE
if($total_busca_clientes == 1){

if($funcao == "IA"){
    
include 'segundo_contato_ia.php';     
#$msg = 'segundo contato';   
#$sql = "INSERT INTO envio (telefone,msg,status,usuario_api) VALUES ('$telefone','$msg','1','$usuario_api')";
#$query = mysqli_query($conn,$sql);    
    
    
}
##################################################    
##################################################    
##################################################    
    




if($funcao == 'ENQUETE'){
$msg = 'segundo contato';   
$sql = "INSERT INTO envio (telefone,msg,status,usuario_api) VALUES ('$telefone','$msg','1','$usuario_api')";
$query = mysqli_query($conn,$sql);    
    
    
}#if($total_busca_clientes == 1){







}


#NÃO EXISTE
if($total_busca_clientes == 0){
    
    
    
    
if($funcao == "IA"){    
include 'primeiro_contato.php';   
$msg = $IA_boas_vindas; 
$user_id = $usuario_api;

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$IA_boas_vindas','2','$usuario_api')";
$query = mysqli_query($conn,$sql);
if($query){
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

 $response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg);
}

}#if($funcao == 'IA'){
##################################################    
##################################################    
##################################################  


if($funcao == 'ENQUETE'){
    
    
}#if($funcao == 'ENQUETE'){    
    


  





#$sql = "INSERT INTO envio (telefone,msg,status,usuario_api) VALUES ('$telefone','$msg','1','$usuario_api')";
#$query = mysqli_query($conn,$sql);


}#if($total_busca_clientes == 0){





}#if ($_POST['codigo'] == 'msg') {
   
?>





<?php
// Lê o JSON enviado via POST
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

// Verifica se houve erro na decodificação
if ($data === null) {
    http_response_code(400);
    echo "Erro: JSON inválido.";
    exit;
}

// Verifica se a ação é "CronJob"

    // Extraia os dados necessários
    $usuario_api = isset($data['usuario']) ? $data['usuario'] : "desconhecido";
    $message = isset($data['message']) ? $data['message'] : "";
    



#####################################
    #################
    ###############

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $webhook  = $rows_config['webhook'];
    $validade  = $rows_config['validade'];
    $link_pagamento =$rows_config['link_pagamento'];
     $preco  = $rows_config['preco'];
      #$telefone  = $rows_config['telefone'];

}



$sql = "SELECT * FROM login WHERE usuario_api = '$usuario_api' ";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){


$tempo = $lista_login['tempo_final'];
$despedida = $lista_login['IA_despedida'];
$agenda_verfica = $lista_login['agenda_verfica'];

} 
#######################################
######################################
#agora envi a mensagemde despedida
 
// Ajustar o fuso horário para o Brasil
mysqli_query($conn, "SET time_zone = '-03:00'");

// Definir a consulta
#$sql = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND situacao = '1' AND time_atendimento <= DATE_SUB(NOW(), INTERVAL '$tempo' MINUTE) LIMIT 1";
$sql = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND (situacao = '1' OR situacao IS NULL) AND time_atendimento <= DATE_SUB(NOW(), INTERVAL '$tempo' MINUTE) LIMIT 1";

// Executar a consulta
$query = mysqli_query($conn, $sql);
$total = mysqli_num_rows($query);

if (!$query) {
    die("Erro na consulta SQL: " . mysqli_error($conn));
}
if($total > 0){
while($lista_login = mysqli_fetch_array($query)){

$id = $lista_login['id'];
$telefone = $lista_login['telefone'];
######################################
########## atualizar a siotuação do cliente
######################################


$sql = "UPDATE clientes SET situacao = '2' WHERE id = '$id'";
$query = mysqli_query($conn,$sql);

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$despedida','2','$usuario_api')";
$query = mysqli_query($conn,$sql);

        $id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção
        $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $despedida, $id_msg);

$sql = "DELETE FROM ia_historico WHERE usuario_api='$usuario_api' AND telefone_usuario = '$telefone'";
$query = mysqli_query($conn,$sql);

   // **Pausa de 500ms antes de continuar para o próximo loop**
    #usleep(500000); 

}
}


#######################################################################################
##################################################################
#função pra enviar enquete














$sql = "SELECT * FROM login WHERE usuario_api = '$usuario_api' ";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){



$tempo_verificar = $lista_login['tempo_verifica'];

}   
 
 
 
 
 
date_default_timezone_set('America/Sao_Paulo'); // Definindo o fuso horário do Brasil

#$usuario_api = 'agenda_3184767335'; // Usuário específico para filtrar
$data_atual = date('Y-m-d'); // Data atual
#$data_atual = '2024-11-18';
$hora_atual = date('H:i'); // Hora atual (apenas horas e minutos)
#$usuario_api = $_POST['usuario'];   
#$telefone = $_POST['telefone'];
// Consulta SQL para encontrar o horário mais próximo


// Variáveis
#$usuario_api = 'agenda_3184767335';
#$tempo_verificar = 10; // Tempo em minutos para o lembrete (exemplo: 10 minutos)

// Data e hora atuais
$data_hora_atual = date('Y-m-d H:i:s');

// Query SQL para buscar agendamentos
$sql = "
    SELECT *, 
           TIMESTAMP(data, horario) AS agendamento_completo, 
           TIMESTAMP(data, horario) - INTERVAL $tempo_verificar MINUTE AS lembrete_ajustado 
    FROM agendamento 
    WHERE usuario_api = '$usuario_api'
      AND TIMESTAMP(data, horario) - INTERVAL $tempo_verificar MINUTE <= '$data_hora_atual'
      AND lembrete = '0'
    ORDER BY agendamento_completo ASC
    LIMIT 1
";


$query = mysqli_query($conn, $sql);

// Verifica se há resultados
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
       $id = $row['id'];
       $telefone= $row['cliente_telefone'];
        $profissional_nome = $row['profissional_nome'];
        $data =  $row['data'] ;
         $horario = $row['horario'] ;
        
        /*echo "Usuário API: " . $row['usuario_api'] . "\n";
        echo "Login: " . $row['login'] . "\n";
        echo "Dia: " . $row['dia'] . "\n";
        echo "Horário: " . $row['horario'] . "\n";
       
        echo "Cargo: " . $row['profissional_cargo'] . "\n";
        
        echo "Nome Cliente: " . $row['cliente_nome'] . "\n";
        echo "Data: " . $row['data'] . "\n";
        echo "ID Profissional: " . $row['id_profissional'] . "\n";
        echo "Confirmação: " . $row['confirmacao'] . "\n";
        echo "Lembrete: " . $row['lembrete'] . "\n";*/
    }    
 
 $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
        $login = $row_usuario['login'];
       $agenda_verfica = $row_usuario['agenda_verfica'];
    }
                
     
     
     
         $sql_busca_clientes = "SELECT * FROM clientes WHERE telefone = '$telefone' AND usuario_api = '$usuario_api'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    if ($row = mysqli_fetch_array($query_busca_clientes)) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    }
    
    
    
    
    
    
       function novo_texto($string, $nome, $agendamento, $profissional) {
            $substituicoes = [
                '{nome}' => $nome,
                '{agendamento}' => $agendamento,
                '{profissional}' => $profissional
            ];
            return str_replace(array_keys($substituicoes), array_values($substituicoes), $string);
        }

        function formatar_data_brasileira($data) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                return date("d/m/Y", strtotime($data));
            } else {
                return $data;
            }
        }
        
            
             $data_formatada = formatar_data_brasileira($data);
        $agendamento = $horario . " " . $data_formatada;
        $profissional = $profissional_nome;
            
    $agenda_verfica =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
    
function formatarTexto($texto) {
    // Remove todas as quebras de linha e espaços extras
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    
    // Adiciona "Sim" e "Não" ao final
    $texto .= "\nSim\nNão";

    return $texto;
}


$agenda_verfica2 =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
$agenda_verfica3 = formatarTexto($agenda_verfica2);
#$agenda_verifica3  = "\nSim\nNão";
#$agenda_verfica3 = $agenda_verfica2 . $agenda_verifica3;
                         
$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('Enquete','$telefone','$agenda_verfica3','2','$usuario_api')";
$query = mysqli_query($conn,$sql);   
        $id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

 $reponse = EscreverEnquete($servidor,$porta , $usuario_api, $token, $telefone, $agenda_verfica3, $id_msg);


$sql = "UPDATE agendamento SET lembrete = '2' WHERE id='$id'";
$query = mysqli_query($conn, $sql);


    
} else {
   # echo "Nenhum agendamento encontrado para as condições fornecidas.";
}

 
 
 
 














####################################
    ########################
    ###############
    #######





?>

   
   
