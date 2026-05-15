<?php
/**
 * Cliente de Instalação de Módulos
 * 
 * Este script se conecta à sua API de módulos, baixa os arquivos necessários
 * e instala automaticamente no site do cliente.
 */

class ModuloInstalador {
    private $api_url;
    private $usuario_id;
    private $api_key;
    private $base_dir;
    private $conn;
    
    /**
     * Construtor
     * 
     * @param string $api_url URL base da API de módulos
     * @param string $usuario_id ID do usuário na plataforma
     * @param string $api_key Chave de API para autenticação
     * @param string $base_dir Diretório base para instalação dos arquivos
     * @param object $conn Conexão com o banco de dados
     */
    public function __construct($api_url, $usuario_id, $api_key, $base_dir, $conn) {
        $this->api_url = rtrim($api_url, '/');
        $this->usuario_id = $usuario_id;
        $this->api_key = $api_key;
        $this->base_dir = rtrim($base_dir, '/');
        $this->conn = $conn;
    }
    
    /**
     * Lista todos os módulos disponíveis
     * 
     * @param int $categoria_id ID da categoria para filtrar (opcional)
     * @return array Lista de módulos
     */
    public function listarModulos($categoria_id = 0) {
        $url = $this->api_url . '/api_modulos.php?acao=listar&usuario=' . 
               urlencode($this->usuario_id) . '&api_key=' . urlencode($this->api_key);
        
        if ($categoria_id > 0) {
            $url .= '&categoria_id=' . intval($categoria_id);
        }
        
        $response = $this->fazerRequisicao($url);
        return json_decode($response, true);
    }
    
    /**
     * Lista módulos comprados pelo usuário
     * 
     * @return array Lista de módulos comprados
     */
    public function listarModulosComprados() {
        $url = $this->api_url . '/api_modulos.php?acao=listar_comprados&usuario=' . 
               urlencode($this->usuario_id) . '&api_key=' . urlencode($this->api_key);
        
        $response = $this->fazerRequisicao($url);
        return json_decode($response, true);
    }
    
    /**
     * Obtém detalhes de um módulo específico
     * 
     * @param string $modulo_id ID do módulo
     * @return array Detalhes do módulo
     */
    public function obterDetalhesModulo($modulo_id) {
        $url = $this->api_url . '/api_modulos.php?acao=detalhes&id=' . 
               urlencode($modulo_id) . '&usuario=' . urlencode($this->usuario_id) . 
               '&api_key=' . urlencode($this->api_key);
        
        $response = $this->fazerRequisicao($url);
        return json_decode($response, true);
    }
    
    /**
     * Instala um módulo completo
     * 
     * @param string $modulo_id ID do módulo a ser instalado
     * @return array Resultado da instalação
     */
    public function instalarModulo($modulo_id) {
        $resultado = [
            'sucesso' => false,
            'mensagens' => [],
            'erros' => []
        ];
        
        // Verifica se o módulo existe e se o usuário tem acesso
        $modulo = $this->obterDetalhesModulo($modulo_id);
        
        if (isset($modulo['erro'])) {
            $resultado['erros'][] = 'Erro ao obter detalhes do módulo: ' . $modulo['erro'];
            return $resultado;
        }
        
        if ($modulo['tipo'] == 'pago' && empty($modulo['comprado'])) {
            $resultado['erros'][] = 'Este módulo é pago e você não o adquiriu ainda.';
            return $resultado;
        }
        
        // Cria o diretório para o módulo se não existir
        $modulo_dir = $this->base_dir . '/modulos/' . $modulo_id;
        if (!is_dir($modulo_dir)) {
            if (!mkdir($modulo_dir, 0755, true)) {
                $resultado['erros'][] = 'Não foi possível criar o diretório para o módulo.';
                return $resultado;
            }
        }
        
        // Instala os arquivos SQL se existirem
        if (!empty($modulo['sql_arquivos_lista'])) {
            $resultado_sql = $this->instalarSQL($modulo_id, $modulo['sql_arquivos_lista']);
            $resultado['mensagens'] = array_merge($resultado['mensagens'], $resultado_sql['mensagens']);
            $resultado['erros'] = array_merge($resultado['erros'], $resultado_sql['erros']);
        }
        
        // Executa script SQL direto se existir
        if (!empty($modulo['sql_codigo'])) {
            $resultado_sql_direto = $this->executarSQL($modulo['sql_codigo']);
            if ($resultado_sql_direto['sucesso']) {
                $resultado['mensagens'][] = 'Script SQL executado com sucesso.';
            } else {
                $resultado['erros'][] = 'Erro ao executar script SQL: ' . $resultado_sql_direto['erro'];
            }
        }
        
        // Baixa e instala os arquivos do módulo
        if (!empty($modulo['arquivos_lista'])) {
            $resultado_arquivos = $this->instalarArquivos($modulo_id, $modulo['arquivos_lista']);
            $resultado['mensagens'] = array_merge($resultado['mensagens'], $resultado_arquivos['mensagens']);
            $resultado['erros'] = array_merge($resultado['erros'], $resultado_arquivos['erros']);
        }
        
        // Verifica se houve erros na instalação
        if (count($resultado['erros']) == 0) {
            $resultado['sucesso'] = true;
            $resultado['mensagens'][] = 'Módulo instalado com sucesso!';
        }
        
        return $resultado;
    }
    
