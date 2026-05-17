<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';


function somenteNumeros($texto) {
    return preg_replace('/\D/', '', $texto);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nomeProfissional        = trim($_POST['nomeProfissional'] ?? '');
    $codigoPais              = trim($_POST['codigoPais'] ?? '');
    $telefoneProfissional    = trim($_POST['telefoneProfissional'] ?? '');
    $especialidadeProfissional = trim($_POST['especialidadeProfissional'] ?? '');

    $telefoneLimpo = somenteNumeros($codigoPais . $telefoneProfissional);

    $usuario_api = 'agenda_' . $login;

    mysqli_begin_transaction($conn);

    try {
        $stmt_prof = mysqli_prepare($conn,
            "INSERT INTO profissional (usuario_api, login, profissional_nome, profissional_cargo, telefone, codigo_pais)
             VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_prof, "ssssss",
            $usuario_api, $login, $nomeProfissional, $especialidadeProfissional, $telefoneLimpo, $codigoPais);

        if (!mysqli_stmt_execute($stmt_prof)) {
            throw new Exception("Erro ao inserir profissional: " . mysqli_error($conn));
        }

        $senha_padrao = '123456';
        $tipo = 5;

        $stmt_check = mysqli_prepare($conn, "SELECT id FROM login WHERE login = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $telefoneLimpo);
        mysqli_stmt_execute($stmt_check);
        $res_check = mysqli_stmt_get_result($stmt_check);
        $total = mysqli_num_rows($res_check);

        if ($total == 0) {
            $stmt_login = mysqli_prepare($conn,
                "INSERT INTO login (login, senha, tipo, perfil_img, usuario_api, nome, autorizado, modo_atuante)
                 VALUES (?, ?, ?, 'img/perfil.png', ?, ?, 2, 'prof')");
            mysqli_stmt_bind_param($stmt_login, "sissss",
                $telefoneLimpo, $senha_padrao, $tipo, $usuario_api, $nomeProfissional);

            if (!mysqli_stmt_execute($stmt_login)) {
                throw new Exception("Erro ao inserir login: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);

        echo "<script nonce=\"". ($GLOBALS['csp_nonce'] ?? '') ."\">
                alert('Profissional cadastrado com sucesso!');
                window.location.href = 'listar_profissionais.php';
              </script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);

        echo "<script nonce=\"". ($GLOBALS['csp_nonce'] ?? '') ."\">
                alert('Erro ao cadastrar profissional: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
}
?>
