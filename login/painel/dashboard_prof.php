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
    $nome_empresa = $r['nome_empresa'] ?? '';
}

include 'menu.php';

if ($q_u->num_rows < 1) VaiPara('login.php');
if ($autorizado != 2)   VaiPara('desbloquar.php');

include 'bloqueio.php';

// Busca dados do profissional
$stmt_p = $conn->prepare("SELECT id, profissional_nome, profissional_cargo, telefone FROM profissional WHERE telefone = ?");
$stmt_p->bind_param("s", $login);
$stmt_p->execute();
$q_p = $stmt_p->get_result();
$stmt_p->close();

$id_profissional   = 0;
$profissional_nome = $nome;
$profissional_cargo = '';
if ($row_p = $q_p->fetch_assoc()) {
    $id_profissional    = (int)$row_p['id'];
    $profissional_nome  = $row_p['profissional_nome'] ?: $nome;
    $profissional_cargo = $row_p['profissional_cargo'] ?? '';
}

$hoje  = date('Y-m-d');
$amanha = date('Y-m-d', strtotime('+1 day'));
$ini_semana = date('Y-m-d', strtotime('monday this week'));
$fim_semana = date('Y-m-d', strtotime('sunday this week'));
$ini_mes    = date('Y-m-01');
$fim_mes    = date('Y-m-t');

// --- Stats com ou sem id_profissional ---
function statCount($conn, $sql, $types, $params) {
    $st = $conn->prepare($sql);
    $st->bind_param($types, ...$params);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return (int)($r['total'] ?? 0);
}
function statSum($conn, $sql, $types, $params) {
    $st = $conn->prepare($sql);
    $st->bind_param($types, ...$params);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return (float)($r['total'] ?? 0);
}

