<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/../auth_guard.php';
include '../conn.php';
include '../funcoes.php';
include 'editacodigo.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$login_sess = $_SESSION['login'];
$acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

$stmt_user = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_user->bind_param("s", $login_sess);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$stmt_user->close();
if (!$res_user || $res_user->num_rows === 0) {
    echo json_encode(['erro' => 'Usuário não encontrado']); exit;
}
$user_data   = $res_user->fetch_assoc();
$tipo_user   = $user_data['tipo'];
$usuario_api = $user_data['usuario_api'];

$is_admin = ($tipo_user === '1');

$sql_cfg = "SELECT * FROM config LIMIT 1";
$res_cfg = mysqli_query($conn, $sql_cfg);
$cfg = mysqli_fetch_assoc($res_cfg);
$servidor = preg_replace('#^https?://#i', '', trim($cfg['ip_vps'] ?? ''));
$porta    = $cfg['porta'] ?? 443;
$token_bd = $cfg['chave'] ?? '';

/**
 * Verifica acesso ESTRITO a uma conversa: apenas admin ou atendente_atual.
 * Usado em: responder, encerrar, devolver_ia, transferir_depto.
 */
function verificar_acesso_estrito(array $cli, string $login_sess, bool $is_admin): bool
{
    if ($is_admin) return true;
    return (!empty($cli['atendente_atual']) && $cli['atendente_atual'] === $login_sess);
}

/**
 * Verifica acesso para HISTÓRICO:
 * - Admin: acesso total
 * - Atendente atual da conversa: acesso total
 * - Atendente vinculado ao departamento E conversa está em 'fila': pode ler para decidir se assume
 */
function verificar_acesso_historico(mysqli $conn, array $cli, string $login_sess, string $usuario_api, bool $is_admin): bool
{
    if ($is_admin) return true;

    // Atendente atual
    if (!empty($cli['atendente_atual']) && $cli['atendente_atual'] === $login_sess) return true;

    // Conversa em fila: atendente vinculado ao depto pode ler histórico para decidir se assume
    if ($cli['modo_atendimento'] === 'fila') {
        $depto = (int)($cli['depto_atual'] ?? 0);
        if ($depto > 0) {
            $s = $conn->prepare("SELECT id FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=? LIMIT 1");
            $s->bind_param("sis", $login_sess, $depto, $usuario_api);
            $s->execute();
            $r = $s->get_result();
            $s->close();
            if ($r && $r->num_rows > 0) return true;
        }
    }

    return false;
}

/**
 * Verifica acesso para ASSUMIR conversa:
 * Admin pode assumir qualquer conversa. Atendente pode assumir conversas em
 * estado 'fila' do seu departamento. Não pode "roubar" conversas humano de outros.
 */
function verificar_acesso_assumir(mysqli $conn, array $cli, string $login_sess, string $usuario_api, bool $is_admin): bool
{
    if ($is_admin) return true;

    // Já é o atendente_atual (reconectar a própria conversa)
    if (!empty($cli['atendente_atual']) && $cli['atendente_atual'] === $login_sess) return true;

    // Só pode assumir conversas em estado 'fila', não humano de outro atendente
    if ($cli['modo_atendimento'] !== 'fila') return false;

    // Verificar vínculo com o departamento
    $depto = (int)($cli['depto_atual'] ?? 0);
    if ($depto > 0) {
        $s = $conn->prepare("SELECT id FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=? LIMIT 1");
        $s->bind_param("sis", $login_sess, $depto, $usuario_api);
        $s->execute();
        $r = $s->get_result();
        $s->close();
        if ($r && $r->num_rows > 0) return true;
    }
    return false;
}

/**
 * Busca e valida cliente por ID dentro do tenant.
 */
function buscar_cliente(mysqli $conn, int $cliente_id, string $usuario_api): ?array
{
    $s = $conn->prepare("SELECT * FROM clientes WHERE id=? AND usuario_api=?");
    $s->bind_param("is", $cliente_id, $usuario_api);
    $s->execute();
    $r = $s->get_result();
    $s->close();
    return ($r && $r->num_rows > 0) ? $r->fetch_assoc() : null;
}

