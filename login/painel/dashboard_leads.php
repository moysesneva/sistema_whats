<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
if (!isset($_SESSION['login'])) VaiPara('login.php');
$login = $_SESSION['login'];
include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$res_u  = $stmt_u->get_result();
$stmt_u->close();
if (!$res_u || $res_u->num_rows === 0) VaiPara('login.php');
$udata       = $res_u->fetch_assoc();
$tipo        = $udata['tipo'];
$autorizado  = $udata['autorizado'];
$usuario_api = $udata['usuario_api'];

include 'menu.php';
if ($autorizado != 2) VaiPara('desbloquar.php');
if ($tipo != 1) VaiPara('index.php');

$titulo = 'Dashboard de Leads';

// ── Período do filtro ────────────────────────────────────────────────────────
$periodo = $_GET['periodo'] ?? 'hoje';
switch ($periodo) {
    case '7dias':  $data_ini = date('Y-m-d', strtotime('-7 days')); break;
    case '30dias': $data_ini = date('Y-m-d', strtotime('-30 days')); break;
    case 'mes':    $data_ini = date('Y-m-01'); break;
    default:       $data_ini = date('Y-m-d'); $periodo = 'hoje';
}
$data_fim = date('Y-m-d');

// ── Helpers ──────────────────────────────────────────────────────────────────
function q($conn, $sql, $types = '', ...$params) {
    if ($types) {
        $s = $conn->prepare($sql);
        $s->bind_param($types, ...$params);
        $s->execute();
        $r = $s->get_result();
        $s->close();
        return $r;
    }
    return $conn->query($sql);
}

// ── KPIs principais ──────────────────────────────────────────────────────────
// Conversas iniciadas no período (qualquer cliente com time_atendimento no período)
$r = q($conn,
    "SELECT COUNT(*) AS n FROM clientes WHERE usuario_api=? AND DATE(time_atendimento) BETWEEN ? AND ?",
    'sss', $usuario_api, $data_ini, $data_fim);
$total_conversas = $r->fetch_assoc()['n'] ?? 0;

// Atualmente na fila
$r = q($conn,
    "SELECT COUNT(*) AS n FROM clientes WHERE usuario_api=? AND modo_atendimento='fila'",
    's', $usuario_api);
$em_fila = $r->fetch_assoc()['n'] ?? 0;

// Em atendimento humano agora
$r = q($conn,
    "SELECT COUNT(*) AS n FROM clientes WHERE usuario_api=? AND modo_atendimento='humano'",
    's', $usuario_api);
$em_atendimento = $r->fetch_assoc()['n'] ?? 0;

// Com IA agora
$r = q($conn,
    "SELECT COUNT(*) AS n FROM clientes WHERE usuario_api=? AND modo_atendimento='ia'",
    's', $usuario_api);
$com_ia = $r->fetch_assoc()['n'] ?? 0;

// Mensagens trocadas no período
$r = q($conn,
    "SELECT COUNT(*) AS n FROM ia_historico WHERE usuario_api=? AND DATE(data_hora) BETWEEN ? AND ?",
    'sss', $usuario_api, $data_ini, $data_fim);
$total_msgs = $r->fetch_assoc()['n'] ?? 0;

// ── Leads por departamento (todos os tempos, agrupado) ───────────────────────
$r_depto = q($conn,
    "SELECT d.nome AS depto, COUNT(c.id) AS total,
            SUM(c.modo_atendimento='fila') AS em_fila,
            SUM(c.modo_atendimento='humano') AS em_atend
     FROM clientes c
     JOIN departamentos d ON d.id = c.depto_atual
     WHERE c.usuario_api=?
     GROUP BY d.id, d.nome
     ORDER BY total DESC",
    's', $usuario_api);

// ── Atendimentos por atendente no período ────────────────────────────────────
$r_atend = q($conn,
    "SELECT atendente_atual AS atendente, COUNT(*) AS total,
            SUM(modo_atendimento='humano') AS ativos
     FROM clientes
     WHERE usuario_api=? AND atendente_atual IS NOT NULL
       AND DATE(time_atendimento) BETWEEN ? AND ?
     GROUP BY atendente_atual
     ORDER BY total DESC",
    'sss', $usuario_api, $data_ini, $data_fim);

// ── Conversas por dia (últimos 7 dias sempre, para o gráfico) ────────────────
$r_diario = q($conn,
    "SELECT DATE(time_atendimento) AS dia, COUNT(*) AS n
     FROM clientes WHERE usuario_api=? AND DATE(time_atendimento) >= ?
     GROUP BY DATE(time_atendimento) ORDER BY dia ASC",
    'ss', $usuario_api, date('Y-m-d', strtotime('-6 days')));
$dias_labels = [];
$dias_data   = [];
while ($row = $r_diario->fetch_assoc()) {
    $dias_labels[] = date('d/m', strtotime($row['dia']));
    $dias_data[]   = (int)$row['n'];
}

