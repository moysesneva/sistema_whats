<?php
// Função Vaipara para redirecionamento
function Vaipara($site) {
    echo "<script>window.location.href = '$site';</script>";
    exit; // Garante que o script PHP pare de executar
}

// Obtém o mês e o ano atual
$mesAtual = date('n'); // Mês atual (numérico, 1 a 12)
$anoAtual = date('Y'); // Ano atual (ex.: 2025)

// Redireciona usando a função Vaipara
Vaipara("agenda2_porf.php?m=$mesAtual&y=$anoAtual");
?>
