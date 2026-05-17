<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
$login = $_SESSION['login'];

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $profissional_id = intval($_POST['id']);
    
    $stmt_busca = $conn->prepare("SELECT * FROM profissional WHERE id = ? AND login = ?");
    $stmt_busca->bind_param("is", $profissional_id, $login);
    $stmt_busca->execute();
    $query_busca = $stmt_busca->get_result();
    
    if($query_busca->num_rows > 0) {
        $profissional = $query_busca->fetch_array();
        $telefone = $profissional['telefone'];
        $stmt_busca->close();
        
        mysqli_begin_transaction($conn);
        
        try {
            $stmt_del1 = $conn->prepare("DELETE FROM profissional WHERE id = ? AND login = ?");
            $stmt_del1->bind_param("is", $profissional_id, $login);
            if (!$stmt_del1->execute()) {
                throw new Exception("Erro ao deletar profissional");
            }
            $stmt_del1->close();
            
            $stmt_del2 = $conn->prepare("DELETE FROM login WHERE login = ? AND tipo = 5");
            $stmt_del2->bind_param("s", $telefone);
            if (!$stmt_del2->execute()) {
                throw new Exception("Erro ao deletar login do profissional");
            }
            $stmt_del2->close();
            
            mysqli_commit($conn);
            
            echo "<script nonce=\"". ($GLOBALS['csp_nonce'] ?? '') ."\">
                    alert('Profissional deletado com sucesso!');
                    window.location.href = 'listar_profissionais.php';
                  </script>";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            
            echo "<script nonce=\"". ($GLOBALS['csp_nonce'] ?? '') ."\">
                    alert('Erro ao deletar profissional: " . $e->getMessage() . "');
                    window.history.back();
                  </script>";
        }
    } else {
        $stmt_busca->close();
        echo "<script nonce=\"". ($GLOBALS['csp_nonce'] ?? '') ."\">
                alert('Profissional não encontrado ou sem permissão!');
                window.history.back();
              </script>";
    }
} else {
    header('Location: cadastrar_profissional.php');
}
?>
