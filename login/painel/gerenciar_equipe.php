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
$udata      = $res_u->fetch_assoc();
$tipo       = $udata['tipo'];
$autorizado = $udata['autorizado'];
$nome       = Priletra($udata['nome']);
$img_perfil = $udata['perfil_img'];
$usuario_api= $udata['usuario_api'];

include 'menu.php';

if ($autorizado != 2) VaiPara('desbloquar.php');
if ($tipo != 1)       VaiPara('index.php');

// ─── Processamento POST ────────────────────────────────────────────────────
$msg_ok  = '';
$msg_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_form = $_POST['acao_form'] ?? '';

    // ── Criar atendente ──────────────────────────────────────────────────
    if ($acao_form === 'criar_usuario') {
        $at_nome  = trim($_POST['at_nome']  ?? '');
        $at_login = trim($_POST['at_login'] ?? '');
        $at_senha = trim($_POST['at_senha'] ?? '');
        $at_email = trim($_POST['at_email'] ?? '');
        $at_depto    = (int)($_POST['at_depto'] ?? 0);
        $at_telnotif = preg_replace('/\D/', '', trim($_POST['at_telnotif'] ?? ''));

        if ($at_nome === '' || $at_login === '' || $at_senha === '') {
            $msg_err = 'Nome, login e senha são obrigatórios.';
        } elseif (strlen($at_senha) < 6) {
            $msg_err = 'A senha deve ter pelo menos 6 caracteres.';
        } else {
            $s_chk = $conn->prepare("SELECT id FROM login WHERE login=?");
            $s_chk->bind_param("s", $at_login);
            $s_chk->execute();
            $r_chk = $s_chk->get_result();
            $s_chk->close();
            if ($r_chk && $r_chk->num_rows > 0) {
                $msg_err = 'Este login já está em uso. Escolha outro.';
            } else {
                $s_ins = $conn->prepare("INSERT INTO login (login, senha, tipo, usuario_api, nome, email, telefone_notif, autorizado, modo_atuante, funcao, creditos, plano) VALUES (?, ?, '3', ?, ?, ?, ?, '2', 'MultiAtendente', 'MultiAtendente', 0, '')");
                $s_ins->bind_param("ssssss", $at_login, $at_senha, $usuario_api, $at_nome, $at_email, $at_telnotif);
                if ($s_ins->execute()) {
                    $msg_ok = "Atendente '{$at_nome}' criado com sucesso!";
                    if ($at_depto > 0) {
                        $s_vn = $conn->prepare("INSERT IGNORE INTO atendentes_depto (login_atendente, depto_id, usuario_api) VALUES (?,?,?)");
                        $s_vn->bind_param("sis", $at_login, $at_depto, $usuario_api);
                        $s_vn->execute();
                        $s_vn->close();
                        $msg_ok .= ' Vinculado ao setor selecionado.';
                    }
                } else {
                    $msg_err = 'Erro ao criar: ' . $s_ins->error;
                }
                $s_ins->close();
            }
        }
    }

    // ── Editar atendente ─────────────────────────────────────────────────
    if ($acao_form === 'editar_usuario') {
        $uid      = (int)($_POST['uid'] ?? 0);
        $at_nome  = trim($_POST['at_nome']  ?? '');
        $at_email = trim($_POST['at_email'] ?? '');
        $at_senha = trim($_POST['at_senha'] ?? '');

        $at_telnotif = preg_replace('/\D/', '', trim($_POST['at_telnotif'] ?? ''));
        if ($uid > 0 && $at_nome !== '') {
            if ($at_senha !== '') {
                if (strlen($at_senha) < 6) {
                    $msg_err = 'A nova senha deve ter pelo menos 6 caracteres.';
                } else {
                    $s_upd = $conn->prepare("UPDATE login SET nome=?, email=?, telefone_notif=?, senha=? WHERE id=? AND usuario_api=? AND tipo='3'");
                    $s_upd->bind_param("ssssss", $at_nome, $at_email, $at_telnotif, $at_senha, $uid, $usuario_api);
                    if ($s_upd->execute()) $msg_ok = 'Atendente atualizado.';
                    else $msg_err = 'Erro ao atualizar: ' . $s_upd->error;
                    $s_upd->close();
                }
            } else {
                $s_upd = $conn->prepare("UPDATE login SET nome=?, email=?, telefone_notif=? WHERE id=? AND usuario_api=? AND tipo='3'");
                $s_upd->bind_param("sssss", $at_nome, $at_email, $at_telnotif, $uid, $usuario_api);
                if ($s_upd->execute()) $msg_ok = 'Atendente atualizado.';
                else $msg_err = 'Erro ao atualizar: ' . $s_upd->error;
                $s_upd->close();
            }
        }
    }

    // ── Ativar / Desativar ────────────────────────────────────────────────
    if ($acao_form === 'toggle_status') {
        $uid   = (int)($_POST['uid'] ?? 0);
        $novoA = (int)($_POST['novo_autorizado'] ?? 0);
        $novoS = $novoA === 2 ? 'ativo' : 'inativo';
        if ($uid > 0) {
            $s_tog = $conn->prepare("UPDATE login SET autorizado=?, situacao=? WHERE id=? AND usuario_api=? AND tipo='3'");
            $s_tog->bind_param("isis", $novoA, $novoS, $uid, $usuario_api);
            $s_tog->execute();
            $s_tog->close();
            $msg_ok = $novoA === 2 ? 'Atendente ativado.' : 'Atendente desativado.';
        }
    }

    // ── Deletar atendente ────────────────────────────────────────────────
    if ($acao_form === 'deletar_usuario') {
        $uid = (int)($_POST['uid'] ?? 0);
        if ($uid > 0) {
            // Busca o login antes de deletar para remover vínculos
            $s_lg = $conn->prepare("SELECT login FROM login WHERE id=? AND usuario_api=? AND tipo='3' LIMIT 1");
            $s_lg->bind_param("is", $uid, $usuario_api);
            $s_lg->execute();
            $r_lg = $s_lg->get_result();
            $s_lg->close();
            if ($r_lg && $r_lg->num_rows > 0) {
                $login_at = $r_lg->fetch_assoc()['login'];
                // Remove vínculos de departamento
                $s_dv = $conn->prepare("DELETE FROM atendentes_depto WHERE login_atendente=? AND usuario_api=?");
                $s_dv->bind_param("ss", $login_at, $usuario_api);
                $s_dv->execute();
                $s_dv->close();
                // Deleta o usuário
                $s_del = $conn->prepare("DELETE FROM login WHERE id=? AND usuario_api=? AND tipo='3'");
                $s_del->bind_param("is", $uid, $usuario_api);
                $s_del->execute();
                $s_del->close();
                $msg_ok = 'Atendente deletado.';
            } else {
                $msg_err = 'Atendente não encontrado.';
            }
        }
    }

    // ── Vincular / desvincular setor por usuário ─────────────────────────
    if ($acao_form === 'vincular_setor') {
        $login_at = trim($_POST['login_atendente'] ?? '');
        $depto_id = (int)($_POST['depto_id'] ?? 0);
        if ($login_at !== '' && $depto_id > 0) {
            $s_va = $conn->prepare("INSERT IGNORE INTO atendentes_depto (login_atendente, depto_id, usuario_api) VALUES (?,?,?)");
            $s_va->bind_param("sis", $login_at, $depto_id, $usuario_api);
            $s_va->execute();
            $s_va->close();
            $msg_ok = 'Setor vinculado.';
        }
    }

    if ($acao_form === 'desvincular_setor') {
        $login_at = trim($_POST['login_atendente'] ?? '');
        $depto_id = (int)($_POST['depto_id'] ?? 0);
        if ($login_at !== '' && $depto_id > 0) {
            $s_dv = $conn->prepare("DELETE FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=?");
            $s_dv->bind_param("sis", $login_at, $depto_id, $usuario_api);
            $s_dv->execute();
            $s_dv->close();
            $msg_ok = 'Setor desvinculado.';
        }
    }

    // ── CRUD de setores ──────────────────────────────────────────────────
    if ($acao_form === 'salvar_setor') {
        $depto_id   = (int)($_POST['depto_id'] ?? 0);
        $nome_d     = trim($_POST['nome_depto'] ?? '');
        $descricao  = trim($_POST['descricao'] ?? '');
        $palavras   = trim($_POST['palavras_chave'] ?? '');
        $msg_transf = trim($_POST['msg_transferencia'] ?? '');
        $ativo             = isset($_POST['ativo']) ? 1 : 0;
        $notif_atendentes  = isset($_POST['notificar_atendentes']) ? 1 : 0;

        if ($nome_d === '') {
            $msg_err = 'O nome do setor é obrigatório.';
        } else {
            if ($depto_id > 0) {
                $stmt_s = $conn->prepare("UPDATE departamentos SET nome=?, descricao=?, palavras_chave=?, msg_transferencia=?, ativo=?, notificar_atendentes=? WHERE id=? AND usuario_api=?");
                $stmt_s->bind_param("ssssiiis", $nome_d, $descricao, $palavras, $msg_transf, $ativo, $notif_atendentes, $depto_id, $usuario_api);
            } else {
                $stmt_s = $conn->prepare("INSERT INTO departamentos (usuario_api, nome, descricao, palavras_chave, msg_transferencia, ativo, notificar_atendentes) VALUES (?,?,?,?,?,?,?)");
                $stmt_s->bind_param("sssssii", $usuario_api, $nome_d, $descricao, $palavras, $msg_transf, $ativo, $notif_atendentes);
            }
            if ($stmt_s->execute()) {
                $msg_ok = $depto_id > 0 ? 'Setor atualizado.' : 'Setor criado com sucesso.';
            } else {
                $msg_err = 'Erro ao salvar: ' . $stmt_s->error;
            }
            $stmt_s->close();
        }
    }

    if ($acao_form === 'excluir_setor') {
        $depto_id = (int)($_POST['depto_id'] ?? 0);
        if ($depto_id > 0) {
            $stmt_d = $conn->prepare("DELETE FROM departamentos WHERE id=? AND usuario_api=?");
            $stmt_d->bind_param("is", $depto_id, $usuario_api);
            $stmt_d->execute();
            $stmt_d->close();
            $stmt_da = $conn->prepare("DELETE FROM atendentes_depto WHERE depto_id=? AND usuario_api=?");
            $stmt_da->bind_param("is", $depto_id, $usuario_api);
            $stmt_da->execute();
            $stmt_da->close();
            $msg_ok = 'Setor excluído.';
        }
    }

    // Vincular/desvincular atendente a setor (vista do setor)
    if ($acao_form === 'vincular_atendente') {
        $depto_id    = (int)($_POST['depto_id'] ?? 0);
        $login_at    = trim($_POST['login_atendente'] ?? '');
        if ($depto_id > 0 && $login_at !== '') {
            $s_va = $conn->prepare("INSERT IGNORE INTO atendentes_depto (login_atendente, depto_id, usuario_api) VALUES (?,?,?)");
            $s_va->bind_param("sis", $login_at, $depto_id, $usuario_api);
            $s_va->execute();
            $s_va->close();
            $msg_ok = 'Atendente vinculado ao setor.';
        }
    }

    if ($acao_form === 'desvincular_atendente') {
        $depto_id    = (int)($_POST['depto_id'] ?? 0);
        $login_at    = trim($_POST['login_atendente'] ?? '');
        if ($depto_id > 0 && $login_at !== '') {
            $s_dv = $conn->prepare("DELETE FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=?");
            $s_dv->bind_param("sis", $login_at, $depto_id, $usuario_api);
            $s_dv->execute();
            $s_dv->close();
            $msg_ok = 'Atendente desvinculado.';
        }
    }
}

