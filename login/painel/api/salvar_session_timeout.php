<?php
/**
 * Endpoint AJAX para salvar o tempo de expiração de sessão.
 * Requer sessão de admin com tipo 1 ou 4.
 * Aceita POST com corpo JSON: { "session_timeout_min": <int> }
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

if (!is_array($data) || !isset($data['session_timeout_min'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'erro' => 'Parâmetro session_timeout_min ausente.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$valor = (int) $data['session_timeout_min'];

if ($valor < 5 || $valor > 480) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Valor inválido. Informe um número entre 5 e 480.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare("UPDATE config SET session_timeout_min = ?");
$stmt->bind_param("i", $valor);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode([
        'ok'       => true,
        'mensagem' => 'Tempo de expiração atualizado para ' . $valor . ' min. Válido a partir do próximo login.',
        'valor'    => $valor,
    ], JSON_UNESCAPED_UNICODE);
} else {
    $stmt->close();
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Erro ao salvar no banco de dados.'], JSON_UNESCAPED_UNICODE);
}
