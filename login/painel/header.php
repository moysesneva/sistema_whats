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
                                <a href="#!" id="pcoded-fullscreen-btn">
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
// ── Badge de fila para atendentes (tipo=3 ou tipo=2 MultiAtendente) ──────────
$_fila_badge = 0;
if (isset($tipo, $usuario_api, $login) && ($tipo === '3' || ($tipo === '2' && ($udata['modo_atuante'] ?? '') === 'MultiAtendente'))) {
    // Departamentos do atendente
    $_at_deptos = [];
    $_sfd = $conn->prepare("SELECT depto_id FROM atendentes_depto WHERE login_atendente=? AND usuario_api=? LIMIT 20");
    if ($_sfd) {
        $_sfd->bind_param("ss", $login, $usuario_api);
        $_sfd->execute();
        $_rfd = $_sfd->get_result();
        $_sfd->close();
        while ($_rd = $_rfd->fetch_assoc()) $_at_deptos[] = (int)$_rd['depto_id'];
    }
    if (!empty($_at_deptos)) {
        $_in = implode(',', $_at_deptos);
        $_rq = $conn->query("SELECT COUNT(*) AS n FROM clientes WHERE usuario_api='" . $conn->real_escape_string($usuario_api) . "' AND modo_atendimento='fila' AND depto_atual IN ($_in)");
        if ($_rq) $_fila_badge = (int)$_rq->fetch_assoc()['n'];
    }
    unset($_at_deptos, $_sfd, $_rfd, $_rd, $_in, $_rq);
}