// ─── Buscar dados ────────────────────────────────────────────────────────────
// Atendentes (tipo=3)
$stmt_at = $conn->prepare("SELECT id, login, nome, email, telefone_notif, autorizado, situacao FROM login WHERE usuario_api=? AND tipo='3' ORDER BY nome ASC");
$stmt_at->bind_param("s", $usuario_api);
$stmt_at->execute();
$res_at = $stmt_at->get_result();
$stmt_at->close();
$atendentes = [];
while ($r = $res_at->fetch_assoc()) $atendentes[] = $r;

// Departamentos
$stmt_dep = $conn->prepare("SELECT * FROM departamentos WHERE usuario_api=? ORDER BY nome ASC");
$stmt_dep->bind_param("s", $usuario_api);
$stmt_dep->execute();
$res_dep = $stmt_dep->get_result();
$stmt_dep->close();
$deptos = [];
while ($r = $res_dep->fetch_assoc()) $deptos[] = $r;

// Vínculos: atendente -> setores
$vinculos_por_atendente = [];  // login -> [{depto_id, nome}]
$vinculos_por_depto     = [];  // depto_id -> [{login, nome_atendente}]
if (!empty($atendentes)) {
    $res_vat = $conn->query("SELECT ad.login_atendente, ad.depto_id, d.nome AS depto_nome, l.nome AS at_nome
        FROM atendentes_depto ad
        LEFT JOIN departamentos d ON d.id = ad.depto_id
        LEFT JOIN login l ON l.login = ad.login_atendente
        WHERE ad.usuario_api = '{$conn->real_escape_string($usuario_api)}'");
    if ($res_vat) {
        while ($rv = $res_vat->fetch_assoc()) {
            $vinculos_por_atendente[$rv['login_atendente']][] = $rv;
            $vinculos_por_depto[$rv['depto_id']][] = $rv;
        }
    }
}

// Edição de setor (via GET)
$depto_editar = null;
if (isset($_GET['editar_setor'])) {
    $eid = (int)$_GET['editar_setor'];
    $se = $conn->prepare("SELECT * FROM departamentos WHERE id=? AND usuario_api=?");
    $se->bind_param("is", $eid, $usuario_api);
    $se->execute();
    $re = $se->get_result();
    $se->close();
    if ($re && $re->num_rows > 0) $depto_editar = $re->fetch_assoc();
}

// Tab ativa (GET ou inferida)
$tab_ativa = ($_GET['tab'] ?? 'usuarios');
if ($depto_editar) $tab_ativa = 'setores';

// Contadores
$total_at = count($atendentes);
$ativos   = count(array_filter($atendentes, fn($a) => $a['autorizado'] == 2));
$inativos = $total_at - $ativos;
$total_dep = count($deptos);

$css_extra = '<style>
/* ── Layout geral ── */
.ge-header { margin-bottom:24px; }
.ge-header h4 { color:#001f3f; font-weight:700; margin:0; }

/* ── Cards de estatísticas ── */
.stat-row { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:24px; }
.stat-card { flex:1; min-width:140px; border-radius:12px; padding:18px 20px; color:#fff;
             display:flex; align-items:center; gap:14px; box-shadow:0 4px 16px rgba(0,0,0,.12); }
.stat-card .s-icon { font-size:32px; opacity:.85; }
.stat-card .s-num  { font-size:30px; font-weight:700; line-height:1; }
.stat-card .s-label{ font-size:11px; opacity:.88; text-transform:uppercase; letter-spacing:.5px; }
.sc-total   { background:linear-gradient(135deg,#001f3f,#003f7f); }
.sc-ativo   { background:linear-gradient(135deg,#27ae60,#1abc9c); }
.sc-inativo { background:linear-gradient(135deg,#e74c3c,#c0392b); }
.sc-setor   { background:linear-gradient(135deg,#FF5500,#e04400); }

/* ── Barra de ações ── */
.acao-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
.acao-bar .btn-ab { padding:9px 18px; border-radius:8px; font-weight:600; font-size:13px;
                    border:none; cursor:pointer; display:inline-flex; align-items:center;
                    gap:6px; text-decoration:none; transition:opacity .18s; color:#fff; }
.acao-bar .btn-ab:hover { opacity:.82; text-decoration:none; color:#fff; }
.ab-usr   { background:#001f3f; }
.ab-new   { background:#FF5500; }
.ab-setor { background:#17a2b8; }
.ab-nsetor{ background:#28a745; }

/* ── Tabs ── */
.nav-tabs .nav-link { color:#001f3f; font-weight:600; }
.nav-tabs .nav-link.active { color:#FF5500; border-bottom:2px solid #FF5500; }

/* ── Tabela de usuários ── */
.usr-table { width:100%; border-collapse:collapse; font-size:13px; }
.usr-table thead th { background:#001f3f; color:#fff; padding:11px 10px;
                      font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.usr-table tbody tr:hover { background:#f5f8ff; }
.usr-table td { padding:9px 10px; vertical-align:middle; border-bottom:1px solid #eef; }
.badge-ativo   { background:#d4f5e9; color:#1a7a4a; padding:3px 10px; border-radius:20px;
                 font-size:11px; font-weight:700; white-space:nowrap; }
.badge-inativo { background:#fde8e8; color:#c0392b; padding:3px 10px; border-radius:20px;
                 font-size:11px; font-weight:700; white-space:nowrap; }
.setor-badge { background:#e8f0fe; color:#1a3f8f; padding:2px 8px; border-radius:12px;
               font-size:11px; margin:2px; display:inline-block; white-space:nowrap; }
.setor-badge .rm-setor { cursor:pointer; color:#888; margin-left:3px; font-size:10px; }
.setor-badge .rm-setor:hover { color:#c0392b; }

/* ── Botões de ação ── */
.btn-ativar   { background:#27ae60; color:#fff; border:none; border-radius:6px;
                padding:5px 11px; font-size:12px; cursor:pointer; font-weight:600; }
.btn-desativar{ background:#e67e22; color:#fff; border:none; border-radius:6px;
                padding:5px 11px; font-size:12px; cursor:pointer; font-weight:600; }
.btn-editar   { background:#001f3f; color:#fff; border:none; border-radius:6px;
                padding:5px 10px; font-size:12px; cursor:pointer; }
.btn-deletar  { background:#e74c3c; color:#fff; border:none; border-radius:6px;
                padding:5px 11px; font-size:12px; cursor:pointer; font-weight:600; }
.btn-ativar:hover,.btn-desativar:hover,.btn-editar:hover,.btn-deletar:hover { opacity:.82; }

/* ── Modais ── */
.modal-header { background:#001f3f; color:#fff; }
.modal-header .close { color:#fff; opacity:1; }

/* ── Card de setor ── */
.setor-card { border:none; box-shadow:0 2px 10px rgba(0,0,0,.08); margin-bottom:16px; border-radius:10px; }
.setor-card .card-header { border-radius:10px 10px 0 0 !important; }

/* ── Empty state ── */
.empty-state { text-align:center; padding:48px 20px; color:#aaa; }
.empty-state i { font-size:48px; display:block; margin-bottom:12px; }

@media(max-width:600px) {
    .stat-card .s-num { font-size:22px; }
    .usr-table thead th:nth-child(3),
    .usr-table td:nth-child(3) { display:none; }
}
</style>';

include 'header.php';
?>

<div class="content-page">
<div class="content">
<div class="container-fluid" style="padding:20px 24px;">

<?php if ($msg_ok): ?>
<div class="alert alert-success alert-dismissible fade show">
  <i class="feather icon-check-circle mr-2"></i><?= htmlspecialchars($msg_ok) ?>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php endif; ?>
<?php if ($msg_err): ?>
<div class="alert alert-danger alert-dismissible fade show">
  <i class="feather icon-alert-circle mr-2"></i><?= htmlspecialchars($msg_err) ?>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php endif; ?>

<!-- Cabeçalho -->
<div class="ge-header">
  <h4><i class="feather icon-users mr-2"></i>Gerenciar Equipe</h4>
  <small class="text-muted">Gerencie atendentes e setores/departamentos</small>
</div>

<!-- Cards de estatísticas -->
<div class="stat-row">
  <div class="stat-card sc-total">
    <i class="feather icon-users s-icon"></i>
    <div><div class="s-num"><?= $total_at ?></div><div class="s-label">Total de Usuários</div></div>
  </div>
  <div class="stat-card sc-ativo">
    <i class="feather icon-user-check s-icon"></i>
    <div><div class="s-num"><?= $ativos ?></div><div class="s-label">Ativos</div></div>
  </div>
  <div class="stat-card sc-inativo">
    <i class="feather icon-user-x s-icon"></i>
    <div><div class="s-num"><?= $inativos ?></div><div class="s-label">Inativos</div></div>
  </div>
  <div class="stat-card sc-setor">
    <i class="feather icon-tag s-icon"></i>
    <div><div class="s-num"><?= $total_dep ?></div><div class="s-label">Setores</div></div>
  </div>
</div>

<!-- Barra de ações -->
<div class="acao-bar">
  <a href="#" class="btn-ab ab-usr" onclick="setTab('usuarios');return false;">
    <i class="feather icon-users"></i> Usuários
  </a>
  <button class="btn-ab ab-new" data-toggle="modal" data-target="#modalNovoUsuario">
    <i class="feather icon-user-plus"></i> Novo Usuário
  </button>
  <a href="#" class="btn-ab ab-setor" onclick="setTab('setores');return false;">
    <i class="feather icon-tag"></i> Setores
  </a>
  <button class="btn-ab ab-nsetor" data-toggle="modal" data-target="#modalNovoSetor">
    <i class="feather icon-plus"></i> Novo Setor
  </button>
  <a href="fila_geral.php" class="btn-ab" style="background:#8e44ad;">
    <i class="feather icon-list"></i> Fila de Atendimento
  </a>
</div>

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-3" id="equipeTab">
  <li class="nav-item">
    <a class="nav-link <?= $tab_ativa === 'usuarios' ? 'active' : '' ?>" href="#tab-usuarios" data-toggle="tab">
      <i class="feather icon-users mr-1"></i>Usuários
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $tab_ativa === 'setores' ? 'active' : '' ?>" href="#tab-setores" data-toggle="tab">
      <i class="feather icon-tag mr-1"></i>Setores
    </a>
  </li>
</ul>

<div class="tab-content" id="equipeTabContent">

  <!-- ═══════════ ABA USUÁRIOS ═══════════ -->
  <div class="tab-pane fade <?= $tab_ativa === 'usuarios' ? 'show active' : '' ?>" id="tab-usuarios">

    <?php if (empty($atendentes)): ?>
    <div class="card"><div class="card-body empty-state">
      <i class="feather icon-user-plus"></i>
      <p>Nenhum atendente cadastrado ainda.</p>
      <button class="btn btn-sm btn-laranja" data-toggle="modal" data-target="#modalNovoUsuario">
        <i class="feather icon-user-plus mr-1"></i>Criar primeiro atendente
      </button>
    </div></div>
    <?php else: ?>
    <div class="card" style="border:none;box-shadow:0 2px 12px rgba(0,0,0,.08);border-radius:10px;">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="usr-table">
            <thead>
              <tr>
                <th style="width:48px;">ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Login / Fone</th>
                <th>Setores</th>
                <th style="width:80px;">Status</th>
                <th style="width:200px;">Ações</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($atendentes as $at): ?>
            <?php
              $is_ativo = ($at['autorizado'] == 2);
              $vins = $vinculos_por_atendente[$at['login']] ?? [];
            ?>
            <tr>
              <td><small class="text-muted"><?= $at['id'] ?></small></td>
              <td><strong><?= htmlspecialchars($at['nome'] ?: '—') ?></strong></td>
              <td><small><?= htmlspecialchars($at['email'] ?: '—') ?></small></td>
              <td><small class="text-muted"><?= htmlspecialchars($at['login']) ?></small></td>
              <td>
                <?php foreach ($vins as $v): ?>
                <span class="setor-badge">
                  <?= htmlspecialchars($v['depto_nome'] ?? '?') ?>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Remover deste setor?')">
                    <input type="hidden" name="acao_form" value="desvincular_setor">
                    <input type="hidden" name="login_atendente" value="<?= htmlspecialchars($at['login']) ?>">
                    <input type="hidden" name="depto_id" value="<?= (int)$v['depto_id'] ?>">
                    <button type="submit" class="rm-setor" title="Remover setor" style="background:none;border:none;padding:0;">&times;</button>
                  </form>
                </span>
                <?php endforeach; ?>
                <!-- Adicionar setor -->
                <?php
                  $ids_vinculados = array_column($vins, 'depto_id');
                  $deptos_livres  = array_filter($deptos, fn($d) => !in_array($d['id'], $ids_vinculados));
                ?>
                <?php if (!empty($deptos_livres)): ?>
                <form method="post" style="display:inline;" class="form-inline">
                  <input type="hidden" name="acao_form" value="vincular_setor">
                  <input type="hidden" name="login_atendente" value="<?= htmlspecialchars($at['login']) ?>">
                  <select name="depto_id" class="form-control form-control-sm" style="font-size:11px;height:24px;padding:0 4px;display:inline-block;width:auto;" onchange="this.form.submit()">
                    <option value="">+ setor</option>
                    <?php foreach ($deptos_livres as $dl): ?>
                    <option value="<?= $dl['id'] ?>"><?= htmlspecialchars($dl['nome']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
                <?php endif; ?>
              </td>
              <td>
                <span class="<?= $is_ativo ? 'badge-ativo' : 'badge-inativo' ?>">
                  <?= $is_ativo ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td style="white-space:nowrap;">
                <!-- Editar -->
                <button class="btn-editar mr-1"
                  onclick="abrirEdicao(<?= $at['id'] ?>, '<?= htmlspecialchars(addslashes($at['nome']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($at['email'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars($at['telefone_notif'] ?? '', ENT_QUOTES) ?>')"
                  title="Editar"><i class="feather icon-edit-2"></i></button>
                <!-- Toggle status -->
                <form method="post" style="display:inline;">
                  <input type="hidden" name="acao_form" value="toggle_status">
                  <input type="hidden" name="uid" value="<?= $at['id'] ?>">
                  <input type="hidden" name="novo_autorizado" value="<?= $is_ativo ? 0 : 2 ?>">
                  <button type="submit" class="<?= $is_ativo ? 'btn-desativar' : 'btn-ativar' ?> mr-1">
                    <?= $is_ativo ? 'Desativar' : 'Ativar' ?>
                  </button>
                </form>
                <!-- Deletar -->
                <form method="post" style="display:inline;" onsubmit="return confirm('Deletar este atendente definitivamente?')">
                  <input type="hidden" name="acao_form" value="deletar_usuario">
                  <input type="hidden" name="uid" value="<?= $at['id'] ?>">
                  <button type="submit" class="btn-deletar">Deletar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /tab-usuarios -->

  <!-- ═══════════ ABA SETORES ═══════════ -->
  <div class="tab-pane fade <?= $tab_ativa === 'setores' ? 'show active' : '' ?>" id="tab-setores">

    <?php if ($depto_editar): ?>
    <!-- Formulário de edição inline -->
    <div class="card setor-card mb-4">
      <div class="card-header" style="background:#FF5500;color:#fff;">
        <h6 class="mb-0"><i class="feather icon-edit-2 mr-2"></i>Editando: <?= htmlspecialchars($depto_editar['nome']) ?></h6>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="acao_form" value="salvar_setor">
          <input type="hidden" name="depto_id" value="<?= (int)$depto_editar['id'] ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nome <span class="text-danger">*</span></label>
              <input type="text" name="nome_depto" class="form-control" required value="<?= htmlspecialchars($depto_editar['nome']) ?>">
            </div>
            <div class="form-group col-md-6">
              <label>Descrição</label>
              <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($depto_editar['descricao'] ?? '') ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Palavras-chave <small class="text-muted">(separadas por vírgula)</small></label>
              <input type="text" name="palavras_chave" class="form-control" placeholder="suporte, financeiro..." value="<?= htmlspecialchars($depto_editar['palavras_chave'] ?? '') ?>">
            </div>
            <div class="form-group col-md-6">
              <label>Mensagem de transferência</label>
              <input type="text" name="msg_transferencia" class="form-control" value="<?= htmlspecialchars($depto_editar['msg_transferencia'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <div class="checkbox checkbox-primary">
              <input type="checkbox" id="ativo_ed" name="ativo" <?= $depto_editar['ativo'] ? 'checked' : '' ?>>
              <label for="ativo_ed">Setor ativo</label>
            </div>
            <div class="checkbox checkbox-primary mt-1">
              <input type="checkbox" id="notif_ed" name="notificar_atendentes" <?= ($depto_editar['notificar_atendentes'] ?? 1) ? 'checked' : '' ?>>
              <label for="notif_ed">Notificar atendentes via WhatsApp ao entrar na fila</label>
            </div>
          </div>
          <button type="submit" class="btn btn-sm mr-2" style="background:#FF5500;color:#fff;font-weight:600;">
            <i class="feather icon-save mr-1"></i>Salvar alterações
          </button>
          <a href="gerenciar_equipe.php?tab=setores" class="btn btn-sm btn-outline-secondary">Cancelar</a>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <?php if (empty($deptos)): ?>
    <div class="card setor-card"><div class="card-body empty-state">
      <i class="feather icon-tag"></i>
      <p>Nenhum setor cadastrado ainda.</p>
      <button class="btn btn-sm" style="background:#28a745;color:#fff;" data-toggle="modal" data-target="#modalNovoSetor">
        <i class="feather icon-plus mr-1"></i>Criar primeiro setor
      </button>
    </div></div>
    <?php else: ?>
    <div class="row">
    <?php foreach ($deptos as $dep): ?>
    <?php $at_dep = $vinculos_por_depto[$dep['id']] ?? []; ?>
    <div class="col-md-6">
    <div class="card setor-card">
      <div class="card-header d-flex justify-content-between align-items-center"
           style="background:<?= $dep['ativo'] ? '#001f3f' : '#6c757d' ?>;color:#fff;border-radius:10px 10px 0 0;">
        <h6 class="mb-0">
          <i class="feather icon-tag mr-2"></i><?= htmlspecialchars($dep['nome']) ?>
          <?php if (!$dep['ativo']): ?><span class="badge badge-warning ml-2">Inativo</span><?php endif; ?>
        </h6>
        <div class="d-flex gap-1">
          <a href="gerenciar_equipe.php?editar_setor=<?= $dep['id'] ?>" class="btn btn-sm btn-light mr-1" title="Editar">
            <i class="feather icon-edit-2"></i>
          </a>
          <form method="post" style="display:inline;" onsubmit="return confirm('Excluir setor e desvincular todos os atendentes?')">
            <input type="hidden" name="acao_form" value="excluir_setor">
            <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
            <button class="btn btn-sm btn-danger" title="Excluir"><i class="feather icon-trash-2"></i></button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <?php if ($dep['descricao']): ?>
        <p class="text-muted small mb-2"><?= htmlspecialchars($dep['descricao']) ?></p>
        <?php endif; ?>
        <?php if ($dep['palavras_chave']): ?>
        <p class="mb-2 small"><strong>Palavras-chave:</strong> <span class="text-muted"><?= htmlspecialchars($dep['palavras_chave']) ?></span></p>
        <?php endif; ?>

        <!-- Atendentes vinculados -->
        <div class="mt-2">
          <strong class="small">Atendentes:</strong>
          <?php if (!empty($at_dep)): ?>
          <div class="mt-1">
            <?php foreach ($at_dep as $av): ?>
            <span class="setor-badge">
              <?= htmlspecialchars($av['at_nome'] ?? $av['login_atendente']) ?>
              <form method="post" style="display:inline;" onsubmit="return confirm('Remover atendente deste setor?')">
                <input type="hidden" name="acao_form" value="desvincular_atendente">
                <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
                <input type="hidden" name="login_atendente" value="<?= htmlspecialchars($av['login_atendente']) ?>">
                <button type="submit" style="background:none;border:none;padding:0;" class="rm-setor" title="Remover">&times;</button>
              </form>
            </span>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <span class="text-muted small"> Nenhum atendente vinculado.</span>
          <?php endif; ?>

          <?php
            $logins_vinc = array_column($at_dep, 'login_atendente');
            $at_livres   = array_filter($atendentes, fn($a) => !in_array($a['login'], $logins_vinc));
          ?>
          <?php if (!empty($at_livres)): ?>
          <form method="post" class="form-inline mt-2">
            <input type="hidden" name="acao_form" value="vincular_atendente">
            <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
            <select name="login_atendente" class="form-control form-control-sm mr-2">
              <option value="">— Adicionar atendente —</option>
              <?php foreach ($at_livres as $alv): ?>
              <option value="<?= htmlspecialchars($alv['login']) ?>"><?= htmlspecialchars($alv['nome'] ?: $alv['login']) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm" style="background:#FF5500;color:#fff;">Vincular</button>
          </form>
          <?php elseif (empty($atendentes)): ?>
          <p class="text-muted small mt-1">Crie atendentes primeiro para vincular aqui.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div><!-- /tab-setores -->

</div><!-- /tab-content -->
</div><!-- /container -->
</div>
</div>

<!-- ══════ MODAL: Novo Usuário ══════ -->
<div class="modal fade" id="modalNovoUsuario" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="feather icon-user-plus mr-2"></i>Novo Atendente</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="post">
        <input type="hidden" name="acao_form" value="criar_usuario">
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-6">
              <label>Nome completo <span class="text-danger">*</span></label>
              <input type="text" name="at_nome" class="form-control" placeholder="Ex: João Silva" required>
            </div>
            <div class="form-group col-6">
              <label>Login / Telefone <span class="text-danger">*</span></label>
              <input type="text" name="at_login" class="form-control" placeholder="Ex: 67999001122" required autocomplete="off">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-6">
              <label>E-mail</label>
              <input type="email" name="at_email" class="form-control" placeholder="joao@empresa.com">
            </div>
            <div class="form-group col-6">
              <label>Senha <span class="text-danger">*</span> <small class="text-muted">(mín. 6)</small></label>
              <input type="password" name="at_senha" class="form-control" placeholder="••••••" required autocomplete="new-password">
            </div>
          </div>
          <div class="form-group">
            <label>WhatsApp para alertas de fila <small class="text-muted">(com DDD, ex: 67999001122)</small></label>
            <input type="text" name="at_telnotif" class="form-control" placeholder="67999001122" maxlength="15" inputmode="numeric">
            <small class="text-muted">Quando um cliente entrar na fila do setor, o sistema enviará uma mensagem WhatsApp para este número.</small>
          </div>
          <div class="form-group">
            <label>Vincular ao setor <small class="text-muted">(opcional)</small></label>
            <select name="at_depto" class="form-control">
              <option value="">— Nenhum agora —</option>
              <?php foreach ($deptos as $d_opt): ?>
              <option value="<?= $d_opt['id'] ?>"><?= htmlspecialchars($d_opt['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn" style="background:#FF5500;color:#fff;font-weight:600;">
            <i class="feather icon-user-plus mr-1"></i>Criar atendente
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══════ MODAL: Editar Usuário ══════ -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="feather icon-edit-2 mr-2"></i>Editar Atendente</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="post">
        <input type="hidden" name="acao_form" value="editar_usuario">
        <input type="hidden" name="uid" id="edit_uid">
        <div class="modal-body">
          <div class="form-group">
            <label>Nome completo <span class="text-danger">*</span></label>
            <input type="text" name="at_nome" id="edit_nome" class="form-control" required>
          </div>
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="at_email" id="edit_email" class="form-control">
          </div>
          <div class="form-group">
            <label>WhatsApp para alertas de fila <small class="text-muted">(com DDD, ex: 67999001122)</small></label>
            <input type="text" name="at_telnotif" id="edit_telnotif" class="form-control" placeholder="67999001122" maxlength="15" inputmode="numeric">
          </div>
          <div class="form-group">
            <label>Nova senha <small class="text-muted">(deixe em branco para não alterar)</small></label>
            <input type="password" name="at_senha" class="form-control" placeholder="••••••" autocomplete="new-password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn" style="background:#001f3f;color:#fff;font-weight:600;">
            <i class="feather icon-save mr-1"></i>Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══════ MODAL: Novo Setor ══════ -->
<div class="modal fade" id="modalNovoSetor" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="feather icon-plus mr-2"></i>Novo Setor</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="post">
        <input type="hidden" name="acao_form" value="salvar_setor">
        <input type="hidden" name="depto_id" value="0">
        <div class="modal-body">
          <div class="form-group">
            <label>Nome do setor <span class="text-danger">*</span></label>
            <input type="text" name="nome_depto" class="form-control" placeholder="Ex: Comercial, Suporte..." required>
          </div>
          <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" placeholder="Breve descrição do setor">
          </div>
          <div class="form-group">
            <label>Palavras-chave <small class="text-muted">(separadas por vírgula)</small></label>
            <input type="text" name="palavras_chave" class="form-control" placeholder="suporte, reclamação, financeiro">
            <small class="text-muted">Quando o cliente digitar uma dessas palavras na conversa com a IA, será transferido para este setor.</small>
          </div>
          <div class="form-group">
            <label>Mensagem de transferência</label>
            <textarea name="msg_transferencia" class="form-control" rows="2" placeholder="Aguarde, vou transferir para nosso time..."></textarea>
          </div>
          <div class="form-group mb-0">
            <div class="checkbox checkbox-primary">
              <input type="checkbox" id="novo_ativo" name="ativo" checked>
              <label for="novo_ativo">Setor ativo</label>
            </div>
            <div class="checkbox checkbox-primary mt-1">
              <input type="checkbox" id="novo_notif" name="notificar_atendentes" checked>
              <label for="novo_notif">Notificar atendentes via WhatsApp ao entrar na fila</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn" style="background:#28a745;color:#fff;font-weight:600;">
            <i class="feather icon-save mr-1"></i>Criar setor
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function setTab(id) {
    document.querySelector('#equipeTab a[href="#tab-' + id + '"]').click();
}

function abrirEdicao(uid, nome, email, telnotif) {
    document.getElementById('edit_uid').value      = uid;
    document.getElementById('edit_nome').value     = nome;
    document.getElementById('edit_email').value    = email;
    document.getElementById('edit_telnotif').value = telnotif || '';
    $('#modalEditarUsuario').modal('show');
}

// Abrir modal automaticamente se houver erro e o form veio do modal
<?php if ($msg_err && isset($_POST['acao_form'])): ?>
<?php if ($_POST['acao_form'] === 'criar_usuario'): ?>
$(document).ready(function(){ $('#modalNovoUsuario').modal('show'); });
<?php elseif ($_POST['acao_form'] === 'salvar_setor' && !isset($_POST['depto_id'])): ?>
$(document).ready(function(){ $('#modalNovoSetor').modal('show'); });
<?php endif; ?>
<?php endif; ?>
</script>

<?php include 'footer.php'; ?>
