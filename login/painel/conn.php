<?php
$socket = '/home/runner/mysql.sock';

if (file_exists($socket)) {
    // MySQL local do Replit — usa root sem senha via socket
    $conn = mysqli_connect('localhost', 'root', '', 'agendamento', 3306, $socket);
} else {
    // Banco externo (ex: Hostinger) — usa credenciais das variáveis de ambiente
    $host    = getenv('DB_HOST') ?: '';
    $usuario = getenv('DB_USER') ?: '';
    $senha   = getenv('DB_PASS') ?: '';
    $banco   = getenv('DB_NAME') ?: '';
    $conn = mysqli_connect($host, $usuario, $senha, $banco);
}

if (!$conn) {
    die('Erro ao conectar com o banco de dados: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
