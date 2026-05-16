<?php
#include 'conn.php';


// Consulta SQL
$sql = "SELECT * FROM estilo";
$query = mysqli_query($conn, $sql);

// Verifica o número de registros retornados
$total = mysqli_num_rows($query);

// Itera sobre os resultados
while ($rows_usuarios = mysqli_fetch_array($query)) {
    $logo = $rows_usuarios['logo_site'];
    $small_logo  = $rows_usuarios['emblema_site'];
    $background_image = $rows_usuarios['fundo_login'];
    $icon = $rows_usuarios['icon_site'];
    $titulo = $rows_usuarios['titulo'];
    $favicon = $rows_usuarios['icon_site'];
}


$sql = "SELECT * FROM config";
$query = mysqli_query($conn, $sql);

$total = mysqli_num_rows($query);

while ($rows_usuarios = mysqli_fetch_array($query)) {
    $background_image = $rows_usuarios['caminho_modelo'];
}

?>
<!-- Tema Enam Dark Navy + Orange — MoysesNet -->
<link href="../files/assets/vendor/fonts/montserrat/montserrat.css" rel="stylesheet">
<link rel="stylesheet" href="/login/painel/enam-panel.css">
<style>
    body { background: #000d1a !important; }
</style>
