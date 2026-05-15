<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
$login = $_SESSION['login'];
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profissional_id']) && isset($_POST['especialidade'])) {

    $profissional_id = intval($_POST['profissional_id']);
    $nova_especialidade = trim($_POST['especialidade']);

    $stmt_busca = mysqli_prepare($conn, "SELECT profissional_cargo FROM profissional WHERE id = ? AND login = ?");
    mysqli_stmt_bind_param($stmt_busca, "is", $profissional_id, $login);
    mysqli_stmt_execute($stmt_busca);
    $query_busca = mysqli_stmt_get_result($stmt_busca);

    if (mysqli_num_rows($query_busca) > 0) {
        $row = mysqli_fetch_array($query_busca);
        $especialidades_atuais = $row['profissional_cargo'];

        $especialidades_array = array_map('trim', explode(',', $especialidades_atuais));

        if (in_array($nova_especialidade, $especialidades_array)) {
            echo 'Profissional já possui esta especialidade';
        } else {
            if (empty($especialidades_atuais)) {
                $novas_especialidades = $nova_especialidade;
            } else {
                $novas_especialidades = $especialidades_atuais . ', ' . $nova_especialidade;
            }

            $stmt_update = mysqli_prepare($conn, "UPDATE profissional SET profissional_cargo = ? WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_update, "sis", $novas_especialidades, $profissional_id, $login);

            if (mysqli_stmt_execute($stmt_update)) {
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
