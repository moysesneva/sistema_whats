<?php
require_once __DIR__ . '/api_auth.php';
date_default_timezone_set('America/Sao_Paulo');
include '../conn.php';


// 1. Inclui o arquivo de conexão e define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Variável que identifica o usuário da API
$usuario_api = 'agenda_553184767331';

/**
 * Função para calcular o atraso de um envio.
 * @param string $data_agendada A data/hora que o envio deveria ter ocorrido.
 * @return string O tempo de atraso formatado.
 */
function calcularTempoAtraso($data_agendada) {
    $agora = new DateTime();
    $data_prevista = new DateTime($data_agendada);
    
    // Se a data prevista ainda não passou, não há atraso.
    if ($agora < $data_prevista) {
        return "Ainda não agendado";
    }
    
    $intervalo = $agora->diff($data_prevista);
    return $intervalo->format('%d dias, %h horas, %i min e %s seg de atraso');
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador de Envios Atuais</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1, h2 { color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .campanha { border-left: 5px solid #3498db; padding-left: 15px; margin-bottom: 25px; }
        .envio-item { background-color: #e8f4fd; border: 1px solid #d1e9fc; padding: 15px; border-radius: 5px; margin-top: 10px; }
        .envio-item strong { color: #0056b3; }
        .nenhum-envio { color: #888; font-style: italic; }
        .info-header { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .atraso { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <div class="info-header">
            <h1>Verificador de Envios Imediatos</h1>
            <p>
                <strong>Usuário API:</strong> <?php echo htmlspecialchars($usuario_api, ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>Consulta realizada em:</strong> <?php echo date('d/m/Y H:i:s'); ?> (Horário de Brasília)
            </p>
        </div>

        <?php
        // Parâmetros para a consulta
        $agora = date('Y-m-d H:i:s');
        $dia_da_semana = date('w'); // 0 (Domingo) a 6 (Sábado)

        // Query para buscar campanhas ATIVAS NESTE MOMENTO
        $sql_campanhas = "
            SELECT id, campaign_name, message_text, proximo_envio, created_at
            FROM mensagens_massa
            WHERE
                usuario_api = ? AND
                status = 'pendente' AND
                proximo_envio <= ? AND
                CAST(? AS TIME) BETWEEN start_time AND end_time AND
                FIND_IN_SET(?, days_week) > 0";
        
        $stmt_campanhas = $conn->prepare($sql_campanhas);
        if (!$stmt_campanhas) {
            die("Erro na preparação da query de campanhas: " . $conn->error);
        }
        $stmt_campanhas->bind_param("sssi", $usuario_api, $agora, $agora, $dia_da_semana);
        $stmt_campanhas->execute();
        $result_campanhas = $stmt_campanhas->get_result();

        if ($result_campanhas->num_rows > 0) {
            echo "<h2>Encontradas campanhas com envios pendentes para agora:</h2>";

            // Para cada campanha ativa, busca o próximo cliente na fila
            while($campanha = $result_campanhas->fetch_assoc()) {
                echo "<div class='campanha'>";
                echo "<h3>Campanha: '" . htmlspecialchars($campanha['campaign_name'], ENT_QUOTES, 'UTF-8') . "' (ID: " . $campanha['id'] . ")</h3>";
                echo "<p><strong>Atraso no processamento da fila:</strong> <span class='atraso'>" . calcularTempoAtraso($campanha['proximo_envio']) . "</span></p>";

                // Query para buscar o próximo envio PENDENTE para esta campanha
                $sql_proximo_envio = "
                    SELECT id, cliente_id, cliente_nome, cliente_telefone, created_at
                    FROM mensagens_massa_envios
                    WHERE
                        mensagem_massa_id = ? AND
                        status = 'pendente'
                    ORDER BY id ASC
                    LIMIT 1";
                
                $stmt_proximo = $conn->prepare($sql_proximo_envio);
                if (!$stmt_proximo) {
                    die("Erro na preparação da query de envios: " . $conn->error);
                }
                $stmt_proximo->bind_param("i", $campanha['id']);
                $stmt_proximo->execute();
                $result_proximo = $stmt_proximo->get_result();

                if ($result_proximo->num_rows > 0) {
                    $envio = $result_proximo->fetch_assoc();
                    echo "<div class='envio-item'>";
                    echo "<h4>&#128233; Próximo Envio a ser Realizado:</h4>";
                    echo "<strong>ID do Envio:</strong> " . htmlspecialchars($envio['id'], ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "<strong>Cliente:</strong> " . htmlspecialchars($envio['cliente_nome'], ENT_QUOTES, 'UTF-8') . " (ID: " . htmlspecialchars($envio['cliente_id'], ENT_QUOTES, 'UTF-8') . ")<br>";
                    echo "<strong>Telefone:</strong> " . htmlspecialchars($envio['cliente_telefone'], ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "<strong>Mensagem (modelo):</strong> <code>" . htmlspecialchars($campanha['message_text'], ENT_QUOTES, 'UTF-8') . "</code><br>";
                    echo "<strong>Aguardando na fila há:</strong> " . calcularTempoDecorrido($envio['created_at']) . "";
                    echo "</div>";
                } else {
                    echo "<p class='nenhum-envio'>Esta campanha está pendente, mas não há mais clientes na fila de envio. (Recomenda-se atualizar o status da campanha para 'concluida').</p>";
                }
                $stmt_proximo->close();
                echo "</div>";
            }
        } else {
            echo "<h2><span style='color: #2ecc71;'>&#10004;</span> Tudo em dia!</h2>";
            echo "<p class='nenhum-envio'>Nenhuma campanha com envio agendado para este exato momento.</p>";
        }
        $stmt_campanhas->close();
        ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>