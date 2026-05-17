<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
include 'conn.php';
$login = $_SESSION['login'];

header('Content-Type: application/json');

$response = array(
    'success' => false,
    'message' => '',
    'data' => null
);

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!isset($_SESSION['login'])) {
        throw new Exception('Sessão expirada');
    }

    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    switch ($acao) {
        case 'salvar':
            $nome = trim($_POST['nome'] ?? '');
            $duracao = intval($_POST['duracao'] ?? 0);
            $valor = floatval($_POST['valor'] ?? 0);
            $descricao = trim($_POST['descricao'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');

            if (empty($nome)) {
                throw new Exception('Nome do serviço é obrigatório');
            }
            if ($duracao < 15) {
                throw new Exception('Duração mínima é 15 minutos');
            }
            if ($valor < 0) {
                throw new Exception('Valor não pode ser negativo');
            }

            $stmt_v = mysqli_prepare($conn, "SELECT id FROM servicos WHERE nome = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_v, "ss", $nome, $login);
            mysqli_stmt_execute($stmt_v);
            $res_v = mysqli_stmt_get_result($stmt_v);

            if (mysqli_num_rows($res_v) > 0) {
                throw new Exception('Já existe um serviço com este nome');
            }

            $stmt = mysqli_prepare($conn,
                "INSERT INTO servicos (nome, descricao, duracao_minutos, valor, categoria, login, ativo)
                 VALUES (?, ?, ?, ?, ?, ?, 1)");
            mysqli_stmt_bind_param($stmt, "ssidss", $nome, $descricao, $duracao, $valor, $categoria, $login);

            if (!mysqli_stmt_execute($stmt)) {
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
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $duracao = intval($_POST['duracao'] ?? 0);
            $valor = floatval($_POST['valor'] ?? 0);
            $descricao = trim($_POST['descricao'] ?? '');

            $stmt_v = mysqli_prepare($conn, "SELECT id FROM servicos WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_v, "is", $id, $login);
            mysqli_stmt_execute($stmt_v);
            $res_v = mysqli_stmt_get_result($stmt_v);

            if (mysqli_num_rows($res_v) == 0) {
                throw new Exception('Serviço não encontrado');
            }

            $stmt = mysqli_prepare($conn,
                "UPDATE servicos SET nome = ?, descricao = ?, duracao_minutos = ?, valor = ?, atualizado_em = NOW()
                 WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt, "ssidis", $nome, $descricao, $duracao, $valor, $id, $login);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Erro ao atualizar serviço');
            }

            $response['success'] = true;
            $response['message'] = 'Serviço atualizado com sucesso!';
            break;

        case 'toggle':
            $id = intval($_POST['id'] ?? 0);

            $stmt_s = mysqli_prepare($conn, "SELECT ativo FROM servicos WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_s, "is", $id, $login);
            mysqli_stmt_execute($stmt_s);
            $res_s = mysqli_stmt_get_result($stmt_s);

            if (mysqli_num_rows($res_s) == 0) {
                throw new Exception('Serviço não encontrado');
            }

            $servico = mysqli_fetch_array($res_s);
            $novo_status = $servico['ativo'] == 1 ? 0 : 1;

            $stmt = mysqli_prepare($conn, "UPDATE servicos SET ativo = ? WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt, "iis", $novo_status, $id, $login);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Erro ao alterar status');
            }

            $response['success'] = true;
            $response['message'] = 'Status alterado com sucesso!';
            $response['data'] = array('novo_status' => $novo_status);
            break;

        case 'listar':
            $stmt = mysqli_prepare($conn, "SELECT * FROM servicos WHERE login = ? ORDER BY nome");
            mysqli_stmt_bind_param($stmt, "s", $login);
            mysqli_stmt_execute($stmt);
            $query = mysqli_stmt_get_result($stmt);

            $servicos = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $servicos[] = $row;
            }

            $response['success'] = true;
            $response['data'] = $servicos;
            break;

        case 'buscar':
            $id = intval($_POST['id'] ?? 0);

            $stmt = mysqli_prepare($conn, "SELECT * FROM servicos WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt, "is", $id, $login);
            mysqli_stmt_execute($stmt);
            $query = mysqli_stmt_get_result($stmt);

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

echo json_encode($response);

mysqli_close($conn);
?>
