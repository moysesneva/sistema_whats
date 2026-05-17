<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) VaiPara('login.php');
$login = $_SESSION['login'];

include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

$pagina_nome_recebe = isset($_GET['pagina_nome']) ? (int)$_GET['pagina_nome'] : 0;

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$q_u = $stmt_u->get_result();
$stmt_u->close();

while ($r = $q_u->fetch_array()) {
    $nome        = Priletra($r['nome']);
    $img_perfil  = $r['perfil_img'];
    $autorizado  = $r['autorizado'];
    $tipo        = $r['tipo'];
    $usuario_api = $r['usuario_api'] ?? '';
    $porta       = $r['porta'] ?? '';
    $caminho_vps = $r['caminho_vps'] ?? '';
}

include 'menu.php';

if ($q_u->num_rows < 1) VaiPara('login.php');
if ($autorizado != 2)   VaiPara('desbloquar.php');

include 'bloqueio.php';

// --- Endpoint AJAX: buscar grupos ---
if (isset($_GET['ajax_grupos'])) {
    header('Content-Type: application/json');

    $q_cfg   = $conn->query("SELECT ip_vps, porta, api FROM config LIMIT 1");
    $cfg     = $q_cfg ? $q_cfg->fetch_assoc() : [];
    $api_url = $cfg['api'] ?? '';

    if (empty($api_url) || empty($usuario_api)) {
        echo json_encode(['erro' => 'API não configurada.']);
        exit;
    }

    $url = rtrim($api_url, '/') . '/groups?connectionId=' . urlencode($usuario_api);
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['erro' => 'Erro de conexão: ' . $err]);
    } elseif (!$resp) {
        echo json_encode(['erro' => 'Sem resposta da API.']);
    } else {
        $json = json_decode($resp, true);
        if (is_array($json)) {
            echo json_encode(['grupos' => $json]);
        } else {
            echo json_encode(['erro' => 'Resposta inválida da API.']);
        }
    }
    exit;
}

// Verifica se API está configurada (apenas para mostrar aviso)
$q_cfg       = $conn->query("SELECT ip_vps, api FROM config LIMIT 1");
$cfg         = $q_cfg ? $q_cfg->fetch_assoc() : [];
$api_configurada = !empty($cfg['api']) && !empty($usuario_api);

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
/* ── Grid ── */
.grupos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px,1fr)); gap: 16px; margin-top: 20px; }

/* ── Card ── */
.grupo-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.07); padding: 18px; display: flex; flex-direction: column; gap: 10px; border-top: 3px solid #001f3f; transition: box-shadow .2s, transform .2s; }
.grupo-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.12); transform: translateY(-2px); }

