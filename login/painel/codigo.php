<?php
session_start();
include 'conn.php';
include 'funcoes.php';


$login = $_SESSION['login'];

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
while ($rows_config = mysqli_fetch_array($query_config)) {
    $servidor = $rows_config['ip_vps'];
    $porta = $rows_config['porta'];
    $token = $rows_config['chave'];
}

$sql_busca_usuario1 = "SELECT * FROM login WHERE tipo = '1' ";
$query_busca_usuario1 = mysqli_query($conn, $sql_busca_usuario1);
while ($rows_usuarios1 = mysqli_fetch_array($query_busca_usuario1)) {
   # $code_autorizado = $rows_usuarios['code_autorizado'];
    $usuario_api = $rows_usuarios1['usuario_api'];
}

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $code_autorizado = $rows_usuarios['code_autorizado'];
   # $usuario_api = $rows_usuarios['usuario_api'];
}




$GeraNumero = $code_autorizado;
$telefone = $login;
$msg = 'Seu Codigo ' . $GeraNumero;
#$msg =$code_autorizado;
$id_msg = '1';
include 'api/editacodigo.php';


// Imprimindo as variáveis para verificação
echo "Servidor: " . $servidor . "<br>";
echo "Porta: " . $porta . "<br>";
echo "Usuário API: " . $usuario_api . "<br>";
echo "Token: " . $token . "<br>";
echo "Telefone: " . $telefone . "<br>";
echo "Mensagem: " . $msg . "<br>";
echo "ID da Mensagem: " . $id_msg . "<br>";
echo "---<br>"; // Para separar a impressão das variáveis da resposta da função
// CORREÇÃO: Adicionando o parâmetro de ação ('enviarMensagem')
// Verifique se a sua função na API tem essa sintaxe.
$response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $msg, $id_msg);


// A API vai retornar um JSON. Você pode tratá-lo aqui.
// $response_data = json_decode($response, true);
// if (isset($response_data['status']) && $response_data['status'] === '✅ Mensagem enviada') {
//     VaiPara('desbloquear.php?confirmacao=cadastro_sucesso');
// } else {
//     // Lida com o erro
//     VaiPara('desbloquear.php?erro=api');
// }
#echo $response;
#exit();
// Se a sua API retorna apenas uma string, você pode ir direto para a próxima página.
VaiPara('desbloquear.php?confirmacao=cadastro_sucesso');