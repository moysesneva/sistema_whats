<?php
session_start(); // Necessary for success/error flash messages

#ini_set('display_errors', 1); // For development
#error_reporting(E_ALL);     // For development

// Define the client ID for redirection, getting from POST and ensuring it's not null.
// Used in several places to redirect back to the scheduling page.
// It comes from the 'idd' hidden field in agendar.php form
$idd_cliente_param_redirect = $_POST['idd'] ?? '';

$basePath = 'login/painel/';
if (!file_exists($basePath . 'conn.php')) {
    $_SESSION['agendamento_status'] = "Erro crítico: Arquivo de conexão não encontrado.";
    header("Location: agendar.php?id=" . urlencode($idd_cliente_param_redirect));
    exit;
}
include $basePath . 'conn.php';

include 'login/painel/api/editacodigo.php';

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = $rows_config['ip_vps'];
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}

if (!isset($conn) || $conn->connect_error) {
    $_SESSION['agendamento_status'] = "Erro: Falha na conexão com o banco: " . ($conn->connect_error ?? 'Erro desconhecido');
    header("Location: agendar.php?id=" . urlencode($idd_cliente_param_redirect));
    exit;
}

// --- 1. Collect and Initially Validate POST Data ---
$profissional_id = isset($_POST['profissional']) ? (int)$_POST['profissional'] : null; // From <select name="profissional">
$data_agendamento_str = $_POST['data'] ?? null; // From <input name="data"> (YYYY-MM-DD)
$servico_id_atual = isset($_POST['servico_id']) ? (int)$_POST['servico_id'] : null; // From <input name="servico_id">
$horario_selecionado_str = $_POST['horario'] ?? null; // From <input name="horario"> (HH:MM)
$duracao_servico_atual_min = isset($_POST['duracao_servico']) ? (int)$_POST['duracao_servico'] : 0;
$valor_servico_atual = isset($_POST['valor_servico']) ? (float)$_POST['valor_servico'] : 0.0;

$usuario_api = $_POST['usuario_api'] ?? null; // API of the account/company
$idd_cliente_ref = $_POST['idd'] ?? null; // The original ID from the URL (e.g., iNVWZ) - this is id_agendamento in 'clientes' table

// Fetch client name and phone from 'clientes' table using idd_cliente_ref
$cliente_nome_db = 'Cliente'; // Default
$cliente_telefone_db = null;







if($servico_id_atual){
    
$sql_busca_modulos = "SELECT * FROM servicos WHERE id = '$servico_id_atual'";
$query = mysqli_query($conn, $sql_busca_modulos);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $servico_nome = $rows_usuarios['nome'];
}   
      
    
    
}












if ($idd_cliente_ref) {
    $stmt_cliente = $conn->prepare("SELECT nome, telefone FROM clientes WHERE id_agendamento = ? AND usuario_api = ?");
    if ($stmt_cliente) {
        $stmt_cliente->bind_param("ss", $idd_cliente_ref, $usuario_api);
        $stmt_cliente->execute();
        $result_cliente = $stmt_cliente->get_result();
        if ($cliente_data = $result_cliente->fetch_assoc()) {
            $cliente_nome_db = $cliente_data['nome'] ?: 'Cliente';
            $cliente_telefone_db = $cliente_data['telefone'];
        } else {
            // Optionally handle case where client ref is not found for the given usuario_api
            // For now, we proceed with defaults but this could be an error condition
             $_SESSION['agendamento_status'] = "Erro: Referência de cliente não encontrada para o usuário API fornecido.";
             // header("Location: agendar.php?id=" . urlencode($idd_cliente_param_redirect)); // Or a more general error page
             // exit;
        }
        $stmt_cliente->close();
    } else {
        $_SESSION['agendamento_status'] = "Erro DB (prepare client fetch): " . $conn->error;
        header("Location: agendar.php?id=" . urlencode($idd_cliente_param_redirect));
        exit;
    }
}

