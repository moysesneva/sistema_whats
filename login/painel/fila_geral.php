<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) { VaiPara('login.php'); }
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$res_u = $stmt_u->get_result();
$stmt_u->close();
if (!$res_u || $res_u->num_rows === 0) VaiPara('login.php');
$udata = $res_u->fetch_assoc();
$tipo        = $udata['tipo'];
$autorizado  = $udata['autorizado'];
$nome_user   = Priletra($udata['nome']);
$img_perfil  = $udata['perfil_img'];
$usuario_api = $udata['usuario_api'];

include 'menu.php';

if ($autorizado != 2) VaiPara('desbloquar.php');
if ($tipo != 1) VaiPara('index.php');

// ── Ações admin ───────────────────────────────────────────────────────────────
$msg_ok  = '';
$msg_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_admin'])) {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    if ($cliente_id > 0) {
        if ($_POST['acao_admin'] === 'devolver_ia') {
            $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='ia', atendente_atual=NULL, depto_atual=NULL WHERE id=? AND usuario_api=?");
            $stmt_up->bind_param("is", $cliente_id, $usuario_api);
            $stmt_up->execute();
            $stmt_up->close();
            $msg_ok = 'Conversa devolvida para a IA.';
        }
        if ($_POST['acao_admin'] === 'assumir_forca') {
            $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='humano', atendente_atual=? WHERE id=? AND usuario_api=?");
            $stmt_up->bind_param("sis", $login, $cliente_id, $usuario_api);
            $stmt_up->execute();
            $stmt_up->close();
            $msg_ok = 'Conversa assumida pelo admin.';
        }
        if ($_POST['acao_admin'] === 'transferir_depto') {
            $novo_depto = (int)($_POST['novo_depto'] ?? 0);
            if ($novo_depto > 0) {
                // Verificar que o departamento pertence ao mesmo tenant
                $s_vd = $conn->prepare("SELECT id FROM departamentos WHERE id=? AND usuario_api=? AND ativo=1");
                $s_vd->bind_param("is", $novo_depto, $usuario_api);
                $s_vd->execute();
                $r_vd = $s_vd->get_result();
                $s_vd->close();
                if ($r_vd && $r_vd->num_rows > 0) {
                    $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='fila', depto_atual=?, atendente_atual=NULL WHERE id=? AND usuario_api=?");
                    $stmt_up->bind_param("iis", $novo_depto, $cliente_id, $usuario_api);
                    $stmt_up->execute();
                    $stmt_up->close();
                    $msg_ok = 'Cliente transferido para outro departamento.';
                } else {
                    $msg_err = 'Departamento de destino inválido.';
                }
            } else {
                $msg_err = 'Selecione um departamento de destino.';
            }
        }
    }
}

// ── Ação: forçar transferência de conversa em modo IA ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_admin']) && $_POST['acao_admin'] === 'forca_ia_depto') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $novo_depto = (int)($_POST['novo_depto'] ?? 0);
    if ($cliente_id > 0 && $novo_depto > 0) {
        $s_vd2 = $conn->prepare("SELECT id FROM departamentos WHERE id=? AND usuario_api=? AND ativo=1");
        $s_vd2->bind_param("is", $novo_depto, $usuario_api);
        $s_vd2->execute();
        $r_vd2 = $s_vd2->get_result();
        $s_vd2->close();
        if ($r_vd2 && $r_vd2->num_rows > 0) {
            $s_fi = $conn->prepare("UPDATE clientes SET modo_atendimento='fila', depto_atual=?, atendente_atual=NULL WHERE id=? AND usuario_api=? AND modo_atendimento='ia'");
            $s_fi->bind_param("iis", $novo_depto, $cliente_id, $usuario_api);
            $s_fi->execute();
            $aff = $s_fi->affected_rows;
            $s_fi->close();
            $msg_ok = $aff > 0 ? 'Conversa transferida para a fila do departamento.' : 'Nenhuma alteração (conversa pode não estar mais em modo IA).';
        } else {
            $msg_err = 'Departamento de destino inválido.';
        }
    }
}

