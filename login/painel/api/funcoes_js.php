<?php
/**
 * funcoes_js.php
 * Retorna o módulo JavaScript com as funções Baileys para o index.js do VPS.
 * O index.js faz POST { token } e espera uma string JS como resposta.
 *
 * Autenticação: token validado contra a coluna `chave` da tabela `config`.
 */
require_once __DIR__ . '/../error_config.php';
require_once __DIR__ . '/../conn.php';

header('Content-Type: text/plain; charset=utf-8');

// ── Validação do token ─────────────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);
$token_enviado = trim($input['token'] ?? '');

if (empty($token_enviado)) {
    http_response_code(401);
    echo 'Token não enviado.';
    exit;
}

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT chave FROM config LIMIT 1"));
$token_esperado = trim($row['chave'] ?? '');

if (empty($token_esperado) || !hash_equals($token_esperado, $token_enviado)) {
    http_response_code(401);
    echo 'Token inválido.';
    exit;
}

// ── Retorna o módulo JavaScript ─────────────────────────────────────────────
echo <<<'JS'
// ═══════════════════════════════════════════════════════════════════════════
// Módulo Baileys — carregado dinamicamente pelo index.js
// Gerado por funcoes_js.php (MoysesNet)
// ═══════════════════════════════════════════════════════════════════════════

const {
  default: makeWASocket,
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
  getContentType,
  downloadMediaMessage
} = require('@whiskeysockets/baileys');

const pino = require('pino');
const path = require('path');
const fs   = require('fs');

const SESSIONS_DIR = '/opt/editacodigo/sessions';

// ── Helpers ─────────────────────────────────────────────────────────────────

function extrairTexto(msg) {
  const c = msg.message;
  if (!c) return '';
  const t = getContentType(c);
  if (t === 'conversation')              return c.conversation || '';
  if (t === 'extendedTextMessage')       return c.extendedTextMessage?.text || '';
  if (t === 'imageMessage')              return c.imageMessage?.caption || '';
  if (t === 'videoMessage')              return c.videoMessage?.caption || '';
  if (t === 'documentMessage')           return c.documentMessage?.caption || '';
  if (t === 'buttonsResponseMessage')    return c.buttonsResponseMessage?.selectedButtonId || '';
  if (t === 'listResponseMessage')       return c.listResponseMessage?.singleSelectReply?.selectedRowId || '';
  if (t === 'templateButtonReplyMessage') return c.templateButtonReplyMessage?.selectedId || '';
  return '';
}

function normalizarTelefone(jid) {
  return (jid || '').replace(/@s\.whatsapp\.net|@g\.us|@c\.us/g, '').replace(/\D/g, '');
}

function toJid(telefone) {
  return telefone.includes('@') ? telefone : telefone + '@s.whatsapp.net';
}

// ── Abre instância WhatsApp ──────────────────────────────────────────────────

