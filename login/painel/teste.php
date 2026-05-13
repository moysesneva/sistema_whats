<?php
// Dados de conexão
include 'conn.php';
$sqlFile = __DIR__ . '/banco.sql';
if (!file_exists($sqlFile)) {
    die("Arquivo banco.sql não encontrado!");
}

$sql = file_get_contents($sqlFile);

// ==================== LIMPEZA DO DUMP ==================== //
// Remove DELIMITER
$sql = str_replace(["DELIMITER $$", "DELIMITER ;"], "", $sql);

// Substitui $$ por ;
$sql = str_replace("$$", ";", $sql);

// Remove DEFINER=...
$sql = preg_replace('/DEFINER=`[^`]+`@`[^`]+`/i', '', $sql);

// Adiciona DROP PROCEDURE IF EXISTS antes de cada CREATE PROCEDURE
$sql = preg_replace_callback(
    '/CREATE\s+PROCEDURE\s+`?([a-zA-Z0-9_]+)`?/i',
    function ($m) {
        return "DROP PROCEDURE IF EXISTS `{$m[1]}`;\nCREATE PROCEDURE `{$m[1]}`";
    },
    $sql
);

// ======================================================== //

// Executa o SQL
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    echo "Banco de dados importado com sucesso.<br>";
} else {
    die("<strong>ERRO AO IMPORTAR O BANCO:</strong> " . $conn->error . "<br>");
}

$conn->close();
?>