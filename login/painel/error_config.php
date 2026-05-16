<?php
/**
 * Configuração central de relatório de erros do sistema.
 *
 * Define o comportamento de exibição e registro de erros com base na variável de ambiente APP_ENV.
 * Em desenvolvimento (APP_ENV=dev ou APP_ENV=development) todos os erros são exibidos na tela
 * e também registrados na saída de erro padrão (stderr).
 * Em qualquer outro ambiente (produção, padrão) os erros são suprimidos da saída mas
 * registrados em arquivo para facilitar o diagnóstico sem expor detalhes ao usuário.
 * Em produção, uma página de erro genérica é exibida ao usuário em lugar de stack traces.
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

        /**
         * Exibe a página de erro genérica ao usuário sem expor detalhes técnicos.
         * Usa APP_ERRO_EXIBIDO para garantir que a página seja renderizada apenas uma vez,
         * mesmo que múltiplos handlers (set_error_handler + register_shutdown_function)
         * sejam acionados pelo mesmo erro fatal.
         */
        function _app_mostrar_erro_generico(): void
        {
            if (defined('APP_ERRO_EXIBIDO')) {
                return;
            }
            define('APP_ERRO_EXIBIDO', true);

            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=UTF-8');
            }
            $pagina = __DIR__ . '/erro_generico.php';
            if (file_exists($pagina)) {
                include $pagina;
            } else {
                echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
                    . '<title>Erro — MoysesNet</title></head><body>'
                    . '<p>Ocorreu um erro inesperado. Tente novamente em instantes.</p>'
                    . '</body></html>';
            }
        }

        /**
         * Intercepta exceções não tratadas — registra no log e mostra página genérica.
         */
        set_exception_handler(function (Throwable $e): void {
            error_log(
                'Exceção não tratada: ' . get_class($e)
                . ' | ' . $e->getMessage()
                . ' em ' . $e->getFile() . ':' . $e->getLine()
            );
            _app_mostrar_erro_generico();
        });

        /**
         * Intercepta erros PHP (E_USER_ERROR, E_RECOVERABLE_ERROR, etc.) —
         * registra no log e mostra página genérica para erros fatais.
         * Retorna false para os demais níveis a fim de preservar o comportamento padrão do PHP.
         */
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
            $fatal_levels = E_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
            if ($errno & $fatal_levels) {
                error_log(
                    'Erro PHP [' . $errno . ']: ' . $errstr
                    . ' em ' . $errfile . ':' . $errline
                );
                _app_mostrar_erro_generico();
                exit(1);
            }
            return false;
        });

        /**
         * Captura erros fatais que o set_error_handler não consegue interceptar
         * (E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR).
         * A constante APP_ERRO_EXIBIDO evita renderização duplicada quando
         * set_error_handler já tratou o erro antes do shutdown.
         */
        register_shutdown_function(function (): void {
            $erro = error_get_last();
            if ($erro === null) {
                return;
            }
            $fatal_levels = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR
                          | E_USER_ERROR | E_RECOVERABLE_ERROR;
            if ($erro['type'] & $fatal_levels) {
                error_log(
                    'Erro fatal [' . $erro['type'] . ']: ' . $erro['message']
                    . ' em ' . $erro['file'] . ':' . $erro['line']
                );
                _app_mostrar_erro_generico();
            }
        });
    }

    unset($_app_env, $_is_dev);
}
