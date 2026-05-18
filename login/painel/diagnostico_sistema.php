<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

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
// APP_ENV / modo de operação
// -----------------------------------------------------------------------

$_app_env       = getenv('APP_ENV') ?: '';
$_env_lower     = strtolower($_app_env);
$_is_dev        = ($_env_lower === 'dev' || $_env_lower === 'development');
$_log_errors    = filter_var(ini_get('log_errors'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
$_log_file      = ini_get('error_log');
if (empty($_log_file)) {
    $_log_file = getenv('PHP_ERROR_LOG') ?: '/tmp/php_errors.log';
}

// -----------------------------------------------------------------------
// Status do token de webhook
// -----------------------------------------------------------------------

$_token_val    = getenv('API_WEBHOOK_TOKEN');
$_token_ok     = ($_token_val !== false && $_token_val !== '');

// -----------------------------------------------------------------------
// Log de erros PHP
// -----------------------------------------------------------------------

$_php_log_existe = !$_is_dev && is_file($_log_file);
$_php_log_tam    = $_php_log_existe ? (int) filesize($_log_file) : 0;
$_php_log_linhas = [];
$_php_log_max    = 30;

if ($_php_log_existe) {
    $todas = file($_log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($todas !== false && count($todas) > 0) {
        $_php_log_linhas = array_slice($todas, -$_php_log_max);
    }
    unset($todas);
}

// -----------------------------------------------------------------------
// Log de falhas de banco
// -----------------------------------------------------------------------

$db_log_file  = __DIR__ . '/logs/db_failures.log';
$db_entradas  = [];
$db_total     = 0;
$db_erros_1h  = 0;
$db_erros_24h = 0;
$agora        = time();

if (is_file($db_log_file)) {
    $linhas = file($db_log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas !== false) {
        foreach (array_reverse($linhas) as $linha) {
            $obj = json_decode($linha, true);
            if (!is_array($obj)) continue;
            $db_entradas[] = $obj;
            $db_total++;
            $ts = isset($obj['ts']) ? strtotime($obj['ts']) : 0;
            if ($ts && ($agora - $ts) <= 3600)  $db_erros_1h++;
            if ($ts && ($agora - $ts) <= 86400) $db_erros_24h++;
        }
    }
}
$db_recentes = array_slice($db_entradas, 0, 5);

// -----------------------------------------------------------------------
// Log de acessos bloqueados
// -----------------------------------------------------------------------

$auth_log      = __DIR__ . '/logs/auth_blocked.log';
$api_log       = __DIR__ . '/logs/api_blocked.log';
$notfound_log  = __DIR__ . '/logs/not_found.log';

function _contar_log_recente(string $path, int $horas): int
{
    if (!is_file($path)) return 0;
    $limite = time() - ($horas * 3600);
    $n = 0;
    $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$linhas) return 0;
    foreach ($linhas as $linha) {
        $obj = json_decode($linha, true);
        if (!is_array($obj)) continue;
        $ts = isset($obj['ts']) ? strtotime($obj['ts']) : 0;
        if ($ts && $ts >= $limite) $n++;
    }
    return $n;
}

$bloqueados_1h  = _contar_log_recente($auth_log, 1)
                + _contar_log_recente($api_log, 1)
                + _contar_log_recente($notfound_log, 1);
$bloqueados_24h = _contar_log_recente($auth_log, 24)
                + _contar_log_recente($api_log, 24)
                + _contar_log_recente($notfound_log, 24);

// -----------------------------------------------------------------------
// Disco / tamanhos de arquivos
// -----------------------------------------------------------------------

function _sz_dir(string $path): int
{
    if (!is_dir($path)) return 0;
    $total = 0;
    $iter  = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $f) { $total += $f->getSize(); }
    return $total;
}

function _sz_file(string $path): int
{
    return is_file($path) ? (int) filesize($path) : 0;
}

function _fmt_sz(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1)    . ' KB';
    return $bytes . ' B';
}

$api_base     = __DIR__ . '/api';
$sz_logs_dir  = _sz_dir($api_base . '/logs');
$sz_db_fail   = _sz_file($db_log_file);
$sz_log_proc  = _sz_file($api_base . '/log_processamento.txt');
$sz_log_recv  = _sz_file($api_base . '/log_recebidos.txt');
$sz_uploads   = _sz_dir($api_base . '/img');
$sz_php_log   = $_php_log_tam;
$sz_total     = $sz_logs_dir + $sz_db_fail + $sz_log_proc + $sz_log_recv + $sz_uploads + $sz_php_log;

