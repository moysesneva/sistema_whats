<?php
include 'login/painel/conn.php';

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave  = $rows_config['chave'];
    $validade  = $rows_config['validade'];
    $link_pagamento =$rows_config['link_pagamento'];
    $preco  = $rows_config['preco'];
    $telefone  = $rows_config['telefone'];
    $webhook  = $rows_config['webhook'];
    $imagem_fundo_pagina  = $rows_config['caminho_modelo'];

}



$url = $webhook .'/login/painel/'. $imagem_fundo_pagina;
// Variáveis PHP para definir as cores de fundo
$cor_fundo_hero = "linear-gradient(to right, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.1)), url('$url')"; // Cor de fundo para a seção hero
// Variável PHP para o número de telefone do WhatsApp
$telefone_whatsapp = '(31) 98765-4321'; // Substitua pelo seu número de WhatsApp

?>


   <style>
        /* Cor de fundo do corpo da página */
       

        /* Seção Hero (destaque principal) */
        .hero-section {
            background-image: <?php echo $cor_fundo_hero; ?>;
            background-position: 50% 50%;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100%;
            width: 100%;
        }

      
    </style>