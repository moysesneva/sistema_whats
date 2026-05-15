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
    $sql_busca_clientes = "SELECT * FROM clientes WHERE id_agendamento = '$idd'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    if ($row = mysqli_fetch_array($query_busca_clientes)) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    } else {
        die("Cliente não encontrado.");
    }

    // Busca o login do usuário com base no `usuario_api`
    $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
        $login = $row_usuario['login'];
        $agenda_confirma = $row_usuario['agenda_confirma'];
    } else {
        die("Usuário não encontrado.");
    }

    // Processamento da data e horário
    list($data, $horario) = explode('|', $_POST['agendamento']);
    $timestamp = strtotime($data);
    $dias_semana_portugues = array('0' => 'domingo', '1' => 'segunda', '2' => 'terça', '3' => 'quarta', '4' => 'quinta', '5' => 'sexta', '6' => 'sábado');
    $dia_numero = date('w', $timestamp);
    $dia_semana = $dias_semana_portugues[$dia_numero]; // Dia da semana em português

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


    // Ajustar o fuso horário para o Brasil (se necessário)
#O SISTEMA
     
date_default_timezone_set('America/Sao_Paulo');

// Obter a data e o horário atual no formato correto
$data_hora_atual = date('Y-m-d H:i');

// Combinar a data e o horário do agendamento em um único formato
$data_hora_agendamento = $data . ' ' . $horario;

// Verificar se o horário do agendamento já passou
if (strtotime($data_hora_agendamento) < strtotime($data_hora_atual)) {
    // Redirecionar com erro caso o horário seja inválido
    VaiPara('agendar.php?id=' . $idd . '&erro=horario_passado');
    exit; // Interrompe o script para evitar o INSERT
}

    // Corrigindo a inserção para usar o dia da semana ($dia_semana) em vez de $profissional_nome
    $sql_inserir = "INSERT INTO agendamento (usuario_api, login, dia, horario, profissional_nome, profissional_cargo, cliente_telefone, cliente_nome, data, id_profissional) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_inserir = mysqli_prepare($conn, $sql_inserir);
    mysqli_stmt_bind_param($stmt_inserir, "sssssssssi", $usuario_api, $login, $dia_semana, $horario, $profissional_nome, $profissional_cargo, $telefone, $nome, $data, $id_profissional);

    if (mysqli_stmt_execute($stmt_inserir)) {
        // Funções para formatar texto e data
        function novo_texto($string, $nome, $data, $horario,$telefone,$profissional_nome) {
            $substituicoes = [
                '{nome}' => $nome,
                '{data_agendamento}' => $data,
                '{hora_agendamento}' => $horario,
                '{telefone_cliente}' => $telefone,
                '{profissional}' => $profissional_nome
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
       # $agendamento = $horario . " " . $data_formatada;
        #$profissional = $profissional_nome;
        $agenda_confirma1 = novo_texto($agenda_confirma, $nome, $data, $horario,$telefone,$profissional_nome);

       # $msg = $agenda_confirma;

        // Inserir mensagem de confirmação na tabela de envio
        $sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$telefone', '$agenda_confirma1', '2', '$usuario_api')";
        $query = mysqli_query($conn, $sql);
        $id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção
        
        
        
        $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone, $agenda_confirma1, $id_msg);








        VaiPara('agendar.php?id=' . $idd . '&agenda=atualizado');
    } else {
        echo "Erro ao realizar o agendamento: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Dados insuficientes ou método inválido.";
}
?>
