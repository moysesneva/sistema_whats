<?php
session_start();
$login = $_SESSION['login'];
// ajax_buscar_especialidades_profissional.php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profissional_id'])) {
    
    $profissional_id = intval($_POST['profissional_id']);
    
    // Busca o profissional e suas especialidades
    $sql = "SELECT profissional_cargo FROM profissional WHERE id = '$profissional_id' AND login = '$login'";
    $query = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_array($query);
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