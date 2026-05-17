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

// Busca configurações da VPS
$q_cfg = $conn->query("SELECT ip_vps, porta, api FROM config LIMIT 1");
$cfg   = $q_cfg ? $q_cfg->fetch_assoc() : [];
$ip_vps  = $cfg['ip_vps'] ?? '';
$porta_vps = $cfg['porta'] ?? '443';
$api_url   = $cfg['api'] ?? '';

// Tenta buscar grupos via API da VPS
$grupos = [];
$erro_api = '';
$api_configurada = !empty($ip_vps) && !empty($usuario_api);

if ($api_configurada && isset($_GET['atualizar'])) {
    $url = rtrim($api_url, '/') . '/groups?connectionId=' . urlencode($usuario_api);
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        $erro_api = 'Erro de conexão com a API: ' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8');
    } elseif ($resp) {
        $json = json_decode($resp, true);
        if (is_array($json)) {
            $grupos = $json;
        } else {
            $erro_api = 'Resposta inválida da API.';
        }
    }
}

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
.grupos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 16px; margin-top: 20px; }
.grupo-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 18px; display: flex; flex-direction: column; gap: 10px; border-left: 4px solid #001f3f; }
.grupo-card .grupo-nome { font-weight: 700; font-size: 15px; color: #1a2340; }
.grupo-card .grupo-info { font-size: 12px; color: #888; }
.grupo-card .grupo-acoes { display: flex; gap: 8px; margin-top: 4px; }
.empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
.empty-state i { font-size: 56px; display: block; margin-bottom: 16px; color: #ccd; }
.empty-state h5 { color: #888; margin-bottom: 8px; }
.info-box { background: #eef4ff; border-left: 4px solid #001f3f; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #334; }
.info-box i { color: #001f3f; }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <div>
            <h4 style="color:#001f3f; font-weight:700; margin:0;"><i class="feather icon-users"></i> Grupos do WhatsApp</h4>
            <small class="text-muted">Gerencie e visualize seus grupos ativos</small>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="?atualizar=1" class="btn btn-sm btn-primary"><i class="feather icon-refresh-cw"></i> Atualizar Grupos</a>
            <a href="msg_massa_grupos.php" class="btn btn-sm btn-success"><i class="feather icon-send"></i> Disparar para Grupos</a>
        </div>
    </div>

    <?php if (!$api_configurada): ?>
    <div class="info-box">
        <i class="feather icon-alert-circle"></i>
        <strong>API não configurada.</strong> Configure o IP da VPS e conecte sua conta WhatsApp via QR Code antes de usar esta funcionalidade.
        <br><a href="config_adm.php" style="color:#001f3f; font-weight:600;">Ir para Configurações →</a>
    </div>
    <?php endif; ?>

    <?php if ($erro_api): ?>
    <div class="alert alert-warning"><i class="feather icon-alert-triangle"></i> <?= $erro_api ?></div>
    <?php endif; ?>

    <?php if (!isset($_GET['atualizar']) && $api_configurada): ?>
    <div class="info-box">
        <i class="feather icon-info"></i>
        Clique em <strong>Atualizar Grupos</strong> para buscar os grupos do WhatsApp conectado.
    </div>
    <?php endif; ?>

    <?php if (!empty($grupos)): ?>
    <p class="text-muted" style="font-size:13px;"><i class="feather icon-check-circle" style="color:#27ae60;"></i> <?= count($grupos) ?> grupo(s) encontrado(s)</p>
    <div class="grupos-grid">
        <?php foreach ($grupos as $g):
            $gid    = htmlspecialchars($g['id'] ?? '', ENT_QUOTES, 'UTF-8');
            $gnome  = htmlspecialchars($g['subject'] ?? $g['name'] ?? 'Grupo sem nome', ENT_QUOTES, 'UTF-8');
            $gpart  = (int)($g['size'] ?? count($g['participants'] ?? []));
            $gadmin = isset($g['owner']) ? htmlspecialchars($g['owner'], ENT_QUOTES, 'UTF-8') : '';
        ?>
        <div class="grupo-card">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#001f3f,#0066cc);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;flex-shrink:0;">
                    <i class="feather icon-users"></i>
                </div>
                <div>
                    <div class="grupo-nome"><?= $gnome ?></div>
                    <?php if ($gadmin): ?><div class="grupo-info">Admin: <?= $gadmin ?></div><?php endif; ?>
                    <div class="grupo-info"><i class="feather icon-user" style="font-size:11px;"></i> <?= $gpart ?> participante(s)</div>
                </div>
            </div>
            <div class="grupo-acoes">
                <a href="msg_massa_grupos.php?grupo=<?= urlencode($gid) ?>" class="btn btn-sm btn-primary" style="font-size:12px;">
                    <i class="feather icon-send"></i> Enviar Mensagem
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php elseif (isset($_GET['atualizar']) && !$erro_api): ?>
    <div class="empty-state">
        <i class="feather icon-users"></i>
        <h5>Nenhum grupo encontrado</h5>
        <p>O WhatsApp conectado não possui grupos, ou a API ainda não sincronizou.</p>
        <a href="qrcode.php" class="btn btn-primary btn-sm"><i class="feather icon-smartphone"></i> Verificar Conexão</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
