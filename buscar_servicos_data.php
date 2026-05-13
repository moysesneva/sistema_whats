<?php
// HABILITAR EXIBIÇÃO DE ERROS (APENAS PARA DESENVOLVIMENTO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8'); // Garante a codificação correta

// Definir o local para português do Brasil para formatação de números, se necessário
setlocale(LC_MONETARY, 'pt_BR', 'pt_BR.utf-8');

$basePath = 'login/painel/';
// Apenas conn.php é estritamente necessário para este script buscar dados.
// Outros includes (estilo, config_dados, etc.) podem ser adicionados se forem
// realmente necessários para a lógica de busca de serviços ou conexão.
if (!file_exists($basePath . 'conn.php')) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro crítico: Arquivo de conexão não encontrado.</p></div>';
    exit;
}
include $basePath . 'conn.php';

if (!isset($conn)) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro: Variável de conexão não definida.</p></div>';
    exit;
}
if ($conn->connect_error) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro de conexão: ' . htmlspecialchars($conn->connect_error) . '</p></div>';
    exit;
}

// Verifica se os dados necessários foram enviados via POST
if (!isset($_POST['profissional_id']) || empty($_POST['profissional_id']) || !isset($_POST['data_selecionada']) || empty($_POST['data_selecionada'])) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Dados insuficientes para buscar serviços (ID do profissional ou data não fornecidos).</p></div>';
    exit;
}

$profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_id']);
$data_selecionada = mysqli_real_escape_string($conn, $_POST['data_selecionada']); // Formato YYYY-MM-DD

// Opcional: Verificar se o profissional realmente trabalha neste dia (como uma segunda camada de validação)
// Isto pode ser mais complexo se os nomes dos dias da semana precisarem de mapeamento
// Por ora, vamos assumir que buscar_datas_disponiveis.php já filtrou corretamente.

// Buscar serviços associados ao profissional e ativos,
// e também os detalhes do serviço da tabela 'servicos'.
// Usamos LEFT JOIN para pegar todos os serviços que o profissional oferece (em profissional_servicos)
// e seus detalhes correspondentes em 'servicos'.
// Priorizamos 'tempo_execucao_minutos' e 'valor_profissional' de 'profissional_servicos' se existirem,
// caso contrário, usamos 'duracao_minutos' e 'valor' de 'servicos'.

$sql_servicos = "SELECT
                    s.id AS servico_id,
                    s.nome AS servico_nome,
                    s.descricao AS servico_descricao,
                    COALESCE(ps.tempo_execucao_minutos, s.duracao_minutos) AS duracao_final,
                    COALESCE(ps.valor_profissional, s.valor) AS valor_final
                 FROM profissional_servicos ps
                 JOIN servicos s ON ps.servico_id = s.id
                 WHERE ps.profissional_id = '$profissional_id'
                   AND ps.ativo = 1
                   AND s.ativo = 1";
// Adicione AND s.login = 'seu_login_de_sistema' se os serviços forem filtrados por login global do sistema
// Ou AND p.usuario_api = 'api_do_usuario_logado' se os serviços são atrelados ao 'usuario_api' do profissional

$result_servicos = mysqli_query($conn, $sql_servicos);

if (!$result_servicos) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao consultar serviços: ' . htmlspecialchars(mysqli_error($conn)) . '</p></div>';
    exit;
}

$output_html = '';

if (mysqli_num_rows($result_servicos) > 0) {
    while ($servico = mysqli_fetch_assoc($result_servicos)) {
        $servico_id_val = htmlspecialchars($servico['servico_id']);
        $servico_nome_val = htmlspecialchars($servico['servico_nome']);
        $servico_descricao_val = !empty($servico['servico_descricao']) ? htmlspecialchars($servico['servico_descricao']) : 'Sem descrição adicional.';
        $duracao_val = (int)$servico['duracao_final']; // Garantir que é um inteiro
        $valor_val = (float)$servico['valor_final'];   // Garantir que é um float
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
    $output_html = '<div class="empty-state"><i class="fas fa-concierge-bell"></i><p>Nenhum serviço encontrado para este profissional nesta data.</p></div>';
}

echo $output_html;

if (isset($conn)) {
    mysqli_close($conn);
}
?>