<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
$login = $_SESSION['login'];
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profissional_id'])) {
    $profissional_id = intval($_POST['profissional_id']);
    
    $stmt = $conn->prepare("SELECT profissional_cargo FROM profissional WHERE id = ? AND login = ?");
    $stmt->bind_param("is", $profissional_id, $login);
    $stmt->execute();
    $query = $stmt->get_result();
    $stmt->close();
    
    if($query->num_rows > 0) {
        $row = $query->fetch_array();
        $especialidades = explode(',', $row['profissional_cargo']);
        
        echo '<div class="mt-2">';
        echo '<strong>Especialidades atuais:</strong><br>';
        
        foreach($especialidades as $esp) {
            $esp = trim($esp);
            if(!empty($esp)) {
                echo '<span class="badge badge-primary mr-1">' . $esp . '</span>';
            }
        }
        echo '</div>';
    }
}
?>
