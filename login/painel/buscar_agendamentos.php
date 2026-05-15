<?php
include 'conn.php';

if (isset($_POST['profissional_id'])) {
    $profissional_id = intval($_POST['profissional_id']);

    $stmt = $conn->prepare("SELECT * FROM agenda_padrao WHERE id_profissional = ? ORDER BY CASE WHEN dia = 'segunda' THEN 1 WHEN dia = 'terca' THEN 2 WHEN dia = 'quarta' THEN 3 WHEN dia = 'quinta' THEN 4 WHEN dia = 'sexta' THEN 5 WHEN dia = 'sabado' THEN 6 WHEN dia = 'domingo' THEN 7 END, horario");
    $stmt->bind_param("i", $profissional_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Dia</th><th>Horário</th><th>Ação</th></tr></thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
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

mysqli_close($conn);
?>
