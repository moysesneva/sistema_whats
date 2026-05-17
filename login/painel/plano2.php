<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}
$login = $_SESSION['login'];

include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;
}

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$query_u = $stmt_u->get_result();
$total_u = $query_u->num_rows;

while ($row_u = $query_u->fetch_array()) {
    $nome       = Priletra($row_u['nome']);
    $img_perfil = $row_u['perfil_img'];
    $autorizado = $row_u['autorizado'];
    $tipo       = $row_u['tipo'];
    $plano      = $row_u['plano'];
}

include 'menu.php';

if ($total_u != 1)    VaiPara('login.php');
if ($autorizado != 2) VaiPara('desbloquar.php');

// Busca todos os planos ativos com suas features
$stmt_planos = $conn->prepare("SELECT po.*, GROUP_CONCAT(pf.feature ORDER BY pf.id SEPARATOR '||') as features FROM planos_online po LEFT JOIN planos_features pf ON pf.id_plano = po.id WHERE po.ativo = 1 GROUP BY po.id ORDER BY po.preco ASC");
$stmt_planos->execute();
$result_planos = $stmt_planos->get_result();
$planos_lista = [];
while ($p = $result_planos->fetch_assoc()) {
    $p['features_arr'] = $p['features'] ? explode('||', $p['features']) : [];
    $planos_lista[] = $p;
}
$stmt_planos->close();

// Busca módulos do plano ativo do usuário
$modulos_plano = [];
if ($plano) {
    $stmt_mod = $conn->prepare("SELECT nome_modulo FROM planos_clientes WHERE nome_plano = ? AND tipo = 1");
    $stmt_mod->bind_param("s", $plano);
    $stmt_mod->execute();
    $result_mod = $stmt_mod->get_result();
    while ($m = $result_mod->fetch_assoc()) {
        $modulos_plano[] = $m['nome_modulo'];
    }
    $stmt_mod->close();
}

