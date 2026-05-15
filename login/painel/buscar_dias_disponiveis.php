<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

if (isset($_POST['profissional_id']) && isset($_POST['dia'])) {
    $profissional_id = intval($_POST['profissional_id']);
    $dia = $_POST['dia'];

    $stmt = $conn->prepare("SELECT * FROM agenda_padrao WHERE id_profissional = ? AND dia = ? AND horario NOT IN (SELECT horario FROM agendamentos WHERE profissional_id = ? AND dia = ?)");
    $stmt->bind_param("isis", $profissional_id, $dia, $profissional_id, $dia);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    echo '<option value="">Escolha um horário</option>';

    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['horario']}'>{$row['horario']}</option>";
    }
}
?>