// ── assumir ──────────────────────────────────────────────────────────────────
if ($acao === 'assumir') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    if ($cliente_id <= 0) { echo json_encode(['erro' => 'ID inválido']); exit; }

    $cli = buscar_cliente($conn, $cliente_id, $usuario_api);
    if (!$cli) { echo json_encode(['erro' => 'Cliente não encontrado']); exit; }

    // Verificar acesso: apenas fila do depto ou já é o atendente_atual
    if (!verificar_acesso_assumir($conn, $cli, $login_sess, $usuario_api, $is_admin)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado: você não pode assumir esta conversa']);
        exit;
    }

    if ($cli['modo_atendimento'] === 'humano' && $cli['atendente_atual'] !== $login_sess) {
        echo json_encode(['erro' => 'Conversa já está sendo atendida por ' . htmlspecialchars($cli['atendente_atual'])]);
        exit;
    }

    // UPDATE atômico: só altera se ainda estiver em 'fila' (guard contra race condition).
    // Se já foi assumida por outro atendente entre o pre-check e este UPDATE, affected_rows = 0.
    $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='humano', atendente_atual=? WHERE id=? AND usuario_api=? AND modo_atendimento='fila'");
    $stmt_up->bind_param("sis", $login_sess, $cliente_id, $usuario_api);
    $stmt_up->execute();
    $rows_afetadas = $stmt_up->affected_rows;
    $stmt_up->close();

    if ($rows_afetadas === 0) {
        // Reconexão legítima: atendente_atual já é o mesmo (reconexão após reload)
        if (!empty($cli['atendente_atual']) && $cli['atendente_atual'] === $login_sess) {
            echo json_encode(['ok' => true, 'msg' => 'Conversa já é sua']);
        } else {
            // Assumida por outra pessoa entre o pre-check e o UPDATE
            $cli_atual = buscar_cliente($conn, $cliente_id, $usuario_api);
            $outro = $cli_atual ? htmlspecialchars($cli_atual['atendente_atual'] ?? '') : '(desconhecido)';
            echo json_encode(['erro' => 'Conversa foi assumida por outro atendente' . ($outro ? ': ' . $outro : '')]);
        }
        exit;
    }

    echo json_encode(['ok' => true, 'msg' => 'Conversa assumida com sucesso']);
    exit;
}

// ── responder ─────────────────────────────────────────────────────────────────
if ($acao === 'responder') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $mensagem   = trim($_POST['mensagem'] ?? '');
    if ($cliente_id <= 0 || $mensagem === '') { echo json_encode(['erro' => 'Dados inválidos']); exit; }

    $cli = buscar_cliente($conn, $cliente_id, $usuario_api);
    if (!$cli) { echo json_encode(['erro' => 'Cliente não encontrado']); exit; }

    // Apenas admin ou o atendente_atual pode responder
    if (!$is_admin && $cli['atendente_atual'] !== $login_sess) {
        http_response_code(403);
        echo json_encode(['erro' => 'Você não é o atendente desta conversa']);
        exit;
    }

    $telefone = $cli['telefone'];

    $stmt_ev = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
    $stmt_ev->bind_param("sss", $telefone, $mensagem, $usuario_api);
    $stmt_ev->execute();
    $id_msg = mysqli_insert_id($conn);
    $stmt_ev->close();

    enviarMensagem($servidor, $porta, $usuario_api, $token_bd, $telefone, $mensagem, $id_msg);

    $hora = date('Y-m-d H:i:s');
    $stmt_ih = $conn->prepare("INSERT INTO ia_historico (ia_msg, usuario_msg, telefone_usuario, usuario_api, login_historico, data_hora, tipo_remetente) VALUES (?, '', ?, ?, ?, ?, 'atendente')");
    $stmt_ih->bind_param("sssss", $mensagem, $telefone, $usuario_api, $login_sess, $hora);
    $stmt_ih->execute();
    $stmt_ih->close();

    $stmt_upd = $conn->prepare("UPDATE clientes SET time_atendimento=? WHERE id=? AND usuario_api=?");
    $stmt_upd->bind_param("sis", $hora, $cliente_id, $usuario_api);
    $stmt_upd->execute();
    $stmt_upd->close();

    echo json_encode(['ok' => true]);
    exit;
}