$css_extra = '
<style>
.plano-card {
    background: rgba(0,31,63,0.6);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 32px 24px;
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.plano-card:hover {
    border-color: #FF5500;
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(255,85,0,0.18);
}
.plano-card.plano-ativo {
    border-color: #FF5500;
    background: rgba(255,85,0,0.08);
}
.plano-card.plano-destaque {
    border-color: #FF5500;
}
.plano-badge {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    background: #FF5500;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 18px;
    border-radius: 20px;
    letter-spacing: 1px;
    text-transform: uppercase;
    white-space: nowrap;
}
.plano-titulo {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 4px;
}
.plano-preco {
    font-size: 40px;
    font-weight: 800;
    color: #FF5500;
    line-height: 1;
    margin: 16px 0 4px;
}
.plano-preco small {
    font-size: 14px;
    font-weight: 400;
    color: rgba(255,255,255,0.5);
}
.plano-features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    flex: 1;
}
.plano-features li {
    color: rgba(255,255,255,0.75);
    font-size: 13px;
    padding: 7px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.plano-features li:last-child { border-bottom: none; }
.plano-features li i { color: #FF5500; margin-top: 2px; flex-shrink: 0; }
.btn-assinar {
    display: block;
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s;
    margin-top: auto;
    border: none;
    cursor: pointer;
}
.btn-assinar-ativo {
    background: rgba(255,85,0,0.15);
    color: #FF5500;
    border: 1px solid #FF5500;
    cursor: default;
}
.btn-assinar-upgrade {
    background: #FF5500;
    color: #fff;
}
.btn-assinar-upgrade:hover {
    background: #e04a00;
    color: #fff;
    text-decoration: none;
    transform: scale(1.02);
}
.plano-ativo-banner {
    background: rgba(255,85,0,0.1);
    border: 1px solid rgba(255,85,0,0.3);
    border-radius: 12px;
    padding: 20px 24px;
}
.modulo-tag {
    display: inline-block;
    background: rgba(255,85,0,0.15);
    color: #FF5500;
    border: 1px solid rgba(255,85,0,0.3);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600;
    margin: 4px 4px 4px 0;
}
</style>
';

include 'header.php';
?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Meu Plano</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="modo.php">Início</a></li>
                    <li class="breadcrumb-item">Meu Plano</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="main-body">
    <div class="page-wrapper">

        <!-- PLANO ATUAL -->
        <div class="row m-b-30">
            <div class="col-12">
                <div class="plano-ativo-banner">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 style="color:#fff;margin:0 0 6px;">
                                <i class="fa fa-credit-card" style="color:#FF5500;margin-right:8px;"></i>
                                Plano Atual
                            </h5>
                            <?php if ($plano): ?>
                                <p style="color:rgba(255,255,255,0.7);margin:0;font-size:14px;">
                                    Você está no <strong style="color:#FF5500;"><?= strtoupper($plano) ?></strong>.
                                </p>
                                <?php if (!empty($modulos_plano)): ?>
                                <div style="margin-top:10px;">
                                    <?php foreach ($modulos_plano as $mod): ?>
                                        <span class="modulo-tag"><i class="feather icon-check-circle" style="font-size:11px;"></i> <?= htmlspecialchars($mod) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p style="color:rgba(255,255,255,0.5);margin:0;font-size:14px;font-style:italic;">
                                    Nenhum plano ativo. Escolha um plano abaixo para começar.
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-right">
                            <span style="font-size:48px;color:rgba(255,85,0,0.2);">
                                <i class="fa fa-credit-card"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PLANOS DISPONÍVEIS -->
        <div class="row m-b-20">
            <div class="col-12">
                <h5 style="color:#fff;margin-bottom:24px;">
                    <i class="feather icon-package" style="color:#FF5500;margin-right:8px;"></i>
                    Planos Disponíveis
                </h5>
            </div>
        </div>

        <div class="row">
            <?php
            $plano_idx = 0;
            $destaques = [1]; // índice do plano em destaque (0-based)
            foreach ($planos_lista as $idx => $p):
                $is_ativo = ($plano === 'plano' . $p['id']);
                $is_destaque = in_array($idx, $destaques);
            ?>
            <div class="col-md-4 m-b-30">
                <div class="plano-card <?= $is_ativo ? 'plano-ativo' : '' ?> <?= $is_destaque && !$is_ativo ? 'plano-destaque' : '' ?>">
                    <?php if ($is_ativo): ?>
                        <span class="plano-badge">✓ Plano Ativo</span>
                    <?php elseif ($is_destaque): ?>
                        <span class="plano-badge">Mais Popular</span>
                    <?php endif; ?>

                    <div class="text-center" style="margin-bottom:8px;">
                        <?php if ($p['icone']): ?>
                            <img src="<?= htmlspecialchars($p['icone']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" style="height:52px;width:52px;object-fit:contain;margin-bottom:12px;" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div class="plano-titulo"><?= htmlspecialchars($p['titulo']) ?></div>
                    </div>

                    <div class="plano-preco text-center">
                        R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                        <br><small>/mês</small>
                    </div>

                    <?php if (!empty($p['features_arr'])): ?>
                    <ul class="plano-features">
                        <?php foreach ($p['features_arr'] as $feat): ?>
                            <li>
                                <i class="feather icon-check-circle"></i>
                                <?= htmlspecialchars($feat) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <?php if ($is_ativo): ?>
                        <span class="btn-assinar btn-assinar-ativo">
                            <i class="feather icon-check-circle" style="margin-right:6px;"></i> Plano Ativo
                        </span>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($p['link_pagamento']) ?>" target="_blank" class="btn-assinar btn-assinar-upgrade">
                            <i class="feather icon-zap" style="margin-right:6px;"></i>
                            <?= $plano ? 'Fazer Upgrade' : 'Assinar Agora' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($planos_lista)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="feather icon-info"></i> Nenhum plano disponível no momento. Contate o suporte.
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- FALE CONOSCO -->
        <div class="row m-t-20">
            <div class="col-12">
                <div class="card" style="background:rgba(0,31,63,0.4);border:1px solid rgba(255,255,255,0.06);">
                    <div class="card-block text-center" style="padding:24px;">
                        <i class="feather icon-message-circle" style="font-size:32px;color:#FF5500;display:block;margin-bottom:12px;"></i>
                        <h6 style="color:#fff;margin-bottom:8px;">Dúvidas sobre os planos?</h6>
                        <p style="color:rgba(255,255,255,0.5);font-size:13px;margin-bottom:16px;">
                            Entre em contato com nosso suporte para mais informações ou para solicitar um plano personalizado.
                        </p>
                        <a href="https://wa.me/5511994040494" target="_blank" class="btn" style="background:#FF5500;color:#fff;border-radius:8px;padding:10px 28px;font-weight:600;">
                            <i class="feather icon-message-circle" style="margin-right:6px;"></i> Falar com Suporte
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
