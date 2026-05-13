<?php
include 'conn.php';

// Verificar se o ID do profissional foi enviado via POST
if (isset($_POST['profissional_id'])) {
    $profissional_id = $_POST['profissional_id'];

    // Consulta para buscar os agendamentos do profissional selecionado
    #$sql = "SELECT id, dia, horario FROM agenda_padrao WHERE profissional_id = '$profissional_id'";
    #
    #
    #
    #
    #$sql = "SELECT * FROM agenda_padrao WHERE id_profissional = '$profissional_id'";
$sql = "
    SELECT * 
    FROM agenda_padrao 
    WHERE id_profissional = '$profissional_id'
    ORDER BY 
        CASE 
            WHEN dia = 'segunda' THEN 1
            WHEN dia = 'terca' THEN 2
            WHEN dia = 'quarta' THEN 3
            WHEN dia = 'quinta' THEN 4
            WHEN dia = 'sexta' THEN 5
            WHEN dia = 'sabado' THEN 6
            WHEN dia = 'domingo' THEN 7
        END,
        horario
";
    $result = mysqli_query($conn, $sql);

    // Verificar se há resultados
    if (mysqli_num_rows($result) > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Dia</th><th>Horário</th><th>Ação</th></tr></thead>';
        echo '<tbody>';
        
        // Exibir os resultados em uma tabela
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['dia']}</td>";
            echo "<td>{$row['horario']}</td>";
            echo "<td><button class='btn btn-danger btn-sm' onclick='deletarAgendamento({$row['id']})'>Deletar</button></td>";
            echo "</tr>";
        }
        
        echo '</tbody></table>';
    } else {
        echo "<p>Nenhum agendamento encontrado para este profissional.</p>";
    }
}

// Fechar a conexão
mysqli_close($conn);
?>
