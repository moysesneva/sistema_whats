<?php

header('Content-Type: text/html; charset=utf-8');
setlocale(LC_MONETARY, 'pt_BR', 'pt_BR.utf-8');

$basePath = 'login/painel/';

// DEBUG: Verificar se o caminho para conn.php está correto e se o arquivo existe
if (!file_exists($basePath . 'conn.php')) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>DEBUG: Erro crítico: Arquivo de conexão (conn.php) não encontrado no caminho esperado: ' . htmlspecialchars($basePath . 'conn.php') . '</p></div>';
    exit;
}
include $basePath . 'conn.php';

// DEBUG: Verificar se a variável $conn foi definida pelo conn.php e se a conexão foi bem-sucedida
if (!isset($conn)) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>DEBUG: Erro: Variável de conexão ($conn) não definida após include de conn.php.</p></div>';
    exit;
}
if ($conn->connect_error) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>DEBUG: Erro de conexão com o banco: ' . htmlspecialchars($conn->connect_error) . '</p></div>';
    exit;
}
// echo "<p>DEBUG: Conexão com o banco estabelecida.</p>"; // Descomente para testar a conexão

// DEBUG: Verificar se os dados POST estão chegando
if (!isset($_POST['profissional_id']) || empty($_POST['profissional_id']) || !isset($_POST['dia_semana']) || empty($_POST['dia_semana'])) {
    // echo "<p>DEBUG: POST recebido: "; // Descomente para ver o POST
    // var_dump($_POST); // Descomente para ver o POST
    // echo "</p>"; // Descomente para ver o POST
    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>DEBUG: Dados POST insuficientes (ID do profissional ou dia da semana não fornecidos).</p></div>';
    exit;
}

$profissional_id = $conn->real_escape_string($_POST['profissional_id']);
$dia_semana_selecionado_js = $conn->real_escape_string($_POST['dia_semana']); // Valor como vem do JS
$dia_semana_comparacao_db = strtolower($dia_semana_selecionado_js); // Para comparação com o BD

// DEBUG: Mostrar os valores recebidos e processados
// echo "<p>DEBUG: ID Profissional Recebido: " . htmlspecialchars($profissional_id) . "</p>";
// echo "<p>DEBUG: Dia da Semana Recebido do JS: " . htmlspecialchars($dia_semana_selecionado_js) . "</p>";
// echo "<p>DEBUG: Dia da Semana para Comparação no DB: " . htmlspecialchars($dia_semana_comparacao_db) . "</p>";

// --- PONTO DE FALHA COMUM: O profissional trabalha neste dia da semana? ---
// Verifique se o formato de 'dia_semana' na tabela 'horarios_profissional' é compatível
// com '$dia_semana_comparacao_db' (ex: "segunda-feira", "terca-feira", etc., tudo em minúsculas).
$sql_verifica_dia = "SELECT 1 FROM horarios_profissional WHERE profissional_id = ? AND LOWER(dia_semana) = ? AND ativo = 1 LIMIT 1";
$stmt_verifica_dia = $conn->prepare($sql_verifica_dia);

if (!$stmt_verifica_dia) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>DEBUG: Erro ao preparar verificação do dia: ' . htmlspecialchars($conn->error) . '</p></div>';
    exit;
}
$stmt_verifica_dia->bind_param("ss", $profissional_id, $dia_semana_comparacao_db);
$stmt_verifica_dia->execute();
$result_verifica_dia = $stmt_verifica_dia->get_result();

// DEBUG: Verificar resultado da consulta de dia de trabalho
// echo "<p>DEBUG: Resultado da verificação do dia (num_rows): " . $result_verifica_dia->num_rows . "</p>";

if ($result_verifica_dia->num_rows == 0) {
    echo '<div class="empty-state"><i class="far fa-calendar-times"></i><p>O profissional não atende no dia da semana selecionado (' . htmlspecialchars($dia_semana_selecionado_js) . ') ou não possui horários ativos para este dia.</p></div>';
    $stmt_verifica_dia->close();
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}
$stmt_verifica_dia->close();
// echo "<p>DEBUG: Profissional ATENDE no dia da semana selecionado. Buscando serviços...</p>"; // Descomente para confirmar

// --- PONTO DE FALHA COMUM: Existem serviços para este profissional? ---
$sql_servicos = "SELECT
                    s.id AS servico_id,
                    s.nome AS servico_nome,
                    s.descricao AS servico_descricao,
                    COALESCE(ps.tempo_execucao_minutos, s.duracao_minutos) AS duracao_final,
                    COALESCE(ps.valor_profissional, s.valor) AS valor_final
                 FROM profissional_servicos ps
                 JOIN servicos s ON ps.servico_id = s.id
                 WHERE ps.profissional_id = ?
                   AND ps.ativo = 1
                   AND s.ativo = 1";

$stmt_servicos = $conn->prepare($sql_servicos);
if (!$stmt_servicos) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>DEBUG: Erro ao preparar consulta de serviços: ' . htmlspecialchars($conn->error) . '</p></div>';
    exit;
}
$stmt_servicos->bind_param("s", $profissional_id);
$stmt_servicos->execute();
$result_servicos = $stmt_servicos->get_result();

// DEBUG: Verificar resultado da consulta de serviços
// echo "<p>DEBUG: Resultado da consulta de serviços (num_rows): " . $result_servicos->num_rows . "</p>";

$output_html = '';
if ($result_servicos->num_rows > 0) {
    while ($servico = $result_servicos->fetch_assoc()) {
        $servico_id_val = htmlspecialchars($servico['servico_id']);
        $servico_nome_val = htmlspecialchars($servico['servico_nome']);
        $servico_descricao_val = !empty($servico['servico_descricao']) ? htmlspecialchars($servico['servico_descricao']) : 'Sem descrição adicional.';
        $duracao_val = (int)$servico['duracao_final'];
        $valor_val = (float)$servico['valor_final'];
        $valor_formatado = number_format($valor_val, 2, ',', '.');

        $output_html .= "
            <div class=\"service-card\"
                 data-servico-id=\"{$servico_id_val}\"
                 data-servico-nome=\"{$servico_nome_val}\"
                 data-duracao=\"{$duracao_val}\"
                 data-valor=\"{$valor_val}\">
                <div class=\"service-name\">{$servico_nome_val}</div>
                <div class=\"service-info\">
                    <span class=\"service-duration\"><i class=\"fas fa-clock\"></i> {$duracao_val} min</span>
                    <span class=\"service-price\">R$ {$valor_formatado}</span>
                </div>
                <div class=\"service-description\" style=\"font-size: 0.85em; color: #555; margin-top: 8px; padding-top: 5px; border-top: 1px dashed #eee;\">
                    {$servico_descricao_val}
                </div>
            </div>
        ";
    }
} else {
    // Esta é uma resposta válida se não houver serviços, não necessariamente um "erro".
    $output_html = '<div class="empty-state"><i class="fas fa-concierge-bell"></i><p>Nenhum serviço encontrado para este profissional no dia da semana informado.</p></div>';
}
$stmt_servicos->close();

echo $output_html; // Envia a resposta final para o AJAX

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>