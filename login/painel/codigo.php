<?php
require_once __DIR__ . '/auth_guard.php';
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

$stmt_bu = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_bu->bind_param("s", $login);
$stmt_bu->execute();
$query_busca_usuario = $stmt_bu->get_result();
$stmt_bu->close();
while ($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $code_autorizado = $rows_usuarios['code_autorizado'];
   # $usuario_api = $rows_usuarios['usuario_api'];
}




$GeraNumero = $code_autorizado;
$telefone = $login;
$msg = 'Seu Codigo ' . $GeraNumero;
#$msg =$code_autorizado;
$id_msg = '1';
include 'api/editacodigo.php';


// Imprimindo as vari��veis para verifica�0�4�0�0o
echo "Servidor: " . $servidor . "<br>";
echo "Porta: " . $porta . "<br>";
echo "Usu��rio API: " . $usuario_api . "<br>";
echo "Token: " . $token . "<br>";
echo "Telefone: " . $telefone . "<br>";
echo "Mensagem: " . $msg . "<br>";
echo "ID da Mensagem: " . $id_msg . "<br>";
echo "---<br>"; // Para separar a impress�0�0o das vari��veis da resposta da fun�0�4�0�0o
// CORRE�0�5�0�1O: Adicionando o par�0�9metro de a�0�4�0�0o ('enviarMensagem')
// Verifique se a sua fun�0�4�0�0o na API tem essa sintaxe.
$response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $msg, $id_msg);


// A API vai retornar um JSON. Voc�� pode trat��-lo aqui.
// $response_data = json_decode($response, true);
// if (isset($response_data['status']) && $response_data['status'] === '�7�3 Mensagem enviada') {
//     VaiPara('desbloquear.php?confirmacao=cadastro_sucesso');
// } else {
//     // Lida com o erro
//     VaiPara('desbloquear.php?erro=api');
// }
#echo $response;
#exit();
// Se a sua API retorna apenas uma string, voc�� pode ir direto para a pr��xima p��gina.
VaiPara('desbloquear.php?confirmacao=cadastro_sucesso');