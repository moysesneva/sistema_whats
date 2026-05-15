<?php
// Link de fallback (com ID real será redirecionado via JS depois)
$id = $_GET['id'] ?? '';
?>

<!-- HTML do Popup -->
<div id="successPopup" class="popup-overlay">
    <div class="popup-container">
        <div class="success-icon">✓</div>
        <h2 class="popup-title">Agendamento Realizado!</h2>
<p class="popup-message">Em breve você receberá uma confirmação no seu WhatsApp.</p>
        <button class="close-btn" onclick="closePopup()">Fechar</button>
        <div class="progress-bar"></div>
    </div>
</div>

<style>
/* CSS do Popup */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.popup-overlay.show {
    opacity: 1;
    visibility: visible;
}

.popup-container {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    transform: translateY(-50px) scale(0.8);
    transition: all 0.3s ease;
    position: relative;
}

.popup-overlay.show .popup-container {
    transform: translateY(0) scale(1);
}

.success-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 35px;
    font-weight: bold;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.popup-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

.popup-message {
    font-size: 16px;
    color: #7f8c8d;
    margin-bottom: 25px;
    line-height: 1.5;
}

.close-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #4CAF50, #45a049);
    border-radius: 0 0 20px 20px;
    animation: progress 5s linear forwards;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
// JavaScript para detectar status=sucesso e mostrar popup
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    
    if (status === 'sucesso') {
        showPopup();
        removeUrlParameter('status');
    }
});

function showPopup() {
    const popup = document.getElementById('successPopup');
    popup.classList.add('show');
    
    setTimeout(function() {
        closePopup();
    }, 5000);
}

function closePopup() {
    const popup = document.getElementById('successPopup');
    popup.classList.remove('show');
}

function removeUrlParameter(parameter) {
    const url = new URL(window.location);
    url.searchParams.delete(parameter);
    window.history.replaceState({}, document.title, url);
}

document.addEventListener('click', function(e) {
    const popup = document.getElementById('successPopup');
    if (e.target === popup) {
        closePopup();
    }
});
</script>

<?php
$idd = $_GET['id'] ?? null;

if (empty($idd)) {
    header("Location: index.php");
    exit;
}