if ($total_menu > 0) {
    while ($row_menu = mysqli_fetch_array($query_menu)) {
        $id               = $row_menu['id'];
        $menu_nome        = $row_menu['menu'];
        $menu_pagina_menu = $row_menu['menu_pagina'];
        $icone_menu       = $row_menu['icone_menu'];

        $badge_html = '';
        if ($menu_pagina_menu === 'atendentes.php' && $_fila_badge > 0) {
            $badge_html = ' <span id="menu-fila-badge" style="background:#FF5500;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;font-weight:700;vertical-align:middle;min-width:18px;display:inline-block;text-align:center;">' . $_fila_badge . '</span>';
        } elseif ($menu_pagina_menu === 'atendentes.php') {
            // Badge vazio mas presente no DOM para update via JS
            $badge_html = ' <span id="menu-fila-badge" style="display:none;background:#FF5500;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;font-weight:700;vertical-align:middle;min-width:18px;text-align:center;"></span>';
        }

        echo ($id == $pagina_nome_recebe) ? '<li class="pcoded-hasmenu active">' : '<li class="pcoded-hasmenu">';
        echo '
            <a href="' . $menu_pagina_menu . '?pagina_nome=' . $id . '">
                <span class="pcoded-micon"><i class="' . $icone_menu . '"></i></span>
                <span class="pcoded-mtext">' . $menu_nome . $badge_html . '</span>
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
<?php
require_once __DIR__ . '/disk_warning_config.php';
if (isset($tipo) && in_array($tipo, DISK_WARNING_ROLES)) include __DIR__ . '/disk_warning_banner.php';
?>
<?php
if (isset($tipo) && in_array($tipo, API_TOKEN_WARNING_ROLES) && empty(getenv('API_WEBHOOK_TOKEN'))):
?>
<div id="api-token-warning-banner"
     style="margin:16px 16px 0;padding:14px 18px;background:#fff3cd;border:1px solid #ffc107;border-left:5px solid #FF5500;border-radius:6px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
    <span style="color:#333;font-size:14px;line-height:1.6;">
        <i class="feather icon-alert-triangle" style="color:#FF5500;margin-right:8px;"></i>
        <strong>API_WEBHOOK_TOKEN não configurado.</strong>
        Todos os endpoints de API (<code style="background:#f0f0f0;padding:1px 4px;border-radius:3px;">/login/painel/api/</code>) estão bloqueados com erro 500 até que esse segredo seja definido.<br>
        <span style="margin-left:22px;">
            Para corrigir: gere um token com
            <code style="background:#f0f0f0;padding:1px 4px;border-radius:3px;">openssl rand -hex 32</code>
            e salve o valor como Secret <strong>API_WEBHOOK_TOKEN</strong> no painel do Replit.
        </span>
    </span>
    <button type="button"
            id="api-token-warn-dismiss-btn"
            style="background:none;border:none;font-size:20px;line-height:1;color:#888;cursor:pointer;padding:0 4px;flex-shrink:0;"
            aria-label="Fechar aviso">&times;</button>
</div>
<script src="../files/assets/js/api-token-warning.js"></script>
<?php endif; ?>
<?php
// Badge global de fila: polling JS para atendentes em qualquer página do painel
$_is_atendente_badge = isset($tipo) && ($tipo === '3' || ($tipo === '2' && ($udata['modo_atuante'] ?? '') === 'MultiAtendente'));
$_pagina_atual_badge = basename($_SERVER['PHP_SELF'] ?? '');
// Só injeta se for atendente e NÃO estiver em atendentes.php (que tem seu próprio polling)
if ($_is_atendente_badge && $_pagina_atual_badge !== 'atendentes.php'):
?>
<script>
(function() {
    'use strict';
    var _filaGlobal = <?= (int)$_fila_badge ?>;  // valor inicial do servidor
    var API_CONTAGEM = '../api/atendente_acao.php?acao=contagem_fila';

    function tocarAlertaGlobal() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            [0, 0.18, 0.36].forEach(function(t) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.35, ctx.currentTime + t);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + t + 0.25);
                osc.start(ctx.currentTime + t);
                osc.stop(ctx.currentTime + t + 0.25);
            });
        } catch(e) {}
    }

    function mostrarToastGlobal(n) {
        var toast = document.getElementById('toast-fila-global');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast-fila-global';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#001f3f;color:#fff;padding:14px 20px;border-radius:10px;box-shadow:0 4px 18px rgba(0,0,0,.25);font-size:14px;z-index:9999;display:flex;align-items:center;gap:10px;cursor:pointer;max-width:320px;';
            toast.onclick = function() { window.location.href = 'atendentes.php'; };
            document.body.appendChild(toast);
        }
        toast.innerHTML = '<span style="font-size:22px;color:#FF5500;">🔔</span><span><strong>' + n + ' conversa' + (n > 1 ? 's' : '') + ' na fila</strong><br><small style="opacity:.8;">Clique para atender</small></span>';
        toast.style.opacity = '1';
        toast.style.display = 'flex';
        clearTimeout(toast._t);
        toast._t = setTimeout(function() { toast.style.opacity = '0'; setTimeout(function(){ toast.style.display='none'; }, 400); }, 6000);
    }

    function atualizarBadgeGlobal(n) {
        var badge = document.getElementById('menu-fila-badge');
        if (badge) {
            if (n > 0) { badge.textContent = n; badge.style.display = 'inline-block'; }
            else { badge.style.display = 'none'; }
        }
        var base = document.title.replace(/^\[\d+\]\s*/, '');
        document.title = n > 0 ? '[' + n + '] ' + base : base;
    }

    function pollFila() {
        fetch(API_CONTAGEM, { credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.ok) return;
                var n = parseInt(data.fila) || 0;
                if (_filaGlobal >= 0 && n > _filaGlobal) {
                    tocarAlertaGlobal();
                    mostrarToastGlobal(n);
                }
                _filaGlobal = n;
                atualizarBadgeGlobal(n);
            })
            .catch(function() {});
    }

    // Primeira chamada após 3s (aguarda DOM pronto), depois a cada 10s
    setTimeout(pollFila, 3000);
    setInterval(pollFila, 10000);
})();
</script>
<?php endif; ?>
