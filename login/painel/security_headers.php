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
 * Content-Security-Policy básico.
 *
 * - default-src 'self'           : bloqueia qualquer recurso externo por padrão
 * - script-src 'self' 'unsafe-inline' 'unsafe-eval' : permite scripts locais e inline (jQuery, plugins)
 * - style-src 'self' 'unsafe-inline'  : permite estilos locais e inline (Bootstrap, tema)
 * - img-src 'self' data: blob: https: : permite imagens locais, data URIs e fontes externas (QR, avatares)
 * - font-src 'self' data:         : permite fontes locais e embutidas (base64)
 * - connect-src 'self'            : permite requisições AJAX/fetch apenas para o próprio domínio
 * - frame-src 'none'              : bloqueia iframes de qualquer origem
 * - object-src 'none'             : bloqueia plugins (Flash, etc.)
 * - base-uri 'self'               : previne injeção de <base> apontando para domínio externo
 * - form-action 'self'            : impede que formulários enviem dados a domínios externos
 */
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
header('Content-Security-Policy: ' . $csp);
