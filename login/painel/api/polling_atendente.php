<?php
/**
 * polling_atendente.php
 * Endpoint leve chamado a cada 2-3 s pelo painel do atendente.
 * Retorna novas mensagens de uma conversa e/ou contagem da fila.
 *
 * GET/POST params:
 *   acao        = 'msgs'  | 'fila'  | 'status_conversa'
 *   cliente_id  = (int)   — para acao=msgs e status_conversa
 *   desde_id    = (int)   — ID do último ia_historico já recebido (default 0)
 */

$auth_ajax_mode = true;
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../conn.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$login_sess = $_SESSION['login'];
$acao       = trim($_REQUEST['acao']       ?? 'msgs');
$cliente_id = (int)($_REQUEST['cliente_id'] ?? 0);
$desde_id   = (int)($_REQUEST['desde_id']  ?? 0);

// Carrega dados do usuário logado (mínimo necessário)
$s = $conn->prepare("SELECT tipo, usuario_api FROM login WHERE login = ? LIMIT 1");
$s->bind_param("s", $login_sess);
$s->execute();
$r = $s->get_result();
$s->close();
if (!$r || $r->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário inválido']);
    exit;
}
$u           = $r->fetch_assoc();
$tipo_user   = $u['tipo'];
$usuario_api = $u['usuario_api'];
$is_admin    = ($tipo_user === '1');

// ── acao=msgs : novas mensagens de uma conversa ─────────────────────────────
if ($acao === 'msgs') {
    if ($cliente_id <= 0) {
        echo json_encode(['erro' => 'cliente_id obrigatório']);
        exit;
    }

    // Verifica acesso: admin, atendente_atual, ou vinculado ao depto (conversa em fila)
    $sc = $conn->prepare("SELECT telefone, modo_atendimento, atendente_atual, depto_atual, nome FROM clientes WHERE id=? AND usuario_api=? LIMIT 1");
    $sc->bind_param("is", $cliente_id, $usuario_api);
    $sc->execute();
    $rc = $sc->get_result();
    $sc->close();
    if (!$rc || $rc->num_rows === 0) {
        echo json_encode(['erro' => 'Cliente não encontrado']);
        exit;
    }
    $cli      = $rc->fetch_assoc();
    $telefone = $cli['telefone'];
    $modo     = $cli['modo_atendimento'];
    $at_atual = $cli['atendente_atual'];

    // Controle de acesso
    $pode = false;
    if ($is_admin) {
        $pode = true;
    } elseif (!empty($at_atual) && $at_atual === $login_sess) {
        $pode = true;
    } elseif ($modo === 'fila' && !empty($cli['depto_atual'])) {
        // Atendente vinculado ao departamento pode ver a fila para decidir se assume
        $sd = $conn->prepare("SELECT id FROM atendentes_depto WHERE login_atendente=? AND depto_id=? AND usuario_api=? LIMIT 1");
        $sd->bind_param("sis", $login_sess, $cli['depto_atual'], $usuario_api);
        $sd->execute();
        $rd = $sd->get_result();
        $sd->close();
        if ($rd && $rd->num_rows > 0) $pode = true;
    }

    if (!$pode) {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado']);
        exit;
    }

    // Busca novas mensagens
    $sm = $conn->prepare(
        "SELECT id, ia_msg, usuario_msg, data_hora, login_historico, tipo_remetente
         FROM ia_historico
         WHERE telefone_usuario=? AND usuario_api=? AND id > ?
         ORDER BY id ASC LIMIT 80"
    );
    $sm->bind_param("ssi", $telefone, $usuario_api, $desde_id);
    $sm->execute();
    $rm = $sm->get_result();
    $sm->close();

    $msgs = [];
    while ($row = $rm->fetch_assoc()) $msgs[] = $row;

    echo json_encode([
        'ok'              => true,
        'msgs'            => $msgs,
        'modo'            => $modo,
        'atendente_atual' => $at_atual,
        'nome_cliente'    => $cli['nome'],
        'telefone'        => $telefone,
    ]);
    exit;
}

// ── acao=fila : contagem de conversas na fila dos deptos do atendente ─────────
if ($acao === 'fila') {
    $deptos = [];

    if ($is_admin) {
        // Admin vê todos os deptos ativos da conta
        $sd = $conn->prepare("SELECT id FROM departamentos WHERE usuario_api=? AND ativo=1");
        $sd->bind_param("s", $usuario_api);
        $sd->execute();
        $rd = $sd->get_result();
        $sd->close();
        while ($d = $rd->fetch_assoc()) $deptos[] = (int)$d['id'];
    } else {
        $sd = $conn->prepare("SELECT depto_id FROM atendentes_depto WHERE login_atendente=? AND usuario_api=? LIMIT 20");
        $sd->bind_param("ss", $login_sess, $usuario_api);
        $sd->execute();
        $rd = $sd->get_result();
        $sd->close();
        while ($d = $rd->fetch_assoc()) $deptos[] = (int)$d['depto_id'];
    }

    $total_fila  = 0;
    $meu_total   = 0;

    if (!empty($deptos)) {
        $in_ids      = implode(',', $deptos);
        $esc_api     = $conn->real_escape_string($usuario_api);
        $esc_login   = $conn->real_escape_string($login_sess);

        $rq = $conn->query("SELECT COUNT(*) AS n FROM clientes WHERE usuario_api='$esc_api' AND modo_atendimento='fila' AND depto_atual IN ($in_ids)");
        if ($rq) $total_fila = (int)$rq->fetch_assoc()['n'];

        $rq2 = $conn->query("SELECT COUNT(*) AS n FROM clientes WHERE usuario_api='$esc_api' AND modo_atendimento='humano' AND atendente_atual='$esc_login'");
        if ($rq2) $meu_total = (int)$rq2->fetch_assoc()['n'];
    }

    echo json_encode([
        'ok'         => true,
        'fila'       => $total_fila,
        'meu_total'  => $meu_total,
    ]);
    exit;
}

// ── acao=status_conversa : verifica mudança de status sem puxar msgs ──────────
if ($acao === 'status_conversa') {
    if ($cliente_id <= 0) {
        echo json_encode(['erro' => 'cliente_id obrigatório']);
        exit;
    }
    $sc = $conn->prepare("SELECT modo_atendimento, atendente_atual FROM clientes WHERE id=? AND usuario_api=? LIMIT 1");
    $sc->bind_param("is", $cliente_id, $usuario_api);
    $sc->execute();
    $rc = $sc->get_result();
    $sc->close();
    if (!$rc || $rc->num_rows === 0) {
        echo json_encode(['erro' => 'Cliente não encontrado']);
        exit;
    }
    $cli = $rc->fetch_assoc();
    echo json_encode([
        'ok'              => true,
        'modo'            => $cli['modo_atendimento'],
        'atendente_atual' => $cli['atendente_atual'],
    ]);
    exit;
}

echo json_encode(['erro' => 'Ação desconhecida: ' . htmlspecialchars($acao)]);
