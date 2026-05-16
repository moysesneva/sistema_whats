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
if ($autorizado != 2)    VaiPara('desbloquar.php');
if ($tipo != 1 && $tipo != 4) VaiPara('index.php');

include 'bloqueio.php';

// --- Ações POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ativar usuário
    if (isset($_POST['ativar_usuario'])) {
        $uid = (int)$_POST['ativar_usuario'];
        $stmt = $conn->prepare("UPDATE login SET autorizado = 2, situacao = 'ativo' WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        VaiPara('dashboard_adm.php?msg=ativado');
    }

    // Desativar usuário
    if (isset($_POST['desativar_usuario'])) {
        $uid = (int)$_POST['desativar_usuario'];
        $stmt = $conn->prepare("UPDATE login SET autorizado = 0, situacao = 'inativo' WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        VaiPara('dashboard_adm.php?msg=desativado');
    }

    // Deletar usuário
    if (isset($_POST['deletar_usuario'])) {
        $uid = (int)$_POST['deletar_usuario'];
        $stmt = $conn->prepare("DELETE FROM login WHERE id = ? AND tipo != 1");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        VaiPara('dashboard_adm.php?msg=deletado');
    }
}

// --- Contadores ---
$r_total   = $conn->query("SELECT COUNT(*) AS n FROM login WHERE tipo IN (2,3)")->fetch_assoc();
$r_ativos  = $conn->query("SELECT COUNT(*) AS n FROM login WHERE tipo IN (2,3) AND autorizado = 2")->fetch_assoc();
$r_inativos= $conn->query("SELECT COUNT(*) AS n FROM login WHERE tipo IN (2,3) AND autorizado != 2")->fetch_assoc();
$total_usuarios  = (int)($r_total['n']   ?? 0);
$total_ativos    = (int)($r_ativos['n']  ?? 0);
$total_inativos  = (int)($r_inativos['n']?? 0);

// --- Lista de usuários ---
$busca = trim($_GET['busca'] ?? '');
$pag   = max(1, (int)($_GET['pagina'] ?? 1));
$por_pag = 15;
$offset  = ($pag - 1) * $por_pag;

if ($busca !== '') {
    $like = '%' . $busca . '%';
    $stmt_count = $conn->prepare("SELECT COUNT(*) AS n FROM login WHERE tipo IN (2,3) AND (nome LIKE ? OR login LIKE ? OR email LIKE ?)");
    $stmt_count->bind_param("sss", $like, $like, $like);
    $stmt_count->execute();
    $total_lista = (int)$stmt_count->get_result()->fetch_assoc()['n'];
    $stmt_count->close();

    $stmt_list = $conn->prepare("SELECT id, nome, login, email, tipo, autorizado, situacao, plano, creditos FROM login WHERE tipo IN (2,3) AND (nome LIKE ? OR login LIKE ? OR email LIKE ?) ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt_list->bind_param("sssii", $like, $like, $like, $por_pag, $offset);
} else {
    $r_c = $conn->query("SELECT COUNT(*) AS n FROM login WHERE tipo IN (2,3)");
    $total_lista = (int)$r_c->fetch_assoc()['n'];

    $stmt_list = $conn->prepare("SELECT id, nome, login, email, tipo, autorizado, situacao, plano, creditos FROM login WHERE tipo IN (2,3) ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt_list->bind_param("ii", $por_pag, $offset);
}
$stmt_list->execute();
$result_lista = $stmt_list->get_result();
$stmt_list->close();

$total_pags = max(1, (int)ceil($total_lista / $por_pag));

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
.dash-stat-card {
    border-radius: 12px;
    padding: 24px 20px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 18px rgba(0,0,0,.12);
    margin-bottom: 20px;
}
.dash-stat-card .stat-icon {
    font-size: 40px;
    opacity: .85;
}
.dash-stat-card .stat-num {
    font-size: 38px;
    font-weight: 700;
    line-height: 1;
}
.dash-stat-card .stat-label {
    font-size: 13px;
    opacity: .88;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.card-total   { background: linear-gradient(135deg,#001f3f,#003f7f); }
.card-ativo   { background: linear-gradient(135deg,#27ae60,#1abc9c); }
.card-inativo { background: linear-gradient(135deg,#e74c3c,#c0392b); }

.action-btns { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:24px; }
.action-btns a, .action-btns button {
    padding: 9px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: opacity .2s;
}
.action-btns a:hover, .action-btns button:hover { opacity: .85; text-decoration:none; }
.btn-usr  { background:#001f3f; color:#fff; }
.btn-new  { background:#FF5500; color:#fff; }
.btn-ia   { background:#8e44ad; color:#fff; }
.btn-chat { background:#27ae60; color:#fff; }

.users-table thead th { background:#001f3f; color:#fff; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:12px 10px; }
.users-table tbody tr:hover { background:#f5f8ff; }
.users-table td { vertical-align:middle; font-size:13px; padding:10px; }
.badge-ativo   { background:#d4f5e9; color:#1a7a4a; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-inativo { background:#fde8e8; color:#c0392b; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }

.alert-dash { padding:12px 18px; border-radius:8px; margin-bottom:18px; font-size:14px; }
.alert-dash.success { background:#d4edda; color:#155724; border-left:4px solid #27ae60; }

.search-row { display:flex; gap:10px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
.search-row input { flex:1; min-width:200px; padding:8px 14px; border-radius:8px; border:1px solid #dde; font-size:13px; }
.search-row button { padding:8px 16px; border-radius:8px; background:#001f3f; color:#fff; border:none; font-size:13px; cursor:pointer; }
.search-row a { padding:8px 14px; border-radius:8px; background:#eee; color:#333; text-decoration:none; font-size:13px; }

@media(max-width:600px) {
    .dash-stat-card .stat-num { font-size:28px; }
    .dash-stat-card { padding:16px 14px; }
}
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">

<?php if (isset($_GET['msg'])): ?>
    <?php $msgs = ['ativado'=>'Usuário ativado com sucesso!','desativado'=>'Usuário desativado.','deletado'=>'Usuário deletado.']; ?>
    <div class="alert-dash success"><i class="feather icon-check-circle"></i> <?= htmlspecialchars($msgs[$_GET['msg']] ?? 'Ação realizada!', ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<!-- Cabeçalho -->
<div style="margin-bottom:20px;">
    <h4 style="color:#001f3f; font-weight:700; margin:0;"><i class="feather icon-users"></i> Admin Dashboard</h4>
    <small class="text-muted">Bem-vindo, <strong><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></strong> — visão geral do sistema</small>
</div>

<!-- Cards de contagem -->
<div class="row">
    <div class="col-md-4 col-sm-12">
        <div class="dash-stat-card card-total">
            <div class="stat-icon"><i class="feather icon-users"></i></div>
            <div>
                <div class="stat-num"><?= $total_usuarios ?></div>
                <div class="stat-label">Total de Usuários</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="dash-stat-card card-ativo">
            <div class="stat-icon"><i class="feather icon-user-check"></i></div>
            <div>
                <div class="stat-num"><?= $total_ativos ?></div>
                <div class="stat-label">Ativos</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="dash-stat-card card-inativo">
            <div class="stat-icon"><i class="feather icon-user-x"></i></div>
            <div>
                <div class="stat-num"><?= $total_inativos ?></div>
                <div class="stat-label">Inativos</div>
            </div>
        </div>
    </div>
</div>

<!-- Ações rápidas -->
<div class="action-btns">
    <a href="dashboard_adm.php" class="btn-usr"><i class="feather icon-users"></i> Usuários</a>
    <a href="criar_bot.php" class="btn-new"><i class="feather icon-user-plus"></i> Novo Usuário</a>
    <a href="ia_config.php" class="btn-ia"><i class="fa fa-brain" style="font-size:14px;"></i> IA</a>
    <a href="modo.php" class="btn-chat"><i class="feather icon-message-square"></i> Chat / Robô</a>
    <a href="config_adm.php" style="background:#34495e;color:#fff;"><i class="feather icon-settings"></i> Configurações</a>
</div>

<!-- Tabela de usuários -->
<div class="card" style="border-radius:12px; box-shadow:0 4px 18px rgba(0,0,0,.08);">
    <div class="card-header" style="background:#001f3f; color:#fff; border-radius:12px 12px 0 0; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-size:15px;"><i class="feather icon-users"></i> Gerenciar Usuários</h5>
        <a href="criar_bot.php" style="background:#FF5500; color:#fff; padding:6px 14px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600;">
            <i class="feather icon-plus"></i> Novo
        </a>
    </div>
    <div class="card-block" style="padding:16px 20px;">

        <!-- Busca -->
        <form method="GET" class="search-row">
            <input type="text" name="busca" value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por nome, telefone ou e-mail...">
            <button type="submit"><i class="feather icon-search"></i> Buscar</button>
            <?php if ($busca): ?>
                <a href="dashboard_adm.php">Limpar</a>
            <?php endif; ?>
        </form>

        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Plano</th>
                        <th>Créditos IA</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_lista->num_rows > 0):
                    while ($u = $result_lista->fetch_assoc()):
                        $ativo = ($u['autorizado'] == 2);
                        $uid   = (int)$u['id'];
                        $unome = htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8');
                        $uemail= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
                        $utel  = htmlspecialchars($u['login'], ENT_QUOTES, 'UTF-8');
                        $uplano= htmlspecialchars($u['plano'] ?? '-', ENT_QUOTES, 'UTF-8');
                        $ucred = (int)($u['creditos'] ?? 0);
                ?>
                    <tr>
                        <td><strong><?= $uid ?></strong></td>
                        <td><?= $unome ?></td>
                        <td style="font-size:12px;"><?= $uemail ?></td>
                        <td><?= $utel ?></td>
                        <td><span style="background:#eef2ff;color:#3730a3;padding:2px 8px;border-radius:4px;font-size:12px;"><?= $uplano ?></span></td>
                        <td style="text-align:center;"><?= $ucred ?></td>
                        <td>
                            <?php if ($ativo): ?>
                                <span class="badge-ativo">Ativo</span>
                            <?php else: ?>
                                <span class="badge-inativo">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                <?php if ($ativo): ?>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="desativar_usuario" value="<?= $uid ?>">
                                        <button type="submit" class="btn btn-warning btn-sm" title="Desativar" style="font-size:11px;padding:4px 8px;">
                                            <i class="feather icon-pause-circle"></i> Desativar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="ativar_usuario" value="<?= $uid ?>">
                                        <button type="submit" class="btn btn-success btn-sm" title="Ativar" style="font-size:11px;padding:4px 8px;">
                                            <i class="feather icon-play-circle"></i> Ativar
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="qrcode.php?usuario=<?= $uid ?>" class="btn btn-info btn-sm" title="QR Code" style="font-size:11px;padding:4px 8px;">
                                    <i class="fa fa-qrcode"></i> QR
                                </a>

                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="deletar_usuario" value="<?= $uid ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Deletar" style="font-size:11px;padding:4px 8px;"
                                        data-fn="__confirm" data-args="Deletar o usuário <?= $unome ?>? Esta ação não pode ser desfeita.">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:30px; color:#999;">
                            <i class="feather icon-users" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            <?= $busca ? 'Nenhum usuário encontrado para "' . htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') . '"' : 'Nenhum usuário cadastrado ainda.' ?>
                            <?php if (!$busca): ?><br><a href="criar_bot.php" style="color:#FF5500;">Cadastrar primeiro usuário</a><?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <?php if ($total_pags > 1): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-top:10px;">
            <small style="color:#999;">Mostrando <?= ($offset+1) ?>–<?= min($offset+$por_pag,$total_lista) ?> de <?= $total_lista ?> usuários</small>
            <div style="display:flex;gap:5px;">
                <?php for ($p = 1; $p <= $total_pags; $p++): ?>
                    <a href="?pagina=<?= $p ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>"
                       style="padding:5px 12px;border-radius:6px;border:1px solid #dde;font-size:13px;text-decoration:none;
                              <?= ($p == $pag) ? 'background:#001f3f;color:#fff;' : 'background:#fff;color:#333;' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

</div>

<?php include 'footer.php'; ?>
