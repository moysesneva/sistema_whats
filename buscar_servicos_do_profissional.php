<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: text/html; charset=utf-8');
// setlocale(LC_MONETARY, 'pt_BR', 'pt_BR.utf-8'); // Não estritamente necessário aqui se só formatar no final

$basePath = 'login/painel/';

if (!file_exists($basePath . 'conn.php')) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro crítico: Arquivo de conexão (conn.php) não encontrado.</p></div>';
    exit;
}
include $basePath . 'conn.php';

if (!isset($conn)) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro: Variável de conexão ($conn) não definida.</p></div>';
    exit;
}
if ($conn->connect_error) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro de conexão com o banco: ' . htmlspecialchars($conn->connect_error) . '</p></div>';
    exit;
}

// No novo fluxo, esperamos apenas profissional_id para listar os serviços dele.
// A verificação de dia da semana foi removida desta etapa.
if (!isset($_POST['profissional_id']) || empty($_POST['profissional_id'])) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>ID do profissional não fornecido.</p></div>';
    exit;
}

$profissional_id = $conn->real_escape_string($_POST['profissional_id']);

// A lógica de verificação de dia da semana ($sql_verifica_dia) foi REMOVIDA daqui,
// pois o dia ainda não foi selecionado pelo usuário nesta etapa do novo fluxo.

// Busca os serviços que o profissional oferece
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
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao preparar consulta de serviços: ' . htmlspecialchars($conn->error) . '</p></div>';
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}
$stmt_servicos->bind_param("s", $profissional_id);
$stmt_servicos->execute();
$result_servicos = $stmt_servicos->get_result();

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
    // Se não houver serviços para o profissional, esta é a mensagem correta.
    $output_html = '<div class="empty-state"><i class="fas fa-concierge-bell"></i><p>Nenhum serviço encontrado para este profissional.</p></div>';
}
$stmt_servicos->close();

echo $output_html; // Envia a resposta final para o AJAX

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>