<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8'); // Resposta será JSON

$basePath = 'login/painel/';
if (!file_exists($basePath . 'conn.php')) {
    echo json_encode(["error" => "Erro crítico: Arquivo de conexão não encontrado."]);
    exit;
}
include $basePath . 'conn.php';

if (!isset($conn)) {
    echo json_encode(["error" => "Erro: Variável de conexão não definida."]);
    exit;
}
if ($conn->connect_error) {
    echo json_encode(["error" => "Erro de conexão: " . htmlspecialchars($conn->connect_error)]);
    exit;
}

// Validar entradas
$required_params = ['profissional_id', 'data_selecionada', 'servico_id', 'duracao_servico'];
foreach ($required_params as $param) {
    if (!isset($_POST[$param]) || empty($_POST[$param])) {
        echo json_encode(["error" => "Parâmetro ausente ou vazio: " . $param]);
        exit;
    }
}

$profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_id']);
$data_selecionada_str = mysqli_real_escape_string($conn, $_POST['data_selecionada']); // YYYY-MM-DD
// $servico_id_atual = mysqli_real_escape_string($conn, $_POST['servico_id']); // ID do serviço sendo agendado
$duracao_servico_atual_min = (int)$_POST['duracao_servico']; // Duração do serviço sendo agendado

if ($duracao_servico_atual_min <= 0) {
    echo json_encode(["error" => "Duração do serviço inválida."]);
    exit;
}

$slots_disponiveis = [];

