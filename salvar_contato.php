<?php
error_reporting(0);
ini_set("display_errors", 0 );
include 'login/painel/conn.php';
// Definir fuso horário do Brasil
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco (você já tem em $conn)

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protege os dados com mysqli_real_escape_string (pode usar prepare também)
    $nome  = mysqli_real_escape_string($conn, $_POST['nome'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $whats = mysqli_real_escape_string($conn, $_POST['telefone'] ?? ''); // campo "telefone" vira "whats"
    $data  = date("Y-m-d H:i:s"); // Formato padrão de data e hora

    // Monta o SQL de inserção
    $sql = "INSERT INTO leads (nome, email, whats, data) VALUES ('$nome', '$email', '$whats', '$data')";

    // Executa e verifica sucesso
    if (mysqli_query($conn, $sql)) {
        echo "✅ Lead salvo com sucesso.";
    } else {
        echo "❌ Erro ao salvar lead: " . mysqli_error($conn);
    }
}
?>
