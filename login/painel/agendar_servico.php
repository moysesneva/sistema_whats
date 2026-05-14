<?php
session_start();

include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';


if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $login  = $rows_usuarios['login'];

}
#####DEFINIMOS QUE  O TIPO DO MENU
## 1 É O ADM
## 2 É  O USUARIO
include 'menu.php';


if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
 VaiPara('desbloquar.php');
}




?>
<!DOCTYPE html>
<html lang="pt-br">


  

</style>
<head>
    <title><?=$titulo;?></title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <!-- Favicon icon -->
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="feather icon-menu"></i>
                        </a>
                        <a href="index.php">
                            <img class="img-fluid" src="<?=$logo;?>" alt="Theme-Logo" style="width: 150px; height: 30px;">
                        </a>
                        <a class="mobile-options">
                            <i class="feather icon-more-horizontal"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="feather icon-x"></i></span>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="feather icon-maximize full-screen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                          
                            </li>
                            <li class="user-profile header-notification">
                                <div class="dropdown-primary dropdown">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?=$img_perfil;?>" class="img-radius" alt="User-Profile-Image">
                                        <span><?=$nome?></span>
                                        <i class="feather icon-chevron-down"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <li>
                                            <a href="config.php">
                                                <i class="feather icon-settings"></i> Configurações
                                            </a>
                                        </li>
                                        <li>
                                            <a href="perfil.php">
                                                <i class="feather icon-user"></i> Perfil
                                            </a>
                                        </li>
                                       
                                        
                                        <li>
                                            <a href="sair.php">
                                                <i class="feather icon-log-out"></i> Sair
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Sidebar chat start -->
          

   
            <!-- Sidebar inner chat end-->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="pcoded-navigatio-lavel">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">



 <?php 

if ($total_menu > 0) {
    // Itera sobre os resultados e gera o HTML dinâmico
    while ($row_menu = mysqli_fetch_array($query_menu)) {
        // Atribui os valores dos campos a variáveis com o sufixo _menu
        $id = $row_menu['id'];
        $menu_nome = $row_menu['menu'];
        $menu_pagina_menu = $row_menu['menu_pagina'];
        $tipo_menu = $row_menu['tipo'];
        $icone_menu = $row_menu['icone_menu'];

        // Gera a estrutura HTML para cada item do menu
   if ($id == $pagina_nome_recebe){     
echo   '<li class="pcoded-hasmenu active">';
}else{
  echo   '<li class="pcoded-hasmenu">';
  
}
        echo '
        
            <a href="' . $menu_pagina_menu . '?pagina_nome='.$id .' ">
                <span class="pcoded-micon"><i class="'. $icone_menu . '"></i></span>
                <span class="pcoded-mtext">' . $menu_nome . '</span>
            </a>
        </li>';
    }
}
?>


     </ul>                                                        
                       
</div>
</nav>
                    <div class="row d-flex justify-content-center">
                        <div class="row d-flex justify-content-center">
                            <div class="main-body">
                                <div class="row d-flex justify-content-center">


    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
   
   <!-- Formulário para solicitar confirmação de agendamento -->



<?php
// =========================================================================
// INÍCIO: BLOCO DE CONFIGURAÇÃO E AUTENTICAÇÃO (de agendar_servico.php)
// =========================================================================

$login = $_SESSION['login'];

// Includes essenciais para o funcionamento da página e conexão
include 'conn.php'; // Conexão principal com o banco de dados
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

// Busca os dados do usuário logado (administrador/operador)
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if ($total_busca_usuario == 1) {
    $rows_usuarios = mysqli_fetch_array($query_busca_usuario);
    $nome_usuario_logado  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo_usuario  = $rows_usuarios['tipo'];
} else {
    // Se o usuário não for encontrado, desloga e redireciona
    VaiPara('login.php');
}

// Verificação de autorização do usuário
if($autorizado != 2){
    VaiPara('desbloquar.php');
}

// Inclui o menu do sistema
include 'menu.php';