// ── encerrar / devolver_ia ────────────────────────────────────────────────────
if ($acao === 'encerrar' || $acao === 'devolver_ia') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    if ($cliente_id <= 0) { echo json_encode(['erro' => 'ID inválido']); exit; }

    $cli = buscar_cliente($conn, $cliente_id, $usuario_api);
    if (!$cli) { echo json_encode(['erro' => 'Cliente não encontrado']); exit; }

    // Apenas admin ou o atendente_atual pode encerrar (acesso estrito)
    if (!verificar_acesso_estrito($cli, $login_sess, $is_admin)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Você não é o atendente desta conversa']);
        exit;
    }

    $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='ia', atendente_atual=NULL, depto_atual=NULL WHERE id=? AND usuario_api=?");
    $stmt_up->bind_param("is", $cliente_id, $usuario_api);
    $stmt_up->execute();
    $stmt_up->close();

    echo json_encode(['ok' => true, 'msg' => 'Conversa devolvida para a IA']);
    exit;
}

// ── transferir_depto ──────────────────────────────────────────────────────────
if ($acao === 'transferir_depto') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $novo_depto = (int)($_POST['depto_id'] ?? 0);
    if ($cliente_id <= 0 || $novo_depto <= 0) { echo json_encode(['erro' => 'Dados inválidos']); exit; }

    $cli = buscar_cliente($conn, $cliente_id, $usuario_api);
    if (!$cli) { echo json_encode(['erro' => 'Cliente não encontrado']); exit; }

    // Apenas admin ou atendente_atual pode transferir (acesso estrito)
    if (!verificar_acesso_estrito($cli, $login_sess, $is_admin)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Você não é o atendente desta conversa']);
        exit;
    }

    // Verificar se o departamento destino pertence ao mesmo tenant
    $s_dep = $conn->prepare("SELECT id FROM departamentos WHERE id=? AND usuario_api=? AND ativo=1");
    $s_dep->bind_param("is", $novo_depto, $usuario_api);
    $s_dep->execute();
    $r_dep = $s_dep->get_result();
    $s_dep->close();
    if (!$r_dep || $r_dep->num_rows === 0) {
        echo json_encode(['erro' => 'Departamento de destino não encontrado']); exit;
    }

    $stmt_up = $conn->prepare("UPDATE clientes SET modo_atendimento='fila', depto_atual=?, atendente_atual=NULL WHERE id=? AND usuario_api=?");
    $stmt_up->bind_param("iis", $novo_depto, $cliente_id, $usuario_api);
    $stmt_up->execute();
    $stmt_up->close();

    echo json_encode(['ok' => true, 'msg' => 'Cliente transferido para outro departamento']);
    exit;
}