// Validate essential data
if (!$profissional_id || !$data_agendamento_str || !$servico_id_atual || !$horario_selecionado_str || $duracao_servico_atual_min <= 0 || !$usuario_api || !$idd_cliente_ref || !$cliente_telefone_db) {
    $missing_fields_log = "Profissional: " . ($profissional_id ? 'OK' : 'FALTA') .
                         ", Data: " . ($data_agendamento_str ? 'OK' : 'FALTA') .
                         ", Serviço: " . ($servico_id_atual ? 'OK' : 'FALTA') .
                         ", Horário: " . ($horario_selecionado_str ? 'OK' : 'FALTA') .
                         ", Duração: " . ($duracao_servico_atual_min > 0 ? 'OK' : 'INVÁLIDA') .
                         ", API User: " . ($usuario_api ? 'OK' : 'FALTA') .
                         ", ID Cliente Ref: " . ($idd_cliente_ref ? 'OK' : 'FALTA') .
                         ", Tel Cliente: " . ($cliente_telefone_db ? 'OK' : 'FALTA');

    $_SESSION['agendamento_status'] = "Erro: Dados do agendamento incompletos ou inválidos. Verifique todos os campos. Detalhes: " . $missing_fields_log;
    // error_log("Agendamento falhou - dados incompletos: " . print_r($_POST, true) . " | Cliente Nome: $cliente_nome_db, Cliente Tel: $cliente_telefone_db");
VaiPara("agendar.php?id=" . urlencode($idd_cliente_ref));
    exit;
}

// --- 2. Final Availability Check ---
$slot_disponivel_final = false;
$horario_profissional_id_db = null;
    include 'login/painel/funcoes.php';

