<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];

include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

$stmt = $conn->prepare("SELECT tipo, autorizado FROM login WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r || $r['autorizado'] != 2) {
    VaiPara('desbloquar.php');
}

$tipo = $r['tipo'];

if (!in_array($tipo, [1, 4])) {
    VaiPara('index.php');
}

include 'menu.php';
include 'bloqueio.php';

// -----------------------------------------------------------------------
// CSRF token para proteção do formulário de limpeza
// -----------------------------------------------------------------------

if (empty($_SESSION['csrf_dbdiag'])) {
    $_SESSION['csrf_dbdiag'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['csrf_dbdiag'];

// -----------------------------------------------------------------------
// Limpar log se solicitado
// -----------------------------------------------------------------------

$log_file  = __DIR__ . '/logs/db_failures.log';
$msg_acao  = '';

if (
    isset($_POST['limpar_log']) && $_POST['limpar_log'] === '1' &&
    isset($_POST['csrf_token']) && hash_equals($csrf_token, $_POST['csrf_token'])
) {
    if (is_file($log_file)) {
        file_put_contents($log_file, '');
        $msg_acao = 'Log limpo com sucesso.';
    }
}

// -----------------------------------------------------------------------
// Leitura do log
// -----------------------------------------------------------------------

$entradas     = [];
$total_erros  = 0;
$erros_1h     = 0;
$erros_24h    = 0;
$agora        = time();

if (is_file($log_file)) {
    $linhas = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($linhas) as $linha) {
        $obj = json_decode($linha, true);
        if (!is_array($obj)) continue;
        $entradas[]  = $obj;
        $total_erros++;
        $ts = isset($obj['ts']) ? strtotime($obj['ts']) : 0;
        if ($ts && ($agora - $ts) <= 3600)  $erros_1h++;
        if ($ts && ($agora - $ts) <= 86400) $erros_24h++;
    }
}

$exibir = array_slice($entradas, 0, 100);

// -----------------------------------------------------------------------
// Rótulos amigáveis
// -----------------------------------------------------------------------

function tipo_label(string $tipo): string
{
    $mapa = [
        'banco_externo' => 'Banco externo',
        'vars_ausentes' => 'Variáveis ausentes',
        'local'         => 'MySQL local',
        'sem_conexao'   => 'Sem conexão',
    ];
    return $mapa[$tipo] ?? htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8');
}

function ambiente_badge(string $amb): string
{
    return $amb === 'externo'
        ? '<span class="badge badge-warning">externo</span>'
        : '<span class="badge badge-secondary">local</span>';
}

function tipo_badge_class(string $tipo): string
{
    if ($tipo === 'sem_conexao') return 'danger';
    if ($tipo === 'banco_externo') return 'warning';
    return 'secondary';
}
?>
<?php include 'header.php'; ?>

<style>
.diag-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.diag-card .card-header {
    background: linear-gradient(135deg, #001f3f, #003366);
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 14px 20px;
    font-weight: 600;
}
.diag-card .card-header i {
    margin-right: 8px;
    color: #FF5500;
}
.page-title-box { margin-bottom: 24px; }
.page-title-box h4 { color: #001f3f; font-weight: 700; }
.page-title-box p  { color: #777; font-size: 14px; margin: 0; }
.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px 20px;
    text-align: center;
}
.stat-box .stat-num {
    font-size: 28px;
    font-weight: 800;
    color: #001f3f;
}
.stat-box .stat-num.text-danger  { color: #dc3545 !important; }
.stat-box .stat-num.text-warning { color: #ffc107 !important; }
.stat-box .stat-label {
    font-size: 12px;
    color: #777;
    margin-top: 4px;
}
.log-table td, .log-table th { font-size: 13px; vertical-align: middle; }
.log-table .msg-cell { max-width: 420px; word-break: break-word; }
.no-failures { text-align: center; padding: 40px 20px; color: #aaa; }
.no-failures i { font-size: 40px; color: #ccc; display: block; margin-bottom: 12px; }
</style>

<div class="container-fluid">
    <div class="page-title-box">
        <h4><i class="feather icon-database" style="color:#FF5500;margin-right:8px;"></i>Diagnóstico de Conexão ao Banco</h4>
        <p>Histórico de falhas ao conectar ao banco de dados (banco externo offline, socket local ausente, variáveis ausentes).</p>
    </div>

    <?php if ($msg_acao): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="feather icon-check-circle" style="margin-right:6px;"></i>
        <?= htmlspecialchars($msg_acao, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>

    <!-- Contadores -->
    <div class="row mb-3">
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num <?= $erros_1h > 0 ? 'text-danger' : '' ?>"><?= $erros_1h ?></div>
                <div class="stat-label"><i class="feather icon-clock"></i> Última 1 hora</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num <?= $erros_24h > 0 ? 'text-warning' : '' ?>"><?= $erros_24h ?></div>
                <div class="stat-label"><i class="feather icon-calendar"></i> Últimas 24 horas</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num"><?= $total_erros ?></div>
                <div class="stat-label"><i class="feather icon-list"></i> Total registrado</div>
            </div>
        </div>
    </div>

    <!-- Tabela de falhas -->
    <div class="card diag-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="feather icon-alert-triangle"></i> Falhas registradas
                <?php if ($total_erros > 100): ?>
                    <small style="font-weight:400;opacity:.8;">(exibindo as 100 mais recentes de <?= $total_erros ?>)</small>
                <?php endif; ?>
            </span>
            <?php if ($total_erros > 0): ?>
            <form method="post" style="margin:0;" onsubmit="return confirm('Limpar todo o histórico de falhas?');">
                <input type="hidden" name="limpar_log" value="1">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="feather icon-trash-2"></i> Limpar log
                </button>
            </form>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($exibir)): ?>
            <div class="no-failures">
                <i class="feather icon-check-circle"></i>
                Nenhuma falha de conexão registrada. Tudo certo!
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover log-table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Data/hora</th>
                            <th>Tipo</th>
                            <th>Ambiente</th>
                            <th class="msg-cell">Mensagem</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($exibir as $e): ?>
                        <tr>
                            <td style="white-space:nowrap;">
                                <?= htmlspecialchars($e['ts'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= tipo_badge_class($e['tipo'] ?? '') ?>">
                                    <?= tipo_label($e['tipo'] ?? '-') ?>
                                </span>
                            </td>
                            <td><?= ambiente_badge($e['ambiente'] ?? '') ?></td>
                            <td class="msg-cell">
                                <?= htmlspecialchars($e['mensagem'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <p class="text-muted" style="font-size:12px;">
        <i class="feather icon-info"></i>
        As falhas são registradas automaticamente em <code>logs/db_failures.log</code> sempre que o sistema tenta conectar ao banco e não obtém sucesso.
        O log é exibido em ordem mais recente primeiro.
    </p>
</div>

<?php include 'footer.php'; ?>
