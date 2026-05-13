<?php
session_start();
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];
#echo "Login $login";


$sql = "UPDATE login SET situacao = 'aguarde' WHERE login='$login'";
$query = mysqli_query($conn,$sql);
if($query){
$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
$servidor = barra($servidor_recebido);
$usuario = $_POST['usuario_api']; 
#echo $usuario . $servidor . $token;
$url = $servidor;
$sql = "INSERT INTO gerenciador (usuario_api,comando) VALUES ('$usuario','reset_conta')";
$query = mysqli_query($conn,$sql);


VaiPara('qrcode.php?pagina_nome=3&aguarde=qrcode.php&tempo=45');


}