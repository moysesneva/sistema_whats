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

// Lê o tempo de expiração de sessão configurado
$session_timeout_min = 30;
$r_cfg = $conn->query("SELECT session_timeout_min FROM config LIMIT 1");
if ($r_cfg) {
    $row_cfg = $r_cfg->fetch_assoc();
    if ($row_cfg && isset($row_cfg['session_timeout_min'])) {
        $v = (int) $row_cfg['session_timeout_min'];
        if ($v >= 5 && $v <= 480) {
            $session_timeout_min = $v;
        }
    }
}

// Lê o cooldown de limpeza configurado (coluna adicionada via banco_fix.sql)
$cleanup_cooldown_sec = max(1, (int) (getenv('CLEANUP_COOLDOWN_SECONDS') ?: 30));
$r_cc = $conn->query("SELECT cleanup_cooldown_seconds FROM config LIMIT 1");
if ($r_cc) {
    $row_cc = $r_cc->fetch_assoc();
    if ($row_cc && isset($row_cc['cleanup_cooldown_seconds'])) {
        $v_cc = (int) $row_cc['cleanup_cooldown_seconds'];
        if ($v_cc >= 5 && $v_cc <= 3600) {
            $cleanup_cooldown_sec = $v_cc;
        }
    }
}

// Lê thresholds de limpeza configurados no banco
$cleanup_db = [
    'log_max_age_days'    => null,
    'log_max_size_mb'     => null,
    'uploads_max_age_sec' => null,
    'db_failures_max_mb'  => null,
    'db_failures_max_days'=> null,
];
$r_cleanup = $conn->query(
    "SELECT cleanup_log_max_age_days, cleanup_log_max_size_mb,
            cleanup_uploads_max_age_sec,
            cleanup_db_failures_max_mb, cleanup_db_failures_max_days
     FROM config LIMIT 1"
);
if ($r_cleanup) {
    $row_cleanup = $r_cleanup->fetch_assoc();
    if ($row_cleanup) {
        $cleanup_db['log_max_age_days']    = isset($row_cleanup['cleanup_log_max_age_days'])    && $row_cleanup['cleanup_log_max_age_days']    > 0 ? (int) $row_cleanup['cleanup_log_max_age_days']    : null;
        $cleanup_db['log_max_size_mb']     = isset($row_cleanup['cleanup_log_max_size_mb'])     && $row_cleanup['cleanup_log_max_size_mb']     > 0 ? (int) $row_cleanup['cleanup_log_max_size_mb']     : null;
        $cleanup_db['uploads_max_age_sec'] = isset($row_cleanup['cleanup_uploads_max_age_sec']) && $row_cleanup['cleanup_uploads_max_age_sec'] > 0 ? (int) $row_cleanup['cleanup_uploads_max_age_sec'] : null;
        $cleanup_db['db_failures_max_mb']  = isset($row_cleanup['cleanup_db_failures_max_mb'])  && $row_cleanup['cleanup_db_failures_max_mb']  > 0 ? (int) $row_cleanup['cleanup_db_failures_max_mb']  : null;
        $cleanup_db['db_failures_max_days']= isset($row_cleanup['cleanup_db_failures_max_days'])&& $row_cleanup['cleanup_db_failures_max_days']> 0 ? (int) $row_cleanup['cleanup_db_failures_max_days']: null;
    }
}

// Valores efetivos: banco > env var > padrão embutido
// (valor salvo no painel tem prioridade sobre variável de ambiente)
function _eff_threshold(string $envKey, ?int $dbVal, int $default): int
{
    if ($dbVal !== null && $dbVal > 0)    return $dbVal;
    $env = getenv($envKey);
    if ($env !== false && (int) $env > 0) return (int) $env;
    return $default;
}

