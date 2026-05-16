<?php
// ─── Estilos base dos popups (emitidos uma única vez) ───────────────────────
if (!defined('ERRO_PHP_STYLES')) {
    define('ERRO_PHP_STYLES', true);
    echo '
    <style>
        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 13, 26, 0.85);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            animation: fadeInOverlay 0.25s ease;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .popup-box {
            background: #001729;
            border-radius: 16px;
            padding: 36px 32px;
            text-align: center;
            width: 340px;
            max-width: 90vw;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.07);
            animation: slideUp 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes slideUp {
            from { transform: translateY(30px) scale(0.95); opacity: 0; }
            to   { transform: translateY(0) scale(1);      opacity: 1; }
        }
        .popup-icon {
            font-size: 42px;
            line-height: 1;
            margin-bottom: 14px;
        }
        .popup-box h2 {
            font-family: "Montserrat", sans-serif !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            margin: 0 0 10px !important;
            color: #ffffff !important;
        }
        .popup-box p {
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            margin: 0 0 24px;
            line-height: 1.6;
        }
        .popup-btn {
            display: inline-block;
            padding: 11px 28px;
            border: none;
            border-radius: 8px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
        }
        .popup-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
        .popup-btn-danger  { background: linear-gradient(135deg,#FF5500,#e64a00); color:#fff; }
        .popup-btn-success { background: linear-gradient(135deg,#00a36b,#007a50); color:#fff; }
        .popup-btn-info    { background: linear-gradient(135deg,#0057a8,#003d7a); color:#fff; }
        .popup-top-danger  { width:60px; height:3px; background:#FF5500; border-radius:2px; margin:0 auto 22px; }
        .popup-top-success { width:60px; height:3px; background:#00a36b; border-radius:2px; margin:0 auto 22px; }
        .popup-top-info    { width:60px; height:3px; background:#0057a8; border-radius:2px; margin:0 auto 22px; }
    </style>';
}

// ─── Helpers ────────────────────────────────────────────────────────────────
function popup_fechar_btn($id, $tipo = 'danger') {
    return '<button class="popup-btn popup-btn-' . $tipo . '" data-fn="__el_remove" data-args=\'[' . json_encode((string)$id) . ']\'>' . 'Fechar</button>';
}
function popup_wrap($id, $top_class, $icon, $titulo, $msg, $btn) {
    return '<div class="popup-overlay" id="' . $id . '">
        <div class="popup-box">
            <div class="' . $top_class . '"></div>
            <div class="popup-icon">' . $icon . '</div>
            <h2>' . $titulo . '</h2>
            <p>' . $msg . '</p>
            ' . $btn . '
        </div>
    </div>';
}
?>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'login'): ?>
<?php echo popup_wrap('popup-login', 'popup-top-danger', '🔐', 'Acesso negado', 'Telefone ou senha incorretos. Verifique suas credenciais e tente novamente.', popup_fechar_btn('popup-login', 'danger')); ?>
<?php endif; ?>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'login_duplicado'): ?>
<?php echo popup_wrap('popup-dup', 'popup-top-danger', '⚠️', 'Usuário já existe', 'Este número de telefone já está cadastrado no sistema.', popup_fechar_btn('popup-dup', 'danger')); ?>
<?php endif; ?>

<?php if (isset($_GET['confirmacao']) && $_GET['confirmacao'] == 'cadastro_sucesso'): ?>
<?php echo popup_wrap('popup-cad', 'popup-top-success', '✅', 'Cadastro realizado!', 'Sua conta foi criada com sucesso. Faça login para acessar o sistema.', popup_fechar_btn('popup-cad', 'success')); ?>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
<?php echo popup_wrap('popup-ok', 'popup-top-success', '✅', 'Sucesso!', 'Operação realizada com sucesso.', popup_fechar_btn('popup-ok', 'success')); ?>
<?php endif; ?>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'code'): ?>
<?php echo popup_wrap('popup-code', 'popup-top-danger', '❌', 'Código inválido', 'O código inserido está incorreto. Por favor, tente novamente.', popup_fechar_btn('popup-code', 'danger')); ?>
<?php endif; ?>

<?php if (isset($_GET['confirmacao']) && $_GET['confirmacao'] == 'atualizado'): ?>
<?php echo popup_wrap('popup-upd', 'popup-top-success', '✅', 'Atualizado!', 'As informações foram atualizadas com sucesso.', popup_fechar_btn('popup-upd', 'success')); ?>
<?php endif; ?>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'atualizado'): ?>
<?php echo popup_wrap('popup-upd-err', 'popup-top-danger', '❌', 'Erro ao salvar', 'Não foi possível salvar os dados. Por favor, tente novamente.', popup_fechar_btn('popup-upd-err', 'danger')); ?>
<?php endif; ?>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'duplicado'): ?>
<?php echo popup_wrap('popup-dup2', 'popup-top-danger', '⚠️', 'Erro no agendamento', 'Este horário já está reservado. Por favor, escolha outro horário.', popup_fechar_btn('popup-dup2', 'danger')); ?>
<?php endif; ?>

<?php if (isset($_GET['agenda']) && $_GET['agenda'] == 'atualizado'): ?>
<?php echo popup_wrap('popup-ag', 'popup-top-success', '📅', 'Agendamento atualizado!', 'O agendamento foi atualizado com sucesso.', popup_fechar_btn('popup-ag', 'success')); ?>
<?php endif; ?>

<?php if (isset($_GET['aguarde']) && isset($_GET['tempo'])):
    $pagina_destino = htmlspecialchars($_GET['aguarde'], ENT_QUOTES);
    $tempo_ms = intval($_GET['tempo']) * 1000;
    $intervalo = max(10, $tempo_ms / 100);
?>
<div class="popup-overlay" id="popup-aguarde">
    <div class="popup-box">
        <div class="popup-top-info"></div>
        <div class="popup-icon">⏳</div>
        <h2>Aguarde...</h2>
        <p>Estamos processando e redirecionando você automaticamente.</p>
        <div style="background:rgba(255,255,255,0.06);border-radius:8px;height:10px;overflow:hidden;margin-top:4px;">
            <div id="progressBar" style="height:100%;width:0%;background:linear-gradient(90deg,#FF5500,#ff7733);border-radius:8px;transition:width 0.1s linear;"></div>
        </div>
        <p style="margin-top:10px;font-size:12px;" id="pct-label">0%</p>
    </div>
</div>
<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
(function(){
    var w = 0;
    var bar = document.getElementById('progressBar');
    var lbl = document.getElementById('pct-label');
    var iv = setInterval(function(){
        if(w >= 100){ clearInterval(iv); window.location.href = "<?php echo $pagina_destino; ?>"; return; }
        w++;
        bar.style.width = w + '%';
        lbl.textContent = w + '%';
    }, <?php echo $intervalo; ?>);
})();
</script>
<?php endif; ?>