    /**
     * Instala os arquivos SQL do módulo
     * 
     * @param string $modulo_id ID do módulo
     * @param array $arquivos_sql Lista de arquivos SQL
     * @return array Resultado da instalação
     */
    private function instalarSQL($modulo_id, $arquivos_sql) {
        $resultado = [
            'sucesso' => true,
            'mensagens' => [],
            'erros' => []
        ];
        
        foreach ($arquivos_sql as $arquivo) {
            // Obtém o conteúdo do arquivo SQL
            $url = $this->api_url . '/api_modulos.php?acao=arquivo&id=' . 
                   urlencode($modulo_id) . '&arquivo=' . urlencode($arquivo) . 
                   '&usuario=' . urlencode($this->usuario_id) . 
                   '&api_key=' . urlencode($this->api_key);
            
            $sql_content = $this->fazerRequisicao($url);
            
            // Verifica se o conteúdo parece ser JSON (indicando erro)
            $json_check = json_decode($sql_content, true);
            if (is_array($json_check) && isset($json_check['erro'])) {
                $resultado['erros'][] = 'Erro ao baixar arquivo SQL ' . $arquivo . ': ' . $json_check['erro'];
                continue;
            }
            
            // Executa o script SQL
            $resultado_sql = $this->executarSQL($sql_content);
            if ($resultado_sql['sucesso']) {
                $resultado['mensagens'][] = 'Arquivo SQL ' . $arquivo . ' executado com sucesso.';
            } else {
                $resultado['erros'][] = 'Erro ao executar arquivo SQL ' . $arquivo . ': ' . $resultado_sql['erro'];
                $resultado['sucesso'] = false;
            }
        }
        
        return $resultado;
    }
    
    /**
     * Executa um script SQL
     * 
     * @param string $sql Script SQL a ser executado
     * @return array Resultado da execução
     */
    private function executarSQL($sql) {
        $resultado = [
            'sucesso' => false,
            'erro' => ''
        ];
        
        // Divide o script em consultas individuais para execução
        $queries = $this->splitSQL($sql);
        
        foreach ($queries as $query) {
            if (trim($query) == '') continue;
            
            if (!mysqli_query($this->conn, $query)) {
                $resultado['erro'] = mysqli_error($this->conn);
                return $resultado;
            }
        }
        
        $resultado['sucesso'] = true;
        return $resultado;
    }
    
    /**
     * Divide um script SQL em consultas individuais
     * 
     * @param string $sql Script SQL completo
     * @return array Lista de consultas individuais
     */
    private function splitSQL($sql) {
        $queries = [];
        $currentQuery = '';
        $inString = false;
        $stringChar = '';
        
        // Divide o script em consultas individuais
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            $nextChar = isset($sql[$i + 1]) ? $sql[$i + 1] : '';
            
            // Controla strings para não confundir com delimitadores de consulta
            if (($char == "'" || $char == '"') && ($i == 0 || $sql[$i - 1] != '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char == $stringChar) {
                    $inString = false;
                }
            }
            
            $currentQuery .= $char;
            
            // Detecta o fim de uma consulta
            if ($char == ';' && !$inString) {
                $queries[] = $currentQuery;
                $currentQuery = '';
            }
        }
        
        // Adiciona a última consulta se não terminar com ;
        if (trim($currentQuery) != '') {
            $queries[] = $currentQuery;
        }
        
        return $queries;
    }
    
    /**
     * Baixa e instala os arquivos do módulo
     * 
     * @param string $modulo_id ID do módulo
     * @param array $arquivos Lista de arquivos
     * @return array Resultado da instalação
     */
    private function instalarArquivos($modulo_id, $arquivos) {
        $resultado = [
            'sucesso' => true,
            'mensagens' => [],
            'erros' => []
        ];
        
        // Diretório de destino para o módulo
        $modulo_dir = $this->base_dir . '/modulos/' . $modulo_id;
        
        foreach ($arquivos as $arquivo) {
            // Obtém o conteúdo do arquivo
            $url = $this->api_url . '/api_modulos.php?acao=arquivo&id=' . 
                   urlencode($modulo_id) . '&arquivo=' . urlencode($arquivo) . 
                   '&usuario=' . urlencode($this->usuario_id) . 
                   '&api_key=' . urlencode($this->api_key);
            
            $conteudo = $this->fazerRequisicao($url);
            
            // Verifica se o conteúdo parece ser JSON (indicando erro)
            $json_check = json_decode($conteudo, true);
            if (is_array($json_check) && isset($json_check['erro'])) {
                $resultado['erros'][] = 'Erro ao baixar arquivo ' . $arquivo . ': ' . $json_check['erro'];
                continue;
            }
            
            // Salva o arquivo no diretório de destino
            $caminho_destino = $modulo_dir . '/' . $arquivo;
            
            // Cria diretórios aninhados se necessário
            $diretorio = dirname($caminho_destino);
            if (!is_dir($diretorio)) {
                if (!mkdir($diretorio, 0755, true)) {
                    $resultado['erros'][] = 'Não foi possível criar o diretório para o arquivo ' . $arquivo;
                    continue;
                }
            }
            
            if (file_put_contents($caminho_destino, $conteudo) === false) {
                $resultado['erros'][] = 'Erro ao salvar o arquivo ' . $arquivo;
                $resultado['sucesso'] = false;
            } else {
                $resultado['mensagens'][] = 'Arquivo ' . $arquivo . ' instalado com sucesso.';
            }
        }
        
        return $resultado;
    }
    
    /**
     * Realiza uma requisição HTTP para a API
     * 
     * @param string $url URL da requisição
     * @return string Resposta da requisição
     */
    private function fazerRequisicao($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
