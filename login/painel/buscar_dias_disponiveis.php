<?php
include 'conn.php';
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Verificar se o ID do profissional e o dia foram enviados via POST
if (isset($_POST['profissional_id']) && isset($_POST['dia'])) {
    $profissional_id = $_POST['profissional_id'];
    $dia = $_POST['dia'];

    // Consulta para buscar os horários disponíveis do profissional para o dia selecion

    $sql = "SELECT * FROM agenda_padrao WHERE id_profissional = '$profissional_id' AND dia = '$dia' AND horario NOT IN (SELECT horario FROM agendamentos WHERE profissional_id = '$profissional_id' AND dia = '$dia')";
    $result = mysqli_query($conn, $sql);

    echo '<option value="">Escolha um horário</option>';

    // Preencher os horários disponíveis
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['horario']}'>{$row['horario']}</option>";
    }
}

// Fechar a conexão
?>
