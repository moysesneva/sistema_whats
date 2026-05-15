<?php

#include 'conn.php';

// Consulta para buscar todos os dados da tabela config
$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

// Verifica se existem resultados e atribui os valores às variáveis
if ($total_config > 0) {
    $rows_config = mysqli_fetch_array($query_config);
    
    $ip_vps = $rows_config['ip_vps'];
    $porta = $rows_config['porta'];
    $nova_porta = $rows_config['nova_porta'];
    $token = $rows_config['chave'];
    $caminho_modelo = $rows_config['caminho_modelo'];
    $chave_painel = $rows_config['chave_painel'];
    $webhook = $rows_config['webhook'];
    $validade = $rows_config['validade'];
    $webhook_completo = $rows_config['webhook_completo'];
    $google = $rows_config['google']; // Caso exista este campo
    
    }
    ?>