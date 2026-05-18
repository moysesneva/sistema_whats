<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) { VaiPara('login.php'); }
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

$stmt_u = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_u->bind_param("s", $login);
$stmt_u->execute();
$res_u = $stmt_u->get_result();
$stmt_u->close();
if (!$res_u || $res_u->num_rows === 0) VaiPara('login.php');
$udata = $res_u->fetch_assoc();
$tipo        = $udata['tipo'];
$autorizado  = $udata['autorizado'];
$nome_user   = Priletra($udata['nome']);
$img_perfil  = $udata['perfil_img'];
$usuario_api = $udata['usuario_api'];
$modo_atual  = $udata['modo_atuante'];

include 'menu.php';

if ($autorizado != 2) VaiPara('desbloquar.php');

// Busca departamentos do atendente
$deptos_ids = [];
if ($tipo === '1') {
    $res_all = $conn->query("SELECT id FROM departamentos WHERE usuario_api='$usuario_api' AND ativo=1");
    if ($res_all) while ($r = $res_all->fetch_assoc()) $deptos_ids[] = $r['id'];
} else {
    $stmt_dep = $conn->prepare("SELECT depto_id FROM atendentes_depto WHERE login_atendente=? AND usuario_api=?");
    $stmt_dep->bind_param("ss", $login, $usuario_api);
    $stmt_dep->execute();
    $res_dep = $stmt_dep->get_result();
    $stmt_dep->close();
    while ($r = $res_dep->fetch_assoc()) $deptos_ids[] = $r['depto_id'];
}

// Busca departamentos para transferência
$deptos_lista = [];
if (!empty($deptos_ids)) {
    $in_ids = implode(',', array_map('intval', $deptos_ids));
    $res_dl = $conn->query("SELECT id, nome FROM departamentos WHERE id IN ($in_ids) AND ativo=1 ORDER BY nome");
    if ($res_dl) while ($r = $res_dl->fetch_assoc()) $deptos_lista[] = $r;
}

// Todos os departamentos (para transferir para outros)
$todos_deptos = [];
$res_td = $conn->query("SELECT id, nome FROM departamentos WHERE usuario_api='$usuario_api' AND ativo=1 ORDER BY nome");
if ($res_td) while ($r = $res_td->fetch_assoc()) $todos_deptos[] = $r;

