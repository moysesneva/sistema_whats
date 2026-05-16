<?php
/**
 * security_headers.php — Cabeçalhos HTTP de segurança centralizados.
 *
 * Incluído automaticamente via error_config.php (que é a base de conn.php
 * e auth_guard.php), cobrindo assim todas as páginas do painel.
 *
 * Cabeçalhos aplicados:
 *   X-Frame-Options        — Previne clickjacking (só permite embedding do próprio domínio)
 *   X-Content-Type-Options — Previne MIME-sniffing pelo navegador
 *   Referrer-Policy        — Limita informações de referrer enviadas a terceiros
 *   Content-Security-Policy — Bloqueia scripts/recursos externos não autorizados
 */
if (defined('SECURITY_HEADERS_SENT')) {
    return;
}
define('SECURITY_HEADERS_SENT', true);

if (headers_sent()) {
    return;
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * Nonce criptograficamente aleatório por requisição.
 * Gerado uma vez e reutilizado por todas as tags <script nonce="..."> da página.
 * Exposto globalmente via $GLOBALS['csp_nonce'] para que templates possam usá-lo.
 */
$csp_nonce = base64_encode(random_bytes(16));
$GLOBALS['csp_nonce'] = $csp_nonce;

/**
 * Content-Security-Policy — dois perfis:
 *
 * Painel (/login/painel/):
 *   script-src sem 'unsafe-inline'; scripts precisam de nonce ou ser arquivos externos.
 *   Essa é a política restrita que protege o painel administrativo de XSS.
 *
 * Páginas públicas (qualquer outra rota):
 *   script-src inclui 'unsafe-inline' para compatibilidade com os scripts inline
 *   das páginas de agendamento público que ainda não foram migradas para nonce.
 *
 * Diretivas comuns:
 *   - default-src 'self'        : bloqueia recursos externos por padrão
 *   - style-src 'unsafe-inline' : estilos inline sempre permitidos (risco menor que script)
 *   - img-src data: blob: https:: imagens locais, data URIs e fontes externas
 *   - font-src                  : fontes locais e CDNs
 *   - connect-src 'self'        : AJAX/fetch só para o próprio domínio
 *   - frame-src youtube         : iframes do próprio domínio e YouTube
 *   - object-src 'none'         : sem plugins (Flash, etc.)
 *   - base-uri 'self'           : previne injeção de <base>
 *   - form-action 'self'        : formulários só para o próprio domínio
 */
$request_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$is_panel     = (strpos($request_path, '/login/painel/') === 0);

if ($is_panel) {
    /* Política restrita — sem 'unsafe-inline' no script-src */
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'nonce-{$csp_nonce}' 'unsafe-eval' https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "img-src 'self' data: blob: https:",
        "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "connect-src 'self'",
        "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
    ]);
} else {
    /* Política permissiva — mantém 'unsafe-inline' para páginas públicas */
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "img-src 'self' data: blob: https:",
        "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "connect-src 'self'",
        "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
    ]);
}
header('Content-Security-Policy: ' . $csp);
