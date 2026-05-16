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

$dbFailuresMaxMb   = max(1, (int) (getenv('DB_FAILURES_MAX_SIZE_MB')  ?: 1));
$dbFailuresMaxBytes = $dbFailuresMaxMb * 1024 * 1024;
$dbFailuresMaxAge  = max(1, (int) (getenv('DB_FAILURES_MAX_AGE_DAYS') ?: 30));

$base      = __DIR__;
$logsDir   = $base . '/logs';
$logProc   = $base . '/log_processamento.txt';
$logRecv   = $base . '/log_recebidos.txt';
$uploadsDir = $base . '/img';
$dbFailuresLog = dirname($base) . '/logs/db_failures.log';

$ts = date('Y-m-d H:i:s');

// -----------------------------------------------------------------------
// Limpeza de logs
// -----------------------------------------------------------------------

$removidosLogs  = 0;
$truncamentos   = 0;

if (is_dir($logsDir)) {
    $limiteModif = time() - ($maxDias * 86400);
    foreach (glob($logsDir . '/*.{log,txt}', GLOB_BRACE) as $arquivo) {
        if (basename($arquivo) === 'admin_actions.log') {
            continue;
        }
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

// -----------------------------------------------------------------------
// Rotação de db_failures.log
// -----------------------------------------------------------------------

$dbFailuresAction = 'none';

if (is_file($dbFailuresLog)) {
    if (filesize($dbFailuresLog) > $dbFailuresMaxBytes) {
        file_put_contents($dbFailuresLog, '');
        $truncamentos++;
        $dbFailuresAction = 'truncado';
    } else {
        $cutoff  = time() - ($dbFailuresMaxAge * 86400);
        $linhas  = file($dbFailuresLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($linhas !== false) {
            $filtradas = array_filter($linhas, function (string $linha) use ($cutoff): bool {
                if (preg_match('/"ts":"(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})"/', $linha, $m)) {
                    $epoch = mktime((int)$m[4], (int)$m[5], (int)$m[6], (int)$m[2], (int)$m[3], (int)$m[1]);
                    return $epoch >= $cutoff;
                }
                return true;
            });
            file_put_contents($dbFailuresLog, implode("\n", $filtradas) . (count($filtradas) > 0 ? "\n" : ''));
            $dbFailuresAction = 'filtrado';
        }
    }
}

$statusLogs = json_encode([
    'ultima_varredura'         => $ts,
    'arquivos_removidos'       => $removidosLogs,
    'truncamentos'             => $truncamentos,
    'max_age_dias'             => $maxDias,
    'max_size_mb'              => $maxMb,
    'db_failures_action'       => $dbFailuresAction,
    'db_failures_max_size_mb'  => $dbFailuresMaxMb,
    'db_failures_max_age_dias' => $dbFailuresMaxAge,
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
// Auditoria — registra ação manual do admin
// -----------------------------------------------------------------------

$adminActionsLog = $logsDir . '/admin_actions.log';

if (!is_dir($logsDir)) {
    @mkdir($logsDir, 0755, true);
}

$auditEntry = json_encode([
    'ts'               => $ts,
    'admin'            => $login,
    'logs_removidos'   => $removidosLogs,
    'truncamentos'     => $truncamentos,
    'uploads_removidos' => $removidosUploads,
], JSON_UNESCAPED_UNICODE);

file_put_contents($adminActionsLog, $auditEntry . "\n", FILE_APPEND | LOCK_EX);

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
