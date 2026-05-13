<?php
// buscar_agendamentos_disponiveis.php

// ----------------------------------------------------------------------
// 1. Função para conectar ao banco de dados
// ----------------------------------------------------------------------
function conectarDB() {
    include 'login/painel/conn.php';
    return $conn;
}

// ----------------------------------------------------------------------
// 2. Função para buscar datas excluídas do banco de dados
// ----------------------------------------------------------------------
function buscarDatasExcluidas($id_profissional) {
    $conn = conectarDB();
    
    $sql_datas_excluidas = "
        SELECT data_excluida 
        FROM datas_excluidas 
        WHERE id_profissional = ? OR id_profissional IS NULL
    ";
    $stmt = mysqli_prepare($conn, $sql_datas_excluidas);
    mysqli_stmt_bind_param($stmt, "i", $id_profissional);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $datas_excluidas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $datas_excluidas[] = $row['data_excluida']; // Ex.: '2025-03-10'
    }
    
    return $datas_excluidas;
}

// ----------------------------------------------------------------------
// 3. Função para buscar agendamentos disponíveis
//    - $id_profissional: ID do profissional
//    - $dia_semana: ex.: 'segunda', 'quarta', ...
//    - $dias_futuros: quantos dias à frente buscar (default: 30)
// ----------------------------------------------------------------------
function buscarAgendamentosDisponiveis($id_profissional, $dia_semana, $dias_futuros = 30) {
    $conn = conectarDB();

    // ------------------------------------------------------------------
    // 3.1 Obter os horários que o profissional trabalha nesse dia da semana
    // ------------------------------------------------------------------
    // Como 'dia' na tabela 'agenda_padrao' é texto (segunda, terça...),
    // não faz sentido comparar com CURDATE(). Apenas selecionamos por dia.
    $sql_agenda = "
        SELECT horario 
        FROM agenda_padrao 
        WHERE id_profissional = ? 
          AND dia = ?
    ";
    $stmt = mysqli_prepare($conn, $sql_agenda);
    mysqli_stmt_bind_param($stmt, "is", $id_profissional, $dia_semana);
    mysqli_stmt_execute($stmt);
    $result_agenda = mysqli_stmt_get_result($stmt);

    $horarios_trabalho = [];
    while ($row = mysqli_fetch_assoc($result_agenda)) {
        $horarios_trabalho[] = $row['horario']; // Ex.: '08:00', '09:00', ...
    }

    // Se não houver horários cadastrados para esse dia, retorna vazio
    if (empty($horarios_trabalho)) {
        return [];
    }

    // ------------------------------------------------------------------
    // 3.2 Mapear nomes de dia da semana para números (0=domingo, 1=segunda...)
    // ------------------------------------------------------------------
    $dias_semana_portugues = [
        'domingo' => 0,
        'segunda' => 1,
        'terca'   => 2,
        'terça'   => 2, // caso precise tratar acentuação
        'quarta'  => 3,
        'quinta'  => 4,
        'sexta'   => 5,
        'sabado'  => 6,
        'sábado'  => 6  // caso precise tratar acentuação
    ];

    // Converte o dia da semana para número
    $dia_numero = $dias_semana_portugues[strtolower($dia_semana)];

    // ------------------------------------------------------------------
    // 3.3 Buscar datas excluídas do banco de dados
    // ------------------------------------------------------------------
    $datas_excluidas = buscarDatasExcluidas($id_profissional);

    // ------------------------------------------------------------------
    // 3.4 Gerar as próximas datas correspondentes ao dia da semana selecionado
    //     até $dias_futuros dias à frente
    // ------------------------------------------------------------------
    $datas_disponiveis = [];
    for ($i = 0; $i <= $dias_futuros; $i++) {
        $timestamp = strtotime("+$i days");
        $data_verificada = date('Y-m-d', $timestamp); // Ex.: '2025-03-12'
        
        // Verificar se o dia (0=domingo, 1=segunda...) bate com o $dia_numero
        if (date('w', $timestamp) == $dia_numero) {
            // Verificar se NÃO está na lista de datas excluídas
            if (!in_array($data_verificada, $datas_excluidas)) {
                $datas_disponiveis[] = $data_verificada;
            }
        }
    }

    // Se não houver datas disponíveis, não adianta seguir
    if (empty($datas_disponiveis)) {
        return [];
    }

    // ------------------------------------------------------------------
    // 3.5 Obter os horários já ocupados nessas datas
    // ------------------------------------------------------------------
    // Construímos placeholders para a cláusula IN
    $placeholders = implode(',', array_fill(0, count($datas_disponiveis), '?'));
    // Montamos a string de tipos (todas as datas são strings -> 's')
    $types = str_repeat('s', count($datas_disponiveis));
    // Unimos id_profissional com as datas no array de parâmetros
    $params = array_merge([$id_profissional], $datas_disponiveis);

    // Podemos optar por filtrar somente agendamentos futuros (se desejar).
    // Se quiser listar todos os agendamentos mesmo que sejam no passado,
    // remova a parte final do WHERE:
    $sql_ocupados = "
        SELECT data, horario 
        FROM agendamento 
        WHERE id_profissional = ?
          AND data IN ($placeholders)
          /* Se quiser ignorar agendamentos passados, descomente:
             AND (data > CURDATE() OR (data = CURDATE() AND horario > CURTIME()))
          */
    ";

    $stmt_ocupados = mysqli_prepare($conn, $sql_ocupados);
    mysqli_stmt_bind_param($stmt_ocupados, 'i' . $types, ...$params);
    mysqli_stmt_execute($stmt_ocupados);
    $result_ocupados = mysqli_stmt_get_result($stmt_ocupados);

    // Armazenamos horários ocupados em um array associativo, indexado pela data
    $horarios_ocupados = [];
    while ($row = mysqli_fetch_assoc($result_ocupados)) {
        // Exemplo: $row['data'] = '2025-03-15', $row['horario'] = '09:00'
        $horarios_ocupados[$row['data']][] = $row['horario'];
    }

    // ------------------------------------------------------------------
    // 3.6 Montar a lista final de datas e horários disponíveis
    // ------------------------------------------------------------------
    $agendamentos_disponiveis = [];
    $horaAtual = date('H:i'); // Hora atual, ex.: '10:35'
    $dataHoje = date('Y-m-d'); // Data de hoje, ex.: '2025-03-12'

    foreach ($datas_disponiveis as $data) {
        foreach ($horarios_trabalho as $horario) {

            // 3.6.1 Se a data for hoje, ignore horários que já passaram
            if ($data == $dataHoje && $horario <= $horaAtual) {
                continue; // pula esse horário, pois já passou
            }

            // 3.6.2 Verificar se o horário está ocupado nessa data
            if (isset($horarios_ocupados[$data]) && in_array($horario, $horarios_ocupados[$data])) {
                // Já existe agendamento para esse horário/data
                continue;
            }

            // Se chegou aqui, o horário está livre
            $agendamentos_disponiveis[] = [
                'data'    => $data,
                'horario' => $horario
            ];
        }
    }

    return $agendamentos_disponiveis;
}

// ----------------------------------------------------------------------
// 4. Lógica final: recebe via POST o id do profissional e o dia da semana
// ----------------------------------------------------------------------
if (isset($_POST['profissional_id']) && isset($_POST['dia_semana'])) {
    $id_profissional = intval($_POST['profissional_id']);
    $dia_semana = $_POST['dia_semana'];

    // Buscar datas/horários disponíveis nos próximos 30 dias
    $agendamentos_disponiveis = buscarAgendamentosDisponiveis($id_profissional, $dia_semana, 30);

    if (!empty($agendamentos_disponiveis)) {
        echo '<option value="">Escolha uma data e horário disponível</option>';
        foreach ($agendamentos_disponiveis as $agendamento) {
            // Formatar data e montar a label
            $data_formatada = date('d/m/Y', strtotime($agendamento['data']));
            $label = $data_formatada . ' - ' . $agendamento['horario'];

            // Montar o value para o <option>
            $value = $agendamento['data'] . '|' . $agendamento['horario'];

            echo '<option value="' . $value . '">' . $label . '</option>';
        }
    } else {
        echo '<option value="">Nenhuma data e horário disponível</option>';
    }
} else {
    echo '<option value="">Escolha uma data e horário disponível</option>';
}
?>