if($idd){
    include 'login/painel/conn.php';
    include 'login/painel/estilo.php';
    include 'login/painel/css_de_icones.php';
    include 'login/painel/config_dados.php';
    include 'login/painel/funcoes.php';
    include 'login/painel/menu.php';

    $sql_busca_clientes = "SELECT * FROM clientes WHERE id_agendamento = '" . mysqli_real_escape_string($conn, $idd) . "'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);

    $nome = null;
    $telefone = null;
    $usuario_api = null;

    if ($query_busca_clientes && mysqli_num_rows($query_busca_clientes) > 0) {
        while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
            $nome = $rows_clientes['nome'];
            $telefone = $rows_clientes['telefone'];
            $usuario_api = $rows_clientes['usuario_api'];
        }
    }
    
    $icon_path = 'login/painel/'.$icon;
    $sql_busca_config = "SELECT * FROM config";
    $query_busca_config = mysqli_query($conn, $sql_busca_config);
    $total_busca_config = mysqli_num_rows($query_busca_config);

    while($rows_config = mysqli_fetch_array($query_busca_config)) {
        $chave = $rows_config['chave'];
        $validade = $rows_config['validade'];
        $link_pagamento = $rows_config['link_pagamento'];
        $preco = $rows_config['preco'];
        $telefone = $rows_config['telefone'];
        $caminho_modelo = $rows_config['caminho_modelo'];
        $hero_title = $rows_config['hero_title'];
        $hero_subtitle = $rows_config['hero_subtitle'];
        $services_title = $rows_config['services_title'];
        $services_description = $rows_config['services_description'];
        $telefone = $rows_config['telefone'];
        $tipo_vendas = $rows_config['tipo_vendas'];
        $texto_vendas = $rows_config['texto_vendas'];
        $video_youtube = $rows_config['video_youtube'];
        $tema = (int)$rows_config['tema'];
    }

    $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'AND tipo = '2'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    $total_busca_usuario = mysqli_num_rows($query_busca_usuario);

    while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
        $qr_quantidade  = $rows_usuarios['qr_quantidade'];
        $tempo_code  = $rows_usuarios['tempo_code'];
        $funcao = $rows_usuarios['funcao'];
        $IA_boas_vindas = $rows_usuarios['IA_boas_vindas'];
        $IA_prompt = $rows_usuarios['IA_prompt'];
        $IA_despedida = $rows_usuarios['IA_despedida'];
        $tempo_final = $rows_usuarios['tempo_final'];
        $creditos = $rows_usuarios['creditos'];
        $plano = $rows_usuarios['plano'];
        $modo_atuante = $rows_usuarios['modo_atuante'];
        $tema = $rows_usuarios['tema'];
        $logo = $rows_usuarios['logo'];
        $nome_empresa = $rows_usuarios['nome_empresa'];
    }

    function conectarDB_local() {
        global $conn_global_for_page;
        if (isset($conn_global_for_page) && $conn_global_for_page instanceof mysqli) {
            return $conn_global_for_page;
        }
        include 'login/painel/conn.php';
        return $conn;
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($nome_empresa ?? 'Agendamento Online'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= htmlspecialchars($icon_path); ?>" type="image/png" sizes="16x16">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
        :root {
            <?php
            switch ($tema) {
                case 1:
                    ?>
                    --primary-color: #3a0ca3;
                    --secondary-color: #4cc9f0;
                    --accent-color: #f72585;
                    --dark-color: #2b2d42;
                    --light-color: #f8f9fa;
                    --gradient: linear-gradient(120deg, #7209b7, #3a0ca3);
                    --gradient-hover: linear-gradient(120deg, #3a0ca3, #7209b7);
                    <?php
                    break;
                case 2:
                    ?>
                    --primary-color: #06d6a0;
                    --secondary-color: #1b9aaa;
                    --accent-color: #ff9f1c;
                    --dark-color: #1d3557;
                    --light-color: #f1faee;
                    --gradient: linear-gradient(120deg, #06d6a0, #1b9aaa);
                    --gradient-hover: linear-gradient(120deg, #1b9aaa, #06d6a0);
                    <?php
                    break;
                case 3:
                    ?>
                    --primary-color: #e63946;
                    --secondary-color: #f77f00;
                    --accent-color: #fcbf49;
                    --dark-color: #003049;
                    --light-color: #f1faee;
                    --gradient: linear-gradient(120deg, #e63946, #f77f00);
                    --gradient-hover: linear-gradient(120deg, #f77f00, #e63946);
                    <?php
                    break;
                case 4:
                    ?>
                    --primary-color: #003459;
                    --secondary-color: #00a8e8;
                    --accent-color: #ff6b6b;
                    --dark-color: #00171f;
                    --light-color: #f5f5f5;
                    --gradient: linear-gradient(120deg, #003459, #00a8e8);
                    --gradient-hover: linear-gradient(120deg, #00a8e8, #003459);
                    <?php
                    break;
                case 5:
                    ?>
                    --primary-color: #9b5de5;
                    --secondary-color: #f15bb5;
                    --accent-color: #fee440;
                    --dark-color: #1b1b1b;
                    --light-color: #f8f9fa;
                    --gradient: linear-gradient(120deg, #9b5de5, #f15bb5);
                    --gradient-hover: linear-gradient(120deg, #f15bb5, #9b5de5);
                    <?php
                    break;
                case 6:
                    ?>
                    --primary-color: #2b2d42;
                    --secondary-color: #ffd166;
                    --accent-color: #ef476f;
                    --dark-color: #191923;
                    --light-color: #edf2f4;
                    --gradient: linear-gradient(120deg, #2b2d42, #1a1a2e);
                    --gradient-hover: linear-gradient(120deg, #1a1a2e, #2b2d42);
                    <?php
                    break;
                default:
                    ?>
                    --primary-color: #3a0ca3;
                    --secondary-color: #4cc9f0;
                    --accent-color: #f72585;
                    --dark-color: #2b2d42;
                    --light-color: #f8f9fa;
                    --gradient: linear-gradient(120deg, #7209b7, #3a0ca3);
                    --gradient-hover: linear-gradient(120deg, #3a0ca3, #7209b7);
                    <?php
            }
            ?>
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .header-top {
            background: var(--gradient);
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
            z-index: 100;
        }
        
        .header-top h1 {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .main-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px 30px;
            margin-bottom: 20px;
            animation: slideIn 0.5s ease;
            position: relative;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .section-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .section-title i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
            opacity: 0.8;
        }
        
        .welcome-text {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        /* SISTEMA DE ETAPAS MELHORADO COM AUTO-SCROLL E NAVEGAÇÃO */
        .progress-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }
        
        .progress-stepper::before {
            content: '';
            position: absolute;
            top: 50px;
            left: 20%;
            right: 20%;
            height: 3px;
            background: linear-gradient(90deg, #e0e0e0 0%, #f0f0f0 100%);
            z-index: 0;
            border-radius: 2px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        

        
        .step {
            position: relative;
            flex: 1;
            text-align: center;
            z-index: 2;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 15px 10px;
            border-radius: 15px;
            user-select: none;
            max-width: 120px;
        }
        
        .step:hover {
            background: rgba(255,255,255,0.8);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .step.disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .step.disabled:hover {
            background: transparent;
            transform: none;
            box-shadow: none;
        }
        
        .step-circle {
            width: 50px;
            height: 50px;
            background: #e0e0e0;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 4px solid transparent;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        
        .step-circle::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.3s ease;
        }
        
        .step:hover .step-circle::before {
            transform: scale(1);
        }
        
        .step.active .step-circle {
            background: var(--gradient);
            transform: scale(1.2);
            border-color: white;
            box-shadow: 0 6px 25px rgba(0,0,0,0.25);
            animation: activeStepPulse 2s infinite;
        }
        
        @keyframes activeStepPulse {
            0%, 100% { box-shadow: 0 6px 25px rgba(0,0,0,0.25); }
            50% { box-shadow: 0 8px 35px rgba(0,0,0,0.35); }
        }
        
        .step.completed .step-circle {
            background: var(--secondary-color);
            transform: scale(1.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            border-color: rgba(255,255,255,0.3);
        }
        
        .step.completed:hover .step-circle {
            transform: scale(1.15);
            box-shadow: 0 6px 25px rgba(0,0,0,0.25);
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 5px;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 700;
            transform: scale(1.05);
        }
        
        .step.completed .step-label {
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .step:hover .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .step.disabled .step-label {
            color: #999;
        }
        
        /* Indicador visual de que é clicável */
        .step.clickable::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: var(--accent-color);
            border-radius: 50%;
            opacity: 0.7;
            animation: clickableIndicator 2s infinite;
        }
        
        /* Indicador de edição para etapas completadas */
        .step.completed.clickable::before {
            content: '\f044'; /* Ícone de editar do FontAwesome */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--accent-color);
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .step.completed.clickable:hover::before {
            opacity: 1;
            transform: scale(1.1);
        }
        
        /* Tooltip para etapas */
        .step-tooltip {
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark-color);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .step-tooltip::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 5px solid var(--dark-color);
        }
        
        .step:hover .step-tooltip {
            opacity: 1;
            bottom: -40px;
        }
        
        /* SEÇÕES DE ETAPAS COM SCROLL AUTOMÁTICO */
        .step-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 15px;
            border: 2px solid transparent;
            transition: all 0.4s ease;
            background: #f9f9f9;
            opacity: 0.7;
            transform: translateY(10px);
        }
        
        .step-section.active {
            background: white;
            border-color: var(--primary-color);
            opacity: 1;
            transform: translateY(0);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .step-section.completed {
            background: var(--light-color);
            border-color: var(--secondary-color);
            opacity: 0.9;
        }
        
        .service-card {
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }
        
        .service-card:hover {
            border-color: var(--secondary-color);
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .service-card.selected {
            border-color: var(--primary-color);
            background: var(--light-color);
            box-shadow: 0 5px 20px rgba(155,93,229,0.2);
        }
        
        .service-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .service-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .service-duration {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .service-price {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .date-slots-group {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fdfdfd;
            transition: all 0.3s ease;
        }
        
        .date-slots-group.expanded {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .date-header {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 1.1rem;
            padding: 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .date-header:hover {
            background: #e9ecef;
        }
        
        .date-header.active {
            background: var(--gradient);
            color: white;
        }
        
        .date-header i {
            color: var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .date-header.active i {
            color: white;
        }
        
        .date-header .date-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-header .expand-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .date-header.active .expand-icon {
            transform: rotate(180deg);
        }
        
.time-slots-wrapper {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease;
    padding: 0 20px;
}

.time-slots-wrapper.show {
    max-height: none !important; /* Remove qualquer limite de altura */
    overflow: visible !important; /* Garante que todo conteúdo seja visível */
    padding: 20px;
}

        .time-slots-container {
            margin-top: 25px;
        }
        
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }
        
        .time-slot {
            padding: 12px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
        }
        
        .time-slot:hover {
            border-color: var(--secondary-color);
            background: var(--light-color);
            transform: translateY(-2px);
        }
        
        .time-slot.selected {
            border-color: var(--primary-color);
            background: var(--gradient);
            color: white;
        }
        
        .time-slot.unavailable {
            background: #f5f5f5;
            color: #999;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .time-slot.unavailable:hover {
            transform: none;
            border-color: #e8e8e8;
        }
        
        .booking-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
            border: 1px solid #dee2e6;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.5s ease;
        }
        
        .booking-summary.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .booking-summary h4 {
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            padding-top: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
        }
        
        .summary-label {
            color: #666;
            font-size: 0.95rem;
        }
        
        .summary-value {
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            transition: all 0.3s ease;
            background-color: #ffffff !important;
            font-family: inherit;
            color: #000000 !important;
            font-weight: normal !important;
            -webkit-text-fill-color: #000000 !important;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
            background-color: white !important;
            outline: none;
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }
        
        select.form-control {
            cursor: pointer !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 15px center !important;
            background-size: 20px !important;
            padding-right: 45px !important;
            color: #000000 !important;
            background-color: #ffffff !important;
            text-shadow: none !important;
            font-weight: normal !important;
            font-size: 16px !important;
            line-height: 1.2 !important;
            height: auto !important;
            min-height: 50px !important;
            display: flex !important;
            align-items: center !important;
        }
        
        select.form-control:not([multiple]) {
            text-align: left !important;
            text-indent: 0 !important;
            padding-left: 15px !important;
        }
        
        select.form-control,
        select.form-control:focus,
        select.form-control:active,
        select.form-control:visited,
        select.form-control:valid {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            opacity: 1 !important;
        }
        
        select.form-control option {
            color: #000000 !important;
            background-color: #ffffff !important;
            padding: 12px !important;
            font-weight: normal !important;
            font-size: 16px !important;
            display: block !important;
            visibility: visible !important;
        }
        
        select.form-control option:checked,
        select.form-control option:selected,
        select.form-control option[selected] {
            color: #000000 !important;
            background-color: #e3f2fd !important;
            font-weight: 500 !important;
        }
        
        .form-control::placeholder {
            color: #777777 !important;
            opacity: 1 !important;
            font-weight: normal !important;
        }
        
        input.form-control {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: normal !important;
        }
        
        input.form-control:focus {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
        }
        
        .btn-primary {
    width: 100%;
    padding: 16px;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    background: var(--gradient);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary:hover {
    background: var(--gradient-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-primary:disabled {
    cursor: not-allowed;
    background-color: #e9ecef;
    color: #6c757d;
    box-shadow: none;
    transform: none;
}
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .visitors-alert {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe4cc 100%);
            border: 1px solid #ffd4a3;
            color: #856404;
            padding: 20px;
            border-radius: 15px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
            box-shadow: 0 5px 20px rgba(255,193,7,0.1);
            animation: pulse 3s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }
        
        .visitors-count {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 1.2rem;
        }
        
        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .help-text i {
            color: var(--secondary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* ANIMAÇÕES DE SCROLL SUAVE */
        .smooth-scroll {
            scroll-behavior: smooth;
        }
        
        @media (max-width: 768px) {
            .header-top h1 {
                font-size: 1.5rem;
            }
            
            .booking-card {
                padding: 30px 20px;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
            
            .progress-stepper {
                margin-bottom: 30px;
                padding: 20px 15px;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }
            
            .progress-stepper::before {
                display: none; /* Ocultar linha de conexão no mobile */
            }
            
            .step {
                flex-direction: column;
                text-align: center;
                padding: 10px 5px;
                background: transparent;
                box-shadow: none;
                margin: 0;
                flex: 0 1 22%; /* 4 itens por linha com espaçamento */
                max-width: 70px;
                min-width: 60px;
            }
            
            .step:last-child {
                margin-bottom: 0;
            }
            
            .step-circle {
                width: 35px;
                height: 35px;
                margin: 0 auto 8px;
                flex-shrink: 0;
                font-size: 0.85rem;
            }
            
            .step-label {
                font-size: 0.7rem;
                margin: 0;
                text-align: center;
                line-height: 1.2;
                height: auto;
                word-break: break-word;
            }
            
            .step-tooltip {
                display: none; /* Ocultar tooltips no mobile */
            }
            
            .form-control {
                padding: 12px;
                font-size: 0.95rem;
                color: #333333 !important;
                -webkit-text-fill-color: #333333 !important;
            }
            
            .btn-primary {
                padding: 14px;
                font-size: 1rem;
            }
            
            .time-slots-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        html {
            scroll-behavior: smooth;
        }
        
    </style>
</head>

<body class="smooth-scroll">
    <header class="header-top">
        <?php if($logo == True): ?>
            <div style="text-align: center; margin-top: 1px;">
                <img 
                    src="<?=$webhook.'login/painel/'.$logo;?>" 
                    alt="Descrição da imagem" 
                    style="width: 100%; max-width: 300px; height: auto; display: inline-block;"
                >
            </div>
        <?php else: ?>
            <h1><i class="fas fa-calendar-check"></i> Agendamento Online</h1>
        <?php endif; ?>
    </header>

    <div class="main-container">
        <?php if($nome == null): ?>
          <div class="booking-card">
        <div class="section-title">
            <i class="fas fa-user-circle"></i>
            Vamos começar!
        </div>
        <p class="welcome-text">
            Para realizar seu agendamento, precisamos de algumas informações.
        </p>
        <form action="processar_nome.php" method="POST" id="formNome">
            <div class="form-group">
                <label for="nome">
                    <i class="fas fa-user"></i> 
                    Qual é o seu nome?
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="nome" 
                    name="nome" 
                    placeholder="Digite seu nome completo" 
                    required 
                    autocomplete="name"
                    minlength="3"
                >
                <input type="hidden" name="idd" value="<?= htmlspecialchars($idd); ?>">
                
                <!-- Indicador de caracteres -->
                <div class="character-indicator" id="charIndicator" style="display: none;">
                    <span class="char-count">0</span> caracteres
                    <span class="char-requirement">(mínimo 3)</span>
                </div>
                
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Escreva seu nome  ou como gostaria de ser identificado.
                </p>
            </div>
            
<button 
    type="submit" 
    class="btn-primary" 
    id="btnEnviarNome"
    disabled
    style="display: none;"
>
    Continuar
    <i class="fas fa-arrow-right"></i>
</button>
        </form>
    </div>
            
  <script>
    // Seleciona o campo de nome e o botão de continuar
    const inputNome = document.getElementById('nome');
    const btnContinuar = document.getElementById('btnEnviarNome');

    // Adiciona um "ouvinte" de eventos para monitorar a digitação
    inputNome.addEventListener('input', function() {
        // Pega a quantidade de caracteres digitados
        const caracteresDigitados = this.value.length;

        // Verifica se a quantidade é maior ou igual a 3
        if (caracteresDigitados >= 3) {
            // Se sim, mostra o botão e o habilita
            btnContinuar.style.display = 'block';
            btnContinuar.disabled = false;
        } else {
            // Se não, esconde o botão e o desabilita
            btnContinuar.style.display = 'none';
            btnContinuar.disabled = true;
        }
    });
</script>          
            
            
        <?php else: ?>
            <div class="booking-card">
                <div class="progress-stepper">
                    <div class="step completed clickable" id="step1" onclick="voltarParaEtapa(1, event)">
                        <div class="step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-label">Identificação</div>
                        <div class="step-tooltip">Dados confirmados ✓</div>
                    </div>
                    <div class="step active" id="step2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Profissional</div>
                        <div class="step-tooltip">Escolha o profissional</div>
                    </div>
                    <div class="step disabled" id="step3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Serviço</div>
                        <div class="step-tooltip">Selecione um profissional primeiro</div>
                    </div>
                    <div class="step disabled" id="step4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Dia e Horário</div>
                        <div class="step-tooltip">Complete as etapas anteriores</div>
                    </div>
                </div>
                
                <div class="section-title">
                    <i class="fas fa-calendar-plus"></i>
                    Olá <?= htmlspecialchars(isset($nome) ? explode(' ', $nome)[0] : 'Cliente'); ?>! Vamos agendar seu atendimento
                </div>
                
                <form action="processar_agendamento_servico.php" method="POST" id="formAgendamento">
                    <!-- ETAPA 2: PROFISSIONAL -->
                    <div class="step-section active" id="section-profissional">
                        <div class="form-group">
                            <label for="profissional">
                                <i class="fas fa-user-md"></i> 
                                Escolha o profissional
                            </label>
                            <select class="form-control" id="profissional" name="profissional" required onchange="carregarServicosDoProfissional()">
                                <option value=''>Selecione um profissional</option>
                                <?php
                                if ($usuario_api) {
                                    $conn_prof_select = conectarDB_local();
                                    $sql_prof = "SELECT id, profissional_nome, profissional_cargo FROM profissional WHERE usuario_api = '" . mysqli_real_escape_string($conn_prof_select, $usuario_api) . "'";
                                    $result_prof = mysqli_query($conn_prof_select, $sql_prof);
                                    
                                    if ($result_prof) {
                                        while ($row_prof = mysqli_fetch_assoc($result_prof)) {
                                            echo '<option value="'. htmlspecialchars($row_prof['id']).'">'.htmlspecialchars($row_prof['profissional_nome']) .' - '. htmlspecialchars($row_prof['profissional_cargo']) .'</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- ETAPA 3: SERVIÇO -->
                    <div class="step-section" id="section-servico" style="display: none;">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-concierge-bell"></i> 
                                Escolha o serviço desejado
                            </label>
                            <div id="servicos-lista" class="services-container"></div>
                            <input type="hidden" id="servico_selecionado" name="servico_id" required>
                            <input type="hidden" id="duracao_servico" name="duracao_servico">
                            <input type="hidden" id="valor_servico" name="valor_servico">
                        </div>
                    </div>

                    <!-- ETAPA 4: DIA E HORÁRIO -->
                    <div class="step-section" id="section-horario" style="display: none;">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-alt"></i> <i class="fas fa-clock" style="margin-left: 5px;"></i> 
                                Selecione o dia e horário
                            </label>
                            
                            <div class="time-slots-container" id="dias-horarios-lista"></div>
                            
                            <input type="hidden" id="horario_selecionado" name="horario" required>
                            <input type="hidden" id="data_selecionada" name="data" required>
                        </div>
                    </div>
                    
                    <div class="booking-summary" id="resumo-agendamento" style="display: none;">
                        <h4><i class="fas fa-receipt"></i> Resumo do Agendamento</h4>
                        <div class="summary-item">
                            <span class="summary-label">Profissional:</span>
                            <span class="summary-value" id="resumo-profissional">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Serviço:</span>
                            <span class="summary-value" id="resumo-servico">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Data:</span>
                            <span class="summary-value" id="resumo-data">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Horário:</span>
                            <span class="summary-value" id="resumo-horario">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Duração:</span>
                            <span class="summary-value" id="resumo-duracao">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Valor Total:</span>
                            <span class="summary-value" id="resumo-valor">R$ 0,00</span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="usuario_api" value="<?= htmlspecialchars($usuario_api ?? ''); ?>">
                    <input type="hidden" name="idd" value="<?= htmlspecialchars($idd); ?>">

                    <button type="submit" class="btn-primary" id="btnAgendar" style="display: none; margin-top: 20px;">
                        <i class="fas fa-check-circle"></i>
                        Confirmar Agendamento
                    </button>
                </form>
            </div>
            
            <div class="visitors-alert" id="alerta-visitas">
                <i class="fas fa-fire"></i>
                <span>
                    <span class="visitors-count" id="contador-visitas">3</span> 
                    pessoas estão agendando agora
                </span>
                <i class="fas fa-clock"></i>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Sistema de visitantes online
    let visitas = 3;

    function atualizarVisitas() {
        let incremento = Math.floor(Math.random() * 5) + 2;
        visitas += incremento;
        $('#contador-visitas').text(visitas);
    }

    setInterval(atualizarVisitas, 8000);

    // Validação do formulário de nome
    $('#formNome').on('submit', function(e) { 
        const nome = $('#nome').val().trim();
        if (nome.length < 3) {
            e.preventDefault();
            alert('Por favor, digite seu nome completo.');
            return false;
        }
    });

    // VARIÁVEIS DE CONTROLE DO SISTEMA DE ETAPAS
    let currentStep = 2;
    let completedSteps = [1]; // Array com etapas já completadas
    let stepData = {
        profissional: null,
        servico: null,
        horario: null,
        data: null
    };

    // SISTEMA DE ETAPAS COM NAVEGAÇÃO E AUTO-SCROLL
    function updateStepper(activeStep) {
        currentStep = activeStep;
        $('.step').removeClass('active completed disabled clickable');
        $('.step-section').removeClass('active completed').hide();
        
        // Configurar cada etapa
        for (let i = 1; i <= 4; i++) {
            const $step = $('#step' + i);
            const $circle = $step.find('.step-circle');
            const $tooltip = $step.find('.step-tooltip');
            
            if (completedSteps.includes(i)) {
                // Etapa completada - clicável
                $step.addClass('completed clickable');
                $step.attr('onclick', `voltarParaEtapa(${i}, event)`);
                $circle.html('<i class="fas fa-check"></i>');
                
                // Tooltips personalizados para etapas completadas
                if (i === 1) $tooltip.text('Dados confirmados ✓');
                else if (i === 2) $tooltip.text(`${stepData.profissional || 'Profissional selecionado'} ✓`);
                else if (i === 3) $tooltip.text(`${stepData.servico || 'Serviço escolhido'} ✓`);
                else if (i === 4) $tooltip.text('Horário agendado ✓');
                
            } else if (i === activeStep) {
                // Etapa ativa
                $step.addClass('active');
                $circle.text(i);
                
                // Tooltips para etapa ativa
                if (i === 2) $tooltip.text('Escolha o profissional');
                else if (i === 3) $tooltip.text('Selecione o serviço desejado');
                else if (i === 4) $tooltip.text('Escolha data e horário');
                
            } else if (i > activeStep) {
                // Etapa futura - desabilitada
                $step.addClass('disabled');
                $circle.text(i);
                
                // Tooltips para etapas bloqueadas
                if (i === 3) $tooltip.text('Selecione um profissional primeiro');
                else if (i === 4) $tooltip.text('Complete as etapas anteriores');
                
            } else {
                // Etapa anterior não completada (caso específico)
                $step.addClass('clickable');
                $step.attr('onclick', `voltarParaEtapa(${i}, event)`);
                $circle.text(i);
                $tooltip.text('Clique para editar');
            }
        }

        // Ativar seção correspondente
        if (activeStep === 2) {
            $('#section-profissional').addClass('active').show();
        } else if (activeStep === 3) {
            $('#section-profissional').addClass('completed');
            $('#section-servico').addClass('active').show();
        } else if (activeStep === 4) {
            $('#section-profissional').addClass('completed');
            $('#section-servico').addClass('completed');
            $('#section-horario').addClass('active').show();
        }

        // Auto-scroll para a seção ativa
        autoScrollToActiveSection(activeStep);
    }

    // FUNÇÃO PARA VOLTAR A ETAPAS ANTERIORES
    function voltarParaEtapa(stepNumber, event) {
        // Efeito de ondulação (se event estiver disponível)
        if (event) {
            createRippleEffect(event, $('#step' + stepNumber));
        }
        
        // Não permitir voltar se a etapa não foi completada
        if (!completedSteps.includes(stepNumber) && stepNumber !== currentStep) {
            // Animação de "negação" suave
            const $step = $('#step' + stepNumber);
            $step.addClass('shake-animation');
            setTimeout(() => $step.removeClass('shake-animation'), 500);
            return;
        }

        // Se está voltando, limpar dados das etapas posteriores
        if (stepNumber < currentStep) {
            clearStepsAfter(stepNumber);
        }

        // Ir para a etapa selecionada
        updateStepper(stepNumber);
        
        // Focar no campo principal da etapa
        setTimeout(() => {
            if (stepNumber === 2) {
                $('#profissional').focus();
            }
        }, 500);
    }

    // FUNÇÃO PARA CRIAR EFEITO DE ONDULAÇÃO
    function createRippleEffect(event, $element) {
        if (!event || !$element.length) return;
        
        const circle = $element[0];
        const diameter = Math.max(circle.clientWidth, circle.clientHeight);
        const radius = diameter / 2;

        const rect = circle.getBoundingClientRect();
        const x = event.clientX - rect.left - radius;
        const y = event.clientY - rect.top - radius;

        const ripple = $('<span class="step-ripple"></span>');
        ripple.css({
            width: diameter + 'px',
            height: diameter + 'px',
            left: x + 'px',
            top: y + 'px'
        });

        $element.css('position', 'relative').append(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // LIMPAR DADOS DAS ETAPAS POSTERIORES
    function clearStepsAfter(stepNumber) {
        if (stepNumber < 2) {
            // Reset profissional
            $('#profissional').val('');
            stepData.profissional = null;
            completedSteps = completedSteps.filter(s => s <= stepNumber);
        }
        if (stepNumber < 3) {
            // Reset serviço
            resetFormFrom('servico');
            stepData.servico = null;
            completedSteps = completedSteps.filter(s => s <= stepNumber);
        }
        if (stepNumber < 4) {
            // Reset horário
            resetFormFrom('diaHorario');
            stepData.horario = null;
            stepData.data = null;
            completedSteps = completedSteps.filter(s => s <= stepNumber);
        }
    }

    function autoScrollToActiveSection(activeStep) {
        setTimeout(() => {
            let targetElement;
            
            if (activeStep === 2) {
                targetElement = document.getElementById('section-profissional');
            } else if (activeStep === 3) {
                targetElement = document.getElementById('section-servico');
            } else if (activeStep === 4) {
                targetElement = document.getElementById('section-horario');
            }
            
            if (targetElement) {
                const elementPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - 120; // Mais margem para ver o stepper
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Efeito visual de foco na seção
                $(targetElement).addClass('focus-animation');
                setTimeout(() => {
                    $(targetElement).removeClass('focus-animation');
                }, 1000);
            }
        }, 300);
    }

    // AO SELECIONAR PROFISSIONAL, CARREGAR SERVIÇOS
    function carregarServicosDoProfissional() {
        const profissionalId = $('#profissional').val();
        const profissionalNome = $('#profissional option:selected').text();
        resetFormFrom('servico');

        if (profissionalId !== '') {
            // Marcar etapa 2 como completada
            if (!completedSteps.includes(2)) completedSteps.push(2);
            stepData.profissional = profissionalNome.split(' - ')[0]; // Só o nome
            
            updateStepper(3);
            $('#servicos-lista').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Carregando serviços...</div>');
            $('#resumo-profissional').text(profissionalNome);

            $.ajax({
                url: 'buscar_servicos_do_profissional.php',
                type: 'POST',
                data: { profissional_id: profissionalId },
                success: function(response) {
                    $('#servicos-lista').html(response);
                    if ($('#servicos-lista').find('.service-card').length === 0) {
                        $('#servicos-lista').html('<div class="empty-state"><i class="fas fa-concierge-bell"></i><p>Nenhum serviço encontrado para este profissional.</p></div>');
                    }
                },
                error: function() {
                    $('#servicos-lista').html('<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao carregar serviços.</p></div>');
                }
            });
        } else {
            $('#resumo-profissional').text('-');
            // Remover etapa 2 dos completados
            completedSteps = completedSteps.filter(s => s !== 2);
            stepData.profissional = null;
        }
    }

    // AO CLICAR EM UM SERVIÇO
    $(document).on('click', '.service-card', function() {
        $('.service-card').removeClass('selected');
        $(this).addClass('selected');

        const servicoId = $(this).data('servico-id');
        const servicoNome = $(this).data('servico-nome');
        const duracaoMinutos = $(this).data('duracao');
        const valor = $(this).data('valor');

        $('#servico_selecionado').val(servicoId);
        $('#duracao_servico').val(duracaoMinutos);
        $('#valor_servico').val(valor);

        $('#resumo-servico').text(servicoNome);
        $('#resumo-duracao').text(duracaoMinutos + ' minutos');
        $('#resumo-valor').text('R$ ' + parseFloat(valor).toFixed(2).replace('.', ','));

        // Marcar etapa 3 como completada
        if (!completedSteps.includes(3)) completedSteps.push(3);
        stepData.servico = servicoNome;

        resetFormFrom('diaHorario');
        
        // Delay para mostrar seleção antes de avançar
        setTimeout(() => {
            carregarDiasHorariosDisponiveis();
        }, 500);
    });

    function carregarDiasHorariosDisponiveis() {
        const profissionalId = $('#profissional').val();
        const servicoId = $('#servico_selecionado').val();
        const duracao = $('#duracao_servico').val();

        if (profissionalId && servicoId && duracao) {
            updateStepper(4);
            $('#resumo-agendamento').fadeIn().addClass('show');
            $('#dias-horarios-lista').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Carregando dias e horários...</div>');

            $.ajax({
                url: 'buscar_dias_e_horarios_para_servico.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    profissional_id: profissionalId,
                    servico_id: servicoId,
                    duracao: duracao
                },
                success: function(dataSlots) {
                    $('#dias-horarios-lista').empty();

                    if (dataSlots && typeof dataSlots.error !== 'undefined') {
                        console.error("Erro retornado pelo servidor PHP:", dataSlots.error);
                        $('#dias-horarios-lista').html(
                            `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao carregar horários: ${dataSlots.error}</p></div>`
                        );
                    } else if (Array.isArray(dataSlots) && dataSlots.length > 0) {
                        dataSlots.forEach(function(dataSlot, index) {
                            if (Array.isArray(dataSlot.horarios) && dataSlot.horarios.length > 0) {
                                let slotsHtml = '';
                                dataSlot.horarios.forEach(function(horario) {
                                    slotsHtml += `
                                        <div class="time-slot"
                                             data-horario="${horario}"
                                             data-data="${dataSlot.data}">
                                            ${horario}
                                        </div>
                                    `;
                                });

                                const [year, month, day] = dataSlot.data.split('-');
                                const dateObj = new Date(year, month - 1, day);
                                const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
                                const dateInUserTimezone = new Date(dateObj.getTime() + userTimezoneOffset);

                                const formattedDate = dateInUserTimezone.toLocaleDateString('pt-BR', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                });

                                const dayGroupHtml = `
                                    <div class="date-slots-group" data-date-index="${index}">
                                        <div class="date-header" onclick="toggleDateSlots(${index})">
                                            <div class="date-text">
                                                <i class="fas fa-calendar-day"></i> ${formattedDate}
                                            </div>
                                            <i class="fas fa-chevron-down expand-icon"></i>
                                        </div>
                                        <div class="time-slots-wrapper" id="slots-${index}">
                                            <div class="time-slots-grid">
                                                ${slotsHtml}
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#dias-horarios-lista').append(dayGroupHtml);
                            }
                        });

                        if ($('#dias-horarios-lista').is(':empty')) {
                            $('#dias-horarios-lista').html(
                                '<div class="empty-state"><i class="fas fa-calendar-times"></i><p>Não há horários disponíveis para este serviço com este profissional nos próximos dias.</p></div>'
                            );
                        }

                        if ($('.date-slots-group').length > 0) {
                            $('#dias-horarios-lista').prepend(
                                '<p class="help-text" style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Clique em uma data para ver os horários disponíveis</p>'
                            );
                        }
                    } else {
                        $('#dias-horarios-lista').html(
                            '<div class="empty-state"><i class="fas fa-calendar-times"></i><p>Não há horários disponíveis para este serviço com este profissional nos próximos dias.</p></div>'
                        );
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Erro na requisição AJAX:", textStatus, errorThrown, jqXHR.responseText);
                    $('#dias-horarios-lista').html(
                        '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao conectar com o servidor para buscar horários.</p></div>'
                    );
                }
            });
        }
    }

    // Função para expandir/contrair horários de uma data
   // Função para expandir/contrair horários de uma data - VERSÃO CORRIGIDA
function toggleDateSlots(index) {
    const wrapper = $(`#slots-${index}`);
    const header = $(`.date-slots-group[data-date-index="${index}"] .date-header`);
    const group = $(`.date-slots-group[data-date-index="${index}"]`);
    
    // Primeiro, fechar todos os outros grupos abertos
    $('.time-slots-wrapper').not(wrapper).each(function() {
        $(this).removeClass('show').css('max-height', '0');
    });
    $('.date-header').not(header).removeClass('active');
    $('.date-slots-group').not(group).removeClass('expanded');
    
    // Verificar se o grupo atual está aberto ou fechado
    if (wrapper.hasClass('show')) {
        // FECHAR - Este é o código que estava faltando funcionar
        wrapper.removeClass('show');
        wrapper.css('max-height', '0'); // Importante: definir para 0 ao fechar
        header.removeClass('active');
        group.removeClass('expanded');
    } else {
        // ABRIR
        wrapper.addClass('show');
        header.addClass('active');
        group.addClass('expanded');
        
        // Calcular a altura real do conteúdo para abrir suavemente
        setTimeout(() => {
            const scrollHeight = wrapper[0].scrollHeight;
            wrapper.css('max-height', scrollHeight + 'px');
        }, 10);
        
        // Auto-scroll para o grupo expandido
        setTimeout(() => {
            const groupPosition = group.offset().top;
            const headerHeight = $('.header-top').outerHeight() || 0;
            const offsetPosition = groupPosition - headerHeight - 20;
            
            $('html, body').animate({
                scrollTop: offsetPosition
            }, 500);
        }, 100);
    }
}

    // AO CLICAR EM UM HORÁRIO
    $(document).on('click', '.time-slot:not(.unavailable)', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');

        const horario = $(this).data('horario');
        const data = $(this).data('data');

        const [year, month, day] = data.split('-');
        const dateObj = new Date(year, month - 1, day);
        const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
        const dateInUserTimezone = new Date(dateObj.getTime() + userTimezoneOffset);
        const formattedDateForSummary = dateInUserTimezone.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        $('#horario_selecionado').val(horario);
        $('#data_selecionada').val(data);
        $('#resumo-data').text(formattedDateForSummary);
        $('#resumo-horario').text(horario);

        // Marcar etapa 4 como completada
        if (!completedSteps.includes(4)) completedSteps.push(4);
        stepData.horario = horario;
        stepData.data = formattedDateForSummary;

        $('#btnAgendar').fadeIn().addClass('show');
        
        // Atualizar stepper final
        updateStepper(4);

        // Auto-scroll para o resumo e botão
        setTimeout(() => {
            const resumoElement = document.getElementById('resumo-agendamento');
            const resumoPosition = resumoElement.getBoundingClientRect().top + window.pageYOffset;
            const headerHeight = $('.header-top').outerHeight() || 0;
            const offsetPosition = resumoPosition - headerHeight - 20;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }, 300);
    });

    // Função para resetar partes do formulário
    function resetFormFrom(step) {
        if (step === 'servico') {
            $('#section-servico').hide().removeClass('active completed');
            $('#servicos-lista').empty();
            $('#servico_selecionado').val('');
            $('#duracao_servico').val('');
            $('#valor_servico').val('');
            $('#resumo-servico').text('-');
            $('#resumo-duracao').text('-');
            $('#resumo-valor').text('R$ 0,00');
            
            // Remover das etapas completadas
            completedSteps = completedSteps.filter(s => s !== 3);
            stepData.servico = null;
            
            step = 'diaHorario';
        }
        if (step === 'diaHorario') {
            $('#section-horario').hide().removeClass('active completed');
            $('#dias-horarios-lista').empty();
            $('#horario_selecionado').val('');
            $('#data_selecionada').val('');
            $('#resumo-data').text('-');
            $('#resumo-horario').text('-');
            
            // Remover das etapas completadas
            completedSteps = completedSteps.filter(s => s !== 4);
            stepData.horario = null;
            stepData.data = null;
        }

        $('#btnAgendar').hide().removeClass('show');
        
        if (step === 'servico' || $('#profissional').val() === '') {
            $('#resumo-agendamento').hide().removeClass('show');
            $('#resumo-profissional').text('-');
        }
    }

    // Validação antes de enviar o agendamento
    $('#formAgendamento').on('submit', function(e) {
        const profissional = $('#profissional').val();
        const servico = $('#servico_selecionado').val();
        const data = $('#data_selecionada').val();
        const horario = $('#horario_selecionado').val();

        if (!profissional || !servico || !data || !horario) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos (Profissional, Serviço, Data e Horário) antes de agendar.');
            return false;
        }

        const $btn = $('#btnAgendar');
        $btn.prop('disabled', true).html('<span class="loading"></span> Agendando...');
    });

    // Inicialização
    $(document).ready(function() {
        if ($('#formAgendamento').length) {
            updateStepper(2);
        } else {
            updateStepper(1);
        }
        
        if ($('#profissional').val() === '') {
            resetFormFrom('servico');
        }
    });

    // CSS adicional para animações
    $('<style>').text(`
        .focus-animation {
            animation: focusPulse 1s ease-in-out;
        }
        
        .shake-animation {
            animation: shake 0.5s ease-in-out;
        }
        
        .success-check {
            animation: successPop 0.6s ease-out;
        }
        
        @keyframes focusPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
            100% { transform: scale(1); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        
        @keyframes successPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        /* Efeito de ondulação quando clica em uma etapa */
        .step-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `).appendTo('head');
</script>

</body>
</html>
<?php
} else {
    echo "<!DOCTYPE html><html lang='pt-BR'><head><title>Erro</title></head><body>";
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>";
    echo "<h1>Erro no Agendamento</h1>";
    echo "<p>Ocorreu um problema ao carregar a página de agendamento. O link pode ser inválido ou dados essenciais não foram encontrados.</p>";
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
    echo "</div></body></html>";
}
?>