try {
    $data_obj = new DateTime($data_agendamento_str);
    
    // CORREÇÃO: Usar método mais confiável para determinar o dia da semana
    // Mapear diretamente do índice do dia para o formato do banco (sem acentos)
    $dayOfWeekIndex = (int)$data_obj->format('w'); // 0 = domingo, 1 = segunda, etc.
    $dias_semana_banco = [
        0 => 'domingo',
        1 => 'segunda', 
        2 => 'terca',    // SEM ACENTO - conforme tabela do banco
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado'    // SEM ACENTO - conforme tabela do banco
    ];
    
    $dia_semana_php = $dias_semana_banco[$dayOfWeekIndex];
    
    // Fallback caso o índice seja inválido
    if (empty($dia_semana_php)) {
        throw new Exception("Não foi possível determinar o dia da semana para a data: $data_agendamento_str");
    }

    // Get professional's working hours
    $stmt_horario = $conn->prepare("SELECT id, hora_entrada, hora_saida, almoco_inicio, almoco_fim FROM horarios_profissional WHERE profissional_id = ? AND dia_semana = ? AND ativo = 1");
    if (!$stmt_horario) throw new Exception("Erro ao preparar consulta de horário profissional: " . $conn->error);
    $stmt_horario->bind_param("is", $profissional_id, $dia_semana_php);
    $stmt_horario->execute();
    $result_horario_prof = $stmt_horario->get_result();
    if ($result_horario_prof->num_rows == 0) throw new Exception("Horário do profissional não encontrado ou inativo para o dia selecionado ($dia_semana_php).");
    $horario_prof = $result_horario_prof->fetch_assoc();
    $horario_profissional_id_db = (int)$horario_prof['id'];
    $stmt_horario->close();

    $dt_entrada_prof = new DateTime($data_agendamento_str . ' ' . $horario_prof['hora_entrada']);
    $dt_saida_prof = new DateTime($data_agendamento_str . ' ' . $horario_prof['hora_saida']);
    $dt_almoco_inicio = ($horario_prof['almoco_inicio'] && $horario_prof['almoco_inicio'] !== '00:00:00') ? new DateTime($data_agendamento_str . ' ' . $horario_prof['almoco_inicio']) : null;
    $dt_almoco_fim = ($horario_prof['almoco_fim'] && $horario_prof['almoco_fim'] !== '00:00:00') ? new DateTime($data_agendamento_str . ' ' . $horario_prof['almoco_fim']) : null;

    // Get additional intervals
    $intervalos_adicionais = [];
    if ($horario_profissional_id_db) {
        $stmt_intervalos = $conn->prepare("SELECT intervalo_inicio, intervalo_fim FROM intervalos_profissional WHERE horario_id = ?");
        if (!$stmt_intervalos) throw new Exception("Erro ao preparar consulta de intervalos: " . $conn->error);
        $stmt_intervalos->bind_param("i", $horario_profissional_id_db);
        $stmt_intervalos->execute();
        $result_intervalos = $stmt_intervalos->get_result();
        while ($intervalo = $result_intervalos->fetch_assoc()) {
            $intervalos_adicionais[] = [
                'inicio' => new DateTime($data_agendamento_str . ' ' . $intervalo['intervalo_inicio']),
                'fim' => new DateTime($data_agendamento_str . ' ' . $intervalo['intervalo_fim'])
            ];
        }
        $stmt_intervalos->close();
    }

    // Get existing appointments
    $agendamentos_existentes = [];
    // The 'agendamento' table in SQL dump doesn't have servico_id or duracao_minutos.
    // This query assumes they have been added as per the original script's logic.
    $stmt_agendamentos = $conn->prepare(
        "SELECT a.horario AS horario_inicio_agendado, a.duracao_minutos AS duracao_agendado_minutos 
         FROM agendamento a 
         WHERE a.id_profissional = ? AND a.data = ? AND (a.confirmacao IS NULL OR a.confirmacao != 2)" // Excludes canceled
    );
    if (!$stmt_agendamentos) throw new Exception("Erro ao preparar consulta de agendamentos existentes: " . $conn->error);
    $stmt_agendamentos->bind_param("is", $profissional_id, $data_agendamento_str);
    $stmt_agendamentos->execute();
    $result_agendamentos = $stmt_agendamentos->get_result();
    while ($ag = $result_agendamentos->fetch_assoc()) {
        $dt_inicio_ag = new DateTime($data_agendamento_str . ' ' . $ag['horario_inicio_agendado']);
        $duracao_ag_min = (int)$ag['duracao_agendado_minutos'];
        if ($duracao_ag_min > 0) {
            $dt_fim_ag = (clone $dt_inicio_ag)->add(new DateInterval('PT' . $duracao_ag_min . 'M'));
            $agendamentos_existentes[] = ['inicio' => $dt_inicio_ag, 'fim' => $dt_fim_ag];
        }
    }
    $stmt_agendamentos->close();

    // Check the specific selected slot
    $slot_inicio_selecionado = new DateTime($data_agendamento_str . ' ' . $horario_selecionado_str);
    $slot_fim_selecionado = (clone $slot_inicio_selecionado)->add(new DateInterval('PT' . $duracao_servico_atual_min . 'M'));
    
    $slot_disponivel_final = true; // Assume available initially

    // Check if within working hours
    if ($slot_inicio_selecionado < $dt_entrada_prof || $slot_fim_selecionado > $dt_saida_prof) $slot_disponivel_final = false;
    
    // Check conflict with lunch
    if ($slot_disponivel_final && $dt_almoco_inicio && $dt_almoco_fim && ($slot_inicio_selecionado < $dt_almoco_fim && $slot_fim_selecionado > $dt_almoco_inicio)) $slot_disponivel_final = false;
    
    // Check conflict with additional intervals
    if ($slot_disponivel_final) {
        foreach ($intervalos_adicionais as $intervalo_add) {
            if ($slot_inicio_selecionado < $intervalo_add['fim'] && $slot_fim_selecionado > $intervalo_add['inicio']) {
                $slot_disponivel_final = false; break;
            }
        }
    }
    // Check conflict with existing appointments
    if ($slot_disponivel_final) {
        foreach ($agendamentos_existentes as $ag_existente) {
            if ($slot_inicio_selecionado < $ag_existente['fim'] && $slot_fim_selecionado > $ag_existente['inicio']) {
                $slot_disponivel_final = false; break;
            }
        }
    }

} catch (Exception $e) {
    $_SESSION['agendamento_status'] = "Erro ao verificar disponibilidade: " . htmlspecialchars($e->getMessage());
VaiPara("agendar.php?id=" . urlencode($idd_cliente_ref));
    exit;
}

