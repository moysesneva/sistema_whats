<?php
include 'conn.php';
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Verificar se o ID foi enviado via POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query para deletar o agendamento com o ID especificado
    $sql = "DELETE FROM agenda_padrao WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "Agendamento deletado com sucesso!";
    } else {
        echo "Erro ao deletar o agendamento: " . mysqli_error($conn);
    }
}

// Fechar a conexão
mysqli_close($conn);
?>
