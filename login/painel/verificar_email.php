<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];

#print_r($_REQUEST);
include 'conn.php';
include 'config_dados.php';

$emailPerfil = $_POST['emailPerfil'];

include 'estilo.php';

include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $usuario_api  = $rows_usuarios['usuario_api'];
    $situacao  = $rows_usuarios['situacao'];
    $email  = $rows_usuarios['email'];

}

$stmt_ve = $conn->prepare("UPDATE login SET email = ? WHERE login = ?");
$stmt_ve->bind_param("ss", $emailPerfil, $login);
$query = $stmt_ve->execute();
$stmt_ve->close();

if($query){
    VaiPara('perfil.php?pagina_nome=24');
}

?>