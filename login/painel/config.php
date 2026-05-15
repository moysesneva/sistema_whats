<?php
session_start();
include 'conn.php';
$login = $_SESSION['login'];
include 'funcoes.php';
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
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