// -----------------------------------------------------------------------
// Variáveis de ambiente
// -----------------------------------------------------------------------

$_env_groups = [
    'Banco de Dados' => [
        ['name' => 'DB_HOST',    'required' => false, 'secret' => false, 'default' => 'localhost',           'desc' => 'Host do MySQL'],
        ['name' => 'DB_USER',    'required' => false, 'secret' => false, 'default' => 'root',                'desc' => 'Usuário do banco'],
        ['name' => 'DB_PASS',    'required' => false, 'secret' => true,  'default' => null,                  'desc' => 'Senha do banco'],
        ['name' => 'DB_NAME',    'required' => false, 'secret' => false, 'default' => 'agendamento',         'desc' => 'Nome do banco'],
    ],
    'Aplicação' => [
        ['name' => 'APP_ENV',           'required' => false, 'secret' => false, 'default' => null,                   'desc' => 'Ambiente (dev / produção)'],
        ['name' => 'PHP_ERROR_LOG',     'required' => false, 'secret' => false, 'default' => '/tmp/php_errors.log',  'desc' => 'Caminho do log de erros PHP'],
        ['name' => 'API_WEBHOOK_TOKEN', 'required' => true,  'secret' => true,  'default' => null,                   'desc' => 'Token secreto da API / webhook'],
    ],
    'Limpeza de Logs e Uploads' => [
        ['name' => 'LOG_MAX_AGE_DAYS',         'required' => false, 'secret' => false, 'default' => '7',    'desc' => 'Idade máxima dos logs (dias)'],
        ['name' => 'LOG_MAX_SIZE_MB',          'required' => false, 'secret' => false, 'default' => '10',   'desc' => 'Tamanho máximo do log de erros (MB)'],
        ['name' => 'CLEANUP_COOLDOWN_SECONDS', 'required' => false, 'secret' => false, 'default' => '30',   'desc' => 'Intervalo mínimo entre limpezas (s)'],
        ['name' => 'UPLOADS_MAX_AGE_SECONDS',  'required' => false, 'secret' => false, 'default' => '3600', 'desc' => 'Idade máxima de uploads temporários (s)'],
        ['name' => 'DB_FAILURES_MAX_SIZE_MB',  'required' => false, 'secret' => false, 'default' => '1',    'desc' => 'Tamanho máximo do log de falhas do banco (MB)'],
        ['name' => 'DB_FAILURES_MAX_AGE_DAYS', 'required' => false, 'secret' => false, 'default' => '30',   'desc' => 'Idade máxima do log de falhas do banco (dias)'],
    ],
];

function _env_status(string $name, bool $required, ?string $default): string
{
    $val = getenv($name);
    if ($val !== false && $val !== '') return 'set';
    if (!$required && $default !== null) return 'default';
    return 'missing';
}

// Conta quantas vars obrigatórias estão faltando
$_env_missing_count = 0;
foreach ($_env_groups as $_grp) {
    foreach ($_grp as $_v) {
        if (_env_status($_v['name'], $_v['required'], $_v['default']) === 'missing' && $_v['required']) {
            $_env_missing_count++;
        }
    }
}

// -----------------------------------------------------------------------
// CSRF token para ações
// -----------------------------------------------------------------------

