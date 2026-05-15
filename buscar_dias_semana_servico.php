<?php
// HABILITAR EXIBIÇÃO DE ERROS (APENAS PARA DESENVOLVIMENTO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

$basePath = 'login/painel/';
if (!file_exists($basePath . 'conn.php')) {
    echo '<option value="">Erro crítico: Arquivo de conexão não encontrado.</option>';
    exit;
}
include $basePath . 'conn.php';

if (!isset($conn)) {
    echo '<option value="">Erro: Variável de conexão não definida.</option>';
    exit;
}
if ($conn->connect_error) {
    echo '<option value="">Erro de conexão: ' . htmlspecialchars($conn->connect_error) . '</option>';
    exit;
}

$options_html = '<option value="">Escolha um dia da semana</option>';

if (isset($_POST['profissional_id']) && !empty($_POST['profissional_id'])) {
    $profissional_id = $conn->real_escape_string($_POST['profissional_id']);

    // Ajuste na cláusula ORDER BY FIELD para usar os nomes curtos dos dias
    $sql_dias_semana = "SELECT DISTINCT dia_semana
                        FROM horarios_profissional
                        WHERE profissional_id = ? AND ativo = 1
                        ORDER BY FIELD(LOWER(dia_semana), 'domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado')";

    $stmt = $conn->prepare($sql_dias_semana);
    if (!$stmt) {
        $options_html = '<option value="">Erro ao preparar consulta de dias.</option>';
    } else {
        $stmt->bind_param("s", $profissional_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ajuste nas chaves do array $dias_ordenados para nomes curtos
            // O texto da opção (valor do array) pode continuar como você preferir para exibição.
            $dias_ordenados = [
                'domingo' => 'Domingo',
                'segunda' => 'Segunda-feira', // Chave 'segunda' corresponde a "segunda" do banco
                'terca'   => 'Terça-feira',   // Chave 'terca'
                'quarta'  => 'Quarta-feira',  // Chave 'quarta'
                'quinta'  => 'Quinta-feira',  // Chave 'quinta'
                'sexta'   => 'Sexta-feira',   // Chave 'sexta'
                'sabado'  => 'Sábado'         // Chave 'sabado'
            ];
            
            $dias_encontrados_db = [];
            while ($row = $result->fetch_assoc()) {
                // A coluna 'dia_semana' no seu banco já parece estar em minúsculas e sem espaços extras.
                // Ex: "segunda", "quarta"
                $dia_db_lower = strtolower(trim($row['dia_semana']));
                $dias_encontrados_db[$dia_db_lower] = true;
            }

            // Iterar na ordem desejada e adicionar ao HTML se encontrado no DB
            foreach($dias_ordenados as $dia_value_key => $dia_texto_display) {
                if (isset($dias_encontrados_db[$dia_value_key])) {
                     // O valor da option será a chave (ex: 'segunda')
                    $options_html .= "<option value=\"" . htmlspecialchars($dia_value_key) . "\">" . htmlspecialchars($dia_texto_display) . "</option>";
                }
            }
            
            // Verificação se alguma opção foi realmente adicionada
            if ($options_html === '<option value="">Escolha um dia da semana</option>' && !empty($dias_encontrados_db)) {
                 // Esta mensagem agora deve aparecer com menos frequência se os mapeamentos estiverem corretos
                 //$options_html = '<option value="">Dias encontrados, mas mapeamento falhou. Verifique chaves em $dias_ordenados vs. dados do DB.</option>';
                 // Para depurar, você pode imprimir os dias encontrados do DB:
                 $options_html = '<option value="">DEBUG: Dias do DB (lowercase, trimmed): ' . implode(', ', array_keys($dias_encontrados_db)) . '</option>';
            }


        } else {
            $options_html = '<option value="">Nenhum dia de atendimento configurado para este profissional.</option>';
        }
        $stmt->close();
    }
} else {
    $options_html = '<option value="">ID do profissional não fornecido.</option>';
}

echo $options_html;
// Removi o ' teste' do final para não interferir na saída HTML das options.

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>