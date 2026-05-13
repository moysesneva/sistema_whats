<?php
session_start();
$login = $_SESSION['login'];
include 'conn.php';
include 'funcoes.php';

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os dados do formulário
    $boas_vindas = $conn->real_escape_string($_POST["boas_vindas"]);
    $prompt = $conn->real_escape_string($_POST["prompt"]);
    $despedida = $conn->real_escape_string($_POST["despedida"]);
    $tempo = (int) $_POST["tempo"];

    // Montar a consulta SQL para atualizar os dados
    $sql = "UPDATE login SET 
                IA_boas_vindas = '$boas_vindas',
                IA_prompt = '$prompt',
                IA_despedida = '$despedida',
                tempo_final = $tempo
            WHERE login = '$login'"; // Altere para o ID adequado, caso seja necessário

    // Executar a consulta e verificar se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        #echo "Dados atualizados com sucesso!";
        VaiPara('prompt.php?pagina_nome=16&confirmacao=atualizado');
    } else {
        echo "Erro ao atualizar dados: " . $conn->error;
    }
}

// Fechar a conexão
$conn->close();
?>
