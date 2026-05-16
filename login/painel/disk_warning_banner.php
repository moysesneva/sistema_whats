<?php
// -----------------------------------------------------------------------
// Banner de aviso de disco — inclua após header.php nas páginas do painel
// Exibe um alerta descartável se o uso total monitorado ultrapassar o limiar.
// -----------------------------------------------------------------------

if (!defined('DISK_WARN_THRESHOLD_MB')) {
    define('DISK_WARN_THRESHOLD_MB', 50);
}

function _disk_banner_dir_size(string $path): int
{
    if (!is_dir($path)) return 0;
    $total = 0;
    $iter  = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $f) {
        $total += $f->getSize();
    }
    return $total;
}

function _disk_banner_file_size(string $path): int
{
    return (is_file($path)) ? (int) filesize($path) : 0;
}

$_disk_base      = __DIR__ . '/api';
$_disk_total_bytes =
    _disk_banner_dir_size($_disk_base . '/logs') +
    _disk_banner_file_size($_disk_base . '/log_processamento.txt') +
    _disk_banner_file_size($_disk_base . '/log_recebidos.txt') +
    _disk_banner_dir_size($_disk_base . '/img');

$_disk_total_mb = $_disk_total_bytes / 1048576;

if ($_disk_total_mb >= DISK_WARN_THRESHOLD_MB):
    $formatted = round($_disk_total_mb, 1) . ' MB';
?>
<div id="disk-warning-banner"
     style="margin:16px 0;padding:14px 18px;background:#fff3cd;border:1px solid #ffc107;border-left:5px solid #FF5500;border-radius:6px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <span style="color:#333;font-size:14px;line-height:1.5;">
        <i class="feather icon-alert-triangle" style="color:#FF5500;margin-right:8px;"></i>
        <strong>Uso de disco elevado:</strong>
        o espaço monitorado atingiu <strong><?= htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8') ?></strong>
        (limiar: <?= DISK_WARN_THRESHOLD_MB ?> MB).
        <a href="disk_stats.php" style="color:#001f3f;font-weight:600;margin-left:6px;">Ver detalhes &rsaquo;</a>
    </span>
    <button type="button"
            onclick="_diskWarnDismiss();"
            style="background:none;border:none;font-size:20px;line-height:1;color:#888;cursor:pointer;padding:0 4px;flex-shrink:0;"
            aria-label="Fechar aviso">&times;</button>
</div>
<script>
(function () {
    var KEY = 'disk_warn_dismissed_until';

    function _diskWarnDismissed() {
        try {
            var until = parseInt(localStorage.getItem(KEY), 10);
            if (isNaN(until)) return false;
            if (Date.now() >= until) {
                localStorage.removeItem(KEY);
                return false;
            }
            return true;
        } catch (e) { return false; }
    }

    function _endOfDay() {
        var d = new Date();
        d.setHours(23, 59, 59, 999);
        return d.getTime();
    }

    window._diskWarnDismiss = function () {
        try {
            localStorage.setItem(KEY, _endOfDay());
        } catch (e) {}
        var el = document.getElementById('disk-warning-banner');
        if (el) el.style.display = 'none';
    };

    if (_diskWarnDismissed()) {
        var el = document.getElementById('disk-warning-banner');
        if (el) el.style.display = 'none';
    }
})();
</script>
<?php endif; ?>
