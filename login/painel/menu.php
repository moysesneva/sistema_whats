<?php
#session_start();
#$tipo_usuario = $_SESSION['tipo_menu'];

$sql_busca_modulos = "SELECT * FROM login WHERE login = '$login'";
$query = mysqli_query($conn, $sql_busca_modulos);
$total = mysqli_num_rows($query);


if($total){
while($rows_usuarios = mysqli_fetch_array($query)) {
    $funcao = $rows_usuarios['modo_atuante'];
}   
     


$sql_menu = "SELECT * FROM menu 
             WHERE funcao IS NOT NULL 
             AND funcao != '' 
             AND FIND_IN_SET('$funcao', funcao) > 0 
             ORDER BY ordem ASC";

$query_menu = mysqli_query($conn, $sql_menu);

// Verifica o número de registros retornados
$total_menu = mysqli_num_rows($query_menu);
}else{
    
  // Consulta SQL para selecionar todos os registros da tabela
$sql_menu = "SELECT * FROM menu WHERE tipo ='$tipo'  ORDER BY ordem ASC";
$query_menu = mysqli_query($conn, $sql_menu);

// Verifica o número de registros retornados
$total_menu = mysqli_num_rows($query_menu);  
    
    
}
$pagina_atual = basename($_SERVER['PHP_SELF']); // Ex: "agenda.php"


$sql_pagina = "SELECT * FROM menu WHERE menu_pagina ='$pagina_atual'  ORDER BY ordem ASC";
$query_pagina = mysqli_query($conn, $sql_pagina);
$total_pagina = mysqli_num_rows($query_pagina);  


// Verifica o número de registros retornados



if($total_pagina > 0){
    

$sql_menu_pagina = "SELECT * FROM menu 
             WHERE funcao IS NOT NULL 
             AND funcao != '' 
             AND FIND_IN_SET('$funcao', funcao) > 0 AND menu_pagina = '$pagina_atual'
             ORDER BY ordem ASC";

$query_pagina = mysqli_query($conn, $sql_menu_pagina);

// Verifica o número de registros retornados
$total_pagina = mysqli_num_rows($query_pagina);




if($total_pagina == 0){
    
VaiPara('index.php');    
}
}
if($total_pagina == 0){
 if($tipo != 2){
     
 # VaiPara('index.php');    
   
 }   
    
    
}



?>