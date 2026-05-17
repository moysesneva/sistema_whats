<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
$login = $_SESSION['login'];
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['especialidade'])) {

    $especialidade = trim($_POST['especialidade']);

    $stmt_verifica = mysqli_prepare($conn, "SELECT id FROM especialidades WHERE especialidades = ? AND login = ?");
    mysqli_stmt_bind_param($stmt_verifica, "ss", $especialidade, $login);
    mysqli_stmt_execute($stmt_verifica);
    $query_verifica = mysqli_stmt_get_result($stmt_verifica);

    if (mysqli_num_rows($query_verifica) > 0) {
        echo 'Especialidade já existe';
    } else {
        $stmt_insert = mysqli_prepare($conn, "INSERT INTO especialidades (especialidades, login) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ss", $especialidade, $login);

        if (mysqli_stmt_execute($stmt_insert)) {
            echo 'sucesso';
        } else {
            echo 'Erro ao inserir: ' . mysqli_error($conn);
        }
    }
} else {
    echo 'Requisição inválida';
}
?>
