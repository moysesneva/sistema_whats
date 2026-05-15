<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'conn.php';
$login = $_SESSION['login'];
include 'funcoes.php';
$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    #$nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $login  = $rows_usuarios['login'];

}

if($tipo == '2'){
    
VaiPara('msg_config.php?pagina_nome=22');
    
}
#VaiPara('config_adm.php?pagina_nome=1');
if($tipo == '1'){
    
VaiPara('config_adm.php?pagina_nome=1');
    
}


if($tipo == '3'){
    
VaiPara('perfil.php?pagina_nome=24');
    
}
if($tipo == '5'){
    
VaiPara('senha.php');
    
}
exit();
?>