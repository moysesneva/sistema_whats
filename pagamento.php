<?php
include 'login/painel/conn.php';

header("Content-Type: application/json");

$dadosJson = file_get_contents("php://input");
$dados = json_decode($dadosJson, true);
if ($dados === null) {
    http_response_code(400);
    echo json_encode(["erro" => "JSON inválido ou nenhum dado recebido."]);
    exit;
}


$sql_busca_config = "SELECT * FROM config ";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {

    $chave  = $rows_config['chave'];

}



// Verifica se o parâmetro "chave_lkiwify" existe na URL
if (!isset($_GET['chave'])) {
    exit('Chave não informada. Código encerrado.');
}

// Recupera o valor da chave informado na URL
$chave_kiwify = $_GET['chave'];

// Verifica se o valor informado é diferente do valor esperado
if ($chave_kiwify !== $chave) {
    exit('Chave incorreta. Código encerrado.');
}

function dataDeVencimento() {
    $dataAtual = new DateTime();
    $dataAtual->modify('+30 days');
    return $dataAtual->format('Y-m-d');
}
$vencimento = dataDeVencimento();

if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'order_approved') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $subscriptionId = $dados['subscription_id'] ?? null;
    $sql = "UPDATE login SET vencimento = '$vencimento', id_assinatura = '$subscriptionId', situacao = 'ativado', tipo = '2',funcao='IA' WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        http_response_code(201);
        echo json_encode(["mensagem" => "Pagamento atualizado com sucesso."]);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao atualizar pagamento: " . mysqli_error($conn)]);
    }
}

if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_renewed') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $id_assinatura = $dados['subscription_id'] ?? null;
    $sql = "UPDATE login SET vencimento = '$vencimento', id_assinatura = '$id_assinatura', situacao = 'ativado', tipo = '2',funcao='IA' WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura renovada com sucesso para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}

if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_late') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $sql = "UPDATE login SET tipo = '3', id_assinatura = 'bloqueado', situacao = 'bloqueado',funcao = NULL WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura marcada como atrasada para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}

if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_canceled') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $sql = "UPDATE login SET tipo = '3', id_assinatura = 'cancelado', situacao = 'cancelado',funcao = NULL WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura marcada como atrasada para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}


if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'order_refunded') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $sql = "UPDATE login SET tipo = '3', id_assinatura = 'reembolso', situacao = 'reembolso',funcao = NULL WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura marcada como atrasada para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}


if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'chargeback') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $sql = "UPDATE login SET tipo = '3', id_assinatura = 'chargeback' , situacao = 'chargeback',funcao = NULL WHERE email = '$emailCliente'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura marcada como atrasada para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}


















if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_canceled') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    #$sql = "DELETE FROM login WHERE email = '$emailCliente'";
    #$query = mysqli_query($conn, $sql);
    if ($query) {
        echo "Assinatura cancelada com sucesso para o cliente: $emailCliente";
    } else {
        echo "Erro ao atualizar a assinatura: " . mysqli_error($conn);
    }
}
?>
