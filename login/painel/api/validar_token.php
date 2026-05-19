<?php
/**
 * validar_token.php
 * Endpoint chamado pelo index.js do VPS a cada 60 segundos.
 * Valida o token e retorna a lista de usuários ativos registrados no banco.
 *
 * POST { token, usuarios: [...] }
 */
require_once __DIR__ . '/../error_config.php';
require_once __DIR__ . '/../conn.php';

header('Content-Type: application/json; charset=utf-8');

$input    = json_decode(file_get_contents('php://input'), true);
$token    = trim($input['token']   ?? '');
$usuarios = $input['usuarios']     ?? [];

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token não enviado']);
    exit;
}

$row            = mysqli_fetch_assoc(mysqli_query($conn, "SELECT chave FROM config LIMIT 1"));
$token_esperado = trim($row['chave'] ?? '');

if (empty($token_esperado) || !hash_equals($token_esperado, $token)) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token inválido']);
    exit;
}

// Retorna confirmação com timestamp
echo json_encode([
    'status'    => 'ok',
    'timestamp' => time(),
    'usuarios'  => $usuarios
]);
