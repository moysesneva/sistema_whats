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

include 'menu.php';

if ($q_u->num_rows < 1) VaiPara('login.php');
if ($autorizado != 2)   VaiPara('desbloquar.php');

include 'bloqueio.php';

// Data selecionada — sanitizada
$data_selecionada = '';
if (isset($_GET['data'])) {
    $d = DateTime::createFromFormat('Y-m-d', $_GET['data']);
    if ($d && $d->format('Y-m-d') === $_GET['data']) {
        $data_selecionada = $_GET['data'];
    }
}
if (!$data_selecionada) $data_selecionada = date('Y-m-d');

// Busca id do profissional
$id_profissional = 0;
$stmt_bp = $conn->prepare("SELECT id FROM profissional WHERE telefone = ?");
$stmt_bp->bind_param("s", $login);
$stmt_bp->execute();
$r_bp = $stmt_bp->get_result()->fetch_assoc();
$stmt_bp->close();
if ($r_bp) $id_profissional = (int)$r_bp['id'];

// Busca agendamentos do dia
$agendamentos = [];
if ($id_profissional > 0) {
    $stmt_ag = $conn->prepare("SELECT * FROM agendamento WHERE id_profissional = ? AND data = ? ORDER BY horario ASC");
    $stmt_ag->bind_param("is", $id_profissional, $data_selecionada);
    $stmt_ag->execute();
    $res_ag = $stmt_ag->get_result();
    $stmt_ag->close();
    while ($row = $res_ag->fetch_assoc()) {
        $agendamentos[] = $row;
    }
}

$total_ag = count($agendamentos);

$dia_semana = (int)date('w', strtotime($data_selecionada));
$nomes_dias = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
$data_fmt   = $nomes_dias[$dia_semana] . ', ' . date('d/m/Y', strtotime($data_selecionada));

