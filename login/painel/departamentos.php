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
$tipo       = $udata['tipo'];
$autorizado = $udata['autorizado'];
$nome       = Priletra($udata['nome']);
$img_perfil = $udata['perfil_img'];
$usuario_api= $udata['usuario_api'];

include 'menu.php';

if ($autorizado != 2) VaiPara('desbloquar.php');
if ($tipo != 1) VaiPara('index.php');

// ── Processamento de ações ─────────────────────────────────────────────────
$msg_ok  = '';
$msg_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_form = $_POST['acao_form'] ?? '';

    // Criar / Editar departamento
    if ($acao_form === 'salvar_depto') {
        $depto_id     = (int)($_POST['depto_id'] ?? 0);
        $nome_d       = trim($_POST['nome_depto'] ?? '');
        $descricao    = trim($_POST['descricao'] ?? '');
        $palavras     = trim($_POST['palavras_chave'] ?? '');
        $msg_transf   = trim($_POST['msg_transferencia'] ?? '');
        $ativo              = isset($_POST['ativo']) ? 1 : 0;
        $notif_atendentes   = isset($_POST['notificar_atendentes']) ? 1 : 0;

        if ($nome_d === '') {
            $msg_err = 'O nome do departamento é obrigatório.';
        } else {
            if ($depto_id > 0) {
                $stmt_s = $conn->prepare("UPDATE departamentos SET nome=?, descricao=?, palavras_chave=?, msg_transferencia=?, ativo=?, notificar_atendentes=? WHERE id=? AND usuario_api=?");
                $stmt_s->bind_param("ssssiiis", $nome_d, $descricao, $palavras, $msg_transf, $ativo, $notif_atendentes, $depto_id, $usuario_api);
            } else {
                $stmt_s = $conn->prepare("INSERT INTO departamentos (usuario_api, nome, descricao, palavras_chave, msg_transferencia, ativo, notificar_atendentes) VALUES (?,?,?,?,?,?,?)");
                $stmt_s->bind_param("sssssii", $usuario_api, $nome_d, $descricao, $palavras, $msg_transf, $ativo, $notif_atendentes);
            }
            if ($stmt_s->execute()) {
                $msg_ok = $depto_id > 0 ? 'Departamento atualizado com sucesso.' : 'Departamento criado com sucesso.';
            } else {
                $msg_err = 'Erro ao salvar: ' . $stmt_s->error;
            }
            $stmt_s->close();
        }
    }

    // Excluir departamento
    if ($acao_form === 'excluir_depto') {
        $depto_id = (int)($_POST['depto_id'] ?? 0);
        if ($depto_id > 0) {
            $stmt_d = $conn->prepare("DELETE FROM departamentos WHERE id=? AND usuario_api=?");
            $stmt_d->bind_param("is", $depto_id, $usuario_api);
            $stmt_d->execute();
            $stmt_d->close();
            // Remove vínculos
            $stmt_da = $conn->prepare("DELETE FROM atendentes_depto WHERE depto_id=? AND usuario_api=?");
            $stmt_da->bind_param("is", $depto_id, $usuario_api);
            $stmt_da->execute();
            $stmt_da->close();
            $msg_ok = 'Departamento excluído.';
        }
    }

    // Vincular/desvincular atendente
    if ($acao_form === 'vincular_atendente') {
        $depto_id       = (int)($_POST['depto_id'] ?? 0);
        $login_atendente= trim($_POST['login_atendente'] ?? '');
        if ($depto_id > 0 && $login_atendente !== '') {
            $stmt_va = $conn->prepare("INSERT IGNORE INTO atendentes_depto (login_atendente, depto_id, usuario_api) VALUES (?,?,?)");
            $stmt_va->bind_param("sis", $login_atendente, $depto_id, $usuario_api);
            $stmt_va->execute();
            $stmt_va->close();
            $msg_ok = 'Atendente vinculado ao departamento.';
        }
    }

    if ($acao_form === 'desvincular_atendente') {
        $depto_id       = (int)($_POST['depto_id'] ?? 0);
        $login_atendente= trim($_POST['login_atendente'] ?? '');
        if ($depto_id > 0 && $login_atendente !== '') {
            $stmt_dv = $conn->prepare("DELETE FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=?");
            $stmt_dv->bind_param("sis", $login_atendente, $depto_id, $usuario_api);
            $stmt_dv->execute();
            $stmt_dv->close();
            $msg_ok = 'Atendente desvinculado.';
        }
    }

    // Criar novo atendente humano (tipo=3) direto nesta página
    if ($acao_form === 'criar_atendente') {
        $at_nome  = trim($_POST['at_nome']  ?? '');
        $at_login = trim($_POST['at_login'] ?? '');
        $at_senha = trim($_POST['at_senha'] ?? '');
        $at_depto = (int)($_POST['at_depto'] ?? 0);

        if ($at_nome === '' || $at_login === '' || $at_senha === '') {
            $msg_err = 'Nome, login e senha são obrigatórios.';
        } elseif (strlen($at_senha) < 6) {
            $msg_err = 'A senha deve ter pelo menos 6 caracteres.';
        } else {
            // Verificar se login já existe
            $s_chk = $conn->prepare("SELECT id FROM login WHERE login=?");
            $s_chk->bind_param("s", $at_login);
            $s_chk->execute();
            $r_chk = $s_chk->get_result();
            $s_chk->close();
            if ($r_chk && $r_chk->num_rows > 0) {
                $msg_err = 'Este login já está em uso. Escolha outro.';
            } else {
                $s_ins = $conn->prepare("INSERT INTO login (login, senha, tipo, usuario_api, nome, autorizado, modo_atuante, funcao, creditos, plano) VALUES (?, ?, '3', ?, ?, '2', 'MultiAtendente', 'MultiAtendente', 0, '')");
                $s_ins->bind_param("ssss", $at_login, $at_senha, $usuario_api, $at_nome);
                if ($s_ins->execute()) {
                    $msg_ok = "Atendente '{$at_nome}' criado com sucesso!";
                    // Vincular ao departamento, se selecionado
                    if ($at_depto > 0) {
                        $s_vn = $conn->prepare("INSERT IGNORE INTO atendentes_depto (login_atendente, depto_id, usuario_api) VALUES (?,?,?)");
                        $s_vn->bind_param("sis", $at_login, $at_depto, $usuario_api);
                        $s_vn->execute();
                        $s_vn->close();
                        $msg_ok .= ' Vinculado ao departamento selecionado.';
                    }
                } else {
                    $msg_err = 'Erro ao criar atendente: ' . $s_ins->error;
                }
                $s_ins->close();
            }
        }
    }
}

