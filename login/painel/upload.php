<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';
$pagina = 'estilo_pagina.php?pagina_nome=8&confirmacao=atualizado';

if (isset($_POST['titulo_site'])) {
    $titulo_site = trim($_POST['titulo_site']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET titulo = ?");
    mysqli_stmt_bind_param($stmt, "s", $titulo_site);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['barra_icon'])) {
    $barra_icon = trim($_POST['barra_icon']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET barra_logo = ?");
    mysqli_stmt_bind_param($stmt, "s", $barra_icon);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['barra_cima'])) {
    $barra_cima = trim($_POST['barra_cima']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET barra_principal = ?");
    mysqli_stmt_bind_param($stmt, "s", $barra_cima);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['menu_cor'])) {
    $menu_cor = trim($_POST['menu_cor']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET menu_trasnparencia = ?");
    mysqli_stmt_bind_param($stmt, "s", $menu_cor);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['selecao_menu'])) {
    $selecao_menu = trim($_POST['selecao_menu']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET cor_selecao_menu = ?");
    mysqli_stmt_bind_param($stmt, "s", $selecao_menu);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['selecao_menu_tema'])) {
    $selecao_menu = trim($_POST['selecao_menu_tema']);
    $stmt = mysqli_prepare($conn, "UPDATE config SET tema = ?");
    mysqli_stmt_bind_param($stmt, "s", $selecao_menu);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_POST['tema'])) {
    $tema = trim($_POST['tema']);
    $stmt = mysqli_prepare($conn, "UPDATE estilo SET tema_menu = ?");
    mysqli_stmt_bind_param($stmt, "s", $tema);
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($pagina);
    }
}

if (isset($_FILES['logo_site'])) {
    $logo = $_FILES['logo_site']['name'];
    $logo_tmp = $_FILES['logo_site']['tmp_name'];
    $logo_destino = "img/" . $logo;

    if (move_uploaded_file($logo_tmp, $logo_destino)) {
        $stmt = mysqli_prepare($conn, "UPDATE estilo SET logo_site = ?");
        mysqli_stmt_bind_param($stmt, "s", $logo_destino);
        if (mysqli_stmt_execute($stmt)) {
            VaiPara($pagina);
        }
    }
}

if (isset($_FILES['emblema_site'])) {
    $emblema = $_FILES['emblema_site']['name'];
    $emblema_tmp = $_FILES['emblema_site']['tmp_name'];
    $emblema_destino = "img/" . $emblema;

    if (move_uploaded_file($emblema_tmp, $emblema_destino)) {
        $stmt = mysqli_prepare($conn, "UPDATE estilo SET emblema_site = ?");
        mysqli_stmt_bind_param($stmt, "s", $emblema_destino);
        if (mysqli_stmt_execute($stmt)) {
            VaiPara($pagina);
        }
    }
}

if (isset($_FILES['fundo_login'])) {
    $fundo = $_FILES['fundo_login']['name'];
    $fundo_tmp = $_FILES['fundo_login']['tmp_name'];
    $fundo_destino = "img/" . $fundo;

    if (move_uploaded_file($fundo_tmp, $fundo_destino)) {
        $stmt = mysqli_prepare($conn, "UPDATE estilo SET fundo_login = ?");
        mysqli_stmt_bind_param($stmt, "s", $fundo_destino);
        if (mysqli_stmt_execute($stmt)) {
            VaiPara($pagina);
        }
    }
}

if (isset($_FILES['icon_site'])) {
    $icon = $_FILES['icon_site']['name'];
    $icon_tmp = $_FILES['icon_site']['tmp_name'];
    $icon_destino = "img/" . $icon;

    if (move_uploaded_file($icon_tmp, $icon_destino)) {
        $stmt = mysqli_prepare($conn, "UPDATE estilo SET icon_site = ?");
        mysqli_stmt_bind_param($stmt, "s", $icon_destino);
        if (mysqli_stmt_execute($stmt)) {
            VaiPara($pagina);
        }
    }
}