module.exports.abrirInstancia = async function(instancias, usuario, enviarWebhook) {
  if (instancias[usuario]?.isConnected) {
    return { status: 'Instância já conectada' };
  }

  const sessionDir = path.join(SESSIONS_DIR, String(usuario));
  if (!fs.existsSync(sessionDir)) fs.mkdirSync(sessionDir, { recursive: true });

  const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
  const { version }          = await fetchLatestBaileysVersion();

  const sock = makeWASocket({
    version,
    auth:               state,
    logger:             pino({ level: 'silent' }),
    printQRInTerminal:  true,
    browser:            ['MoysesNet', 'Chrome', '120.0']
  });

  instancias[usuario] = { sock, qrcode: null, isConnected: false };

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', async ({ connection, lastDisconnect, qr }) => {
    if (qr) {
      instancias[usuario].qrcode = qr;
      qrcode.generate(qr, { small: true });
    }

    if (connection === 'open') {
      instancias[usuario].isConnected = true;
      instancias[usuario].qrcode      = null;
      console.log('[' + usuario + '] Conectado ao WhatsApp!');
      try { await enviarWebhook({ action: 'conectado', usuario }); } catch(e) {}
    }

    if (connection === 'close') {
      instancias[usuario].isConnected = false;
      const code            = lastDisconnect?.error?.output?.statusCode;
      const shouldReconnect = code !== DisconnectReason.loggedOut;
      console.log('[' + usuario + '] Desconectado. Código: ' + code + '. Reconectar: ' + shouldReconnect);
      if (shouldReconnect) {
        setTimeout(() => module.exports.abrirInstancia(instancias, usuario, enviarWebhook), 5000);
      } else {
        delete instancias[usuario];
        try { await enviarWebhook({ action: 'desconectado', usuario }); } catch(e) {}
      }
    }
  });

  sock.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type !== 'notify') return;

    for (const msg of messages) {
      if (msg.key.fromMe || !msg.message) continue;
      if (msg.key.remoteJid?.endsWith('@g.us')) continue; // ignora grupos

      const telefone  = normalizarTelefone(msg.key.remoteJid || '');
      const texto     = extrairTexto(msg);
      const timestamp = Number(msg.messageTimestamp) || Math.floor(Date.now() / 1000);

      let media = null;
      const contentType = getContentType(msg.message);
      const tiposMidia  = ['imageMessage','videoMessage','audioMessage','documentMessage','stickerMessage'];
      if (tiposMidia.includes(contentType)) {
        try {
          const buffer   = await downloadMediaMessage(msg, 'buffer', {});
          const mimeType = msg.message[contentType]?.mimetype || 'application/octet-stream';
          media = { data: buffer.toString('base64'), type: mimeType };
        } catch (e) {
          console.error('Erro ao baixar mídia:', e.message);
        }
      }

      const payload = { telefone, texto, de: msg.key.remoteJid, usuario, timestamp };
      if (media) payload.media = media;

      try {
        await axios.post(WEBHOOK_MENSAGENS, payload, { timeout: 10000 });
      } catch (e) {
        console.error('Erro ao enviar webhook mensagem:', e.message);
      }
    }
  });

  return { status: 'Instância sendo iniciada' };
};

// ── Retorna QR code atual ─────────────────────────────────────────────────

module.exports.gerarQrcode = function(instancias, usuario) {
  const inst = instancias[usuario];
  if (!inst)           return { qrcode: null, status: 'instancia_nao_encontrada' };
  if (inst.isConnected) return { qrcode: null, status: 'conectado' };
  if (inst.qrcode)      return { qrcode: inst.qrcode, status: 'aguardando_scan' };
  return { qrcode: null, status: 'aguardando_qrcode' };
};

// ── Recarrega / Regenera sessão ───────────────────────────────────────────

module.exports.recarregarSessao = async function(instancias, usuario, enviarWebhook) {
  const inst = instancias[usuario];
  if (inst?.sock) {
    try { await inst.sock.logout(); } catch(e) {}
    try { inst.sock.end();          } catch(e) {}
  }
  delete instancias[usuario];

  const sessionDir = path.join(SESSIONS_DIR, String(usuario));
  if (fs.existsSync(sessionDir)) fs.rmSync(sessionDir, { recursive: true, force: true });

  return await module.exports.abrirInstancia(instancias, usuario, enviarWebhook);
};

// ── Fecha instância (mantém sessão) ───────────────────────────────────────

module.exports.fecharInstancia = function(instancias, usuario) {
  const inst = instancias[usuario];
  if (!inst) return { status: 'Instância não encontrada' };
  try { inst.sock.end(); } catch(e) {}
  delete instancias[usuario];
  return { status: 'Instância fechada' };
};

// ── Destrói instância (apaga sessão) ─────────────────────────────────────

module.exports.destruirInstanciaDefinitivamente = async function(instancias, usuario) {
  const inst = instancias[usuario];
  if (inst?.sock) {
    try { await inst.sock.logout(); } catch(e) {}
    try { inst.sock.end();          } catch(e) {}
  }
  delete instancias[usuario];
  const sessionDir = path.join(SESSIONS_DIR, String(usuario));
  if (fs.existsSync(sessionDir)) fs.rmSync(sessionDir, { recursive: true, force: true });
  return { status: 'Instância destruída' };
};

