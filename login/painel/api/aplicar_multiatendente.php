<?php
/**
 * aplicar_multiatendente.php
 * Aplica o schema do Multi Atendente no banco ao vivo (Hostinger).
 * Execução única — segura para rodar múltiplas vezes (idempotente).
 * Acesso: somente admin autenticado (tipo=1).
 */
require_once __DIR__ . '/../error_config.php';
require_once __DIR__ . '/../conn.php';

// ── Auth: só admin ─────────────────────────────────────────────────────────────
session_start();
if (empty($_SESSION['login'])) {
    http_response_code(403);
    die(json_encode(['erro' => 'Não autenticado']));
}
$s = $conn->prepare("SELECT tipo FROM login WHERE login=? LIMIT 1");
$s->bind_param("s", $_SESSION['login']);
$s->execute();
$r = $s->get_result()->fetch_assoc();
$s->close();
if (!$r || $r['tipo'] !== '1') {
    http_response_code(403);
    die(json_encode(['erro' => 'Acesso negado']));
}

header('Content-Type: text/html; charset=utf-8');

$db = $conn->query("SELECT DATABASE() AS d")->fetch_assoc()['d'];
$resultados = [];
$erros = 0;

function run(mysqli $c, string $label, string $sql): string {
    global $erros;
    try {
        if ($c->query($sql) !== false) {
            return "<tr><td>✅</td><td>" . htmlspecialchars($label) . "</td><td class='text-success'>OK (affected: {$c->affected_rows})</td></tr>";
        } else {
            $erros++;
            return "<tr><td>❌</td><td>" . htmlspecialchars($label) . "</td><td class='text-danger'>" . htmlspecialchars($c->error) . "</td></tr>";
        }
    } catch (Throwable $e) {
        $erros++;
        return "<tr><td>❌</td><td>" . htmlspecialchars($label) . "</td><td class='text-danger'>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
}

function colExists(mysqli $c, string $table, string $col): bool {
    $r = $c->query("SELECT COUNT(*) AS n FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$table' AND COLUMN_NAME='$col'");
    return $r ? (int)$r->fetch_assoc()['n'] > 0 : false;
}

function tableExists(mysqli $c, string $table): bool {
    $r = $c->query("SHOW TABLES LIKE '$table'");
    return $r && $r->num_rows > 0;
}

$out = '';

// ── 1. Tabela departamentos ────────────────────────────────────────────────────
$out .= run($conn, 'CREATE TABLE departamentos', "
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id`               int(11)      NOT NULL AUTO_INCREMENT,
  `usuario_api`      varchar(255) NOT NULL DEFAULT '',
  `nome`             varchar(255) NOT NULL DEFAULT '',
  `descricao`        text         DEFAULT NULL,
  `palavras_chave`   text         DEFAULT NULL,
  `msg_transferencia` text        DEFAULT NULL,
  `ativo`            tinyint(1)   NOT NULL DEFAULT 1,
  `criado_em`        timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ── 2. Tabela atendentes_depto ─────────────────────────────────────────────────
$out .= run($conn, 'CREATE TABLE atendentes_depto', "
CREATE TABLE IF NOT EXISTS `atendentes_depto` (
  `id`               int(11)      NOT NULL AUTO_INCREMENT,
  `login_atendente`  varchar(255) NOT NULL DEFAULT '',
  `depto_id`         int(11)      NOT NULL,
  `usuario_api`      varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_atendente_depto` (`login_atendente`, `depto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ── 3. Coluna modo_atendimento em clientes ────────────────────────────────────
if (!colExists($conn, 'clientes', 'modo_atendimento')) {
    $out .= run($conn, 'ALTER clientes ADD modo_atendimento', "ALTER TABLE `clientes` ADD COLUMN `modo_atendimento` VARCHAR(20) NOT NULL DEFAULT 'ia'");
} else {
    $out .= "<tr><td>⏭️</td><td>modo_atendimento (clientes)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 4. Coluna depto_atual em clientes ─────────────────────────────────────────
if (!colExists($conn, 'clientes', 'depto_atual')) {
    $out .= run($conn, 'ALTER clientes ADD depto_atual', "ALTER TABLE `clientes` ADD COLUMN `depto_atual` INT(11) DEFAULT NULL");
} else {
    $out .= "<tr><td>⏭️</td><td>depto_atual (clientes)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 5. Coluna atendente_atual em clientes ─────────────────────────────────────
if (!colExists($conn, 'clientes', 'atendente_atual')) {
    $out .= run($conn, 'ALTER clientes ADD atendente_atual', "ALTER TABLE `clientes` ADD COLUMN `atendente_atual` VARCHAR(255) DEFAULT NULL");
} else {
    $out .= "<tr><td>⏭️</td><td>atendente_atual (clientes)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 6. Coluna depto_pendente em clientes ──────────────────────────────────────
if (!colExists($conn, 'clientes', 'depto_pendente')) {
    $out .= run($conn, 'ALTER clientes ADD depto_pendente', "ALTER TABLE `clientes` ADD COLUMN `depto_pendente` INT(11) DEFAULT NULL");
} else {
    $out .= "<tr><td>⏭️</td><td>depto_pendente (clientes)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 7. Coluna login_historico em ia_historico ─────────────────────────────────
if (!colExists($conn, 'ia_historico', 'login_historico')) {
    $out .= run($conn, 'ALTER ia_historico ADD login_historico', "ALTER TABLE `ia_historico` ADD COLUMN `login_historico` VARCHAR(255) NOT NULL DEFAULT ''");
} else {
    $out .= "<tr><td>⏭️</td><td>login_historico (ia_historico)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 8. Coluna tipo_remetente em ia_historico ──────────────────────────────────
if (!colExists($conn, 'ia_historico', 'tipo_remetente')) {
    $out .= run($conn, 'ALTER ia_historico ADD tipo_remetente', "ALTER TABLE `ia_historico` ADD COLUMN `tipo_remetente` VARCHAR(20) NOT NULL DEFAULT 'ia'");
} else {
    $out .= "<tr><td>⏭️</td><td>tipo_remetente (ia_historico)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 9. Coluna telefone_notif em login ─────────────────────────────────────────
if (!colExists($conn, 'login', 'telefone_notif')) {
    $out .= run($conn, 'ALTER login ADD telefone_notif', "ALTER TABLE `login` ADD COLUMN `telefone_notif` VARCHAR(20) NOT NULL DEFAULT ''");
} else {
    $out .= "<tr><td>⏭️</td><td>telefone_notif (login)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 10. Coluna notificar_atendentes em departamentos ──────────────────────────
if (!colExists($conn, 'departamentos', 'notificar_atendentes')) {
    $out .= run($conn, 'ALTER departamentos ADD notificar_atendentes', "ALTER TABLE `departamentos` ADD COLUMN `notificar_atendentes` TINYINT(1) NOT NULL DEFAULT 1");
} else {
    $out .= "<tr><td>⏭️</td><td>notificar_atendentes (departamentos)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 11. Coluna proximo_atendente em departamentos ─────────────────────────────
if (!colExists($conn, 'departamentos', 'proximo_atendente')) {
    $out .= run($conn, 'ALTER departamentos ADD proximo_atendente', "ALTER TABLE `departamentos` ADD COLUMN `proximo_atendente` VARCHAR(100) DEFAULT NULL");
} else {
    $out .= "<tr><td>⏭️</td><td>proximo_atendente (departamentos)</td><td class='text-muted'>já existe</td></tr>";
}

// ── 12. Menu: atendentes.php para tipo=2 (MultiAtendente) ─────────────────────
$out .= run($conn, "Menu: Minhas Conversas (tipo=2)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Minhas Conversas', 'atendentes.php', '2', '1.0', 'feather icon-message-circle', 'MultiAtendente'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='atendentes.php' AND `tipo`='2')
");

// ── 13. Menu: Sair para tipo=2 MultiAtendente ─────────────────────────────────
$out .= run($conn, "Menu: Sair (tipo=2, MultiAtendente)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Sair', 'sair.php', '2', '9.0', 'feather icon-log-out', 'MultiAtendente'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='sair.php' AND `funcao` LIKE '%MultiAtendente%')
");

// ── 14. Menu: atendentes.php para tipo=3 ──────────────────────────────────────
$out .= run($conn, "Menu: Minhas Conversas (tipo=3)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Minhas Conversas', 'atendentes.php', '3', '1.0', 'feather icon-message-circle', 'MultiAtendente'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='atendentes.php' AND `tipo`='3')
");

// ── 15. Menu: Sair para tipo=3 ────────────────────────────────────────────────
$out .= run($conn, "Menu: Sair (tipo=3)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Sair', 'sair.php', '3', '9.0', 'feather icon-log-out', 'adm,Agendamento,Atendimento,prof,MultiAtendente'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='sair.php' AND `tipo`='3')
");

// ── 16. Menu: Departamentos para tipo=1 (admin) ───────────────────────────────
$out .= run($conn, "Menu: Departamentos (tipo=1)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Departamentos', 'departamentos.php', '1', '9.1', 'feather icon-users', 'adm'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='departamentos.php' AND `tipo`='1')
");

// ── 17. Menu: Fila de Atendimento para tipo=1 (admin) ────────────────────────
$out .= run($conn, "Menu: Fila de Atendimento (tipo=1)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Fila de Atendimento', 'fila_geral.php', '1', '9.2', 'feather icon-list', 'adm'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='fila_geral.php' AND `tipo`='1')
");

// ── 18. Menu: Gerenciar Equipe para tipo=1 (admin) ────────────────────────────
$out .= run($conn, "Menu: Gerenciar Equipe (tipo=1)", "
INSERT INTO `menu` (`menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`)
SELECT 'Gerenciar Equipe', 'gerenciar_equipe.php', '1', '9.3', 'feather icon-users', 'adm'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menu_pagina`='gerenciar_equipe.php' AND `tipo`='1')
");

$status_cor  = $erros === 0 ? '#28a745' : '#dc3545';
$status_txt  = $erros === 0 ? "✅ Schema aplicado com sucesso!" : "⚠️ {$erros} erro(s) encontrado(s). Verifique abaixo.";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Aplicar Multi Atendente — Banco</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4" style="max-width:900px;">
  <h4 style="color:#001f3f;font-weight:700;">🔧 Aplicar Schema — Multi Atendente</h4>
  <p class="text-muted">Banco: <strong><?= htmlspecialchars($db) ?></strong></p>

  <div class="alert" style="background:<?= $status_cor ?>;color:#fff;font-weight:600;border-radius:8px;">
    <?= $status_txt ?>
  </div>

  <table class="table table-sm table-bordered">
    <thead class="thead-dark">
      <tr><th width="40">Status</th><th>Operação</th><th>Resultado</th></tr>
    </thead>
    <tbody>
      <?= $out ?>
    </tbody>
  </table>

  <div class="mt-3">
    <a href="../departamentos.php" class="btn btn-primary" style="background:#001f3f;border-color:#001f3f;">
      Ir para Departamentos
    </a>
    <a href="../fila_geral.php" class="btn btn-outline-secondary ml-2">
      Ver Fila Geral
    </a>
    <a href="javascript:location.reload()" class="btn btn-outline-info ml-2">
      Executar novamente
    </a>
  </div>
</div>
</body>
</html>