// --- 3. Insert Appointment if Available ---
if ($slot_disponivel_final) {
    // Get professional name and role to insert into agendamento table
    $stmt_prof_details = $conn->prepare("SELECT profissional_nome, profissional_cargo FROM profissional WHERE id = ?");
    if (!$stmt_prof_details) {
        $_SESSION['agendamento_status'] = "Erro DB (detalhes prof): " . $conn->error;
        header("Location: agendar.php?id=" . urlencode($idd_cliente_ref));
        exit;
    }
    $stmt_prof_details->bind_param("i", $profissional_id);
    $stmt_prof_details->execute();
    $result_prof_details = $stmt_prof_details->get_result();
    if (!($prof_details = $result_prof_details->fetch_assoc())) {
        $_SESSION['agendamento_status'] = "Erro: Profissional não encontrado.";
        header("Location: agendar.php?id=" . urlencode($idd_cliente_ref));
        exit;
    }
    $stmt_prof_details->close();

    $nome_profissional_db = $prof_details['profissional_nome'];
    $cargo_profissional_db = $prof_details['profissional_cargo'];

    $login_agendamento = $usuario_api; 
    if (strpos($usuario_api, 'agenda_') === 0) {
        $login_agendamento = substr($usuario_api, strlen('agenda_'));
    }

    // Get day of week string for DB (e.g., "Segunda-feira")
    // Ensure this matches the format used elsewhere if consistency is needed for 'dia' column
    $dia_semana_para_db_obj = new DateTime($data_agendamento_str);
    $dayOfWeekIndex_para_db = (int)$dia_semana_para_db_obj->format('w');
    $days_map_pt = [
        0 => 'Domingo', 
        1 => 'Segunda-feira', 
        2 => 'Terça-feira',    // COM ACENTO para a coluna 'dia' (display)
        3 => 'Quarta-feira',
        4 => 'Quinta-feira', 
        5 => 'Sexta-feira', 
        6 => 'Sábado'
    ];
    $dia_semana_para_db = $days_map_pt[$dayOfWeekIndex_para_db] ?? 'Dia inválido';

    $confirmacao_inicial = 0; // 0 for Pending
    $lembrete_inicial = 0;    // 0 for Not Sent

    $sql_insert = "INSERT INTO agendamento (
                       usuario_api, login, dia, horario, profissional_nome, 
                       profissional_cargo, cliente_telefone, cliente_nome, data, id_profissional, 
                       confirmacao, lembrete, servico_id, duracao_minutos, valor_servico, 
                       id_cliente_ref
                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) {
        $_SESSION['agendamento_status'] = "Erro DB (prepare insert): " . $conn->error;
        header("Location: agendar.php?id=" . urlencode($idd_cliente_ref));
        exit;
    }

    // Type definition string: 9s, 5i, 1d, 1s = 16 parameters
    $stmt_insert->bind_param("sssssssssiiiiids",
        $usuario_api,
        $login_agendamento,
        $dia_semana_para_db, // Day of the week string
        $horario_selecionado_str,
        $nome_profissional_db,
        $cargo_profissional_db,
        $cliente_telefone_db, // Fetched from 'clientes' table
        $cliente_nome_db,     // Fetched from 'clientes' table
        $data_agendamento_str,
        $profissional_id,          // int
        $confirmacao_inicial,      // int
        $lembrete_inicial,         // int
        $servico_id_atual,         // int
        $duracao_servico_atual_min,// int
        $valor_servico_atual,      // double
        $idd_cliente_ref           // string (this is the 'id_agendamento' from 'clientes' table)
    );

    if ($stmt_insert->execute()) {
        $novo_agendamento_id = $stmt_insert->insert_id;

        $_SESSION['agendamento_status'] = "Agendamento realizado com sucesso! Seu código de agendamento é: " . $novo_agendamento_id;
     
     $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
        $login = $row_usuario['login'];
        $agenda_confirma = $row_usuario['agenda_confirma'];
        $confirma_prof = $row_usuario['confirma_prof'];
         $numero_bot = $row_usuario['numero_bot'];
         $google_cal = $row_usuario['google_cal'];
         
    }

function isMobile() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    $mobileAgents = [
        'iphone', 'ipad', 'android', 'blackberry', 'nokia', 'opera mini',
        'windows phone', 'windows mobile', 'iemobile', 'mobile', 'kindle',
        'silk', 'fennec', 'webos', 'palm', 'symbian'
    ];

    foreach ($mobileAgents as $device) {
        if (strpos($userAgent, $device) !== false) {
            return true; // É um dispositivo móvel
        }
    }

    return false; // É um PC ou desktop
}    
    




    

    













