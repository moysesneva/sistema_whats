<?php
// Define o valor da cor de fundo do cabeçalho
$cor1 = "theme2"; // Defina o valor que deseja

// Retorna o valor de configuração em formato JSON
header('Content-Type: application/json');
echo json_encode(["tema_lateral" => $cor1]);
?>
