<?php
session_start();
include '../conn.php';
include '../funcoes.php';
include '../api/api_funcao.php';
include '../config_dados.php';
// Inclui o arquivo de conexão com o banco de dados


// Verifica se o formulário foi enviado com a opção 'criar_usuario_adm'
if ($_POST['opcao'] == 'criar_usuario_adm') {
    
    // Obtém os valores enviados pelo formulário
    $usuario = $_POST['usuario'];
    $usuario_api = $_POST['usuario_api'];
    


$sql = "UPDATE login SET usuario_api = '$usuario_api' WHERE login='$usuario'";

$query = mysqli_query($conn,$sql);
if($query){
    
$url = $ip_vps . ':' . $nova_porta . '/webhook';
$url= barra($url);

$resposta = criarUsuarioViaWebhook($usuario_api, $token, $url);

if (strpos($resposta, 'Erro:') !== false) {
    // Exibe a mensagem de erro
    echo "Ocorreu um erro: " . $resposta;
} else {
    // Sucesso, exibe a resposta da API
    #echo $token;
    #echo '<br>';
    #echo $usuario_api;
    #echo '<br>';
    VaiPara('../qrcode.php?pagina_nome=3&aguarde=qrcode.php&tempo=3');
    #echo $resposta;
}


    
}

}
?>
