<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
session_start();
require_once 'conn.php';
require_once 'funcoes.php';

// --- Funções auxiliares ----------------------------------

/**
 * Detecta se a requisição é AJAX (fetch/XHR).
 */
function isAjax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Retorna um array em JSON e encerra a execução.
 */
function returnJson(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Trata erro: se for AJAX, retorna JSON; senão, redireciona.
 */
function returnError(string $msg): void
{
    if (isAjax()) {
        returnJson(['status' => 'error', 'msg' => $msg]);
    } else {
        VaiPara('cadastrar_horario.php?status=error&msg=' . urlencode($msg));
    }
}

// ---------------------------------------------------------

// 1) Verifica se usuário está logado
if (!isset($_SESSION['login'])) {
    if (isAjax()) {
        returnJson(['status' => 'error', 'msg' => 'Usuário não autenticado']);
    } else {
        header('Location: login.php?erro=2');
    }
    exit();
}
$login = $_SESSION['login'];

// 2) Verifica método e parâmetro obrigatório
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['horario_id'])) {
    returnError('invalid_request');
}

// 3) Sanitização dos dados
$horario_id     = (int) $_POST['horario_id'];
$entrada        = !empty($_POST['entrada'])        ? $_POST['entrada']        : null;
$almoco_inicio  = !empty($_POST['almoco_inicio'])  ? $_POST['almoco_inicio']  : null;
$almoco_fim     = !empty($_POST['almoco_fim'])     ? $_POST['almoco_fim']     : null;
$saida          = !empty($_POST['saida'])          ? $_POST['saida']          : null;
$ativo          = isset($_POST['ativo']) ? 1 : 0;

// 4) Inicia transação
mysqli_begin_transaction($conn);

try {
    // 4.1) Atualiza a tabela principal
    $sql = "
        UPDATE horarios_profissional
        SET hora_entrada  = ?,
            almoco_inicio = ?,
            almoco_fim    = ?,
            hora_saida    = ?,
            ativo         = ?
        WHERE id = ?
          AND profissional_id IN (
              SELECT id
                FROM profissional
               WHERE login = ?
          )
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssssisi',
        $entrada,
        $almoco_inicio,
        $almoco_fim,
        $saida,
        $ativo,
        $horario_id,
        $login
    );
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Erro ao atualizar horário: ' . mysqli_stmt_error($stmt));
    }

    // 4.2) Exclui os intervalos antigos
    $sql = "DELETE FROM intervalos_profissional WHERE horario_id = ? AND login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $horario_id, $login);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Erro ao limpar intervalos antigos: ' . mysqli_stmt_error($stmt));
    }

    // 4.3) Insere os novos intervalos (se houver)
    if (!empty($_POST['intervalos_inicio']) && is_array($_POST['intervalos_inicio'])) {
        $sql = "
            INSERT INTO intervalos_profissional
                (horario_id, intervalo_inicio, intervalo_fim, motivo, login)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = mysqli_prepare($conn, $sql);

        foreach ($_POST['intervalos_inicio'] as $i => $inicio) {
            $fim    = $_POST['intervalos_fim'][$i]    ?? null;
            $motivo = $_POST['intervalos_motivo'][$i] ?? null;

            if ($inicio && $fim) {
                mysqli_stmt_bind_param($stmt, 'issss', $horario_id, $inicio, $fim, $motivo, $login);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Erro ao inserir intervalo: ' . mysqli_stmt_error($stmt));
                }
            }
        }
    }

    // 5) Commit da transação
    mysqli_commit($conn);

    // 6) Resposta de sucesso
    if (isAjax()) {
        returnJson(['status' => 'success', 'msg' => 'Horário atualizado com sucesso']);
    } else {
        VaiPara('cadastrar_horario.php?status=success&msg=horario_atualizado');
        exit();
    }

} catch (Exception $e) {
    // 7) Rollback em caso de erro
    mysqli_rollback($conn);
    returnError($e->getMessage());
} finally {
    mysqli_close($conn);
}
