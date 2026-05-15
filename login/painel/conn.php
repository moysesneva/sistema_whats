<?php
$host = getenv('DB_HOST') ?: 'localhost';

if ($host === 'localhost') {
    // MySQL local do Replit — sempre root + socket + banco agendamento
    $usuario = 'root';
    $senha   = '';
    $banco   = 'agendamento';
    $socket  = '/home/runner/mysql.sock';
    $conn = mysqli_connect($host, $usuario, $senha, $banco, 3306, $socket);
} else {
    // Banco externo (ex: Hostinger) — usa credenciais das variáveis de ambiente
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