$cleanup_eff = [
    'log_max_age_days'    => _eff_threshold('LOG_MAX_AGE_DAYS',        $cleanup_db['log_max_age_days'],    7),
    'log_max_size_mb'     => _eff_threshold('LOG_MAX_SIZE_MB',         $cleanup_db['log_max_size_mb'],     10),
    'uploads_max_age_sec' => _eff_threshold('UPLOADS_MAX_AGE_SECONDS', $cleanup_db['uploads_max_age_sec'], 3600),
    'db_failures_max_mb'  => _eff_threshold('DB_FAILURES_MAX_SIZE_MB', $cleanup_db['db_failures_max_mb'],  1),
    'db_failures_max_days'=> _eff_threshold('DB_FAILURES_MAX_AGE_DAYS',$cleanup_db['db_failures_max_days'],30),
];
// -----------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------

function tamanho_dir(string $path): int
{
    if (!is_dir($path)) return 0;
    $total = 0;
    $iter  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
        $path, FilesystemIterator::SKIP_DOTS
    ));
    foreach ($iter as $f) {
        $total += $f->getSize();
    }
    return $total;
}

function tamanho_arquivo(string $path): int
{
    return (is_file($path)) ? (int) filesize($path) : 0;
}

function formatar_tamanho(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1)    . ' KB';
    return $bytes . ' B';
}

function ler_status(string $path): ?array
{
    if (!is_file($path)) return null;
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

function badge_tamanho(int $bytes, int $aviso_mb = 5, int $critico_mb = 20): string
{
    $mb = $bytes / 1048576;
    if ($mb >= $critico_mb) return 'danger';
    if ($mb >= $aviso_mb)   return 'warning';
    return 'success';
}

// -----------------------------------------------------------------------
// Caminhos
// -----------------------------------------------------------------------

$base        = __DIR__ . '/api';
$logs_dir    = $base . '/logs';
$log_proc    = $base . '/log_processamento.txt';
$log_recv    = $base . '/log_recebidos.txt';
$uploads_dir = $base . '/img';
$db_failures = __DIR__ . '/logs/db_failures.log';

$status_logs    = ler_status($base . '/status_limpar_logs.json');
$status_uploads = ler_status($base . '/status_limpar_uploads.json');

$admin_actions_log = $base . '/logs/admin_actions.log';
$admin_actions = [];
if (is_file($admin_actions_log)) {
    $linhas = file($admin_actions_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas !== false) {
        $linhas = array_reverse(array_slice($linhas, -20));
        foreach ($linhas as $linha) {
            $entry = json_decode($linha, true);
            if (is_array($entry)) {
                $admin_actions[] = $entry;
            }
        }
    }
}

// -----------------------------------------------------------------------
// Tamanhos
// -----------------------------------------------------------------------

$sz_logs_dir      = tamanho_dir($logs_dir);
$sz_log_proc      = tamanho_arquivo($log_proc);
$sz_log_recv      = tamanho_arquivo($log_recv);
$sz_uploads       = tamanho_dir($uploads_dir);
$sz_db_failures   = tamanho_arquivo($db_failures);
$sz_admin_actions = tamanho_arquivo($admin_actions_log);
$sz_total         = $sz_logs_dir + $sz_log_proc + $sz_log_recv + $sz_uploads + $sz_db_failures;
?>
<?php include 'header.php'; ?>

<style>
.stats-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.stats-card .card-header {
    background: linear-gradient(135deg, #001f3f, #003366);
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 14px 20px;
    font-weight: 600;
}
.stats-card .card-header i {
    margin-right: 8px;
    color: #FF5500;
}
.stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}
.stat-row:last-child {
    border-bottom: none;
}
.stat-label {
    color: #555;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.stat-label i {
    color: #001f3f;
    width: 16px;
}
.stat-value {
    font-weight: 600;
    font-size: 14px;
}
.total-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 14px 20px;
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.total-box .label {
    font-weight: 700;
    color: #001f3f;
    font-size: 15px;
}
.total-box .value {
    font-size: 20px;
    font-weight: 800;
    color: #FF5500;
}
.sweep-info {
    font-size: 13px;
    color: #666;
}
.sweep-info strong {
    color: #001f3f;
}
.never-ran {
    color: #aaa;
    font-style: italic;
    font-size: 13px;
}
.page-title-box {
    margin-bottom: 24px;
}
.page-title-box h4 {
    color: #001f3f;
    font-weight: 700;
}
.page-title-box p {
    color: #777;
    font-size: 14px;
    margin: 0;
}
#btn-limpar-agora {
    background: #FF5500;
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 9px 22px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background .2s;
}
#btn-limpar-agora:hover:not(:disabled) {
    background: #e04a00;
}
#btn-limpar-agora:disabled {
    opacity: .65;
    cursor: not-allowed;
}
#cleanup-result {
    font-size: 13px;
    margin-top: 10px;
}
</style>

