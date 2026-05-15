<?php
include 'conn.php';
include 'funcoes.php';
$pagina = 'estilo_pagina.php?pagina_nome=8&confirmacao=atualizado';
#####################
#1. Atualizar o Título da Página:

if (isset($_POST['titulo_site'])) {
    $titulo_site = mysqli_real_escape_string($conn, $_POST['titulo_site']);

    $sql = "UPDATE estilo SET titulo = '$titulo_site'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}
###################################
#2. Atualizar a Barra de Ícones:


if (isset($_POST['barra_icon'])) {
    $barra_icon = mysqli_real_escape_string($conn, $_POST['barra_icon']);

    $sql = "UPDATE estilo SET barra_logo = '$barra_icon'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}

##############################
#3. Atualizar a Barra de Cima:


if (isset($_POST['barra_cima'])) {
    $barra_cima = mysqli_real_escape_string($conn, $_POST['barra_cima']);

    $sql = "UPDATE estilo SET barra_principal = '$barra_cima'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}

#####################################
#4. Atualizar a Cor do Menu:

if (isset($_POST['menu_cor'])) {
    $menu_cor = mysqli_real_escape_string($conn, $_POST['menu_cor']);

    $sql = "UPDATE estilo SET menu_trasnparencia = '$menu_cor'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}


###################################
#5. Atualizar a Seleção do Menu:


if (isset($_POST['selecao_menu'])) {
    $selecao_menu = mysqli_real_escape_string($conn, $_POST['selecao_menu']);

    $sql = "UPDATE estilo SET cor_selecao_menu = '$selecao_menu'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}



if (isset($_POST['selecao_menu_tema'])) {
    $selecao_menu = mysqli_real_escape_string($conn, $_POST['selecao_menu_tema']);

    $sql = "UPDATE config SET tema = '$selecao_menu'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}


#6. Atualizar o Tema:

###################


if (isset($_POST['tema'])) {
    $tema = mysqli_real_escape_string($conn, $_POST['tema']);

    $sql = "UPDATE estilo SET tema_menu = '$tema'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        VaiPara($pagina);
    }
}

#####################################
#7. Atualizar a Logo do Site (Imagem):

if (isset($_FILES['logo_site'])) {
    $logo = $_FILES['logo_site']['name'];
    $logo_tmp = $_FILES['logo_site']['tmp_name'];
    $logo_destino = "img/" . $logo;

    if (move_uploaded_file($logo_tmp, $logo_destino)) {
        $sql = "UPDATE estilo SET logo_site = '$logo_destino'";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            VaiPara($pagina);
        }
    }
}


##8. Atualizar o Emblema do Site (Imagem):
#

if (isset($_FILES['emblema_site'])) {
    $emblema = $_FILES['emblema_site']['name'];
    $emblema_tmp = $_FILES['emblema_site']['tmp_name'];
    $emblema_destino = "img/" . $emblema;

    if (move_uploaded_file($emblema_tmp, $emblema_destino)) {
        $sql = "UPDATE estilo SET emblema_site = '$emblema_destino'";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            VaiPara($pagina);
        }
    }
}


########################
#9. Atualizar o Fundo do Login (Imagem):



if (isset($_FILES['fundo_login'])) {
    $fundo = $_FILES['fundo_login']['name'];
    $fundo_tmp = $_FILES['fundo_login']['tmp_name'];
    $fundo_destino = "img/" . $fundo;

    if (move_uploaded_file($fundo_tmp, $fundo_destino)) {
        $sql = "UPDATE estilo SET fundo_login = '$fundo_destino'";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            VaiPara($pagina);
        }
    }
}

#9. Atualizar o Fundo do Login (Imagem):
#
if (isset($_FILES['icon_site'])) {
    $icon = $_FILES['icon_site']['name'];
    $icon_tmp = $_FILES['icon_site']['tmp_name'];
    $icon_destino = "img/" . $icon;

    if (move_uploaded_file($icon_tmp, $icon_destino)) {
        $sql = "UPDATE estilo SET icon_site = '$icon_destino'";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            VaiPara($pagina);
        }
    }
}