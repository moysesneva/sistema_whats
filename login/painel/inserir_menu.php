<?php
session_start();
include 'conn.php';
include 'funcoes.php';
#print_r($_REQUEST);



// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber dados do formulário
    $menu = $_POST['menu'];
    $menu_pagina = $_POST['menu_pagina'];
    $tipo = $_POST['tipo'];
    $ordem = $_POST['ordem'];
    $icone_menu = $_POST['icone_menu'];

    // Preparar o comando SQL para inserir os dados
    $sql = "INSERT INTO menu (menu, menu_pagina, tipo, ordem, icone_menu) 
            VALUES ('$menu', '$menu_pagina', '$tipo', '$ordem', '$icone_menu')";
 $query = mysqli_query($conn, $sql);
  
   if($query){
       
  VaiPara('criar_menus.php?pagina_nome=9');     
       
   }         
            
            
            }
            
            
            ?>