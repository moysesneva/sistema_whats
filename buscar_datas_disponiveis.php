<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Definir o local para português do Brasil para formatação de data
$locale_set = setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'portuguese');
if (!$locale_set) {
    echo '<option value="">Erro: Locale pt_BR não pôde ser configurado no servidor.</option>';
    // Você pode querer registrar isso em um log também.
    // exit; // Comentar o exit para ver se o resto funciona com o locale padrão
}

// Caminho base para os includes do painel
$basePath = 'login/painel/';

// VERIFICAR EXISTÊNCIA DOS ARQUIVOS DE INCLUDE (OPCIONAL, MAS BOM PARA DEBUG)
$includes = ['conn.php', 'estilo.php', 'css_de_icones.php', 'config_dados.php', 'funcoes.php'];
foreach ($includes as $file) {
    if (!file_exists($basePath . $file)) {
        echo "<option value=''>Erro crítico: O arquivo de inclusão '{$basePath}{$file}' não foi encontrado.</option>";
        exit;
    }
}

// Tentar incluir os arquivos dentro de um bloco try-catch pode ser excessivo aqui,
// pois erros de parse em 'conn.php' ainda seriam fatais antes do catch.
// A verificação file_exists é um primeiro passo.
include $basePath . 'conn.php';
// Outros includes que não são estritamente necessários para a lógica de datas podem ser omitidos
// ou verificados se causam problemas. Para este script, apenas conn.php é vital.
// include $basePath . 'estilo.php';
// include $basePath . 'css_de_icones.php';
// include $basePath . 'config_dados.php';
// include $basePath . 'funcoes.php';


// VERIFICAR CONEXÃO COM O BANCO
if (!isset($conn)) {
    echo '<option value="">Erro: Variável de conexão com o banco ($conn) não definida após include.</option>';
    exit;
}
if ($conn->connect_error) {
    echo '<option value="">Erro: Falha na conexão com o banco: ' . htmlspecialchars($conn->connect_error) . '</option>';
    exit;
}

// VERIFICAR SE O POST FOI ENVIADO
if (!isset($_POST['profissional_id']) || empty($_POST['profissional_id'])) {
    echo '<option value="">ID do profissional não fornecido via POST.</option>';
    exit;
}

$profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_id']);
$options = '<option value="">Selecione uma data</option>';
$datas_disponiveis_formatadas = [];

// 1. Buscar os dias da semana que o profissional trabalha
$sql_horarios = "SELECT DISTINCT dia_semana FROM horarios_profissional WHERE profissional_id = '$profissional_id' AND ativo = 1";
$result_horarios = mysqli_query($conn, $sql_horarios);

if (!$result_horarios) {
    echo '<option value="">Erro na consulta de horários: ' . htmlspecialchars(mysqli_error($conn)) . '</option>';
    exit;
}

$dias_de_trabalho_str = [];
if (mysqli_num_rows($result_horarios) > 0) {
    while ($row = mysqli_fetch_assoc($result_horarios)) {
        if (isset($row['dia_semana']) && !empty(trim($row['dia_semana']))) {
            $dias_de_trabalho_str[] = strtolower(trim($row['dia_semana']));
        }
    }
}

if (empty($dias_de_trabalho_str)) {
    echo '<option value="">Profissional sem dias de trabalho válidos configurados ou nenhum dia ativo.</option>';
    exit;
}
// Para depuração: ver quais dias foram carregados
// echo '<option value="">Dias de trabalho carregados: ' . implode(', ', $dias_de_trabalho_str) . '</option>';


// 2. Buscar datas excluídas para o profissional
$datas_excluidas = [];
$sql_excluidas = "SELECT data_excluida FROM datas_excluidas WHERE id_profissional = '$profissional_id'";
$result_excluidas = mysqli_query($conn, $sql_excluidas);

