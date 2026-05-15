<?php
// URL da página PHP externa que retorna JSON com o número de telefone
$url = 'https://editacodigo.com.br/contato.php';

// Faz o GET da página externa
$resposta = file_get_contents($url);

// Verifica se houve erro
if ($resposta === FALSE) {
    die('Erro ao acessar a URL externa.');
}

// Decodifica o JSON recebido
$dados = json_decode($resposta, true);

// Verifica se o número está presente
if (!isset($dados['telefone'])) {
    die('Telefone não encontrado no JSON.');
}

// Extrai o número e formata para o link do WhatsApp
$numero = preg_replace('/\D/', '', $dados['telefone']); // remove tudo que não for número

// Redireciona para o link do WhatsApp
header("Location: https://wa.me/$numero");
exit;
?>
