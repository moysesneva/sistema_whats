<?php
// -----------------------------------------------------------------------
// Configuração central dos banners de aviso do painel.
// Altere APENAS este arquivo para controlar quem vê cada banner — sem
// tocar em header.php ou disk_warning_banner.php.
// -----------------------------------------------------------------------

// Tipos de usuário (campo `tipo` da tabela `login`) que enxergam o banner
// de uso de disco. Adicione ou remova valores conforme os papéis do sistema.
if (!defined('DISK_WARNING_ROLES')) {
    define('DISK_WARNING_ROLES', [1, 4]);
}

// Tipos de usuário que enxergam o aviso de API_WEBHOOK_TOKEN não configurado.
// Mantido separado de DISK_WARNING_ROLES para que cada banner possa ser
// ajustado de forma independente.
if (!defined('API_TOKEN_WARNING_ROLES')) {
    define('API_TOKEN_WARNING_ROLES', [1, 4]);
}

// Limiar de uso de disco (em MB) a partir do qual o banner é exibido.
if (!defined('DISK_WARN_THRESHOLD_MB')) {
    define('DISK_WARN_THRESHOLD_MB', 50);
}
