<?php
// Arquivo: remover_especialidade.php
include 'conn.php';

header('Content-Type: application/json');

if($_POST) {
    $especialidade_para_remover = mysqli_real_escape_string($conn, $_POST['especialidade']);
    $profissional_id = mysqli_real_escape_string($conn, $_POST['profissional_id']);
    
    // Buscar o campo profissional_cargo atual
    $sql_buscar = "SELECT profissional_cargo FROM profissional WHERE id = '$profissional_id'";
    $query_buscar = mysqli_query($conn, $sql_buscar);
    
    if(mysqli_num_rows($query_buscar) > 0) {
        $row = mysqli_fetch_array($query_buscar);
        $cargo_atual = $row['profissional_cargo'];
        
        // Separar as especialidades por vírgula
        $especialidades = explode(',', $cargo_atual);
        
        // Limpar espaços e criar novo array sem a especialidade removida
        $novas_especialidades = array();
        foreach($especialidades as $esp) {
            $esp_limpa = trim($esp);
            // Só adiciona se não for a especialidade a ser removida e não estiver vazia
            if(!empty($esp_limpa) && trim(strtolower($esp_limpa)) != trim(strtolower($especialidade_para_remover))) {
                $novas_especialidades[] = $esp_limpa;
            }
        }
        
        // Reorganizar e juntar com vírgulas
        $novo_cargo = implode(', ', $novas_especialidades);
        
        // Atualizar no banco de dados
        $sql_atualizar = "UPDATE profissional 
                          SET profissional_cargo = '$novo_cargo' 
                          WHERE id = '$profissional_id'";
        
        if(mysqli_query($conn, $sql_atualizar)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Especialidade removida com sucesso',
                'novo_cargo' => $novo_cargo
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Erro ao atualizar no banco de dados: ' . mysqli_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Profissional não encontrado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Dados não enviados'
    ]);
}
?>