try {
    // --- 1. Obter horário de trabalho do profissional para o dia da semana selecionado ---
    $data_obj = new DateTime($data_selecionada_str);
    // Nomes dos dias em português para compatibilidade com strftime usado anteriormente.
    // Se seu BD usa números (0-6) ou outros nomes, ajuste aqui.
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'portuguese');
    $dia_semana_php = strtolower(strftime('%A', $data_obj->getTimestamp())); // ex: 'segunda-feira'

    $sql_horario_profissional = "SELECT id, hora_entrada, hora_saida, almoco_inicio, almoco_fim
                                 FROM horarios_profissional
                                 WHERE profissional_id = '$profissional_id'
                                   AND dia_semana = '$dia_semana_php' -- Ajuste se o nome no BD for diferente
                                   AND ativo = 1";
    $result_horario_prof = mysqli_query($conn, $sql_horario_profissional);

    if (!$result_horario_prof || mysqli_num_rows($result_horario_prof) == 0) {
        echo json_encode([]); // Nenhum horário de trabalho configurado ou profissional não trabalha no dia
        exit;
    }
    $horario_prof = mysqli_fetch_assoc($result_horario_prof);
    $horario_profissional_id_db = $horario_prof['id']; // ID para buscar intervalos

    $dt_entrada_prof = new DateTime($data_selecionada_str . ' ' . $horario_prof['hora_entrada']);
    $dt_saida_prof = new DateTime($data_selecionada_str . ' ' . $horario_prof['hora_saida']);
    $dt_almoco_inicio = $horario_prof['almoco_inicio'] ? new DateTime($data_selecionada_str . ' ' . $horario_prof['almoco_inicio']) : null;
    $dt_almoco_fim = $horario_prof['almoco_fim'] ? new DateTime($data_selecionada_str . ' ' . $horario_prof['almoco_fim']) : null;


    // --- 2. Obter intervalos adicionais do profissional ---
    $intervalos_adicionais = [];
    $sql_intervalos = "SELECT intervalo_inicio, intervalo_fim
                       FROM intervalos_profissional
                       WHERE horario_id = '$horario_profissional_id_db'"; // Usando o ID do horário do profissional
    $result_intervalos = mysqli_query($conn, $sql_intervalos);
    if ($result_intervalos) {
        while ($intervalo = mysqli_fetch_assoc($result_intervalos)) {
            $intervalos_adicionais[] = [
                'inicio' => new DateTime($data_selecionada_str . ' ' . $intervalo['intervalo_inicio']),
                'fim' => new DateTime($data_selecionada_str . ' ' . $intervalo['intervalo_fim'])
            ];
        }
    }


    // --- 3. Obter agendamentos existentes ---
    $agendamentos_existentes = [];
    // **IMPORTANTE**: A query abaixo assume que você adicionou 'servico_id' à tabela 'agendamento'
    // para poder buscar a duração do serviço agendado.
    $sql_agendamentos = "SELECT a.horario AS horario_inicio_agendado, s.duracao_minutos AS duracao_agendado_minutos
                         FROM agendamento a
                         JOIN servicos s ON a.servico_id = s.id -- ASSUMINDO QUE agendamento.servico_id EXISTE!
                         WHERE a.id_profissional = '$profissional_id'
                           AND a.data = '$data_selecionada_str'
                           AND a.confirmacao != 2"; // Exclui cancelados (se 2 for cancelado)

    // Se você não tem servico_id em agendamento, mas tem uma coluna duracao_agendado_minutos:
    // $sql_agendamentos = "SELECT horario AS horario_inicio_agendado, duracao_agendado_minutos
    //                      FROM agendamento
    //                      WHERE id_profissional = '$profissional_id'
    //                        AND data = '$data_selecionada_str'
    //                        AND confirmacao != 2";

    // Se não tiver nem servico_id nem duracao_agendado_minutos na tabela agendamento,
    // você não poderá calcular o horário de término dos agendamentos existentes com precisão.
    // Nesse caso, a lógica de verificação de conflito será falha.

    $result_agendamentos = mysqli_query($conn, $sql_agendamentos);
    if ($result_agendamentos) {
        while ($ag = mysqli_fetch_assoc($result_agendamentos)) {
            $dt_inicio_ag = new DateTime($data_selecionada_str . ' ' . $ag['horario_inicio_agendado']);
            $duracao_ag_min = (int)$ag['duracao_agendado_minutos'];
            if ($duracao_ag_min > 0) {
                $dt_fim_ag = (clone $dt_inicio_ag)->add(new DateInterval('PT' . $duracao_ag_min . 'M'));
                $agendamentos_existentes[] = ['inicio' => $dt_inicio_ag, 'fim' => $dt_fim_ag];
            }
        }
    }


    // --- 4. Gerar e Verificar Slots ---
    $intervalo_slot_min = 15; // Define o intervalo para gerar os slots (ex: a cada 15 minutos)
    $hora_corrente = clone $dt_entrada_prof;

    while ($hora_corrente < $dt_saida_prof) {
        $slot_inicio_potencial = clone $hora_corrente;
        $slot_fim_potencial = (clone $hora_corrente)->add(new DateInterval('PT' . $duracao_servico_atual_min . 'M'));

        // Se o fim do slot potencial ultrapassar a hora de saída do profissional, para.
        if ($slot_fim_potencial > $dt_saida_prof) {
            break;
        }

        $slot_disponivel = true;

        // Verificar conflito com almoço
        if ($dt_almoco_inicio && $dt_almoco_fim) {
            if (($slot_inicio_potencial >= $dt_almoco_inicio && $slot_inicio_potencial < $dt_almoco_fim) ||
                ($slot_fim_potencial > $dt_almoco_inicio && $slot_fim_potencial <= $dt_almoco_fim) ||
                ($slot_inicio_potencial < $dt_almoco_inicio && $slot_fim_potencial > $dt_almoco_fim)) {
                $slot_disponivel = false;
            }
        }

        // Verificar conflito com intervalos adicionais
        if ($slot_disponivel) {
            foreach ($intervalos_adicionais as $intervalo_add) {
                if (($slot_inicio_potencial >= $intervalo_add['inicio'] && $slot_inicio_potencial < $intervalo_add['fim']) ||
                    ($slot_fim_potencial > $intervalo_add['inicio'] && $slot_fim_potencial <= $intervalo_add['fim']) ||
                    ($slot_inicio_potencial < $intervalo_add['inicio'] && $slot_fim_potencial > $intervalo_add['fim'])) {
                    $slot_disponivel = false;
                    break;
                }
            }
        }

        // Verificar conflito com agendamentos existentes
        if ($slot_disponivel) {
            foreach ($agendamentos_existentes as $ag_existente) {
                 if (($slot_inicio_potencial >= $ag_existente['inicio'] && $slot_inicio_potencial < $ag_existente['fim']) ||
                    ($slot_fim_potencial > $ag_existente['inicio'] && $slot_fim_potencial <= $ag_existente['fim']) ||
                    ($slot_inicio_potencial < $ag_existente['inicio'] && $slot_fim_potencial > $ag_existente['fim'])) {
                    $slot_disponivel = false;
                    break;
                }
            }
        }

        $slots_disponiveis[] = [
            "horario" => $slot_inicio_potencial->format('H:i'),
            "disponivel" => $slot_disponivel
        ];

        $hora_corrente->add(new DateInterval('PT' . $intervalo_slot_min . 'M'));
    }

} catch (Exception $e) {
    $slots_disponiveis = ["error" => "Ocorreu uma exceção: " . $e->getMessage()];
}

echo json_encode($slots_disponiveis);

if (isset($conn)) {
    mysqli_close($conn);
}
?>