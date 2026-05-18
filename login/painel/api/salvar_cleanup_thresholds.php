<?php
/**
 * Endpoint AJAX para salvar os thresholds de limpeza de log.
 * Requer sessão de admin com tipo 1 ou 4.
 * Aceita POST com corpo JSON contendo um ou mais dos campos:
 *   log_max_age_days, log_max_size_mb, uploads_max_age_sec,
 *   db_failures_max_mb, db_failures_max_days
 * Valor null ou string vazia = remover override (volta a usar env var / padrão).
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

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'erro' => 'Corpo JSON inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Campos aceitos: [chave_json => [coluna_db, min, max]]
$campos = [
    'log_max_age_days'    => ['cleanup_log_max_age_days',   1, 365],
    'log_max_size_mb'     => ['cleanup_log_max_size_mb',    1, 500],
    'uploads_max_age_sec' => ['cleanup_uploads_max_age_sec',60, 86400],
    'db_failures_max_mb'  => ['cleanup_db_failures_max_mb', 1, 500],
    'db_failures_max_days'=> ['cleanup_db_failures_max_days',1, 365],
];

$salvos = [];
$erros  = [];

foreach ($campos as $chave => [$coluna, $min, $max]) {
    if (!array_key_exists($chave, $data)) {
        continue;
    }

    $raw = $data[$chave];

    if ($raw === null || $raw === '' || $raw === false) {
        $stmt = $conn->prepare("UPDATE config SET `{$coluna}` = NULL LIMIT 1");
        if ($stmt->execute()) {
            $salvos[$chave] = null;
        } else {
            $erros[] = $chave;
        }
        $stmt->close();
        continue;
    }

    $valor = (int) $raw;
    if ($valor < $min || $valor > $max) {
        $erros[] = "{$chave} deve estar entre {$min} e {$max}";
        continue;
    }

    $stmt = $conn->prepare("UPDATE config SET `{$coluna}` = ? LIMIT 1");
    $stmt->bind_param("i", $valor);
    if ($stmt->execute()) {
        $salvos[$chave] = $valor;
    } else {
        $erros[] = $chave;
    }
    $stmt->close();
}

if (!empty($erros)) {
    http_response_code(422);
    echo json_encode([
        'ok'    => false,
        'erro'  => 'Alguns valores não puderam ser salvos: ' . implode(', ', $erros),
        'salvos'=> $salvos,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Atualiza o arquivo JSON de config para o script shell poder ler
_escrever_json_config($conn, __DIR__);

echo json_encode([
    'ok'     => true,
    'mensagem'=> 'Thresholds salvos com sucesso.',
    'salvos' => $salvos,
], JSON_UNESCAPED_UNICODE);

// ---------------------------------------------------------------------------
// Escreve login/painel/api/cleanup_config.json com os valores efetivos
// (DB > env var > padrão) para que o script shell possa ler
// ---------------------------------------------------------------------------
function _escrever_json_config(\mysqli $conn, string $base): void
{
    $r = $conn->query(
        "SELECT cleanup_log_max_age_days, cleanup_log_max_size_mb,
                cleanup_uploads_max_age_sec,
                cleanup_db_failures_max_mb, cleanup_db_failures_max_days
         FROM config LIMIT 1"
    );
    $row = $r ? $r->fetch_assoc() : [];

    $cfg = [
        'log_max_age_days'    => _resolve((int)($row['cleanup_log_max_age_days']    ?? 0), 'LOG_MAX_AGE_DAYS',        7),
        'log_max_size_mb'     => _resolve((int)($row['cleanup_log_max_size_mb']     ?? 0), 'LOG_MAX_SIZE_MB',        10),
        'uploads_max_age_sec' => _resolve((int)($row['cleanup_uploads_max_age_sec'] ?? 0), 'UPLOADS_MAX_AGE_SECONDS', 3600),
        'db_failures_max_mb'  => _resolve((int)($row['cleanup_db_failures_max_mb']  ?? 0), 'DB_FAILURES_MAX_SIZE_MB',  1),
        'db_failures_max_days'=> _resolve((int)($row['cleanup_db_failures_max_days']?? 0), 'DB_FAILURES_MAX_AGE_DAYS',30),
    ];

    file_put_contents(
        $base . '/cleanup_config.json',
        json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n",
        LOCK_EX
    );
}

function _resolve(int $dbVal, string $envKey, int $default): int
{
    if ($dbVal > 0) {
        return $dbVal;
    }
    $env = getenv($envKey);
    if ($env !== false && (int) $env > 0) {
        return (int) $env;
    }
    return $default;
}
