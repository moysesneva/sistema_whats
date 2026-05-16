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

$base = __DIR__;

// -----------------------------------------------------------------------
// Cooldown — evita execuções duplicadas em rápida sucessão
// (leitura + verificação + escrita são atômicas via flock)
// -----------------------------------------------------------------------

$cooldownSec  = max(1, (int) (getenv('CLEANUP_COOLDOWN_SECONDS') ?: 30));
$cooldownFile = $base . '/cleanup_cooldown.json';
$lockFile     = $base . '/cleanup_cooldown.lock';

$lockFp = fopen($lockFile, 'c');
if ($lockFp === false || !flock($lockFp, LOCK_EX)) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'erro' => 'Não foi possível adquirir o bloqueio de concorrência. Tente novamente.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Dentro da seção crítica: ler, verificar e gravar são uma operação atômica.
$ultimaExec = 0;
if (is_file($cooldownFile)) {
    $cd = json_decode(file_get_contents($cooldownFile), true);
    $ultimaExec = isset($cd['ts']) ? (int) $cd['ts'] : 0;
}

$decorrido = time() - $ultimaExec;
if ($decorrido < $cooldownSec) {
    $restante = $cooldownSec - $decorrido;
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    http_response_code(429);
    echo json_encode([
        'ok'                => false,
        'erro'              => "Limpeza executada há menos de {$cooldownSec} segundos. Aguarde mais {$restante} segundo(s) antes de tentar novamente.",
        'cooldown_restante' => $restante,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Grava o timestamp enquanto ainda segura o lock — impede corrida entre requisições.
file_put_contents($cooldownFile, json_encode(['ts' => time()], JSON_UNESCAPED_UNICODE));

flock($lockFp, LOCK_UN);
fclose($lockFp);

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

$logsDir   = $base . '/logs';
$logProc   = $base . '/log_processamento.txt';
$logRecv   = $base . '/log_recebidos.txt';
$uploadsDir = $base . '/img';
$dbFailuresLog = dirname($base) . '/logs/db_failures.log';
$phpErrorLog   = getenv('PHP_ERROR_LOG') ?: '/tmp/php_errors.log';

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
// Rotação do log de erros PHP (/tmp/php_errors.log ou PHP_ERROR_LOG)
// -----------------------------------------------------------------------

$phpErrorLogAction = 'none';

if (is_file($phpErrorLog) && filesize($phpErrorLog) > $maxBytes) {
    $fp = fopen($phpErrorLog, 'a');
    if ($fp !== false) {
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            flock($fp, LOCK_UN);
            $truncamentos++;
            $phpErrorLogAction = 'truncado';
        }
        fclose($fp);
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
    'php_error_log_action'     => $phpErrorLogAction,
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
    'ok'                   => true,
    'ts'                   => $ts,
    'logs_removidos'       => $removidosLogs,
    'truncamentos'         => $truncamentos,
    'uploads_removidos'    => $removidosUploads,
    'php_error_log_action' => $phpErrorLogAction,
], JSON_UNESCAPED_UNICODE);
