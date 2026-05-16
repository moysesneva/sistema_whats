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

$sz_logs_dir    = tamanho_dir($logs_dir);
$sz_log_proc    = tamanho_arquivo($log_proc);
$sz_log_recv    = tamanho_arquivo($log_recv);
$sz_uploads     = tamanho_dir($uploads_dir);
$sz_db_failures = tamanho_arquivo($db_failures);
$sz_total       = $sz_logs_dir + $sz_log_proc + $sz_log_recv + $sz_uploads + $sz_db_failures;
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
            <button id="btn-limpar-agora" type="button">
                <i class="feather icon-zap" style="margin-right:6px;"></i>Limpar Agora
            </button>
            <div id="cleanup-result"></div>
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

<script>
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
</script>

<?php include 'footer.php'; ?>