// ── Dados ─────────────────────────────────────────────────────────────────────
// Departamentos
$stmt_dep = $conn->prepare("SELECT * FROM departamentos WHERE usuario_api=? AND ativo=1 ORDER BY nome");
$stmt_dep->bind_param("s", $usuario_api);
$stmt_dep->execute();
$res_dep = $stmt_dep->get_result();
$stmt_dep->close();
$deptos = [];
while ($r = $res_dep->fetch_assoc()) $deptos[] = $r;

// Clientes em fila/humano
$clientes_ativos = [];
$res_ca = $conn->prepare("SELECT c.*, d.nome AS depto_nome FROM clientes c LEFT JOIN departamentos d ON d.id=c.depto_atual WHERE c.usuario_api=? AND c.modo_atendimento IN ('fila','humano') ORDER BY c.modo_atendimento ASC, c.time_atendimento ASC");
$res_ca->bind_param("s", $usuario_api);
$res_ca->execute();
$res_ca2 = $res_ca->get_result();
$res_ca->close();
while ($r = $res_ca2->fetch_assoc()) $clientes_ativos[] = $r;

// Atendentes MultiAtendente
$stmt_at = $conn->prepare("SELECT login, nome FROM login WHERE usuario_api=? AND modo_atuante='MultiAtendente' ORDER BY nome");
$stmt_at->bind_param("s", $usuario_api);
$stmt_at->execute();
$res_at = $stmt_at->get_result();
$stmt_at->close();
$atendentes = [];
while ($r = $res_at->fetch_assoc()) $atendentes[$r['login']] = $r['nome'] ?: $r['login'];

// Métricas
$total_fila   = count(array_filter($clientes_ativos, fn($c) => $c['modo_atendimento'] === 'fila'));
$total_humano = count(array_filter($clientes_ativos, fn($c) => $c['modo_atendimento'] === 'humano'));
$total_deptos = count($deptos);
$total_atend  = count($atendentes);

// Busca clientes em modo IA para transferência forçada pelo admin
$busca_ia = trim($_GET['busca_ia'] ?? '');
$clientes_ia_result = [];
if ($busca_ia !== '') {
    $like = '%' . $busca_ia . '%';
    $s_ia = $conn->prepare("SELECT id, nome, telefone, time_atendimento FROM clientes WHERE usuario_api=? AND modo_atendimento='ia' AND (telefone LIKE ? OR nome LIKE ?) ORDER BY time_atendimento DESC LIMIT 20");
    $s_ia->bind_param("sss", $usuario_api, $like, $like);
    $s_ia->execute();
    $r_ia = $s_ia->get_result();
    $s_ia->close();
    while ($row = $r_ia->fetch_assoc()) $clientes_ia_result[] = $row;
}
?>
<?php include 'header.php'; ?>

<div class="content-page">
<div class="content">
<div class="container-fluid">

<div class="row m-t-20 mb-3">
  <div class="col-sm-12 d-flex justify-content-between align-items-center flex-wrap">
    <h4 class="page-title mb-0"><i class="feather icon-list mr-2"></i>Fila de Atendimento</h4>
    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()"><i class="feather icon-refresh-cw mr-1"></i>Atualizar</button>
  </div>
</div>

<?php if ($msg_ok): ?>
<div class="alert alert-success"><i class="feather icon-check-circle mr-2"></i><?= htmlspecialchars($msg_ok) ?></div>
<?php endif; ?>
<?php if ($msg_err): ?>
<div class="alert alert-danger"><i class="feather icon-alert-circle mr-2"></i><?= htmlspecialchars($msg_err) ?></div>
<?php endif; ?>

