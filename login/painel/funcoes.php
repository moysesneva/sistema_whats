<?php
$termo = 'agenda_';

function VaiPara($pagina) {
    if (!empty($pagina)) {
        echo "<script type='text/javascript'>
                window.location.href = '$pagina';
              </script>";
        exit; // Importante: para interromper a execução e evitar conteúdo extra
    } else {
        echo "<script type='text/javascript'>
                console.error('Erro: Nenhuma página definida para redirecionamento.');
              </script>";
        exit; // Também encerra mesmo com erro
    }
}


function Priletra($string) {
    // Transforma toda a string em minúscula, exceto a primeira letra
    $string = strtolower($string);
    // Converte a primeira letra em maiúscula
    $string = ucfirst($string);

    return $string;
}


function GeraNumero() {
    // Gera um número aleatório de 5 dígitos
    return rand(10000, 99999);
}

// Exemplo de uso


function barra($url) {
    // Verifica se a URL começa com http:// ou https://
    if (preg_match('/^(http[s]?:\/\/)/', $url, $matches)) {
        // Extrai o prefixo (http:// ou https://)
        $prefix = $matches[0];
        // Remove o prefixo da URL
        $rest_of_url = substr($url, strlen($prefix));

        // Substitui barras duplas por uma única barra
        $rest_of_url = preg_replace('/\/{2,}/', '/', $rest_of_url);

        // Substitui ocorrências de dois pontos duplos por um único dois-pontos
        $rest_of_url = preg_replace('/:{2,}/', ':', $rest_of_url);

        // Retorna a URL corrigida com o prefixo original
        return $prefix . $rest_of_url;
    }

    // Caso não tenha http:// ou https://, apenas retorna a URL original
    return $url;
}



function espera($segundos = 5) {
    echo "
    <script>
        setTimeout(function() {
            document.getElementById('espera-iniciar').style.display = 'block';
        }, " . ($segundos * 1000) . ");
    </script>";
}


function So_numeros($string) {
    // Remove todos os caracteres que não sejam números
    return preg_replace('/\D/', '', $string);
}





function hora() {
    // Define o fuso horário para o horário de Brasília (UTC-3)
    date_default_timezone_set('America/Sao_Paulo');
    
    // Retorna a hora atual formatada
    return date('H:i:s');
}




function salvar_dados_resquest() {

// Nome do arquivo onde os dados serão salvos
$filename = 'post.txt';

// Verifica se a requisição é POST e se contém dados JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados JSON do corpo da requisição POST
    $jsonData = file_get_contents('php://input');

    // Decodifica o JSON para um array associativo
    $decodedData = json_decode($jsonData, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Se a decodificação for bem-sucedida, verifica se há dados na chave "opcoes"
        if (isset($decodedData['opcoes'])) {
            // Decodifica a string JSON dentro de 'opcoes' duas vezes para resolver a questão de caracteres escapados
            $decodedOpcoes = json_decode($decodedData['opcoes'], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                // Substitui a string escapada pelo array decodificado
                $decodedData['opcoes'] = $decodedOpcoes;
            } else {
                // Se a decodificação falhar, mantém o valor original
                $decodedData['opcoes'] = $decodedData['opcoes'];
            }
        }

        // Formata os dados como JSON para salvar
        $data = json_encode([
            'GET' => $_GET,
            'POST' => $decodedData
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        // Se a decodificação falhar, armazena os dados brutos
        $data = json_encode([
            'GET' => $_GET,
            'POST' => $_POST
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} else {
    // Se não for um POST, processa GET e POST normalmente
    $data = json_encode([
        'GET' => $_GET,
        'POST' => $_POST
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Salva os dados no arquivo 'qrcode.txt' com codificação UTF-8
file_put_contents($filename, $data);

// Mensagem de confirmação
echo "Dados salvos em $filename com sucesso!";

}




function compara_segundos($hora1, $hora2) {
    // Converte as horas em timestamps
    $timestamp1 = strtotime($hora1);
    $timestamp2 = strtotime($hora2);
    
    // Calcula a diferença em segundos
    $diferenca = abs($timestamp2 - $timestamp1);
    
    return $diferenca;
}



function MsgTexto($conn, $msg, $telefone, $usuario_api) {
    // Escapar as variáveis para evitar SQL Injection
    $msg = mysqli_real_escape_string($conn, $msg);
    $telefone = mysqli_real_escape_string($conn, $telefone);
    $usuario_api = mysqli_real_escape_string($conn, $usuario_api);
    
    // Montar o SQL de inserção
    $sql = "INSERT INTO envio (comando,msg, telefone, status, usuario_api) 
            VALUES ('MsgTexto','$msg', '$telefone', '2', '$usuario_api')";

    // Executar a query
    if (mysqli_query($conn, $sql)) {
        return "Mensagem enviada com sucesso!";
    } else {
        return "Erro ao enviar mensagem: " . mysqli_error($conn);
    }
}

?>
<?php
function gerarVariavelAleatoria() {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $tamanho = 5;
    $variavelAleatoria = '';

    for ($i = 0; $i < $tamanho; $i++) {
        $indice = rand(0, strlen($caracteres) - 1);
        $variavelAleatoria .= $caracteres[$indice];
    }

    return $variavelAleatoria;
}

// Exemplo de uso
$id_agenda = gerarVariavelAleatoria();
//echo $variavel;






function verificarIdOuRedirecionar() {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        // Redireciona para index.php se o id não existir ou estiver vazio
        header("Location: index.php");
        exit;
    }
}

?>

