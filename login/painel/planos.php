<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) { VaiPara('login.php'); }
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

$msg_ok  = '';
$msg_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];

        if ($acao === 'adicionar') {
            $nome_plano   = trim($_POST['nome_plano'] ?? '');
            $nome_modulo  = trim($_POST['nome_modulo'] ?? '');
            $tipo_modulo  = (int)($_POST['tipo_modulo'] ?? 1);

            if ($nome_plano && $nome_modulo) {
                $existe = $conn->prepare("SELECT id FROM planos_clientes WHERE nome_plano=? AND nome_modulo=? AND tipo=?");
                $existe->bind_param("ssi", $nome_plano, $nome_modulo, $tipo_modulo);
                $existe->execute();
                $existe->store_result();
                if ($existe->num_rows > 0) {
                    $msg_err = 'Este módulo já existe neste plano.';
                } else {
                    $ins = $conn->prepare("INSERT INTO planos_clientes (nome_plano, nome_modulo, tipo) VALUES (?, ?, ?)");
                    $ins->bind_param("ssi", $nome_plano, $nome_modulo, $tipo_modulo);
                    if ($ins->execute()) {
                        $msg_ok = 'Módulo adicionado com sucesso!';
                    } else {
                        $msg_err = 'Erro ao adicionar: ' . $conn->error;
                    }
                    $ins->close();
                }
                $existe->close();
            } else {
                $msg_err = 'Preencha todos os campos.';
            }
        }

        if ($acao === 'excluir' && isset($_POST['id'])) {
            $del_id = (int)$_POST['id'];
            $del = $conn->prepare("DELETE FROM planos_clientes WHERE id=?");
            $del->bind_param("i", $del_id);
            if ($del->execute()) {
                $msg_ok = 'Módulo removido.';
            } else {
                $msg_err = 'Erro ao remover.';
            }
            $del->close();
        }
    }
}

// Busca todos os planos agrupados
$res_planos = $conn->query("SELECT * FROM planos_clientes ORDER BY nome_plano ASC, tipo DESC, nome_modulo ASC");
$planos_agrupados = [];
if ($res_planos) {
    while ($row = $res_planos->fetch_assoc()) {
        $planos_agrupados[$row['nome_plano']][] = $row;
    }
}

// Busca planos distintos para o select
$res_planos_dist = $conn->query("SELECT DISTINCT nome_plano FROM planos_clientes ORDER BY nome_plano ASC");
$planos_list = [];
if ($res_planos_dist) {
    while ($r = $res_planos_dist->fetch_assoc()) {
        $planos_list[] = $r['nome_plano'];
    }
}

// Busca usuários e seus planos para referência
$res_usuarios = $conn->query("SELECT login, nome, plano, funcao FROM login WHERE tipo=2 ORDER BY nome ASC");

include 'header.php';
?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Planos & Módulos</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_adm.php">Início</a></li>
                    <li class="breadcrumb-item active">Planos</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <?php if ($msg_ok): ?>
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show">
            <i class="feather icon-check-circle"></i> <?= htmlspecialchars($msg_ok) ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($msg_err): ?>
    <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="feather icon-alert-circle"></i> <?= htmlspecialchars($msg_err) ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Adicionar módulo -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header" style="background:#001f3f;color:#fff;">
                <h5 style="color:#fff;margin:0;"><i class="feather icon-plus-circle"></i> Adicionar Módulo ao Plano</h5>
            </div>
            <div class="card-block">
                <form method="post" action="">
                    <input type="hidden" name="acao" value="adicionar">
                    <div class="form-group">
                        <label>Nome do Plano</label>
                        <input type="text" name="nome_plano" class="form-control"
                               placeholder="ex: plano1"
                               list="lista_planos" required>
                        <datalist id="lista_planos">
                            <?php foreach ($planos_list as $p): ?>
                            <option value="<?= htmlspecialchars($p) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label>Nome do Módulo / Função</label>
                        <input type="text" name="nome_modulo" class="form-control"
                               placeholder="ex: Agendamento, Atendimento, MultiAtendente"
                               list="lista_modulos" required>
                        <datalist id="lista_modulos">
                            <option value="Agendamento">
                            <option value="Atendimento">
                            <option value="MultiAtendente">
                            <option value="Credito 100">
                            <option value="Credito 500">
                            <option value="Ilimitado">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo_modulo" class="form-control">
                            <option value="1">Função (1) — aparece como opção de função do usuário</option>
                            <option value="0">Crédito/Add-on (0) — crédito ou item extra</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="feather icon-save"></i> Salvar
                    </button>
                </form>
            </div>
        </div>

        <!-- Usuários por plano -->
        <div class="card" style="margin-top:16px;">
            <div class="card-header" style="background:#001f3f;color:#fff;">
                <h5 style="color:#fff;margin:0;"><i class="feather icon-users"></i> Usuários por Plano</h5>
            </div>
            <div class="card-block" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr><th>Login</th><th>Plano</th><th>Função</th></tr>
                        </thead>
                        <tbody>
                        <?php if ($res_usuarios && $res_usuarios->num_rows > 0):
                            while ($u = $res_usuarios->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['login']) ?></td>
                                <td><?= htmlspecialchars($u['plano'] ?: '—') ?></td>
                                <td><span class="badge badge-secondary"><?= htmlspecialchars($u['funcao'] ?: '—') ?></span></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="3" class="text-center text-muted">Nenhum usuário</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de planos agrupados -->
    <div class="col-md-8">
        <?php if (empty($planos_agrupados)): ?>
        <div class="card">
            <div class="card-block text-center text-muted" style="padding:40px;">
                <i class="feather icon-inbox" style="font-size:48px;opacity:.3;"></i>
                <p style="margin-top:12px;">Nenhum plano cadastrado ainda.</p>
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($planos_agrupados as $nome_plano => $modulos): ?>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header" style="background:#f0f4f8;border-left:4px solid #FF5500;">
                <h5 style="margin:0;color:#001f3f;">
                    <i class="feather icon-package"></i>
                    <?= htmlspecialchars(strtoupper($nome_plano)) ?>
                    <span class="badge badge-secondary" style="font-size:12px;"><?= count($modulos) ?> módulo(s)</span>
                </h5>
            </div>
            <div class="card-block" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>#</th>
                                <th>Módulo / Função</th>
                                <th>Tipo</th>
                                <th>Adicionado em</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($modulos as $mod): ?>
                        <tr>
                            <td><?= $mod['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($mod['nome_modulo']) ?></strong>
                            </td>
                            <td>
                                <?php if ($mod['tipo'] == 1): ?>
                                    <span class="badge badge-primary">Função</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Crédito/Add-on</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($mod['date'])) ?></td>
                            <td>
                                <form method="post" action="" style="display:inline;"
                                      onsubmit="return confirm('Remover este módulo do plano?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $mod['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>
