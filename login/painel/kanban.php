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

// --- Ação: mover cliente de coluna (etiqueta) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mover_cliente'])) {
    $cid       = (int)$_POST['mover_cliente'];
    $nova_etiq = trim($_POST['nova_etiqueta']);
    $stmt_mv   = $conn->prepare("UPDATE clientes SET etiqueta = ? WHERE id = ? AND usuario_api = ?");
    $stmt_mv->bind_param("sis", $nova_etiq, $cid, $usuario_api);
    $stmt_mv->execute();
    $stmt_mv->close();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
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

// --- Busca clientes por etiqueta ---
$stmt_cli = $conn->prepare("SELECT id, nome, telefone, etiqueta, status, time_atendimento FROM clientes WHERE usuario_api = ? ORDER BY nome ASC");
$stmt_cli->bind_param("s", $usuario_api);
$stmt_cli->execute();
$res_cli = $stmt_cli->get_result();
$stmt_cli->close();

$clientes_por_col = [];
while ($c = $res_cli->fetch_assoc()) {
    $col = ($c['etiqueta'] !== '') ? $c['etiqueta'] : 'Sem Etiqueta';
    $clientes_por_col[$col][] = $c;
}

$cores_col = ['#001f3f','#FF5500','#27ae60','#8e44ad','#e67e22','#16a085','#2980b9','#c0392b'];

$css_extra = '
<link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
<style>
.kanban-wrapper { overflow-x: auto; padding-bottom: 20px; }
.kanban-board { display: flex; gap: 16px; min-width: max-content; padding: 4px 2px; }
.kanban-col { width: 270px; flex-shrink: 0; background: #f4f6fb; border-radius: 12px; display: flex; flex-direction: column; }
.kanban-col-header { padding: 14px 16px; border-radius: 12px 12px 0 0; color: #fff; display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 13px; }
.kanban-col-body { padding: 10px; flex: 1; min-height: 120px; }
.kanban-card { background: #fff; border-radius: 10px; padding: 14px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.07); cursor: grab; border-left: 4px solid transparent; transition: box-shadow .2s; }
.kanban-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.13); }
.kanban-card .card-nome { font-weight: 600; font-size: 14px; color: #1a2340; margin-bottom: 4px; }
.kanban-card .card-tel { font-size: 12px; color: #888; margin-bottom: 8px; }
.kanban-card .card-actions { display: flex; gap: 6px; }
.kanban-card .card-actions a { font-size: 11px; padding: 3px 8px; border-radius: 5px; text-decoration: none; font-weight: 600; }
.kanban-card.dragging { opacity: .5; box-shadow: 0 8px 24px rgba(0,0,0,.2); }
.kanban-col-body.drag-over { background: #e8f0fe; border-radius: 8px; }
.badge-count { background: rgba(255,255,255,.25); color: #fff; border-radius: 20px; padding: 2px 8px; font-size: 11px; }
.kanban-empty { text-align: center; color: #aaa; font-size: 13px; padding: 20px 10px; }
.kanban-empty i { font-size: 28px; display: block; margin-bottom: 6px; }
.move-select { font-size: 11px; border: 1px solid #dde; border-radius: 4px; padding: 2px 4px; }
</style>';

include 'header.php';
?>

<div class="container-fluid" style="padding:20px 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <div>
            <h4 style="color:#001f3f; font-weight:700; margin:0;"><i class="feather icon-trello"></i> Kanban de Contatos</h4>
            <small class="text-muted">Gerencie seus contatos por etapa de atendimento</small>
        </div>
        <a href="clientes.php" class="btn btn-sm btn-primary"><i class="feather icon-users"></i> Ver Lista de Clientes</a>
    </div>

    <div class="kanban-wrapper">
        <div class="kanban-board" id="kanbanBoard">
        <?php foreach ($colunas as $idx => $coluna): 
            $cor = $cores_col[$idx % count($cores_col)];
            $cards = $clientes_por_col[$coluna] ?? [];
            $total_col = count($cards);
        ?>
            <div class="kanban-col" data-coluna="<?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?>">
                <div class="kanban-col-header" style="background:<?= $cor ?>;">
                    <span><?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="badge-count"><?= $total_col ?></span>
                </div>
                <div class="kanban-col-body" data-coluna="<?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (empty($cards)): ?>
                    <div class="kanban-empty">
                        <i class="feather icon-inbox"></i>
                        Sem contatos
                    </div>
                <?php else: ?>
                    <?php foreach ($cards as $c):
                        $tel_clean = preg_replace('/[^0-9]/', '', $c['telefone']);
                        $wa_link   = 'https://wa.me/' . (str_starts_with($tel_clean, '55') ? $tel_clean : '55' . $tel_clean);
                    ?>
                    <div class="kanban-card" draggable="true" data-id="<?= (int)$c['id'] ?>">
                        <div class="card-nome"><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="card-tel"><i class="feather icon-phone" style="font-size:11px;"></i> <?= htmlspecialchars($c['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="card-actions">
                            <a href="<?= $wa_link ?>" target="_blank" style="background:#d4f5e9;color:#1a7a4a;">
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

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Drag & Drop
    let dragging = null;

    document.querySelectorAll('.kanban-card').forEach(function(card) {
        card.addEventListener('dragstart', function() {
            dragging = card;
            setTimeout(function() { card.classList.add('dragging'); }, 0);
        });
        card.addEventListener('dragend', function() {
            card.classList.remove('dragging');
            dragging = null;
        });
    });

    document.querySelectorAll('.kanban-col-body').forEach(function(body) {
        body.addEventListener('dragover', function(e) {
            e.preventDefault();
            body.classList.add('drag-over');
        });
        body.addEventListener('dragleave', function() {
            body.classList.remove('drag-over');
        });
        body.addEventListener('drop', function(e) {
            e.preventDefault();
            body.classList.remove('drag-over');
            if (dragging && body !== dragging.closest('.kanban-col-body')) {
                body.appendChild(dragging);
                const novaEtiqueta = body.closest('.kanban-col').dataset.coluna;
                const cid = dragging.dataset.id;
                moverCliente(cid, novaEtiqueta);
                atualizarContadores();
            }
        });
    });

    // Select de mover
    document.querySelectorAll('.move-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            if (!this.value) return;
            const cid = this.dataset.id;
            const nova = this.value;
            const card = this.closest('.kanban-card');
            const destBody = document.querySelector('.kanban-col-body[data-coluna="' + nova + '"]');
            if (destBody) {
                destBody.appendChild(card);
                moverCliente(cid, nova);
                atualizarContadores();
            }
            this.value = '';
        });
    });

    function moverCliente(id, etiqueta) {
        const fd = new FormData();
        fd.append('mover_cliente', id);
        fd.append('nova_etiqueta', etiqueta === 'Sem Etiqueta' ? '' : etiqueta);
        fetch('kanban.php', { method: 'POST', body: fd });
    }

    function atualizarContadores() {
        document.querySelectorAll('.kanban-col').forEach(function(col) {
            const body = col.querySelector('.kanban-col-body');
            const cnt = body.querySelectorAll('.kanban-card').length;
            col.querySelector('.badge-count').textContent = cnt;
        });
    }
});
</script>

<?php include 'footer.php'; ?>
