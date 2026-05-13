<?php
session_start();
include 'funcoes.php';
include 'conn.php';

// --- FASE 0: CONFIGURAÇÃO INICIAL E URL DE REDIRECIONAMENTO ---
// Define a página para onde o usuário será enviado após o processo.
// Use a página do admin que criamos anteriormente.
$pagina_redirecionamento = "agendar_servico.php"; 

// Verifica se a conexão com o banco de dados foi bem-sucedida.
if (!isset($conn) || $conn->connect_error) {
    // Codifica a mensagem de erro para ser segura na URL.
    $msg = urlencode("Falha crítica na conexão com o banco de dados.");
    VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
    exit;
}


// --- FASE 1: COLETA E VALIDAÇÃO DOS DADOS DO FORMULÁRIO ---
$profissional_id = filter_input(INPUT_POST, 'profissional', FILTER_VALIDATE_INT);
$servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
$duracao_servico = filter_input(INPUT_POST, 'duracao_servico', FILTER_VALIDATE_INT);
$valor_servico = filter_input(INPUT_POST, 'valor_servico', FILTER_VALIDATE_FLOAT);
$data_str = $_POST['data'] ?? null;
$horario_str = $_POST['horario'] ?? null;
$usuario_api = $_POST['usuario_api'] ?? null;
$id_cliente_ref = $_POST['idd'] ?? null; // ID de referência do cliente (ex: iNVWZ)

// Validação básica: verifica se algum campo essencial está faltando.
if (!$profissional_id || !$servico_id || !$data_str || !$horario_str || !$usuario_api || !$id_cliente_ref) {
    $msg = urlencode("Dados essenciais do agendamento não foram recebidos. Tente novamente.");
    VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
    exit;
}

// --- FASE 2: VERIFICAÇÃO DE INTEGRIDADE DOS DADOS NO BANCO ---
try {
    // 2.1 - Busca e valida os dados do Profissional
    $stmt = $conn->prepare("SELECT profissional_nome, profissional_cargo FROM profissional WHERE id = ? AND usuario_api = ?");
    $stmt->bind_param("is", $profissional_id, $usuario_api);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Profissional selecionado é inválido ou não pertence a esta conta.");
    }
    $profissional_db = $result->fetch_assoc();
    $stmt->close();

    // 2.2 - Busca e valida os dados do Cliente
    $stmt = $conn->prepare("SELECT nome, telefone FROM clientes WHERE id_agendamento = ? AND usuario_api = ?");
    $stmt->bind_param("ss", $id_cliente_ref, $usuario_api);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Os dados do cliente não foram encontrados. Tente buscar o cliente novamente.");
    }
    $cliente_db = $result->fetch_assoc();
    $stmt->close();

} catch (Exception $e) {
    $msg = urlencode("Erro de validação: " . $e->getMessage());
    VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
    exit;
}