// ── Buscar dados ────────────────────────────────────────────────────────────
// Departamentos
$stmt_dep = $conn->prepare("SELECT * FROM departamentos WHERE usuario_api=? ORDER BY nome ASC");
$stmt_dep->bind_param("s", $usuario_api);
$stmt_dep->execute();
$res_dep = $stmt_dep->get_result();
$stmt_dep->close();
$deptos = [];
while ($r = $res_dep->fetch_assoc()) $deptos[] = $r;

// Atendentes: tipo=3 OU tipo=2 com modo_atuante='MultiAtendente'
$stmt_at = $conn->prepare("SELECT login, nome, tipo FROM login WHERE usuario_api=? AND (tipo='3' OR (tipo='2' AND modo_atuante='MultiAtendente')) ORDER BY nome ASC");
$stmt_at->bind_param("s", $usuario_api);
$stmt_at->execute();
$res_at = $stmt_at->get_result();
$stmt_at->close();
$atendentes_disp = [];
while ($r = $res_at->fetch_assoc()) $atendentes_disp[] = $r;

// Edição
$depto_editar = null;
if (isset($_GET['editar'])) {
    $eid = (int)$_GET['editar'];
    $stmt_e = $conn->prepare("SELECT * FROM departamentos WHERE id=? AND usuario_api=?");
    $stmt_e->bind_param("is", $eid, $usuario_api);
    $stmt_e->execute();
    $res_e = $stmt_e->get_result();
    $stmt_e->close();
    if ($res_e && $res_e->num_rows > 0) $depto_editar = $res_e->fetch_assoc();
}

