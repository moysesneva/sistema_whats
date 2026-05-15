<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'conn.php';
$login = isset($_SESSION['login']) ? $_SESSION['login'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'];

    switch ($acao) {
        case 'listar':
            $profissional_id = intval($_POST['profissional_id']);

            $stmt = mysqli_prepare($conn,
                "SELECT ep.*, p.profissional_nome
                 FROM especialidade_profissional ep
                 JOIN profissional p ON ep.profissional_id = p.id
                 WHERE ep.profissional_id = ? AND ep.login = ?");
            mysqli_stmt_bind_param($stmt, "is", $profissional_id, $login);
            mysqli_stmt_execute($stmt);
            $query = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($query) > 0) {
                echo '<div class="list-group">';
                while ($row = mysqli_fetch_array($query)) {
                    echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    echo htmlspecialchars($row['especialidade'], ENT_QUOTES, 'UTF-8');
                    echo '<button class="btn btn-danger btn-sm" onclick="removerEspecialidade(' . $row['id'] . ')">Remover</button>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-muted">Nenhuma especialidade cadastrada</p>';
            }
            break;

        case 'adicionar':
            $profissional_id = intval($_POST['profissional_id']);
            $especialidade = trim($_POST['especialidade']);

            $stmt_verifica = mysqli_prepare($conn, "SELECT id FROM profissional WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_verifica, "is", $profissional_id, $login);
            mysqli_stmt_execute($stmt_verifica);
            $query_verifica = mysqli_stmt_get_result($stmt_verifica);

            if (mysqli_num_rows($query_verifica) > 0) {
                $stmt_insert = mysqli_prepare($conn,
                    "INSERT INTO especialidade_profissional (login, profissional_id, especialidade)
                     VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "sis", $login, $profissional_id, $especialidade);

                if (mysqli_stmt_execute($stmt_insert)) {
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

            $stmt_delete = mysqli_prepare($conn,
                "DELETE FROM especialidade_profissional WHERE id = ? AND login = ?");
            mysqli_stmt_bind_param($stmt_delete, "is", $especialidade_id, $login);

            if (mysqli_stmt_execute($stmt_delete)) {
                echo 'sucesso';
            } else {
                echo 'erro';
            }
            break;
    }
}
?>
