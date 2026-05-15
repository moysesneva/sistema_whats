<?php
session_start();
include 'funcoes.php';
include 'conn.php';
$login = $_SESSION['login'];

// Definir header para retorno JSON
header('Content-Type: application/json');

// Array de resposta
$response = array(
    'success' => false,
    'message' => '',
    'data' => null
);

try {
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método não permitido');
    }
    
    // Verificar sessão
    if (!isset($_SESSION['login'])) {
        throw new Exception('Sessão expirada');
    }
    
    // Capturar e validar dados
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';
    
    switch ($acao) {
        case 'salvar':
            // Dados do novo serviço
            $nome = mysqli_real_escape_string($conn, $_POST['nome']);
            $duracao = intval($_POST['duracao']);
            $valor = floatval($_POST['valor']);
            $descricao = mysqli_real_escape_string($conn, isset($_POST['descricao']) ? $_POST['descricao'] : '');
            $categoria = mysqli_real_escape_string($conn, isset($_POST['categoria']) ? $_POST['categoria'] : '');
            
            // Validações
            if (empty($nome)) {
                throw new Exception('Nome do serviço é obrigatório');
            }
            
            if ($duracao < 15) {
                throw new Exception('Duração mínima é 15 minutos');
            }
            
            if ($valor < 0) {
                throw new Exception('Valor não pode ser negativo');
            }
            
            // Verificar se já existe
            $sql_verifica = "SELECT id FROM servicos WHERE nome = '$nome' AND login = '$login'";
            $query_verifica = mysqli_query($conn, $sql_verifica);
            
            if (mysqli_num_rows($query_verifica) > 0) {
                throw new Exception('Já existe um serviço com este nome');
            }
            
            // Inserir serviço
            $sql = "INSERT INTO servicos (nome, descricao, duracao_minutos, valor, categoria, login, ativo) 
                    VALUES ('$nome', '$descricao', '$duracao', '$valor', '$categoria', '$login', 1)";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Erro ao salvar serviço: ' . mysqli_error($conn));
            }
            
            $servico_id = mysqli_insert_id($conn);
            
            $response['success'] = true;
            $response['message'] = 'Serviço cadastrado com sucesso!';
            $response['data'] = array(
                'id' => $servico_id,
                'nome' => $nome,
                'duracao' => $duracao,
                'valor' => $valor
            );
            break;
            
        case 'editar':
            $id = intval($_POST['id']);
            $nome = mysqli_real_escape_string($conn, $_POST['nome']);
            $duracao = intval($_POST['duracao']);
            $valor = floatval($_POST['valor']);
            $descricao = mysqli_real_escape_string($conn, isset($_POST['descricao']) ? $_POST['descricao'] : '');
            
            // Verificar se o serviço pertence ao usuário
            $sql_verifica = "SELECT id FROM servicos WHERE id = '$id' AND login = '$login'";
            $query_verifica = mysqli_query($conn, $sql_verifica);
            
            if (mysqli_num_rows($query_verifica) == 0) {
                throw new Exception('Serviço não encontrado');
            }
            
            // Atualizar serviço
            $sql = "UPDATE servicos SET 
                    nome = '$nome',
                    descricao = '$descricao',
                    duracao_minutos = '$duracao',
                    valor = '$valor',
                    atualizado_em = NOW()
                    WHERE id = '$id' AND login = '$login'";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Erro ao atualizar serviço');
            }
            
            $response['success'] = true;
            $response['message'] = 'Serviço atualizado com sucesso!';
            break;
            
        case 'toggle':
            $id = intval($_POST['id']);
            
            // Buscar status atual
            $sql_status = "SELECT ativo FROM servicos WHERE id = '$id' AND login = '$login'";
            $query_status = mysqli_query($conn, $sql_status);
            
            if (mysqli_num_rows($query_status) == 0) {
                throw new Exception('Serviço não encontrado');
            }
            
            $servico = mysqli_fetch_array($query_status);
            $novo_status = $servico['ativo'] == 1 ? 0 : 1;
            
            // Atualizar status
            $sql = "UPDATE servicos SET ativo = '$novo_status' WHERE id = '$id' AND login = '$login'";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Erro ao alterar status');
            }
            
            $response['success'] = true;
            $response['message'] = 'Status alterado com sucesso!';
            $response['data'] = array('novo_status' => $novo_status);
            break;
            
        case 'listar':
            // Listar todos os serviços do usuário
            $sql = "SELECT * FROM servicos WHERE login = '$login' ORDER BY nome";
            $query = mysqli_query($conn, $sql);
            
            $servicos = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $servicos[] = $row;
            }
            
            $response['success'] = true;
            $response['data'] = $servicos;
            break;
            
        case 'buscar':
            $id = intval($_POST['id']);
            
            $sql = "SELECT * FROM servicos WHERE id = '$id' AND login = '$login'";
            $query = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($query) == 0) {
                throw new Exception('Serviço não encontrado');
            }
            
            $response['success'] = true;
            $response['data'] = mysqli_fetch_assoc($query);
            break;
            
        default:
            throw new Exception('Ação inválida');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Retornar resposta JSON
echo json_encode($response);

// Fechar conexão
mysqli_close($conn);





















//////////////////

?>