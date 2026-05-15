<?php
include 'conn.php';
include 'api/editacodigo.php';
header("Content-Type: application/json");



$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = $rows_config['ip_vps'];
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}

// Simula o total de atualizações necessárias
 $sql_busca_usuario = "SELECT * FROM login WHERE tipo = '2' OR tipo = '1'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome = $rows_usuarios['nome'];
    $usuario_api = $rows_usuarios['usuario_api'];



atualizarClassesGlobais($servidor,$porta, $usuario_api,$token);

usleep(3000); // 3000 microsegundos = 3 milissegundos

}

// Retorna o total de atualizações para o progresso
echo json_encode(["total" => $total_busca_usuario]);
?>


