<?php
$socket = '/home/runner/mysql.sock';

if (file_exists($socket)) {
    // MySQL local do Replit — usa root sem senha via socket
    $conn = mysqli_connect('localhost', 'root', '', 'agendamento', 3306, $socket);

    if (!$conn) {
        error_log('Falha na conexão local MySQL: ' . mysqli_connect_error());
        die('Serviço temporariamente indisponível. Tente novamente em instantes.');
    }
} else {
    // Banco externo (ex: Hostinger) — usa credenciais das variáveis de ambiente
    $host    = getenv('DB_HOST');
    $usuario = getenv('DB_USER');
    $senha   = getenv('DB_PASS');
    $banco   = getenv('DB_NAME');

    // Valida que todas as variáveis obrigatórias estão definidas
    $vars_faltando = [];
    if (empty($host))    $vars_faltando[] = 'DB_HOST';
    if (empty($usuario)) $vars_faltando[] = 'DB_USER';
    if ($senha === false) $vars_faltando[] = 'DB_PASS';
    if (empty($banco))   $vars_faltando[] = 'DB_NAME';

    if (!empty($vars_faltando)) {
        error_log('Variáveis de ambiente ausentes para conexão ao banco: ' . implode(', ', $vars_faltando));
        die('Serviço temporariamente indisponível. Tente novamente em instantes.');
    }

    // Configura timeout de conexão via MySQLi driver options
    $mysqli_driver = new mysqli_driver();
    $mysqli_driver->report_mode = MYSQLI_REPORT_OFF;

    $conn = mysqli_init();
    if (!$conn) {
        error_log('Falha ao inicializar mysqli.');
        die('Serviço temporariamente indisponível. Tente novamente em instantes.');
    }

    // Timeout de conexão: 10 segundos (evita travamentos em banco externo lento)
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

    $conectado = mysqli_real_connect($conn, $host, $usuario, $senha, $banco);

    if (!$conectado) {
        error_log('Falha na conexão ao banco externo (' . $host . '): ' . mysqli_connect_error());
        die('Serviço temporariamente indisponível. Tente novamente em instantes.');
    }
}

mysqli_set_charset($conn, 'utf8mb4');
?>
