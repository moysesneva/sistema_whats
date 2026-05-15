<?php
// buscar_horarios_disponiveis.php

// Função para conectar ao banco de dados
function conectarDB() {
    $host = 'localhost'; // Ajuste conforme necessário
    $db = 'tilsco63_novo_painel';
    $user = 'tilsco63_novo_painel';
    $pass = 'aline908070';

    $conn = mysqli_connect($host, $user, $pass, $db);

    if (!$conn) {
        die("Erro na conexão: " . mysqli_connect_error());
    }

    return $conn;
}

// Função para buscar os horários disponíveis para um profissional em uma data específica
function buscarHorariosDisponiveis($id_profissional, $data) {
    $conn = conectarDB();

    // Obter o dia da semana da data fornecida
    $timestamp = strtotime($data);
    $dia_numero = date('w', $timestamp); // 0 (domingo) a 6 (sábado)

    // Mapear números de dia da semana para nomes em português
    $dias_semana_portugues = array(
        '0' => 'domingo',
        '1' => 'segunda',
        '2' => 'terça',
        '3' => 'quarta',
        '4' => 'quinta',
        '5' => 'sexta',
        '6' => 'sábado'
    );

    $dia_semana = $dias_semana_portugues[$dia_numero]; // Dia da semana em português

    // Obter os horários que o profissional trabalha nesse dia da semana
    $sql_agenda = "SELECT horario FROM agenda_padrao WHERE id_profissional = ? AND dia = ?";
    $stmt = mysqli_prepare($conn, $sql_agenda);
    mysqli_stmt_bind_param($stmt, "is", $id_profissional, $dia_semana);
    mysqli_stmt_execute($stmt);
    $result_agenda = mysqli_stmt_get_result($stmt);

    $horarios_trabalho = [];
    while ($row = mysqli_fetch_assoc($result_agenda)) {
        $horarios_trabalho[] = $row['horario'];
    }

    if (empty($horarios_trabalho)) {
        return [];
    }

    // Obter os horários já ocupados na data
    $sql_ocupados = "SELECT horario FROM agendamento WHERE id_profissional = ? AND data = ?";
    $stmt2 = mysqli_prepare($conn, $sql_ocupados);
    mysqli_stmt_bind_param($stmt2, "is", $id_profissional, $data);
    mysqli_stmt_execute($stmt2);
    $result_ocupados = mysqli_stmt_get_result($stmt2);

    $horarios_ocupados = [];
    while ($row = mysqli_fetch_assoc($result_ocupados)) {
        $horarios_ocupados[] = $row['horario'];
    }

    // Filtrar os horários disponíveis
    $horarios_disponiveis = array_diff($horarios_trabalho, $horarios_ocupados);

    mysqli_close($conn);

    return $horarios_disponiveis;
}

// Recebe o id do profissional e a data via POST
if (isset($_POST['profissional_id']) && isset($_POST['dia'])) {
    $id_profissional = intval($_POST['profissional_id']);
    $data = $_POST['dia']; // Data em formato 'Y-m-d'

    $horarios_disponiveis = buscarHorariosDisponiveis($id_profissional, $data);

    if (!empty($horarios_disponiveis)) {
        echo '<option value="">Escolha um horário</option>';
        foreach ($horarios_disponiveis as $horario) {
            echo '<option value="' . $horario . '">' . $horario . '</option>';
        }
    } else {
        echo '<option value="">Nenhum horário disponível</option>';
    }
} else {
    echo '<option value="">Escolha um horário</option>';
}
?>
