<?php
$idd = $_GET['id'] ?? null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Exemplo de uso
if($idd){
    include 'login/painel/conn.php';
    include 'login/painel/estilo.php';
    include 'login/painel/css_de_icones.php';
    include 'login/painel/config_dados.php';
    include 'login/painel/funcoes.php';
    include 'login/painel/menu.php';

    $sql_busca_clientes = "SELECT * FROM clientes WHERE id_agendamento = '" . mysqli_real_escape_string($conn, $idd) . "'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    $total_busca_clientes = mysqli_num_rows($query_busca_clientes);

    $nome = null;
    $telefone = null;
    $usuario_api = null;

    while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
        $nome = $rows_clientes['nome'];
        $telefone = $rows_clientes['telefone'];
        $usuario_api = $rows_clientes['usuario_api'];
    }
    
    $icon_path = 'login/painel/'.$icon; 
    $tema = $tema_global ?? 5; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?=htmlspecialchars($titulo_global ?? 'Agendamento');?> - Agendamento Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?=htmlspecialchars($icon_path);?>" type="image/png" sizes="16x16">
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
        
        /* Header Simplificado */
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
        
        /* Container Principal */
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
        
        /* Títulos e Textos */
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
        
        /* Stepper - Indicador de Progresso */
        .progress-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .progress-stepper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step {
            position: relative;
            flex: 1;
            text-align: center;
            z-index: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            background: #e0e0e0;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background: var(--gradient);
            transform: scale(1.1);
        }
        
        .step.completed .step-circle {
            background: var(--secondary-color);
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #666;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Cards de Serviço */
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
        
        /* Formulários */
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
            background-color: #f9f9f9;
            font-family: inherit;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
            background-color: white;
            outline: none;
        }
        
        select.form-control {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 20px;
            padding-right: 45px;
        }
        
        /* Resumo do Agendamento */
        .booking-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
            border: 1px solid #dee2e6;
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
        
        /* Botões */
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
        
        /* Loading State */
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
        
        /* Alerta de Visitantes */
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
        
        /* Helper Text */
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
        
        /* Empty State */
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
        
        /* Responsividade */
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
            }
            
            .step-label {
                font-size: 0.75rem;
            }
            
            .form-control {
                padding: 12px;
                font-size: 0.95rem;
            }
            
            .btn-primary {
                padding: 14px;
                font-size: 1rem;
            }
        }
        
        /* Animações de Transição */
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
        
        /* Scroll suave */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <!-- Header Simplificado -->
    <header class="header-top">
        <h1><i class="fas fa-calendar-check"></i> Agendamento Online</h1>
    </header>

    <?php
    // Função para conectar ao banco de dados
    function conectarDB() {
        include 'login/painel/conn.php';
        return $conn;
    }
    ?>

    <!-- Container Principal -->
    <div class="main-container">
        <?php if($nome == null): ?>
            <!-- Etapa 1: Solicitar Nome -->
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
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               placeholder="Digite seu nome completo" 
                               required
                               autocomplete="name">
                        <input type="hidden" name="idd" value="<?=htmlspecialchars($idd);?>">
                        <p class="help-text">
                            <i class="fas fa-info-circle"></i>
                            Use seu nome completo para facilitar a identificação
                        </p>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Continuar
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
            
        <?php else: ?>
            <!-- Etapa 2: Formulário de Agendamento -->
            <div class="booking-card">
                <!-- Progress Stepper -->
                <div class="progress-stepper">
                    <div class="step completed" id="step1">
                        <div class="step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-label">Identificação</div>
                    </div>
                    <div class="step active" id="step2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Profissional</div>
                    </div>
                    <div class="step" id="step3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Serviço</div>
                    </div>
                    <div class="step" id="step4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Dia e Horário</div>
                    </div>
                </div>
                
                <div class="section-title">
                    <i class="fas fa-calendar-plus"></i>
                    Olá <?=htmlspecialchars(explode(' ', $nome)[0]);?>! Vamos agendar seu atendimento
                </div>
                
                <form action="processar_agendamento.php" method="POST" id="formAgendamento">
                    <!-- Seleção do Profissional -->
                    <div class="form-group">
                        <label for="profissional">
                            <i class="fas fa-user-md"></i> 
                            Escolha o profissional
                        </label>
                        <select class="form-control" 
                                id="profissional" 
                                name="profissional" 
                                required 
                                onchange="carregarServicosDoProfissional()">
                            <option value=''>Selecione um profissional</option>
                            <?php
                            if ($usuario_api) {
                                $conn = conectarDB();
                                $sql = "SELECT * FROM profissional WHERE usuario_api = '" . mysqli_real_escape_string($conn, $usuario_api) . "'";
                                $result = mysqli_query($conn, $sql);
                                
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="'. htmlspecialchars($row['id']).'">'.htmlspecialchars($row['profissional_nome']) .' - '. htmlspecialchars($row['profissional_cargo']) .'</option>';
                                }
                                
                                mysqli_close($conn);
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Seleção do Serviço -->
                    <div class="form-group" id="servicoGroup" style="display: none;">
                        <label>
                            <i class="fas fa-concierge-bell"></i> 
                            Escolha o serviço desejado
                        </label>
                        <div id="servicos-lista" class="services-container">
                            <!-- Serviços serão carregados aqui via AJAX -->
                        </div>
                        <input type="hidden" id="servico_selecionado" name="servico_id" required>
                        <input type="hidden" id="duracao_servico" name="duracao_servico">
                        <input type="hidden" id="valor_servico" name="valor_servico">
                    </div>

                    <!-- Seleção do Dia da Semana -->
                    <div class="form-group" id="diaSemanaGroup" style="display: none;">
                        <label for="dia_semana">
                            <i class="fas fa-calendar-week"></i> 
                            Qual dia da semana?
                        </label>
                        <select class="form-control" 
                                id="dia_semana" 
                                name="dia_semana" 
                                required 
                                onchange="carregarAgendamentosDisponiveis()">
                            <option value="">Escolha um dia da semana</option>
                        </select>
                        <p class="help-text">
                            <i class="fas fa-lightbulb"></i>
                            Dias com mais horários disponíveis aparecerão primeiro
                        </p>
                    </div>

                    <!-- Seleção do Horário Disponível -->
                    <div class="form-group" id="horarioGroup" style="display: none;">
                        <label for="agendamento">
                            <i class="fas fa-clock"></i> 
                            Selecione o melhor horário
                        </label>
                        <select class="form-control" 
                                id="agendamento" 
                                name="agendamento" 
                                required
                                onchange="atualizarResumo()">
                            <option value="">Escolha uma data e horário</option>
                        </select>
                    </div>
                    
                    <!-- Resumo do Agendamento -->
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
                            <span class="summary-label">Dia da Semana:</span>
                            <span class="summary-value" id="resumo-dia">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Data e Horário:</span>
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
                    
                    <input type="hidden" name="usuario_api" value="<?=htmlspecialchars($usuario_api);?>">
                    <input type="hidden" name="idd" value="<?=htmlspecialchars($idd);?>">

                    <!-- Botão para Agendar -->
                    <button type="submit" 
                            class="btn-primary" 
                            id="btnAgendar" 
                            style="display: none; margin-top: 20px;">
                        <i class="fas fa-check-circle"></i>
                        Confirmar Agendamento
                    </button>
                </form>
            </div>
            
            <!-- Alerta de Visitantes -->
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
            // Variação mais realista
            const variacao = Math.random();
            if (variacao < 0.3) {
                visitas = Math.max(1, visitas - 1);
            } else if (variacao > 0.7) {
                visitas = Math.min(15, visitas + Math.floor(Math.random() * 2) + 1);
            }
            
            $('#contador-visitas').text(visitas);
            
            // Adiciona animação ao número
            $('#contador-visitas').addClass('fade-in');
            setTimeout(() => {
                $('#contador-visitas').removeClass('fade-in');
            }, 500);
        }
        
        // setInterval(atualizarVisitas, 8000);
        
        // Validação do formulário de nome
        $('#formNome').on('submit', function(e) {
            const nome = $('#nome').val().trim();
            if (nome.length < 3) {
                e.preventDefault();
                alert('Por favor, digite seu nome completo.');
                $('#nome').focus();
            }
        });
        
        // Função para atualizar o stepper
        function updateStepper(activeStep) {
            $('.step').removeClass('active completed');
            if (activeStep > 1) $('#step1').addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
            else $('#step1 .step-circle').text('1');
            if (activeStep > 2) $('#step2').addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
            else $('#step2 .step-circle').text('2');
            if (activeStep > 3) $('#step3').addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
            else $('#step3 .step-circle').text('3');
            if (activeStep > 4) $('#step4').addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
            else $('#step4 .step-circle').text('4');

            $('#step' + activeStep).addClass('active');
        }
        
        // Função para carregar serviços do profissional
        function carregarServicosDoProfissional() {
            const profissionalId = $('#profissional').val();
            const profissionalNome = $('#profissional option:selected').text();
            resetFormFrom('servico');
            
            if (profissionalId !== '') {
                updateStepper(3);
                $('#servicoGroup').fadeIn();
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
            }
        }
        
        // Ao clicar em um serviço
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
            
            carregarDiasSemana();
        });
        
        // Função para carregar dias da semana
        function carregarDiasSemana() {
            const profissionalId = $('#profissional').val();
            const servicoId = $('#servico_selecionado').val();
            
            if (profissionalId !== '' && servicoId !== '') {
                updateStepper(4);
                $('#diaSemanaGroup').fadeIn();
                $('#dia_semana').html('<option value="">Carregando...</option>');
                
                $.ajax({
                    url: 'buscar_dias_semana.php',
                    type: 'POST',
                    data: { 
                        profissional_id: profissionalId,
                        servico_id: servicoId
                    },
                    success: function(response) {
                        $('#dia_semana').html(response);
                        $('#agendamento').html('<option value="">Escolha uma data e horário</option>');
                        $('#horarioGroup').hide();
                        $('#btnAgendar').hide();
                        $('#resumo-agendamento').fadeIn();
                    },
                    error: function() {
                        $('#dia_semana').html('<option value="">Erro ao carregar dias</option>');
                    }
                });
            } else {
                $('#diaSemanaGroup').hide();
                $('#horarioGroup').hide();
                $('#btnAgendar').hide();
            }
        }
        
        // Função para carregar agendamentos disponíveis
        function carregarAgendamentosDisponiveis() {
            const profissionalId = $('#profissional').val();
            const servicoId = $('#servico_selecionado').val();
            const diaSemana = $('#dia_semana').val();
            const duracao = $('#duracao_servico').val();
            
            if (profissionalId !== '' && servicoId !== '' && diaSemana !== '') {
                $('#horarioGroup').fadeIn();
                $('#agendamento').html('<option value="">Carregando horários...</option>');
                
                $('#resumo-dia').text($('#dia_semana option:selected').text());
                
                $.ajax({
                    url: 'buscar_agendamentos_disponiveis.php',
                    type: 'POST',
                    data: { 
                        profissional_id: profissionalId,
                        servico_id: servicoId,
                        dia_semana: diaSemana,
                        duracao: duracao
                    },
                    success: function(response) {
                        $('#agendamento').html(response);
                        
                        // Verifica se há horários disponíveis
                        if ($('#agendamento option').length > 1) {
                            // Não mostra o botão ainda, só quando selecionar horário
                        } else {
                            $('#agendamento').html('<option value="">Nenhum horário disponível neste dia</option>');
                            $('#btnAgendar').hide();
                        }
                    },
                    error: function() {
                        $('#agendamento').html('<option value="">Erro ao carregar horários</option>');
                        $('#btnAgendar').hide();
                    }
                });
            } else {
                $('#horarioGroup').hide();
                $('#btnAgendar').hide();
            }
        }
        
        // Função para atualizar o resumo e fazer scroll
        function atualizarResumo() {
            const horario = $('#agendamento').val();
            
            if (horario !== '') {
                $('#resumo-horario').text($('#agendamento option:selected').text());
                $('#btnAgendar').fadeIn();
                
                // Scroll suave até o resumo
                setTimeout(() => {
                    const resumoElement = document.getElementById('resumo-agendamento');
                    const resumoPosition = resumoElement.getBoundingClientRect().top + window.pageYOffset - 100;
                    
                    window.scrollTo({
                        top: resumoPosition,
                        behavior: 'smooth'
                    });
                }, 100);
            } else {
                $('#btnAgendar').hide();
                $('#resumo-horario').text('-');
            }
        }
        
        // Função para resetar partes do formulário
        function resetFormFrom(step) {
            if (step === 'servico') {
                $('#servicoGroup').hide();
                $('#servicos-lista').empty();
                $('#servico_selecionado').val('');
                $('#duracao_servico').val('');
                $('#valor_servico').val('');
                $('#resumo-servico').text('-');
                $('#resumo-duracao').text('-');
                $('#resumo-valor').text('R$ 0,00');
                step = 'diaSemana';
            }
            if (step === 'diaSemana') {
                $('#diaSemanaGroup').hide();
                $('#dia_semana').html('<option value="">Escolha um dia da semana</option>');
                $('#resumo-dia').text('-');
                step = 'horario';
            }
            if (step === 'horario') {
                $('#horarioGroup').hide();
                $('#agendamento').html('<option value="">Escolha uma data e horário</option>');
                $('#resumo-horario').text('-');
            }
            
            $('#btnAgendar').hide();
            
            // Esconder o resumo se estiver resetando desde o início
            if (step === 'servico' || $('#profissional').val() === '') {
                $('#resumo-agendamento').hide();
                $('#resumo-profissional').text('-');
            }
        }
        
        // Validação antes de enviar o agendamento
        $('#formAgendamento').on('submit', function(e) {
            const profissional = $('#profissional').val();
            const servico = $('#servico_selecionado').val();
            const diaSemana = $('#dia_semana').val();
            const horario = $('#agendamento').val();
            
            if (!profissional || !servico || !diaSemana || !horario) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos antes de agendar.');
                return false;
            }
            
            // Adiciona loading ao botão
            const $btn = $('#btnAgendar');
            const textoOriginal = $btn.html();
            $btn.prop('disabled', true).html('<span class="loading"></span> Processando...');
        });
        
        // Inicializa o stepper
        $(document).ready(function() {
            if ($('#formAgendamento').length) {
                updateStepper(2);
            } else {
                updateStepper(1);
            }
            
            // Reset inicial se necessário
            if ($('#profissional').val() === '') {
                resetFormFrom('servico');
            }
        });
    </script>

</body>
</html>
<?php
}
?>