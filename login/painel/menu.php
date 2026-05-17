<?php
#session_start();
#$tipo_usuario = $_SESSION['tipo_menu'];

$stmt_menu_login = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_menu_login->bind_param("s", $login);
$stmt_menu_login->execute();
$query = $stmt_menu_login->get_result();
$total = $query->num_rows;


if($total){
while($rows_usuarios = $query->fetch_array()) {
    $funcao = $rows_usuarios['modo_atuante'];
}

$query_menu = null;
$total_menu  = 0;

if (!empty($funcao)) {
    // Usuário tem função definida: filtra menu pela coluna funcao
    $stmt_menu = $conn->prepare("SELECT * FROM menu WHERE funcao IS NOT NULL AND funcao != '' AND FIND_IN_SET(?, funcao) > 0 ORDER BY ordem ASC");
    $stmt_menu->bind_param("s", $funcao);
    $stmt_menu->execute();
    $query_menu = $stmt_menu->get_result();
    $total_menu = $query_menu->num_rows;
    $stmt_menu->close();
}

// Se funcao está vazio OU não bateu com nenhum item de menu,
// cai para menu por tipo de usuário (evita sidebar vazia por dado incorreto no campo modo_atuante)
if ($total_menu == 0) {
    $stmt_menu2 = $conn->prepare("SELECT * FROM menu WHERE tipo = ? ORDER BY ordem ASC");
    $stmt_menu2->bind_param("s", $tipo);
    $stmt_menu2->execute();
    $query_menu = $stmt_menu2->get_result();
    $total_menu = $query_menu->num_rows;
    $stmt_menu2->close();
}
}else{

$stmt_menu2 = $conn->prepare("SELECT * FROM menu WHERE tipo = ? ORDER BY ordem ASC");
$stmt_menu2->bind_param("s", $tipo);
$stmt_menu2->execute();
$query_menu = $stmt_menu2->get_result();
$total_menu = $query_menu->num_rows;
$stmt_menu2->close();

}
$stmt_menu_login->close();
$pagina_atual = basename($_SERVER['PHP_SELF']); // Ex: "agenda.php"

$stmt_pag = $conn->prepare("SELECT * FROM menu WHERE menu_pagina = ? ORDER BY ordem ASC");
$stmt_pag->bind_param("s", $pagina_atual);
$stmt_pag->execute();
$query_pagina = $stmt_pag->get_result();
$total_pagina = $query_pagina->num_rows;
$stmt_pag->close();

if($total_pagina > 0){

$stmt_menu_pagina = $conn->prepare("SELECT * FROM menu WHERE funcao IS NOT NULL AND funcao != '' AND FIND_IN_SET(?, funcao) > 0 AND menu_pagina = ? ORDER BY ordem ASC");
$stmt_menu_pagina->bind_param("ss", $funcao, $pagina_atual);
$stmt_menu_pagina->execute();
$query_pagina = $stmt_menu_pagina->get_result();
$total_pagina = $query_pagina->num_rows;
$stmt_menu_pagina->close();

if($total_pagina == 0){
    
# VaiPara('index.php');    
}
}
if($total_pagina == 0){
 if($tipo != 2){
     
 # VaiPara('index.php');    
   
 }   
    
    
}



?>