// ── Status de todas as instâncias ────────────────────────────────────────

module.exports.statusInstancias = function(instancias) {
  const resultado = {};
  for (const [id, inst] of Object.entries(instancias)) {
    resultado[id] = {
      conectado:  inst.isConnected || false,
      tem_qrcode: !!(inst.qrcode)
    };
  }
  return resultado;
};

// ── Envia mensagem de texto ───────────────────────────────────────────────

module.exports.enviarMensagem = async function(instancias, usuario, telefone, msg) {
  const inst = instancias[usuario];
  if (!inst?.isConnected) return { error: 'Instância não conectada', usuario };
  await inst.sock.sendMessage(toJid(telefone), { text: msg });
  return { status: 'Mensagem enviada', telefone };
};

// ── Envia mídia ──────────────────────────────────────────────────────────

module.exports.enviarMidia = async function(instancias, usuario, telefone, tipo, arquivo, legenda) {
  const inst = instancias[usuario];
  if (!inst?.isConnected) return { error: 'Instância não conectada' };

  const jid    = toJid(telefone);
  const isUrl  = typeof arquivo === 'string' && arquivo.startsWith('http');
  const src    = isUrl ? { url: arquivo } : Buffer.from(arquivo, 'base64');
  let content  = {};

  if (tipo === 'imagem' || tipo === 'image') {
    content = { image: src, caption: legenda || '' };
  } else if (tipo === 'video') {
    content = { video: src, caption: legenda || '' };
  } else if (tipo === 'audio') {
    content = { audio: src, mimetype: 'audio/mp4', ptt: false };
  } else if (tipo === 'documento' || tipo === 'document') {
    content = { document: src, fileName: legenda || 'arquivo', mimetype: 'application/octet-stream' };
  } else {
    return { error: 'Tipo desconhecido: ' + tipo };
  }

  await inst.sock.sendMessage(jid, content);
  return { status: 'Mídia enviada' };
};

// ── Envia enquete ─────────────────────────────────────────────────────────

module.exports.enviarEnquete = async function(instancias, usuario, telefone, pergunta, opcoes, permiteMultiplas) {
  const inst = instancias[usuario];
  if (!inst?.isConnected) return { error: 'Instância não conectada' };
  const arr = Array.isArray(opcoes) ? opcoes : [];
  await inst.sock.sendMessage(toJid(telefone), {
    poll: {
      name:            pergunta,
      values:          arr,
      selectableCount: permiteMultiplas ? arr.length : 1
    }
  });
  return { status: 'Enquete enviada' };
};

// ── Lista grupos ──────────────────────────────────────────────────────────

module.exports.listarMeusGrupos = async function(instancias, usuario) {
  const inst = instancias[usuario];
  if (!inst?.isConnected) return { error: 'Instância não conectada' };
  const grupos = await inst.sock.groupFetchAllParticipating();
  return Object.entries(grupos).map(([id, g]) => ({
    id,
    nome:          g.subject || '',
    participantes: g.participants?.length || 0
  }));
};

// ── Envia mensagem para grupo ─────────────────────────────────────────────

module.exports.enviarParaGrupo = async function(instancias, usuario, idGrupo, msg, mencoes) {
  const inst = instancias[usuario];
  if (!inst?.isConnected) return { error: 'Instância não conectada' };

  const jid     = idGrupo.includes('@') ? idGrupo : idGrupo + '@g.us';
  const payload = { text: msg };
  if (Array.isArray(mencoes) && mencoes.length > 0) {
    payload.mentions = mencoes.map(m => m.includes('@') ? m : m + '@s.whatsapp.net');
  }

  await inst.sock.sendMessage(jid, payload);
  return { status: 'Mensagem enviada ao grupo' };
};

JS;
