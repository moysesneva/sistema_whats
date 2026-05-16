<?php
$uri = $_SERVER['REQUEST_URI'];

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
    require __DIR__ . '/login/painel/erro_404.php';
    exit;
}

require __DIR__ . '/index.php';
