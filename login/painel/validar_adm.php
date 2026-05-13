<?php
session_start();
include 'conn.php';
include 'funcoes.php';
$pagina = 'index.php';
if (isset($_POST['titulo'])) {
$titulo = $_POST['titulo'];

if(isset($_POST['telefone'])){
$login =$_POST['telefone'];
}


if(isset($_POST['password'])){
$senha =	$_POST['password'];
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
if($total_busca_usuario == 1){
VaiPara('login.php');	
}
if($total_busca_usuario == 0){
$nome = $_POST['user-name'];
$GeraNumero = GeraNumero();
$perfil_img = 'img/perfil.png';
$usuario_api = 'agenda_'.$login;
$sql = "INSERT INTO login (login,senha,tipo,nome,usuario_api,autorizado,code_autorizado,perfil_img,modo_atuante) VALUES ('$login','$senha','1','$nome','$usuario_api','2','$GeraNumero','$perfil_img','adm')";

$query = mysqli_query($conn,$sql);
$sql = "UPDATE config SET ip_vps='', porta='', chave=''";
$query = mysqli_query($conn,$sql);

if($query){

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



print_r($_REQUEST);

?>