<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';

header('Content-Type: application/json');

if ($_POST) {
    $especialidade_para_remover = trim($_POST['especialidade']);
    $profissional_id = intval($_POST['profissional_id']);

    $stmt_buscar = mysqli_prepare($conn, "SELECT profissional_cargo FROM profissional WHERE id = ?");
    mysqli_stmt_bind_param($stmt_buscar, "i", $profissional_id);
    mysqli_stmt_execute($stmt_buscar);
    $query_buscar = mysqli_stmt_get_result($stmt_buscar);

    if (mysqli_num_rows($query_buscar) > 0) {
        $row = mysqli_fetch_array($query_buscar);
        $cargo_atual = $row['profissional_cargo'];

        $especialidades = explode(',', $cargo_atual);

        $novas_especialidades = array();
        foreach ($especialidades as $esp) {
            $esp_limpa = trim($esp);
            if (!empty($esp_limpa) && trim(strtolower($esp_limpa)) != trim(strtolower($especialidade_para_remover))) {
                $novas_especialidades[] = $esp_limpa;
            }
        }

        $novo_cargo = implode(', ', $novas_especialidades);

        $stmt_atualizar = mysqli_prepare($conn, "UPDATE profissional SET profissional_cargo = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_atualizar, "si", $novo_cargo, $profissional_id);

        if (mysqli_stmt_execute($stmt_atualizar)) {
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