// =========================================================================
// INÍCIO: LÓGICA DE CONTROLE - BUSCAR OU AGENDAR
// =========================================================================

// Variáveis para armazenar os dados do cliente
$cliente_selecionado_id = null;
$nome_cliente = null;
$telefone_cliente = null;
$idd_agendamento = null;
$usuario_api = null;
$tema = 1; // Tema padrão

// Função para estabelecer uma conexão local (usada em várias partes)
// Idealmente, a conexão $conn principal já deveria ser usada.
function conectarDB_local() {
    include 'conn.php';
    return $conn;
}


// PASSO 1: VERIFICAR SE UM CLIENTE FOI SELECIONADO NA BUSCA
if (isset($_POST['cliente_selecionado']) && !empty($_POST['cliente_selecionado'])) {
    $cliente_selecionado_id = intval($_POST['cliente_selecionado']);
    
    // Conecta e busca os dados completos do cliente selecionado
    $conn_cliente = conectarDB_local();
    $sql_cliente = "SELECT * FROM clientes WHERE id = ?";
    if ($stmt = mysqli_prepare($conn_cliente, $sql_cliente)) {
        mysqli_stmt_bind_param($stmt, "i", $cliente_selecionado_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $cliente_data = mysqli_fetch_assoc($result);
            $nome_cliente = $cliente_data['nome'];
            $telefone_cliente = $cliente_data['telefone'];
            $idd_agendamento = $cliente_data['id_agendamento']; // Campo importante para o fluxo
            $usuario_api = $cliente_data['usuario_api'] ?? null;
        }
        mysqli_stmt_close($stmt);
    }
    // Não feche a conexão se ela for usada em outras partes da página

    // Se o cliente foi encontrado, busca as configurações da API dele
    // CÓDIGO CORRIGIDO:
// Busca a configuração de tema global, pois a tabela 'config' não é por usuário

// A variável $conn já deve existir da conexão principal no topo do arquivo
$sql_busca_config = "SELECT tema FROM config LIMIT 1"; 
$query_busca_config = mysqli_query($conn, $sql_busca_config);

if($query_busca_config && mysqli_num_rows($query_busca_config) > 0) {
    $rows_config = mysqli_fetch_assoc($query_busca_config);
    $tema = (int)$rows_config['tema'];
}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">

    <style>
        /* CSS unificado e aprimorado de agendar2.php */
        :root {
            <?php
            // Lógica de temas de cor
            switch ($tema) {
                case 1: // Roxo e Azul
                    echo "--primary-color: #3a0ca3; --secondary-color: #4cc9f0; --accent-color: #f72585; --dark-color: #2b2d42; --light-color: #f8f9fa; --gradient: linear-gradient(120deg, #7209b7, #3a0ca3); --gradient-hover: linear-gradient(120deg, #3a0ca3, #7209b7);";
                    break;
                case 2: // Verde e Aqua
                    echo "--primary-color: #06d6a0; --secondary-color: #1b9aaa; --accent-color: #ff9f1c; --dark-color: #1d3557; --light-color: #f1faee; --gradient: linear-gradient(120deg, #06d6a0, #1b9aaa); --gradient-hover: linear-gradient(120deg, #1b9aaa, #06d6a0);";
                    break;
                // Adicione outros casos de tema aqui se necessário
                default:
                    echo "--primary-color: #007bff; --secondary-color: #28a745; --accent-color: #ffc107; --dark-color: #343a40; --light-color: #f8f9fa; --gradient: linear-gradient(120deg, #007bff, #0056b3); --gradient-hover: linear-gradient(120deg, #0056b3, #007bff);";
            }
            ?>
        }
        
        body { font-family: 'Inter', sans-serif; }

        .booking-card {
            background: white; border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px 30px; margin-bottom: 20px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .section-title {
            color: var(--primary-color); font-size: 1.5rem; font-weight: 600;
            text-align: center; margin-bottom: 30px; position: relative;
        }
        
        .section-title i { font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.8; }
        
        .welcome-text { text-align: center; color: #666; margin-bottom: 30px; font-size: 1rem; }
        
        /* Estilos para o Stepper, Formulários, Botões, Resumo, etc. (copiados de agendar2.php) */
        .progress-stepper { display: flex; justify-content: space-between; margin-bottom: 40px; position: relative; }
        .progress-stepper::before { content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: #e0e0e0; z-index: 0; }
        .step { position: relative; flex: 1; text-align: center; z-index: 1; }
        .step-circle { width: 40px; height: 40px; background: #e0e0e0; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-weight: 600; color: white; transition: all 0.3s ease; }
        .step.active .step-circle { background: var(--gradient); transform: scale(1.1); }
        .step.completed .step-circle { background: var(--secondary-color); }
        .step-label { font-size: 0.85rem; color: #666; }
        .step.active .step-label { color: var(--primary-color); font-weight: 500; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: flex; align-items: center; color: var(--dark-color); font-weight: 500; margin-bottom: 10px; font-size: 0.95rem; }
        .form-group label i { margin-right: 8px; color: var(--primary-color); font-size: 1.1rem; }
        .form-control { width: 100%; padding: 15px; font-size: 1rem; border: 2px solid #e8e8e8; border-radius: 12px; transition: all 0.3s ease; background-color: #f9f9f9; font-family: inherit; }
        .form-control:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1); background-color: white; outline: none; }
        .btn-primary { width: 100%; padding: 16px; font-size: 1.1rem; font-weight: 600; color: white; background: var(--gradient); border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-primary:hover { background: var(--gradient-hover); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        select.form-control { cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; background-size: 20px; padding-right: 45px; }
        .cliente-resultado { border: 2px solid #e8e8e8; border-radius: 12px; padding: 15px; margin: 10px 0; cursor: pointer; transition: all 0.3s ease; background-color: #f9f9f9; }
        .cliente-resultado:hover { border-color: var(--secondary-color); background-color: white; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .service-card { border: 2px solid #e8e8e8; border-radius: 12px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease; background: #f9f9f9; }
        .service-card:hover { border-color: var(--secondary-color); background: white; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .service-card.selected { border-color: var(--primary-color); background: var(--light-color); box-shadow: 0 5px 20px rgba(155,93,229,0.2); }
        .time-slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
        .time-slot { padding: 12px; border: 2px solid #e8e8e8; border-radius: 10px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: white; font-weight: 500; }
        .time-slot:hover { border-color: var(--secondary-color); background: var(--light-color); transform: translateY(-2px); }
        .time-slot.selected { border-color: var(--primary-color); background: var(--gradient); color: white; }
        .booking-summary { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 20px; margin-top: 25px; border: 1px solid #dee2e6; }
        .booking-summary h4 { color: var(--primary-color); font-size: 1.1rem; margin-bottom: 15px; font-weight: 600; }
        .summary-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
        .summary-item:last-child { border-bottom: none; }
        .loading { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 1s ease-in-out infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>

<body>

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-8 col-md-10">
                                        <?php if ($nome_cliente == null): // PASSO 1: MOSTRAR BUSCA DE CLIENTE ?>
                                            
                                            <div class="booking-card">
                                                <div class="section-title">
                                                    <i class="fas fa-user-search"></i>
                                                    Buscar Cliente para Agendar
                                                </div>
                                                <p class="welcome-text">
                                                    Digite o nome ou telefone para buscar o cliente no sistema.
                                                </p>
                                                
                                                <div class="form-group">
                                                    <label for="nome_busca"><i class="fas fa-user"></i> Nome do Cliente</label>
                                                    <input type="text" class="form-control" id="nome_busca" onkeyup="buscarCliente()" placeholder="Digite o nome do cliente">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="telefone_busca"><i class="fas fa-phone"></i> Telefone do Cliente</label>
                                                    <input type="text" class="form-control" id="telefone_busca" onkeyup="buscarCliente()" placeholder="Digite o telefone do cliente">
                                                </div>
                                                
                                                <div id="resultadoBusca"></div>
                                            </div>

                                        <?php else: // PASSO 2: MOSTRAR FLUXO DE AGENDAMENTO ?>
                                            
                                            <div class="booking-card">
                                                <div class="progress-stepper">
                                                    <div class="step completed" id="step1"><div class="step-circle"><i class="fas fa-check"></i></div><div class="step-label">Cliente</div></div>
                                                    <div class="step active" id="step2"><div class="step-circle">2</div><div class="step-label">Profissional</div></div>
                                                    <div class="step" id="step3"><div class="step-circle">3</div><div class="step-label">Serviço</div></div>
                                                    <div class="step" id="step4"><div class="step-circle">4</div><div class="step-label">Dia e Horário</div></div>
                                                </div>
                                                
                                                <div class="section-title">
                                                    <i class="fas fa-calendar-plus"></i>
                                                    Olá <?= htmlspecialchars(explode(' ', $nome_cliente)[0]); ?>! Vamos agendar seu atendimento.
                                                </div>
                                                
                                                <form action="processar_agendamento_servico.php" method="POST" id="formAgendamento">
                                                    <div class="form-group">
                                                        <label for="profissional"><i class="fas fa-user-md"></i> Escolha o profissional</label>
                                                        <select class="form-control" id="profissional" name="profissional" required onchange="carregarServicosDoProfissional()">
                                                            <option value=''>Selecione um profissional</option>
                                                            <?php
                                                            if ($usuario_api) {
                                                                $conn_prof = conectarDB_local();
                                                                $sql_prof = "SELECT id, profissional_nome, profissional_cargo FROM profissional WHERE usuario_api = '" . mysqli_real_escape_string($conn_prof, $usuario_api) . "'";
                                                                $result_prof = mysqli_query($conn_prof, $sql_prof);
                                                                if ($result_prof) {
                                                                    while ($row_prof = mysqli_fetch_assoc($result_prof)) {
                                                                        echo '<option value="'. htmlspecialchars($row_prof['id']).'">'.htmlspecialchars($row_prof['profissional_nome']) .' - '. htmlspecialchars($row_prof['profissional_cargo']) .'</option>';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group" id="servicoGroup" style="display: none;">
                                                        <label><i class="fas fa-concierge-bell"></i> Escolha o serviço desejado</label>
                                                        <div id="servicos-lista" class="services-container"></div>
                                                    </div>

                                                    <div class="form-group" id="diaHorarioGroup" style="display: none;">
                                                        <label><i class="fas fa-calendar-alt"></i> Selecione o dia e horário</label>
                                                        <div id="dias-horarios-lista"></div>
                                                    </div>
                                                    
                                                    <div class="booking-summary" id="resumo-agendamento" style="display: none;">
                                                        <h4><i class="fas fa-receipt"></i> Resumo do Agendamento</h4>
                                                        <div class="summary-item"><span>Cliente:</span><span><?= htmlspecialchars($nome_cliente); ?></span></div>
                                                        <div class="summary-item"><span>Telefone:</span><span><?= htmlspecialchars($telefone_cliente); ?></span></div>
                                                        <div class="summary-item"><span>Profissional:</span><span id="resumo-profissional">-</span></div>
                                                        <div class="summary-item"><span>Serviço:</span><span id="resumo-servico">-</span></div>
                                                        <div class="summary-item"><span>Data:</span><span id="resumo-data">-</span></div>
                                                        <div class="summary-item"><span>Horário:</span><span id="resumo-horario">-</span></div>
                                                        <div class="summary-item"><span>Duração:</span><span id="resumo-duracao">-</span></div>
                                                        <div class="summary-item"><span>Valor Total:</span><span id="resumo-valor">R$ 0,00</span></div>
                                                    </div>
                                                    
                                                    <input type="hidden" name="usuario_api" value="<?= htmlspecialchars($usuario_api ?? ''); ?>">
                                                    <input type="hidden" name="idd" value="<?= htmlspecialchars($idd_agendamento); ?>">
                                                    <input type="hidden" name="cliente_nome" value="<?= htmlspecialchars($nome_cliente); ?>">
                                                    <input type="hidden" name="cliente_telefone" value="<?= htmlspecialchars($telefone_cliente); ?>">
                                                    <input type="hidden" id="servico_selecionado" name="servico_id" required>
                                                    <input type="hidden" id="duracao_servico" name="duracao_servico">
                                                    <input type="hidden" id="valor_servico" name="valor_servico">
                                                    <input type="hidden" id="horario_selecionado" name="horario" required>
                                                    <input type="hidden" id="data_selecionada" name="data" required>

                                                    <button type="submit" class="btn-primary" id="btnAgendar" style="display: none; margin-top: 20px;">
                                                        <i class="fas fa-check-circle"></i>
                                                        Confirmar Agendamento
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <script src="..\files\assets\js\pcoded.min.js"></script> 
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>

<?php
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $usuario_api  = $rows_usuarios['usuario_api'];
}

?>
    <script>
    // =========================================================================
    // JAVASCRIPT UNIFICADO
    // =========================================================================

    // PARTE 1: FUNÇÕES PARA BUSCA DE CLIENTE (de agendar_servico.php)
    function buscarCliente() {
        var nome = $('#nome_busca').val();
        var telefone = $('#telefone_busca').val();
        
        if (nome.length >= 2 || telefone.length >= 2) {
            $.ajax({
                url: "buscar_cliente_com_idd.php",
                type: "GET",
                   data: { 
        nome: nome, 
        telefone: telefone,
        usuario_api: '<?=$usuario_api?>' // ou outra variável JS
    },
                success: function(response) {
                    $('#resultadoBusca').html(response);
                },
                error: function() {
                     $('#resultadoBusca').html('<p class="text-danger">Erro ao buscar clientes.</p>');
                }
            });
        } else {
            $('#resultadoBusca').html('');
        }
    }

    function selecionarCliente(clienteId) {
        // Cria um formulário oculto para enviar o ID do cliente via POST para a mesma página
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; // Envia para a própria página
        
        var inputCliente = document.createElement('input');
        inputCliente.type = 'hidden';
        inputCliente.name = 'cliente_selecionado';
        inputCliente.value = clienteId;
        
        form.appendChild(inputCliente);
        document.body.appendChild(form);
        form.submit();
    }


    <?php if ($nome_cliente !== null): // PARTE 2: LÓGICA DE AGENDAMENTO (de agendar2.php) ?>
    
    $(document).ready(function() {
        updateStepper(2);
    });

    function updateStepper(activeStep) {
        $('.step').removeClass('active completed');
        for (let i = 1; i < activeStep; i++) {
            $('#step' + i).addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
        }
        $('#step' + activeStep).addClass('active').find('.step-circle').text(activeStep);
    }

    function resetFormFrom(step) {
        if (step === 'servico') {
            $('#servicoGroup').hide();
            $('#servicos-lista').empty();
            $('#servico_selecionado').val('');
            $('#resumo-servico, #resumo-duracao, #resumo-valor').text('-');
            step = 'diaHorario'; // Continua para resetar o próximo passo
        }
        if (step === 'diaHorario') {
            $('#diaHorarioGroup').hide();
            $('#dias-horarios-lista').empty();
            $('#horario_selecionado').val('');
            $('#data_selecionada').val('');
            $('#resumo-data, #resumo-horario').text('-');
        }
        $('#btnAgendar').hide();
        if ($('#profissional').val() === '') {
            $('#resumo-agendamento').hide();
            $('#resumo-profissional').text('-');
        }
    }

    function carregarServicosDoProfissional() {
        const profissionalId = $('#profissional').val();
        const profissionalNome = $('#profissional option:selected').text();
        resetFormFrom('servico');

        if (profissionalId) {
            updateStepper(3);
            $('#servicoGroup').fadeIn();
            $('#servicos-lista').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>');
            $('#resumo-profissional').text(profissionalNome);
            
            $.ajax({
                url: 'buscar_servicos_do_profissional.php',
                type: 'POST',
                data: { profissional_id: profissionalId },
                success: function(response) {
                    $('#servicos-lista').html(response);
                }
            });
        }
    }

    $(document).on('click', '.service-card', function() {
        $('.service-card').removeClass('selected');
        $(this).addClass('selected');

        $('#servico_selecionado').val($(this).data('servico-id'));
        $('#duracao_servico').val($(this).data('duracao'));
        $('#valor_servico').val($(this).data('valor'));

        $('#resumo-servico').text($(this).data('servico-nome'));
        $('#resumo-duracao').text($(this).data('duracao') + ' min');
        $('#resumo-valor').text('R$ ' + parseFloat($(this).data('valor')).toFixed(2).replace('.', ','));

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
            $('#dias-horarios-lista').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>');

            $.ajax({
                url: 'buscar_dias_e_horarios_para_servico.php',
                type: 'POST',
                dataType: 'json',
                data: { profissional_id: profissionalId, servico_id: servicoId, duracao: duracao },
                success: function(dataSlots) {
                    $('#dias-horarios-lista').empty();
                    if (Array.isArray(dataSlots) && dataSlots.length > 0) {
                        dataSlots.forEach(function(slot) {
                            if (slot.horarios.length > 0) {
                                let horariosHtml = slot.horarios.map(h => `<div class="time-slot" data-horario="${h}" data-data="${slot.data}">${h}</div>`).join('');
                                
                                const [y, m, d] = slot.data.split('-');
                                const dateObj = new Date(y, m - 1, d);
                                const dataFormatada = dateObj.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: '2-digit' });

                                $('#dias-horarios-lista').append(`<h6 class="mt-4"><i class="fas fa-calendar-day"></i> ${dataFormatada}</h6><div class="time-slots-grid">${horariosHtml}</div>`);
                            }
                        });
                    } else {
                        $('#dias-horarios-lista').html('<p class="text-center">Nenhum horário disponível.</p>');
                    }
                },
                error: function() {
                    $('#dias-horarios-lista').html('<p class="text-danger">Erro ao carregar horários.</p>');
                }
            });
        }
    }

    $(document).on('click', '.time-slot', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');

        const horario = $(this).data('horario');
        const data = $(this).data('data');

        const [y, m, d] = data.split('-');
        const dataFormatada = `${d}/${m}/${y}`;
        
        $('#horario_selecionado').val(horario);
        $('#data_selecionada').val(data);
        $('#resumo-data').text(dataFormatada);
        $('#resumo-horario').text(horario);
        $('#btnAgendar').fadeIn();
    });

    $('#formAgendamento').on('submit', function(e) {
        if (!$('#profissional').val() || !$('#servico_selecionado').val() || !$('#data_selecionada').val() || !$('#horario_selecionado').val()) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos antes de agendar.');
            return false;
        }
        $('#btnAgendar').prop('disabled', true).html('<span class="loading"></span> Agendando...');
    });
    <?php endif; ?>
    </script>
</body>
</html>


   
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->




   </div>
</div>
</div>
</div>


    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <!-- Chart js -->
    <script type="text/javascript" src="..\files\bower_components\chart.js\js\Chart.js"></script>
    <!-- amchart js -->
    <script src="..\files\assets\pages\widget\amchart\amcharts.js"></script>
    <script src="..\files\assets\pages\widget\amchart\serial.js"></script>
    <script src="..\files\assets\pages\widget\amchart\light.js"></script>
    <script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\SmoothScroll.js"></script>
    <!--   LEMBRAR DESSA PARTE  <script src="..\files\assets\js\pcoded.min.js"></script> -->

    <!-- custom js -->
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?=$google;?>');
</script>
</body>

</html>
<script type="text/javascript">
    // Redireciona para uma nova URL após 3 segundos
 /   setTimeout(function() {
        window.location.href = "http://localhost/codigos/template/adminty-dashboard-master/default/edita.php";
    }, 2000); // 3000 milissegundos = 3 segundos
</script>


<?php

include 'pcoded.php';
include'erro.php';

?>