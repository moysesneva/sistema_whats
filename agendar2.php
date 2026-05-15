<?php
// Nota: É uma boa prática usar include_once ou require_once para arquivos de configuração e conexão
// para evitar problemas com redeclarações ou múltiplas conexões.
// Ex: include_once 'login/painel/conn.php';

$idd = $_GET['id'] ?? null; // Usar operador de coalescência nula para evitar notice se 'id' não existir

if (empty($idd)) { // Verifica se $idd é nulo ou vazio
    header("Location: index.php");
    exit; // Sempre adicione exit após um redirecionamento de header
}

// Exemplo de uso
// O if($idd) já foi verificado acima, então não é estritamente necessário aqui novamente,
// mas não causa mal.
if($idd){
    include 'login/painel/conn.php';
    include 'login/painel/estilo.php';
    include 'login/painel/css_de_icones.php';
    include 'login/painel/config_dados.php';
    include 'login/painel/funcoes.php';
    include 'login/painel/menu.php';

    // ALERTA DE SEGURANÇA: A query abaixo é vulnerável a SQL Injection.
    // Considere usar Prepared Statements.
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($titulo_global ?? 'Agendamento'); ?> - Agendamento Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= htmlspecialchars($icon_path); ?>" type="image/png" sizes="16x16">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* SEU CSS EXISTENTE VAI AQUI - SEM ALTERAÇÕES SIGNIFICATIVAS NELE AINDA */
        /* ... (todo o seu CSS :root, body, .header-top, etc.) ... */
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
        
        /* NOVO: Estilo para agrupar horários por data com collapse */
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
            transition: max-height 0.3s ease;
            padding: 0 20px;
        }
        
        .time-slots-wrapper.show {
            max-height: 500px;
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
        
        /* Animação de scroll suave */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <header class="header-top">
        <h1><i class="fas fa-calendar-check"></i> Agendamento Online</h1>
    </header>

    <?php
    // Função para conectar ao banco de dados
    // Nota: Se conn.php já define $conn globalmente, esta função pode ser desnecessária
    // ou pode causar problemas se não usar include_once no conn.php.
    function conectarDB_local() { // Renomeado para evitar conflito se houver outra função conectarDB
        global $conn_global_for_page; // Usar uma variável global específica para esta página se necessário
        if (isset($conn_global_for_page) && $conn_global_for_page instanceof mysqli) {
            return $conn_global_for_page;
        }
        // Se não, tenta incluir e estabelecer a conexão
        // É crucial que conn.php defina uma variável (ex: $db_connection) e a retorne, ou defina $conn global.
        // Este é um ponto que precisa de atenção na estrutura dos seus includes.
        // Para este exemplo, assumo que conn.php define $conn e pode ser incluído novamente.
        // O ideal é que conn.php use include_once e retorne a conexão ou a defina de forma segura.
        include 'login/painel/conn.php'; // Este $conn será o escopo local da função.
        return $conn; // Retorna a conexão estabelecida por este include.
    }
    ?>

    <div class="main-container">
        <?php if($nome == null): // Verifica se $nome é null após a busca no banco ?>
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
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome completo" required autocomplete="name">
                        <input type="hidden" name="idd" value="<?= htmlspecialchars($idd); ?>">
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
            <div class="booking-card">
                <div class="progress-stepper">
                    <div class="step completed" id="step1"> <div class="step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-label">Identificação</div>
                    </div>
                    <div class="step active" id="step2"> <div class="step-circle">2</div>
                        <div class="step-label">Profissional</div>
                    </div>
                    <div class="step" id="step3"> <div class="step-circle">3</div>
                        <div class="step-label">Serviço</div>
                    </div>
                    <div class="step" id="step4"> <div class="step-circle">4</div>
                        <div class="step-label">Dia e Horário</div>
                    </div>
                </div>
                
                <div class="section-title">
                    <i class="fas fa-calendar-plus"></i>
                    Olá <?= htmlspecialchars(isset($nome) ? explode(' ', $nome)[0] : 'Cliente'); ?>! Vamos agendar seu atendimento
                </div>
                
                <form action="processar_agendamento_servico.php" method="POST" id="formAgendamento">
                    <div class="form-group">
                        <label for="profissional">
                            <i class="fas fa-user-md"></i> 
                            Escolha o profissional
                        </label>
                        <select class="form-control" id="profissional" name="profissional" required onchange="carregarServicosDoProfissional()"> <option value=''>Selecione um profissional</option>
                            <?php
                            if ($usuario_api) {
                                // ALERTA DE SEGURANÇA: A query abaixo é vulnerável a SQL Injection.
                                // Considere usar Prepared Statements.
                                // Usar a conexão $conn já estabelecida no início da página, se possível e seguro.
                                // Se for necessário uma nova conexão, certifique-se que `conectarDB_local()` lida com isso corretamente.
                                $conn_prof_select = conectarDB_local();
                                $sql_prof = "SELECT id, profissional_nome, profissional_cargo FROM profissional WHERE usuario_api = '" . mysqli_real_escape_string($conn_prof_select, $usuario_api) . "'";
                                $result_prof = mysqli_query($conn_prof_select, $sql_prof);
                                
                                if ($result_prof) {
                                    while ($row_prof = mysqli_fetch_assoc($result_prof)) {
                                        echo '<option value="'. htmlspecialchars($row_prof['id']).'">'.htmlspecialchars($row_prof['profissional_nome']) .' - '. htmlspecialchars($row_prof['profissional_cargo']) .'</option>';
                                    }
                                }
                                // Não feche $conn_prof_select aqui se ela for a mesma conexão global $conn.
                                // Se conectarDB_local() sempre cria uma nova, então o close é apropriado se ela não for mais usada.
                                // if ($conn_prof_select !== $conn) { mysqli_close($conn_prof_select); }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" id="servicoGroup" style="display: none;">
                        <label>
                            <i class="fas fa-concierge-bell"></i> 
                            Escolha o serviço desejado
                        </label>
                        <div id="servicos-lista" class="services-container">
                            </div>
                        <input type="hidden" id="servico_selecionado" name="servico_id" required>
                        <input type="hidden" id="duracao_servico" name="duracao_servico">
                        <input type="hidden" id="valor_servico" name="valor_servico">
                    </div>

                    <div class="form-group" id="diaHorarioGroup" style="display: none;">
                        <label>
                            <i class="fas fa-calendar-alt"></i> <i class="fas fa-clock" style="margin-left: 5px;"></i> 
                            Selecione o dia e horário
                        </label>
                        
                        <div class="time-slots-container" id="dias-horarios-lista">
                            </div>
                        
                        <input type="hidden" id="horario_selecionado" name="horario" required>
                        <input type="hidden" id="data_selecionada" name="data" required>
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
    // Sistema de visitantes online (sem alterações)
    let visitas = 3;
    function atualizarVisitas() { 
        visitas = Math.floor(Math.random() * 5) + 2; // Entre 2 e 6 visitantes
        $('#contador-visitas').text(visitas);
    }
    // setInterval(atualizarVisitas, 8000); // Descomente se quiser

    // Validação do formulário de nome (sem alterações)
    $('#formNome').on('submit', function(e) { 
        const nome = $('#nome').val().trim();
        if (nome.length < 3) {
            e.preventDefault();
            alert('Por favor, digite seu nome completo.');
            return false;
        }
    });

    // --- NOVA LÓGICA DE AGENDAMENTO ---

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

    // AO SELECIONAR PROFISSIONAL, CARREGAR SERVIÇOS DELE
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

    // AO CLICAR EM UM SERVIÇO, CARREGAR DIAS E HORÁRIOS DISPONÍVEIS
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

        resetFormFrom('diaHorario');
        carregarDiasHorariosDisponiveis();
    });

    function carregarDiasHorariosDisponiveis() {
        const profissionalId = $('#profissional').val();
        const servicoId = $('#servico_selecionado').val();
        const duracao = $('#duracao_servico').val();

        if (profissionalId && servicoId && duracao) {
            updateStepper(4);
            $('#diaHorarioGroup').fadeIn();
            $('#resumo-agendamento').fadeIn();

            $('#dias-horarios-lista').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Carregando dias e horários...</div>');

            $.ajax({
                url: 'buscar_dias_e_horarios_para_servico.php',
                type: 'POST',
                dataType: 'json',  // espera JSON do servidor
                data: {
                    profissional_id: profissionalId,
                    servico_id: servicoId,
                    duracao: duracao
                },
                success: function(dataSlots) {
                    // dataSlots já é um array de objetos JavaScript
                    $('#dias-horarios-lista').empty();

                    if (dataSlots && typeof dataSlots.error !== 'undefined') {
                        // Se o PHP retornou um erro JSON customizado
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
                                // Ajuste de fuso para evitar "um dia antes"
                                const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
                                const dateInUserTimezone = new Date(dateObj.getTime() + userTimezoneOffset);

                                const formattedDate = dateInUserTimezone.toLocaleDateString('pt-BR', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                });

                                // Modificação: Adicionar estrutura com collapse
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

                        // Adicionar dica para o usuário
                        if ($('.date-slots-group').length > 0) {
                            $('#dias-horarios-lista').prepend(
                                '<p class="help-text" style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Clique em uma data para ver os horários disponíveis</p>'
                            );
                        }
                    } else {
                        // Resposta vazia ou formato inesperado não coberto acima
                        $('#dias-horarios-lista').html(
                            '<div class="empty-state"><i class="fas fa-calendar-times"></i><p>Não há horários disponíveis para este serviço com este profissional nos próximos dias (resposta vazia).</p></div>'
                        );
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Erro na requisição AJAX:", textStatus, errorThrown, jqXHR.responseText);
                    $('#dias-horarios-lista').html(
                        '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Erro ao conectar com o servidor para buscar horários. Verifique o console para detalhes.</p></div>'
                    );
                }
            });
        }
    }

    // Função para expandir/contrair horários de uma data
    function toggleDateSlots(index) {
        const wrapper = $(`#slots-${index}`);
        const header = $(`.date-slots-group[data-date-index="${index}"] .date-header`);
        const group = $(`.date-slots-group[data-date-index="${index}"]`);
        
        // Fechar todos os outros grupos abertos
        $('.time-slots-wrapper').not(wrapper).removeClass('show');
        $('.date-header').not(header).removeClass('active');
        $('.date-slots-group').not(group).removeClass('expanded');
        
        // Toggle do grupo clicado
        wrapper.toggleClass('show');
        header.toggleClass('active');
        group.toggleClass('expanded');
        
        // Se estiver abrindo, fazer scroll suave até o grupo
        if (wrapper.hasClass('show')) {
            setTimeout(() => {
                $('html, body').animate({
                    scrollTop: group.offset().top - 100
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

        $('#btnAgendar').fadeIn();

        // NOVO: Scroll suave até o resumo do agendamento
        setTimeout(() => {
            const resumoElement = document.getElementById('resumo-agendamento');
            const resumoPosition = resumoElement.getBoundingClientRect().top + window.pageYOffset - 100;
            
            window.scrollTo({
                top: resumoPosition,
                behavior: 'smooth'
            });
        }, 100);
    });

    // Função para resetar partes do formulário quando uma seleção anterior muda
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
            step = 'diaHorario'; // Força o reset do próximo passo também
        }
        if (step === 'diaHorario') {
            $('#diaHorarioGroup').hide();
            $('#dias-horarios-lista').empty();
            $('#horario_selecionado').val('');
            $('#data_selecionada').val('');
            $('#resumo-data').text('-');
            $('#resumo-horario').text('-');
        }

        $('#btnAgendar').hide();
        // Esconder o resumo completo se estivermos resetando a partir do profissional ou serviço
        if (step === 'servico' || $('#profissional').val() === '') {
            $('#resumo-agendamento').hide();
            $('#resumo-profissional').text('-'); // Limpa também o profissional do resumo
        } else if (step === 'diaHorario') {
            // Mantém o resumo visível se já tiver profissional e serviço
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
        // A lógica de redirecionamento ou mensagem de sucesso/erro virá do 'processar_agendamento_servico.php'
    });

    // Inicializa o stepper
    $(document).ready(function() {
        if ($('#formAgendamento').length) {
            updateStepper(2);
        } else {
            updateStepper(1); // Se estiver no formulário de nome
        }
        // Adiciona um reset inicial para profissional caso a página seja recarregada
        // ou o usuário volte para ela.
        if ($('#profissional').val() === '') {
            resetFormFrom('servico');
        }
    });
</script>

</body>
</html>
<?php
} else {
    // Tratar caso $idd não exista ou $usuario_api não seja encontrado após a busca inicial
    // (Este else corresponde ao `if($idd)` do início, não ao `if($nome == null)`)
    // No entanto, o primeiro `if (empty($idd))` já faz o redirect.
    // Este bloco pode ser alcançado se $idd for fornecido mas, por exemplo,
    // os includes falharem ou $conn não for estabelecida.
    // Para segurança, é bom ter um fallback.
    echo "<!DOCTYPE html><html lang='pt-BR'><head><title>Erro</title></head><body>";
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>";
    echo "<h1>Erro no Agendamento</h1>";
    echo "<p>Ocorreu um problema ao carregar a página de agendamento. O link pode ser inválido ou dados essenciais não foram encontrados.</p>";
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
    echo "</div></body></html>";
}
?>