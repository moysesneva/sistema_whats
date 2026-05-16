<?php
if (isset($conn)) { return; }
require_once __DIR__ . '/error_config.php';

/**
 * Registra uma falha de conexão ao banco em logs/db_failures.log (JSONL).
 *
 * @param string $tipo      Identificador do tipo de falha (e.g. 'banco_externo', 'local', 'vars_ausentes', 'sem_conexao')
 * @param string $ambiente  'externo' ou 'local'
 * @param string $mensagem  Mensagem de erro detalhada
 */
function registrar_falha_banco(string $tipo, string $ambiente, string $mensagem): void
{
    $log_file = __DIR__ . '/logs/db_failures.log';
    $entrada = json_encode([
        'ts'        => date('Y-m-d H:i:s'),
        'tipo'      => $tipo,
        'ambiente'  => $ambiente,
        'mensagem'  => $mensagem,
    ], JSON_UNESCAPED_UNICODE);
    @file_put_contents($log_file, $entrada . "\n", FILE_APPEND | LOCK_EX);
    error_log($mensagem);
}

$ext_host = getenv('DB_HOST');
$use_external = !empty($ext_host) && $ext_host !== 'localhost' && $ext_host !== '127.0.0.1';

if ($use_external) {
    // Banco externo configurado (ex: Hostinger) — tenta primeiro
    $host    = $ext_host;
    $usuario = getenv('DB_USER');
    $senha   = getenv('DB_PASS');
    $banco   = getenv('DB_NAME');

    $vars_faltando = [];
    if (empty($usuario)) $vars_faltando[] = 'DB_USER';
    if ($senha === false) $vars_faltando[] = 'DB_PASS';
    if (empty($banco))   $vars_faltando[] = 'DB_NAME';

    if (empty($vars_faltando)) {
        $mysqli_driver = new mysqli_driver();
        $mysqli_driver->report_mode = MYSQLI_REPORT_OFF;

        $conn = mysqli_init();
        if ($conn) {
            mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
            $conectado = @mysqli_real_connect($conn, $host, $usuario, $senha, $banco);
            if ($conectado) {
                mysqli_set_charset($conn, 'utf8mb4');
                return;
            }
            $erro_ext = mysqli_connect_error();
            registrar_falha_banco(
                'banco_externo',
                'externo',
                'Falha na conexão ao banco externo (' . $host . '): ' . $erro_ext . ' — tentando MySQL local como fallback.'
            );
        }
    } else {
        registrar_falha_banco(
            'vars_ausentes',
            'externo',
            'Variáveis de ambiente ausentes: ' . implode(', ', $vars_faltando) . ' — tentando MySQL local como fallback.'
        );
    }
}

// MySQL local via socket (dev ou fallback quando externo falha)
$socket = '/home/runner/mysql.sock';
if (file_exists($socket)) {
    $conn = mysqli_connect('localhost', 'root', '', 'agendamento', 3306, $socket);
    if (!$conn) {
        registrar_falha_banco(
            'local',
            'local',
            'Falha na conexão local MySQL: ' . mysqli_connect_error()
        );
        http_response_code(503);
        require __DIR__ . '/db_error.php';
        exit;
    }
} else {
    registrar_falha_banco(
        'sem_conexao',
        'local',
        'Nenhuma conexão disponível: banco externo falhou e socket local não encontrado.'
    );
    http_response_code(503);
    require __DIR__ . '/db_error.php';
    exit;
}

mysqli_set_charset($conn, 'utf8mb4');
?>