/* ── Avatar do grupo ── */
.grupo-avatar { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg,#001f3f,#0066cc); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; flex-shrink: 0; }

/* ── Textos ── */
.grupo-nome { font-weight: 700; font-size: 14.5px; color: #1a2340; margin-bottom: 1px; }
.grupo-meta { font-size: 11.5px; color: #888; }
.grupo-id-wrap { display: flex; align-items: center; gap: 6px; background: #f4f6fb; border-radius: 6px; padding: 5px 9px; }
.grupo-id-txt { font-size: 11px; color: #556; font-family: monospace; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.btn-copy { border: none; background: none; cursor: pointer; color: #001f3f; font-size: 13px; padding: 0 2px; flex-shrink: 0; }
.btn-copy:focus { outline: none; }
.btn-copy.copied { color: #27ae60; }

/* ── Badge participantes ── */
.badge-part { background: #eef4ff; color: #001f3f; border-radius: 20px; font-size: 11px; font-weight: 700; padding: 2px 9px; display: inline-flex; align-items: center; gap: 4px; }

/* ── Ações ── */
.grupo-acoes { display: flex; gap: 7px; margin-top: 2px; }
.grupo-acoes .btn { font-size: 12px; border-radius: 7px; display: flex; align-items: center; gap: 4px; padding: 5px 12px; }

/* ── Info box ── */
.info-box { background: #eef4ff; border-left: 4px solid #001f3f; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #334; }
.info-box i { color: #001f3f; margin-right: 5px; }

/* ── Estado vazio ── */
.empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
.empty-state .es-icon { font-size: 56px; display: block; margin-bottom: 16px; color: #ccd; }
.empty-state h5 { color: #888; margin-bottom: 8px; }

/* ── Spinner ── */
.spinner-wrap { text-align: center; padding: 60px 20px; display: none; }
.spinner-wrap .spin { width: 44px; height: 44px; border: 4px solid #e0e4ef; border-top-color: #001f3f; border-radius: 50%; animation: spin .75s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Toast ── */
#gr-toast { position: fixed; bottom: 24px; right: 24px; background: #1a2340; color: #fff; border-radius: 10px; padding: 11px 20px; font-size: 13px; font-weight: 600; display: none; z-index: 9999; box-shadow: 0 4px 20px rgba(0,0,0,.2); }
#gr-toast.success { border-left: 4px solid #27ae60; }
#gr-toast.error   { border-left: 4px solid #e74c3c; }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">

    <!-- Cabeçalho -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h4 style="color:#001f3f; font-weight:700; margin:0 0 3px;"><i class="fa fa-whatsapp"></i> Grupos do WhatsApp</h4>
            <small class="text-muted">Visualize e gerencie os grupos da conta conectada</small>
        </div>
        <div style="display:flex; gap:8px;">
            <button id="btnAtualizar" class="btn btn-sm btn-primary" <?= !$api_configurada ? 'disabled' : '' ?>>
                <i class="feather icon-refresh-cw"></i> Atualizar Grupos
            </button>
            <a href="msg_massa_grupos.php" class="btn btn-sm btn-success">
                <i class="feather icon-send"></i> Disparar para Grupos
            </a>
        </div>
    </div>

    <?php if (!$api_configurada): ?>
    <div class="info-box">
        <i class="feather icon-alert-circle"></i>
        <strong>API não configurada.</strong> Configure o IP da VPS e conecte sua conta WhatsApp via QR Code antes de usar esta página.
        <br><a href="config_adm.php" style="color:#001f3f; font-weight:600; margin-top:4px; display:inline-block;">Ir para Configurações →</a>
    </div>
    <?php endif; ?>

    <!-- Instrução inicial -->
    <div id="estadoInicial" class="info-box" <?= !$api_configurada ? 'style="display:none"' : '' ?>>
        <i class="feather icon-info"></i>
        Clique em <strong>Atualizar Grupos</strong> para buscar os grupos do WhatsApp conectado.
    </div>

    <!-- Spinner -->
    <div id="spinnerWrap" class="spinner-wrap">
        <div class="spin"></div>
        <p style="color:#888; font-size:13px;">Buscando grupos...</p>
    </div>

    <!-- Área de resultado -->
    <div id="resultadoArea"></div>

</div>

<!-- Toast -->
<div id="gr-toast"></div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
(function () {

    /* ── Toast ── */
    var toast = document.getElementById('gr-toast');
    var toastTimer;
    function showToast(msg, type) {
        toast.textContent = msg;
        toast.className   = type || 'success';
        toast.style.display = 'block';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toast.style.display = 'none'; }, 2800);
    }

    /* ── Templates de card ── */
    function renderCard(g) {
        var gid    = g.id    || '';
        var gnome  = g.subject || g.name || 'Grupo sem nome';
        var gpart  = parseInt(g.size || (g.participants ? g.participants.length : 0)) || 0;
        var gdesc  = g.desc  || g.description || '';
        var gadmin = g.owner || '';
        var dataStr = '';
        if (g.creation) {
            var d = new Date(g.creation * 1000);
            dataStr = d.toLocaleDateString('pt-BR');
        }

        var idCurto = gid.length > 30 ? gid.slice(0, 30) + '…' : gid;
        var linkEnvio = 'msg_massa_grupos.php?grupo=' + encodeURIComponent(gid);

        return '<div class="grupo-card">' +
            '<div style="display:flex;align-items:flex-start;gap:12px;">' +
                '<div class="grupo-avatar"><i class="fa fa-users"></i></div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div class="grupo-nome">' + escHtml(gnome) + '</div>' +
                    '<div class="grupo-meta" style="margin-top:4px;">' +
                        '<span class="badge-part"><i class="feather icon-user" style="font-size:10px;"></i> ' + gpart + ' participante(s)</span>' +
                        (dataStr ? ' &nbsp;<span style="font-size:11px;color:#aaa;">criado em ' + escHtml(dataStr) + '</span>' : '') +
                    '</div>' +
                    (gadmin ? '<div class="grupo-meta" style="margin-top:3px;"><i class="feather icon-shield" style="font-size:10px;"></i> Admin: ' + escHtml(gadmin) + '</div>' : '') +
                '</div>' +
            '</div>' +
            (gdesc ? '<div style="font-size:12px;color:#556;padding:6px 0 0;border-top:1px solid #f0f2f7;line-height:1.45;">' + escHtml(gdesc) + '</div>' : '') +
            '<div class="grupo-id-wrap">' +
                '<span class="grupo-id-txt" title="' + escHtml(gid) + '">' + escHtml(idCurto) + '</span>' +
                '<button class="btn-copy" data-gid="' + escHtml(gid) + '" title="Copiar ID do grupo"><i class="feather icon-copy"></i></button>' +
            '</div>' +
            '<div class="grupo-acoes">' +
                '<a href="' + escHtml(linkEnvio) + '" class="btn btn-sm btn-primary">' +
                    '<i class="feather icon-send"></i> Enviar Mensagem' +
                '</a>' +
            '</div>' +
        '</div>';
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Cópia do ID ── */
    function bindCopiar(container) {
        container.querySelectorAll('.btn-copy').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var gid = btn.dataset.gid;
                if (!gid) return;
                navigator.clipboard.writeText(gid).then(function () {
                    btn.classList.add('copied');
                    btn.innerHTML = '<i class="feather icon-check"></i>';
                    showToast('ID copiado!', 'success');
                    setTimeout(function () {
                        btn.classList.remove('copied');
                        btn.innerHTML = '<i class="feather icon-copy"></i>';
                    }, 2000);
                }).catch(function () { showToast('Não foi possível copiar.', 'error'); });
            });
        });
    }

    /* ── Buscar grupos via AJAX ── */
    function buscarGrupos() {
        var inicial  = document.getElementById('estadoInicial');
        var spinner  = document.getElementById('spinnerWrap');
        var resultado = document.getElementById('resultadoArea');
        var btn      = document.getElementById('btnAtualizar');

        if (inicial)  inicial.style.display = 'none';
        spinner.style.display = 'block';
        resultado.innerHTML   = '';
        btn.disabled          = true;

        fetch('grupos.php?ajax_grupos=1', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                spinner.style.display = 'none';
                btn.disabled = false;

                if (data.erro) {
                    resultado.innerHTML = '<div class="alert alert-warning"><i class="feather icon-alert-triangle"></i> ' + escHtml(data.erro) + '</div>';
                    return;
                }

                var grupos = data.grupos || [];
                if (grupos.length === 0) {
                    resultado.innerHTML =
                        '<div class="empty-state">' +
                            '<i class="feather icon-users es-icon"></i>' +
                            '<h5>Nenhum grupo encontrado</h5>' +
                            '<p>O WhatsApp conectado não possui grupos, ou a API ainda não sincronizou.</p>' +
                            '<a href="qrcode.php" class="btn btn-primary btn-sm"><i class="feather icon-smartphone"></i> Verificar Conexão</a>' +
                        '</div>';
                    return;
                }

                var html = '<p class="text-muted" style="font-size:13px;">' +
                    '<i class="feather icon-check-circle" style="color:#27ae60;"></i> ' +
                    grupos.length + ' grupo(s) encontrado(s)</p>' +
                    '<div class="grupos-grid">';
                grupos.forEach(function (g) { html += renderCard(g); });
                html += '</div>';
                resultado.innerHTML = html;
                bindCopiar(resultado);
                showToast(grupos.length + ' grupo(s) carregado(s)', 'success');
            })
            .catch(function (err) {
                spinner.style.display = 'none';
                btn.disabled = false;
                resultado.innerHTML = '<div class="alert alert-danger"><i class="feather icon-x-circle"></i> Erro ao buscar grupos. Verifique a conexão com a VPS.</div>';
                showToast('Erro de comunicação.', 'error');
            });
    }

    /* ── Bind botão ── */
    var btn = document.getElementById('btnAtualizar');
    if (btn) btn.addEventListener('click', buscarGrupos);

})();
</script>

<?php include 'footer.php'; ?>
