<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/../auth_guard.php';
session_start();
include '../conn.php';
include '../funcoes.php';
include '../api/api_funcao.php';
include '../config_dados.php';

if ($_POST['opcao'] == 'criar_usuario_adm') {
    
    $usuario = $_POST['usuario'] ?? '';
    $usuario_api = $_POST['usuario_api'] ?? '';
    
$stmt = $conn->prepare("UPDATE login SET usuario_api = ? WHERE login = ?");
$stmt->bind_param("ss", $usuario_api, $usuario);
$query = $stmt->execute();
$stmt->close();

if($query){
    
$url = $ip_vps . ':' . $nova_porta . '/webhook';
$url= barra($url);

$resposta = criarUsuarioViaWebhook($usuario_api, $token, $url);

if (strpos($resposta, 'Erro:') !== false) {
    echo "Ocorreu um erro: " . $resposta;
} else {
    VaiPara('../qrcode.php?pagina_nome=3&aguarde=qrcode.php&tempo=3');
}


    
}

}
?>
