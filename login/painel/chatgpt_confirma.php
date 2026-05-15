<?php
session_start();
$login = $_SESSION['login'];
include 'conn.php';
include 'funcoes.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boas_vindas = $_POST["boas_vindas"] ?? '';
    $prompt = $_POST["prompt"] ?? '';
    $despedida = $_POST["despedida"] ?? '';
    $tempo = (int)($_POST["tempo"] ?? 0);

    $stmt = $conn->prepare("UPDATE login SET IA_boas_vindas = ?, IA_prompt = ?, IA_despedida = ?, tempo_final = ? WHERE login = ?");
    $stmt->bind_param("sssis", $boas_vindas, $prompt, $despedida, $tempo, $login);
    if ($stmt->execute()) {
        $stmt->close();
        VaiPara('prompt.php?pagina_nome=16&confirmacao=atualizado');
    } else {
        echo "Erro ao atualizar dados: " . $conn->error;
        $stmt->close();
    }
}

$conn->close();
?>
