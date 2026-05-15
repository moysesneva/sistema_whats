<?php
#session_start();
include 'login/painel/conn.php';
include 'login/painel/funcoes.php';
include 'login/painel/api/editacodigo.php';

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}


// Verifica se os campos obrigatórios foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_api'], $_POST['idd'], $_POST['profissional'], $_POST['agendamento'])) {
    $usuario_api = $_POST['usuario_api'];
    $idd = $_POST['idd'];

    // Consulta para obter nome e telefone do cliente com base no id do agendamento
    $stmt_cli = $conn->prepare("SELECT * FROM clientes WHERE id_agendamento = ?");
    $stmt_cli->bind_param("s", $idd);
    $stmt_cli->execute();
    $query_busca_clientes = $stmt_cli->get_result();
    if ($row = $query_busca_clientes->fetch_array()) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    } else {
        die("Cliente não encontrado.");
    }
    $stmt_cli->close();

    // Busca o login do usuário com base no `usuario_api`
    $stmt_usu = $conn->prepare("SELECT * FROM login WHERE usuario_api = ?");
    $stmt_usu->bind_param("s", $usuario_api);
    $stmt_usu->execute();
    $query_busca_usuario = $stmt_usu->get_result();
    if ($row_usuario = $query_busca_usuario->fetch_array()) {
        $login = $row_usuario['login'];
        $agenda_confirma = $row_usuario['agenda_confirma'];
    } else {
        die("Usuário não encontrado.");
    }
    $stmt_usu->close();

    // Processamento da data e horário
    list($data, $horario) = explode('|', $_POST['agendamento']);
    $timestamp = strtotime($data);
    $dias_semana_portugues = array('0' => 'domingo', '1' => 'segunda', '2' => 'terça', '3' => 'quarta', '4' => 'quinta', '5' => 'sexta', '6' => 'sábado');
    $dia_numero = date('w', $timestamp);
    $dia_semana = $dias_semana_portugues[$dia_numero];

    $id_profissional = intval($_POST['profissional']);

    // Consulta para obter informações do profissional
    $sql_profissional = "SELECT profissional_nome, profissional_cargo FROM profissional WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_profissional);
    mysqli_stmt_bind_param($stmt, "i", $id_profissional);
    mysqli_stmt_execute($stmt);
    $result_profissional = mysqli_stmt_get_result($stmt);

    if ($row_profissional = mysqli_fetch_assoc($result_profissional)) {
        $profissional_nome = $row_profissional['profissional_nome'];
        $profissional_cargo = $row_profissional['profissional_cargo'];
    } else {
        die("Profissional não encontrado.");
    }

    // Verificação de disponibilidade do horário e inserção do agendamento
    $sql_verifica = "SELECT * FROM agendamento WHERE id_profissional = ? AND data = ? AND horario = ?";
    $stmt_verifica = mysqli_prepare($conn, $sql_verifica);
    mysqli_stmt_bind_param($stmt_verifica, "iss", $id_profissional, $data, $horario);
    mysqli_stmt_execute($stmt_verifica);
    $result_verifica = mysqli_stmt_get_result($stmt_verifica);

    if (mysqli_fetch_assoc($result_verifica)) {
        VaiPara('agendar.php?id=' . $idd . '&erro=duplicado');
    }

date_default_timezone_set('America/Sao_Paulo');

$data_hora_atual = date('Y-m-d H:i');
$data_hora_agendamento = $data . ' ' . $horario;

if (strtotime($data_hora_agendamento) < strtotime($data_hora_atual)) {
    VaiPara('agendar.php?id=' . $idd . '&erro=horario_passado');
    exit;
}

    $sql_inserir = "INSERT INTO agendamento (usuario_api, login, dia, horario, profissional_nome, profissional_cargo, cliente_telefone, cliente_nome, data, id_profissional) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_inserir = mysqli_prepare($conn, $sql_inserir);
    mysqli_stmt_bind_param($stmt_inserir, "sssssssssi", $usuario_api, $login, $dia_semana, $horario, $profissional_nome, $profissional_cargo, $telefone, $nome, $data, $id_profissional);

    if (mysqli_stmt_execute($stmt_inserir)) {
        function novo_texto($string, $nome, $agendamento, $profissional) {
            $substituicoes = [
                '{nome}' => $nome,
                '{agendamento}' => $agendamento,
                '{profissional}' => $profissional
            ];
            return str_replace(array_keys($substituicoes), array_values($substituicoes), $string);
        }

        function formatar_data_brasileira($data) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                return date("d/m/Y", strtotime($data));
            } else {
                return $data;
            }
        }

        $data_formatada = formatar_data_brasileira($data);
        $agendamento = $horario . " " . $data_formatada;
        $profissional = $profissional_nome;
        $agenda_confirma = novo_texto($agenda_confirma, $nome, $agendamento, $profissional);

        $msg = $agenda_confirma;

        $stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
        $stmt_env->bind_param("sss", $telefone, $agenda_confirma, $usuario_api);
        $stmt_env->execute();
        $stmt_env->close();
        $id_msg = mysqli_insert_id($conn);
        $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $msg, $id_msg);

        VaiPara('agendar.php?id=' . $idd . '&agenda=atualizado');
    } else {
        echo "Erro ao realizar o agendamento: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Dados insuficientes ou método inválido.";
}
?>
