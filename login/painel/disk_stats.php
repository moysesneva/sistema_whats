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

$status_logs    = ler_status($base . '/status_limpar_logs.json');
$status_uploads = ler_status($base . '/status_limpar_uploads.json');

// -----------------------------------------------------------------------
// Tamanhos
// -----------------------------------------------------------------------

$sz_logs_dir  = tamanho_dir($logs_dir);
$sz_log_proc  = tamanho_arquivo($log_proc);
$sz_log_recv  = tamanho_arquivo($log_recv);
$sz_uploads   = tamanho_dir($uploads_dir);
$sz_total     = $sz_logs_dir + $sz_log_proc + $sz_log_recv + $sz_uploads;
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

                    <hr style="margin:16px 0 12px;">

                    <?php if ($status_logs): ?>
                    <div class="sweep-info">
                        <div class="stat-row">
                            <span class="stat-label"><i class="feather icon-clock"></i> Última limpeza</span>
                            <strong><?= htmlspecialchars($status_logs['ultima_varredura']) ?></strong>
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
                            <span class="stat-label"><i class="feather icon-sliders"></i> Configuração</span>
                            <span class="sweep-info">
                                Máx. <?= (int) $status_logs['max_age_dias'] ?> dias &bull;
                                Máx. <?= (int) $status_logs['max_size_mb'] ?> MB por arquivo
                            </span>
                        </div>
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
                            <strong><?= htmlspecialchars($status_uploads['ultima_varredura']) ?></strong>
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

</div>

<?php include 'footer.php'; ?>
