<?php
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    // Calcula o caminho absoluto até login_adm.php (sempre no mesmo dir que auth_guard.php)
    $doc_root  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $guard_dir = rtrim(str_replace('\\', '/', __DIR__), '/');
    $base_url  = ($doc_root !== '' && strpos($guard_dir, $doc_root) === 0)
        ? substr($guard_dir, strlen($doc_root))
        : '/login/painel';
    $login_url = rtrim($base_url, '/') . '/login_adm.php';

    $is_ajax = (isset($auth_ajax_mode) && $auth_ajax_mode === true)
        || (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($is_ajax) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'erro'     => 'Sessão expirada. Faça login novamente.',
            'redirect' => $login_url,
        ]);
        exit;
    }

    header('Location: ' . $login_url);
    exit;
}