if ($id_profissional > 0) {
    $ag_hoje   = statCount($conn, "SELECT COUNT(*) as total FROM agendamento WHERE id_profissional = ? AND data = ?",            "is", [$id_profissional, $hoje]);
    $ag_semana = statCount($conn, "SELECT COUNT(*) as total FROM agendamento WHERE id_profissional = ? AND data BETWEEN ? AND ?", "iss", [$id_profissional, $ini_semana, $fim_semana]);
    $ag_mes    = statCount($conn, "SELECT COUNT(*) as total FROM agendamento WHERE id_profissional = ? AND data BETWEEN ? AND ?", "iss", [$id_profissional, $ini_mes, $fim_mes]);
    $ag_conf   = statCount($conn, "SELECT COUNT(*) as total FROM agendamento WHERE id_profissional = ? AND data BETWEEN ? AND ? AND confirmacao = 1", "iss", [$id_profissional, $ini_mes, $fim_mes]);
    $fat_mes   = statSum($conn,   "SELECT SUM(valor_servico) as total FROM agendamento WHERE id_profissional = ? AND data BETWEEN ? AND ? AND valor_servico > 0", "iss", [$id_profissional, $ini_mes, $fim_mes]);

    // Próximos agendamentos
    $stmt_prox = $conn->prepare("SELECT data, horario, cliente_nome, cliente_telefone, confirmacao FROM agendamento WHERE id_profissional = ? AND data >= ? ORDER BY data ASC, horario ASC LIMIT 6");
    $stmt_prox->bind_param("is", $id_profissional, $hoje);
    $stmt_prox->execute();
    $prox_rows = $stmt_prox->get_result();
    $stmt_prox->close();
} else {
    $ag_hoje = $ag_semana = $ag_mes = $ag_conf = $fat_mes = 0;
    $prox_rows = null;
}

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
/* ── Grid de cards de stat ── */
.prof-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.prof-stat  { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.07); padding: 20px 18px; display: flex; align-items: center; gap: 14px; border-left: 4px solid #001f3f; }
.prof-stat.laranja { border-color: #FF5500; }
.prof-stat.verde   { border-color: #27ae60; }
.prof-stat.roxo    { border-color: #8e44ad; }
.prof-stat-icon    { width: 46px; height: 46px; border-radius: 12px; background: #eef4ff; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #001f3f; flex-shrink: 0; }
.prof-stat.laranja .prof-stat-icon { background: #fff3ee; color: #FF5500; }
.prof-stat.verde   .prof-stat-icon { background: #edfaf3; color: #27ae60; }
.prof-stat.roxo    .prof-stat-icon { background: #f5eeff; color: #8e44ad; }
.prof-stat-val     { font-size: 26px; font-weight: 800; color: #1a2340; line-height: 1; }
.prof-stat-label   { font-size: 12px; color: #888; margin-top: 2px; }

/* ── Saudação ── */
.prof-welcome { background: linear-gradient(135deg,#001f3f 60%,#003a70); color: #fff; border-radius: 14px; padding: 24px 28px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.prof-welcome h4 { margin: 0 0 4px; font-size: 22px; font-weight: 800; }
.prof-welcome p  { margin: 0; font-size: 13px; opacity: .75; }
.prof-badge      { background: #FF5500; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 700; margin-top: 4px; display: inline-block; }

/* ── Próximos agendamentos ── */
.next-table th  { background: #f4f6fb; font-size: 12px; font-weight: 700; color: #556; text-transform: uppercase; letter-spacing: .03em; }
.next-table td  { font-size: 13px; vertical-align: middle; }
.next-table tr:hover td { background: #fafbff; }
.data-badge     { background: #eef4ff; color: #001f3f; border-radius: 6px; padding: 3px 8px; font-weight: 700; font-size: 12px; white-space: nowrap; }
.hora-badge     { background: #f4f6fb; color: #334; border-radius: 6px; padding: 3px 8px; font-size: 12px; white-space: nowrap; }

/* ── Progresso confirmação ── */
.prog-wrap { background: #f4f6fb; border-radius: 20px; height: 10px; margin-top: 6px; overflow: hidden; }
.prog-bar  { height: 100%; border-radius: 20px; background: linear-gradient(90deg,#001f3f,#0066cc); transition: width .6s ease; }
</style>';

include 'header.php';

$saudacao = (date('H') < 12) ? 'Bom dia' : ((date('H') < 18) ? 'Boa tarde' : 'Boa noite');
$taxa_conf = $ag_mes > 0 ? round($ag_conf / $ag_mes * 100) : 0;
?>

<div class="container-fluid" style="padding:20px 24px;">

    <!-- Saudação -->
    <div class="prof-welcome">
        <div>
            <h4><?= $saudacao ?>, <?= htmlspecialchars($profissional_nome, ENT_QUOTES, 'UTF-8') ?>!</h4>
            <p><?= date('l, d \d\e F \d\e Y', strtotime($hoje)) ?></p>
            <?php if ($profissional_cargo): ?>
            <span class="prof-badge"><i class="feather icon-award" style="font-size:10px;"></i> <?= htmlspecialchars($profissional_cargo, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </div>
        <div style="text-align:right;">
            <div style="font-size:36px; font-weight:900; line-height:1;"><?= $ag_hoje ?></div>
            <div style="font-size:12px; opacity:.7; margin-top:2px;">agendamento(s) hoje</div>
        </div>
    </div>

    <!-- Stats -->
    <div class="prof-stats">
        <div class="prof-stat">
            <div class="prof-stat-icon"><i class="feather icon-calendar"></i></div>
            <div>
                <div class="prof-stat-val"><?= $ag_hoje ?></div>
                <div class="prof-stat-label">Hoje</div>
            </div>
        </div>
        <div class="prof-stat laranja">
            <div class="prof-stat-icon"><i class="feather icon-trending-up"></i></div>
            <div>
                <div class="prof-stat-val"><?= $ag_semana ?></div>
                <div class="prof-stat-label">Esta semana</div>
            </div>
        </div>
        <div class="prof-stat verde">
            <div class="prof-stat-icon"><i class="feather icon-layers"></i></div>
            <div>
                <div class="prof-stat-val"><?= $ag_mes ?></div>
                <div class="prof-stat-label">Este mês</div>
            </div>
        </div>
        <div class="prof-stat roxo">
            <div class="prof-stat-icon"><i class="feather icon-dollar-sign"></i></div>
            <div>
                <div class="prof-stat-val">R$&nbsp;<?= number_format($fat_mes, 0, ',', '.') ?></div>
                <div class="prof-stat-label">Faturado este mês</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximos agendamentos -->
        <div class="col-lg-8 mb-4">
            <div class="card" style="border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:none;">
                <div class="card-header" style="background:#fff; border-radius:12px 12px 0 0; border-bottom:1px solid #f0f2f7; padding:16px 20px;">
                    <h6 style="margin:0; font-weight:700; color:#1a2340;"><i class="feather icon-clock" style="color:#FF5500;"></i> Próximos Agendamentos</h6>
                </div>
                <div class="card-body" style="padding:0;">
                <?php if ($id_profissional <= 0): ?>
                    <div style="text-align:center; padding:40px; color:#aaa;">
                        <i class="feather icon-alert-circle" style="font-size:36px; display:block; margin-bottom:10px;"></i>
                        Seu cadastro como profissional ainda não foi localizado.<br>
                        <small>Fale com o administrador para ser vinculado.</small>
                    </div>
                <?php else: ?>
                    <?php
                    $prox_list = [];
                    if ($prox_rows) {
                        while ($pr = $prox_rows->fetch_assoc()) $prox_list[] = $pr;
                    }
                    ?>
                    <?php if (empty($prox_list)): ?>
                    <div style="text-align:center; padding:40px; color:#aaa;">
                        <i class="feather icon-calendar" style="font-size:36px; display:block; margin-bottom:10px;"></i>
                        Nenhum agendamento futuro encontrado.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table next-table mb-0">
                            <thead>
                                <tr>
                                    <th style="padding:10px 16px;">Data</th>
                                    <th>Horário</th>
                                    <th>Cliente</th>
                                    <th>Telefone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($prox_list as $pr):
                                $tel_clean = preg_replace('/[^0-9]/', '', $pr['cliente_telefone'] ?? '');
                                $wa_link   = 'https://wa.me/' . (str_starts_with($tel_clean, '55') ? $tel_clean : '55' . $tel_clean);
                                $conf = (int)$pr['confirmacao'];
                            ?>
                                <tr>
                                    <td style="padding:10px 16px;"><span class="data-badge"><?= date('d/m', strtotime($pr['data'])) ?></span></td>
                                    <td><span class="hora-badge"><?= htmlspecialchars($pr['horario'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td style="font-weight:600;"><?= htmlspecialchars($pr['cliente_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($tel_clean): ?>
                                        <a href="<?= $wa_link ?>" target="_blank" style="color:#27ae60; font-size:12px;">
                                            <i class="fa fa-whatsapp"></i> <?= htmlspecialchars($pr['cliente_telefone'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($conf == 1): ?>
                                            <span class="badge badge-success" style="font-size:11px;">Confirmado</span>
                                        <?php elseif ($conf == 2): ?>
                                            <span class="badge badge-danger"  style="font-size:11px;">Cancelado</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning" style="font-size:11px;">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                </div>
                <div class="card-footer" style="background:#fafbff; border-top:1px solid #f0f2f7; padding:10px 20px; border-radius:0 0 12px 12px; text-align:right;">
                    <a href="agenda_porf.php" style="font-size:12px; color:#001f3f; font-weight:600;"><i class="feather icon-calendar"></i> Ver agenda completa →</a>
                </div>
            </div>
        </div>

        <!-- Painel lateral -->
        <div class="col-lg-4 mb-4">

            <!-- Taxa de confirmação -->
            <div class="card mb-3" style="border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:none;">
                <div class="card-body" style="padding:18px 20px;">
                    <h6 style="font-weight:700; color:#1a2340; margin-bottom:14px;"><i class="feather icon-check-circle" style="color:#27ae60;"></i> Confirmação — Mês atual</h6>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="font-size:13px; color:#556;">Confirmados</span>
                        <span style="font-size:13px; font-weight:700; color:#27ae60;"><?= $ag_conf ?> / <?= $ag_mes ?></span>
                    </div>
                    <div class="prog-wrap">
                        <div class="prog-bar" style="width:<?= $taxa_conf ?>%;"></div>
                    </div>
                    <div style="text-align:right; font-size:11px; color:#888; margin-top:4px;"><?= $taxa_conf ?>% de confirmação</div>
                </div>
            </div>

            <!-- Atalhos rápidos -->
            <div class="card" style="border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:none;">
                <div class="card-header" style="background:#fff; border-radius:12px 12px 0 0; border-bottom:1px solid #f0f2f7; padding:14px 18px;">
                    <h6 style="margin:0; font-weight:700; color:#1a2340;"><i class="feather icon-grid" style="color:#FF5500;"></i> Atalhos</h6>
                </div>
                <div class="card-body" style="padding:14px;">
                    <?php
                    $atalhos = [
                        ['agenda_porf.php',      'icon-calendar',   '#001f3f', 'Minha Agenda'],
                        ['relatorio_prof.php',   'icon-bar-chart-2','#27ae60', 'Relatório'],
                        ['financeiro_prof.php',  'icon-dollar-sign','#FF5500', 'Financeiro'],
                        ['integracao.php',       'icon-link',       '#8e44ad', 'Integração'],
                        ['senha.php',            'icon-lock',       '#e67e22', 'Alterar Senha'],
                    ];
                    foreach ($atalhos as [$href, $icon, $cor, $label]):
                    ?>
                    <a href="<?= $href ?>" style="display:flex; align-items:center; gap:10px; padding:9px 6px; border-radius:8px; text-decoration:none; color:#1a2340; transition:background .15s; margin-bottom:2px;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                        <span style="width:32px;height:32px;border-radius:8px;background:<?= $cor ?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="feather <?= $icon ?>" style="color:<?= $cor ?>;font-size:14px;"></i>
                        </span>
                        <span style="font-size:13px; font-weight:600;"><?= $label ?></span>
                        <i class="feather icon-chevron-right" style="color:#ccc;font-size:13px;margin-left:auto;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>
