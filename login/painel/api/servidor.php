<?php
error_reporting(0);
ini_set("display_errors", 0 );
require_once('../conn.php');
include('../funcao.php');
#$data_hora_atual = getCurrentDatetimeInMySQLFormat();


#$usuario = 'bot_01';
#################################################################


$sql = "SELECT * FROM config";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){

$envia = $lista_login['servidor_envia'];
$recebe = $lista_login['servidor_recebe'];
$confirma = $lista_login['servidor_confirma'];
$chave = $lista_login['chave'];
$dominio = $lista_login['dominio'];
$api = $lista_login['api'];
}

############# BUSCA DE LOGIN
$sql = "SELECT * FROM  gerenciador WHERE comando <> 'conta_criada' ";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){

$cpf = $lista_login['usuario_api'];
$id = $lista_login['id'];
$email = $lista_login['email'];
$comando = $lista_login['comando'];

}
if($total > 0){

$dados = array(

"usuario_api" => $cpf ,
"comando" => $comando ,
"email" => $email ,
"id" => $id,
"envia" => $envia,
"recebe" => $recebe,
"confirma" => $confirma,
"chave" => $chave,
"dominio" => $dominio,
"api" => $api,
);

}

if($total == 0){

$dados = array(

"usuario_api" => $cpf ,
"comando" => $comando ,
"email" => $email ,
"id" => $id,
"envia" => $envia,
"recebe" => $recebe,
"confirma" => $confirma,
"chave" => $dominio,
"dominio" => $dominio,
"api" => $api,
);
}
$variavel_json  = json_encode($dados);

echo $variavel_json;
