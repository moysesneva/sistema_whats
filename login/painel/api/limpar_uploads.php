<?php
/**
 * Script CLI para remover arquivos temporários de imagem expirados.
 * Executado periodicamente por scripts/limpar_uploads.sh.
 *
 * Threshold configurável via variável de ambiente:
 *   UPLOADS_MAX_AGE_SECONDS  (padrão: 3600 segundos = 1 hora)
 */

$idadeMaxima = (int) (getenv('UPLOADS_MAX_AGE_SECONDS') ?: 3600);

$pasta = __DIR__ . '/img/';

if (!is_dir($pasta)) {
    exit(0);
}

$agora     = time();
$removidos = 0;

foreach (glob($pasta . 'imagem_*.png') as $arquivo) {
    if (is_file($arquivo) && ($agora - filemtime($arquivo)) > $idadeMaxima) {
        if (unlink($arquivo)) {
            $removidos++;
        }
    }
}

$timestamp = date('Y-m-d H:i:s');
echo "[$timestamp] limpar_uploads: $removidos arquivo(s) removido(s) (threshold={$idadeMaxima}s)\n";
// Linha estruturada para leitura pelo script de varredura
echo "REMOVED_COUNT={$removidos}\n";
