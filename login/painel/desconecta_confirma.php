<?php
session_start();
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];
#echo "Login $login";


$sql = "UPDATE login SET situacao = 'desativado',qr_quantidade ='1' WHERE login='$login'";
$query = mysqli_query($conn,$sql);
if($query){
$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
$servidor = barra($servidor_recebido);
$usuario = $_POST['usuario_api']; 
$usuario_api = $_POST['usuario_api']; 
#echo $usuario . $servidor . $token;

     
 $sql = "INSERT INTO gerenciador (usuario_api,comando) VALUES ('$usuario_api','stop_conta')";
$query = mysqli_query($conn,$sql);    

   VaiPara('desconecta.php?pagina_nome=7&aguarde=qrcode.php&tempo=45');
}