<!-- Métricas -->
<div class="row mb-4">
  <div class="col-6 col-md-3 mb-2">
    <div class="card text-center py-3" style="border-top:3px solid #ffc107;">
      <div style="font-size:2rem;font-weight:700;color:#ffc107;"><?= $total_fila ?></div>
      <div class="text-muted small">Na fila</div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card text-center py-3" style="border-top:3px solid #28a745;">
      <div style="font-size:2rem;font-weight:700;color:#28a745;"><?= $total_humano ?></div>
      <div class="text-muted small">Em atendimento</div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card text-center py-3" style="border-top:3px solid #001f3f;">
      <div style="font-size:2rem;font-weight:700;color:#001f3f;"><?= $total_deptos ?></div>
      <div class="text-muted small">Departamentos</div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card text-center py-3" style="border-top:3px solid #FF5500;">
      <div style="font-size:2rem;font-weight:700;color:#FF5500;"><?= $total_atend ?></div>
      <div class="text-muted small">Atendentes</div>
    </div>
  </div>
</div>

<!-- Transferir conversa em modo IA para departamento -->
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center" style="background:#6f42c1;color:#fff;">
    <h6 class="mb-0"><i class="feather icon-zap mr-2"></i>Transferir conversa da IA para um departamento</h6>
    <button class="btn btn-sm btn-light" type="button" data-toggle="collapse" data-target="#collapseIA" aria-expanded="false">
      <i class="feather icon-chevron-down"></i>
    </button>
  </div>
  <div class="collapse" id="collapseIA">
    <div class="card-body">
      <p class="text-muted small mb-3">Busque um cliente pelo telefone ou nome para forçar a transferência de uma conversa que ainda está sendo atendida pela IA.</p>
      <form method="get" class="form-inline mb-3">
        <input type="text" name="busca_ia" class="form-control form-control-sm mr-2" placeholder="Telefone ou nome do cliente"
          value="<?= htmlspecialchars($busca_ia) ?>" style="min-width:220px;">
        <button class="btn btn-sm" style="background:#6f42c1;color:#fff;" type="submit">
          <i class="feather icon-search mr-1"></i>Buscar
        </button>
      </form>
      <?php if ($busca_ia !== '' && empty($clientes_ia_result)): ?>
      <p class="text-muted small">Nenhum cliente em modo IA encontrado para "<strong><?= htmlspecialchars($busca_ia) ?></strong>".</p>
      <?php endif; ?>
      <?php if (!empty($clientes_ia_result)): ?>
      <table class="table table-sm mb-0">
        <thead><tr><th>Nome</th><th>Telefone</th><th>Último atendimento</th><th>Transferir para</th></tr></thead>
        <tbody>
          <?php foreach ($clientes_ia_result as $cli_ia): ?>
          <tr>
            <td><?= htmlspecialchars($cli_ia['nome'] ?: '—') ?></td>
            <td><?= htmlspecialchars($cli_ia['telefone']) ?></td>
            <td><small class="text-muted"><?= htmlspecialchars($cli_ia['time_atendimento'] ?? '') ?></small></td>
            <td>
              <form method="post" class="d-flex align-items-center" style="gap:4px;">
                <input type="hidden" name="acao_admin" value="forca_ia_depto">
                <input type="hidden" name="cliente_id" value="<?= $cli_ia['id'] ?>">
                <select name="novo_depto" class="form-control form-control-sm" style="max-width:160px;" required>
                  <option value="">Selecione...</option>
                  <?php foreach ($deptos as $d_opt): ?>
                  <option value="<?= $d_opt['id'] ?>"><?= htmlspecialchars($d_opt['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm" style="background:#6f42c1;color:#fff;" type="submit"
                  onclick="return confirm('Transferir esta conversa para a fila do departamento?')">
                  Transferir
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Atendentes e seus clientes -->
<?php if (!empty($deptos)): ?>
<div class="row">
  <?php foreach ($deptos as $dep):
    $clientes_dep = array_filter($clientes_ativos, fn($c) => (int)$c['depto_atual'] === (int)$dep['id']);
    $fila_dep   = array_filter($clientes_dep, fn($c) => $c['modo_atendimento'] === 'fila');
    $humano_dep = array_filter($clientes_dep, fn($c) => $c['modo_atendimento'] === 'humano');

    // Atendentes deste depto
    $stmt_adb = $conn->prepare("SELECT login_atendente FROM atendentes_depto WHERE depto_id=? AND usuario_api=?");
    $stmt_adb->bind_param("is", $dep['id'], $usuario_api);
    $stmt_adb->execute();
    $res_adb = $stmt_adb->get_result();
    $stmt_adb->close();
    $atend_depto = [];
    while ($r = $res_adb->fetch_assoc()) $atend_depto[] = $r['login_atendente'];
  ?>
  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center" style="background:#001f3f;color:#fff;">
        <h6 class="mb-0"><i class="feather icon-tag mr-2"></i><?= htmlspecialchars($dep['nome']) ?></h6>
        <div>
          <span class="badge" style="background:#ffc107;color:#000;">Fila: <?= count($fila_dep) ?></span>
          <span class="badge badge-success ml-1">Ativo: <?= count($humano_dep) ?></span>
        </div>
      </div>
      <div class="card-body p-0">

        <!-- Atendentes do depto -->
        <?php if (!empty($atend_depto)): ?>
        <div class="px-3 pt-2 pb-1">
          <small class="text-muted font-weight-bold">ATENDENTES:</small>
          <div class="mt-1">
            <?php foreach ($atend_depto as $atlog): ?>
            <span class="badge badge-light border mr-1 p-2">
              <i class="feather icon-user mr-1"></i><?= htmlspecialchars($atendentes[$atlog] ?? $atlog) ?>
            </span>
            <?php endforeach; ?>
          </div>
        </div>
        <hr class="my-1">
        <?php endif; ?>

        <!-- Clientes na fila -->
        <?php if (!empty($fila_dep)): ?>
        <div class="px-3 pt-1 pb-1">
          <small class="text-muted font-weight-bold">⏳ NA FILA:</small>
          <?php foreach ($fila_dep as $cl): ?>
          <div class="py-2 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="font-weight-600"><?= htmlspecialchars($cl['nome'] ?: $cl['telefone']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($cl['telefone']) ?></div>
              </div>
              <div style="display:flex;gap:4px;flex-wrap:wrap;justify-content:flex-end;">
                <form method="post" style="display:inline;">
                  <input type="hidden" name="acao_admin" value="assumir_forca">
                  <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
                  <button class="btn btn-sm btn-warning" title="Assumir como admin" onclick="return confirm('Assumir esta conversa?')">
                    <i class="feather icon-user-check"></i>
                  </button>
                </form>
                <button class="btn btn-sm btn-info" title="Transferir para outro depto"
                  onclick="toggleTransfer('tf_<?= $cl['id'] ?>')">
                  <i class="feather icon-shuffle"></i>
                </button>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="acao_admin" value="devolver_ia">
                  <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
                  <button class="btn btn-sm btn-outline-secondary" title="Devolver para IA" onclick="return confirm('Devolver para a IA?')">
                    <i class="feather icon-rotate-ccw"></i>
                  </button>
                </form>
              </div>
            </div>
            <!-- painel de transferência de depto -->
            <div id="tf_<?= $cl['id'] ?>" style="display:none;margin-top:6px;">
              <form method="post" class="d-flex align-items-center" style="gap:4px;">
                <input type="hidden" name="acao_admin" value="transferir_depto">
                <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
                <select name="novo_depto" class="form-control form-control-sm" style="max-width:180px;" required>
                  <option value="">Selecione o depto...</option>
                  <?php foreach ($deptos as $d_opt):
                    if ((int)$d_opt['id'] === (int)$dep['id']) continue; ?>
                  <option value="<?= $d_opt['id'] ?>"><?= htmlspecialchars($d_opt['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-info" type="submit" onclick="return confirm('Transferir para este departamento?')">
                  Transferir
                </button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Clientes em atendimento -->
        <?php if (!empty($humano_dep)): ?>
        <div class="px-3 pt-1 pb-2">
          <small class="text-muted font-weight-bold">🤝 EM ATENDIMENTO:</small>
          <?php foreach ($humano_dep as $cl): ?>
          <div class="py-2 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="font-weight-600"><?= htmlspecialchars($cl['nome'] ?: $cl['telefone']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($cl['telefone']) ?></div>
                <?php if ($cl['atendente_atual']): ?>
                <div><small class="badge badge-success"><?= htmlspecialchars($atendentes[$cl['atendente_atual']] ?? $cl['atendente_atual']) ?></small></div>
                <?php endif; ?>
              </div>
              <div style="display:flex;gap:4px;flex-wrap:wrap;justify-content:flex-end;">
                <button class="btn btn-sm btn-info" title="Transferir para outro depto"
                  onclick="toggleTransfer('tf_<?= $cl['id'] ?>')">
                  <i class="feather icon-shuffle"></i>
                </button>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="acao_admin" value="devolver_ia">
                  <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
                  <button class="btn btn-sm btn-outline-secondary" title="Devolver para IA" onclick="return confirm('Devolver para a IA?')">
                    <i class="feather icon-rotate-ccw"></i>
                  </button>
                </form>
              </div>
            </div>
            <!-- painel de transferência de depto -->
            <div id="tf_<?= $cl['id'] ?>" style="display:none;margin-top:6px;">
              <form method="post" class="d-flex align-items-center" style="gap:4px;">
                <input type="hidden" name="acao_admin" value="transferir_depto">
                <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
                <select name="novo_depto" class="form-control form-control-sm" style="max-width:180px;" required>
                  <option value="">Selecione o depto...</option>
                  <?php foreach ($deptos as $d_opt):
                    if ((int)$d_opt['id'] === (int)$dep['id']) continue; ?>
                  <option value="<?= $d_opt['id'] ?>"><?= htmlspecialchars($d_opt['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-info" type="submit" onclick="return confirm('Transferir para este departamento?')">
                  Transferir
                </button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($fila_dep) && empty($humano_dep)): ?>
        <div class="text-center text-muted py-4 small">Nenhum cliente neste departamento</div>
        <?php endif; ?>

      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Sem depto (modo_atendimento != ia mas sem depto) -->
<?php
$sem_depto = array_filter($clientes_ativos, fn($c) => empty($c['depto_atual']));
if (!empty($sem_depto)):
?>
<div class="card mb-4">
  <div class="card-header" style="background:#6c757d;color:#fff;">
    <h6 class="mb-0"><i class="feather icon-alert-circle mr-2"></i>Sem departamento</h6>
  </div>
  <div class="card-body p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>Nome</th><th>Telefone</th><th>Modo</th><th>Ações</th></tr></thead>
      <tbody>
        <?php foreach ($sem_depto as $cl): ?>
        <tr>
          <td><?= htmlspecialchars($cl['nome'] ?: '—') ?></td>
          <td><?= htmlspecialchars($cl['telefone']) ?></td>
          <td><span class="badge <?= $cl['modo_atendimento']==='fila'?'badge-warning':'badge-success' ?>"><?= $cl['modo_atendimento'] ?></span></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="acao_admin" value="devolver_ia">
              <input type="hidden" name="cliente_id" value="<?= $cl['id'] ?>">
              <button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Devolver?')"><i class="feather icon-rotate-ccw"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php if (empty($deptos) && empty($clientes_ativos)): ?>
<div class="card"><div class="card-body text-center text-muted py-5">
  <i class="feather icon-list" style="font-size:48px;"></i>
  <p class="mt-3">Nenhum departamento configurado ainda. <a href="departamentos.php">Criar departamentos</a></p>
</div></div>
<?php endif; ?>

</div>
</div>
</div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
function toggleTransfer(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = (el.style.display === 'none') ? 'block' : 'none';
}
// Auto-refresh a cada 15s (somente se não houver painel de transferência aberto)
setTimeout(function() {
    var abertos = document.querySelectorAll('[id^="tf_"]');
    var algumAberto = false;
    abertos.forEach(function(el) { if (el.style.display !== 'none') algumAberto = true; });
    if (!algumAberto) location.reload();
}, 15000);
</script>

<?php include 'footer.php'; ?>
