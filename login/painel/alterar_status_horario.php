<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Em um ambiente real, você incluiria seu arquivo de conexão.
include 'conn.php';

// --- Bloco de simulação de conexão (substitua pelo seu conn.php) ---
// --- Fim do bloco de simulação ---


if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Por favor, faça o login.']);
    exit();
}
$login = $_SESSION['login']; // Use o login da sessão para segurança

// ===================================
// LÓGICA DO SCRIPT
// ===================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['horario_id']) || !isset($_POST['novo_status'])) {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida. Faltam parâmetros.']);
    exit();
}

$horario_id = (int)$_POST['horario_id'];
$novo_status = (int)$_POST['novo_status'];

// Valida se o status é apenas 0 ou 1
if ($novo_status !== 0 && $novo_status !== 1) {
    echo json_encode(['success' => false, 'message' => 'Status inválido.']);
    exit();
}

try {
    // A subquery na cláusula WHERE é uma camada extra de segurança para garantir
    // que um usuário não possa modificar horários que não pertencem à sua conta.
    $sql = "
        UPDATE horarios_profissional 
        SET ativo = ? 
        WHERE id = ? AND profissional_id IN (SELECT id FROM profissional WHERE login = ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $novo_status, $horario_id, $login);
    
    if (mysqli_stmt_execute($stmt)) {
        // Verifica se alguma linha foi realmente alterada
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $acao = $novo_status == 1 ? 'ativado' : 'desativado';
            echo json_encode(['success' => true, 'message' => "Horário $acao com sucesso!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum horário foi alterado. Verifique o ID ou suas permissões.']);
        }
    } else {
        throw new Exception("Erro ao executar a atualização: " . mysqli_stmt_error($stmt));
    }

} catch (Exception $e) {
    // Em um sistema de produção, você poderia logar este erro em um arquivo.
    // error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro no servidor.']);
} finally {
    mysqli_close($conn);
}