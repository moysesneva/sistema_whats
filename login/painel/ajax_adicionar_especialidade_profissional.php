<?php
session_start();
$login = $_SESSION['login'];
// ajax_adicionar_especialidade_profissional.php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profissional_id']) && isset($_POST['especialidade'])) {
    
    $profissional_id = intval($_POST['profissional_id']);
    $nova_especialidade = mysqli_real_escape_string($conn, $_POST['especialidade']);
    
    // Busca as especialidades atuais do profissional
    $sql_busca = "SELECT profissional_cargo FROM profissional WHERE id = '$profissional_id' AND login = '$login'";
    $query_busca = mysqli_query($conn, $sql_busca);
    
    if(mysqli_num_rows($query_busca) > 0) {
        $row = mysqli_fetch_array($query_busca);
        $especialidades_atuais = $row['profissional_cargo'];
        
        // Verifica se a especialidade já existe para este profissional
        $especialidades_array = array_map('trim', explode(',', $especialidades_atuais));
        
        if(in_array($nova_especialidade, $especialidades_array)) {
            echo 'Profissional já possui esta especialidade';
        } else {
            // Adiciona a nova especialidade
            if(empty($especialidades_atuais)) {
                $novas_especialidades = $nova_especialidade;
            } else {
                $novas_especialidades = $especialidades_atuais . ', ' . $nova_especialidade;
            }
            
            // Atualiza o profissional
            $sql_update = "UPDATE profissional SET profissional_cargo = '$novas_especialidades' 
                          WHERE id = '$profissional_id' AND login = '$login'";
            
            if(mysqli_query($conn, $sql_update)) {
                echo 'sucesso';
            } else {
                echo 'Erro ao atualizar: ' . mysqli_error($conn);
            }
        }
    } else {
        echo 'Profissional não encontrado';
    }
}
?>