<?php
session_start();
$login = $_SESSION['login'];
// ajax_add_especialidade.php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['especialidade'])) {
    
    $especialidade = mysqli_real_escape_string($conn, $_POST['especialidade']);
    
    // Verifica se a especialidade já existe
    $sql_verifica = "SELECT id FROM especialidades WHERE especialidades = '$especialidade' AND login = '$login'";
    $query_verifica = mysqli_query($conn, $sql_verifica);
    
    if(mysqli_num_rows($query_verifica) > 0) {
        echo 'Especialidade já existe';
    } else {
        // Insere a nova especialidade
        $sql_insert = "INSERT INTO especialidades (especialidades, login) VALUES ('$especialidade', '$login')";
        
        if(mysqli_query($conn, $sql_insert)) {
            echo 'sucesso';
        } else {
            echo 'Erro ao inserir: ' . mysqli_error($conn);
        }
    }
} else {
    echo 'Requisição inválida';
}
?>