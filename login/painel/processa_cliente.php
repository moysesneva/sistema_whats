<?php
session_start();
include 'conn.php';
include 'funcoes.php';
salvar_dados_resquest();

if (
    isset($_SERVER['CONTENT_TYPE']) &&
    (stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false ||
     stripos($_SERVER['CONTENT_TYPE'], 'text/plain') !== false)
) {
    $inputJSON = file_get_contents("php://input");
    $jsonData = json_decode($inputJSON, true);
    if (is_array($jsonData)) {
        $_POST = array_merge($_POST, $jsonData);
    }
}

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

function gerarCodigoAleatorio($tamanho = 4) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $codigo = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $indice = rand(0, strlen($caracteres) - 1);
        $codigo .= $caracteres[$indice];
    }
    return $codigo;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido']);
    exit;
}

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
$usuario_api = isset($_POST['usuario_api']) ? trim($_POST['usuario_api']) : '';

if (empty($usuario_api)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Usuário API não fornecido']);
    exit;
}

switch ($acao) {
    case 'adicionar':
        adicionarCliente();
        break;
    case 'editar':
        editarCliente();
        break;
    case 'excluir':
        excluirCliente();
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
        exit;
}

function adicionarCliente() {
    global $conn, $usuario_api;

    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';

    if (empty($nome) || empty($telefone)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Nome e telefone são obrigatórios']);
        exit;
    }

    $id_agendamento = gerarCodigoAleatorio();
    $telefone = preg_replace('/\D/', '', $telefone);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO clientes (nome, telefone, usuario_api, id_agendamento) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nome, $telefone, $usuario_api, $id_agendamento);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente adicionado com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar cliente: ' . mysqli_error($conn)]);
    }
}

function editarCliente() {
    global $conn, $usuario_api;

    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';

    if ($id_cliente <= 0 || empty($nome) || empty($telefone)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Dados inválidos para edição']);
        exit;
    }

    $stmt_check = mysqli_prepare($conn, "SELECT id FROM clientes WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt_check, "is", $id_cliente, $usuario_api);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) == 0) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este cliente']);
        exit;
    }

    $telefone = preg_replace('/\D/', '', $telefone);

    $stmt = mysqli_prepare($conn,
        "UPDATE clientes SET nome = ?, telefone = ? WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt, "ssis", $nome, $telefone, $id_cliente, $usuario_api);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente atualizado com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar cliente: ' . mysqli_error($conn)]);
    }
}

function excluirCliente() {
    global $conn, $usuario_api;

    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;

    if ($id_cliente <= 0) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'ID de cliente inválido']);
        exit;
    }

    $stmt_check = mysqli_prepare($conn, "SELECT id FROM clientes WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt_check, "is", $id_cliente, $usuario_api);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) == 0) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para excluir este cliente']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM clientes WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt, "is", $id_cliente, $usuario_api);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente excluído com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir cliente: ' . mysqli_error($conn)]);
    }
}
?>
