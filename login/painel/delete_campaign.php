<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir conexão com banco de dados
require_once 'conn.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Obter dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);

// Verificar se o ID da campanha foi fornecido
if (!isset($input['campaign_id']) || empty($input['campaign_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID da campanha é obrigatório'
    ]);
    exit;
}

$campaign_id = (int)$input['campaign_id'];

// Verificar se a campanha existe
$check_query = "SELECT id, campaign_name FROM mensagens_massa WHERE id = ?";
$stmt_check = mysqli_prepare($conn, $check_query);

if (!$stmt_check) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro na preparação da consulta: ' . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt_check, 'i', $campaign_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) === 0) {
    mysqli_stmt_close($stmt_check);
    echo json_encode([
        'success' => false,
        'message' => 'Campanha não encontrada'
    ]);
    exit;
}

$campaign_data = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

// Iniciar transação
mysqli_autocommit($conn, false);

try {
    // Deletar a campanha
    $delete_query = "DELETE FROM mensagens_massa WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $delete_query);
    
    if (!$stmt_delete) {
        throw new Exception('Erro na preparação da consulta de exclusão: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt_delete, 'i', $campaign_id);
    $delete_result = mysqli_stmt_execute($stmt_delete);
    
    if (!$delete_result) {
        throw new Exception('Erro ao executar exclusão: ' . mysqli_stmt_error($stmt_delete));
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    
    if ($affected_rows === 0) {
        throw new Exception('Nenhuma campanha foi deletada');
    }
    
    // Confirmar transação
    mysqli_commit($conn);
    
    // Log da operação (opcional)
    error_log("Campanha deletada: ID {$campaign_id} - {$campaign_data['campaign_name']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Campanha deletada com sucesso',
        'campaign_name' => $campaign_data['campaign_name']
    ]);
    
} catch (Exception $e) {
    // Desfazer transação em caso de erro
    mysqli_rollback($conn);
    
    error_log("Erro ao deletar campanha: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}

// Restaurar autocommit
mysqli_autocommit($conn, true);

// Fechar conexão
mysqli_close($conn);
?>