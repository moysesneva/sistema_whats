<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu = $_POST['menu'] ?? '';
    $menu_pagina = $_POST['menu_pagina'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $ordem = $_POST['ordem'] ?? '';
    $icone_menu = $_POST['icone_menu'] ?? '';

    $stmt = $conn->prepare("INSERT INTO menu (menu, menu_pagina, tipo, ordem, icone_menu) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $menu, $menu_pagina, $tipo, $ordem, $icone_menu);
    $query = $stmt->execute();
    $stmt->close();

    if($query){
        VaiPara('criar_menus.php?pagina_nome=9');
    }
}

?>
