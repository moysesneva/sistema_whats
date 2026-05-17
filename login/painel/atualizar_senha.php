<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];
$senha = $_POST['confirmar_senha'];
$stmt = $conn->prepare("UPDATE login SET senha = ? WHERE login = ?");
$stmt->bind_param("ss", $senha, $login);
$query = $stmt->execute();
$stmt->close();
if($query){
    
VaiPara('senha.php?pagina_nome=6&confirmacao=atualizado')   ; 
}

?>

