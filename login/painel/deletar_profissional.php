<?php
session_start();
$login = $_SESSION['login'];


// deletar_profissional.php
include 'conn.php';
#print_r($_REQUEST);
#exit();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $profissional_id = intval($_POST['id']);
    
    // Busca informações do profissional antes de deletar
    $sql_busca = "SELECT * FROM profissional WHERE id = '$profissional_id' AND login = '$login'";
    $query_busca = mysqli_query($conn, $sql_busca);
    
    if(mysqli_num_rows($query_busca) > 0) {
        $profissional = mysqli_fetch_array($query_busca);
        $telefone = $profissional['telefone'];
        
        // Inicia transação
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Deletar da tabela profissional
            $sql_del_profissional = "DELETE FROM profissional 
                                    WHERE id = '$profissional_id' AND login = '$login'";
            if (!mysqli_query($conn, $sql_del_profissional)) {
                throw new Exception("Erro ao deletar profissional");
            }
            
            // 2. Deletar da tabela login (onde tipo = 5 e login = telefone)
            $sql_del_login = "DELETE FROM login 
                             WHERE login = '$telefone' AND tipo = 5";
            if (!mysqli_query($conn, $sql_del_login)) {
                throw new Exception("Erro ao deletar login do profissional");
            }
            
            // Confirma transação
            mysqli_commit($conn);
            
            echo "<script>
                    alert('Profissional deletado com sucesso!');
                    window.location.href = 'listar_profissionais.php';
                  </script>";
            
        } catch (Exception $e) {
            // Desfaz transação em caso de erro
            mysqli_rollback($conn);
            
            echo "<script>
                    alert('Erro ao deletar profissional: " . $e->getMessage() . "');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('Profissional não encontrado ou sem permissão!');
                window.history.back();
              </script>";
    }
} else {
    header('Location: cadastrar_profissional.php');
}
?>