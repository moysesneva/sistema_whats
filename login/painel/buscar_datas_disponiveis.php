<?php
include 'conn.php';
// Definir o local para português do Brasil para formatação de data
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'portuguese');

// Incluir o arquivo de conexão com o banco de dados
// Certifique-se de que o caminho para conn.php está correto
// e que ele inicializa a variável $conn
#include 'login/painel/conn.php';

if (!isset($conn) || $conn->connect_error) {
    echo '<option value="">Erro: Falha na conexão com o banco.</option>';
    exit;
}

if (isset($_POST['profissional_id']) && !empty($_POST['profissional_id'])) {
    $profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_id']);
    $options = '<option value="">Selecione uma data</option>';
    $datas_disponiveis_formatadas = [];

    // 1. Buscar os dias da semana que o profissional trabalha
    $sql_horarios = "SELECT DISTINCT dia_semana FROM horarios_profissional WHERE profissional_id = '$profissional_id' AND ativo = 1";
    $result_horarios = mysqli_query($conn, $sql_horarios);
    
    $dias_de_trabalho_str = []; // Armazena os nomes dos dias de trabalho (ex: 'segunda-feira')
    if ($result_horarios && mysqli_num_rows($result_horarios) > 0) {
        while ($row = mysqli_fetch_assoc($result_horarios)) {
            // Normaliza o dia da semana para minúsculas e sem acentos para correspondência
            $dia_semana_db = strtolower($row['dia_semana']);
            // Mapeamento simples (ajuste conforme os valores no seu BD)
            // Ex: 'segunda-feira', 'terca-feira', etc. ou 'segunda', 'terca'
            // Para strftime('%A'), os nomes são completos, ex: 'segunda-feira'
            $dias_de_trabalho_str[] = $dia_semana_db;
        }
    } else {
        echo '<option value="">Profissional sem dias de trabalho configurados.</option>';
        exit;
    }

    if (empty($dias_de_trabalho_str)) {
        echo '<option value="">Nenhum dia de trabalho encontrado para este profissional.</option>';
        exit;
    }

    // 2. Buscar datas excluídas para o profissional
    $datas_excluidas = [];
    $sql_excluidas = "SELECT data_excluida FROM datas_excluidas WHERE id_profissional = '$profissional_id'";
    $result_excluidas = mysqli_query($conn, $sql_excluidas);
    if ($result_excluidas) {
        while ($row = mysqli_fetch_assoc($result_excluidas)) {
            $datas_excluidas[] = $row['data_excluida']; // Assume formato YYYY-MM-DD
        }
    }

    // 3. Gerar e verificar datas para os próximos N dias (ex: 60 dias)
    $data_atual = new DateTime();
    $data_limite = (new DateTime())->modify('+60 days'); // Limite de 60 dias no futuro
    $intervalo = new DateInterval('P1D'); // Intervalo de 1 dia
    $periodo = new DatePeriod($data_atual, $intervalo, $data_limite);

    foreach ($periodo as $data) {
        $data_formatada_Ymd = $data->format('Y-m-d');
        $nome_dia_semana_atual = strtolower(strftime('%A', $data->getTimestamp())); // ex: 'segunda-feira'

        // Verifica se o nome do dia da semana atual (ex: 'segunda-feira') está na lista de dias de trabalho
        $trabalha_neste_dia = false;
        foreach ($dias_de_trabalho_str as $dia_trabalho_db) {
            // Adapte esta comparação se os nomes no BD forem diferentes (ex: "segunda" vs "segunda-feira")
            if (strpos($nome_dia_semana_atual, $dia_trabalho_db) !== false || $nome_dia_semana_atual === $dia_trabalho_db) {
                 $trabalha_neste_dia = true;
                 break;
            }
        }

        if ($trabalha_neste_dia) {
            // Verifica se a data não está na lista de datas excluídas
            if (!in_array($data_formatada_Ymd, $datas_excluidas)) {
                // Formata o texto da opção de forma amigável
                $texto_opcao = ucfirst(strftime('%A, %d de %B de %Y', $data->getTimestamp()));
                $datas_disponiveis_formatadas[$data_formatada_Ymd] = $texto_opcao;
            }
        }
    }

    // 4. Montar as opções
    if (!empty($datas_disponiveis_formatadas)) {
        // Ordenar as datas (embora DatePeriod já gere em ordem)
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