if (!$result_excluidas) {
    echo '<option value="">Erro na consulta de datas excluídas: ' . htmlspecialchars(mysqli_error($conn)) . '</option>';
    // Não necessariamente um erro fatal, pode continuar sem datas excluídas.
    // Mas é bom registrar ou notificar.
} else {
    while ($row = mysqli_fetch_assoc($result_excluidas)) {
        $datas_excluidas[] = $row['data_excluida']; // Assume formato YYYY-MM-DD
    }
}

// 3. Gerar e verificar datas para os próximos N dias (ex: 60 dias)
try {
    $data_atual = new DateTime();
    $data_limite = (new DateTime())->modify('+60 days'); // Limite de 60 dias no futuro
    $intervalo = new DateInterval('P1D'); // Intervalo de 1 dia
    $periodo = new DatePeriod($data_atual, $intervalo, $data_limite);
} catch (Exception $e) {
    echo '<option value="">Erro ao criar objetos de data/período: ' . htmlspecialchars($e->getMessage()) . '</option>';
    exit;
}


foreach ($periodo as $data) {
    $data_formatada_Ymd = $data->format('Y-m-d');
    $timestamp_dia = $data->getTimestamp();
    
    // Tenta obter o nome do dia da semana. Se strftime falhar, pode ser um problema de locale.
    $nome_dia_semana_atual_raw = strftime('%A', $timestamp_dia);
    if (empty($nome_dia_semana_atual_raw) && $locale_set === false) {
        // Fallback ou log de erro se strftime não funcionar e o locale não foi setado
        // Para um fallback simples, poderia usar date('l') que é em inglês e então traduzir/mapear.
        // Mas o ideal é corrigir o problema de locale no servidor.
        // echo "<option value=''>DEBUG: strftime falhou para {$data_formatada_Ymd}</option>";
    }
    $nome_dia_semana_atual = strtolower($nome_dia_semana_atual_raw);

    $trabalha_neste_dia = false;
    foreach ($dias_de_trabalho_str as $dia_trabalho_db) {
        // Adapte esta comparação se os nomes no BD forem diferentes (ex: "segunda" vs "segunda-feira")
        // Usar strpos pode ser útil se os nomes não forem exatos mas parciais.
        // Ex: $dia_trabalho_db = 'segunda'; $nome_dia_semana_atual = 'segunda-feira';
        if (strpos($nome_dia_semana_atual, $dia_trabalho_db) !== false || $nome_dia_semana_atual === $dia_trabalho_db) {
            $trabalha_neste_dia = true;
            break;
        }
    }

    if ($trabalha_neste_dia) {
        if (!in_array($data_formatada_Ymd, $datas_excluidas)) {
            $texto_opcao_raw = strftime('%A, %d de %B de %Y', $timestamp_dia);
             // ucfirst não funciona bem com UTF-8 diretamente em algumas configurações, mb_convert_case é melhor
            $texto_opcao = mb_convert_case($texto_opcao_raw, MB_CASE_TITLE, "UTF-8");
            // Fallback se mb_convert_case não estiver disponível ou o resultado for inesperado
            if(empty($texto_opcao) || strpos($texto_opcao, '%') !== false) { // Se strftime não processou
                $texto_opcao = ucfirst($texto_opcao_raw); // Tenta ucfirst normal
            }

            $datas_disponiveis_formatadas[$data_formatada_Ymd] = $texto_opcao;
        }
    }
}

// 4. Montar as opções
if (!empty($datas_disponiveis_formatadas)) {
    ksort($datas_disponiveis_formatadas); // Ordenar as datas
    foreach ($datas_disponiveis_formatadas as $valor_data => $texto_data) {
        $options .= "<option value=\"" . htmlspecialchars($valor_data) . "\">" . htmlspecialchars($texto_data) . "</option>";
    }
} else {
    // Mantém a option inicial "Selecione uma data" e adiciona uma indicando que não há datas.
    // A página AJAX pode decidir como lidar com isso.
    // Ou alterar $options completamente:
    $options = '<option value="">Nenhuma data disponível nos próximos 60 dias.</option>';
}

echo $options;

if (isset($conn)) {
    mysqli_close($conn);
}
?>