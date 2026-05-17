<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) VaiPara('login.php');
$login = $_SESSION['login'];

include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

$pagina_nome_recebe = isset($_GET['pagina_nome']) ? (int)$_GET['pagina_nome'] : 0;

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$q_u = $stmt_u->get_result();
$stmt_u->close();

while ($r = $q_u->fetch_array()) {
    $nome        = Priletra($r['nome']);
    $img_perfil  = $r['perfil_img'];
    $autorizado  = $r['autorizado'];
    $tipo        = $r['tipo'];
    $usuario_api = $r['usuario_api'] ?? ('agenda_' . $login);
}

include 'menu.php';

if ($q_u->num_rows < 1) VaiPara('login.php');
if ($autorizado != 2)   VaiPara('desbloquar.php');

include 'bloqueio.php';

// --- Ação AJAX: mover cliente de coluna (etiqueta) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mover_cliente'])) {
    $cid       = (int)$_POST['mover_cliente'];
    $nova_etiq = trim($_POST['nova_etiqueta']);
    $stmt_mv   = $conn->prepare("UPDATE clientes SET etiqueta = ? WHERE id = ? AND usuario_api = ?");
    $stmt_mv->bind_param("sis", $nova_etiq, $cid, $usuario_api);
    $ok = $stmt_mv->execute();
    $stmt_mv->close();
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok]);
    exit;
}

// --- Busca etiquetas únicas ---
$stmt_etiq = $conn->prepare("SELECT DISTINCT etiqueta FROM clientes WHERE usuario_api = ? ORDER BY etiqueta ASC");
$stmt_etiq->bind_param("s", $usuario_api);
$stmt_etiq->execute();
$res_etiq = $stmt_etiq->get_result();
$stmt_etiq->close();

$colunas = [];
while ($e = $res_etiq->fetch_assoc()) {
    $colunas[] = $e['etiqueta'] !== '' ? $e['etiqueta'] : 'Sem Etiqueta';
}
if (empty($colunas)) {
    $colunas = ['Novo Contato', 'Em Atendimento', 'Aguardando', 'Finalizado'];
}

// --- Busca clientes ---
$stmt_cli = $conn->prepare("SELECT id, nome, telefone, etiqueta, status, funcao, time_atendimento, updated_at FROM clientes WHERE usuario_api = ? ORDER BY nome ASC");
$stmt_cli->bind_param("s", $usuario_api);
$stmt_cli->execute();
$res_cli = $stmt_cli->get_result();
$stmt_cli->close();

$clientes_por_col = [];
$total_clientes   = 0;
while ($c = $res_cli->fetch_assoc()) {
    $col = ($c['etiqueta'] !== '') ? $c['etiqueta'] : 'Sem Etiqueta';
    $clientes_por_col[$col][] = $c;
    $total_clientes++;
}

