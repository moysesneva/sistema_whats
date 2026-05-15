<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';
include 'conn.php';
include 'api/editacodigo.php';

// Verifica se o usuário está logado
if(!isset($_SESSION['login'])){
    echo json_encode(['success' => false]);
    exit;
}
$login = $_SESSION['login'];








$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome          = Priletra($rows_usuarios['nome']);
    $img_perfil    = $rows_usuarios['perfil_img'];
    $autorizado    = $rows_usuarios['autorizado'];
    $tipo          = $rows_usuarios['tipo'];
    $usuario_api   = $rows_usuarios['usuario_api'];
    $situacao      = $rows_usuarios['situacao'];
    $email         = $rows_usuarios['email'];
    $qrcode        = isset($rows_usuarios['qrcode']) ? $rows_usuarios['qrcode'] : '';
    $tempo_code    = isset($rows_usuarios['tempo_code']) ? $rows_usuarios['tempo_code'] : '';
    $qr_data       = isset($rows_usuarios['qr_data']) ? $rows_usuarios['qr_data'] : '';
    $qr_quantidade = isset($rows_usuarios['qr_quantidade']) ? $rows_usuarios['qr_quantidade'] : 0;
}






$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}



$user_id = isset($_GET['usuario']) ? $_GET['usuario'] : null;













if(!$user_id){
   $user_id = $usuario_api;
}
// Chama a função que puxa a imagem (a imagem vem em base64)
$response = puxar_img($user_id, $servidor, $pagina_recebe, $porta, $token);

if($response){
    // Decodifica a imagem base64
    $imgData = base64_decode($response);
    if($imgData === false){
        echo json_encode(['success' => false, 'message' => 'Erro ao decodificar a imagem.']);
        exit;
    }
    // Define um nome único para a imagem
    $img_name = "print_instancia_" . time() . ".jpg";
    $img_path = "uploads/" . $img_name;
    // Salva a imagem decodificada na pasta "uploads"
    if(file_put_contents($img_path, $imgData)){
        echo json_encode(['success' => true, 'img_path' => $img_path]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar a imagem.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nenhuma imagem retornada.']);
}
?>
