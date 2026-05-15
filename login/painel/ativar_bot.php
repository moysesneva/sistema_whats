<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


include 'conn.php';
include 'config_dados.php';

include 'estilo.php';

include 'css_de_icones.php';

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while ($rows_usuarios = $query_busca_usuario->fetch_array()) {

    $login  = $rows_usuarios['login'];
    $email  = $rows_usuarios['email'];




}




$whatsapp_phone = $login;



$sql_busca_config = "SELECT * FROM config ";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {

    $link_pagamento  = $rows_config['link_pagamento'];
}






$site = $link_pagamento . "?phone=" . $whatsapp_phone . "&email=" . $email;
VaiPara($site);