<div class="container-fluid">
    <div class="page-title-box">
        <h4><i class="feather icon-hard-drive" style="color:#FF5500;margin-right:8px;"></i>Uso de Disco e Logs</h4>
        <p>Tamanho atual dos arquivos de log e uploads temporários, e histórico das últimas limpezas automáticas.</p>
    </div>

    <div class="row">

        <!-- Card: Logs -->
        <div class="col-md-6">
            <div class="card stats-card">
                <div class="card-header">
                    <i class="feather icon-file-text"></i> Arquivos de Log
                </div>
                <div class="card-body">
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-folder"></i> Pasta <code>logs/</code></span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_logs_dir) ?>">
                                <?= formatar_tamanho($sz_logs_dir) ?>
                            </span>
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-file"></i> log_processamento.txt</span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_log_proc) ?>">
                                <?= formatar_tamanho($sz_log_proc) ?>
                            </span>
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-file"></i> log_recebidos.txt</span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_log_recv) ?>">
                                <?= formatar_tamanho($sz_log_recv) ?>
                            </span>
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-alert-triangle"></i> db_failures.log</span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_db_failures, 1, 5) ?>">
                                <?= formatar_tamanho($sz_db_failures) ?>
                            </span>
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-clipboard"></i> admin_actions.log</span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_admin_actions, 1, 5) ?>">
                                <?= formatar_tamanho($sz_admin_actions) ?>
                            </span>
                        </span>
                    </div>

                    <hr style="margin:16px 0 12px;">

                    <?php if ($status_logs): ?>
                    <div class="sweep-info">
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-clock"></i> Última limpeza</span>
                            <strong><?= htmlspecialchars($status_logs['ultima_varredura'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-trash-2"></i> Arquivos removidos</span>
                            <strong><?= (int) $status_logs['arquivos_removidos'] ?></strong>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-scissors"></i> Truncamentos</span>
                            <strong><?= (int) $status_logs['truncamentos'] ?></strong>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-sliders"></i> Configuração (logs)</span>
                            <span class="sweep-info">
                                Máx. <?= (int) $status_logs['max_age_dias'] ?> dias &bull;
                                Máx. <?= (int) $status_logs['max_size_mb'] ?> MB por arquivo
                            </span>
                        </div>
                        <?php if (isset($status_logs['db_failures_max_size_mb'])): ?>
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-sliders"></i> Configuração (db_failures)</span>
                            <span class="sweep-info">
                                Máx. <?= (int) $status_logs['db_failures_max_age_dias'] ?> dias &bull;
                                Máx. <?= (int) $status_logs['db_failures_max_size_mb'] ?> MB
                                <?php if (!empty($status_logs['db_failures_action']) && $status_logs['db_failures_action'] !== 'none'): ?>
                                &bull; Última ação: <strong><?= htmlspecialchars($status_logs['db_failures_action'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <span class="never-ran"><i class="feather icon-info"></i> Limpeza ainda não executada neste ciclo.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card: Uploads -->
        <div class="col-md-6">
            <div class="card stats-card">
                <div class="card-header">
                    <i class="feather icon-image"></i> Uploads Temporários
                </div>
                <div class="card-body">
                    <div class="stat-row">
                        <span class="stat-label"><i class="feather icon-folder"></i> Pasta <code>img/</code></span>
                        <span class="stat-value">
                            <span class="badge badge-<?= badge_tamanho($sz_uploads, 10, 50) ?>">
                                <?= formatar_tamanho($sz_uploads) ?>
                            </span>
                        </span>
                    </div>

                    <hr style="margin:16px 0 12px;">

                    <?php if ($status_uploads): ?>
                    <div class="sweep-info">
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-clock"></i> Última limpeza</span>
                            <strong><?= htmlspecialchars($status_uploads['ultima_varredura'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-trash-2"></i> Arquivos removidos</span>
                            <strong><?= (int) $status_uploads['arquivos_removidos'] ?></strong>
                        </div>
                    </div>
                    <?php else: ?>
                    <span class="never-ran"><i class="feather icon-info"></i> Limpeza ainda não executada neste ciclo.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <!-- Totalizador -->
    <div class="card stats-card">
        <div class="card-body">
            <div class="total-box">
                <span class="label"><i class="feather icon-database" style="margin-right:6px;"></i>Espaço total monitorado</span>
                <span class="value"><?= formatar_tamanho($sz_total) ?></span>
            </div>
            <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                <i class="feather icon-info"></i>
                Os dados são lidos diretamente do sistema de arquivos. 
                Verde = abaixo do limiar &bull; Amarelo = atenção &bull; Vermelho = acima do limite configurado.
                Os contadores de limpeza são atualizados automaticamente a cada varredura.
            </p>
        </div>
    </div>

    <!-- Aviso de disco -->
    <div class="card stats-card">
        <div class="card-header">
            <i class="feather icon-bell"></i> Aviso de Uso de Disco
        </div>
        <div class="card-body">
            <p style="font-size:14px;color:#555;margin-bottom:14px;">
                Quando o aviso de disco é dispensado, ele fica oculto até o fim do dia.
                Clique abaixo para reativá-lo imediatamente — ele voltará a aparecer na
                próxima página carregada, se o uso ainda estiver acima do limiar.
            </p>
            <button id="btn-reativar-aviso" type="button"
                    style="background:#001f3f;border:none;color:#fff;font-weight:600;padding:9px 22px;border-radius:6px;cursor:pointer;font-size:14px;transition:background .2s;">
                <i class="feather icon-bell" style="margin-right:6px;"></i>Reativar aviso de disco
            </button>
            <span id="reativar-aviso-info" style="display:none;margin-left:14px;font-size:13px;color:#28a745;">
                <i class="feather icon-check-circle" style="margin-right:4px;"></i>Aviso reativado. Recarregando…
            </span>
        </div>
    </div>

    <!-- Limpeza manual -->
    <div class="card stats-card">
        <div class="card-header">
            <i class="feather icon-trash-2"></i> Limpeza Manual
        </div>
        <div class="card-body">
            <p style="font-size:14px;color:#555;margin-bottom:14px;">
                Executa imediatamente a limpeza de logs antigos e uploads temporários expirados,
                sem aguardar a próxima varredura automática.
            </p>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:14px;">
                <button id="btn-limpar-agora" type="button">
                    <i class="feather icon-zap" style="margin-right:6px;"></i>Limpar Agora
                </button>
                <span id="cooldown-info" style="font-size:13px;color:#777;">
                    <i class="feather icon-clock" style="margin-right:4px;color:#001f3f;"></i>
                    Cooldown atual: <strong id="cooldown-display"><?= (int) $cleanup_cooldown_sec ?></strong> segundo(s)
                </span>
            </div>
            <div id="cleanup-result"></div>

            <hr style="margin:16px 0 12px;">

            <p style="font-size:13px;color:#555;margin-bottom:12px;">
                <i class="feather icon-settings" style="margin-right:4px;color:#001f3f;"></i>
                <strong>Cooldown entre limpezas:</strong> intervalo mínimo (em segundos) entre duas execuções consecutivas do botão acima.
                Mínimo: <strong>5 s</strong> &bull; Máximo: <strong>3600 s (1 h)</strong>.
            </p>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <label for="cooldown-input" style="font-weight:600;color:#001f3f;font-size:14px;white-space:nowrap;">
                    Cooldown (segundos):
                </label>
                <input
                    type="number"
                    id="cooldown-input"
                    value="<?= (int) $cleanup_cooldown_sec ?>"
                    min="5"
                    max="3600"
                    step="1"
                    style="width:110px;padding:7px 12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;text-align:center;"
                >
                <button id="btn-salvar-cooldown" style="background:#FF5500;border:none;color:#fff;font-weight:600;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:14px;transition:background .2s;">
                    <i class="feather icon-save" style="margin-right:6px;"></i>Salvar
                </button>
            </div>
            <div id="cooldown-result" style="margin-top:10px;font-size:13px;"></div>
        </div>
    </div>

    <!-- Histórico de limpezas manuais -->
    <div class="card stats-card">
        <div class="card-header">
            <i class="feather icon-list"></i> Histórico de Limpezas Manuais
        </div>
        <div class="card-body">
            <?php if (empty($admin_actions)): ?>
                <span class="never-ran"><i class="feather icon-info"></i> Nenhuma limpeza manual registrada ainda.</span>
            <?php else: ?>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f4f6f9;color:#001f3f;font-weight:700;">
                                <th style="padding:8px 12px;text-align:left;border-bottom:2px solid #e0e4ea;">Data/Hora</th>
                                <th style="padding:8px 12px;text-align:left;border-bottom:2px solid #e0e4ea;">Admin</th>
                                <th style="padding:8px 12px;text-align:center;border-bottom:2px solid #e0e4ea;">Logs removidos</th>
                                <th style="padding:8px 12px;text-align:center;border-bottom:2px solid #e0e4ea;">Truncamentos</th>
                                <th style="padding:8px 12px;text-align:center;border-bottom:2px solid #e0e4ea;">Uploads removidos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admin_actions as $i => $entry): ?>
                            <tr style="background:<?= $i % 2 === 0 ? '#fff' : '#fafafa' ?>;">
                                <td style="padding:7px 12px;border-bottom:1px solid #f0f0f0;color:#555;">
                                    <i class="feather icon-clock" style="color:#001f3f;margin-right:4px;"></i>
                                    <?= htmlspecialchars($entry['ts'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:7px 12px;border-bottom:1px solid #f0f0f0;">
                                    <i class="feather icon-user" style="color:#FF5500;margin-right:4px;"></i>
                                    <strong><?= htmlspecialchars($entry['admin'] ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td style="padding:7px 12px;border-bottom:1px solid #f0f0f0;text-align:center;">
                                    <?= (int) ($entry['logs_removidos'] ?? 0) ?>
                                </td>
                                <td style="padding:7px 12px;border-bottom:1px solid #f0f0f0;text-align:center;">
                                    <?= (int) ($entry['truncamentos'] ?? 0) ?>
                                </td>
                                <td style="padding:7px 12px;border-bottom:1px solid #f0f0f0;text-align:center;">
                                    <?= (int) ($entry['uploads_removidos'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                    <i class="feather icon-info"></i> Exibindo as últimas <?= count($admin_actions) ?> limpeza(s) manual(is), em ordem decrescente.
                </p>
            <?php endif; ?>
        </div>
    </div>

</div>

    <!-- Card: Tempo de Expiração de Sessão -->
    <div class="row">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-header">
                    <i class="feather icon-clock"></i> Tempo de Expiração de Sessão
                </div>
                <div class="card-body">
                    <p style="color:#555;font-size:14px;margin-bottom:16px;">
                        Defina quantos minutos de inatividade são necessários para encerrar a sessão automaticamente.
                        Mínimo: <strong>5 min</strong> &bull; Máximo: <strong>480 min (8 h)</strong>.
                        O novo valor passa a valer a partir do próximo login.
                    </p>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <label for="session-timeout-input" style="font-weight:600;color:#001f3f;font-size:14px;white-space:nowrap;">
                            Inatividade (minutos):
                        </label>
                        <input
                            type="number"
                            id="session-timeout-input"
                            value="<?= (int) $session_timeout_min ?>"
                            min="5"
                            max="480"
                            step="1"
                            style="width:100px;padding:7px 12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;text-align:center;"
                        >
                        <button id="btn-salvar-timeout" style="background:#FF5500;border:none;color:#fff;font-weight:600;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:14px;transition:background .2s;">
                            <i class="feather icon-save" style="margin-right:6px;"></i>Salvar
                        </button>
                    </div>
                    <div id="timeout-result" style="margin-top:10px;font-size:13px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Thresholds de Limpeza -->
    <div class="row">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-header">
                    <i class="feather icon-sliders"></i> Limites de Limpeza de Log
                </div>
                <div class="card-body">
                    <p style="color:#555;font-size:14px;margin-bottom:16px;">
                        Ajuste os limites usados durante a limpeza de logs e uploads sem precisar alterar variáveis de ambiente.
                        Deixe o campo em branco para remover o override e usar a variável de ambiente (ou o padrão do sistema).
                        <br><small style="color:#888;">Prioridade: valor salvo aqui &rsaquo; variável de ambiente &rsaquo; padrão embutido.</small>
                    </p>

                    <style>
                    .threshold-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                        gap: 18px;
                        margin-bottom: 18px;
                    }
                    .threshold-item label {
                        display: block;
                        font-weight: 600;
                        color: #001f3f;
                        font-size: 13px;
                        margin-bottom: 5px;
                    }
                    .threshold-item small {
                        color: #888;
                        font-size: 12px;
                        display: block;
                        margin-top: 4px;
                    }
                    .threshold-item input[type="number"] {
                        width: 120px;
                        padding: 7px 12px;
                        border: 2px solid #e1e5e9;
                        border-radius: 6px;
                        font-size: 14px;
                        text-align: center;
                        transition: border-color .2s;
                    }
                    .threshold-item input[type="number"]:focus {
                        outline: none;
                        border-color: #FF5500;
                    }
                    </style>

                    <div class="threshold-grid">
                        <div class="threshold-item">
                            <label for="thr-log-age">Idade máxima de logs (dias)</label>
                            <input type="number" id="thr-log-age" min="1" max="365" step="1"
                                placeholder="padrão: 7"
                                value="<?= $cleanup_db['log_max_age_days'] !== null ? (int) $cleanup_db['log_max_age_days'] : '' ?>">
                            <small>Efetivo agora: <strong><?= (int) $cleanup_eff['log_max_age_days'] ?> dias</strong></small>
                        </div>
                        <div class="threshold-item">
                            <label for="thr-log-mb">Tamanho máximo por arquivo de log (MB)</label>
                            <input type="number" id="thr-log-mb" min="1" max="500" step="1"
                                placeholder="padrão: 10"
                                value="<?= $cleanup_db['log_max_size_mb'] !== null ? (int) $cleanup_db['log_max_size_mb'] : '' ?>">
                            <small>Efetivo agora: <strong><?= (int) $cleanup_eff['log_max_size_mb'] ?> MB</strong></small>
                        </div>
                        <div class="threshold-item">
                            <label for="thr-upl-sec">Validade de uploads temporários (segundos)</label>
                            <input type="number" id="thr-upl-sec" min="60" max="86400" step="60"
                                placeholder="padrão: 3600"
                                value="<?= $cleanup_db['uploads_max_age_sec'] !== null ? (int) $cleanup_db['uploads_max_age_sec'] : '' ?>">
                            <small>Efetivo agora: <strong><?= (int) $cleanup_eff['uploads_max_age_sec'] ?> s</strong></small>
                        </div>
                        <div class="threshold-item">
                            <label for="thr-dbf-mb">Tamanho máximo do db_failures.log (MB)</label>
                            <input type="number" id="thr-dbf-mb" min="1" max="500" step="1"
                                placeholder="padrão: 1"
                                value="<?= $cleanup_db['db_failures_max_mb'] !== null ? (int) $cleanup_db['db_failures_max_mb'] : '' ?>">
                            <small>Efetivo agora: <strong><?= (int) $cleanup_eff['db_failures_max_mb'] ?> MB</strong></small>
                        </div>
                        <div class="threshold-item">
                            <label for="thr-dbf-days">Idade máxima de entradas no db_failures.log (dias)</label>
                            <input type="number" id="thr-dbf-days" min="1" max="365" step="1"
                                placeholder="padrão: 30"
                                value="<?= $cleanup_db['db_failures_max_days'] !== null ? (int) $cleanup_db['db_failures_max_days'] : '' ?>">
                            <small>Efetivo agora: <strong><?= (int) $cleanup_eff['db_failures_max_days'] ?> dias</strong></small>
                        </div>
                    </div>

                    <button id="btn-salvar-thresholds" style="background:#FF5500;border:none;color:#fff;font-weight:600;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:14px;transition:background .2s;">
                        <i class="feather icon-save" style="margin-right:6px;"></i>Salvar Limites
                    </button>
                    <div id="thresholds-result" style="margin-top:10px;font-size:13px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
document.getElementById('btn-salvar-cooldown').addEventListener('click', function () {
    var btn    = this;
    var input  = document.getElementById('cooldown-input');
    var result = document.getElementById('cooldown-result');
    var val    = parseInt(input.value, 10);

    if (isNaN(val) || val < 5 || val > 3600) {
        result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Valor inválido. Informe um número entre 5 e 3600.</span>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="margin-right:6px;"></i>Salvando…';
    result.innerHTML = '';

    fetch('api/salvar_cleanup_cooldown.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ cleanup_cooldown_seconds: val })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.ok) {
            document.getElementById('cooldown-display').textContent = val;
            result.innerHTML = '<span style="color:#28a745;"><i class="feather icon-check-circle" style="margin-right:4px;"></i>' + (data.mensagem || 'Salvo com sucesso.') + '</span>';
        } else {
            result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>' + (data.erro || 'Erro ao salvar.') + '</span>';
        }
    })
    .catch(function () {
        result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Falha na comunicação com o servidor.</span>';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="feather icon-save" style="margin-right:6px;"></i>Salvar';
    });
});

document.getElementById('btn-salvar-timeout').addEventListener('click', function () {
    var btn   = this;
    var input = document.getElementById('session-timeout-input');
    var result = document.getElementById('timeout-result');
    var val = parseInt(input.value, 10);

    if (isNaN(val) || val < 5 || val > 480) {
        result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Valor inválido. Informe um número entre 5 e 480.</span>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="margin-right:6px;"></i>Salvando…';
    result.innerHTML = '';

    fetch('api/salvar_session_timeout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ session_timeout_min: val })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.ok) {
            result.innerHTML = '<span style="color:#28a745;"><i class="feather icon-check-circle" style="margin-right:4px;"></i>' + (data.mensagem || 'Salvo com sucesso.') + '</span>';
        } else {
            result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>' + (data.erro || 'Erro ao salvar.') + '</span>';
        }
    })
    .catch(function () {
        result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Falha na comunicação com o servidor.</span>';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="feather icon-save" style="margin-right:6px;"></i>Salvar';
    });
});

document.getElementById('btn-salvar-thresholds').addEventListener('click', function () {
    var btn    = this;
    var result = document.getElementById('thresholds-result');

    function parseFld(id) {
        var v = document.getElementById(id).value.trim();
        if (v === '') return null;
        var n = parseInt(v, 10);
        return isNaN(n) ? undefined : n;
    }

    var payload = {
        log_max_age_days:    parseFld('thr-log-age'),
        log_max_size_mb:     parseFld('thr-log-mb'),
        uploads_max_age_sec: parseFld('thr-upl-sec'),
        db_failures_max_mb:  parseFld('thr-dbf-mb'),
        db_failures_max_days:parseFld('thr-dbf-days')
    };

    for (var k in payload) {
        if (payload[k] === undefined) {
            result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Valor inválido em um dos campos. Use apenas números inteiros ou deixe em branco.</span>';
            return;
        }
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="margin-right:6px;"></i>Salvando…';
    result.innerHTML = '';

    fetch('api/salvar_cleanup_thresholds.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.ok) {
            result.innerHTML = '<span style="color:#28a745;"><i class="feather icon-check-circle" style="margin-right:4px;"></i>' + (data.mensagem || 'Salvo com sucesso.') + '</span>';
            setTimeout(function () { location.reload(); }, 1200);
        } else {
            result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>' + (data.erro || 'Erro ao salvar.') + '</span>';
        }
    })
    .catch(function () {
        result.innerHTML = '<span style="color:#dc3545;"><i class="feather icon-alert-circle" style="margin-right:4px;"></i>Falha na comunicação com o servidor.</span>';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="feather icon-save" style="margin-right:6px;"></i>Salvar Limites';
    });
});

document.getElementById('btn-limpar-agora').addEventListener('click', function () {
    var btn = this;
    var result = document.getElementById('cleanup-result');

    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="margin-right:6px;"></i>Limpando…';
    result.innerHTML = '';

    fetch('api/run_cleanup.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(function (res) {
        return res.json().then(function (data) {
            return { status: res.status, data: data };
        });
    })
    .then(function (resp) {
        var status = resp.status;
        var data   = resp.data;

        if (status === 429) {
            result.innerHTML =
                '<span style="color:#e07800;">' +
                '<i class="feather icon-clock" style="margin-right:4px;"></i>' +
                (data.erro || 'Aguarde antes de executar novamente.') +
                '</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="feather icon-zap" style="margin-right:6px;"></i>Limpar Agora';
            return;
        }

        if (data.ok) {
            result.innerHTML =
                '<span style="color:#28a745;">' +
                '<i class="feather icon-check-circle" style="margin-right:4px;"></i>' +
                'Limpeza concluída em ' + data.ts + '. ' +
                'Logs removidos: <strong>' + data.logs_removidos + '</strong> &bull; ' +
                'Truncamentos: <strong>' + data.truncamentos + '</strong> &bull; ' +
                'Uploads removidos: <strong>' + data.uploads_removidos + '</strong>.' +
                '</span>';
            setTimeout(function () { location.reload(); }, 1500);
        } else {
            result.innerHTML =
                '<span style="color:#dc3545;">' +
                '<i class="feather icon-alert-circle" style="margin-right:4px;"></i>' +
                (data.erro || 'Erro desconhecido.') +
                '</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="feather icon-zap" style="margin-right:6px;"></i>Limpar Agora';
        }
    })
    .catch(function () {
        result.innerHTML =
            '<span style="color:#dc3545;">' +
            '<i class="feather icon-alert-circle" style="margin-right:4px;"></i>' +
            'Falha na comunicação com o servidor.' +
            '</span>';
        btn.disabled = false;
        btn.innerHTML = '<i class="feather icon-zap" style="margin-right:6px;"></i>Limpar Agora';
    });
});

(function () {
    var KEY = 'disk_warn_dismissed_until';
    var btn  = document.getElementById('btn-reativar-aviso');
    var info = document.getElementById('reativar-aviso-info');
    if (!btn) return;

    function isDismissed() {
        try {
            var until = parseInt(localStorage.getItem(KEY), 10);
            return !isNaN(until) && Date.now() < until;
        } catch (e) { return false; }
    }

    if (!isDismissed()) {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.title = 'O aviso já está ativo (não foi dispensado).';
    }

    btn.addEventListener('click', function () {
        try { localStorage.removeItem(KEY); } catch (e) {}
        btn.disabled = true;
        info.style.display = 'inline';
        setTimeout(function () { location.reload(); }, 1000);
    });
})();
</script>

<?php include 'footer.php'; ?>
