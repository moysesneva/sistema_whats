<?php
// Conexão com o banco de dados
include 'login/painel/conn.php';

// Define o tipo de retorno da resposta como JSON
header("Content-Type: application/json");

// Recebe os dados do webhook (JSON enviado via POST)
$dadosJson = file_get_contents("php://input");
$dados = json_decode($dadosJson, true);

// Verifica se o JSON é válido
if ($dados === null) {
    http_response_code(400);
    echo json_encode(["erro" => "JSON inválido ou nenhum dado recebido."]);
    exit;
}

// Busca a chave de segurança salva na tabela config
$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$chave = null;
if ($row = mysqli_fetch_array($query_busca_config)) {
    $chave = $row['chave'];
}

// Verifica se a chave foi enviada via URL (GET)
if (!isset($_GET['chave'])) {
    exit('Chave não informada. Código encerrado.');
}

// Compara a chave recebida com a chave esperada
$chave_kiwify = $_GET['chave'];
if ($chave_kiwify !== $chave) {
    exit('Chave incorreta. Código encerrado.');
}

// Função para calcular a data de vencimento (30 dias a partir de hoje)
function dataDeVencimento() {
    $dataAtual = new DateTime();
    $dataAtual->modify('+30 days');
    return $dataAtual->format('Y-m-d');
}
$vencimento = dataDeVencimento();











// EVENTO: Pedido aprovado
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'order_approved') {
    
    
    
    
    
    
   
$sql_busca_modulos = "SELECT * FROM planos_clientes WHERE nome_plano = 'plano3' AND tipo = 0";
$query = mysqli_query($conn, $sql_busca_modulos);
$total_plano = mysqli_num_rows($query);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $nome_modulo = $rows_usuarios['nome_modulo'];
}   
     


$sql_busca_modulos = "SELECT * FROM modulos_lista WHERE nome_modulo = '$nome_modulo'";
$query = mysqli_query($conn, $sql_busca_modulos);
$total_plano = mysqli_num_rows($query);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $creditos = $rows_usuarios['creditos'];
}   
     
 
    
    
    
    
    
 #$creditos = '10';   
    
    
    
    
    $emailCliente = $dados['Customer']['email'] ?? null;
    $subscriptionId = $dados['subscription_id'] ?? null;

    $sql = "UPDATE login 
            SET vencimento = '$vencimento',
                id_assinatura = '$subscriptionId',
                situacao = 'ativado',
                tipo = '2',
                funcao = 'IA', plano = 'plano3', creditos = '$creditos'
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    if ($query) {
        http_response_code(201);
        echo json_encode(["mensagem" => "Pagamento atualizado com sucesso."]);
    } else {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao atualizar pagamento: " . mysqli_error($conn)]);
    }
}

// EVENTO: Renovação de assinatura
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_renewed') {
    $emailCliente = $dados['Customer']['email'] ?? null;
    $id_assinatura = $dados['subscription_id'] ?? null;




   
    
   
$sql_busca_modulos = "SELECT * FROM planos_clientes WHERE nome_plano = 'plano3' AND tipo = 0";
$query = mysqli_query($conn, $sql_busca_modulos);
$total_plano = mysqli_num_rows($query);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $nome_modulo = $rows_usuarios['nome_modulo'];
}   
     


$sql_busca_modulos = "SELECT * FROM modulos_lista WHERE nome_modulo = '$nome_modulo'";
$query = mysqli_query($conn, $sql_busca_modulos);
$total_plano = mysqli_num_rows($query);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $creditos = $rows_usuarios['creditos'];
}   
     

















    $sql = "UPDATE login 
            SET vencimento = '$vencimento',
                id_assinatura = '$id_assinatura',
                situacao = 'ativado',
                tipo = '2',
                 funcao = 'IA',plano = 'plano3', creditos = '$creditos'
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    echo $query
        ? "Assinatura renovada com sucesso para o cliente: $emailCliente"
        : "Erro ao atualizar a assinatura: " . mysqli_error($conn);
}

// EVENTO: Assinatura em atraso
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_late') {
    $emailCliente = $dados['Customer']['email'] ?? null;

    $sql = "UPDATE login 
            SET tipo = '3',
                id_assinatura = 'bloqueado',
                situacao = 'bloqueado',
                funcao = NULL
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    echo $query
        ? "Assinatura marcada como atrasada para o cliente: $emailCliente"
        : "Erro ao atualizar a assinatura: " . mysqli_error($conn);
}

// EVENTO: Assinatura cancelada
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'subscription_canceled') {
    $emailCliente = $dados['Customer']['email'] ?? null;

    $sql = "UPDATE login 
            SET tipo = '3',
                id_assinatura = 'cancelado',
                situacao = 'cancelado',
                funcao = NULL
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    echo $query
        ? "Assinatura cancelada para o cliente: $emailCliente"
        : "Erro ao atualizar a assinatura: " . mysqli_error($conn);
}

// EVENTO: Pedido reembolsado
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'order_refunded') {
    $emailCliente = $dados['Customer']['email'] ?? null;

    $sql = "UPDATE login 
            SET tipo = '3',
                id_assinatura = 'reembolso',
                situacao = 'reembolso',
                funcao = NULL
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    echo $query
        ? "Pedido reembolsado. Assinatura atualizada para o cliente: $emailCliente"
        : "Erro ao atualizar a assinatura: " . mysqli_error($conn);
}

// EVENTO: Estorno (chargeback)
if (isset($dados['webhook_event_type']) && $dados['webhook_event_type'] === 'chargeback') {
    $emailCliente = $dados['Customer']['email'] ?? null;

    $sql = "UPDATE login 
            SET tipo = '3',
                id_assinatura = 'chargeback',
                situacao = 'chargeback',
                funcao = NULL
            WHERE email = '$emailCliente'";
    
    $query = mysqli_query($conn, $sql);

    echo $query
        ? "Chargeback registrado para o cliente: $emailCliente"
        : "Erro ao atualizar a assinatura: " . mysqli_error($conn);
}
?>
