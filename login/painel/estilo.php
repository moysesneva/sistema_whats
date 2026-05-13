<?php
#include 'conn.php';


// Consulta SQL
$sql = "SELECT * FROM estilo";
$query = mysqli_query($conn, $sql);

// Verifica o número de registros retornados
$total = mysqli_num_rows($query);

// Itera sobre os resultados
while ($rows_usuarios = mysqli_fetch_array($query)) {
    // Atribui os valores dos campos a variáveis
    $logo = $rows_usuarios['logo_site'];
    $small_logo  = $rows_usuarios['emblema_site'];
    $background_image = $rows_usuarios['fundo_login'];
    $icon = $rows_usuarios['icon_site'];
    $titulo = $rows_usuarios['titulo'];
    $favicon = $rows_usuarios['icon_site'];



}


$sql = "SELECT * FROM config";
$query = mysqli_query($conn, $sql);

// Verifica o número de registros retornados
$total = mysqli_num_rows($query);

// Itera sobre os resultados
while ($rows_usuarios = mysqli_fetch_array($query)) {
    // Atribui os valores dos campos a variáveis

    $background_image = $rows_usuarios['caminho_modelo'];



}




// Variáveis PHP para personalizar a logo e a imagem de fundo
#$logo = '../files/assets/images/logo.png';  // Caminho para a logo
#$small_logo = '../files/assets/images/auth/Logo-small-bottom.png';  // Caminho para a logo pequena
#$favicon = '../files/assets/images/favicon.ico';  // Caminho para o favicon
#$background_image = "../files/assets/images/auth/bg.jpg"; // Caminho para a imagem de fundo


#$background_image = "https://img.freepik.com/vetores-gratis/fundo-de-gradiente-de-linhas-azuis-dinamicas_23-2148995756.jpg"; // Caminho para a imagem de fundo

#$titulo = 'Painel Administrativo - Edita Código';



?>

<style>
        /* Definir a imagem de fundo dinamicamente */
        body {
            background: url('<?php echo $background_image; ?>') no-repeat center center fixed;
            background-size: cover;
        }
    </style>