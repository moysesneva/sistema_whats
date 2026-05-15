<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';
include 'conn.php';
$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    VaiPara('cadastrar_horario.php?erro=metodo_invalido');
    exit;
}

mysqli_begin_transaction($conn);

try {
    // 1. Buscar dados do usuário logado
    $stmt_usuario = mysqli_prepare($conn, "SELECT usuario_api FROM login WHERE login = ?");
    mysqli_stmt_bind_param($stmt_usuario, "s", $login);
    mysqli_stmt_execute($stmt_usuario);
    $result_usuario = mysqli_stmt_get_result($stmt_usuario);

    if (!$result_usuario || mysqli_num_rows($result_usuario) == 0) {
        throw new Exception("Usuário não encontrado");
    }

    $usuario_data = mysqli_fetch_array($result_usuario);
    $usuario_api = $usuario_data['usuario_api'];

    // 2. Capturar dados do profissional
    $profissional_id = intval($_POST['profissional_servico']);

    $stmt_profissional = mysqli_prepare($conn,
        "SELECT * FROM profissional WHERE id = ? AND login = ?");
    mysqli_stmt_bind_param($stmt_profissional, "is", $profissional_id, $login);
    mysqli_stmt_execute($stmt_profissional);
    $result_profissional = mysqli_stmt_get_result($stmt_profissional);

    if (!$result_profissional || mysqli_num_rows($result_profissional) == 0) {
        throw new Exception("Profissional não encontrado");
    }

    $profissional_data = mysqli_fetch_array($result_profissional);
    $profissional_nome = $profissional_data['profissional_nome'];
    $profissional_cargo = $profissional_data['profissional_cargo'];

    // 3. Limpar horários anteriores do profissional
    $stmt_limpar = mysqli_prepare($conn,
        "DELETE FROM horarios_profissional WHERE profissional_id = ?");
    mysqli_stmt_bind_param($stmt_limpar, "i", $profissional_id);
    mysqli_stmt_execute($stmt_limpar);

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

    $stmt_horario = mysqli_prepare($conn,
        "INSERT INTO horarios_profissional
         (profissional_id, dia_semana, hora_entrada, almoco_inicio, almoco_fim, hora_saida, ativo)
         VALUES (?, ?, ?, ?, ?, ?, 1)");

    $stmt_intervalo = mysqli_prepare($conn,
        "INSERT INTO intervalos_profissional
         (horario_id, intervalo_inicio, intervalo_fim, motivo, login)
         VALUES (?, ?, ?, ?, ?)");

    $stmt_agenda = mysqli_prepare($conn,
        "INSERT INTO agenda_padrao
         (usuario_api, login, dia, horario, profissional_nome, profissional_cargo, id_profissional)
         VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($dias_ativos as $dia) {
        $entrada = isset($_POST['entrada_' . $dia]) ? $_POST['entrada_' . $dia] : null;
        $almoco_inicio = isset($_POST['almoco_inicio_' . $dia]) ? $_POST['almoco_inicio_' . $dia] : null;
        $almoco_fim = isset($_POST['almoco_fim_' . $dia]) ? $_POST['almoco_fim_' . $dia] : null;
        $saida = isset($_POST['saida_' . $dia]) ? $_POST['saida_' . $dia] : null;

        if (empty($entrada) || empty($saida)) {
            throw new Exception("Horários de entrada e saída são obrigatórios para " . ($dias_map[$dia] ?? $dia));
        }

        // Inserir horário principal
        mysqli_stmt_bind_param($stmt_horario, "isssss",
            $profissional_id, $dia, $entrada, $almoco_inicio, $almoco_fim, $saida);

        if (!mysqli_stmt_execute($stmt_horario)) {
            throw new Exception("Erro ao inserir horário: " . mysqli_error($conn));
        }

        $horario_id = mysqli_insert_id($conn);

        // 5. Processar intervalos adicionais do dia
        $intervalos_inicio = isset($_POST['intervalo_inicio_' . $dia]) ? $_POST['intervalo_inicio_' . $dia] : array();
        $intervalos_fim = isset($_POST['intervalo_fim_' . $dia]) ? $_POST['intervalo_fim_' . $dia] : array();
        $intervalos_motivo = isset($_POST['intervalo_motivo_' . $dia]) ? $_POST['intervalo_motivo_' . $dia] : array();

        for ($i = 0; $i < count($intervalos_inicio); $i++) {
            if (!empty($intervalos_inicio[$i]) && !empty($intervalos_fim[$i])) {
                $motivo = isset($intervalos_motivo[$i]) ? $intervalos_motivo[$i] : '';
                mysqli_stmt_bind_param($stmt_intervalo, "issss",
                    $horario_id, $intervalos_inicio[$i], $intervalos_fim[$i], $motivo, $login);

                if (!mysqli_stmt_execute($stmt_intervalo)) {
                    throw new Exception("Erro ao inserir intervalo: " . mysqli_error($conn));
                }
            }
        }

        // 6. Inserir na agenda_padrao os horários disponíveis
        $hora_atual = strtotime($entrada);
        $hora_fim = strtotime($saida);
        $intervalo_agendamento = 30;

        while ($hora_atual < $hora_fim) {
            $horario_slot = date('H:i', $hora_atual);

            $no_almoco = false;
            if ($almoco_inicio && $almoco_fim) {
                $hora_almoco_inicio = strtotime($almoco_inicio);
                $hora_almoco_fim = strtotime($almoco_fim);
                if ($hora_atual >= $hora_almoco_inicio && $hora_atual < $hora_almoco_fim) {
                    $no_almoco = true;
                }
            }

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

            if (!$no_almoco && !$no_intervalo) {
                mysqli_stmt_bind_param($stmt_agenda, "ssssssi",
                    $usuario_api, $login, $dia, $horario_slot,
                    $profissional_nome, $profissional_cargo, $profissional_id);

                if (!mysqli_stmt_execute($stmt_agenda)) {
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

    $stmt_limpar_servicos = mysqli_prepare($conn,
        "DELETE FROM profissional_servicos WHERE profissional_id = ?");
    mysqli_stmt_bind_param($stmt_limpar_servicos, "i", $profissional_id);
    mysqli_stmt_execute($stmt_limpar_servicos);

    $stmt_prof_servico = mysqli_prepare($conn,
        "INSERT INTO profissional_servicos
         (profissional_id, servico_id, tempo_execucao_minutos, valor_profissional, login, ativo)
         VALUES (?, ?, ?, ?, ?, 1)");

    for ($i = 0; $i < count($servicos_ids); $i++) {
        if (!empty($servicos_ids[$i])) {
            $servico_id = intval($servicos_ids[$i]);
            $tempo = trim($tempos_servico[$i] ?? '');
            $valor = trim($valores_servico[$i] ?? '');

            mysqli_stmt_bind_param($stmt_prof_servico, "iisss",
                $profissional_id, $servico_id, $tempo, $valor, $login);

            if (!mysqli_stmt_execute($stmt_prof_servico)) {
                throw new Exception("Erro ao associar serviço: " . mysqli_error($conn));
            }
        }
    }

    // 8. Confirmar transação
    mysqli_commit($conn);

    $log_msg = "Horários e serviços cadastrados para o profissional $profissional_nome";
    error_log($log_msg);

    VaiPara('cadastrar_horario.php?confirmacao=servicos_cadastrados&profissional=' . $profissional_nome);

} catch (Exception $e) {
    mysqli_rollback($conn);

    error_log("Erro ao cadastrar horários/serviços: " . $e->getMessage());

    VaiPara('cadastrar_horario.php?erro=' . urlencode($e->getMessage()));
}
?>
