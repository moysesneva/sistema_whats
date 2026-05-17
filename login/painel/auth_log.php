<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];

require_once __DIR__ . '/conn.php';
include 'estilo.php';
include 'css_de_icones.php';

$stmt = $conn->prepare("SELECT tipo, autorizado FROM login WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r || $r['autorizado'] != 2) {
    VaiPara('desbloquar.php');
}

$tipo = $r['tipo'];

if (!in_array($tipo, [1, 4])) {
    VaiPara('index.php');
}

include 'menu.php';
include 'bloqueio.php';

// -----------------------------------------------------------------------
// CSRF token para proteção do formulário de limpeza
// -----------------------------------------------------------------------

if (empty($_SESSION['csrf_authlog'])) {
    $_SESSION['csrf_authlog'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['csrf_authlog'];

// -----------------------------------------------------------------------
// Limpar log se solicitado
// -----------------------------------------------------------------------

$log_file          = __DIR__ . '/logs/auth_blocked.log';
$api_log_file      = __DIR__ . '/logs/api_blocked.log';
$not_found_log     = __DIR__ . '/logs/not_found.log';
$msg_acao = '';
$msg_acao_tipo = 'success';

if (
    isset($_POST['limpar_log']) && $_POST['limpar_log'] === '1' &&
    isset($_POST['csrf_token']) && hash_equals($csrf_token, $_POST['csrf_token'])
) {
    $erros = 0;
    foreach ([$log_file, $api_log_file, $not_found_log] as $__f) {
        if (is_file($__f)) {
            if (@file_put_contents($__f, '') === false) {
                $erros++;
            }
        }
    }
    if ($erros === 0) {
        $msg_acao = 'Log de acessos bloqueados limpo com sucesso.';
    } else {
        $msg_acao = 'Erro: não foi possível limpar um ou mais arquivos de log. Verifique as permissões.';
        $msg_acao_tipo = 'danger';
    }
}

// -----------------------------------------------------------------------
// Filtros
// -----------------------------------------------------------------------

$filtro_ip     = trim($_GET['ip']     ?? '');
$filtro_motivo = trim($_GET['motivo'] ?? '');

// -----------------------------------------------------------------------
// Leitura dos logs (sessão + API webhook)
// -----------------------------------------------------------------------

$entradas  = [];
$total     = 0;
$total_1h  = 0;
$total_24h = 0;
$agora     = time();

foreach ([$log_file, $api_log_file, $not_found_log] as $__arquivo_log) {
    if (!is_file($__arquivo_log)) continue;
    $linhas = file($__arquivo_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas === false) continue;
    foreach ($linhas as $linha) {
        $obj = json_decode($linha, true);
        if (!is_array($obj)) continue;
        $total++;
        $ts_val = isset($obj['ts']) ? strtotime($obj['ts']) : 0;
        if ($ts_val && ($agora - $ts_val) <= 3600)  $total_1h++;
        if ($ts_val && ($agora - $ts_val) <= 86400) $total_24h++;
        $entradas[] = $obj;
    }
}

// Ordena todas as entradas da mais recente para a mais antiga
usort($entradas, function (array $a, array $b): int {
    return strcmp($b['ts'] ?? '', $a['ts'] ?? '');
});

// Aplicar filtros
$filtradas = $entradas;
if ($filtro_ip !== '') {
    $filtradas = array_filter($filtradas, fn($e) => str_contains($e['ip'] ?? '', $filtro_ip));
}
if ($filtro_motivo !== '') {
    $filtradas = array_filter($filtradas, fn($e) => ($e['motivo'] ?? '') === $filtro_motivo);
}
$filtradas = array_values($filtradas);

$exibir = array_slice($filtradas, 0, 100);

// -----------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------

function motivo_badge(string $motivo): string
{
    $map = [
        'sessao_expirada'      => ['warning',  'Sessão expirada'],
        'sem_sessao'           => ['danger',   'Sem sessão'],
        'token_ausente'        => ['danger',   'Token ausente'],
        'token_invalido'       => ['danger',   'Token inválido'],
        'token_nao_configurado'=> ['secondary','Token não configurado'],
        'pagina_nao_encontrada'=> ['info',     'Página não encontrada (404)'],
    ];
    if (isset($map[$motivo])) {
        [$cor, $label] = $map[$motivo];
        return '<span class="badge badge-' . $cor . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
    }
    return '<span class="badge badge-secondary">' . htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8') . '</span>';
}
?>
<?php include 'header.php'; ?>

<style>
.diag-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.diag-card .card-header {
    background: linear-gradient(135deg, #001f3f, #003366);
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 14px 20px;
    font-weight: 600;
}
.diag-card .card-header i {
    margin-right: 8px;
    color: #FF5500;
}
.page-title-box { margin-bottom: 24px; }
.page-title-box h4 { color: #001f3f; font-weight: 700; }
.page-title-box p  { color: #777; font-size: 14px; margin: 0; }
.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px 20px;
    text-align: center;
}
.stat-box .stat-num {
    font-size: 28px;
    font-weight: 800;
    color: #001f3f;
}
.stat-box .stat-num.text-danger  { color: #dc3545 !important; }
.stat-box .stat-num.text-warning { color: #ffc107 !important; }
.stat-box .stat-label {
    font-size: 12px;
    color: #777;
    margin-top: 4px;
}
.log-table td, .log-table th { font-size: 13px; vertical-align: middle; }
.log-table .url-cell { max-width: 360px; word-break: break-all; }
.no-entries { text-align: center; padding: 40px 20px; color: #aaa; }
.no-entries i { font-size: 40px; color: #ccc; display: block; margin-bottom: 12px; }
.filter-bar { background: #f8f9fa; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; }
</style>

<div class="container-fluid">
    <div class="page-title-box">
        <h4><i class="feather icon-shield" style="color:#FF5500;margin-right:8px;"></i>Log de Acessos Bloqueados</h4>
        <p>Histórico de tentativas de acesso negado: sessões inválidas no painel e tokens inválidos nos webhooks de API.</p>
    </div>

    <?php if ($msg_acao): ?>
    <?php
        $icons = ['success' => 'icon-check-circle', 'danger' => 'icon-alert-octagon', 'warning' => 'icon-alert-triangle'];
        $icone = $icons[$msg_acao_tipo] ?? 'icon-info';
    ?>
    <div class="alert alert-<?= htmlspecialchars($msg_acao_tipo, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
        <i class="feather <?= $icone ?>" style="margin-right:6px;"></i>
        <?= htmlspecialchars($msg_acao, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>

    <!-- Contadores -->
    <div class="row mb-3">
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num <?= $total_1h > 0 ? 'text-danger' : '' ?>"><?= $total_1h ?></div>
                <div class="stat-label"><i class="feather icon-clock"></i> Última 1 hora</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num <?= $total_24h > 0 ? 'text-warning' : '' ?>"><?= $total_24h ?></div>
                <div class="stat-label"><i class="feather icon-calendar"></i> Últimas 24 horas</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="stat-num"><?= $total ?></div>
                <div class="stat-label"><i class="feather icon-list"></i> Total registrado</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-bar">
        <form method="get" class="form-inline" style="gap:10px;flex-wrap:wrap;">
            <div class="form-group mr-3 mb-2">
                <label class="mr-2" style="font-size:13px;font-weight:600;">IP:</label>
                <input type="text" name="ip" class="form-control form-control-sm"
                       placeholder="Ex: 192.168."
                       value="<?= htmlspecialchars($filtro_ip, ENT_QUOTES, 'UTF-8') ?>"
                       style="width:160px;">
            </div>
            <div class="form-group mr-3 mb-2">
                <label class="mr-2" style="font-size:13px;font-weight:600;">Motivo:</label>
                <select name="motivo" class="form-control form-control-sm" style="width:200px;">
                    <option value="">Todos</option>
                    <optgroup label="Painel (sessão)">
                        <option value="sessao_expirada" <?= $filtro_motivo === 'sessao_expirada' ? 'selected' : '' ?>>Sessão expirada</option>
                        <option value="sem_sessao"      <?= $filtro_motivo === 'sem_sessao'      ? 'selected' : '' ?>>Sem sessão</option>
                    </optgroup>
                    <optgroup label="Webhook (token)">
                        <option value="token_ausente"         <?= $filtro_motivo === 'token_ausente'         ? 'selected' : '' ?>>Token ausente</option>
                        <option value="token_invalido"        <?= $filtro_motivo === 'token_invalido'        ? 'selected' : '' ?>>Token inválido</option>
                        <option value="token_nao_configurado" <?= $filtro_motivo === 'token_nao_configurado' ? 'selected' : '' ?>>Token não configurado</option>
                    </optgroup>
                    <optgroup label="Páginas inexistentes">
                        <option value="pagina_nao_encontrada" <?= $filtro_motivo === 'pagina_nao_encontrada' ? 'selected' : '' ?>>Página não encontrada (404)</option>
                    </optgroup>
                </select>
            </div>
            <div class="mb-2">
                <button type="submit" class="btn btn-sm btn-primary mr-2">
                    <i class="feather icon-filter"></i> Filtrar
                </button>
                <?php if ($filtro_ip !== '' || $filtro_motivo !== ''): ?>
                <a href="auth_log.php" class="btn btn-sm btn-outline-secondary">
                    <i class="feather icon-x"></i> Limpar filtro
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabela de acessos bloqueados -->
    <div class="card diag-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="feather icon-lock"></i> Acessos bloqueados
                <?php if (count($filtradas) < $total): ?>
                    <small style="font-weight:400;opacity:.8;">
                        (<?= count($filtradas) ?> de <?= $total ?> com filtro aplicado<?= count($filtradas) > 100 ? ', exibindo 100 mais recentes' : '' ?>)
                    </small>
                <?php elseif ($total > 100): ?>
                    <small style="font-weight:400;opacity:.8;">(exibindo as 100 mais recentes de <?= $total ?>)</small>
                <?php endif; ?>
            </span>
            <?php if ($total > 0): ?>
            <form method="post" style="margin:0;" data-confirm="Limpar todo o histórico de acessos bloqueados?">
                <input type="hidden" name="limpar_log" value="1">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="feather icon-trash-2"></i> Limpar log
                </button>
            </form>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($exibir)): ?>
            <div class="no-entries">
                <i class="feather icon-check-circle"></i>
                <?php if ($filtro_ip !== '' || $filtro_motivo !== ''): ?>
                    Nenhum registro encontrado para o filtro aplicado.
                <?php else: ?>
                    Nenhum acesso bloqueado registrado. Tudo certo!
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover log-table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="white-space:nowrap;">Data/hora</th>
                            <th>IP</th>
                            <th>Método</th>
                            <th>Motivo</th>
                            <th class="url-cell">URL acessada</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($exibir as $e): ?>
                        <tr>
                            <td style="white-space:nowrap;">
                                <?= htmlspecialchars($e['ts'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($e['ip'] ?? '-', ENT_QUOTES, 'UTF-8') ?></code>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <?= htmlspecialchars($e['method'] ?? 'GET', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td><?= motivo_badge($e['motivo'] ?? '') ?></td>
                            <td class="url-cell">
                                <?= htmlspecialchars($e['url'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($e['ua'])): ?>
                                <br><small class="text-muted" style="font-size:11px;word-break:break-all;" title="User-Agent">
                                    <i class="feather icon-monitor" style="font-size:10px;"></i>
                                    <?= htmlspecialchars($e['ua'], ENT_QUOTES, 'UTF-8') ?>
                                </small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <p class="text-muted" style="font-size:12px;">
        <i class="feather icon-info"></i>
        Eventos de painel (sessão inválida) são registrados em <code>logs/auth_blocked.log</code>; eventos de webhook (token inválido) em <code>logs/api_blocked.log</code>;
        acessos a URLs inexistentes (404) em <code>logs/not_found.log</code>.
        Todos são exibidos aqui em ordem mais recente primeiro. <strong>Token ausente</strong> / <strong>Token inválido</strong> indicam chamadas ao webhook sem autenticação correta;
        <strong>Sem sessão</strong> / <strong>Sessão expirada</strong> indicam acessos ao painel sem login válido;
        <strong>Página não encontrada (404)</strong> indica acesso a URL inexistente no sistema — inclui IP, URL e user agent.
    </p>
</div>

<?php include 'footer.php'; ?>
