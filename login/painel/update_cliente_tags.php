<?php
session_start();
require_once 'conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

try {
    $login = $_SESSION['login'];

    $stmt_user = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
    mysqli_stmt_bind_param($stmt_user, "s", $login);
    mysqli_stmt_execute($stmt_user);
    $query_busca_usuario = mysqli_stmt_get_result($stmt_user);
    $total_busca_usuario = mysqli_num_rows($query_busca_usuario);

    if ($total_busca_usuario == 0) {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit;
    }

    $usuario_data = $query_busca_usuario->fetch_array();
    $usuario_api = $usuario_data['usuario_api'];
    $tipo = $usuario_data['tipo'];

    $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
    $etiquetas = isset($_POST['etiquetas']) ? trim($_POST['etiquetas']) : '';

    if (!$cliente_id || $cliente_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do cliente inválido']);
        exit;
    }

    $stmt_verifica = mysqli_prepare($conn,
        "SELECT id, nome FROM clientes WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt_verifica, "is", $cliente_id, $usuario_api);
    mysqli_stmt_execute($stmt_verifica);
    $result_verifica = mysqli_stmt_get_result($stmt_verifica);

    if (!$result_verifica || mysqli_num_rows($result_verifica) == 0) {
        echo json_encode(['success' => false, 'message' => 'Cliente não encontrado ou sem permissão']);
        exit;
    }

    $cliente_data = mysqli_fetch_assoc($result_verifica);
    $cliente_nome = $cliente_data['nome'];

    $etiquetas_array = array_filter(array_map('trim', explode(',', $etiquetas)));
    $etiquetas_formatadas = implode(', ', $etiquetas_array);

    $stmt_update = mysqli_prepare($conn,
        "UPDATE clientes SET etiqueta = ?, updated_at = NOW() WHERE id = ? AND usuario_api = ?");
    mysqli_stmt_bind_param($stmt_update, "sis", $etiquetas_formatadas, $cliente_id, $usuario_api);
    $result_update = mysqli_stmt_execute($stmt_update);

    if (!$result_update) {
        throw new Exception('Erro ao atualizar etiquetas: ' . mysqli_error($conn));
    }

    if (mysqli_affected_rows($conn) == 0) {
        $stmt_check = mysqli_prepare($conn,
            "SELECT id FROM clientes WHERE id = ? AND usuario_api = ?");
        mysqli_stmt_bind_param($stmt_check, "is", $cliente_id, $usuario_api);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) == 0) {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado']);
            exit;
        }
    }

    $log_message = "Etiquetas do cliente ID $cliente_id ($cliente_nome) atualizadas por $login";
    registrar_log_etiquetas($conn, $usuario_api, $login, $cliente_id, $etiquetas_formatadas, $log_message);

    echo json_encode([
        'success' => true,
        'message' => 'Etiquetas atualizadas com sucesso',
        'cliente_id' => $cliente_id,
        'cliente_nome' => $cliente_nome,
        'etiquetas' => $etiquetas_formatadas,
        'total_etiquetas' => count($etiquetas_array)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function registrar_log_etiquetas($conn, $usuario_api, $login, $cliente_id, $etiquetas, $descricao) {
    $sql_check_table = "SHOW TABLES LIKE 'logs_etiquetas'";
    $result_check = mysqli_query($conn, $sql_check_table);

    if (mysqli_num_rows($result_check) == 0) {
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

    $stmt_log = mysqli_prepare($conn,
        "INSERT INTO logs_etiquetas (usuario_api, login, cliente_id, etiquetas, descricao)
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_log, "ssiss", $usuario_api, $login, $cliente_id, $etiquetas, $descricao);
    mysqli_stmt_execute($stmt_log);
}

function obter_etiquetas_populares($conn, $usuario_api, $limite = 10) {
    $limite = intval($limite);
    $stmt = mysqli_prepare($conn, "
        SELECT etiqueta, COUNT(*) as total
        FROM (
            SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(clientes.etiqueta, ',', numbers.n), ',', -1)) as etiqueta
            FROM clientes
            CROSS JOIN (
                SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                UNION ALL SELECT 9 UNION ALL SELECT 10
            ) numbers
            WHERE usuario_api = ?
            AND LENGTH(clientes.etiqueta) - LENGTH(REPLACE(clientes.etiqueta, ',', '')) >= numbers.n - 1
            AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(clientes.etiqueta, ',', numbers.n), ',', -1)) != ''
        ) as tags
        GROUP BY etiqueta
        ORDER BY total DESC
        LIMIT $limite
    ");
    mysqli_stmt_bind_param($stmt, "s", $usuario_api);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
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
?>
