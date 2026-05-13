<?php
$idd = $_GET['id'] ?? '';

?>







<!-- HTML do Popup Cancelado -->
<div id="canceledPopup" class="popup-overlay-canceled">
    <div class="popup-container-canceled">
        <div class="canceled-icon">✕</div>
        <h2 class="popup-title-canceled">Agendamento Cancelado!</h2>
        <p class="popup-message-canceled">Seu agendamento foi cancelado com sucesso.</p>
        <button class="close-btn-canceled" onclick="closeCanceledPopup()">Fechar</button>
        <div class="progress-bar-canceled"></div>
    </div>
</div>

<style>
/* CSS do Popup Cancelado */
.popup-overlay-canceled {
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

.popup-overlay-canceled.show {
    opacity: 1;
    visibility: visible;
}

.popup-container-canceled {
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

.popup-overlay-canceled.show .popup-container-canceled {
    transform: translateY(0) scale(1);
}

.canceled-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 35px;
    font-weight: bold;
    animation: shake 2s infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.popup-title-canceled {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

.popup-message-canceled {
    font-size: 16px;
    color: #7f8c8d;
    margin-bottom: 25px;
    line-height: 1.5;
}

.close-btn-canceled {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-btn-canceled:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(231, 76, 60, 0.3);
}

.progress-bar-canceled {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #e74c3c, #c0392b);
    border-radius: 0 0 20px 20px;
    animation: progress-canceled 5s linear forwards;
}

@keyframes progress-canceled {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
// JavaScript para detectar status=cancelado e mostrar popup
document.addEventListener('DOMContentLoaded', function() {
    // Pega os parâmetros da URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    
    // Se o status for 'cancelado', mostra o popup
    if (status === 'cancelado') {
        showCanceledPopup();
        // Remove o parâmetro da URL para evitar mostrar novamente no refresh
        removeUrlParameter('status');
    }
});

function showCanceledPopup() {
    const popup = document.getElementById('canceledPopup');
    popup.classList.add('show');
    
    // Auto-close após 5 segundos
    setTimeout(function() {
        closeCanceledPopup();
    }, 5000);
}

function closeCanceledPopup() {
    const popup = document.getElementById('canceledPopup');
    popup.classList.remove('show');
}

// Remove parâmetro da URL
function removeUrlParameter(parameter) {
    const url = new URL(window.location);
    url.searchParams.delete(parameter);
    window.history.replaceState({}, document.title, url);
}

// Fechar popup clicando fora dele
document.addEventListener('click', function(e) {
    const popup = document.getElementById('canceledPopup');
    if (e.target === popup) {
        closeCanceledPopup();
    }
});
</script>





















<?php
// Configuração padrão do tema

// Inclui arquivos necessários para ter acesso às configurações
if (file_exists('login/painel/conn.php')) {
    include 'login/painel/conn.php';
    include 'login/painel/estilo.php';
    include 'login/painel/css_de_icones.php';
    include 'login/painel/config_dados.php';
    include 'login/painel/funcoes.php';
    include 'login/painel/menu.php';
    
    // Busca configurações para o tema
    $sql_busca_config = "SELECT * FROM config";
    $query_busca_config = mysqli_query($conn, $sql_busca_config);
    
    if ($query_busca_config && mysqli_num_rows($query_busca_config) > 0) {
        while($rows_config = mysqli_fetch_array($query_busca_config)) {
            if (isset($rows_config['tema'])) {
                $tema = (int)$rows_config['tema'];
            }
        }
    }
    
    $icon = 'login/painel/' . $icon;
    $logo = 'login/painel/' . $logo;
} else {
    // Valores padrão caso os arquivos não existam
    $icon = 'assets/img/icon.png';
    $logo = 'assets/img/logo.png';
    $titulo_global = 'Sistema';
}










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






   $icon_path = 'login/painel/'.$icon; // Renomeado para evitar conflito com a variável $icon se ela for um objeto/array
$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave = $rows_config['chave'];
    $validade = $rows_config['validade'];
    $link_pagamento = $rows_config['link_pagamento'];
    $preco = $rows_config['preco'];
    $telefone = $rows_config['telefone'];
    $caminho_modelo       = $rows_config['caminho_modelo'];

        // Seção Hero
        $hero_title           = $rows_config['hero_title'];
        $hero_subtitle        = $rows_config['hero_subtitle'];

        // Seção Serviços
        $services_title       = $rows_config['services_title'];
        $services_description = $rows_config['services_description'];

        // Contato
        $telefone             = $rows_config['telefone'];

        // Vendas
        $tipo_vendas          = $rows_config['tipo_vendas'];
        $texto_vendas         = $rows_config['texto_vendas'];
        $video_youtube        = $rows_config['video_youtube'];

        // Tema de cores
        $tema                 = (int)$rows_config['tema'];
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
    $nome_empresa	 = $rows_usuarios['nome_empresa'];

}











// Função para renderizar o cabeçalho HTML
function renderHeader($titulo_global, $icon, $tema) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($nome_empresa ?? 'Sistema'); ?> - Cancelar Agendamento</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="<?= $icon; ?>" type="image/png" sizes="16x16">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <style>
            :root {
                <?php
                switch ($tema) {
                    case 1: // Roxo e Azul
                        ?>
                        --primary-color: #3a0ca3;
                        --secondary-color: #4cc9f0;
                        --accent-color: #f72585;
                        --dark-color: #2b2d42;
                        --light-color: #f8f9fa;
                        --gradient: linear-gradient(120deg, #7209b7, #3a0ca3);
                        --gradient-hover: linear-gradient(120deg, #3a0ca3, #7209b7);
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
                        <?php
                        break;
                    case 2: // Verde e Aqua
                        ?>
                        --primary-color: #06d6a0;
                        --secondary-color: #1b9aaa;
                        --accent-color: #ff9f1c;
                        --dark-color: #1d3557;
                        --light-color: #f1faee;
                        --gradient: linear-gradient(120deg, #06d6a0, #1b9aaa);
                        --gradient-hover: linear-gradient(120deg, #1b9aaa, #06d6a0);
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
                        <?php
                        break;
                    case 3: // Vermelho e Laranja
                        ?>
                        --primary-color: #e63946;
                        --secondary-color: #f77f00;
                        --accent-color: #fcbf49;
                        --dark-color: #003049;
                        --light-color: #f1faee;
                        --gradient: linear-gradient(120deg, #e63946, #f77f00);
                        --gradient-hover: linear-gradient(120deg, #f77f00, #e63946);
                        --danger-color: #d00000;
                        --danger-hover: #9d0208;
                        <?php
                        break;
                    case 4: // Azul Escuro e Ciano
                        ?>
                        --primary-color: #003459;
                        --secondary-color: #00a8e8;
                        --accent-color: #ff6b6b;
                        --dark-color: #00171f;
                        --light-color: #f5f5f5;
                        --gradient: linear-gradient(120deg, #003459, #00a8e8);
                        --gradient-hover: linear-gradient(120deg, #00a8e8, #003459);
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
                        <?php
                        break;
                    case 5: // Roxo e Rosa
                        ?>
                        --primary-color: #9b5de5;
                        --secondary-color: #f15bb5;
                        --accent-color: #fee440;
                        --dark-color: #1b1b1b;
                        --light-color: #f8f9fa;
                        --gradient: linear-gradient(120deg, #9b5de5, #f15bb5);
                        --gradient-hover: linear-gradient(120deg, #f15bb5, #9b5de5);
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
                        <?php
                        break;
                    case 6: // Cinza e Amarelo
                        ?>
                        --primary-color: #2b2d42;
                        --secondary-color: #ffd166;
                        --accent-color: #ef476f;
                        --dark-color: #191923;
                        --light-color: #edf2f4;
                        --gradient: linear-gradient(120deg, #2b2d42, #1a1a2e);
                        --gradient-hover: linear-gradient(120deg, #1a1a2e, #2b2d42);
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
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
                        --danger-color: #e63946;
                        --danger-hover: #c1121f;
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
            }
            
            .header-top h1 {
                color: white;
                font-size: 1.8rem;
                font-weight: 600;
                margin: 0;
                letter-spacing: -0.5px;
            }
            
            .main-container {
                max-width: 900px;
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
            
            .client-info-card {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border: 1px solid #dee2e6;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 30px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            }
            
            .client-info-card h5 {
                color: var(--primary-color);
                font-size: 1.2rem;
                margin-bottom: 15px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .client-info-card p {
                color: var(--dark-color);
                font-size: 1rem;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            /* Estado de erro */
            .error-state {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            
            .error-state i {
                font-size: 4rem;
                margin-bottom: 20px;
                color: var(--danger-color);
            }
            
            .error-state h3 {
                color: var(--dark-color);
                font-size: 1.5rem;
                margin-bottom: 15px;
                font-weight: 600;
            }
            
            .error-state p {
                font-size: 1.1rem;
                margin-bottom: 30px;
                color: #666;
            }
            
            /* Botões */
            .btn-primary {
                background: var(--gradient);
                border: none;
                padding: 12px 25px;
                border-radius: 10px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
            }
            
            .btn-primary:hover {
                background: var(--gradient-hover);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.3);
                color: white;
                text-decoration: none;
            }
            
            .btn-danger {
                background: linear-gradient(120deg, var(--danger-color), var(--danger-hover));
                border: none;
                padding: 12px 20px;
                border-radius: 10px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .btn-danger:hover {
                background: linear-gradient(120deg, var(--danger-hover), var(--danger-color));
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(230, 57, 70, 0.4);
                color: white;
            }
            
            .btn-danger:active {
                transform: translateY(0);
            }
            
            .btn-danger.btn-sm {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            
            /* Tabela Desktop */
            .agendamento-tabela {
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                margin-top: 20px;
            }
            
            .table {
                margin: 0;
                background: white;
            }
            
            .table thead th {
                background: var(--gradient);
                color: white;
                border: none;
                padding: 20px 15px;
                font-weight: 600;
                text-align: center;
                font-size: 0.95rem;
                letter-spacing: 0.5px;
            }
            
            .table tbody tr {
                transition: all 0.3s ease;
            }
            
            .table tbody tr:hover {
                background-color: rgba(0,0,0,0.02);
                transform: scale(1.01);
            }
            
            .table td {
                padding: 20px 15px;
                border: none;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                text-align: center;
                font-size: 0.95rem;
            }
            
            /* Estado vazio */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #999;
                background: white;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            
            .empty-state i {
                font-size: 3.5rem;
                margin-bottom: 20px;
                opacity: 0.5;
                color: var(--primary-color);
            }
            
            .empty-state p {
                font-size: 1.1rem;
                margin: 0;
            }
            
            /* Responsividade CORRIGIDA */
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
                
                .main-container {
                    padding: 0 15px;
                }
                
                /* Esconder tabela no mobile */
                .agendamento-tabela {
                    display: none !important;
                }
                
                /* Mostrar cards no mobile */
                .agendamento-detalhe {
                    display: block !important;
                }
            }
            
            @media (min-width: 769px) {
                /* Esconder cards no desktop */
                .agendamento-detalhe {
                    display: none !important;
                }
                
                /* Mostrar tabela no desktop */
                .agendamento-tabela {
                    display: block !important;
                }
            }
            
            /* Animações */
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
    <body>
    <?php
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    renderHeader($titulo_global ?? 'Sistema', $icon, $tema);
    ?>
    <header class="header-top">
        <h1><i class="fas fa-exclamation-triangle"></i> Erro de Acesso</h1>
    </header>

    <div class="main-container">
        <div class="booking-card fade-in">
            <div class="error-state">
                <i class="fas fa-exclamation-circle"></i>
                <h3>ID de Agendamento Não Informado</h3>
                <p>Para cancelar um agendamento, é necessário fornecer um ID válido.</p>
                <a href="index.php" class="btn-primary">
                    <i class="fas fa-home"></i>
                    Voltar para o Início
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

if ($idd) {
    // Busca os dados do cliente com base no id_agendamento
    $sql_busca_clientes = "SELECT * FROM clientes WHERE id_agendamento = '$idd'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    $total_busca_clientes = mysqli_num_rows($query_busca_clientes);

    if ($total_busca_clientes > 0) {
        while ($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
            $nome = $rows_clientes['nome'];
            $telefone = $rows_clientes['telefone'];
            $usuario_api = $rows_clientes['usuario_api'];
        }
    } else {
        // Cliente não encontrado - renderizar página de erro
        renderHeader($titulo_global ?? 'Sistema', $icon, $tema);
        ?>
        <header class="header-top">
            <h1><i class="fas fa-user-times"></i> Cliente Não Encontrado</h1>
        </header>

        <div class="main-container">
            <div class="booking-card fade-in">
                <div class="error-state">
                    <i class="fas fa-user-slash"></i>
                    <h3>Cliente Não Encontrado</h3>
                    <p>Não foi possível encontrar um cliente com o ID de agendamento fornecido.<br>
                    Verifique se o ID está correto ou entre em contato com o suporte.</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="index.php" class="btn-primary">
                            <i class="fas fa-home"></i>
                            Voltar para o Início
                        </a>
                        <a href="javascript:history.back()" class="btn-danger">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    }

    // Usa o telefone e o usuário_api para buscar os agendamentos na tabela agendamento
$sql_busca_agendamentos = "SELECT * FROM agendamento WHERE cliente_telefone = '$telefone' AND usuario_api = '$usuario_api' AND data >= CURDATE() ORDER BY data ASC";
$query_busca_agendamentos = mysqli_query($conn, $sql_busca_agendamentos);
    $total_busca_agendamentos = mysqli_num_rows($query_busca_agendamentos);

    renderHeader($titulo_global ?? 'Sistema', $icon, $tema);
    ?>

    <header class="header-top">
        <h1><i class="fas fa-calendar-times"></i> Cancelar Agendamento</h1>
    </header>

    <div class="main-container">
        <div class="booking-card fade-in">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                Informações do Cliente
            </div>
            
            <!-- Informações do Cliente -->
            <div class="client-info-card">
                <h5><i class="fas fa-user"></i> <?= htmlspecialchars($nome); ?></h5>
                <p><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($telefone); ?></p>
            </div>
            
            <div class="section-title" style="margin-top: 40px;">
                <i class="fas fa-calendar-check"></i>
                Agendamentos Ativos
            </div>

            <?php if ($total_busca_agendamentos > 0) { ?>
                <!-- Exibição Horizontal (Desktop) -->
                <div class="agendamento-tabela">
                    <table class="table table-striped table-bordered mt-3">
                        <thead>
                            <tr>
                                <th style="text-align: center;"><i class="fas fa-calendar-day mr-1"></i> Dia</th>
                                <th style="text-align: center;"><i class="fas fa-clock mr-1"></i> Horário</th>
                                <th style="text-align: center;"><i class="fas fa-user-md mr-1"></i> Profissional</th>
                                <th style="text-align: center;"><i class="fas fa-calendar-alt mr-1"></i> Data</th>
                                <th style="text-align: center;"><i class="fas fa-cog mr-1"></i> Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row_agendamento = mysqli_fetch_array($query_busca_agendamentos)) { ?>
                                <tr class="text-center">
                                    <td><?= $row_agendamento['dia']; ?></td>
                                    <td><?= $row_agendamento['horario']; ?></td>
                                    <td><?= $row_agendamento['profissional_nome']; ?> (<?= $row_agendamento['profissional_cargo']; ?>)</td>
                                    <td><?= date('d/m/Y', strtotime($row_agendamento['data'])); ?></td>
                                    <td>
                                        <form action="processar_cancelamento.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $row_agendamento['id']; ?>">
                                            <input type="hidden" name="idd" value="<?= $_GET['id']; ?>">
                                            <!-- Botão com confirmação -->
                                            <button 
                                                type="submit" 
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');"
                                            >
                                                <i class="fas fa-times-circle mr-1"></i> Cancelar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Exibição Vertical (Celular) -->
                <?php 
                    // Resetar o ponteiro do resultado para reaproveitar os dados
                    mysqli_data_seek($query_busca_agendamentos, 0); 
                ?>
                <div class="agendamento-detalhe">
                    <?php
                    $index = 0; // Índice para alternar cores
                    while ($row_agendamento = mysqli_fetch_array($query_busca_agendamentos)) {
                        $linhaCor = ($index % 2 == 0) ? '#f9f9f9' : '#ffffff'; 
                        $index++;
                    ?>
                        <div style="background-color: <?= $linhaCor; ?>; padding: 0; margin-bottom: 15px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="display: flex; flex-direction: column;">
                                <div class="flex" style="display: flex; justify-content: space-between; padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                    <span><strong><i class="fas fa-calendar-day mr-1"></i> Dia:</strong></span>
                                    <span><?= $row_agendamento['dia']; ?></span>
                                </div>
                                <div class="flex" style="display: flex; justify-content: space-between; padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                    <span><strong><i class="fas fa-clock mr-1"></i> Horário:</strong></span>
                                    <span><?= $row_agendamento['horario']; ?></span>
                                </div>
                                <div class="flex" style="display: flex; justify-content: space-between; padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                    <span><strong><i class="fas fa-user-md mr-1"></i> Profissional:</strong></span>
                                    <span><?= $row_agendamento['profissional_nome']; ?> (<?= $row_agendamento['profissional_cargo']; ?>)</span>
                                </div>
                                <div class="flex" style="display: flex; justify-content: space-between; padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                    <span><strong><i class="fas fa-calendar-alt mr-1"></i> Data:</strong></span>
                                    <span><?= date('d/m/Y', strtotime($row_agendamento['data'])); ?></span>
                                </div>
                                <div style="text-align: center; padding: 15px;">
                                    <form action="processar_cancelamento.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row_agendamento['id']; ?>">
                                        <input type="hidden" name="idd" value="<?= $_GET['id']; ?>">
                                        <!-- Botão com confirmação -->
                                        <button 
                                            type="submit" 
                                            class="btn btn-danger"
                                            onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');"
                                        >
                                            <i class="fas fa-times-circle mr-1"></i> Cancelar Agendamento
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            <?php } else { ?>
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>Nenhum agendamento encontrado para este cliente.</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>

<?php
} else {
    renderHeader($titulo_global ?? 'Sistema', $icon, $tema);
    ?>
    <header class="header-top">
        <h1><i class="fas fa-exclamation-triangle"></i> Erro de Acesso</h1>
    </header>

    <div class="main-container">
        <div class="booking-card fade-in">
            <div class="error-state">
                <i class="fas fa-exclamation-circle"></i>
                <h3>ID de Agendamento Não Informado</h3>
                <p>Para cancelar um agendamento, é necessário fornecer um ID válido.</p>
                <a href="index.php" class="btn-primary">
                    <i class="fas fa-home"></i>
                    Voltar para o Início
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}
?>