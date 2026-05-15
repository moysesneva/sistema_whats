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
$senha =        $_POST['password'];
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

$stmt = $conn->prepare("SELECT * FROM login WHERE login = ? AND senha = ?");
$stmt->bind_param("ss", $login, $senha);
$stmt->execute();
$query_busca_usuario = $stmt->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $autorizado = $rows_usuarios['autorizado'];

}
$stmt->close();
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

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
$stmt_busca_usuario->close();

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
$stmt_insert = $conn->prepare("INSERT INTO login (login,senha,tipo,usuario_api,nome,autorizado,code_autorizado,perfil_img,funcao,modo_atuante) VALUES (?,?,'2',?,?,'1',?,'IA','Agendamento',?)");
$stmt_insert->bind_param("ssssss", $login, $senha, $usuario_api, $nome, $GeraNumero, $perfil_img);
$query = $stmt_insert->execute();
$stmt_insert->close();

if($query){
    

$sql_busca_usuario = "SELECT * FROM login WHERE tipo = '1'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
while($rows_usuarios = $query_busca_usuario->fetch_array()) {


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
$stmt = $conn->prepare("SELECT * FROM login WHERE login = ? AND code_autorizado = ?");
$stmt->bind_param("ss", $login, $codigo);
$stmt->execute();
$query_busca_usuario = $stmt->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
$stmt->close();

if($total_busca_usuario == 1){
$stmt_update = $conn->prepare("UPDATE login SET autorizado = '2' WHERE login = ?");
$stmt_update->bind_param("s", $login);
$query = $stmt_update->execute();
$stmt_update->close();
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
while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    

    $usuario_api = $rows_usuarios['usuario_api'];

}
$telefone = $_POST['telefone'];
$stmt = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt->bind_param("s", $telefone);
$stmt->execute();
$query_busca_usuario = $stmt->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    

    $senha = $rows_usuarios['senha'];

}
$stmt->close();

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