if (empty($_SESSION['csrf_diagsys'])) {
    $_SESSION['csrf_diagsys'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['csrf_diagsys'];

// -----------------------------------------------------------------------
// Ação: limpar log de falhas de banco
// -----------------------------------------------------------------------

$msg_acao = '';

if (
    isset($_POST['limpar_db_log']) && $_POST['limpar_db_log'] === '1' &&
    isset($_POST['csrf_token']) && hash_equals($csrf_token, $_POST['csrf_token'])
) {
    if (is_file($db_log_file)) {
        file_put_contents($db_log_file, '');
        $msg_acao = 'Log de falhas de banco limpo com sucesso.';
        $db_total = $db_erros_1h = $db_erros_24h = 0;
        $db_recentes = [];
    }
}

// -----------------------------------------------------------------------
// Ação: limpar log de erros PHP
// -----------------------------------------------------------------------

$msg_acao_php = '';

if (
    !$_is_dev &&
    isset($_POST['limpar_log_php']) && $_POST['limpar_log_php'] === '1' &&
    isset($_POST['csrf_token']) && hash_equals($csrf_token, $_POST['csrf_token'])
) {
    if (is_file($_log_file)) {
        $fp = fopen($_log_file, 'a');
        if ($fp !== false) {
            if (flock($fp, LOCK_EX)) {
                ftruncate($fp, 0);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
        $msg_acao_php = 'Log de erros PHP limpo com sucesso.';
        $_php_log_linhas = [];
        $_php_log_tam    = 0;
    }
}
?>
<?php include 'header.php'; ?>

<style>
/* -----------------------------------------------------------------------
   Diagnóstico do Sistema — estilos
   ----------------------------------------------------------------------- */
.ds-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.ds-card .card-header {
    background: linear-gradient(135deg, #001f3f, #003366);
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 13px 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.ds-card .card-header i {
    color: #FF5500;
    margin-right: 6px;
}
.ds-card .card-header .card-header-title {
    display: flex;
    align-items: center;
}
.page-title-box { margin-bottom: 24px; }
.page-title-box h4 { color: #001f3f; font-weight: 700; }
.page-title-box p  { color: #777; font-size: 14px; margin: 0; }

/* Indicadores de saúde */
.health-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 0;
}
.health-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px 14px 12px;
    text-align: center;
    border: 2px solid transparent;
    transition: border-color .15s;
}
.health-item.ok    { border-color: #d4edda; }
.health-item.warn  { border-color: #fff3cd; }
.health-item.error { border-color: #f5c6cb; }
.health-icon {
    font-size: 28px;
    margin-bottom: 6px;
    display: block;
}
.health-icon.ok    { color: #28a745; }
.health-icon.warn  { color: #ffc107; }
.health-icon.error { color: #dc3545; }
.health-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #777;
    margin-bottom: 4px;
}
.health-value {
    font-size: 15px;
    font-weight: 700;
    color: #001f3f;
}
.health-value.ok    { color: #1a7431; }
.health-value.warn  { color: #856404; }
.health-value.error { color: #721c24; }

/* Tabelas */
.ds-table td, .ds-table th { font-size: 13px; vertical-align: middle; }
.ds-table .msg-cell { max-width: 380px; word-break: break-word; }

/* Env vars */
.env-group { border-bottom: 1px solid #f0f0f0; }
.env-group:last-child { border-bottom: none; }
.env-group-title {
    background: #f4f6f9;
    padding: 7px 16px;
    font-size: 11px;
    font-weight: 700;
    color: #001f3f;
    text-transform: uppercase;
    letter-spacing: .6px;
    border-bottom: 1px solid #e8eaf0;
}
.env-table td, .env-table th { font-size: 12px; vertical-align: middle; padding: 6px 12px; }
.env-name  { background: #f0f4f8; color: #001f3f; font-size: 11px; padding: 2px 5px; border-radius: 3px; }
.env-value { background: #f8f9fa; color: #333;    font-size: 11px; padding: 2px 5px; border-radius: 3px; word-break: break-all; }

/* Log PHP */
.php-log-pre {
    background: #0d1117;
    color: #e6edf3;
    font-size: 11px;
    line-height: 1.6;
    padding: 14px 16px;
    border-radius: 0 0 10px 10px;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
    max-height: 360px;
    overflow-y: auto;
}

/* Linha de disco */
.disk-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}
.disk-row:last-child { border-bottom: none; }
.disk-row .disk-label { color: #555; display: flex; align-items: center; gap: 6px; }
.disk-row .disk-label i { color: #001f3f; width: 16px; }

/* Links de atalho */
.shortcut-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #FF5500;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}
.shortcut-link:hover { color: #c94000; text-decoration: underline; }

/* Stat simples */
.stat-num {
    font-size: 26px;
    font-weight: 800;
    color: #001f3f;
    line-height: 1;
}
.stat-num.text-danger  { color: #dc3545 !important; }
.stat-num.text-warning { color: #ffc107 !important; }
.stat-sub { font-size: 12px; color: #777; margin-top: 4px; }
</style>

<div class="container-fluid">

    <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
        <div>
            <h4><i class="feather icon-activity" style="color:#FF5500;margin-right:8px;"></i>Diagnóstico do Sistema</h4>
            <p>Visão consolidada da saúde do ambiente: banco, logs, disco, autenticação e variáveis de configuração.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="db_diagnostics.php" class="btn btn-sm btn-outline-secondary">
                <i class="feather icon-database" style="margin-right:4px;"></i>Detalhes do banco
            </a>
            <a href="disk_stats.php" class="btn btn-sm btn-outline-secondary">
                <i class="feather icon-hard-drive" style="margin-right:4px;"></i>Uso de disco
            </a>
            <a href="auth_log.php" class="btn btn-sm btn-outline-secondary">
                <i class="feather icon-shield" style="margin-right:4px;"></i>Log de acessos
            </a>
        </div>
    </div>

    <?php if ($msg_acao): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="feather icon-check-circle" style="margin-right:6px;"></i>
        <?= htmlspecialchars($msg_acao, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>

    <?php if ($msg_acao_php): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="feather icon-check-circle" style="margin-right:6px;"></i>
        <?= htmlspecialchars($msg_acao_php, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>

    <!-- ================================================================
         PAINEL DE SAÚDE — indicadores rápidos
         ================================================================ -->
    <div class="card ds-card mb-4">
        <div class="card-header">
            <span class="card-header-title"><i class="feather icon-activity"></i> Painel de Saúde</span>
            <small style="font-weight:400;opacity:.75;font-size:12px;">
                Atualizado em <?= date('d/m/Y H:i:s') ?>
            </small>
        </div>
        <div class="card-body">
            <div class="health-grid">

                <?php
                // APP_ENV
                if ($_is_dev):
                    $hcls = 'warn'; $icls = 'warn';
                    $hval = htmlspecialchars($_app_env ?: 'dev', ENT_QUOTES, 'UTF-8');
                elseif ($_app_env !== ''):
                    $hcls = 'ok'; $icls = 'ok';
                    $hval = htmlspecialchars($_app_env, ENT_QUOTES, 'UTF-8');
                else:
                    $hcls = 'ok'; $icls = 'ok';
                    $hval = 'Produção';
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $icls ?> feather icon-settings"></span>
                    <div class="health-label">APP_ENV</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Webhook token
                $hcls = $_token_ok ? 'ok' : 'error';
                $hval = $_token_ok ? 'Configurado' : 'Ausente';
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-key"></span>
                    <div class="health-label">Token Webhook</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Falhas de banco (última hora)
                if ($db_erros_1h === 0):
                    $hcls = 'ok'; $hval = 'Sem falhas';
                elseif ($db_erros_1h < 5):
                    $hcls = 'warn'; $hval = $db_erros_1h . ' na 1ª hora';
                else:
                    $hcls = 'error'; $hval = $db_erros_1h . ' na 1ª hora';
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-database"></span>
                    <div class="health-label">Falhas de Banco</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Log PHP
                if ($_is_dev):
                    $hcls = 'warn'; $hval = 'Modo dev (stderr)';
                elseif (!$_php_log_existe || $_php_log_tam === 0):
                    $hcls = 'ok'; $hval = 'Sem erros';
                elseif ($_php_log_tam < 512 * 1024):
                    $hcls = 'warn'; $hval = _fmt_sz($_php_log_tam);
                else:
                    $hcls = 'error'; $hval = _fmt_sz($_php_log_tam);
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-file-text"></span>
                    <div class="health-label">Log de Erros PHP</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Acessos bloqueados (1h)
                if ($bloqueados_1h === 0):
                    $hcls = 'ok'; $hval = 'Nenhum';
                elseif ($bloqueados_1h < 5):
                    $hcls = 'warn'; $hval = $bloqueados_1h . ' (1h)';
                else:
                    $hcls = 'error'; $hval = $bloqueados_1h . ' (1h)';
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-shield-off"></span>
                    <div class="health-label">Bloqueios</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Disco
                $disco_mb = $sz_total / 1048576;
                if ($disco_mb < 5):
                    $hcls = 'ok'; $hval = _fmt_sz($sz_total);
                elseif ($disco_mb < 20):
                    $hcls = 'warn'; $hval = _fmt_sz($sz_total);
                else:
                    $hcls = 'error'; $hval = _fmt_sz($sz_total);
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-hard-drive"></span>
                    <div class="health-label">Disco (logs + uploads)</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

                <?php
                // Variáveis de ambiente ausentes
                if ($_env_missing_count === 0):
                    $hcls = 'ok'; $hval = 'Todas OK';
                else:
                    $hcls = 'error'; $hval = $_env_missing_count . ' ausente(s)';
                endif;
                ?>
                <div class="health-item <?= $hcls ?>">
                    <span class="health-icon <?= $hcls ?> feather icon-sliders"></span>
                    <div class="health-label">Env Vars obrigatórias</div>
                    <div class="health-value <?= $hcls ?>"><?= $hval ?></div>
                </div>

            </div><!-- /.health-grid -->
        </div>
    </div>

    <!-- ================================================================
         FALHAS DE BANCO + LOG PHP — contadores lado a lado
         ================================================================ -->
    <div class="row">

        <!-- Falhas de banco -->
        <div class="col-md-6">
            <div class="card ds-card">
                <div class="card-header">
                    <span class="card-header-title"><i class="feather icon-database"></i> Falhas de Conexão ao Banco</span>
                    <a href="db_diagnostics.php" class="shortcut-link">
                        Ver detalhes <i class="feather icon-external-link" style="font-size:11px;"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="stat-num <?= $db_erros_1h  > 0 ? 'text-danger'  : '' ?>"><?= $db_erros_1h ?></div>
                            <div class="stat-sub"><i class="feather icon-clock"></i> Última 1h</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-num <?= $db_erros_24h > 0 ? 'text-warning' : '' ?>"><?= $db_erros_24h ?></div>
                            <div class="stat-sub"><i class="feather icon-calendar"></i> 24 horas</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-num"><?= $db_total ?></div>
                            <div class="stat-sub"><i class="feather icon-list"></i> Total</div>
                        </div>
                    </div>

                    <?php if (!empty($db_recentes)): ?>
                    <p style="font-size:12px;font-weight:700;color:#001f3f;margin-bottom:6px;">
                        <i class="feather icon-clock"></i> Últimas ocorrências:
                    </p>
                    <div style="overflow-x:auto;">
                        <table class="table table-sm ds-table mb-2">
                            <thead class="thead-light">
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Tipo</th>
                                    <th class="msg-cell">Mensagem</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($db_recentes as $e): ?>
                                <tr>
                                    <td style="white-space:nowrap;font-size:12px;">
                                        <?= htmlspecialchars($e['ts'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $tipo_mapa = [
                                            'banco_externo' => ['label' => 'Externo',    'cls' => 'warning'],
                                            'vars_ausentes' => ['label' => 'Vars aus.',  'cls' => 'secondary'],
                                            'local'         => ['label' => 'Local',      'cls' => 'secondary'],
                                            'sem_conexao'   => ['label' => 'Sem conexão','cls' => 'danger'],
                                        ];
                                        $tm = $tipo_mapa[$e['tipo'] ?? ''] ?? ['label' => htmlspecialchars($e['tipo'] ?? '—', ENT_QUOTES, 'UTF-8'), 'cls' => 'secondary'];
                                        ?>
                                        <span class="badge badge-<?= $tm['cls'] ?>"><?= $tm['label'] ?></span>
                                    </td>
                                    <td class="msg-cell" style="font-size:11px;">
                                        <?= htmlspecialchars($e['mensagem'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($db_total > 0): ?>
                    <form method="post" data-confirm="Limpar todo o log de falhas de banco?">
                        <input type="hidden" name="limpar_db_log" value="1">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="feather icon-trash-2"></i> Limpar log de falhas
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php else: ?>
                    <div style="text-align:center;padding:24px 0;color:#aaa;">
                        <i class="feather icon-check-circle" style="font-size:32px;color:#28a745;display:block;margin-bottom:8px;"></i>
                        Nenhuma falha de banco registrada.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Acessos bloqueados -->
        <div class="col-md-6">
            <div class="card ds-card">
                <div class="card-header">
                    <span class="card-header-title"><i class="feather icon-shield-off"></i> Acessos Bloqueados</span>
                    <a href="auth_log.php" class="shortcut-link">
                        Ver detalhes <i class="feather icon-external-link" style="font-size:11px;"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="stat-num <?= $bloqueados_1h  > 0 ? 'text-danger' : '' ?>"><?= $bloqueados_1h ?></div>
                            <div class="stat-sub"><i class="feather icon-clock"></i> Última 1h</div>
                        </div>
                        <div class="col-6">
                            <div class="stat-num <?= $bloqueados_24h > 4 ? 'text-warning' : '' ?>"><?= $bloqueados_24h ?></div>
                            <div class="stat-sub"><i class="feather icon-calendar"></i> Últimas 24h</div>
                        </div>
                    </div>
                    <p class="text-muted mb-0" style="font-size:12px;">
                        <i class="feather icon-info"></i>
                        Inclui sessões expiradas, tokens de API inválidos e páginas não encontradas (404).
                        <a href="auth_log.php" style="color:#FF5500;">Ver log completo →</a>
                    </p>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <!-- ================================================================
         LOG DE ERROS PHP
         ================================================================ -->
    <div class="card ds-card">
        <div class="card-header">
            <span class="card-header-title">
                <i class="feather icon-file-text"></i> Log de Erros PHP
                <?php if ($_php_log_existe && $_php_log_tam > 0): ?>
                    <small style="font-weight:400;opacity:.8;margin-left:6px;">
                        — <?= _fmt_sz($_php_log_tam) ?> &bull;
                        <?= count($_php_log_linhas) === $_php_log_max ? 'últimas ' . $_php_log_max : count($_php_log_linhas) ?> linhas
                    </small>
                <?php endif; ?>
            </span>
            <div style="display:flex;align-items:center;gap:10px;">
                <?php if (!$_is_dev && $_php_log_existe && count($_php_log_linhas) > 0): ?>
                <form method="post" style="margin:0;" data-confirm="Limpar o log de erros PHP?">
                    <input type="hidden" name="limpar_log_php" value="1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">
                        <i class="feather icon-trash-2"></i> Limpar
                    </button>
                </form>
                <?php endif; ?>
                <a href="db_diagnostics.php#php-log" class="shortcut-link">
                    Ver completo <i class="feather icon-external-link" style="font-size:11px;"></i>
                </a>
            </div>
        </div>
        <div class="card-body <?= (!$_is_dev && $_php_log_existe && count($_php_log_linhas) > 0) ? 'p-0' : '' ?>">
            <?php if ($_is_dev): ?>
            <div class="alert alert-warning mb-0" style="font-size:13px;">
                <i class="feather icon-alert-triangle" style="margin-right:6px;"></i>
                Em modo <strong>dev</strong>, os erros são enviados para <code>stderr</code> (console do servidor), não para arquivo.
            </div>
            <?php elseif (!$_php_log_existe || $_php_log_tam === 0): ?>
            <div style="text-align:center;padding:24px 0;color:#aaa;">
                <i class="feather icon-check-circle" style="font-size:32px;color:#28a745;display:block;margin-bottom:8px;"></i>
                Nenhum erro PHP registrado.
            </div>
            <?php elseif (empty($_php_log_linhas)): ?>
            <div style="text-align:center;padding:24px 0;color:#aaa;">
                <i class="feather icon-file-text" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                Arquivo de log existe mas está vazio.
            </div>
            <?php else: ?>
            <pre class="php-log-pre mb-0"><?php
                foreach ($_php_log_linhas as $linha) {
                    echo htmlspecialchars($linha, ENT_QUOTES, 'UTF-8') . "\n";
                }
            ?></pre>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================================================================
         DISCO — tamanhos dos arquivos monitorados
         ================================================================ -->
    <div class="card ds-card">
        <div class="card-header">
            <span class="card-header-title"><i class="feather icon-hard-drive"></i> Uso de Disco</span>
            <a href="disk_stats.php" class="shortcut-link">
                Gerenciar <i class="feather icon-external-link" style="font-size:11px;"></i>
            </a>
        </div>
        <div class="card-body">
            <?php
            function _badge_disk(int $bytes, int $warn_mb = 5, int $crit_mb = 20): string
            {
                $mb = $bytes / 1048576;
                if ($mb >= $crit_mb) return 'danger';
                if ($mb >= $warn_mb) return 'warning';
                return 'success';
            }
            ?>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-folder"></i> Pasta <code>api/logs/</code></span>
                <span class="badge badge-<?= _badge_disk($sz_logs_dir) ?>"><?= _fmt_sz($sz_logs_dir) ?></span>
            </div>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-alert-triangle"></i> db_failures.log</span>
                <span class="badge badge-<?= _badge_disk($sz_db_fail, 1, 5) ?>"><?= _fmt_sz($sz_db_fail) ?></span>
            </div>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-file-text"></i> Log de erros PHP</span>
                <span class="badge badge-<?= _badge_disk($sz_php_log, 2, 10) ?>"><?= _fmt_sz($sz_php_log) ?></span>
            </div>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-file"></i> log_processamento.txt</span>
                <span class="badge badge-<?= _badge_disk($sz_log_proc) ?>"><?= _fmt_sz($sz_log_proc) ?></span>
            </div>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-file"></i> log_recebidos.txt</span>
                <span class="badge badge-<?= _badge_disk($sz_log_recv) ?>"><?= _fmt_sz($sz_log_recv) ?></span>
            </div>
            <div class="disk-row">
                <span class="disk-label"><i class="feather icon-image"></i> Uploads temporários (<code>img/</code>)</span>
                <span class="badge badge-<?= _badge_disk($sz_uploads, 10, 50) ?>"><?= _fmt_sz($sz_uploads) ?></span>
            </div>
            <div style="background:#f8f9fa;border-radius:8px;padding:12px 16px;margin-top:12px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:700;color:#001f3f;font-size:14px;">
                    <i class="feather icon-database" style="margin-right:6px;color:#FF5500;"></i>Total monitorado
                </span>
                <span style="font-size:20px;font-weight:800;color:#FF5500;"><?= _fmt_sz($sz_total) ?></span>
            </div>
            <p class="text-muted mt-2 mb-0" style="font-size:11px;">
                <i class="feather icon-info"></i>
                Verde = normal &bull; Amarelo = atenção &bull; Vermelho = acima do limite.
                Acesse <a href="disk_stats.php" style="color:#FF5500;">Uso de Disco</a> para executar limpeza manual.
            </p>
        </div>
    </div>

    <!-- ================================================================
         VARIÁVEIS DE AMBIENTE
         ================================================================ -->
    <div class="card ds-card">
        <div class="card-header">
            <span class="card-header-title"><i class="feather icon-sliders"></i> Variáveis de Ambiente</span>
            <?php if ($_env_missing_count > 0): ?>
            <span class="badge badge-danger" style="font-size:12px;">
                <i class="feather icon-alert-triangle"></i> <?= $_env_missing_count ?> obrigatória(s) ausente(s)
            </span>
            <?php else: ?>
            <span class="badge badge-success" style="font-size:12px;">
                <i class="feather icon-check"></i> Todas configuradas
            </span>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php foreach ($_env_groups as $_grp_name => $_grp_vars): ?>
            <div class="env-group">
                <div class="env-group-title"><?= htmlspecialchars($_grp_name, ENT_QUOTES, 'UTF-8') ?></div>
                <table class="table table-sm env-table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:200px;">Variável</th>
                            <th>Descrição</th>
                            <th style="width:160px;">Status</th>
                            <th>Valor / Padrão</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_grp_vars as $_v):
                        $_st  = _env_status($_v['name'], $_v['required'], $_v['default']);
                        $_raw = getenv($_v['name']);
                    ?>
                        <tr class="<?= ($_st === 'missing' && $_v['required']) ? 'table-danger' : '' ?>">
                            <td><code class="env-name"><?= htmlspecialchars($_v['name'], ENT_QUOTES, 'UTF-8') ?></code></td>
                            <td class="text-muted" style="font-size:11px;"><?= htmlspecialchars($_v['desc'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if ($_st === 'set'): ?>
                                    <span class="badge badge-success"><i class="feather icon-check"></i> configurada</span>
                                <?php elseif ($_st === 'default'): ?>
                                    <span class="badge badge-secondary">usando padrão</span>
                                <?php elseif ($_v['required']): ?>
                                    <span class="badge badge-danger"><i class="feather icon-alert-triangle"></i> ausente (obrigatória)</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">ausente</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:11px;">
                                <?php if ($_v['secret']): ?>
                                    <?php if ($_st === 'set'): ?>
                                        <span class="text-muted"><i class="feather icon-lock" style="font-size:10px;"></i> <em>configurado</em></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                <?php elseif ($_st === 'set'): ?>
                                    <code class="env-value"><?= htmlspecialchars($_raw, ENT_QUOTES, 'UTF-8') ?></code>
                                <?php elseif ($_st === 'default'): ?>
                                    <code class="env-value text-muted"><?= htmlspecialchars($_v['default'], ENT_QUOTES, 'UTF-8') ?></code>
                                    <span class="text-muted" style="font-size:10px;">(padrão)</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div><!-- /.container-fluid -->

<script>
$(document).on('submit', 'form[data-confirm]', function(e) {
    var msg = $(this).data('confirm') || 'Confirmar ação?';
    if (!confirm(msg)) {
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>
