<?php
// Este script cria uma imagem de exemplo do print do WhatsApp
// Definir o tipo de conteúdo como imagem PNG
header('Content-Type: image/png');

// Criar uma imagem de 800x600 pixels
$img = imagecreatetruecolor(800, 600);

// Definir cores
$bg_color = imagecolorallocate($img, 240, 240, 240);
$whatsapp_color = imagecolorallocate($img, 0, 168, 132);
$text_color = imagecolorallocate($img, 50, 50, 50);
$white = imagecolorallocate($img, 255, 255, 255);
$chat_bg = imagecolorallocate($img, 230, 230, 230);
$message_bg = imagecolorallocate($img, 220, 248, 198);
$time_color = imagecolorallocate($img, 120, 120, 120);

// Preencher o fundo
imagefill($img, 0, 0, $bg_color);

// Desenhar a barra superior do WhatsApp
imagefilledrectangle($img, 0, 0, 800, 60, $whatsapp_color);

// Adicionar texto na barra superior
imagestring($img, 5, 70, 20, "WhatsApp", $white);

// Desenhar um círculo para o avatar
imagefilledellipse($img, 35, 30, 40, 40, $white);

// Desenhar a área de chat
imagefilledrectangle($img, 0, 60, 800, 600, $chat_bg);

// Desenhar algumas mensagens de exemplo
// Mensagem 1
imagefilledroundrectangle($img, 20, 100, 400, 150, 10, $white);
imagestring($img, 4, 30, 110, "Ola, como posso ajudar?", $text_color);
imagestring($img, 2, 320, 130, "10:30", $time_color);

// Mensagem 2
imagefilledroundrectangle($img, 400, 180, 780, 230, 10, $message_bg);
imagestring($img, 4, 410, 190, "Preciso de informacoes sobre o produto", $text_color);
imagestring($img, 2, 700, 210, "10:32", $time_color);

// Mensagem 3
imagefilledroundrectangle($img, 20, 260, 400, 310, 10, $white);
imagestring($img, 4, 30, 270, "Claro, qual produto especifico?", $text_color);
imagestring($img, 2, 320, 290, "10:33", $time_color);

// Mensagem 4
imagefilledroundrectangle($img, 400, 340, 780, 390, 10, $message_bg);
imagestring($img, 4, 410, 350, "O novo modelo de smartphone", $text_color);
imagestring($img, 2, 700, 370, "10:35", $time_color);

// Desenhar a barra de entrada de texto
imagefilledrectangle($img, 0, 540, 800, 600, $white);
imagefilledroundrectangle($img, 70, 550, 730, 590, 20, $chat_bg);
imagestring($img, 4, 80, 560, "Digite uma mensagem", $time_color);

// Exibir a imagem
imagepng($img);

// Salvar a imagem como print_whatsapp.png
imagepng($img, 'print_whatsapp.png');

// Liberar memória
imagedestroy($img);

// Função para desenhar retângulos com cantos arredondados
function imagefilledroundrectangle($img, $x1, $y1, $x2, $y2, $radius, $color) {
    // Desenhar retângulo principal
    imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    
    // Desenhar cantos arredondados
    imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}
?> 