<?php
$uri = $_SERVER['REQUEST_URI'];

// Health check probe do Replit Autoscale (Google Cloud Run — GoogleHC/1.0)
// Deve responder 200 imediatamente, antes de qualquer conexão ao banco.
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if ($ua === 'GoogleHC/1.0') {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo 'ok';
    exit;
}

if (strpos($uri, '/__mockup/') === 0) {
    $target = 'http://127.0.0.1:23636' . $uri;
    $method = $_SERVER['REQUEST_METHOD'];
    $headers = [];
    foreach (getallheaders() as $k => $v) {
        if (strtolower($k) === 'host') continue;
        $headers[] = "$k: $v";
    }
    $body = file_get_contents('php://input');

    $ctx = stream_context_create([
        'http' => [
            'method'  => $method,
            'header'  => implode("\r\n", $headers),
            'content' => $body,
            'ignore_errors' => true,
            'timeout' => 10,
        ]
    ]);

    $response = @file_get_contents($target, false, $ctx);

    if ($response === false) {
        http_response_code(502);
        echo 'Mockup sandbox unavailable';
        exit;
    }

    foreach ($http_response_header as $h) {
        if (preg_match('/^HTTP\//i', $h)) continue;
        if (preg_match('/^(Transfer-Encoding|Connection):/i', $h)) continue;
        header($h, false);
    }

    echo $response;
    exit;
}

// Cabeçalhos de segurança HTTP para todas as rotas públicas
require_once __DIR__ . '/login/painel/security_headers.php';

// Configuração de relatório de erros e handlers de erro 500
require_once __DIR__ . '/login/painel/error_config.php';

// Roteamento normal da aplicação PHP
$path = parse_url($uri, PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Página não encontrada (404) para caminhos inexistentes
if ($path !== '/' && !file_exists($file)) {
    http_response_code(404);

    // Registra o acesso 404 no log de acessos bloqueados
    (function () {
        $log_file = __DIR__ . '/login/painel/logs/not_found.log';
        $logs_dir = dirname($log_file);
        if (!is_dir($logs_dir)) {
            @mkdir($logs_dir, 0755, true);
        }
        $max_bytes = max(1, (int) (getenv('LOG_MAX_SIZE_MB') ?: 10)) * 1024 * 1024;
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
            'ts'     => date('Y-m-d H:i:s'),
            'ip'     => $ip,
            'url'    => $url,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'motivo' => 'pagina_nao_encontrada',
            'ua'     => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        @file_put_contents($log_file, $entrada . "\n", FILE_APPEND | LOCK_EX);
    })();

    require __DIR__ . '/login/painel/erro_404.php';
    exit;
}

require __DIR__ . '/index.php';
