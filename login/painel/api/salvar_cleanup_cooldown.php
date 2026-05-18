<?php
/**
 * Endpoint AJAX para salvar o cooldown de limpeza manual.
 * Requer sessão de admin com tipo 1 ou 4.
 * Aceita POST com corpo JSON: { "cleanup_cooldown_seconds": <int> }
 */

require_once __DIR__ . '/../auth_guard.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['login'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'erro' => 'Sem sessão ativa.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
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
    echo json_encode(['ok' => false, 'erro' => 'Acesso negado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data) || !isset($data['cleanup_cooldown_seconds'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'erro' => 'Parâmetro cleanup_cooldown_seconds ausente.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$valor = (int) $data['cleanup_cooldown_seconds'];

if ($valor < 5 || $valor > 3600) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Valor inválido. Informe um número entre 5 e 3600 segundos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare("UPDATE config SET cleanup_cooldown_seconds = ?");
$stmt->bind_param("i", $valor);

if (!$stmt->execute()) {
    $stmt->close();
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Erro ao salvar no banco de dados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

if ($affected === 0) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Nenhuma linha atualizada. A tabela config pode estar vazia.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok'       => true,
    'mensagem' => 'Cooldown de limpeza atualizado para ' . $valor . ' segundo(s).',
    'valor'    => $valor,
], JSON_UNESCAPED_UNICODE);
