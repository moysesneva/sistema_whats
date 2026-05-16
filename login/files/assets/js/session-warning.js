/**
 * session-warning.js — Aviso de expiração de sessão no painel.
 *
 * Exibe um modal X minutos antes do timeout de inatividade e permite
 * que o admin renove a sessão via AJAX (keepalive.php).
 *
 * Configuração (definida via variáveis globais antes de carregar este script):
 *   window.SESSION_TIMEOUT_SEC  — tempo total de inatividade em segundos
 *   window.SESSION_KEEPALIVE_URL — URL do endpoint keepalive.php
 *   window.SESSION_LOGIN_URL    — URL de redirecionamento ao expirar
 *   window.SESSION_WARN_SEC     — segundos antes do fim para exibir aviso (padrão: 300)
 */
(function ($) {
    'use strict';

    var totalSec      = window.SESSION_TIMEOUT_SEC    || 1800;
    var warnSec       = window.SESSION_WARN_SEC       || 300;
    var keepaliveUrl  = window.SESSION_KEEPALIVE_URL  || 'api/keepalive.php';
    var loginUrl      = window.SESSION_LOGIN_URL      || 'login_adm.php?expirado=1';

    var countdownInterval = null;
    var checkInterval     = null;
    var modalVisible      = false;
    var renewInProgress   = false;
    var renewRetryCount   = 0;
    var MAX_RETRIES       = 3;

    var serverRemainingAt = null;
    var serverRemainingTs = null;

    function getClientRemaining() {
        if (serverRemainingAt !== null && serverRemainingTs !== null) {
            var elapsed = Math.floor((Date.now() - serverRemainingTs) / 1000);
            return Math.max(0, serverRemainingAt - elapsed);
        }
        return totalSec;
    }

    function syncFromServer(restanteSeg) {
        serverRemainingAt = restanteSeg;
        serverRemainingTs = Date.now();
    }

    function formatTime(sec) {
        var m = Math.floor(sec / 60);
        var s = sec % 60;
        return (m > 0 ? m + 'm ' : '') + (s < 10 ? '0' : '') + s + 's';
    }

    function showModal(remainingSec) {
        if (modalVisible) return;
        modalVisible = true;
        $('#session-warning-btn-continuar').prop('disabled', false).text('');
        $('<i class="feather icon-refresh-cw" style="margin-right:6px;"></i>').appendTo('#session-warning-btn-continuar');
        $('#session-warning-btn-continuar').append('Continuar sessão');
        $('#session-warning-status').hide().text('');
        updateCountdown(remainingSec);
        startCountdown(remainingSec);
        $('#session-warning-modal').modal({backdrop: 'static', keyboard: false});
    }

    function hideModal() {
        modalVisible = false;
        clearInterval(countdownInterval);
        countdownInterval = null;
        renewRetryCount   = 0;
        $('#session-warning-modal').modal('hide');
    }

    function updateCountdown(sec) {
        if (sec <= 0) {
            clearInterval(countdownInterval);
            countdownInterval = null;
            redirectToLogin();
            return;
        }
        $('#session-warning-countdown').text(formatTime(sec));
    }

    function startCountdown(remainingSec) {
        var left = Math.floor(remainingSec);
        clearInterval(countdownInterval);
        countdownInterval = setInterval(function () {
            left--;
            updateCountdown(left);
        }, 1000);
    }

    function redirectToLogin() {
        clearInterval(checkInterval);
        clearInterval(countdownInterval);
        window.location.href = loginUrl;
    }

    function setRenewStatus(msg, isError) {
        var $s = $('#session-warning-status');
        $s.text(msg)
          .css('color', isError ? '#c0392b' : '#27ae60')
          .show();
    }

    function renewSession(isRetry) {
        if (renewInProgress && !isRetry) return;
        renewInProgress = true;

        var $btn = $('#session-warning-btn-continuar');
        $btn.prop('disabled', true);
        if (!isRetry) {
            $btn.html('<i class="feather icon-loader" style="margin-right:6px;"></i>Aguardando…');
        }

        $.ajax({
            url: keepaliveUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 8000,
            success: function (data) {
                renewInProgress = false;
                renewRetryCount = 0;
                if (data && data.ok) {
                    if (data.restante_seg !== undefined) {
                        syncFromServer(data.restante_seg);
                    }
                    if (data.session_timeout) {
                        totalSec = data.session_timeout;
                    }
                    hideModal();
                } else {
                    redirectToLogin();
                }
            },
            error: function (xhr) {
                renewInProgress = false;
                if (xhr.status === 401) {
                    redirectToLogin();
                    return;
                }
                renewRetryCount++;
                if (renewRetryCount <= MAX_RETRIES) {
                    setRenewStatus(
                        'Erro de rede. Tentando novamente (' + renewRetryCount + '/' + MAX_RETRIES + ')…',
                        true
                    );
                    $btn.html('<i class="feather icon-refresh-cw" style="margin-right:6px;"></i>Continuar sessão');
                    $btn.prop('disabled', false);
                    setTimeout(function () { renewSession(true); }, 3000);
                } else {
                    setRenewStatus(
                        'Não foi possível renovar a sessão. Verifique sua conexão.',
                        true
                    );
                    $btn.html('<i class="feather icon-refresh-cw" style="margin-right:6px;"></i>Tentar novamente');
                    $btn.prop('disabled', false);
                    renewRetryCount = 0;
                }
            }
        });
    }

    function checkSession() {
        var remaining = getClientRemaining();

        if (remaining <= 0) {
            clearInterval(checkInterval);
            redirectToLogin();
            return;
        }

        if (remaining <= warnSec) {
            if (!modalVisible) {
                showModal(remaining);
            }
        }
    }

    $(document).ready(function () {
        checkInterval = setInterval(checkSession, 5000);

        $('#session-warning-btn-continuar').on('click', function () {
            renewRetryCount = 0;
            renewSession(false);
        });

        $('#session-warning-btn-sair').on('click', function () {
            window.location.href = 'sair.php';
        });
    });

})(jQuery);
