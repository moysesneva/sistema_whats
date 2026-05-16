<?php
/**
 * keepalive.php — Renova a sessão do admin via AJAX.
 *
 * Atualiza $_SESSION['last_activity'] e retorna JSON com o novo tempo restante.
 * Retorna 401 se a sessão já expirou.
 */
$auth_ajax_mode = true;
require_once __DIR__ . '/../auth_guard.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$restante = SESSION_TIMEOUT - (time() - ($_SESSION['last_activity'] ?? time()));

echo json_encode([
    'ok'              => true,
    'restante_seg'    => max(0, $restante),
    'session_timeout' => SESSION_TIMEOUT,
]);
