<?php
session_start();
include 'funcoes.php';
include 'conn.php';
$login = $_SESSION['login'];

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    VaiPara('cadastrar_horario.php?erro=metodo_invalido');
    exit;
}

// Iniciar transação para garantir integridade dos dados
mysqli_begin_transaction($conn);

try {
    // 1. Buscar dados do usuário logado
    $sql_usuario = "SELECT usuario_api FROM login WHERE login = '$login'";
    $query_usuario = mysqli_query($conn, $sql_usuario);
    
    if (!$query_usuario || mysqli_num_rows($query_usuario) == 0) {
        throw new Exception("Usuário não encontrado");
    }
    
    $usuario_data = mysqli_fetch_array($query_usuario);
    $usuario_api = $usuario_data['usuario_api'];
    
    // 2. Capturar dados do profissional
    $profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_servico']);
    
    // Buscar dados do profissional
    $sql_profissional = "SELECT * FROM profissional WHERE id = '$profissional_id' AND login = '$login'";
    $query_profissional = mysqli_query($conn, $sql_profissional);
    
    if (!$query_profissional || mysqli_num_rows($query_profissional) == 0) {
        throw new Exception("Profissional não encontrado");
    }
    
    $profissional_data = mysqli_fetch_array($query_profissional);
    $profissional_nome = $profissional_data['profissional_nome'];
    $profissional_cargo = $profissional_data['profissional_cargo'];
    
    // 3. Limpar horários anteriores do profissional (opcional - você pode comentar se quiser manter)
    $sql_limpar_horarios = "DELETE FROM horarios_profissional WHERE profissional_id = '$profissional_id'";
    mysqli_query($conn, $sql_limpar_horarios);
    
    // 4. Processar dias ativos e seus horários
    $dias_ativos = isset($_POST['dias_ativos']) ? $_POST['dias_ativos'] : array();
    
    $dias_map = [
        'segunda' => 'Segunda-feira',
        'terca' => 'Terça-feira', 
        'quarta' => 'Quarta-feira',
        'quinta' => 'Quinta-feira',
        'sexta' => 'Sexta-feira',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo'
    ];
    
    foreach ($dias_ativos as $dia) {
        // Capturar horários do dia
        $entrada = isset($_POST['entrada_' . $dia]) ? $_POST['entrada_' . $dia] : null;
        $almoco_inicio = isset($_POST['almoco_inicio_' . $dia]) ? $_POST['almoco_inicio_' . $dia] : null;
        $almoco_fim = isset($_POST['almoco_fim_' . $dia]) ? $_POST['almoco_fim_' . $dia] : null;
        $saida = isset($_POST['saida_' . $dia]) ? $_POST['saida_' . $dia] : null;
        
        // Validar se pelo menos entrada e saída foram preenchidas
        if (empty($entrada) || empty($saida)) {
            throw new Exception("Horários de entrada e saída são obrigatórios para " . $dias_map[$dia]);
        }
        
        // Inserir horário principal
        $sql_horario = "INSERT INTO horarios_profissional 
                        (profissional_id, dia_semana, hora_entrada, almoco_inicio, almoco_fim, hora_saida, ativo) 
                        VALUES 
                        ('$profissional_id', '$dia', '$entrada', 
                         " . ($almoco_inicio ? "'$almoco_inicio'" : "NULL") . ", 
                         " . ($almoco_fim ? "'$almoco_fim'" : "NULL") . ", 
                         '$saida', 1)";
        
        if (!mysqli_query($conn, $sql_horario)) {
            throw new Exception("Erro ao inserir horário: " . mysqli_error($conn));
        }
        
        $horario_id = mysqli_insert_id($conn);
        
        // 5. Processar intervalos adicionais do dia
        $intervalos_inicio = isset($_POST['intervalo_inicio_' . $dia]) ? $_POST['intervalo_inicio_' . $dia] : array();
        $intervalos_fim = isset($_POST['intervalo_fim_' . $dia]) ? $_POST['intervalo_fim_' . $dia] : array();
        $intervalos_motivo = isset($_POST['intervalo_motivo_' . $dia]) ? $_POST['intervalo_motivo_' . $dia] : array();
        
        for ($i = 0; $i < count($intervalos_inicio); $i++) {
            if (!empty($intervalos_inicio[$i]) && !empty($intervalos_fim[$i])) {
                $intervalo_inicio = mysqli_real_escape_string($conn, $intervalos_inicio[$i]);
                $intervalo_fim = mysqli_real_escape_string($conn, $intervalos_fim[$i]);
                $motivo = mysqli_real_escape_string($conn, $intervalos_motivo[$i]);
                
                $sql_intervalo = "INSERT INTO intervalos_profissional 
                                  (horario_id, intervalo_inicio, intervalo_fim, motivo, login) 
                                  VALUES 
                                  ('$horario_id', '$intervalo_inicio', '$intervalo_fim', '$motivo', '$login')";
                
                if (!mysqli_query($conn, $sql_intervalo)) {
                    throw new Exception("Erro ao inserir intervalo: " . mysqli_error($conn));
                }
            }
        }
        
        // 6. Inserir na agenda_padrao os horários disponíveis para agendamento
        // Gerar slots de horários baseados na configuração
        $hora_atual = strtotime($entrada);
        $hora_fim = strtotime($saida);
        $intervalo_agendamento = 30; // 30 minutos por padrão - você pode tornar isso configurável
        
        while ($hora_atual < $hora_fim) {
            $horario_slot = date('H:i', $hora_atual);
            
            // Verificar se não está no horário de almoço
            $no_almoco = false;
            if ($almoco_inicio && $almoco_fim) {
                $hora_almoco_inicio = strtotime($almoco_inicio);
                $hora_almoco_fim = strtotime($almoco_fim);
                if ($hora_atual >= $hora_almoco_inicio && $hora_atual < $hora_almoco_fim) {
                    $no_almoco = true;
                }
            }
            
            // Verificar se não está em um intervalo adicional
            $no_intervalo = false;
            for ($i = 0; $i < count($intervalos_inicio); $i++) {
                if (!empty($intervalos_inicio[$i]) && !empty($intervalos_fim[$i])) {
                    $intervalo_inicio_time = strtotime($intervalos_inicio[$i]);
                    $intervalo_fim_time = strtotime($intervalos_fim[$i]);
                    if ($hora_atual >= $intervalo_inicio_time && $hora_atual < $intervalo_fim_time) {
                        $no_intervalo = true;
                        break;
                    }
                }
            }
            
            // Se não está em almoço ou intervalo, adicionar à agenda padrão
            if (!$no_almoco && !$no_intervalo) {
                $sql_agenda = "INSERT INTO agenda_padrao 
                               (usuario_api, login, dia, horario, profissional_nome, profissional_cargo, id_profissional) 
                               VALUES 
                               ('$usuario_api', '$login', '$dia', '$horario_slot', '$profissional_nome', '$profissional_cargo', '$profissional_id')";
                
                if (!mysqli_query($conn, $sql_agenda)) {
                    throw new Exception("Erro ao inserir na agenda padrão: " . mysqli_error($conn));
                }
            }
            
            $hora_atual = strtotime("+$intervalo_agendamento minutes", $hora_atual);
        }
    }
    
    // 7. Processar serviços associados ao profissional
    $servicos_ids = isset($_POST['servico_id']) ? $_POST['servico_id'] : array();
    $tempos_servico = isset($_POST['tempo_servico']) ? $_POST['tempo_servico'] : array();
    $valores_servico = isset($_POST['valor_servico']) ? $_POST['valor_servico'] : array();
    
    // Limpar serviços anteriores do profissional (opcional)
    $sql_limpar_servicos = "DELETE FROM profissional_servicos WHERE profissional_id = '$profissional_id'";
    mysqli_query($conn, $sql_limpar_servicos);
    
    for ($i = 0; $i < count($servicos_ids); $i++) {
        if (!empty($servicos_ids[$i])) {
            $servico_id = mysqli_real_escape_string($conn, $servicos_ids[$i]);
            $tempo = mysqli_real_escape_string($conn, $tempos_servico[$i]);
            $valor = mysqli_real_escape_string($conn, $valores_servico[$i]);
            
            $sql_prof_servico = "INSERT INTO profissional_servicos 
                                 (profissional_id, servico_id, tempo_execucao_minutos, valor_profissional, login, ativo) 
                                 VALUES 
                                 ('$profissional_id', '$servico_id', '$tempo', '$valor', '$login', 1)";
            
            if (!mysqli_query($conn, $sql_prof_servico)) {
                throw new Exception("Erro ao associar serviço: " . mysqli_error($conn));
            }
        }
    }
    
    // 8. Confirmar transação
    mysqli_commit($conn);
    
    // Registrar log de sucesso (opcional)
    $log_msg = "Horários e serviços cadastrados para o profissional $profissional_nome";
    error_log($log_msg);
    
    // Redirecionar com sucesso
    VaiPara('cadastrar_horario.php?confirmacao=servicos_cadastrados&profissional=' . $profissional_nome);
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    mysqli_rollback($conn);
    
    // Registrar erro
    error_log("Erro ao cadastrar horários/serviços: " . $e->getMessage());
    
    // Redirecionar com erro
    VaiPara('cadastrar_horario.php?erro=' . urlencode($e->getMessage()));
}

// Fechar conexão
mysqli_close($conn);
?>