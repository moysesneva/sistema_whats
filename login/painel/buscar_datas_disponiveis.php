<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'portuguese');

if (!isset($conn) || $conn->connect_error) {
    echo '<option value="">Erro: Falha na conexão com o banco.</option>';
    exit;
}

if (isset($_POST['profissional_id']) && !empty($_POST['profissional_id'])) {
    $profissional_id = intval($_POST['profissional_id']);
    $options = '<option value="">Selecione uma data</option>';
    $datas_disponiveis_formatadas = [];

    $stmt_horarios = mysqli_prepare($conn,
        "SELECT DISTINCT dia_semana FROM horarios_profissional WHERE profissional_id = ? AND ativo = 1");
    mysqli_stmt_bind_param($stmt_horarios, "i", $profissional_id);
    mysqli_stmt_execute($stmt_horarios);
    $result_horarios = mysqli_stmt_get_result($stmt_horarios);

    $dias_de_trabalho_str = [];
    if ($result_horarios && mysqli_num_rows($result_horarios) > 0) {
        while ($row = mysqli_fetch_assoc($result_horarios)) {
            $dias_de_trabalho_str[] = strtolower($row['dia_semana']);
        }
    } else {
        echo '<option value="">Profissional sem dias de trabalho configurados.</option>';
        exit;
    }

    if (empty($dias_de_trabalho_str)) {
        echo '<option value="">Nenhum dia de trabalho encontrado para este profissional.</option>';
        exit;
    }

    $datas_excluidas = [];
    $stmt_excluidas = mysqli_prepare($conn,
        "SELECT data_excluida FROM datas_excluidas WHERE id_profissional = ?");
    mysqli_stmt_bind_param($stmt_excluidas, "i", $profissional_id);
    mysqli_stmt_execute($stmt_excluidas);
    $result_excluidas = mysqli_stmt_get_result($stmt_excluidas);
    if ($result_excluidas) {
        while ($row = mysqli_fetch_assoc($result_excluidas)) {
            $datas_excluidas[] = $row['data_excluida'];
        }
    }

    $data_atual = new DateTime();
    $data_limite = (new DateTime())->modify('+60 days');
    $intervalo = new DateInterval('P1D');
    $periodo = new DatePeriod($data_atual, $intervalo, $data_limite);

    foreach ($periodo as $data) {
        $data_formatada_Ymd = $data->format('Y-m-d');
        $nome_dia_semana_atual = strtolower(strftime('%A', $data->getTimestamp()));

        $trabalha_neste_dia = false;
        foreach ($dias_de_trabalho_str as $dia_trabalho_db) {
            if (strpos($nome_dia_semana_atual, $dia_trabalho_db) !== false || $nome_dia_semana_atual === $dia_trabalho_db) {
                $trabalha_neste_dia = true;
                break;
            }
        }

        if ($trabalha_neste_dia) {
            if (!in_array($data_formatada_Ymd, $datas_excluidas)) {
                $texto_opcao = ucfirst(strftime('%A, %d de %B de %Y', $data->getTimestamp()));
                $datas_disponiveis_formatadas[$data_formatada_Ymd] = $texto_opcao;
            }
        }
    }

    if (!empty($datas_disponiveis_formatadas)) {
        ksort($datas_disponiveis_formatadas);
        foreach ($datas_disponiveis_formatadas as $valor_data => $texto_data) {
            $options .= "<option value=\"{$valor_data}\">{$texto_data}</option>";
        }
    } else {
        $options = '<option value="">Nenhuma data disponível nos próximos 60 dias.</option>';
    }

    echo $options;

} else {
    echo '<option value="">ID do profissional não fornecido.</option>';
}

mysqli_close($conn);
?>
