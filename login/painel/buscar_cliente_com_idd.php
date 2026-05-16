<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();

$login = $_SESSION['login'];

include 'conn.php';


$stmt_busca_usuario = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_busca_usuario, "s", $login);
mysqli_stmt_execute($stmt_busca_usuario);
$query_busca_usuario = mysqli_stmt_get_result($stmt_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = $query_busca_usuario->fetch_array()) {

$usuario_api = $rows_usuarios['usuario_api'];

}
mysqli_stmt_close($stmt_busca_usuario);

$nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';

if (strlen($nome) >= 2 || strlen($telefone) >= 2) {
    
    // Preparar a consulta SQL
    $sql = "SELECT id, nome, telefone, id_agendamento, usuario_api FROM clientes WHERE 1=1";
    $params = array();
    $types = "";
    
    if (!empty($nome)) {
        $sql .= " AND nome LIKE ? AND usuario_api = ?";
    $params[] = "%" . $nome . "%";
    $params[] = $usuario_api;
    $types .= "ss";

    }
    
    if (!empty($telefone)) {
        $sql .= " AND telefone LIKE ?";
        $params[] = "%" . $telefone . "%";
        $types .= "s";
    }
    
    $sql .= " LIMIT 10"; // Limitar resultados
    
    // Preparar e executar a consulta
    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> Clientes encontrados - Clique no cliente desejado:
                  </div>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                // Verificar se tem id_agendamento válido
                $idAgendamento = $row['id_agendamento'];
                $statusCliente = !empty($idAgendamento) ? 'Cliente ativo' : 'Cliente sem agendamento';
                $iconClass = !empty($idAgendamento) ? 'fas fa-user-check text-success' : 'fas fa-user-times text-warning';
                
                echo '<div class="cliente-resultado" onclick="selecionarCliente(' . 
                     (int)$row['id'] . ', ' . 
                     json_encode($row['nome'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ', ' . 
                     json_encode($row['telefone'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ')">';
                
                echo '<div class="d-flex justify-content-between align-items-start">';
                echo '<div>';
                echo '<h6 class="mb-1"><i class="fas fa-user me-2"></i>' . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . '</h6>';
                echo '<p class="mb-1"><i class="fas fa-phone me-2"></i>' . htmlspecialchars($row['telefone'], ENT_QUOTES, 'UTF-8') . '</p>';
                if ($row['usuario_api']) {
                    echo '<small class="text-muted"><i class="fas fa-key me-1"></i>API: ' . htmlspecialchars($row['usuario_api'], ENT_QUOTES, 'UTF-8') . '</small>';
                }
                echo '</div>';
                echo '<div class="text-end">';
                echo '<i class="' . $iconClass . '" title="' . $statusCliente . '"></i>';
                if (!empty($idAgendamento)) {
                    echo '<br><small class="text-muted">ID: ' . htmlspecialchars($idAgendamento, ENT_QUOTES, 'UTF-8') . '</small>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            // Se não encontrou, mostrar opção para cadastrar novo cliente
            echo '<div class="alert alert-warning">';
            echo '<i class="fas fa-exclamation-triangle"></i> <strong>Cliente não encontrado.</strong><br>';
            echo 'Para continuar, você precisará cadastrar este cliente no sistema primeiro, ';
            echo 'ou verificar se o nome/telefone estão corretos.';
            echo '</div>';
            
            // Botão para criar novo cliente (opcional)
            echo '<div class="text-center mt-3">';
            echo '<button class="btn btn-primary" onclick="criarNovoCliente(' . json_encode($nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ', ' . json_encode($telefone, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ')">';
            echo '<i class="fas fa-user-plus"></i> Cadastrar Novo Cliente';
            echo '</button>';
            echo '</div>';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo '<div class="alert alert-danger">';
        echo '<i class="fas fa-exclamation-circle"></i> Erro ao buscar clientes. Tente novamente.';
        echo '</div>';
    }
} else {
    echo '<div class="help-text mt-3">';
    echo '<i class="fas fa-info-circle"></i> Digite pelo menos 2 caracteres para buscar clientes.';
    echo '</div>';
}

mysqli_close($conn);
?>

<script>
function criarNovoCliente(nome, telefone) {
    if (confirm('Deseja cadastrar o cliente "' + nome + '" com telefone "' + telefone + '"?')) {
        // Redirecionar para página de cadastro ou fazer via AJAX
        window.location.href = 'cadastrar_cliente.php?nome=' + encodeURIComponent(nome) + '&telefone=' + encodeURIComponent(telefone);
    }
}
</script>
