<?php
// Link de fallback (com ID real será redirecionado via JS depois)
$id = $_GET['id'] ?? '';
$titulo = "Agende com Inteligência – Tasmota";
$descricao = "Clique para agendar online com automação e inteligência artificial!";
$url = "https://agenda.tasmota.com.br/agenda/agendar.php?id=" . urlencode($id);
$imagem = "https://agenda.tasmota.com.br/agenda/logo.png"; // Esta imagem precisa estar disponível publicamente via HTTPS
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?></title>

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($titulo) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($descricao) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($url) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($imagem) ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter / WhatsApp -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($titulo) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($descricao) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($imagem) ?>">

    <!-- Redirecionamento automático (para humanos) -->
    <meta http-equiv="refresh" content="2; url=<?= htmlspecialchars($url) ?>">
</head>
<body>
    <p>Carregando agendamento... Se não redirecionar, <a href="<?= htmlspecialchars($url) ?>">clique aqui</a>.</p>
</body>
</html>