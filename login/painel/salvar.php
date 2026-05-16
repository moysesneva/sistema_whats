<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
// menu.php

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    // Recebe as configurações enviadas via AJAX
    $settings = $_POST['settings'];

    // Salva as configurações em um arquivo .txt
    $file = 'settings.txt';
    $content = json_encode($settings, JSON_PRETTY_PRINT);
    file_put_contents($file, $content);

    // Retorna uma resposta de sucesso
    echo json_encode(['status' => 'success']);
    exit;
}

// Carrega as configurações salvas, se existirem
$savedSettings = [];
if (file_exists('settings.txt')) {
    $content = file_get_contents('settings.txt');
    $savedSettings = json_decode($content, true);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Menu Personalizado</title>
    <!-- Inclua o jQuery -->
    <script src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <!-- Inclua seu arquivo JavaScript -->
    <script src="pcoded.min.js"></script>
    <style>
        /* Estilos básicos para demonstração */
        body {
            font-family: Arial, sans-serif;
        }
        #menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        #menu.vertical {
            flex-direction: column;
        }
        #menu.horizontal {
            flex-direction: row;
        }
        #menu li {
            padding: 10px 20px;
            background-color: #ccc;
            margin: 5px;
            cursor: pointer;
        }
        #menu li.active {
            background-color: #aaa;
        }
    </style>
</head>
<body>

<h1>Menu Personalizado</h1>

<!-- Formulário para selecionar opções -->
<div>
    <label for="themelayout">Layout do Tema:</label>
    <select id="themelayout">
        <option value="vertical" <?= isset($savedSettings['themelayout']) && $savedSettings['themelayout'] === 'vertical' ? 'selected' : '' ?>>Vertical</option>
        <option value="horizontal" <?= isset($savedSettings['themelayout']) && $savedSettings['themelayout'] === 'horizontal' ? 'selected' : '' ?>>Horizontal</option>
    </select>

    <label for="MenuTrigger">Menu Trigger:</label>
    <select id="MenuTrigger">
        <option value="click" <?= isset($savedSettings['MenuTrigger']) && $savedSettings['MenuTrigger'] === 'click' ? 'selected' : '' ?>>Click</option>
        <option value="hover" <?= isset($savedSettings['MenuTrigger']) && $savedSettings['MenuTrigger'] === 'hover' ? 'selected' : '' ?>>Hover</option>
    </select>

    <!-- Adicione outros campos conforme necessário -->
</div>

<!-- Menu -->
<ul id="menu" class="<?= isset($savedSettings['themelayout']) ? $savedSettings['themelayout'] : 'vertical' ?>">
    <li id="item1">Item 1</li>
    <li id="item2">Item 2</li>
    <li id="item3">Item 3</li>
    <li id="item4">Item 4</li>
</ul>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
$(document).ready(function() {
    // Função para aplicar configurações salvas
    function applySettings(settings) {
        // Aplica o layout do tema
        if (settings.themelayout) {
            $('#menu').removeClass('vertical horizontal').addClass(settings.themelayout);
        }

        // Configura o Menu Trigger
        if (settings.MenuTrigger) {
            $('#menu li').off('click mouseenter mouseleave');
            if (settings.MenuTrigger === 'click') {
                $('#menu li').on('click', function() {
                    $('#menu li').removeClass('active');
                    $(this).addClass('active');
                });
            } else if (settings.MenuTrigger === 'hover') {
                $('#menu li').on('mouseenter', function() {
                    $('#menu li').removeClass('active');
                    $(this).addClass('active');
                });
            }
        }
    }

    // Carrega as configurações salvas
    var savedSettings = <?= json_encode($savedSettings) ?>;
    applySettings(savedSettings);

    // Quando uma opção é alterada
    $('#themelayout, #MenuTrigger').on('change', function() {
        var settings = {
            themelayout: $('#themelayout').val(),
            MenuTrigger: $('#MenuTrigger').val()
            // Adicione outros campos conforme necessário
        };

        // Aplica as configurações imediatamente
        applySettings(settings);

        // Envia as configurações para o servidor via AJAX
        $.ajax({
            url: 'menu.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'save_settings',
                settings: settings
            },
            success: function(response) {
                if (response.status === 'success') {
                    console.log('Configurações salvas com sucesso.');
                } else {
                    console.error('Erro ao salvar configurações.');
                }
            },
            error: function() {
                console.error('Erro na comunicação com o servidor.');
            }
        });
    });
});
</script>

</body>
</html>
