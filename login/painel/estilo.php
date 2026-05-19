<?php
require_once __DIR__ . '/conn.php';

$sql = "SELECT * FROM estilo";
$query = mysqli_query($conn, $sql);
while ($rows_usuarios = mysqli_fetch_array($query)) {
    $logo             = $rows_usuarios['logo_site'];
    $small_logo       = $rows_usuarios['emblema_site'];
    $background_image = $rows_usuarios['fundo_login'];
    $icon             = $rows_usuarios['icon_site'];
    $titulo           = $rows_usuarios['titulo'];
    $favicon          = $rows_usuarios['icon_site'];
}

$sql = "SELECT * FROM config";
$query = mysqli_query($conn, $sql);
while ($rows_usuarios = mysqli_fetch_array($query)) {
    $background_image = $rows_usuarios['caminho_modelo'];
}
