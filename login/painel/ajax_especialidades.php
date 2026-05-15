<?php
include 'conn.php';

// ajax_especialidades.php
// Assumindo que já existe conexão com o banco ($conn) e sessão iniciada com $login

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'];
    
    switch($acao) {
        case 'listar':
            $profissional_id = intval($_POST['profissional_id']);
            
            $sql = "SELECT ep.*, p.profissional_nome 
                    FROM especialidade_profissional ep
                    JOIN profissional p ON ep.profissional_id = p.id
                    WHERE ep.profissional_id = '$profissional_id' 
                    AND ep.login = '$login'";
            
            $query = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($query) > 0) {
                echo '<div class="list-group">';
                while($row = mysqli_fetch_array($query)) {
                    echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    echo $row['especialidade'];
                    echo '<button class="btn btn-danger btn-sm" onclick="removerEspecialidade('.$row['id'].')">Remover</button>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-muted">Nenhuma especialidade cadastrada</p>';
            }
            break;
            
        case 'adicionar':
            $profissional_id = intval($_POST['profissional_id']);
            $especialidade = mysqli_real_escape_string($conn, $_POST['especialidade']);
            
            // Verifica se o profissional pertence ao usuário logado
            $sql_verifica = "SELECT id FROM profissional WHERE id = '$profissional_id' AND login = '$login'";
            $query_verifica = mysqli_query($conn, $sql_verifica);
            
            if(mysqli_num_rows($query_verifica) > 0) {
                $sql = "INSERT INTO especialidade_profissional (login, profissional_id, especialidade) 
                        VALUES ('$login', '$profissional_id', '$especialidade')";
                
                if(mysqli_query($conn, $sql)) {
                    echo 'sucesso';
                } else {
                    echo 'erro';
                }
            } else {
                echo 'erro';
            }
            break;
            
        case 'remover':
            $especialidade_id = intval($_POST['especialidade_id']);
            
            // Verifica se a especialidade pertence ao usuário logado
            $sql = "DELETE FROM especialidade_profissional 
                    WHERE id = '$especialidade_id' AND login = '$login'";
            
            if(mysqli_query($conn, $sql)) {
                echo 'sucesso';
            } else {
                echo 'erro';
            }
            break;
    }
}
?>