$css_extra = '
<style>
:root { --azul:#001f3f; --laranja:#FF5500; }
.chat-layout { display:flex; height:calc(100vh - 180px); min-height:500px; gap:0; }
.lista-conversas { width:320px; min-width:260px; border-right:2px solid #e0e0e0; overflow-y:auto; background:#fff; flex-shrink:0; }
.painel-chat { flex:1; display:flex; flex-direction:column; background:#f5f5f5; }
.conv-item { padding:12px 14px; border-bottom:1px solid #f0f0f0; cursor:pointer; transition:background .15s; }
.conv-item:hover { background:#f8f9fa; }
.conv-item.ativo { background:#e8f0fe; border-left:4px solid var(--azul); }
.conv-item .nome { font-weight:600; font-size:.9rem; color:#222; }
.conv-item .depto-badge { font-size:.7rem; padding:2px 7px; border-radius:99px; background:var(--azul); color:#fff; }
.conv-item .fila-badge { font-size:.7rem; padding:2px 7px; border-radius:99px; background:#ffc107; color:#000; }
.conv-item .humano-badge { font-size:.7rem; padding:2px 7px; border-radius:99px; background:#28a745; color:#fff; }
.conv-item .telefone { font-size:.78rem; color:#777; }
.chat-header { padding:12px 18px; background:var(--azul); color:#fff; display:flex; align-items:center; gap:10px; }
.chat-header .atend-name { font-size:.8rem; opacity:.8; }
.chat-msgs { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:8px; }
.msg-bubble { max-width:72%; padding:9px 14px; border-radius:16px; font-size:.88rem; line-height:1.4; word-break:break-word; }
.msg-cliente { align-self:flex-start; background:#fff; border-radius:16px 16px 16px 4px; box-shadow:0 1px 3px rgba(0,0,0,.08); }
.msg-ia { align-self:flex-end; background:var(--azul); color:#fff; border-radius:16px 16px 4px 16px; }
.msg-atendente { align-self:flex-end; background:var(--laranja); color:#fff; border-radius:16px 16px 4px 16px; }
.msg-hora { font-size:.68rem; opacity:.7; margin-top:2px; }
.chat-input-area { padding:14px; background:#fff; border-top:1px solid #e0e0e0; }
.chat-input-area textarea { resize:none; border-radius:20px; }
.btn-azul { background:var(--azul); color:#fff; border:none; }
.btn-azul:hover { background:#003366; color:#fff; }
.btn-laranja { background:var(--laranja); color:#fff; border:none; }
.btn-laranja:hover { background:#e04a00; color:#fff; }
.secao-label { font-size:.72rem; text-transform:uppercase; font-weight:700; letter-spacing:.5px; color:#999; padding:8px 14px 4px; }
.empty-chat { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:#aaa; }
.pulse { animation:pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.5} }
</style>
';
?>
<?php include 'header.php'; ?>

<div class="content-page">
<div class="content">
<div class="container-fluid px-2">

<div class="row m-t-10 mb-2">
  <div class="col-sm-12 d-flex align-items-center justify-content-between">
    <h5 class="page-title mb-0"><i class="feather icon-message-circle mr-2"></i>Minhas Conversas</h5>
    <span id="status-online" class="badge badge-success px-3 py-2"><i class="feather icon-wifi mr-1"></i>Online</span>
  </div>
</div>

<?php if (empty($deptos_ids)): ?>
<div class="alert alert-warning">
  <i class="feather icon-alert-triangle mr-2"></i>
  Você ainda não está vinculado a nenhum departamento. Contate o administrador.
</div>
<?php else: ?>

<div class="card p-0" style="border:1px solid #ddd;">
<div class="chat-layout">

  <!-- LISTA DE CONVERSAS -->
  <div class="lista-conversas" id="listaConversas">
    <div class="secao-label mt-2">⏳ Na fila</div>
    <div id="lista-fila">
      <div class="text-center text-muted py-3 small"><i class="feather icon-loader pulse mr-1"></i>Carregando...</div>
    </div>
    <div class="secao-label mt-2">🤝 Em atendimento</div>
    <div id="lista-ativos">
      <div class="text-center text-muted py-3 small"><i class="feather icon-loader pulse mr-1"></i>Carregando...</div>
    </div>
  </div>

  <!-- PAINEL DO CHAT -->
  <div class="painel-chat" id="painelChat">
    <div class="empty-chat" id="emptyChat">
      <i class="feather icon-message-circle" style="font-size:64px;color:#ddd;"></i>
      <p class="mt-3">Selecione uma conversa à esquerda</p>
    </div>

    <!-- Chat aberto (oculto inicialmente) -->
    <div id="chatAberto" style="display:none;flex:1;display:none;flex-direction:column;">
      <div class="chat-header" id="chatHeader">
        <div style="flex:1;">
          <div id="chatNome" style="font-weight:700;font-size:1rem;"></div>
          <div id="chatTel" class="atend-name"></div>
        </div>
        <div id="chatAcoes" class="d-flex gap-1 flex-wrap justify-content-end" style="gap:6px;"></div>
      </div>

      <div class="chat-msgs" id="chatMsgs"></div>

      <div class="chat-input-area" id="chatInputArea" style="display:none;">
        <div class="d-flex gap-2" style="gap:8px;">
          <textarea id="txtMsg" class="form-control" rows="2" placeholder="Digite sua mensagem..." style="flex:1;"></textarea>
          <button class="btn btn-laranja px-3" id="btnEnviar" title="Enviar"><i class="feather icon-send"></i></button>
        </div>
        <div class="d-flex mt-2 flex-wrap" style="gap:6px;">
          <button class="btn btn-sm btn-outline-danger" id="btnDevolver"><i class="feather icon-rotate-ccw mr-1"></i>Devolver para IA</button>
          <?php if (!empty($todos_deptos)): ?>
          <div class="input-group input-group-sm" style="width:auto;">
            <select class="form-control form-control-sm" id="selTransfDepto">
              <option value="">Transferir para depto...</option>
              <?php foreach ($todos_deptos as $td): ?>
              <option value="<?= $td['id'] ?>"><?= htmlspecialchars($td['nome']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="input-group-append">
              <button class="btn btn-outline-secondary btn-sm" id="btnTransfDepto">OK</button>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

<?php endif; ?>

</div>
</div>
</div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
const API_URL = 'api/atendente_acao.php';
let clienteAtivo = null;
let ultimoIdMsg   = 0;
let pollingTimer  = null;

// ── Listas ────────────────────────────────────────────────────────────────────
function carregarListas() {
    fetch(API_URL + '?acao=listar_fila')
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return;
            const fila   = data.clientes.filter(c => c.modo_atendimento === 'fila');
            const ativos = data.clientes.filter(c => c.modo_atendimento === 'humano');
            renderLista('lista-fila',   fila,   'fila');
            renderLista('lista-ativos', ativos, 'humano');
        })
        .catch(() => {});
}

function renderLista(containerId, items, modo) {
    const el = document.getElementById(containerId);
    if (items.length === 0) {
        el.innerHTML = '<div class="text-center text-muted py-3 small">Nenhuma conversa</div>';
        return;
    }
    // Usa data-* attributes para evitar XSS via interpolação de string em onclick
    el.innerHTML = items.map(c => `
        <div class="conv-item ${clienteAtivo && clienteAtivo.id == c.id ? 'ativo' : ''}"
             data-cid="${escAttr(String(c.id))}"
             data-nome="${escAttr(c.nome||c.telefone)}"
             data-tel="${escAttr(c.telefone)}"
             data-modo="${escAttr(c.modo_atendimento||'')}"
             role="button" tabindex="0">
            <div class="d-flex justify-content-between align-items-start">
                <div class="nome">${escHtml(c.nome || c.telefone)}</div>
                <span class="${modo}-badge">${modo === 'fila' ? 'Na fila' : 'Em atendimento'}</span>
            </div>
            <div class="telefone">${escHtml(c.telefone)}</div>
            ${c.depto_nome ? `<div><small class="depto-badge">${escHtml(c.depto_nome)}</small></div>` : ''}
            ${c.atendente_atual ? `<div class="mt-1"><small class="text-muted">Atendente: ${escHtml(c.atendente_atual)}</small></div>` : ''}
        </div>
    `).join('');
}

/** Escapa para contexto HTML text/attribute (duplas aspas). */
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/** Escapa para uso em atributo HTML com aspas duplas (inclui aspas simples). */
function escAttr(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;');
}

// Delegação de evento: um único listener no container raiz das listas
document.getElementById('listaConversas').addEventListener('click', function(e) {
    const item = e.target.closest('[data-cid]');
    if (!item) return;
    const id   = parseInt(item.dataset.cid, 10);
    const nome = item.dataset.nome;
    const tel  = item.dataset.tel;
    const modo = item.dataset.modo || '';
    abrirConversa(id, nome, tel, modo);
});

// ── Abrir conversa ─────────────────────────────────────────────────────────────
function abrirConversa(id, nome, tel, modoInicial) {
    clienteAtivo = { id, nome, tel, modo: modoInicial || '' };
    ultimoIdMsg  = 0;
    document.getElementById('emptyChat').style.display = 'none';
    document.getElementById('chatAberto').style.display = 'flex';
    document.getElementById('chatAberto').style.flexDirection = 'column';
    document.getElementById('chatNome').textContent = nome || tel;
    document.getElementById('chatTel').textContent  = tel;
    document.getElementById('chatMsgs').innerHTML   = '<div class="text-center text-muted py-3"><i class="feather icon-loader"></i> Carregando...</div>';
    carregarHistorico(true);
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = setInterval(() => carregarHistorico(false), 3000);
    carregarListas();
}

// ── Histórico / Polling ────────────────────────────────────────────────────────
function carregarHistorico(resetar) {
    if (!clienteAtivo) return;
    const fd = new FormData();
    fd.append('acao', 'historico');
    fd.append('cliente_id', clienteAtivo.id);
    fd.append('desde_id', resetar ? 0 : ultimoIdMsg);

    fetch(API_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) {
                // Fallback: se for conversa em fila e o histórico foi negado (403),
                // mostramos o botão Assumir para que o atendente possa pegá-la.
                if (resetar && clienteAtivo && clienteAtivo.modo === 'fila') {
                    atualizarAcoes('fila', null, clienteAtivo.nome, clienteAtivo.tel);
                    document.getElementById('chatMsgs').innerHTML =
                        '<div class="text-center text-muted py-3 small">Histórico disponível após assumir a conversa.</div>';
                }
                return;
            }
            const msgs    = data.msgs;
            const modo    = data.modo;
            const atAtual = data.atendente_atual;

            // Atualiza o modo local para manter coerência com o estado do servidor
            if (clienteAtivo) clienteAtivo.modo = modo;
            atualizarAcoes(modo, atAtual, data.nome_cliente, data.telefone);

            const container = document.getElementById('chatMsgs');
            if (resetar) container.innerHTML = '';

            msgs.forEach(m => {
                if (m.id > ultimoIdMsg) ultimoIdMsg = m.id;
                if (m.usuario_msg) appendMsg(container, m.usuario_msg, 'cliente', m.data_hora);
                if (m.ia_msg) {
                    const cls = m.tipo_remetente === 'atendente' ? 'atendente' : 'ia';
                    const label = m.tipo_remetente === 'atendente' ? (m.login_historico || 'Atendente') : 'IA';
                    appendMsg(container, m.ia_msg, cls, m.data_hora, label);
                }
            });

            if (msgs.length > 0 || resetar) {
                container.scrollTop = container.scrollHeight;
            }
        })
        .catch(() => {});
}

function appendMsg(container, texto, tipo, hora, label) {
    const div = document.createElement('div');
    div.className = 'd-flex flex-column ' + (tipo === 'cliente' ? 'align-items-start' : 'align-items-end');
    const labelHtml = label ? `<div style="font-size:.68rem;color:#999;margin-bottom:2px;${tipo!=='cliente'?'text-align:right;':''}">${escHtml(label)}</div>` : '';
    div.innerHTML = `
        ${labelHtml}
        <div class="msg-bubble msg-${tipo}">${escHtml(texto).replace(/\n/g,'<br>')}</div>
        <div class="msg-hora">${hora || ''}</div>
    `;
    container.appendChild(div);
}

// ── Ações do chat ─────────────────────────────────────────────────────────────
function atualizarAcoes(modo, atAtual, nomeCliente, tel) {
    const acoesDiv = document.getElementById('chatAcoes');
    const inputArea = document.getElementById('chatInputArea');
    const loginAtual = '<?= htmlspecialchars($login, ENT_QUOTES) ?>';

    acoesDiv.innerHTML = '';

    if (modo === 'fila') {
        inputArea.style.display = 'none';
        const btn = document.createElement('button');
        btn.className = 'btn btn-sm btn-laranja';
        btn.innerHTML = '<i class="feather icon-check mr-1"></i>Assumir conversa';
        btn.onclick = () => assumirConversa();
        acoesDiv.appendChild(btn);
    } else if (modo === 'humano') {
        if (atAtual === loginAtual || '<?= $tipo ?>' === '1') {
            inputArea.style.display = 'block';
        } else {
            inputArea.style.display = 'none';
            const info = document.createElement('span');
            info.className = 'badge badge-info p-2';
            info.textContent = 'Sendo atendido por: ' + (atAtual || '—');
            acoesDiv.appendChild(info);
        }
    } else {
        inputArea.style.display = 'none';
    }
}

function assumirConversa() {
    if (!clienteAtivo) return;
    const fd = new FormData();
    fd.append('acao', 'assumir');
    fd.append('cliente_id', clienteAtivo.id);
    fetch(API_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { carregarHistorico(false); carregarListas(); }
            else alert(data.erro || 'Erro ao assumir');
        });
}

document.getElementById('btnEnviar')?.addEventListener('click', enviarMensagem);
document.getElementById('txtMsg')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarMensagem(); }
});

function enviarMensagem() {
    if (!clienteAtivo) return;
    const txt = document.getElementById('txtMsg').value.trim();
    if (!txt) return;
    const fd = new FormData();
    fd.append('acao', 'responder');
    fd.append('cliente_id', clienteAtivo.id);
    fd.append('mensagem', txt);
    document.getElementById('txtMsg').value = '';
    document.getElementById('btnEnviar').disabled = true;
    fetch(API_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('btnEnviar').disabled = false;
            if (data.ok) carregarHistorico(false);
            else alert(data.erro || 'Erro ao enviar');
        })
        .catch(() => { document.getElementById('btnEnviar').disabled = false; });
}

document.getElementById('btnDevolver')?.addEventListener('click', function() {
    if (!clienteAtivo || !confirm('Devolver esta conversa para a IA?')) return;
    const fd = new FormData();
    fd.append('acao', 'devolver_ia');
    fd.append('cliente_id', clienteAtivo.id);
    fetch(API_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { carregarListas(); document.getElementById('chatAberto').style.display='none'; document.getElementById('emptyChat').style.display='flex'; clienteAtivo=null; clearInterval(pollingTimer); }
            else alert(data.erro);
        });
});

document.getElementById('btnTransfDepto')?.addEventListener('click', function() {
    const sel = document.getElementById('selTransfDepto');
    const depto_id = sel.value;
    if (!depto_id || !clienteAtivo) return;
    const fd = new FormData();
    fd.append('acao', 'transferir_depto');
    fd.append('cliente_id', clienteAtivo.id);
    fd.append('depto_id', depto_id);
    fetch(API_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { alert('Transferido!'); carregarListas(); document.getElementById('chatAberto').style.display='none'; document.getElementById('emptyChat').style.display='flex'; clienteAtivo=null; clearInterval(pollingTimer); }
            else alert(data.erro);
        });
});

// ── Notificação: badge + som + título da aba ──────────────────────────────
let _filaAnterior = -1;  // -1 = ainda não carregou; ignora primeiro ciclo

/** Gera som de ding usando Web Audio API (sem arquivo externo) */
function tocarAlerta() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.3);
        gain.gain.setValueAtTime(0.4, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch(e) { /* AudioContext não disponível */ }
}

/** Atualiza badge no menu lateral e no título da aba */
function atualizarBadgeFila(n) {
    // Badge no menu
    const badge = document.getElementById('menu-fila-badge');
    if (badge) {
        if (n > 0) {
            badge.textContent = n;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
    // Título da aba
    const base = 'Minhas Conversas';
    document.title = n > 0 ? '[' + n + '] ' + base : base;
}

/** Exibe toast de nova conversa na fila */
function mostrarToastFila(qtd) {
    let toast = document.getElementById('toast-fila');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-fila';
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#001f3f;color:#fff;padding:14px 20px;border-radius:10px;box-shadow:0 4px 18px rgba(0,0,0,.25);font-size:14px;z-index:9999;display:flex;align-items:center;gap:10px;transition:opacity .4s;max-width:320px;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = '<i class="feather icon-bell" style="font-size:20px;color:#FF5500;flex-shrink:0;"></i><span><strong>' + qtd + ' conversa' + (qtd > 1 ? 's' : '') + ' na fila</strong><br><small style="opacity:.8;">Acesse a fila para atender</small></span>';
    toast.style.opacity = '1';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 5000);
}

// ── Polling da lista a cada 5s ─────────────────────────────────────────────
function carregarListasComNotificacao() {
    fetch(API_URL + '?acao=listar_fila')
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return;
            const fila   = data.clientes.filter(c => c.modo_atendimento === 'fila');
            const ativos = data.clientes.filter(c => c.modo_atendimento === 'humano');
            renderLista('lista-fila',   fila,   'fila');
            renderLista('lista-ativos', ativos, 'humano');

            const qtdFila = fila.length;
            atualizarBadgeFila(qtdFila);

            // Notifica apenas quando a fila cresce (não no primeiro carregamento)
            if (_filaAnterior >= 0 && qtdFila > _filaAnterior) {
                tocarAlerta();
                mostrarToastFila(qtdFila);
            }
            _filaAnterior = qtdFila;
        })
        .catch(() => {});
}

carregarListasComNotificacao();
setInterval(carregarListasComNotificacao, 5000);
</script>

<?php include 'footer.php'; ?>
