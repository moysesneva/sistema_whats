<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'conn.php';
include 'funcoes.php';
include 'api/editacodigo.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' ) {
    $id_agendamento = intval($_GET['id']);
    $idd = $id_agendamento;

    $stmt_busca = $conn->prepare("SELECT * FROM agendamento WHERE id = ?");
    $stmt_busca->bind_param("i", $id_agendamento);
    $stmt_busca->execute();
    $query_busca_agendamento = $stmt_busca->get_result();

    if ($query_busca_agendamento->num_rows > 0) {
        $stmt_del = $conn->prepare("DELETE FROM agendamento WHERE id = ?");
        $stmt_del->bind_param("i", $id_agendamento);
        if ($stmt_del->execute()) {
            $stmt_del->close();

      if ($query_busca_agendamento->num_rows > 0) {
    while ($row = $query_busca_agendamento->fetch_assoc()) {
        $id = $row['id'];
        $usuario_api = $row['usuario_api'];
        $login = $row['login'];
        $dia = $row['dia'];
        $horario = $row['horario'];
        $profissional_nome = $row['profissional_nome'];
        $profissional_cargo = $row['profissional_cargo'];
        $telefone = $row['cliente_telefone'];
        $cliente_nome = $row['cliente_nome'];
        $data = $row['data'];
        $id_profissional = $row['id_profissional'];
        $servico_id = $row['servico_id'];
        $valor_servico = $row['valor_servico'];
    }
} else {
    echo "Nenhum agendamento encontrado.";
}
$stmt_busca->close();

    $stmt_usu = $conn->prepare("SELECT * FROM login WHERE usuario_api = ?");
    $stmt_usu->bind_param("s", $usuario_api);
    $stmt_usu->execute();
    $query_busca_usuario = $stmt_usu->get_result();
    if ($row_usuario = $query_busca_usuario->fetch_array()) {
        $login = $row_usuario['login'];
        $agenda_cancela = $row_usuario['agenda_cancela'];
        $cancela_prof = $row_usuario['cancela_prof'];
    }
    $stmt_usu->close();

    $stmt_cli = $conn->prepare("SELECT * FROM clientes WHERE telefone = ?");
    $stmt_cli->bind_param("s", $telefone);
    $stmt_cli->execute();
    $query_busca_clientes = $stmt_cli->get_result();
    if ($row = $query_busca_clientes->fetch_array()) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    }
    $stmt_cli->close();

    $stmt_serv = $conn->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt_serv->bind_param("i", $servico_id);
    $stmt_serv->execute();
    $query_serv = $stmt_serv->get_result();
    while($rows_usuarios = $query_serv->fetch_array()) {
        $servico_nome = $rows_usuarios['nome'];
    }
    $stmt_serv->close();

    $stmt_prof = $conn->prepare("SELECT * FROM profissional WHERE id = ?");
    $stmt_prof->bind_param("i", $id_profissional);
    $stmt_prof->execute();
    $query_prof = $stmt_prof->get_result();
    while($rows_usuarios = $query_prof->fetch_array()) {
        $telefone_profissional = $rows_usuarios['telefone'];
    }
    $stmt_prof->close();

    function novo_texto($string, $nome, $data,$horario, $profissional_nome,$servico_nome,$valor_servico,$telefone) {
            $substituicoes = [
                '{nome}' => $nome,
                '{data_agendamento}' => $data,
                 '{hora_agendamento}' => $horario,
                  '{profissional}' => $profissional_nome,
                   '{serviço}' => $servico_nome,
                    '{preço_serviço}' => '$'.$valor_servico,
                   '{telefone_cliente}' => $telefone
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
            
    $agenda_cancela = novo_texto($agenda_cancela, $nome, $data,$horario, $profissional_nome,$servico_nome,$valor_servico,$telefone);

  $id_msg = mysqli_insert_id($conn);
        $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $agenda_cancela, $id_msg);

    $stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '1', ?)");
    $stmt_env->bind_param("sss", $telefone, $agenda_cancela, $usuario_api);
    $stmt_env->execute();
    $stmt_env->close();

            VaiPara("agenda.php?pagina_nome=18&confirmacao=atualizado");
            exit;
        } else {
            VaiPara("agenda.php?pagina_nome=18&status=erro");
            exit;
        }
    } else {
        $stmt_busca->close();
        VaiPara("agenda.php?pagina_nome=18&status=naoencontrado");
        exit;
    }
} else {
    VaiPara("agenda.php?pagina_nome=18&status=erro");
    exit;
}
?>