function dataParaBR($data) {
    if (empty($data)) {
        return '';
    }

    $partes = explode('-', $data);
    if (count($partes) === 3) {
        return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
    } else {
        return $data; // Retorna a original se não estiver no formato esperado
    }
}
    
    // Inserir mensagem de confirmação na tabela de envio
    $sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$cliente_telefone_db', '$agenda_confirma', '2', '$usuario_api')";
    $query = mysqli_query($conn, $sql);
    $id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção    
  
    $data_agendamento_str2 = $data_agendamento_str;
    $data_agendamento_str = dataParaBR($data_agendamento_str2);
    
    
    
    // Funções para formatar texto e data
function novo_texto($template, $cliente_nome, $data_agendamento, $hora_agendamento, $telefone_cliente, $nome_servico, $valor_servico_atual, $nome_profissional,$link_agendamento) {
    $substituicoes = [
        '{nome}'            => $cliente_nome,
        '{data_agendamento}' => $data_agendamento,
        '{hora_agendamento}' => $hora_agendamento,
        '{telefone_cliente}' => $telefone_cliente,
        '{servico}'          => $nome_servico,
        '{serviço}'          => $nome_servico,
        '{preco_servico}'    => $valor_servico_atual,
        '{preço_serviço}'    => $valor_servico_atual,
        '{link_agendamento}'=> $link_agendamento,
        '{profissional}'     => $nome_profissional
    ];
    
    return str_replace(array_keys($substituicoes), array_values($substituicoes), $template);
}















function letras($string, $quantidade_letras = 4) {
    // Gera letras aleatórias (minúsculas)
    $letras = '';
    for ($i = 0; $i < $quantidade_letras; $i++) {
        $letras .= chr(rand(97, 122)); // de 'a' a 'z'
    }

    // Insere cada letra em uma posição aleatória da string original
    for ($i = 0; $i < strlen($letras); $i++) {
        $pos = rand(0, strlen($string));
        $string = substr($string, 0, $pos) . $letras[$i] . substr($string, $pos);
    }

    return $string;
}



$novo_agendamento_f = letras($novo_agendamento_id);
$link_agendamento =     $webhook.'/link.php?id='.$novo_agendamento_f;


$link_agendamento = preg_replace('#(?<!:)/{2,}#', '/', $link_agendamento);
  
  
  
  
  
  
////busca por dados od profissional

  
  
$sql_busca_modulos = "SELECT * FROM  profissional WHERE id = '$profissional_id'";
$query = mysqli_query($conn, $sql_busca_modulos);

while($rows_usuarios = mysqli_fetch_array($query)) {
    $telefone_profissional = $rows_usuarios['telefone'];
}   
        
   
   
   
  $sql_busca_usuario = "SELECT * FROM login WHERE login = '$telefone_profissional'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
 
         $google_cal = $row_usuario['google_cal'];
         
    }
  
   
   
   
   
   
   
   
   
   
   
   
    
    
    
     $confirma_prof = novo_texto($confirma_prof, $cliente_nome_db, $data_agendamento_str, $horario_selecionado_str,$cliente_telefone_db, $servico_nome, $valor_servico_atual, $nome_profissional_db,$link_agendamento); 
    

    $agenda_confirma =  novo_texto($agenda_confirma, $cliente_nome_db, $data_agendamento_str, $horario_selecionado_str,$cliente_telefone_db, $servico_nome, $valor_servico_atual, $nome_profissional_db,$link_agendamento); 
       
    $msg = $agenda_confirma;
    $response = enviarMensagem($servidor,$porta , $usuario_api, $token, $cliente_telefone_db, $msg, $id_msg);
    
    
     $response_prof = enviarMensagem($servidor,$porta , $usuario_api, $token, $telefone_profissional, $confirma_prof, $id_msg);

// Mensagem descritiva
$msg_agenda = "Atendimento agendado com o cliente $cliente_nome_db.\n"
             . "Serviço: $servico_nome.\n"
             . "Data: $data_agendamento_str2.\n"
             . "Horário: $horario_selecionado_str.\n"
             . "Duração: $duracao_servico_atual_min minutos.";

// Função para completar hora no formato hh:mm:00
function horaParaCompleta($hora) {
    list($h, $m) = explode(':', $hora);
    return sprintf('%02d:%02d:00', $h, $m);
}

