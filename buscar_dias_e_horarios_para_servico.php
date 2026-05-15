<?php
// buscar_dias_e_horarios_para_servico.php

// HABILITAR EXIBIÇÃO DE ERROS (REMOVER OU COMENTAR EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$basePath = 'login/painel/'; // Ajuste este caminho se o seu conn.php estiver em outro lugar relativo a este arquivo
$fuso_horario = new DateTimeZone('America/Sao_Paulo'); // IMPORTANTE: Ajuste para seu fuso horário
$hoje = new DateTime('now', $fuso_horario);

function output_json_error($message) {
    echo json_encode(['error' => $message, 'data' => []]);
    exit;
}

function output_json_success($data) {
    echo json_encode($data);
    exit;
}

// --- Conexão com o Banco de Dados ---
if (!file_exists($basePath . 'conn.php')) {
    output_json_error('Erro crítico: Arquivo de conexão (conn.php) não encontrado no caminho: ' . $basePath . 'conn.php');
}
include $basePath . 'conn.php';

if (!isset($conn)) {
    output_json_error('Erro: Variável de conexão ($conn) não definida após include de conn.php.');
}
if ($conn->connect_error) {
    output_json_error('Erro de conexão com o banco de dados: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset("utf8mb4");

// --- Validação das Entradas POST ---
if (!isset($_POST['profissional_id']) || empty($_POST['profissional_id']) ||
    !isset($_POST['servico_id'])    || empty($_POST['servico_id']) ||
    !isset($_POST['duracao'])       || !is_numeric($_POST['duracao']) || (int)$_POST['duracao'] <= 0) {
    output_json_error('Dados POST insuficientes ou inválidos. Necessário: profissional_id, servico_id, duracao.');
}

$profissional_id = $conn->real_escape_string($_POST['profissional_id']);
// $servico_id_param = $conn->real_escape_string($_POST['servico_id']); // Não usado diretamente no cálculo principal de slots, mas bom para log se necessário
$duracao_servico_minutos = (int)$_POST['duracao'];

// --- Configurações ---
$dias_para_verificar = 30;
$intervalo_slot_minutos = 15; // Granularidade dos horários oferecidos

$slots_disponiveis_formatado_para_json = [];

// --- 1. Buscar horários de trabalho padrão e de almoço do profissional ---
$horarios_trabalho_profissional = [];
$map_dias_pt_para_numero = [
    'domingo' => 0, 'segunda' => 1, 'terca'   => 2,
    'quarta'  => 3, 'quinta'  => 4, 'sexta'   => 5, 'sabado'  => 6
];

$sql_horarios = "SELECT dia_semana, hora_entrada, hora_saida, almoco_inicio, almoco_fim
                 FROM horarios_profissional
                 WHERE profissional_id = ? AND ativo = 1";
$stmt_horarios = $conn->prepare($sql_horarios);
if (!$stmt_horarios) {
    output_json_error("Erro ao preparar consulta de horários do profissional: " . htmlspecialchars($conn->error));
}
$stmt_horarios->bind_param("s", $profissional_id);
$stmt_horarios->execute();
$result_horarios = $stmt_horarios->get_result();
while ($row = $result_horarios->fetch_assoc()) {
    $dia_semana_db = strtolower(trim($row['dia_semana']));
    // Normalizar 'terça' para 'terca' se vier com cedilha do banco ou strftime no futuro
    $dia_semana_db = str_replace(['ç', 'Ç'], 'c', $dia_semana_db);
    if (array_key_exists($dia_semana_db, $map_dias_pt_para_numero)) {
        $numero_dia_semana = $map_dias_pt_para_numero[$dia_semana_db];
        $horarios_trabalho_profissional[$numero_dia_semana] = [
            'entrada'       => $row['hora_entrada'],
            'saida'         => $row['hora_saida'],
            'almoco_inicio' => $row['almoco_inicio'],
            'almoco_fim'    => $row['almoco_fim']
        ];
    }
}
$stmt_horarios->close();

if (empty($horarios_trabalho_profissional)) {
    output_json_success([]);
}

// --- 2. Buscar intervalos/pausas adicionais ---
$intervalos_adicionais_profissional = [];
$sql_intervalos = "SELECT ip.intervalo_inicio, ip.intervalo_fim, hp.dia_semana
                   FROM intervalos_profissional ip
                   JOIN horarios_profissional hp ON ip.horario_id = hp.id
                   WHERE hp.profissional_id = ? AND hp.ativo = 1";
$stmt_intervalos = $conn->prepare($sql_intervalos);
if ($stmt_intervalos) {
    $stmt_intervalos->bind_param("s", $profissional_id);
    $stmt_intervalos->execute();
    $result_intervalos = $stmt_intervalos->get_result();
    while ($row = $result_intervalos->fetch_assoc()) {
        $dia_semana_db_intervalo = strtolower(trim($row['dia_semana']));
        $dia_semana_db_intervalo = str_replace(['ç', 'Ç'], 'c', $dia_semana_db_intervalo);
        if (array_key_exists($dia_semana_db_intervalo, $map_dias_pt_para_numero)) {
            $numero_dia_semana = $map_dias_pt_para_numero[$dia_semana_db_intervalo];
            if (!isset($intervalos_adicionais_profissional[$numero_dia_semana])) {
                $intervalos_adicionais_profissional[$numero_dia_semana] = [];
            }
            $intervalos_adicionais_profissional[$numero_dia_semana][] = [
                'inicio' => $row['intervalo_inicio'],
                'fim'    => $row['intervalo_fim']
            ];
        }
    }
    $stmt_intervalos->close();
} else {
    // error_log("Aviso: Erro ao preparar consulta de intervalos adicionais: " . htmlspecialchars($conn->error));
}

// --- 3. Buscar datas excluídas (folgas, feriados específicos do profissional) ---
$datas_folga_profissional = [];
// Verifique o nome da coluna para o ID do profissional na tabela datas_excluidas. Pode ser 'id_profissional' ou 'profissional_id'
$sql_excluidas = "SELECT data_excluida FROM datas_excluidas WHERE id_profissional = ?";
$stmt_excluidas = $conn->prepare($sql_excluidas);
if ($stmt_excluidas) {
    $stmt_excluidas->bind_param("s", $profissional_id); // Assumindo que profissional_id é string; se for int, use "i"
    $stmt_excluidas->execute();
    $result_excluidas = $stmt_excluidas->get_result();
    while ($row = $result_excluidas->fetch_assoc()) {
        $datas_folga_profissional[$row['data_excluida']] = true;
    }
    $stmt_excluidas->close();
} else {
    // error_log("Aviso: Erro ao preparar consulta de datas excluídas: " . htmlspecialchars($conn->error));
}

// --- 4. Buscar agendamentos existentes para calcular períodos ocupados ---
$periodos_ocupados_profissional = [];

// ***** CORREÇÃO APLICADA AQUI *****
// Busca a duração diretamente da coluna `duracao_minutos` da tabela `agendamento`.
$sql_agendamentos_corrigido = "SELECT data, horario, duracao_minutos AS duracao_servico_agendado_minutos
                               FROM agendamento
                               WHERE id_profissional = ? 
                                 AND (confirmacao IS NULL OR confirmacao != 2)"; // Exclui cancelados (confirmacao = 2)

$stmt_agendamentos = $conn->prepare($sql_agendamentos_corrigido);
if (!$stmt_agendamentos) {
    output_json_error("Erro ao preparar consulta de agendamentos existentes: " . htmlspecialchars($conn->error));
}
$stmt_agendamentos->bind_param("s", $profissional_id); // Assumindo que id_profissional é string no bind; se for int, use "i"
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();
while ($row = $result_agendamentos->fetch_assoc()) {
    $data_ag = $row['data'];
    $horario_inicio_ag_str = $row['horario'];
    // Usa a duracao_minutos da tabela agendamento
    $duracao_ag_min = (int)$row['duracao_servico_agendado_minutos'];

    if (empty($data_ag) || empty($horario_inicio_ag_str) || $duracao_ag_min <= 0) {
        // error_log("Agendamento com dados inválidos ou duração zero para prof $profissional_id: Data=$data_ag, Horario=$horario_inicio_ag_str, Duracao=$duracao_ag_min");
        continue;
    }

    try {
        $inicio_ocupado = new DateTime($data_ag . ' ' . $horario_inicio_ag_str, $fuso_horario);
        $fim_ocupado = (clone $inicio_ocupado)->add(new DateInterval("PT{$duracao_ag_min}M"));

        if (!isset($periodos_ocupados_profissional[$data_ag])) {
            $periodos_ocupados_profissional[$data_ag] = [];
        }
        $periodos_ocupados_profissional[$data_ag][] = ['inicio' => $inicio_ocupado, 'fim' => $fim_ocupado];
    } catch (Exception $e) {
        error_log("Data/hora inválida no agendamento para prof $profissional_id: $data_ag $horario_inicio_ag_str - " . $e->getMessage());
    }
}
$stmt_agendamentos->close();

// --- 5. Iterar pelos próximos N dias para encontrar slots ---
$data_corrente_loop = new DateTime('now', $fuso_horario);
$data_corrente_loop->setTime(0,0,0);
$dias_processados = 0;

while ($dias_processados < $dias_para_verificar) {
    $data_atual_formatada_bd = $data_corrente_loop->format('Y-m-d');
    $numero_dia_semana_loop = (int)$data_corrente_loop->format('w');
    $horarios_disponiveis_para_esta_data = [];

    if (isset($datas_folga_profissional[$data_atual_formatada_bd])) {
        $data_corrente_loop->add(new DateInterval('P1D'));
        $dias_processados++;
        continue;
    }

    if (!isset($horarios_trabalho_profissional[$numero_dia_semana_loop])) {
        $data_corrente_loop->add(new DateInterval('P1D'));
        $dias_processados++;
        continue;
    }

    $config_horario_dia = $horarios_trabalho_profissional[$numero_dia_semana_loop];

    try {
        $hora_entrada_obj = new DateTime($data_atual_formatada_bd . ' ' . $config_horario_dia['entrada'], $fuso_horario);
        $hora_saida_obj = new DateTime($data_atual_formatada_bd . ' ' . $config_horario_dia['saida'], $fuso_horario);
        $almoco_inicio_obj = null;
        $almoco_fim_obj = null;
        if (!empty($config_horario_dia['almoco_inicio']) && $config_horario_dia['almoco_inicio'] !== '00:00:00' &&
            !empty($config_horario_dia['almoco_fim']) && $config_horario_dia['almoco_fim'] !== '00:00:00') {
            $almoco_inicio_obj = new DateTime($data_atual_formatada_bd . ' ' . $config_horario_dia['almoco_inicio'], $fuso_horario);
            $almoco_fim_obj = new DateTime($data_atual_formatada_bd . ' ' . $config_horario_dia['almoco_fim'], $fuso_horario);
        }

        $slot_inicio_proposto = clone $hora_entrada_obj;

        while ($slot_inicio_proposto < $hora_saida_obj) {
            $slot_fim_proposto = (clone $slot_inicio_proposto)->add(new DateInterval("PT{$duracao_servico_minutos}M"));
            if ($slot_fim_proposto > $hora_saida_obj) break;

            $este_slot_esta_livre = true;

            if ($almoco_inicio_obj && $almoco_fim_obj) {
                if ($slot_inicio_proposto < $almoco_fim_obj && $slot_fim_proposto > $almoco_inicio_obj) {
                    $este_slot_esta_livre = false;
                }
            }

            if ($este_slot_esta_livre && isset($intervalos_adicionais_profissional[$numero_dia_semana_loop])) {
                foreach ($intervalos_adicionais_profissional[$numero_dia_semana_loop] as $intervalo) {
                    $intervalo_inicio_obj = new DateTime($data_atual_formatada_bd . ' ' . $intervalo['inicio'], $fuso_horario);
                    $intervalo_fim_obj    = new DateTime($data_atual_formatada_bd . ' ' . $intervalo['fim'], $fuso_horario);
                    if ($slot_inicio_proposto < $intervalo_fim_obj && $slot_fim_proposto > $intervalo_inicio_obj) {
                        $este_slot_esta_livre = false;
                        break;
                    }
                }
            }

            if ($este_slot_esta_livre && isset($periodos_ocupados_profissional[$data_atual_formatada_bd])) {
                foreach ($periodos_ocupados_profissional[$data_atual_formatada_bd] as $periodo_ocupado) {
                    if ($slot_inicio_proposto < $periodo_ocupado['fim'] && $slot_fim_proposto > $periodo_ocupado['inicio']) {
                        $este_slot_esta_livre = false;
                        break;
                    }
                }
            }

            if ($este_slot_esta_livre) {
                $agora_exato = new DateTime('now', $fuso_horario);
                if ($data_atual_formatada_bd > $agora_exato->format('Y-m-d') ||
                    ($data_atual_formatada_bd == $agora_exato->format('Y-m-d') && $slot_inicio_proposto >= $agora_exato) ) {
                    $horarios_disponiveis_para_esta_data[] = $slot_inicio_proposto->format('H:i');
                }
            }
            $slot_inicio_proposto->add(new DateInterval("PT{$intervalo_slot_minutos}M"));
        }
    } catch (Exception $e) {
        error_log("Erro ao processar data/hora para $data_atual_formatada_bd: " . $e->getMessage());
    }

    if (!empty($horarios_disponiveis_para_esta_data)) {
        // $nomes_dias_semana_pt = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        $slots_disponiveis_formatado_para_json[] = [
            'data'            => $data_atual_formatada_bd,
            // 'dia_semana_nome' => $nomes_dias_semana_pt[$numero_dia_semana_loop], // Já formatado no JS
            'horarios'        => array_values(array_unique($horarios_disponiveis_para_esta_data))
        ];
    }

    $data_corrente_loop->add(new DateInterval('P1D'));
    $dias_processados++;
}

// --- Fecha a conexão e envia a resposta ---
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

output_json_success($slots_disponiveis_formatado_para_json);
?>