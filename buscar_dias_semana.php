<?php
// buscar_dias_semana.php

// Função para conectar ao banco de dados
function conectarDB() {
include 'login/painel/conn.php';
    return $conn;
}

function ç($texto) {
    // Substitui a letra "c" pelo "ç"
    return str_replace('c', 'ç', $texto);
}



// Recebe o id do profissional via POST
if (isset($_POST['profissional_id'])) {
    $id_profissional = intval($_POST['profissional_id']);

    $conn = conectarDB();

    // Obter os dias da semana que o profissional trabalha
    $sql = "SELECT DISTINCT dia FROM agenda_padrao WHERE id_profissional = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_profissional);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $dias_semana = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dias_semana[] = $row['dia'];
    }

    if (!empty($dias_semana)) {
        echo '<option value="">Escolha um dia da semana</option>';
        foreach ($dias_semana as $dia) {
            echo '<option value="' . $dia . '">' . ucfirst(ç($dia)) . '</option>';
        }
    } else {
        echo '<option value="">Nenhum dia disponível</option>';
    }

    mysqli_close($conn);
} else {
    echo '<option value="">Escolha um dia da semana</option>';
}
?>
