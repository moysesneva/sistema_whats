<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <link href="../files/assets/vendor/fonts/open-sans/open-sans.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/feather/css/feather.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/css/jquery.mCustomScrollbar.css">
    <?php if (isset($css_extra)) echo $css_extra; ?>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="feather icon-menu"></i>
                        </a>
                        <a href="index.php">
                            <img class="img-fluid" src="<?=$logo;?>" alt="Theme-Logo" style="width: 150px; height: 30px;">
                        </a>
                        <a class="mobile-options">
                            <i class="feather icon-more-horizontal"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="feather icon-x"></i></span>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="feather icon-maximize full-screen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-notification">
                                <div class="dropdown-primary dropdown">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?=$img_perfil;?>" class="img-radius" alt="User-Profile-Image">
                                        <span><?=$nome?></span>
                                        <i class="feather icon-chevron-down"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <li>
                                            <a href="config.php">
                                                <i class="feather icon-settings"></i> Configurações
                                            </a>
                                        </li>
                                        <li>
                                            <a href="perfil.php">
                                                <i class="feather icon-user"></i> Perfil
                                            </a>
                                        </li>
                                        <li>
                                            <a href="sair.php">
                                                <i class="feather icon-log-out"></i> Sair
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="pcoded-navigatio-lavel">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">

<?php
if ($total_menu > 0) {
    while ($row_menu = mysqli_fetch_array($query_menu)) {
        $id             = $row_menu['id'];
        $menu_nome      = $row_menu['menu'];
        $menu_pagina_menu = $row_menu['menu_pagina'];
        $icone_menu     = $row_menu['icone_menu'];

        if ($id == $pagina_nome_recebe) {
            echo '<li class="pcoded-hasmenu active">';
        } else {
            echo '<li class="pcoded-hasmenu">';
        }
        echo '
            <a href="' . $menu_pagina_menu . '?pagina_nome=' . $id . '">
                <span class="pcoded-micon"><i class="' . $icone_menu . '"></i></span>
                <span class="pcoded-mtext">' . $menu_nome . '</span>
            </a>
        </li>';
    }
}
?>

                            </ul>
                        </div>
                    </nav>

                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
<?php
// Alerta de falhas recentes de conexão ao banco (visível apenas para admin tipo 1 ou 4)
if (isset($tipo) && in_array($tipo, [1, 4])) {
    $_dbf_log   = __DIR__ . '/logs/db_failures.log';
    $_dbf_recente = 0;
    if (is_file($_dbf_log)) {
        $_dbf_linhas = file($_dbf_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $_dbf_agora  = time();
        foreach ($_dbf_linhas as $_dbf_linha) {
            $_dbf_obj = json_decode($_dbf_linha, true);
            if (is_array($_dbf_obj) && isset($_dbf_obj['ts'])) {
                if (($_dbf_agora - strtotime($_dbf_obj['ts'])) <= 3600) {
                    $_dbf_recente++;
                }
            }
        }
        unset($_dbf_linhas, $_dbf_obj, $_dbf_linha, $_dbf_agora);
    }
    if ($_dbf_recente > 0):
?>
<div class="alert alert-warning alert-dismissible fade show" role="alert"
     style="margin:16px 16px 0;border-left:4px solid #FF5500;border-radius:8px;">
    <i class="feather icon-alert-triangle" style="margin-right:6px;color:#FF5500;"></i>
    <strong>Atenção:</strong>
    <?= $_dbf_recente ?> falha<?= $_dbf_recente > 1 ? 's' : '' ?> de conexão ao banco registrada<?= $_dbf_recente > 1 ? 's' : '' ?> na última hora.
    <a href="db_diagnostics.php" style="color:#001f3f;font-weight:600;margin-left:8px;">
        <i class="feather icon-eye"></i> Ver diagnóstico
    </a>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php
    endif;
    unset($_dbf_log, $_dbf_recente);
}
?>
