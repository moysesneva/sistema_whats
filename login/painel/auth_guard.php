<?php
require_once __DIR__ . '/error_config.php';

/**
 * auth_guard.php — Proteção centralizada de sessão para o painel.
 *
 * Uso em páginas HTML:
 *   require_once __DIR__ . '/auth_guard.php';
 *
 * Uso em endpoints AJAX/ação (retorna 401 JSON em vez de redirecionar):
 *   $auth_ajax_mode = true;
 *   require_once __DIR__ . '/auth_guard.php';
 */

// Lê o tempo de inatividade da tabela config (com fallback para 30 min)
$_auth_timeout_sec = 30 * 60;
if (!isset($conn)) {
    require_once __DIR__ . '/conn.php';
}
if (isset($conn)) {
    $r = @$conn->query("SELECT session_timeout_min FROM config LIMIT 1");
    if ($r) {
        $row = $r->fetch_assoc();
        if ($row && isset($row['session_timeout_min'])) {
            $v = (int) $row['session_timeout_min'];
            if ($v >= 5 && $v <= 480) {
                $_auth_timeout_sec = $v * 60;
            }
        }
    }
}
define('SESSION_TIMEOUT', $_auth_timeout_sec);
unset($_auth_timeout_sec);

/**
 * Registra uma tentativa de acesso bloqueado (sem sessão válida) em logs/auth_blocked.log.
 *
 * Cada entrada é uma linha JSON (JSONL) com: ts, ip, url, method.
 * Quando o arquivo ultrapassa LOG_MAX_SIZE_MB (padrão: 10 MB) ele é truncado
 * — mesmo comportamento de rotação dos outros logs do projeto.
 *
 * @param bool $expired true quando a sessão expirou, false quando nunca houve sessão
 */
function registrar_acesso_bloqueado(bool $expired): void
{
    $log_file = __DIR__ . '/logs/auth_blocked.log';

    $logs_dir = dirname($log_file);
    if (!is_dir($logs_dir)) {
        @mkdir($logs_dir, 0755, true);
    }

    $max_mb    = max(1, (int) (getenv('LOG_MAX_SIZE_MB') ?: 10));
    $max_bytes = $max_mb * 1024 * 1024;
    if (is_file($log_file) && filesize($log_file) > $max_bytes) {
        @file_put_contents($log_file, '');
    }

    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    $ip = $forwarded !== ''
        ? trim(explode(',', $forwarded)[0])
        : ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido');

    $url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://'
         . ($_SERVER['HTTP_HOST'] ?? 'localhost')
         . ($_SERVER['REQUEST_URI'] ?? '/');

    $entrada = json_encode([
        'ts'      => date('Y-m-d H:i:s'),
        'ip'      => $ip,
        'url'     => $url,
        'method'  => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'motivo'  => $expired ? 'sessao_expirada' : 'sem_sessao',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    @file_put_contents($log_file, $entrada . "\n", FILE_APPEND | LOCK_EX);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_session_expired = false;

if (isset($_SESSION['login'])) {
    if (!isset($_SESSION['last_activity'])) {
        session_unset();
        session_destroy();
        $_session_expired = true;
    } elseif ((time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        $_session_expired = true;
    } else {
        $_SESSION['last_activity'] = time();
    }
}

if ($_session_expired || !isset($_SESSION['login'])) {
    registrar_acesso_bloqueado($_session_expired);

    // Calcula o caminho absoluto até login_adm.php (sempre no mesmo dir que auth_guard.php)
    $doc_root  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $guard_dir = rtrim(str_replace('\\', '/', __DIR__), '/');
    $base_url  = ($doc_root !== '' && strpos($guard_dir, $doc_root) === 0)
        ? substr($guard_dir, strlen($doc_root))
        : '/login/painel';
    $login_url = rtrim($base_url, '/') . '/login_adm.php';
    $redirect_url = $_session_expired ? $login_url . '?expirado=1' : $login_url;

    $is_ajax = (isset($auth_ajax_mode) && $auth_ajax_mode === true)
        || (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($is_ajax) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'erro'     => 'Sessão expirada. Faça login novamente.',
            'redirect' => $redirect_url,
        ]);
        exit;
    }

    header('Location: ' . $redirect_url);
    exit;
}
