(function () {
    'use strict';

    function getPainel()    { return document.getElementById('qrCodeInline'); }
    function getContainer() { return document.getElementById('qrCodeContainer'); }

    function mostrarPainel() {
        var p = getPainel();
        if (p) p.style.display = 'block';
    }
    function ocultarPainel() {
        var p = getPainel();
        if (p) p.style.display = 'none';
    }

    function carregarQr() {
        var btn = document.getElementById('btnGerarQRCode');
        var usuarioApi = btn ? (btn.getAttribute('data-usuario-api') || '') : '';
        var endpointQr = 'qr/gerar_qrcode.php?usuario=' + encodeURIComponent(usuarioApi);

        mostrarPainel();
        var container = getContainer();
        if (!container) return;

        container.innerHTML =
            '<div class="text-center p-3">' +
            '<div class="spinner-border text-primary" role="status">' +
            '<span class="sr-only">Carregando...</span></div>' +
            '<p class="mt-2 text-muted">Gerando QR Code\u2026 aguarde at\u00e9 20s</p></div>';

        fetch(endpointQr, { credentials: 'same-origin' })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var c = getContainer();
                if (c) c.innerHTML = html;
            })
            .catch(function () {
                var c = getContainer();
                if (c) c.innerHTML =
                    '<p class="text-danger text-center mt-3">' +
                    '<i class="feather icon-alert-triangle"></i> ' +
                    'Erro ao conectar com o servidor. Verifique se o VPS est\u00e1 online.</p>';
            });
    }

    function init() {
        var btnGerar   = document.getElementById('btnGerarQRCode');
        var btnRefresh = document.getElementById('btnRefreshQR');
        var btnFechar  = document.getElementById('btnFecharQR');
        if (btnGerar)   btnGerar.addEventListener('click', carregarQr);
        if (btnRefresh) btnRefresh.addEventListener('click', carregarQr);
        if (btnFechar)  btnFechar.addEventListener('click', ocultarPainel);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
