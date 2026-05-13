<?php

function salvar() {
    // Captura o método da requisição
    $metodo = $_SERVER['REQUEST_METHOD'];

    // Captura os dados da requisição
    $dados = file_get_contents('php://input');

    // Se for GET, usa $_GET, senão, usa os dados brutos
    if ($metodo === 'GET') {
        $dados = $_GET;
    } else {
        $dados = json_decode($dados, true) ?: $_POST;
    }

    // Cria um array com as informações da requisição
    $requisicao = [
        'metodo' => $metodo,
        'dados' => $dados,
        'data_hora' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    // Nome do arquivo de log
    $arquivo = 'logs.json';

    // Lê logs anteriores
    $logs = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

    // Adiciona a nova requisição ao array
    $logs[] = $requisicao;

    // Salva o JSON atualizado no arquivo
    file_put_contents($arquivo, json_encode($logs, JSON_PRETTY_PRINT));

    // Retorna uma resposta JSON
    return json_encode(['status' => 'sucesso', 'mensagem' => 'Requisição salva com sucesso']);
}

// Chamada da função
header('Content-Type: application/json');

?>
<?php

function salvando($string) {
    $arquivo = 'log.json';

    // Verifica se o arquivo já existe
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) {
            $dados = [];
        }
    } else {
        $dados = [];
    }

    // Adiciona a string diretamente
    $dados[] = $string;

    // Salva novamente no JSON
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

# ✅ Exemplo de uso:
#salvando("Iniciando sistema");


?>