// Atendentes por depto (para exibição)
$atendentes_por_depto = [];
if (!empty($deptos)) {
    $ids = implode(',', array_column($deptos, 'id'));
    $res_apdm = $conn->query("SELECT ad.login_atendente, ad.depto_id, l.nome FROM atendentes_depto ad LEFT JOIN login l ON l.login=ad.login_atendente WHERE ad.depto_id IN ($ids) AND ad.usuario_api='$usuario_api'");
    if ($res_apdm) while ($r = $res_apdm->fetch_assoc()) $atendentes_por_depto[$r['depto_id']][] = $r;
}
?>
<?php include 'header.php'; ?>

<div class="content-page">
<div class="content">
<div class="container-fluid">

<div class="row m-t-20">
  <div class="col-sm-12">
    <h4 class="page-title"><i class="feather icon-users mr-2"></i>Departamentos</h4>
  </div>
</div>

<?php if ($msg_ok): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="feather icon-check-circle mr-2"></i><?= htmlspecialchars($msg_ok) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
<?php endif; ?>
<?php if ($msg_err): ?>
<div class="alert alert-danger alert-dismissible fade show"><i class="feather icon-alert-circle mr-2"></i><?= htmlspecialchars($msg_err) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
<?php endif; ?>

<div class="row">
  <!-- Formulário -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:#001f3f;color:#fff;">
        <h5 class="mb-0"><i class="feather icon-plus-circle mr-2"></i><?= $depto_editar ? 'Editar' : 'Novo' ?> Departamento</h5>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="acao_form" value="salvar_depto">
          <input type="hidden" name="depto_id" value="<?= (int)($depto_editar['id'] ?? 0) ?>">

          <div class="form-group">
            <label>Nome <span class="text-danger">*</span></label>
            <input type="text" name="nome_depto" class="form-control" required value="<?= htmlspecialchars($depto_editar['nome'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($depto_editar['descricao'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Palavras-chave para transferência <small class="text-muted">(separadas por vírgula)</small></label>
            <input type="text" name="palavras_chave" class="form-control" placeholder="suporte, financeiro, reclamação" value="<?= htmlspecialchars($depto_editar['palavras_chave'] ?? '') ?>">
            <small class="text-muted">Quando o cliente digitar uma dessas palavras, será transferido para este departamento.</small>
          </div>
          <div class="form-group">
            <label>Mensagem de transferência</label>
            <textarea name="msg_transferencia" class="form-control" rows="3" placeholder="Aguarde, vou transferir para nosso time..."><?= htmlspecialchars($depto_editar['msg_transferencia'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <div class="checkbox checkbox-primary">
              <input type="checkbox" id="ativo" name="ativo" <?= (!$depto_editar || $depto_editar['ativo']) ? 'checked' : '' ?>>
              <label for="ativo">Departamento ativo</label>
            </div>
            <div class="checkbox checkbox-primary mt-1">
              <input type="checkbox" id="notificar_atendentes" name="notificar_atendentes" <?= (!$depto_editar || ($depto_editar['notificar_atendentes'] ?? 1)) ? 'checked' : '' ?>>
              <label for="notificar_atendentes">Notificar atendentes via WhatsApp ao entrar na fila</label>
            </div>
          </div>
          <button type="submit" class="btn btn-block" style="background:#FF5500;color:#fff;font-weight:600;">
            <i class="feather icon-save mr-1"></i><?= $depto_editar ? 'Salvar alterações' : 'Criar departamento' ?>
          </button>
          <?php if ($depto_editar): ?>
          <a href="departamentos.php" class="btn btn-outline-secondary btn-block mt-2">Cancelar edição</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- Lista -->
  <div class="col-md-8">
    <?php if (empty($deptos)): ?>
    <div class="card"><div class="card-body text-center text-muted py-5">
      <i class="feather icon-users" style="font-size:48px;"></i>
      <p class="mt-3">Nenhum departamento cadastrado ainda.</p>
    </div></div>
    <?php else: ?>
    <?php foreach ($deptos as $dep): ?>
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center" style="background:<?= $dep['ativo'] ? '#001f3f' : '#6c757d' ?>;color:#fff;">
        <h6 class="mb-0"><i class="feather icon-tag mr-2"></i><?= htmlspecialchars($dep['nome']) ?></h6>
        <div>
          <?php if (!$dep['ativo']): ?><span class="badge badge-warning mr-2">Inativo</span><?php endif; ?>
          <a href="departamentos.php?editar=<?= $dep['id'] ?>" class="btn btn-sm btn-light mr-1"><i class="feather icon-edit-2"></i></a>
          <form method="post" style="display:inline;" onsubmit="return confirm('Excluir departamento e desvincular todos os atendentes?')">
            <input type="hidden" name="acao_form" value="excluir_depto">
            <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
            <button class="btn btn-sm btn-danger"><i class="feather icon-trash-2"></i></button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <?php if ($dep['descricao']): ?>
        <p class="text-muted small mb-2"><?= htmlspecialchars($dep['descricao']) ?></p>
        <?php endif; ?>
        <?php if ($dep['palavras_chave']): ?>
        <p class="mb-1"><small><strong>Palavras-chave:</strong> <?= htmlspecialchars($dep['palavras_chave']) ?></small></p>
        <?php endif; ?>

        <!-- Atendentes vinculados -->
        <div class="mt-3">
          <strong>Atendentes:</strong>
          <?php if (!empty($atendentes_por_depto[$dep['id']])): ?>
          <div class="mt-1">
            <?php foreach ($atendentes_por_depto[$dep['id']] as $at_dep): ?>
            <span class="badge badge-primary mr-1 p-2">
              <?= htmlspecialchars($at_dep['nome'] ?: $at_dep['login_atendente']) ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="acao_form" value="desvincular_atendente">
                <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
                <input type="hidden" name="login_atendente" value="<?= htmlspecialchars($at_dep['login_atendente']) ?>">
                <button class="btn btn-sm p-0 ml-1 text-white" title="Remover">&times;</button>
              </form>
            </span>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <span class="text-muted small"> Nenhum atendente vinculado.</span>
          <?php endif; ?>
          <?php if (!empty($atendentes_disp)): ?>
          <form method="post" class="form-inline mt-2">
            <input type="hidden" name="acao_form" value="vincular_atendente">
            <input type="hidden" name="depto_id" value="<?= $dep['id'] ?>">
            <select name="login_atendente" class="form-control form-control-sm mr-2">
              <option value="">— Adicionar atendente —</option>
              <?php foreach ($atendentes_disp as $at): ?>
              <?php $ja_vinculado = false;
              if (!empty($atendentes_por_depto[$dep['id']])) foreach ($atendentes_por_depto[$dep['id']] as $av) if ($av['login_atendente']===$at['login']) { $ja_vinculado=true; break; }
              if (!$ja_vinculado): ?>
              <option value="<?= htmlspecialchars($at['login']) ?>"><?= htmlspecialchars($at['nome'] ?: $at['login']) ?></option>
              <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm" style="background:#FF5500;color:#fff;">Vincular</button>
          </form>
          <?php else: ?>
          <p class="text-muted small mt-1">Crie usuários com modo "MultiAtendente" para vincular aqui.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Criar atendente humano (tipo=3) -->
    <div class="card mt-2">
      <div class="card-header" style="background:#17a2b8;color:#fff;">
        <h6 class="mb-0"><i class="feather icon-user-plus mr-2"></i>Novo atendente humano</h6>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="acao_form" value="criar_atendente">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nome completo <span class="text-danger">*</span></label>
              <input type="text" name="at_nome" class="form-control" placeholder="Ex: João Silva" required>
            </div>
            <div class="form-group col-md-6">
              <label>Login <span class="text-danger">*</span></label>
              <input type="text" name="at_login" class="form-control" placeholder="Ex: joao.silva" required autocomplete="off">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Senha <span class="text-danger">*</span> <small class="text-muted">(mín. 6 chars)</small></label>
              <input type="password" name="at_senha" class="form-control" placeholder="••••••" required autocomplete="new-password">
            </div>
            <div class="form-group col-md-6">
              <label>Vincular ao departamento <small class="text-muted">(opcional)</small></label>
              <select name="at_depto" class="form-control">
                <option value="">— Nenhum agora —</option>
                <?php foreach ($deptos as $d_opt): ?>
                <option value="<?= $d_opt['id'] ?>"><?= htmlspecialchars($d_opt['nome']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-info btn-sm">
            <i class="feather icon-user-plus mr-1"></i>Criar atendente
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</div>

<?php include 'footer.php'; ?>
