<?php
include 'conn.php';
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Verificar se o ID foi enviado via POST
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Query para deletar o agendamento com o ID especificado
    $stmt_del = $conn->prepare("DELETE FROM agenda_padrao WHERE id = ?");
    $stmt_del->bind_param("i", $id);

    if ($stmt_del->execute()) {
        echo "Agendamento deletado com sucesso!";
    } else {
        echo "Erro ao deletar o agendamento: " . $conn->error;
    }
    $stmt_del->close();
}

// Fechar a conexão
mysqli_close($conn);
?>
