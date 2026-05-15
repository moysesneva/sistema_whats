<?php
include '../conn.php';
include '../funcoes.php'; // onde está a função enviarMensagem()
include 'editacodigo.php';

date_default_timezone_set('America/Sao_Paulo');

$usuario_api = 'agenda_553184767331';

// === Carregar configurações ===
$sql_config = "SELECT * FROM config LIMIT 1";
$query_config = mysqli_query($conn, $sql_config);
$config = mysqli_fetch_assoc($query_config);

$servidor = $config['ip_vps'];
$porta = $config['porta'];
$token = $config['chave'];

// === Buscar a campanha mais recente do usuário ===
$sql = "
    SELECT *
    FROM mensagens_massa
    WHERE usuario_api = ?
    ORDER BY id DESC
    LIMIT 1
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $usuario_api);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($campanha = mysqli_fetch_assoc($result)) {
    $id_campanha        = $campanha['id'];
    $mensagem_texto     = $campanha['message_text'];
    $clientes_ids       = json_decode($campanha['clientes_ids'], true);
    $enviados           = (int)$campanha['enviados'];
    $total_clientes     = (int)$campanha['total_clientes'];

    echo "🧪 Campanha encontrada:\n";
    echo "ID: $id_campanha\n";

    // === Verifica se ainda há cliente a ser atendido ===
    if ($enviados >= $total_clientes) {
        echo "✅ Campanha já finalizada.\n";
        exit;
    }

    // === Obter próximo cliente ===
    $proximo_cliente_id = $clientes_ids[$enviados];
    $sql_cliente = "SELECT nome, telefone FROM clientes WHERE id = ? AND usuario_api = ?";
    $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
    mysqli_stmt_bind_param($stmt_cliente, 'is', $proximo_cliente_id, $usuario_api);
    mysqli_stmt_execute($stmt_cliente);
    $res_cliente = mysqli_stmt_get_result($stmt_cliente);
    $cliente = mysqli_fetch_assoc($res_cliente);

    if (!$cliente) {
        echo "❌ Cliente não encontrado.\n";
        exit;
    }

    // === Personalizar mensagem ===
    $mensagem_final = str_replace('{nome}', $cliente['nome'], $mensagem_texto);
    $telefone_cliente = $cliente['telefone'];

    // === Enviar mensagem ===
    $response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone_cliente, $mensagem_final, $id_campanha);

    if (isset($response['status']) && $response['status'] === 'error') {
        echo "❌ Falha no envio: " . json_encode($response) . "\n";
        // Atualiza erros
        $sql_erro = "UPDATE mensagens_massa SET erros = erros + 1, log_erros = CONCAT(IFNULL(log_erros, ''), ?) WHERE id = ?";
        $log_erro_msg = "Erro ao enviar para {$telefone_cliente}: " . json_encode($response) . "\n";
        $stmt_erro = mysqli_prepare($conn, $sql_erro);
        mysqli_stmt_bind_param($stmt_erro, 'si', $log_erro_msg, $id_campanha);
        mysqli_stmt_execute($stmt_erro);
    } else {
        echo "✅ Mensagem enviada com sucesso para {$cliente['nome']} ({$telefone_cliente})\n";
        // Atualiza campanha
        $novo_enviados = $enviados + 1;
        $proximo_envio = date('Y-m-d H:i:s', time() + (int)$campanha['interval_seconds']);
        $sql_update = "UPDATE mensagens_massa SET enviados = ?, ultimo_envio = NOW(), proximo_envio = ? WHERE id = ?";
        $stmt_upd = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_upd, 'isi', $novo_enviados, $proximo_envio, $id_campanha);
        mysqli_stmt_execute($stmt_upd);
    }

} else {
    echo "❌ Nenhuma campanha encontrada para esse usuário.\n";
}
?>
