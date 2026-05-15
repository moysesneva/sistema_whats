<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Em um ambiente real, você incluiria seu arquivo de conexão.
include 'conn.php';



if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Por favor, faça o login.']);
    exit();
}
$login = $_SESSION['login']; // Use o login da sessão para segurança

// ===================================
// LÓGICA DO SCRIPT
// ===================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['horario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida. ID do horário não fornecido.']);
    exit();
}

$horario_id = (int)$_POST['horario_id'];

try {
    // A lógica de segurança aqui é a mesma dos outros arquivos:
    // só permite a exclusão se o horário pertencer a um profissional da conta do usuário logado.
    $sql = "
        DELETE FROM horarios_profissional 
        WHERE id = ? AND profissional_id IN (SELECT id FROM profissional WHERE login = ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $horario_id, $login);
    
    if (mysqli_stmt_execute($stmt)) {
        // Verifica se a exclusão foi bem-sucedida
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true, 'message' => 'Horário excluído com sucesso! A página será atualizada.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Não foi possível excluir o horário. Verifique o ID ou suas permissões.']);
        }
    } else {
        throw new Exception("Erro ao executar a exclusão: " . mysqli_stmt_error($stmt));
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro no servidor ao tentar excluir o horário.']);
} finally {
    mysqli_close($conn);
}