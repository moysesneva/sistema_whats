<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
include 'api/editacodigo.php';


if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 

$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;    
}



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

if($situacao == 'bloqueado'||$situacao == 'desativado' ){
    VaiPara('perfil.php');
}
if($tipo ==1){
    include 'qr_admin.php';
}

if($tipo == 2){
    include 'qr_usuario.php';
}