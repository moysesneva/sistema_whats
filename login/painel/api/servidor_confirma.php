<?php
require_once('../conn.php');
include('../funcao.php');



if($_GET['comando'] == 'criar_conta'){
$id =  $_GET['id'];
$usuario_api =  $_GET['cpf'];
$caminho =  $_GET['caminho'];

$sql = "UPDATE gerenciador SET comando = 'conta_criada' WHERE id = '$id'";
$query = mysqli_query($conn,$sql);

$sql = "UPDATE login SET caminho_vps = '$caminho' WHERE usuario_api = '$usuario_api'";
$query = mysqli_query($conn,$sql);

}#criar_conta





if($_GET['comando'] == 'delete'){
$id =  $_GET['id'];
$usuario_api =  $_GET['cpf'];
$caminho =  $_GET['caminho'];

$sql = "UPDATE gerenciador SET comando = 'conta_criada' WHERE id = '$id'";
$query = mysqli_query($conn,$sql);
$sql = "DELETE FROM login WHERE email = '$email'";
$query = mysqli_query($conn,$sql);


}#criar_conta











if($_GET['comando'] == 'restart'){
$id =  $_GET['id'];
$usuario_api =  $_GET['usuario'];




$sql = "DELETE FROM gerenciador WHERE id='$id'";
$query = mysqli_query($conn,$sql);

$sql = "UPDATE login SET situacao = 'ativado' WHERE usuario_api='$usuario_api'";

$query = mysqli_query($conn,$sql);

}#stop


if($_GET['comando'] == 'stop'){
$id =  $_GET['id'];
$usuario_api =  $_GET['usuario'];


#$sql = "UPDATE dados_compra SET comando = 'conta_criada',status= 'Parado' WHERE email = '$email'";
#$query = mysqli_query($conn,$sql);

#$sql = "UPDATE login SET status= 'Parado' WHERE email = '$email'";
#$query = mysqli_query($conn,$sql);

$sql = "DELETE FROM gerenciador WHERE id='$id'";
$query = mysqli_query($conn,$sql);

$sql = "UPDATE login SET situacao = 'desativado' WHERE usuario_api='$usuario_api'";

$query = mysqli_query($conn,$sql);

}#stop
$query = mysqli_query($conn,$sql);
if($_GET['comando'] == 'start'){
$id =  $_GET['id'];
$email =  $_GET['email'];
$cpf =  $_GET['cpf'];


$sql = "UPDATE dados_compra SET comando = 'conta_criada',status= 'Ativo' WHERE email = '$email'";
$query = mysqli_query($conn,$sql);

$sql = "UPDATE login SET status= 'Ativo' WHERE email = '$email'";
$query = mysqli_query($conn,$sql);


}#stop




?>