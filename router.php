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

// Roteamento normal da aplicação PHP
$path = parse_url($uri, PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

require __DIR__ . '/index.php';