// Navegação de dia
$dia_ant = date('Y-m-d', strtotime($data_selecionada . ' -1 day'));
$dia_prox = date('Y-m-d', strtotime($data_selecionada . ' +1 day'));
$mes_atual = date('n', strtotime($data_selecionada));
$ano_atual = date('Y', strtotime($data_selecionada));

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
/* ── Cabeçalho do dia ── */
.dia-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
.dia-titulo { font-size: 20px; font-weight: 800; color: #001f3f; }
.dia-sub    { font-size: 13px; color: #888; margin-top: 2px; }
.dia-nav    { display: flex; gap: 8px; align-items: center; }
.dia-nav a  { display: flex; align-items: center; gap: 4px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1.5px solid #e0e4ef; color: #334; transition: background .15s; }
.dia-nav a:hover { background: #f4f6fb; }
.dia-nav a.hoje { background: #001f3f; color: #fff; border-color: #001f3f; }

/* ── Filtro de data ── */
.filtro-data { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e0e4ef; border-radius: 9px; padding: 6px 12px; }
.filtro-data input { border: none; outline: none; font-size: 13px; color: #334; cursor: pointer; }
.filtro-data button { background: #001f3f; color: #fff; border: none; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; }

/* ── Card de agendamento ── */
.ag-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 14px; overflow: hidden; border-left: 4px solid #001f3f; transition: box-shadow .18s; }
.ag-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.ag-card-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px 10px; gap: 12px; }
.ag-hora         { background: #001f3f; color: #fff; border-radius: 8px; padding: 6px 12px; font-size: 15px; font-weight: 800; flex-shrink: 0; }
.ag-cliente      { font-size: 15px; font-weight: 700; color: #1a2340; }
.ag-servico      { font-size: 12px; color: #888; margin-top: 1px; }
.ag-card-body    { padding: 10px 18px 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border-top: 1px solid #f0f2f7; }
.ag-acoes        { display: flex; gap: 8px; flex-wrap: wrap; }
.ag-acoes a      { font-size: 12px; padding: 6px 12px; border-radius: 7px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; }
.btn-wa-ag  { background: #d4f5e9; color: #1a7a4a; }
.btn-wa-ag:hover { background: #b8ecd5; color: #1a7a4a; }
.btn-cancel-ag { background: #fdecea; color: #c0392b; }
.btn-cancel-ag:hover { background: #f9d6d3; color: #c0392b; }

/* ── Badge status ── */
.status-badge { border-radius: 20px; font-size: 11px; font-weight: 700; padding: 3px 10px; display: inline-flex; align-items: center; gap: 4px; }
.status-conf    { background: #d4f5e9; color: #1a7a4a; }
.status-pend    { background: #fff3cd; color: #856404; }
.status-cancel  { background: #fdecea; color: #c0392b; }

/* ── Estado vazio ── */
.ag-vazio { text-align: center; padding: 60px 20px; color: #aaa; }
.ag-vazio i { font-size: 52px; display: block; margin-bottom: 14px; opacity: .4; }
.ag-vazio h5 { color: #888; margin-bottom: 6px; }

/* ── Contador ── */
.contador { background: #FF5500; color: #fff; border-radius: 20px; font-size: 12px; font-weight: 700; padding: 2px 10px; margin-left: 8px; }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">

    <!-- Cabeçalho do dia -->
    <div class="dia-header">
        <div>
            <div class="dia-titulo">
                <i class="feather icon-calendar" style="color:#FF5500;"></i>
                <?= htmlspecialchars($data_fmt, ENT_QUOTES, 'UTF-8') ?>
                <span class="contador"><?= $total_ag ?></span>
            </div>
            <div class="dia-sub">Agendamentos do dia — clique nos botões para navegar</div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <!-- Filtro de data -->
            <form method="GET" style="display:flex;">
                <div class="filtro-data">
                    <i class="feather icon-calendar" style="color:#888; font-size:14px;"></i>
                    <input type="date" name="data" value="<?= htmlspecialchars($data_selecionada, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit"><i class="feather icon-search"></i></button>
                </div>
            </form>
            <!-- Navegação de dia -->
            <div class="dia-nav">
                <a href="?data=<?= $dia_ant ?>"><i class="feather icon-chevron-left"></i> Anterior</a>
                <a href="?data=<?= date('Y-m-d') ?>" class="hoje"><i class="feather icon-home"></i> Hoje</a>
                <a href="?data=<?= $dia_prox ?>">Próximo <i class="feather icon-chevron-right"></i></a>
            </div>
            <a href="agenda2_porf.php?m=<?= $mes_atual ?>&y=<?= $ano_atual ?>" class="btn btn-sm btn-outline-primary">
                <i class="feather icon-grid"></i> Ver Calendário
            </a>
        </div>
    </div>

    <!-- Lista de agendamentos -->
    <?php if ($id_profissional <= 0): ?>
    <div class="ag-vazio">
        <i class="feather icon-alert-circle"></i>
        <h5>Perfil não localizado</h5>
        <p>Seu cadastro como profissional não foi encontrado. Fale com o administrador.</p>
    </div>
    <?php elseif (empty($agendamentos)): ?>
    <div class="ag-vazio">
        <i class="feather icon-calendar"></i>
        <h5>Nenhum agendamento para este dia</h5>
        <p>Use os botões acima para navegar para outro dia.</p>
        <a href="agenda2_porf.php?m=<?= $mes_atual ?>&y=<?= $ano_atual ?>" class="btn btn-sm btn-primary mt-2">
            <i class="feather icon-grid"></i> Ver Calendário do Mês
        </a>
    </div>
    <?php else: ?>
    <?php foreach ($agendamentos as $row):
        $tel_clean = preg_replace('/\D/', '', $row['cliente_telefone'] ?? '');
        $wa_link   = 'https://wa.me/' . (str_starts_with($tel_clean, '55') ? $tel_clean : '55' . $tel_clean);
        $conf      = (int)$row['confirmacao'];

        if ($conf == 1) {
            $status_class = 'status-conf';
            $status_txt   = 'Confirmado';
            $status_icon  = 'icon-check-circle';
            $border_cor   = '#27ae60';
        } elseif ($conf == 2) {
            $status_class = 'status-cancel';
            $status_txt   = 'Cancelado';
            $status_icon  = 'icon-x-circle';
            $border_cor   = '#e74c3c';
        } else {
            $status_class = 'status-pend';
            $status_txt   = 'Pendente';
            $status_icon  = 'icon-clock';
            $border_cor   = '#e67e22';
        }
    ?>
    <div class="ag-card" style="border-left-color:<?= $border_cor ?>;">
        <div class="ag-card-header">
            <span class="ag-hora" style="background:<?= $border_cor ?>;"><i class="feather icon-clock" style="font-size:12px;"></i> <?= htmlspecialchars($row['horario'], ENT_QUOTES, 'UTF-8') ?></span>
            <div style="flex:1; min-width:0;">
                <div class="ag-cliente"><?= htmlspecialchars($row['cliente_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="ag-servico"><?= htmlspecialchars($row['profissional_cargo'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <span class="status-badge <?= $status_class ?>">
                <i class="feather <?= $status_icon ?>" style="font-size:11px;"></i> <?= $status_txt ?>
            </span>
        </div>
        <div class="ag-card-body">
            <div style="font-size:12px; color:#888;">
                <i class="feather icon-phone" style="font-size:11px;"></i> <?= htmlspecialchars($row['cliente_telefone'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($row['duracao_minutos'])): ?>
                &nbsp;· <i class="feather icon-clock" style="font-size:11px;"></i> <?= (int)$row['duracao_minutos'] ?> min
                <?php endif; ?>
                <?php if (!empty($row['valor_servico']) && $row['valor_servico'] > 0): ?>
                &nbsp;· <i class="feather icon-dollar-sign" style="font-size:11px;"></i> R$ <?= number_format((float)$row['valor_servico'], 2, ',', '.') ?>
                <?php endif; ?>
            </div>
            <div class="ag-acoes">
                <?php if ($tel_clean): ?>
                <a href="<?= $wa_link ?>" target="_blank" class="btn-wa-ag">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
                <?php endif; ?>
                <?php if ($conf != 2): ?>
                <a href="cancelar_agendamento.php?id=<?= (int)$row['id'] ?>" class="btn-cancel-ag" data-fn="__confirm" data-args='["Tem certeza que deseja cancelar este agendamento?"]'>
                    <i class="feather icon-x"></i> Cancelar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
