<?php
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
            error_log('Falha na conexão ao banco externo (' . $host . '): ' . mysqli_connect_error() . ' — tentando MySQL local como fallback.');
        }
    } else {
        error_log('Variáveis de ambiente ausentes: ' . implode(', ', $vars_faltando) . ' — tentando MySQL local como fallback.');
    }
}

// MySQL local via socket (dev ou fallback quando externo falha)
$socket = '/home/runner/mysql.sock';
if (file_exists($socket)) {
    $conn = mysqli_connect('localhost', 'root', '', 'agendamento', 3306, $socket);
    if (!$conn) {
        error_log('Falha na conexão local MySQL: ' . mysqli_connect_error());
        die('Serviço temporariamente indisponível. Tente novamente em instantes.');
    }
} else {
    error_log('Nenhuma conexão disponível: banco externo falhou e socket local não encontrado.');
    die('Serviço temporariamente indisponível. Tente novamente em instantes.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
