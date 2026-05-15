<?php
session_start();
include 'conn.php';
include 'funcoes.php';
salvar_dados_resquest();

// 🔄 Suporte a JSON ou text/plain como entrada
if (
    isset($_SERVER['CONTENT_TYPE']) &&
    (stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false ||
     stripos($_SERVER['CONTENT_TYPE'], 'text/plain') !== false)
) {
    $inputJSON = file_get_contents("php://input");
    $jsonData = json_decode($inputJSON, true);
    if (is_array($jsonData)) {
        $_POST = array_merge($_POST, $jsonData); // mescla com $_POST caso algo já exista
    }
}

// 🔒 Verifica sessão
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

// 🔡 Geração de código aleatório
function gerarCodigoAleatorio($tamanho = 4) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $codigo = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $indice = rand(0, strlen($caracteres) - 1);
        $codigo .= $caracteres[$indice];
    }
    return $codigo;
}

// ✅ Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido']);
    exit;
}

// 📥 Obter a ação e o usuário
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
$usuario_api = isset($_POST['usuario_api']) ? $_POST['usuario_api'] : '';

// 🔎 Verifica se usuário foi informado
if (empty($usuario_api)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Usuário API não fornecido']);
    exit;
}

// 🚦 Executar ação
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

// ✅ Adicionar cliente
function adicionarCliente() {
    global $conn, $usuario_api;

    $nome = isset($_POST['nome']) ? mysqli_real_escape_string($conn, $_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($conn, $_POST['telefone']) : '';

    if (empty($nome) || empty($telefone)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Nome e telefone são obrigatórios']);
        exit;
    }

    $id_agendamento = gerarCodigoAleatorio();
    $telefone = preg_replace('/\D/', '', $telefone);

    $sql = "INSERT INTO clientes (nome, telefone, usuario_api, id_agendamento) 
            VALUES ('$nome', '$telefone', '$usuario_api', '$id_agendamento')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente adicionado com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar cliente: ' . mysqli_error($conn)]);
    }
}

// ✏️ Editar cliente
function editarCliente() {
    global $conn, $usuario_api;

    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
    $nome = isset($_POST['nome']) ? mysqli_real_escape_string($conn, $_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($conn, $_POST['telefone']) : '';

    if ($id_cliente <= 0 || empty($nome) || empty($telefone)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Dados inválidos para edição']);
        exit;
    }

    $sql_check = "SELECT id FROM clientes WHERE id = $id_cliente AND usuario_api = '$usuario_api'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) == 0) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este cliente']);
        exit;
    }

    $telefone = preg_replace('/\D/', '', $telefone);

    $sql = "UPDATE clientes SET nome = '$nome', telefone = '$telefone' 
            WHERE id = $id_cliente AND usuario_api = '$usuario_api'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente atualizado com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar cliente: ' . mysqli_error($conn)]);
    }
}

// 🗑️ Excluir cliente
function excluirCliente() {
    global $conn, $usuario_api;

    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;

    if ($id_cliente <= 0) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'ID de cliente inválido']);
        exit;
    }

    $sql_check = "SELECT id FROM clientes WHERE id = $id_cliente AND usuario_api = '$usuario_api'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) == 0) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para excluir este cliente']);
        exit;
    }

    $sql = "DELETE FROM clientes WHERE id = $id_cliente AND usuario_api = '$usuario_api'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente excluído com sucesso']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir cliente: ' . mysqli_error($conn)]);
    }
}
?>
