<?php
/**
 * Configuração central de relatório de erros do sistema.
 *
 * Define o comportamento de exibição de erros com base na variável de ambiente APP_ENV.
 * Em desenvolvimento (APP_ENV=dev ou APP_ENV=development) todos os erros são exibidos.
 * Em qualquer outro ambiente (produção, padrão) os erros são suprimidos da saída.
 *
 * Para ativar o modo de desenvolvimento, defina a variável de ambiente:
 *   APP_ENV=dev
 */

if (!defined('APP_ERROR_CONFIG_LOADED')) {
    define('APP_ERROR_CONFIG_LOADED', true);

    $_app_env = getenv('APP_ENV');
    $_is_dev  = ($_app_env === 'development' || $_app_env === 'dev');

    if ($_is_dev) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }

    unset($_app_env, $_is_dev);
}
