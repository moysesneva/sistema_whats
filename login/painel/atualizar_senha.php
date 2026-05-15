<?php
session_start();
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];
$senha = $_POST['confirmar_senha'];
$sql = "UPDATE login SET senha = '$senha' WHERE login='$login'";
$query = mysqli_query($conn,$sql);
if($query){
    
VaiPara('senha.php?pagina_nome=6&confirmacao=atualizado')   ; 
}

?>

