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

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {

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