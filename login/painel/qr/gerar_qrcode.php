<?php
session_start();
include('phpqrcode/qrlib.php');
include '../conn.php';
#include '../api/api_funcao.php';
include '../api/editacodigo.php';

include '../funcoes.php';
$login = $_SESSION['login'];
#include '../salvajson.php';



#salvarRequisicao();



// Captura os par창metros da URL (GET)



$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}










$user_id = isset($_GET['usuario']) ? $_GET['usuario'] : null;


function salvaTXT($string, $nomeArquivo = 'saida.txt') {
    // Abre (ou cria) o arquivo no modo de escrita
    $arquivo = fopen($nomeArquivo, 'a'); // 'a' = append (adiciona no final)

    if ($arquivo) {
        fwrite($arquivo, $string . PHP_EOL); // Salva a string e quebra linha
        fclose($arquivo);
        echo "Arquivo salvo com sucesso em {$nomeArquivo}!";
    } else {
        echo "Erro ao abrir o arquivo.";
    }
}




$qrcode = gerarQrcode($servidor, $porta, $user_id, $token);
#echo $qrcode ;
#exit();
#trim($qrcode);


if ($qrcode == 'Já conectado') {
    // Exibe círculo verde com texto
    echo '
    <div class="text-center mt-4">
        <div style="
            width: 100px;
            height: 100px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            ">
            <i class="feather icon-check" style="color: white; font-size: 48px;"></i>
        </div>
        <p class="text-success font-weight-bold mt-2" style="font-size: 18px;">Conectado</p>
    </div>
    ';
exit();
}

#$qrcode = print_r($qrcode);
#$qrcode = $qrcode['qrcode'];
#salvaTXT($qrcode);
 if (strpos($qrcode, 'Erro') !== false){

 $imagemErro = 'qr/nqr.png'; // Substitua pelo caminho da imagem de erro
        echo '<img src="' . $imagemErro . '" class="img-fluid mx-auto d-block" alt="Erro ao gerar QR Code">';

}else{
    $nome_arquivo = $user_id . "-temp.png";

$caminho_temporario = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nome_arquivo;

Qrcode::png($qrcode,$caminho_temporario,QR_ECLEVEL_L,5);

#exibirQrCode($qrcode);
           

           echo '<div style="text-align: center; margin-top: 20px;">
        <img src="data:image/png;base64,' . base64_encode(file_get_contents($caminho_temporario)) . '" alt="QR Code" />
      </div>';

    
}

    






    
?>