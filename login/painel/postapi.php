<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
// postapi.php
// Salvar todas as requisições num log

// Monta array com tudo que foi recebido
$dadosRecebidos = [
    'data_hora' => date("Y-m-d H:i:s"),
    'POST' => $_POST,
    'GET'  => $_GET,
    'SERVER' => [
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
    ]
];

// Transforma em JSON
$linhaLog = json_encode($dadosRecebidos, JSON_UNESCAPED_UNICODE) . PHP_EOL;

// Salva no arquivo
file_put_contents("log_requisicoes.txt", $linhaLog, FILE_APPEND);

// Retorna resposta simples
echo "Requisição registrada.";
?>

