<?php
/**
 * Configuração central de relatório de erros do sistema.
 *
 * Define o comportamento de exibição e registro de erros com base na variável de ambiente APP_ENV.
 * Em desenvolvimento (APP_ENV=dev ou APP_ENV=development) todos os erros são exibidos na tela
 * e também registrados na saída de erro padrão (stderr).
 * Em qualquer outro ambiente (produção, padrão) os erros são suprimidos da saída mas
 * registrados em arquivo para facilitar o diagnóstico sem expor detalhes ao usuário.
 *
 * Para ativar o modo de desenvolvimento, defina a variável de ambiente:
 *   APP_ENV=dev
 *
 * O arquivo de log de produção pode ser configurado pela variável PHP_ERROR_LOG.
 * Padrão: /tmp/php_errors.log
 */

if (!defined('APP_ERROR_CONFIG_LOADED')) {
    define('APP_ERROR_CONFIG_LOADED', true);

    $_app_env = getenv('APP_ENV');
    $_is_dev  = ($_app_env === 'development' || $_app_env === 'dev');

    if ($_is_dev) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ini_set('log_errors', 1);
        ini_set('error_log', 'php://stderr');
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);
        ini_set('log_errors', 1);
        $_log_file = getenv('PHP_ERROR_LOG') ?: '/tmp/php_errors.log';
        ini_set('error_log', $_log_file);
        unset($_log_file);
    }

    unset($_app_env, $_is_dev);
}
