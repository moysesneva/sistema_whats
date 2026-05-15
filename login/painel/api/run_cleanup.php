<?php
/**
 * Endpoint para limpeza manual de logs e uploads.
 * Chamado pelo botão "Limpar Agora" na página disk_stats.php.
 * Requer sessão de admin com tipo 1 ou 4.
 */

require_once __DIR__ . '/../auth_guard.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['login'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'erro' => 'Sem sessão ativa.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
    exit;
}

include __DIR__ . '/../conn.php';

$login = $_SESSION['login'];
$stmt  = $conn->prepare("SELECT tipo, autorizado FROM login WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r || $r['autorizado'] != 2 || !in_array((int) $r['tipo'], [1, 4])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'erro' => 'Acesso negado.']);
    exit;
}

// -----------------------------------------------------------------------
// Configurações (mesmas usadas pelos scripts shell)
// -----------------------------------------------------------------------

$maxDias  = max(1, (int) (getenv('LOG_MAX_AGE_DAYS')  ?: 7));
$maxMb    = max(1, (int) (getenv('LOG_MAX_SIZE_MB')   ?: 10));
$maxBytes = $maxMb * 1024 * 1024;

$uploadsMaxAge = max(1, (int) (getenv('UPLOADS_MAX_AGE_SECONDS') ?: 3600));

$base      = __DIR__;
$logsDir   = $base . '/logs';
$logProc   = $base . '/log_processamento.txt';
$logRecv   = $base . '/log_recebidos.txt';
$uploadsDir = $base . '/img';

$ts = date('Y-m-d H:i:s');

// -----------------------------------------------------------------------
// Limpeza de logs
// -----------------------------------------------------------------------

$removidosLogs  = 0;
$truncamentos   = 0;

if (is_dir($logsDir)) {
    $limiteModif = time() - ($maxDias * 86400);
    foreach (glob($logsDir . '/*.{log,txt}', GLOB_BRACE) as $arquivo) {
        if (is_file($arquivo) && filemtime($arquivo) < $limiteModif) {
            if (unlink($arquivo)) {
                $removidosLogs++;
            }
        }
    }
}

foreach ([$logProc, $logRecv] as $arquivo) {
    if (is_file($arquivo) && filesize($arquivo) > $maxBytes) {
        file_put_contents($arquivo, '');
        $truncamentos++;
    }
}

$statusLogs = json_encode([
    'ultima_varredura'  => $ts,
    'arquivos_removidos' => $removidosLogs,
    'truncamentos'       => $truncamentos,
    'max_age_dias'       => $maxDias,
    'max_size_mb'        => $maxMb,
], JSON_UNESCAPED_UNICODE);

file_put_contents($base . '/status_limpar_logs.json', $statusLogs);

// -----------------------------------------------------------------------
// Limpeza de uploads
// -----------------------------------------------------------------------

$removidosUploads = 0;
$agora = time();

if (is_dir($uploadsDir)) {
    foreach (glob($uploadsDir . '/imagem_*.png') as $arquivo) {
        if (is_file($arquivo) && ($agora - filemtime($arquivo)) > $uploadsMaxAge) {
            if (unlink($arquivo)) {
                $removidosUploads++;
            }
        }
    }
}

$statusUploads = json_encode([
    'ultima_varredura'   => $ts,
    'arquivos_removidos' => $removidosUploads,
], JSON_UNESCAPED_UNICODE);

file_put_contents($base . '/status_limpar_uploads.json', $statusUploads);

// -----------------------------------------------------------------------
// Resposta
// -----------------------------------------------------------------------

echo json_encode([
    'ok'               => true,
    'ts'               => $ts,
    'logs_removidos'   => $removidosLogs,
    'truncamentos'     => $truncamentos,
    'uploads_removidos' => $removidosUploads,
], JSON_UNESCAPED_UNICODE);