// ── Conversas por departamento para gráfico pizza ────────────────────────────
$r_pizza = q($conn,
    "SELECT d.nome AS depto, COUNT(c.id) AS n
     FROM clientes c JOIN departamentos d ON d.id=c.depto_atual
     WHERE c.usuario_api=? GROUP BY d.nome ORDER BY n DESC",
    's', $usuario_api);
$pizza_labels = [];
$pizza_data   = [];
while ($row = $r_pizza->fetch_assoc()) {
    $pizza_labels[] = $row['depto'];
    $pizza_data[]   = (int)$row['n'];
}
?>
<?php include 'header.php'; ?>
<style>
.dash-kpi { border-radius: 12px; padding: 22px 18px; color: #fff; margin-bottom: 18px; box-shadow: 0 2px 8px rgba(0,0,0,.12); }
.dash-kpi .kpi-val { font-size: 2.4rem; font-weight: 700; line-height: 1; }
.dash-kpi .kpi-lbl { font-size: .82rem; opacity: .88; margin-top: 4px; }
.kpi-azul   { background: #001f3f; }
.kpi-laranj { background: #FF5500; }
.kpi-verde  { background: #28a745; }
.kpi-cinza  { background: #6c757d; }
.kpi-info   { background: #17a2b8; }
.card-dash  { border-radius: 10px; box-shadow: 0 1px 6px rgba(0,0,0,.09); margin-bottom: 22px; }
.badge-fila   { background:#FF5500; color:#fff; }
.badge-humano { background:#28a745; color:#fff; }
.badge-ia     { background:#001f3f; color:#fff; }
.periodo-btn { margin-right:4px; }
.table-dash th { background:#001f3f; color:#fff; }
</style>

<div class="content-page">
<div class="content">
<div class="container-fluid">

    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <h4 style="color:#001f3f;font-weight:700;"><i class="fa fa-bar-chart mr-2" style="color:#FF5500"></i> Dashboard de Leads</h4>
        </div>
        <div class="col-sm-6 text-right">
            <a href="?periodo=hoje"   class="btn btn-sm periodo-btn <?=$periodo=='hoje'   ?'btn-primary':'btn-outline-primary'?>">Hoje</a>
            <a href="?periodo=7dias"  class="btn btn-sm periodo-btn <?=$periodo=='7dias'  ?'btn-primary':'btn-outline-primary'?>">7 dias</a>
            <a href="?periodo=mes"    class="btn btn-sm periodo-btn <?=$periodo=='mes'     ?'btn-primary':'btn-outline-primary'?>">Este mês</a>
            <a href="?periodo=30dias" class="btn btn-sm periodo-btn <?=$periodo=='30dias' ?'btn-primary':'btn-outline-primary'?>">30 dias</a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row">
        <div class="col-6 col-md-2">
            <div class="dash-kpi kpi-azul">
                <div class="kpi-val"><?=$total_conversas?></div>
                <div class="kpi-lbl">Conversas no período</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="dash-kpi kpi-laranj">
                <div class="kpi-val"><?=$em_fila?></div>
                <div class="kpi-lbl">Na fila agora</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="dash-kpi kpi-verde">
                <div class="kpi-val"><?=$em_atendimento?></div>
                <div class="kpi-lbl">Em atendimento</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="dash-kpi kpi-info">
                <div class="kpi-val"><?=$com_ia?></div>
                <div class="kpi-lbl">Com a IA</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="dash-kpi kpi-cinza">
                <div class="kpi-val"><?=$total_msgs?></div>
                <div class="kpi-lbl">Mensagens trocadas no período</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-dash">
                <div class="card-body">
                    <h6 style="color:#001f3f;font-weight:700;"><i class="fa fa-line-chart mr-1" style="color:#FF5500"></i> Conversas por dia (últimos 7 dias)</h6>
                    <canvas id="chartDiario" height="90"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dash">
                <div class="card-body">
                    <h6 style="color:#001f3f;font-weight:700;"><i class="fa fa-pie-chart mr-1" style="color:#FF5500"></i> Por departamento</h6>
                    <canvas id="chartPizza" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas -->
    <div class="row">
        <!-- Por departamento -->
        <div class="col-md-6">
            <div class="card card-dash">
                <div class="card-body">
                    <h6 style="color:#001f3f;font-weight:700;"><i class="feather icon-users mr-1" style="color:#FF5500"></i> Leads por Departamento</h6>
                    <table class="table table-sm table-hover table-dash">
                        <thead><tr><th>Departamento</th><th class="text-center">Total</th><th class="text-center">Na Fila</th><th class="text-center">Em Atend.</th></tr></thead>
                        <tbody>
                        <?php
                        $r_depto->data_seek(0);
                        while ($row = $r_depto->fetch_assoc()):
                            $depto_curto = str_replace(['— CNA Itanhangá','— CNA Vivendas'], ['(Itan.)','(Viv.)'], $row['depto']);
                        ?>
                        <tr>
                            <td><?=htmlspecialchars($depto_curto)?></td>
                            <td class="text-center"><strong><?=$row['total']?></strong></td>
                            <td class="text-center"><span class="badge badge-fila"><?=$row['em_fila']?></span></td>
                            <td class="text-center"><span class="badge badge-humano"><?=$row['em_atend']?></span></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (!$r_depto->num_rows): ?>
                        <tr><td colspan="4" class="text-center text-muted">Nenhum lead com departamento registrado</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Por atendente -->
        <div class="col-md-6">
            <div class="card card-dash">
                <div class="card-body">
                    <h6 style="color:#001f3f;font-weight:700;"><i class="feather icon-user mr-1" style="color:#FF5500"></i> Atendimentos por Atendente <small class="text-muted">(período)</small></h6>
                    <table class="table table-sm table-hover table-dash">
                        <thead><tr><th>Atendente</th><th class="text-center">Leads</th><th class="text-center">Em Atend. agora</th></tr></thead>
                        <tbody>
                        <?php
                        $tem_atendente = false;
                        while ($row = $r_atend->fetch_assoc()):
                            $tem_atendente = true;
                        ?>
                        <tr>
                            <td><?=htmlspecialchars(ucfirst($row['atendente']))?></td>
                            <td class="text-center"><strong><?=$row['total']?></strong></td>
                            <td class="text-center">
                                <?php if ($row['ativos'] > 0): ?>
                                <span class="badge badge-humano"><?=$row['ativos']?></span>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (!$tem_atendente): ?>
                        <tr><td colspan="3" class="text-center text-muted">Nenhum atendimento no período</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila ao vivo -->
    <div class="card card-dash">
        <div class="card-body">
            <h6 style="color:#001f3f;font-weight:700;"><i class="feather icon-clock mr-1" style="color:#FF5500"></i> Fila Atual — aguardando atendimento</h6>
            <?php
            $r_fila = q($conn,
                "SELECT c.nome, c.telefone, c.time_atendimento, c.atendente_atual,
                        d.nome AS depto
                 FROM clientes c
                 LEFT JOIN departamentos d ON d.id = c.depto_atual
                 WHERE c.usuario_api=? AND c.modo_atendimento='fila'
                 ORDER BY c.time_atendimento ASC",
                's', $usuario_api);
            if ($r_fila->num_rows === 0):
            ?>
            <p class="text-muted mb-0"><i class="feather icon-check-circle text-success mr-1"></i> Fila vazia — nenhum cliente aguardando.</p>
            <?php else: ?>
            <table class="table table-sm table-hover table-dash mb-0">
                <thead><tr><th>Cliente</th><th>Telefone</th><th>Departamento</th><th>Atendente pré-atribuído</th><th>Aguardando desde</th></tr></thead>
                <tbody>
                <?php while ($row = $r_fila->fetch_assoc()):
                    $nome_cli = $row['nome'] ?: $row['telefone'];
                    $depto_curto = str_replace(['— CNA Itanhangá','— CNA Vivendas'], ['(Itan.)','(Viv.)'], $row['depto'] ?? '—');
                    $espera = $row['time_atendimento'] ? human_time_diff($row['time_atendimento']) : '—';
                ?>
                <tr>
                    <td><?=htmlspecialchars($nome_cli)?></td>
                    <td><?=htmlspecialchars($row['telefone'])?></td>
                    <td><?=htmlspecialchars($depto_curto)?></td>
                    <td><?=$row['atendente_atual'] ? '<span class="badge badge-humano">'.htmlspecialchars(ucfirst($row['atendente_atual'])).'</span>' : '<span class="text-muted">—</span>'?></td>
                    <td><?=$espera?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
</div>

<?php
function human_time_diff($time_str) {
    $diff = time() - strtotime($time_str);
    if ($diff < 60) return $diff.'s';
    if ($diff < 3600) return floor($diff/60).'min';
    if ($diff < 86400) return floor($diff/3600).'h '.floor(($diff%3600)/60).'min';
    return floor($diff/86400).'d';
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script nonce="<?=htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8')?>">
const azul   = '#001f3f';
const laranj = '#FF5500';

// Gráfico de linhas — conversas por dia
new Chart(document.getElementById('chartDiario'), {
    type: 'bar',
    data: {
        labels: <?=json_encode($dias_labels)?>,
        datasets: [{
            label: 'Conversas',
            data:  <?=json_encode($dias_data)?>,
            backgroundColor: laranj + 'cc',
            borderColor: laranj,
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Pizza — por departamento
const pizzaLabels = <?=json_encode($pizza_labels)?>;
const pizzaData   = <?=json_encode($pizza_data)?>;
const paleta = ['#001f3f','#FF5500','#17a2b8','#28a745','#ffc107','#6c757d','#dc3545','#6f42c1'];
new Chart(document.getElementById('chartPizza'), {
    type: 'doughnut',
    data: {
        labels: pizzaLabels,
        datasets: [{
            data: pizzaData,
            backgroundColor: paleta.slice(0, pizzaLabels.length),
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } }
        }
    }
});

// Auto-refresh a cada 60s
setTimeout(() => location.reload(), 60000);
</script>
