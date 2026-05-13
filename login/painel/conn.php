<?php
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'agendamento';
$socket = '/home/runner/mysql.sock';

$conn = mysqli_connect($host, $usuario, $senha, $banco, 3306, $socket);

if (!$conn) {
    die('Erro ao conectar com o banco de dados: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
