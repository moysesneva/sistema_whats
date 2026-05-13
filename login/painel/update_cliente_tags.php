<?php
session_start();
require_once 'conn.php';

// Configurar header para retornar JSON
header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

try {
    // Buscar dados do usuário logado
    $login = $_SESSION['login'];
    $sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    $total_busca_usuario = mysqli_num_rows($query_busca_usuario);

    if ($total_busca_usuario == 0) {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit;
    }

    // Obter dados do usuário
    $usuario_data = mysqli_fetch_array($query_busca_usuario);
    $usuario_api = $usuario_data['usuario_api'];
    $tipo = $usuario_data['tipo'];
    #$autorizado = $usuario_data['autorizado'];

    // Verificar se usuário está autorizado
    #if ($autorizado != 1) {
    #    echo json_encode(['success' => false, 'message' => 'Usuário não autorizado']);
    #   exit;
    #}

    // Receber dados do POST
    $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
    $etiquetas = isset($_POST['etiquetas']) ? trim($_POST['etiquetas']) : '';

    // Validar ID do cliente
    if (!$cliente_id || $cliente_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do cliente inválido']);
        exit;
    }

    // Primeiro, verificar se o cliente pertence ao usuário
    $sql_verifica = "SELECT id, nome FROM clientes WHERE id = $cliente_id AND usuario_api = '$usuario_api'";
    $result_verifica = mysqli_query($conn, $sql_verifica);
    
    if (!$result_verifica || mysqli_num_rows($result_verifica) == 0) {
        echo json_encode(['success' => false, 'message' => 'Cliente não encontrado ou sem permissão']);
        exit;
    }

    $cliente_data = mysqli_fetch_assoc($result_verifica);
    $cliente_nome = $cliente_data['nome'];

    // Processar etiquetas
    // Limpar e formatar as etiquetas
    $etiquetas_array = array_filter(array_map('trim', explode(',', $etiquetas)));
    $etiquetas_formatadas = implode(', ', $etiquetas_array);
    
    // Sanitizar para evitar SQL injection
    $etiquetas_safe = mysqli_real_escape_string($conn, $etiquetas_formatadas);

    // Atualizar etiquetas do cliente
    $sql_update = "UPDATE clientes 
                   SET etiqueta = '$etiquetas_safe',
                       updated_at = NOW()
                   WHERE id = $cliente_id 
                   AND usuario_api = '$usuario_api'";

    $result_update = mysqli_query($conn, $sql_update);

    if (!$result_update) {
        throw new Exception('Erro ao atualizar etiquetas: ' . mysqli_error($conn));
    }

    // Verificar se realmente atualizou
    if (mysqli_affected_rows($conn) == 0) {
        // Pode ser que o valor seja o mesmo, então vamos considerar como sucesso
        // mas vamos verificar se o cliente existe
        $sql_check = "SELECT id FROM clientes WHERE id = $cliente_id AND usuario_api = '$usuario_api'";
        $result_check = mysqli_query($conn, $sql_check);
        
        if (mysqli_num_rows($result_check) == 0) {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado']);
            exit;
        }
    }

    // Log da alteração (opcional - criar tabela de logs se desejar)
    $log_message = "Etiquetas do cliente ID $cliente_id ($cliente_nome) atualizadas por $login";
    registrar_log_etiquetas($conn, $usuario_api, $login, $cliente_id, $etiquetas_safe, $log_message);

    // Retornar sucesso com dados atualizados
    echo json_encode([
        'success' => true, 
        'message' => 'Etiquetas atualizadas com sucesso',
        'cliente_id' => $cliente_id,
        'cliente_nome' => $cliente_nome,
        'etiquetas' => $etiquetas_formatadas,
        'total_etiquetas' => count($etiquetas_array)
    ]);

} catch (Exception $e) {
    // Em caso de erro, retornar mensagem
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

/**
 * Função para registrar log de alterações de etiquetas
 * Opcional - você pode remover se não quiser logs
 */
function registrar_log_etiquetas($conn, $usuario_api, $login, $cliente_id, $etiquetas, $descricao) {
    // Verificar se a tabela de logs existe
    $sql_check_table = "SHOW TABLES LIKE 'logs_etiquetas'";
    $result_check = mysqli_query($conn, $sql_check_table);
    
    if (mysqli_num_rows($result_check) == 0) {
        // Criar tabela de logs se não existir
        $sql_create_table = "
            CREATE TABLE IF NOT EXISTS logs_etiquetas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_api VARCHAR(255),
                login VARCHAR(255),
                cliente_id INT,
                etiquetas TEXT,
                descricao TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_usuario_api (usuario_api),
                INDEX idx_cliente_id (cliente_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        mysqli_query($conn, $sql_create_table);
    }
    
    // Inserir log
    $descricao_safe = mysqli_real_escape_string($conn, $descricao);
    $sql_log = "INSERT INTO logs_etiquetas (usuario_api, login, cliente_id, etiquetas, descricao) 
                VALUES ('$usuario_api', '$login', $cliente_id, '$etiquetas', '$descricao_safe')";
    
    mysqli_query($conn, $sql_log);
}

/**
 * Função auxiliar para obter estatísticas de etiquetas
 * Retorna as etiquetas mais usadas pelo usuário
 */
function obter_etiquetas_populares($conn, $usuario_api, $limite = 10) {
    $sql = "
        SELECT etiqueta, COUNT(*) as total
        FROM (
            SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(clientes.etiqueta, ',', numbers.n), ',', -1)) as etiqueta
            FROM clientes
            CROSS JOIN (
                SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                UNION ALL SELECT 9 UNION ALL SELECT 10
            ) numbers
            WHERE usuario_api = '$usuario_api'
            AND LENGTH(clientes.etiqueta) - LENGTH(REPLACE(clientes.etiqueta, ',', '')) >= numbers.n - 1
            AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(clientes.etiqueta, ',', numbers.n), ',', -1)) != ''
        ) as tags
        GROUP BY etiqueta
        ORDER BY total DESC
        LIMIT $limite
    ";
    
    $result = mysqli_query($conn, $sql);
    $etiquetas_populares = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $etiquetas_populares[] = [
                'etiqueta' => $row['etiqueta'],
                'total' => $row['total']
            ];
        }
    }
    
    return $etiquetas_populares;
}

// Fechar conexão
#mysqli_close($conn);
?>