$cores_col = ['#001f3f','#FF5500','#27ae60','#8e44ad','#e67e22','#16a085','#2980b9','#c0392b'];

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
/* ── Layout ── */
.kanban-wrapper { overflow-x: auto; padding-bottom: 20px; }
.kanban-board   { display: flex; gap: 16px; min-width: max-content; padding: 4px 2px; }
.kanban-col     { width: 280px; flex-shrink: 0; background: #f4f6fb; border-radius: 12px; display: flex; flex-direction: column; }

/* ── Cabeçalho da coluna ── */
.kanban-col-header { padding: 12px 14px; border-radius: 12px 12px 0 0; color: #fff; display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 13px; }
.badge-count { background: rgba(255,255,255,.25); color: #fff; border-radius: 20px; padding: 2px 9px; font-size: 11px; font-weight: 700; }

/* ── Corpo da coluna ── */
.kanban-col-body { padding: 10px; flex: 1; min-height: 140px; border-radius: 0 0 12px 12px; transition: background .15s; }
.kanban-col-body.drag-over { background: #dce8ff; }

/* ── Card ── */
.kanban-card { background: #fff; border-radius: 10px; padding: 13px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.07); cursor: grab; border-left: 4px solid transparent; transition: box-shadow .18s, opacity .18s; user-select: none; }
.kanban-card:hover    { box-shadow: 0 4px 18px rgba(0,0,0,.13); }
.kanban-card.dragging { opacity: .45; box-shadow: 0 8px 28px rgba(0,0,0,.2); cursor: grabbing; }

/* ── Card — avatar + nome ── */
.card-top    { display: flex; align-items: center; gap: 10px; margin-bottom: 7px; }
.card-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
.card-nome   { font-weight: 600; font-size: 13.5px; color: #1a2340; line-height: 1.2; }
.card-funcao { font-size: 11px; color: #888; margin-top: 1px; }

/* ── Card — telefone + tempo ── */
.card-meta   { font-size: 11.5px; color: #888; margin-bottom: 7px; display: flex; gap: 10px; flex-wrap: wrap; }
.card-meta i { font-size: 11px; }

/* ── Card — badges ── */
.card-badges { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 9px; }
.cb          { font-size: 10px; padding: 2px 7px; border-radius: 20px; font-weight: 600; }
.cb-online   { background: #d4f5e9; color: #1a7a4a; }
.cb-offline  { background: #f1f3f6; color: #888; }
.cb-bot      { background: #fff3cd; color: #856404; }

/* ── Card — ações ── */
.card-actions { display: flex; gap: 6px; align-items: center; }
.card-actions a { font-size: 11px; padding: 4px 9px; border-radius: 6px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 3px; }
.btn-wa    { background: #d4f5e9; color: #1a7a4a; }
.btn-wa:hover { background: #b8ecd5; color: #1a7a4a; }
.move-select { font-size: 11px; border: 1px solid #dde; border-radius: 6px; padding: 3px 5px; flex: 1; min-width: 0; color: #334; cursor: pointer; }
.move-select:focus { outline: none; border-color: #001f3f; }

/* ── Estado vazio ── */
.kanban-empty { text-align: center; color: #bbb; font-size: 12.5px; padding: 24px 10px; }
.kanban-empty i { font-size: 30px; display: block; margin-bottom: 6px; opacity: .5; }

/* ── Barra de busca ── */
.search-bar { background: #fff; border: 1.5px solid #e0e4ef; border-radius: 8px; padding: 7px 14px; display: flex; align-items: center; gap: 8px; max-width: 280px; }
.search-bar input { border: none; outline: none; font-size: 13px; width: 100%; color: #334; background: transparent; }
.search-bar i { color: #aaa; font-size: 14px; }

/* ── Toast ── */
#kb-toast { position: fixed; bottom: 24px; right: 24px; background: #1a2340; color: #fff; border-radius: 10px; padding: 11px 20px; font-size: 13px; font-weight: 600; display: none; z-index: 9999; box-shadow: 0 4px 20px rgba(0,0,0,.2); transition: opacity .3s; }
#kb-toast.success { border-left: 4px solid #27ae60; }
#kb-toast.error   { border-left: 4px solid #e74c3c; }

/* ── Responsivo ── */
@media (max-width: 600px) { .kanban-col { width: 240px; } }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">

    <!-- Cabeçalho -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h4 style="color:#001f3f; font-weight:700; margin:0 0 3px;"><i class="feather icon-trello"></i> Kanban de Contatos</h4>
            <small class="text-muted">
                <?= $total_clientes ?> contato(s) · <?= count($colunas) ?> coluna(s)
                — arraste ou use o seletor para mover entre etapas
            </small>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <div class="search-bar">
                <i class="feather icon-search"></i>
                <input type="text" id="kbSearch" placeholder="Buscar contato..." autocomplete="off">
            </div>
            <a href="clientes.php" class="btn btn-sm btn-outline-primary"><i class="feather icon-users"></i> Lista</a>
        </div>
    </div>

    <!-- Board -->
    <div class="kanban-wrapper">
        <div class="kanban-board" id="kanbanBoard">
        <?php foreach ($colunas as $idx => $coluna):
            $cor   = $cores_col[$idx % count($cores_col)];
            $cards = $clientes_por_col[$coluna] ?? [];
            $total_col = count($cards);
            // avatar bg: usa a cor da coluna com opacidade
            $av_bg = $cor;
        ?>
            <div class="kanban-col" data-coluna="<?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?>">
                <div class="kanban-col-header" style="background:<?= $cor ?>;">
                    <span><i class="feather icon-layers" style="font-size:12px;margin-right:5px;"></i><?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="badge-count" data-col-count><?= $total_col ?></span>
                </div>
                <div class="kanban-col-body" data-coluna="<?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (empty($cards)): ?>
                    <div class="kanban-empty" data-empty>
                        <i class="feather icon-inbox"></i>
                        Sem contatos
                    </div>
                <?php else: ?>
                    <?php foreach ($cards as $c):
                        $tel_clean  = preg_replace('/[^0-9]/', '', $c['telefone']);
                        $wa_link    = 'https://wa.me/' . (str_starts_with($tel_clean, '55') ? $tel_clean : '55' . $tel_clean);
                        $iniciais   = mb_strtoupper(mb_substr($c['nome'], 0, 1, 'UTF-8'), 'UTF-8');
                        $segundo    = mb_substr($c['nome'], mb_strpos($c['nome'], ' ') + 1, 1, 'UTF-8');
                        if ($segundo) $iniciais .= mb_strtoupper($segundo, 'UTF-8');
                        $status_raw = strtolower($c['status'] ?? '');
                        $funcao_str = htmlspecialchars($c['funcao'] ?? '', ENT_QUOTES, 'UTF-8');
                        $updated    = $c['updated_at'] ?? $c['time_atendimento'] ?? '';
                        $updated_fmt = '';
                        if ($updated) {
                            $ts = is_numeric($updated) ? (int)$updated : strtotime($updated);
                            if ($ts > 0) {
                                $diff = time() - $ts;
                                if ($diff < 60)          $updated_fmt = 'agora';
                                elseif ($diff < 3600)    $updated_fmt = floor($diff/60) . 'm atrás';
                                elseif ($diff < 86400)   $updated_fmt = floor($diff/3600) . 'h atrás';
                                else                     $updated_fmt = date('d/m', $ts);
                            }
                        }
                    ?>
                    <div class="kanban-card" draggable="true" data-id="<?= (int)$c['id'] ?>" data-nome="<?= htmlspecialchars(strtolower($c['nome']), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-top">
                            <div class="card-avatar" style="background:<?= $av_bg ?>;"><?= htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8') ?></div>
                            <div>
                                <div class="card-nome"><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($funcao_str): ?>
                                <div class="card-funcao"><?= $funcao_str ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-meta">
                            <span><i class="feather icon-phone"></i> <?= htmlspecialchars($c['telefone'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ($updated_fmt): ?>
                            <span><i class="feather icon-clock"></i> <?= $updated_fmt ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-badges">
                            <?php if (strpos($status_raw, 'online') !== false): ?>
                                <span class="cb cb-online"><i class="feather icon-circle" style="font-size:8px;"></i> Online</span>
                            <?php elseif (strpos($status_raw, 'bot') !== false || strpos($status_raw, 'auto') !== false): ?>
                                <span class="cb cb-bot"><i class="feather icon-zap" style="font-size:9px;"></i> Bot</span>
                            <?php elseif ($status_raw): ?>
                                <span class="cb cb-offline"><?= htmlspecialchars(ucfirst($c['status']), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-actions">
                            <a href="<?= $wa_link ?>" target="_blank" class="btn-wa">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </a>
                            <select class="move-select" data-id="<?= (int)$c['id'] ?>" title="Mover para...">
                                <option value="">Mover...</option>
                                <?php foreach ($colunas as $col_dest): if ($col_dest === $coluna) continue; ?>
                                    <option value="<?= htmlspecialchars($col_dest, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($col_dest, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Toast de feedback -->
<div id="kb-toast"></div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
document.addEventListener('DOMContentLoaded', function () {

    /* ── Toast ── */
    var toast = document.getElementById('kb-toast');
    var toastTimer;
    function showToast(msg, type) {
        toast.textContent = msg;
        toast.className   = type || 'success';
        toast.style.display = 'block';
        toast.style.opacity = '1';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { toast.style.display = 'none'; }, 300);
        }, 2600);
    }

    /* ── Atualiza contadores e estado vazio das colunas ── */
    function refreshCol(col) {
        var body  = col.querySelector('.kanban-col-body');
        var cards = body.querySelectorAll('.kanban-card:not([style*="none"])');
        var hdr   = col.querySelector('[data-col-count]');
        hdr.textContent = cards.length;

        var empty = body.querySelector('[data-empty]');
        if (!empty) {
            empty = document.createElement('div');
            empty.className = 'kanban-empty';
            empty.setAttribute('data-empty', '');
            empty.innerHTML = '<i class="feather icon-inbox"></i>Sem contatos';
            body.appendChild(empty);
        }
        empty.style.display = cards.length === 0 ? '' : 'none';
    }

    /* ── Mover no servidor ── */
    function moverCliente(id, etiqueta) {
        var fd = new FormData();
        fd.append('mover_cliente', id);
        fd.append('nova_etiqueta', etiqueta === 'Sem Etiqueta' ? '' : etiqueta);
        return fetch('kanban.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok) {
                    showToast('✓ Movido para "' + etiqueta + '"', 'success');
                } else {
                    showToast('Erro ao mover contato.', 'error');
                }
            })
            .catch(function () { showToast('Erro de comunicação.', 'error'); });
    }

    /* ── Drag & Drop ── */
    var dragging = null;

    function bindCard(card) {
        card.addEventListener('dragstart', function () {
            dragging = card;
            setTimeout(function () { card.classList.add('dragging'); }, 0);
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('dragging');
            dragging = null;
        });
    }
    document.querySelectorAll('.kanban-card').forEach(bindCard);

    document.querySelectorAll('.kanban-col').forEach(function (col) {
        var body = col.querySelector('.kanban-col-body');
        body.addEventListener('dragover',  function (e) { e.preventDefault(); body.classList.add('drag-over'); });
        body.addEventListener('dragleave', function ()  { body.classList.remove('drag-over'); });
        body.addEventListener('drop', function (e) {
            e.preventDefault();
            body.classList.remove('drag-over');
            if (!dragging) return;
            var oldCol = dragging.closest('.kanban-col');
            if (oldCol === col) return;
            body.insertBefore(dragging, body.querySelector('[data-empty]'));
            var novaEtiqueta = col.dataset.coluna;
            moverCliente(dragging.dataset.id, novaEtiqueta);
            refreshCol(col);
            refreshCol(oldCol);
        });
    });

    /* ── Select de mover ── */
    document.querySelectorAll('.move-select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            if (!this.value) return;
            var nova   = this.value;
            var card   = this.closest('.kanban-card');
            var oldCol = card.closest('.kanban-col');
            var destBody = document.querySelector('.kanban-col-body[data-coluna="' + CSS.escape(nova) + '"]');
            if (!destBody) return;
            var destCol = destBody.closest('.kanban-col');
            destBody.insertBefore(card, destBody.querySelector('[data-empty]'));
            moverCliente(card.dataset.id, nova);
            refreshCol(destCol);
            refreshCol(oldCol);
            this.value = '';
        });
    });

    /* ── Inicializa estados vazios ── */
    document.querySelectorAll('.kanban-col').forEach(refreshCol);

    /* ── Busca por nome ── */
    var searchInput = document.getElementById('kbSearch');
    searchInput.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('.kanban-card').forEach(function (card) {
            var match = !q || card.dataset.nome.indexOf(q) !== -1;
            card.style.display = match ? '' : 'none';
        });
        document.querySelectorAll('.kanban-col').forEach(refreshCol);
    });

});
</script>

<?php include 'footer.php'; ?>
