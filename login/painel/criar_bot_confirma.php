<?php
session_start();
include 'conn.php';
include 'funcoes.php';

include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];



if($_POST['nome_cliente']){
$login = $_POST['telefone_cliente'];
$email_cliente = $_POST['email_cliente'];

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login' OR email = '$email_cliente'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);
   
  
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
// Inserindo os dados na tabela login



$sql = "INSERT INTO login (login, senha, tipo, usuario_api, nome, autorizado,  perfil_img, situacao, email,funcao,creditos,plano,modo_atuante) 
        VALUES ('$login', '$senha','2', '$usuario_api', '$nome', '2',  '$perfil_img', 'ativado','$email_cliente','IA','$creditos','$plano','Agendamento')";

// Executando a consulta SQL
$query = mysqli_query($conn, $sql);

// Verificando se houve sucesso na inserção
if ($query) {
  #  echo "Registro inserido com sucesso!";
    
   
$url = $ip_vps . ':' . $nova_porta . '/webhook';
$url= barra($url);

$sql = "INSERT INTO gerenciador (celular,usuario_api,comando) VALUES ('$login','$usuario_api','criar_conta')";
$query = mysqli_query($conn,$sql);



VaiPara('listar_bot.php?pagina_nome=4&confirmacao=cadastro_sucesso');  

    
    
    
    
    
    
    
    
    
 
 
}   
}
}