// --- FASE 3: VERIFICAÇÃO DE DISPONIBILIDADE DO HORÁRIO ---
$slot_disponivel = false;
try {
    // Converte a data e hora para objetos DateTime para facilitar a comparação
    $data_obj = new DateTime($data_str);
    $slot_inicio_dt = new DateTime("$data_str $horario_str");
    $slot_fim_dt = (clone $slot_inicio_dt)->add(new DateInterval("PT{$duracao_servico}M"));

    // Determina o dia da semana em português (ex: 'segunda', 'terca')
    $dias_semana_map = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
    $dia_semana = $dias_semana_map[(int)$data_obj->format('w')];

    // 3.1 - Busca o horário de trabalho do profissional para aquele dia
    $stmt = $conn->prepare("SELECT hora_entrada, hora_saida, almoco_inicio, almoco_fim FROM horarios_profissional WHERE profissional_id = ? AND dia_semana = ? AND ativo = 1");
    $stmt->bind_param("is", $profissional_id, $dia_semana);
    $stmt->execute();
    $result_horario = $stmt->get_result();
    if ($result_horario->num_rows === 0) {
        throw new Exception("O profissional não trabalha no dia selecionado.");
    }
    $horario_trabalho = $result_horario->fetch_assoc();
    $stmt->close();

    // Cria os horários de trabalho como objetos DateTime
    $entrada_dt = new DateTime("$data_str " . $horario_trabalho['hora_entrada']);
    $saida_dt = new DateTime("$data_str " . $horario_trabalho['hora_saida']);

    // Verifica se o horário desejado está dentro do expediente
    if ($slot_inicio_dt < $entrada_dt || $slot_fim_dt > $saida_dt) {
        throw new Exception("O horário solicitado está fora do expediente do profissional.");
    }

    // 3.2 - Cria uma lista de todos os períodos bloqueados (almoço, pausas, outros agendamentos)
    $bloqueios = [];

    // Adiciona o horário de almoço à lista de bloqueios
    if ($horario_trabalho['almoco_inicio'] && $horario_trabalho['almoco_fim']) {
        $bloqueios[] = [
            'inicio' => new DateTime("$data_str " . $horario_trabalho['almoco_inicio']),
            'fim' => new DateTime("$data_str " . $horario_trabalho['almoco_fim'])
        ];
    }

    // Adiciona outros agendamentos já existentes à lista de bloqueios
    $stmt = $conn->prepare("SELECT horario, duracao_minutos FROM agendamento WHERE id_profissional = ? AND data = ? AND confirmacao != 2");
    $stmt->bind_param("is", $profissional_id, $data_str);
    $stmt->execute();
    $result_agendamentos = $stmt->get_result();
    while ($ag = $result_agendamentos->fetch_assoc()) {
        $inicio_ag = new DateTime("$data_str " . $ag['horario']);
        $bloqueios[] = [
            'inicio' => $inicio_ag,
            'fim' => (clone $inicio_ag)->add(new DateInterval("PT{$ag['duracao_minutos']}M"))
        ];
    }
    $stmt->close();
    
    // 3.3 - Verifica se o slot desejado conflita com algum bloqueio
    $slot_disponivel = true;
    foreach ($bloqueios as $bloqueio) {
        // Lógica de intersecção: um conflito existe se (InicioA < FimB) e (FimA > InicioB)
        if ($slot_inicio_dt < $bloqueio['fim'] && $slot_fim_dt > $bloqueio['inicio']) {
            $slot_disponivel = false; // Encontrou um conflito!
            break; // Para a verificação
        }
    }

} catch (Exception $e) {
    $msg = urlencode("Erro ao verificar disponibilidade: " . $e->getMessage());
    VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
    exit;
}


// --- FASE 4: INSERÇÃO NO BANCO DE DADOS ---
if ($slot_disponivel) {
    
    // Prepara os dados finais para o INSERT
    $login_agendamento = str_replace('agenda_', '', $usuario_api);
    setlocale(LC_TIME, 'pt_BR.utf-8', 'portuguese');
    $dia_semana_db = ucfirst(strftime('%A', $data_obj->getTimestamp()));

    $sql_insert = "INSERT INTO agendamento (
                    usuario_api, login, dia, horario, profissional_nome, 
                    profissional_cargo, cliente_telefone, cliente_nome, data, id_profissional, 
                    confirmacao, lembrete, servico_id, duracao_minutos, valor_servico, 
                    id_cliente_ref
                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sssssssssiiids",
        $usuario_api,
        $login_agendamento,
        $dia_semana_db,
        $horario_str,
        $profissional_db['profissional_nome'],
        $profissional_db['profissional_cargo'],
        $cliente_db['telefone'],
        $cliente_db['nome'],
        $data_str,
        $profissional_id,
        $servico_id,
        $duracao_servico,
        $valor_servico,
        $id_cliente_ref
    );

    if ($stmt->execute()) {
        $novo_id = $stmt->insert_id;
        $msg = urlencode("Agendamento para {$cliente_db['nome']} realizado com sucesso! ID: {$novo_id}");
        VaiPara($pagina_redirecionamento . "?status=sucesso&msg=" . $msg);
    } else {
        $msg = urlencode("Erro do banco de dados ao salvar: " . $stmt->error);
        VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
    }
    $stmt->close();

} else {
    // Se, após todas as verificações, o slot não estiver disponível
    $msg = urlencode("O horário das {$horario_str} não está mais disponível. Por favor, escolha outro.");
    VaiPara($pagina_redirecionamento . "?status=erro&msg=" . $msg);
}

$conn->close();
exit;
?>