// ── listar_fila ───────────────────────────────────────────────────────────────
if ($acao === 'listar_fila') {
    $deptos = [];
    if ($is_admin) {
        $s = $conn->prepare("SELECT id FROM departamentos WHERE usuario_api=? AND ativo=1");
        $s->bind_param("s", $usuario_api);
        $s->execute();
        $r = $s->get_result();
        $s->close();
        while ($row = $r->fetch_assoc()) $deptos[] = $row['id'];
    } else {
        $s = $conn->prepare("SELECT depto_id FROM atendentes_depto WHERE login_atendente=? AND usuario_api=?");
        $s->bind_param("ss", $login_sess, $usuario_api);
        $s->execute();
        $r = $s->get_result();
        $s->close();
        while ($row = $r->fetch_assoc()) $deptos[] = $row['depto_id'];
    }

    $resultado = [];
    if (!empty($deptos)) {
        $in = implode(',', array_map('intval', $deptos));
        if ($is_admin) {
            // Admin vê tudo: fila e humano de todos os deptos
            $s = $conn->prepare("
                SELECT c.id, c.nome, c.telefone, c.modo_atendimento, c.depto_atual, c.atendente_atual,
                       c.time_atendimento, d.nome AS depto_nome
                FROM clientes c
                LEFT JOIN departamentos d ON d.id = c.depto_atual
                WHERE c.usuario_api=? AND c.depto_atual IN ($in)
                  AND c.modo_atendimento IN ('fila','humano')
                ORDER BY c.modo_atendimento ASC, c.time_atendimento ASC
            ");
            $s->bind_param("s", $usuario_api);
        } else {
            // Atendente vê: fila do depto (sem atendente pré-designado OU designado para mim) + meus humano
            $s = $conn->prepare("
                SELECT c.id, c.nome, c.telefone, c.modo_atendimento, c.depto_atual, c.atendente_atual,
                       c.time_atendimento, d.nome AS depto_nome
                FROM clientes c
                LEFT JOIN departamentos d ON d.id = c.depto_atual
                WHERE c.usuario_api=? AND c.depto_atual IN ($in)
                  AND (
                    (c.modo_atendimento = 'fila' AND (c.atendente_atual IS NULL OR c.atendente_atual = ?))
                    OR (c.modo_atendimento = 'humano' AND c.atendente_atual = ?)
                  )
                ORDER BY c.modo_atendimento ASC, c.time_atendimento ASC
            ");
            $s->bind_param("sss", $usuario_api, $login_sess, $login_sess);
        }
        $s->execute();
        $r = $s->get_result();
        $s->close();
        while ($row = $r->fetch_assoc()) $resultado[] = $row;
    }
    echo json_encode(['ok' => true, 'clientes' => $resultado]);
    exit;
}

// ── historico ─────────────────────────────────────────────────────────────────
if ($acao === 'historico') {
    $cliente_id = (int)($_POST['cliente_id'] ?? $_GET['cliente_id'] ?? 0);
    $desde_id   = (int)($_POST['desde_id']   ?? $_GET['desde_id']   ?? 0);
    if ($cliente_id <= 0) { echo json_encode(['erro' => 'ID inválido']); exit; }

    $cli = buscar_cliente($conn, $cliente_id, $usuario_api);
    if (!$cli) { echo json_encode(['erro' => 'Cliente não encontrado']); exit; }

    // Histórico: admin, atendente_atual, ou vinculado ao depto quando conversa está em fila
    if (!verificar_acesso_historico($conn, $cli, $login_sess, $usuario_api, $is_admin)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado a esta conversa']);
        exit;
    }

    $telefone = $cli['telefone'];
    $s = $conn->prepare("SELECT id, ia_msg, usuario_msg, data_hora, login_historico, tipo_remetente FROM ia_historico WHERE telefone_usuario=? AND usuario_api=? AND id > ? ORDER BY id ASC LIMIT 100");
    $s->bind_param("ssi", $telefone, $usuario_api, $desde_id);
    $s->execute();
    $r = $s->get_result();
    $s->close();

    $msgs = [];
    while ($row = $r->fetch_assoc()) $msgs[] = $row;

    echo json_encode([
        'ok'              => true,
        'msgs'            => $msgs,
        'modo'            => $cli['modo_atendimento'],
        'atendente_atual' => $cli['atendente_atual'],
        'nome_cliente'    => $cli['nome'],
        'telefone'        => $telefone,
    ]);
    exit;
}

// ── contagem_fila: retorna quantos clientes estão na fila dos deptos do atendente ─
if ($acao === 'contagem_fila') {
    $deptos_at = [];
    $s_dep = $conn->prepare("SELECT depto_id FROM atendentes_depto WHERE login_atendente=? AND usuario_api=? LIMIT 20");
    if ($s_dep) {
        $s_dep->bind_param("ss", $login_sess, $usuario_api);
        $s_dep->execute();
        $r_dep = $s_dep->get_result();
        $s_dep->close();
        while ($d = $r_dep->fetch_assoc()) $deptos_at[] = (int)$d['depto_id'];
    }
    $total_fila = 0;
    if (!empty($deptos_at)) {
        $in_ids = implode(',', $deptos_at);
        $rq = $conn->query("SELECT COUNT(*) AS n FROM clientes WHERE usuario_api='" . $conn->real_escape_string($usuario_api) . "' AND modo_atendimento='fila' AND depto_atual IN ($in_ids)");
        if ($rq) $total_fila = (int)$rq->fetch_assoc()['n'];
    }
    echo json_encode(['ok' => true, 'fila' => $total_fila]);
    exit;
}

echo json_encode(['erro' => 'Ação desconhecida: ' . htmlspecialchars($acao)]);
