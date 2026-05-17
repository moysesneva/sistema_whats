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
    $nome       = Priletra($r['nome']);
    $img_perfil = $r['perfil_img'];
    $autorizado = $r['autorizado'];
    $tipo       = $r['tipo'];
}

// Busca id do profissional
$id_profissional = 0;
$stmt_bp = $conn->prepare("SELECT id FROM profissional WHERE telefone = ?");
$stmt_bp->bind_param("s", $login);
$stmt_bp->execute();
$r_bp = $stmt_bp->get_result()->fetch_assoc();
$stmt_bp->close();
if ($r_bp) $id_profissional = (int)$r_bp['id'];

include 'menu.php';

if ($q_u->num_rows < 1) VaiPara('login.php');
if ($autorizado != 2)   VaiPara('desbloquar.php');

include 'bloqueio.php';

// Verifica mês/ano via GET
$mes = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('n');
$ano = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');

if ($mes < 1)  { $mes = 12; $ano--; }
elseif ($mes > 12) { $mes = 1; $ano++; }

// Busca agendamentos do profissional (para marcar os dias)
$eventos = [];
if ($id_profissional > 0) {
    $stmt_ag = $conn->prepare("SELECT data, horario, profissional_cargo FROM agendamento WHERE id_profissional = ? ORDER BY horario ASC");
    $stmt_ag->bind_param("i", $id_profissional);
    $stmt_ag->execute();
    $res_ag = $stmt_ag->get_result();
    $stmt_ag->close();

    while ($row = $res_ag->fetch_assoc()) {
        $d_key = date('Y-m-d', strtotime($row['data']));
        $eventos[$d_key][] = $row['horario'] . ($row['profissional_cargo'] ? ' - ' . $row['profissional_cargo'] : '');
    }
}

// Lógica do calendário
date_default_timezone_set('America/Sao_Paulo');
$qtdDias          = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$primeiroDiaSemana = (int)date('w', strtotime("$ano-$mes-01"));
$hoje             = date('Y-m-d');

$meses_pt = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
             7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
$nome_mes = $meses_pt[$mes] ?? '';

// CSS do calendário
$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
/* ── Wrapper ── */
.cal-wrap { max-width: 920px; margin: 0 auto; }

/* ── Cabeçalho ── */
.cal-header { background: linear-gradient(135deg,#001f3f 60%,#003a70); color: #fff; border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.cal-header h4  { margin: 0; font-size: 22px; font-weight: 800; }
.cal-header small { opacity: .7; font-size: 12px; }
.cal-nav    { display: flex; gap: 8px; align-items: center; }
.cal-nav a  { display: flex; align-items: center; gap: 4px; color: rgba(255,255,255,.85); text-decoration: none; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 7px; border: 1.5px solid rgba(255,255,255,.25); transition: background .15s; }
.cal-nav a:hover { background: rgba(255,255,255,.12); color: #fff; }
.cal-nav a.hoje-btn { background: #FF5500; border-color: #FF5500; color: #fff; }

/* ── Grade ── */
.cal-grid { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
.cal-weekdays { display: grid; grid-template-columns: repeat(7,1fr); background: #f4f6fb; }
.cal-weekday  { text-align: center; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .05em; padding: 10px 0; }
.cal-days     { display: grid; grid-template-columns: repeat(7,1fr); gap: 0; }

/* ── Célula ── */
.cal-cell     { min-height: 96px; padding: 7px 6px; border-right: 1px solid #f0f2f7; border-bottom: 1px solid #f0f2f7; position: relative; overflow: hidden; transition: background .12s; }
.cal-cell:nth-child(7n) { border-right: none; }
.cal-cell:hover:not(.cal-empty) { background: #fafbff; }
.cal-empty    { background: #fafcff; }
.cal-today    { background: #eef4ff; }
.cal-num      { font-size: 13px; font-weight: 700; color: #334; margin-bottom: 4px; }
.cal-today .cal-num { color: #001f3f; background: #001f3f; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; }

/* ── Eventos na célula ── */
.cal-events { display: flex; flex-direction: column; gap: 2px; }
.cal-event  { background: #FF5500; color: #fff; border-radius: 4px; font-size: 9.5px; padding: 2px 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; line-height: 1.4; }
.cal-more   { font-size: 10px; color: #FF5500; font-weight: 700; padding-left: 3px; }

/* ── Link invisível sobre a célula ── */
.cal-link   { position: absolute; inset: 0; z-index: 1; }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">
<div class="cal-wrap">

    <!-- Cabeçalho do calendário -->
    <div class="cal-header">
        <div>
            <h4><i class="feather icon-calendar"></i> <?= $nome_mes . ' ' . $ano ?></h4>
            <small>Clique em qualquer dia para ver os agendamentos detalhados</small>
        </div>
        <div class="cal-nav">
            <a href="?m=<?= $mes - 1 ?>&y=<?= $ano ?>"><i class="feather icon-chevron-left"></i> Anterior</a>
            <a href="?m=<?= (int)date('n') ?>&y=<?= (int)date('Y') ?>" class="hoje-btn">Hoje</a>
            <a href="?m=<?= $mes + 1 ?>&y=<?= $ano ?>">Próximo <i class="feather icon-chevron-right"></i></a>
        </div>
    </div>

    <!-- Grade -->
    <div class="cal-grid">
        <!-- Dias da semana -->
        <div class="cal-weekdays">
            <?php foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d): ?>
            <div class="cal-weekday"><?= $d ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Dias do mês -->
        <div class="cal-days">
            <?php
            $dia = 1;
            for ($i = 0; $i < 42; $i++):
                if ($i < $primeiroDiaSemana || $dia > $qtdDias):
                    echo "<div class='cal-cell cal-empty'></div>";
                    if ($i >= $primeiroDiaSemana) $dia++;
                    continue;
                endif;

                $dataAtual = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                $isToday   = ($dataAtual === $hoje);
                $evts      = $eventos[$dataAtual] ?? [];
                $total_ev  = count($evts);
                $extras    = max(0, $total_ev - 3);
            ?>
            <div class="cal-cell <?= $isToday ? 'cal-today' : '' ?>">
                <div class="cal-num"><?= $dia ?></div>
                <?php if ($total_ev > 0): ?>
                <div class="cal-events">
                    <?php foreach (array_slice($evts, 0, 3) as $ev): ?>
                    <div class="cal-event" title="<?= htmlspecialchars($ev, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ev, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach; ?>
                    <?php if ($extras > 0): ?>
                    <div class="cal-more">+<?= $extras ?> mais</div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <a href="calendario2_prof.php?data=<?= $dataAtual ?>" class="cal-link" title="Ver agendamentos de <?= date('d/m/Y', strtotime($dataAtual)) ?>"></a>
            </div>
            <?php $dia++; endfor; ?>
        </div>
    </div>

    <?php if ($id_profissional <= 0): ?>
    <div class="alert alert-warning mt-3" style="border-radius:10px;">
        <i class="feather icon-alert-circle"></i>
        <strong>Perfil de profissional nao localizado.</strong>
        Fale com o administrador para ser vinculado ao sistema.
    </div>
    <?php endif; ?>

</div>
</div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cal-cell:not(.cal-empty)').forEach(function (cell, i) {
        cell.style.opacity = '0';
        cell.style.transform = 'translateY(8px)';
        setTimeout(function () {
            cell.style.transition = 'opacity .25s ease, transform .25s ease';
            cell.style.opacity = '1';
            cell.style.transform = 'translateY(0)';
        }, i * 18);
    });
});
</script>

<?php include 'footer.php'; ?>
