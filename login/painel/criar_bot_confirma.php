<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];



if($_POST['nome_cliente']){
$login = $_POST['telefone_cliente'];
$email_cliente = $_POST['email_cliente'];

$stmt = $conn->prepare("SELECT * FROM login WHERE login = ? OR email = ?");
$stmt->bind_param("ss", $login, $email_cliente);
$stmt->execute();
$query_busca_usuario = $stmt->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;
$stmt->close();
   
  
if($total_busca_usuario > 0){ 
    
VaiPara('criar_bot.php?pagina_nome=4&erro=atualizado');  
}else{  
$nome = $_POST['nome_cliente'];
$login = $_POST['telefone_cliente'];
$email_cliente = $_POST['email_cliente'];
$creditos = $_POST['creditos'];
$senha = $_POST['senha_padrao']; 
$plano = $_POST['plano'];

$perfil_img ='img/perfil.png';   
$nome = Priletra($nome);

$login = So_numeros($login);
$usuario_api = $termo . $login;    
 $login = $_POST['telefone_cliente'];   

$stmt_insert = $conn->prepare("INSERT INTO login (login, senha, tipo, usuario_api, nome, autorizado, perfil_img, situacao, email, funcao, creditos, plano, modo_atuante) VALUES (?, ?, '2', ?, ?, '2', ?, 'ativado', ?, 'IA', ?, ?, 'Agendamento')");
$stmt_insert->bind_param("ssssssss", $login, $senha, $usuario_api, $nome, $perfil_img, $email_cliente, $creditos, $plano);
$query = $stmt_insert->execute();
$stmt_insert->close();

if ($query) {
    
$url = $ip_vps . ':' . $nova_porta . '/webhook';
$url= barra($url);

$stmt_ger = $conn->prepare("INSERT INTO gerenciador (celular, usuario_api, comando) VALUES (?, ?, 'criar_conta')");
$stmt_ger->bind_param("ss", $login, $usuario_api);
$stmt_ger->execute();
$stmt_ger->close();



VaiPara('listar_bot.php?pagina_nome=4&confirmacao=cadastro_sucesso');  

    
    
    
    
    
    
    
    
    
 
 
}   
}
}
