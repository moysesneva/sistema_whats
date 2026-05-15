<?php
session_start();
include 'conn.php';
include 'funcoes.php';
include 'api/editacodigo.php';

$pagina = 'index.php';
if (isset($_POST['titulo'])) {
$titulo = $_POST['titulo'];

if(isset($_POST['telefone'])){
$login =$_POST['telefone'];
}


if(isset($_POST['password'])){
$senha =	$_POST['password'];
}


$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}







if($titulo == 'LOGIN'){




$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login' AND senha = '$senha'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);
while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $autorizado = $rows_usuarios['autorizado'];

}
if(isset($autorizado)){
if($autorizado == 1){	
$_SESSION['login'] = $login;
Vaipara('desbloquear.php');
}}

if($total_busca_usuario  == 1){

$_SESSION['login'] = $login;
VaiPara($pagina);

}#if($total_busca_usuario  == 1){
if($total_busca_usuario  == 0){

VaiPara('login.php?erro=login');

}#if($total_busca_usuario  == 1){



}#if($titulo == 'LOGIN'){

if($titulo == 'CRIAR_CONTA'){



$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if($total_busca_usuario == 1){

VaiPara('cadastro_conta.php?erro=login_duplicado');


}

if($total_busca_usuario == 0){
    
    
$termo = 'agenda_';    
$nome = $_POST['user-name'];
$GeraNumero = GeraNumero();
$perfil_img = 'img/perfil.png';
$login2 = So_numeros($login);
$usuario_api = $termo . $login2;
$sql = "INSERT INTO login (login,senha,tipo,usuario_api,nome,autorizado,code_autorizado,perfil_img,funcao,modo_atuante) VALUES ('$login','$senha','2','$usuario_api','$nome','1','$GeraNumero','$perfil_img','IA','Agendamento')";

$query = mysqli_query($conn,$sql);

if($query){
    

$sql_busca_usuario = "SELECT * FROM login WHERE tipo = '1'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {


$usuario_api = $rows_usuarios['usuario_api'];

}    
    
    
    
    
$telefone = So_numeros($login); 
#$telefone = '55'.$telefone;
$msg = 'Seu Código é '.$GeraNumero;

$resultado = MsgTexto($conn, $msg, $telefone, $usuario_api);
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção
$response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $msg, $id_msg);

VaiPara('login.php?confirmacao=cadastro_sucesso');


}
}
}
###############################################################




#######################################################################
#####################################################################
}else{

#VaiPara('login.php?invasao');
}#if (isset($array['titulo'])) {
if($titulo == 'DESBLOQUEAR'){
$login = $_SESSION['login'];
$codigo = $_POST['codigo'];
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'AND code_autorizado = '$codigo'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if($total_busca_usuario == 1){
$sql = "UPDATE login SET autorizado = '2' WHERE login='$login'";

$query = mysqli_query($conn,$sql);
if($query){
VaiPara($pagina);	
}


}else{
	VaiPara('desbloquear.php?erro=code');
}



}#if($titulo == 'DESBLOQUEAR '){



if($titulo == 'RECUPRAR_SENHA'){


$sql_busca_usuario = "SELECT * FROM login WHERE tipo = '1'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);
while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    

    $usuario_api = $rows_usuarios['usuario_api'];

}
$telefone = $_POST['telefone'];
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$telefone'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);
while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    

    $senha = $rows_usuarios['senha'];

}

if($total_busca_usuario == 1){
$msg = 'Sua senha é *' . $senha .'*';

$telefone = So_numeros($telefone); 
#$telefone = '55'.$telefone;


$resultado = MsgTexto($conn, $msg, $telefone, $usuario_api);
$id_msg = '1';
$response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $msg, $id_msg);

}
VaiPara('login.php');


}







#print_r($_REQUEST);

?>