// Função para somar minutos ao horário inicial
function somaMinutos($hora, $minutos) {
    list($h, $m) = explode(':', $hora);
    $total_minutos = ($h * 60) + $m + $minutos;

    $nova_hora = floor($total_minutos / 60) % 24;
    $novo_minuto = $total_minutos % 60;

    return sprintf('%02d:%02d', $nova_hora, $novo_minuto);
}

// Ajusta horários
$horario_fim = somaMinutos($horario_selecionado_str, $duracao_servico_atual_min);
$horario_selecionado_str = horaParaCompleta($horario_selecionado_str);
$horario_fim = horaParaCompleta($horario_fim);






// Credenciais Google
$client_id     = '106056944943-8stg3lo5cllnce5co6pu8lvo8p63oasq.apps.googleusercontent.com';
$client_secret = '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';
$refresh_token = '1//0hugkPMmmaBt9CgYIARAAGBESNwF-L9IrjJgvFVzfZ3SREBVpvCAbHoD24aoc-2SXflEdD3XONBSecb9wxcsxya_xhbSwI0RICH4';


if($google_cal){
    
    
// 1) Renova o access_token
$tokenUrl = "https://oauth2.googleapis.com/token";
$postFields = http_build_query([
    'client_id'     => $client_id,
    'client_secret' => $client_secret,
    'refresh_token' => $refresh_token,
    'grant_type'    => 'refresh_token'
]);

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$tokenResponse = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
if (empty($tokenData['access_token'])) {
    exit("❌ Erro ao obter access_token:\n" . $tokenResponse);
}
$accessToken = $tokenData['access_token'];

// 2) Datas e horários
$eventDate = $data_agendamento_str2;
$startTime = $horario_selecionado_str;
$endTime   = $horario_fim;
$timeZone  = 'America/Sao_Paulo';

$startDateTime = "{$eventDate}T{$startTime}-03:00";
$endDateTime   = "{$eventDate}T{$endTime}-03:00";

// 3) Payload do evento completo com convite e notificações
$evento = [
    "summary"     => "Atendimento: $servico_nome", // Título do evento
    "location"    => "-------",           // Pode mudar se quiser
    "description" => $msg,
    "start"       => [
        "dateTime" => $startDateTime,
        "timeZone" => $timeZone
    ],
    "end"         => [
        "dateTime" => $endDateTime,
        "timeZone" => $timeZone
    ],
    "attendees"   => [
        ["email" => $google_cal]
    ],
    "reminders" => [
        "useDefault" => false,
        "overrides"  => [
            ["method" => "email", "minutes" => 60], // E-mail 1h antes
            ["method" => "popup", "minutes" => 15]  // Popup 15 min antes
        ]
    ]
];

// 4) Envia o evento
$ch = curl_init("https://www.googleapis.com/calendar/v3/calendars/primary/events");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($evento));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$accessToken}",
    "Content-Type: application/json"
]);
$response   = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5) Retorna a resposta da API
header('Content-Type: application/json', true, $statusCode);
#echo $response;


}//$google_cal

#echo $response;


    if($response){    
        if (isMobile()) {
            #echo "Acesso via dispositivo móvel.";
if($numero_bot){
$url = 'whatsapp://send?phone=' . $numero_bot. '&text=';
}
if(!$numero_bot){
$url = 'whatsapp://send?phone=' . $login .'&text=';
}
    
    // Faz o redirecionamento
VaiPara("$url");


            exit;
        } else {
            VaiPara("agendar.php?id=" . urlencode($idd_cliente_ref) . "&status=sucesso&ag_id=" . urlencode($novo_agendamento_id));
            exit;
        }    
    }        
      
    } else {
        $_SESSION['agendamento_status'] = "Erro ao salvar o agendamento: " . htmlspecialchars($stmt_insert->error);
        // error_log("Erro INSERT agendamento: " . $stmt_insert->error . " | POST: " . print_r($_POST, true) . " | Cliente Nome DB: $cliente_nome_db, Cliente Tel DB: $cliente_telefone_db");
        header("Location: agendar.php?id=" . urlencode($idd_cliente_ref));
        exit;
    }
    $stmt_insert->close();

} else {
    $_SESSION['agendamento_status'] = "Desculpe, o horário selecionado ($horario_selecionado_str em $data_agendamento_str) não está mais disponível ou é inválido. Por favor, tente outro.";
VaiPara("agendar.php?id=" . urlencode($idd_cliente_ref));
    exit;
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>