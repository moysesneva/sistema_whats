<?php
/**
 * Guard de autenticação para endpoints de API / webhooks externos.
 *
 * Valida um token secreto configurado via variável de ambiente API_WEBHOOK_TOKEN.
 * O token pode ser enviado de duas formas:
 *
 *   1. Cabeçalho HTTP (recomendado):
 *      Authorization: Bearer <token>
 *
 *   2. Parâmetro de query (compatibilidade com provedores que não suportam cabeçalhos):
 *      ?token=<token>
 *
 * Se API_WEBHOOK_TOKEN não estiver definido no ambiente, todos os acessos são
 * bloqueados com 500 para forçar a configuração correta antes de usar em produção.
 *
 * Respostas de erro retornam JSON com o campo "erro".
 */

if (!defined('API_AUTH_LOADED')) {
    define('API_AUTH_LOADED', true);

    /**
     * Registra uma tentativa de acesso negado ao webhook em logs/api_blocked.log.
     *
     * @param string $motivo  'token_ausente', 'token_invalido' ou 'token_nao_configurado'
     */
    function __api_registrar_acesso_bloqueado(string $motivo): void
    {
        $log_file = dirname(__DIR__) . '/logs/api_blocked.log';
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
            'ts'     => date('Y-m-d H:i:s'),
            'ip'     => $ip,
            'url'    => $url,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'motivo' => $motivo,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        @file_put_contents($log_file, $entrada . "\n", FILE_APPEND | LOCK_EX);
    }

    $__api_token_esperado = getenv('API_WEBHOOK_TOKEN');

    if (empty($__api_token_esperado)) {
        __api_registrar_acesso_bloqueado('token_nao_configurado');
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['erro' => 'API_WEBHOOK_TOKEN não configurado. Defina a variável de ambiente antes de usar este endpoint.']);
        exit;
    }

    // Extrai o token enviado pelo chamador
    $__api_token_enviado = '';

    // 1. Cabeçalho Authorization: Bearer <token>
    $__auth_header = '';
    if (function_exists('getallheaders')) {
        $__headers = getallheaders();
        foreach ($__headers as $__nome => $__valor) {
            if (strcasecmp($__nome, 'Authorization') === 0) {
                $__auth_header = $__valor;
                break;
            }
        }
    }
    if (empty($__auth_header) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $__auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    }
    if (!empty($__auth_header) && stripos($__auth_header, 'Bearer ') === 0) {
        $__api_token_enviado = trim(substr($__auth_header, 7));
    }

    // 2. Parâmetro de query ?token=
    if (empty($__api_token_enviado) && !empty($_GET['token'])) {
        $__api_token_enviado = trim($_GET['token']);
    }

    if (empty($__api_token_enviado) || !hash_equals($__api_token_esperado, $__api_token_enviado)) {
        $__motivo_rejeicao = empty($__api_token_enviado) ? 'token_ausente' : 'token_invalido';
        __api_registrar_acesso_bloqueado($__motivo_rejeicao);
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['erro' => 'Token inválido ou ausente.']);
        exit;
    }

    unset($__api_token_esperado, $__api_token_enviado, $__auth_header, $__headers, $__nome, $__valor, $__motivo_rejeicao);
}
