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
$senha =        $_POST['password'];
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
$_SESSION['last_activity'] = time();
Vaipara('desbloquear.php');
}}

if($total_busca_usuario  == 1){

$_SESSION['login'] = $login;
$_SESSION['last_activity'] = time();
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
if($total_busca_usuario == 1){
VaiPara('login.php');   
}
if($total_busca_usuario == 0){
$nome = $_POST['user-name'];
$GeraNumero = GeraNumero();
$perfil_img = 'img/perfil.png';
$usuario_api = 'agenda_'.$login;
$stmt_insert = $conn->prepare("INSERT INTO login (login,senha,tipo,nome,usuario_api,autorizado,code_autorizado,perfil_img,modo_atuante) VALUES (?,?,'1',?,?,'2',?,?,'adm')");
$stmt_insert->bind_param("ssssss", $login, $senha, $nome, $usuario_api, $GeraNumero, $perfil_img);
$query = $stmt_insert->execute();
$stmt_insert->close();
$sql = "UPDATE config SET ip_vps='', porta='', chave=''";
$query2 = mysqli_query($conn,$sql);

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

?>
