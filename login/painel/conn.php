<?php
$host   = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$senha  = getenv('DB_PASS') ?: '';
$banco  = getenv('DB_NAME') ?: 'agendamento';

if ($host === 'localhost' && !getenv('DB_USER')) {
    $socket = '/home/runner/mysql.sock';
    $conn = mysqli_connect($host, $usuario, $senha, $banco, 3306, $socket);
} else {
    $conn = mysqli_connect($host, $usuario, $senha, $banco);
}

if (!$conn) {
    die('Erro ao conectar com o banco de dados: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
