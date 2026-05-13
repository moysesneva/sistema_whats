<?php
// ===================================
// CONFIGURAÇÕES INICIAIS E SEGURANÇA
// ===================================
session_start();
include 'funcoes.php';

// Verificação de login
if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit();
} 

// Configurações de erro (descomente em produção)
// error_reporting(0);
// ini_set("display_errors", 0);

$login = $_SESSION['login'];

// Includes necessários
include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

// Recebe parâmetros GET
$pagina_nome_recebe = isset($_GET['pagina_nome']) ? $_GET['pagina_nome'] : 0;

// ===================================
// BUSCA DADOS DO USUÁRIO
// ===================================
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
}

// Validações de segurança
if($total_busca_usuario != 1){
    VaiPara('login.php');
    exit();
}

if($autorizado != 2){
    VaiPara('desbloquar.php');
    exit();
}

// Include do menu
include 'menu.php';

// ===================================
// BUSCA HORÁRIOS EXISTENTES
// ===================================
$sql_horarios_existentes = "SELECT 
    hp.id as horario_id,
    hp.profissional_id,
    hp.dia_semana,
    hp.hora_entrada,
    hp.almoco_inicio,
    hp.almoco_fim,
    hp.hora_saida,
    hp.ativo,
    p.profissional_nome,
    p.profissional_cargo
    FROM horarios_profissional hp
    INNER JOIN profissional p ON hp.profissional_id = p.id
    WHERE p.login = '$login'
    ORDER BY p.profissional_nome, 
    FIELD(hp.dia_semana, 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo')";

$query_horarios_existentes = mysqli_query($conn, $sql_horarios_existentes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Favicon -->
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    
    <!-- CSS Files -->
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/feather/css/feather.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/css/jquery.mCustomScrollbar.css">
    
    <!-- CSS Customizado -->
    <style>
        /* ===================================
           ESTILOS CUSTOMIZADOS
           =================================== */
        
        /* Estilos para cards de dias da semana */
        .dia-semana-card {
            transition: all 0.3s ease;
            border: 2px solid #ddd;
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
        }

        .dia-semana-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 15px rgba(0,123,255,0.2);
            transform: translateY(-2px);
        }

        .dia-header {
            position: relative;
            transition: all 0.3s ease;
            padding: 18px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .dia-header:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        }

        /* Checkbox customizado */
        .dia-checkbox:checked ~ label .dia-status {
            color: #28a745;
            font-weight: bold;
        }

        .dia-checkbox:not(:checked) ~ label .dia-status {
            color: #6c757d;
        }

        .dia-checkbox:checked ~ label {
            color: #007bff;
            font-weight: bold;
        }

        /* Animações */
        .servico-item, .intervalo-item {
            animation: slideIn 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilização das tabs */
        .nav-tabs .nav-link {
            border-radius: 10px 10px 0 0;
            font-weight: 500;
            padding: 12px 20px;
            margin-right: 5px;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #007bff;
        }

        /* Cards principais */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #dee2e6;
        }

        /* Botões customizados */
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Inputs customizados */
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            transform: scale(1.02);
        }

        /* Modal */
        #modalServicos .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Alertas flutuantes */
        #alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Tabela melhorada */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        /* Badge customizado */
        .badge {
            font-size: 0.8em;
            padding: 6px 12px;
            border-radius: 20px;
        }

        /* Header principal */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 300;
            font-size: 2.5rem;
        }

        .page-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <!-- ===================================
         PRE-LOADER
         =================================== -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <?php for($i = 0; $i < 10; $i++): ?>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- ===================================
         CONTAINER PRINCIPAL
         =================================== -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <!-- ===================================
                 NAVBAR SUPERIOR
                 =================================== -->
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

            <!-- ===================================
                 CONTEÚDO PRINCIPAL
                 =================================== -->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    
                    <!-- ===================================
                         MENU LATERAL
                         =================================== -->
                    <nav class="pcoded-navbar">
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="pcoded-navigatio-lavel">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">
                                <?php 
                                if ($total_menu > 0) {
                                    while ($row_menu = mysqli_fetch_array($query_menu)) {
                                        $id = $row_menu['id'];
                                        $menu_nome = $row_menu['menu'];
                                        $menu_pagina_menu = $row_menu['menu_pagina'];
                                        $tipo_menu = $row_menu['tipo'];
                                        $icone_menu = $row_menu['icone_menu'];

                                        $active_class = ($id == $pagina_nome_recebe) ? 'active' : '';
                                        
                                        echo '<li class="pcoded-hasmenu '.$active_class.'">
                                                <a href="'.$menu_pagina_menu.'?pagina_nome='.$id.'">
                                                    <span class="pcoded-micon"><i class="'.$icone_menu.'"></i></span>
                                                    <span class="pcoded-mtext">'.$menu_nome.'</span>
                                                </a>
                                              </li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </nav>

                    <!-- ===================================
                         ÁREA DE CONTEÚDO
                         =================================== -->
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">

                                    <!-- ===================================
                                         CONTAINER DE HORÁRIOS
                                         =================================== -->
                                    <div class="container-fluid mt-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <!-- Header da Página -->
                                                <div class="page-header">
                                                    <h3>
                                                        <i class="fa fa-clock-o"></i>
                                                        Gestão de Horários Profissionais
                                                    </h3>
                                                    <p>Configure horários de trabalho e associe serviços aos seus profissionais</p>
                                                </div>
                                                
                                                <!-- ===================================
                                                     ABAS DE NAVEGAÇÃO
                                                     =================================== -->
                                                <ul class="nav nav-tabs mb-4" id="tipoCadastro" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="servico-tab" data-toggle="tab" href="#cadastro-servico" role="tab">
                                                            <i class="fa fa-briefcase"></i> Configurar Horários e Serviços
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="gerenciar-tab" data-toggle="tab" href="#gerenciar-horarios" role="tab">
                                                            <i class="fa fa-cogs"></i> Gerenciar Horários Cadastrados
                                                        </a>
                                                    </li>
                                                </ul>

                                                <!-- ===================================
                                                     CONTEÚDO DAS ABAS
                                                     =================================== -->
                                                <div class="tab-content" id="tipoCadastroContent">
                                                    
                                                    <!-- ===================================
                                                         TAB 1: CADASTRO POR SERVIÇO
                                                         =================================== -->
                                                    <div class="tab-pane fade show active" id="cadastro-servico" role="tabpanel">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title mb-0">
                                                                    <i class="fa fa-user-plus"></i> Configuração Completa do Profissional
                                                                </h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="alert alert-info">
                                                                    <i class="fa fa-info-circle"></i>
                                                                    <strong>Como usar:</strong> Configure os horários de trabalho completos e associe serviços ao profissional selecionado. 
                                                                    Você pode definir horários diferentes para cada dia da semana, incluir intervalos e escolher quais serviços o profissional oferece.
                                                                </div>
                                                                
                                                                <form action="cadastrar_horarios_servico.php" method="post" id="formPrincipal">
                                                                    
                                                                    <!-- ===================================
                                                                         SELEÇÃO DO PROFISSIONAL
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-primary text-white">
                                                                            <h6 class="mb-0">
                                                                                <i class="fa fa-user"></i> Selecionar Profissional
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="form-group">
                                                                                <label for="profissional_servico">
                                                                                    <i class="fa fa-users"></i> Profissional *
                                                                                </label>
                                                                                <select class="form-control form-control-lg" id="profissional_servico" name="profissional_servico" required>
                                                                                    <option value="">🔍 Selecione um profissional...</option>
                                                                                    <?php              
                                                                                    $sql_busca_profissional = "SELECT * FROM profissional WHERE login = '$login' ORDER BY profissional_nome";
                                                                                    $query_busca_profissional = mysqli_query($conn, $sql_busca_profissional);
                                                                                    while($rows_profissional = mysqli_fetch_array($query_busca_profissional)) {
                                                                                        echo '<option value="'.$rows_profissional['id'].'">👤 '.$rows_profissional['profissional_nome'].' - '.$rows_profissional['profissional_cargo'].'</option>';
                                                                                    }            
                                                                                    ?>                  
                                                                                </select>
                                                                                <small class="form-text text-muted">
                                                                                    Escolha o profissional para configurar horários e serviços
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         HORÁRIOS DE TRABALHO
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-success text-white">
                                                                            <h6 class="mb-0">
                                                                                <i class="fa fa-calendar"></i> Configurar Horários de Trabalho
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="alert alert-warning">
                                                                                <i class="fa fa-lightbulb-o"></i>
                                                                                <strong>Dica:</strong> Clique no checkbox do dia da semana para ativar e configurar os horários daquele dia.
                                                                            </div>
                                                                            
                                                                            <?php
                                                                            $dias_semana = [
                                                                                'segunda' => ['nome' => 'Segunda-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                                                                'terca' => ['nome' => 'Terça-feira', 'icon' => 'fa-briefcase', 'color' => 'info'],
                                                                                'quarta' => ['nome' => 'Quarta-feira', 'icon' => 'fa-briefcase', 'color' => 'success'],
                                                                                'quinta' => ['nome' => 'Quinta-feira', 'icon' => 'fa-briefcase', 'color' => 'warning'],
                                                                                'sexta' => ['nome' => 'Sexta-feira', 'icon' => 'fa-briefcase', 'color' => 'danger'],
                                                                                'sabado' => ['nome' => 'Sábado', 'icon' => 'fa-sun-o', 'color' => 'secondary'],
                                                                                'domingo' => ['nome' => 'Domingo', 'icon' => 'fa-sun-o', 'color' => 'dark']
                                                                            ];
                                                                            
                                                                            foreach($dias_semana as $dia_key => $dia_info):
                                                                            ?>
                                                                            <div class="dia-semana-card">
                                                                                <div class="dia-header" style="cursor: pointer;">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input dia-checkbox" 
                                                                                               id="ativo_<?=$dia_key?>" name="dias_ativos[]" value="<?=$dia_key?>">
                                                                                        <label class="custom-control-label" for="ativo_<?=$dia_key?>" style="cursor: pointer; width: 100%;">
                                                                                            <i class="fa <?=$dia_info['icon']?> text-<?=$dia_info['color']?>"></i> 
                                                                                            <strong><?=$dia_info['nome']?></strong>
                                                                                            <span class="float-right dia-status">
                                                                                                <i class="fa fa-toggle-off text-muted"></i> 
                                                                                                <small>Clique para ativar</small>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body" id="config_<?=$dia_key?>" style="display: none; background: linear-gradient(135deg, #f0f8ff, #e6f3ff);">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-sign-in text-success"></i> 
                                                                                                <strong>Horário de Entrada</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="entrada_<?=$dia_key?>" placeholder="08:00">
                                                                                            <small class="text-muted">Início do expediente</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-cutlery text-warning"></i> 
                                                                                                <strong>Almoço - Início</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="almoco_inicio_<?=$dia_key?>" placeholder="12:00">
                                                                                            <small class="text-muted">Opcional</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-cutlery text-warning"></i> 
                                                                                                <strong>Almoço - Fim</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="almoco_fim_<?=$dia_key?>" placeholder="13:00">
                                                                                            <small class="text-muted">Opcional</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-sign-out text-danger"></i> 
                                                                                                <strong>Horário de Saída</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="saida_<?=$dia_key?>" placeholder="18:00">
                                                                                            <small class="text-muted">Fim do expediente</small>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Intervalos Adicionais -->
                                                                                    <div class="mt-4">
                                                                                        <label>
                                                                                            <i class="fa fa-coffee text-info"></i> 
                                                                                            <strong>Intervalos Adicionais</strong>
                                                                                        </label>
                                                                                        <div id="intervalos_<?=$dia_key?>">
                                                                                            <button type="button" class="btn btn-sm btn-outline-info btn-custom" 
                                                                                                    onclick="adicionarIntervalo('<?=$dia_key?>')">
                                                                                                <i class="fa fa-plus"></i> Adicionar Intervalo
                                                                                            </button>
                                                                                        </div>
                                                                                        <small class="text-muted">Ex: pausas para café, intervalos personalizados</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         SERVIÇOS
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-info text-white">
                                                                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                                                                <span>
                                                                                    <i class="fa fa-wrench"></i> Associar Serviços ao Profissional
                                                                                </span>
                                                                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalServicos">
                                                                                    <i class="fa fa-cog"></i> Gerenciar Serviços
                                                                                </button>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="alert alert-info">
                                                                                <i class="fa fa-info-circle"></i>
                                                                                <strong>Importante:</strong> Selecione quais serviços este profissional pode executar. 
                                                                                Os valores e tempos são carregados automaticamente dos serviços cadastrados.
                                                                            </div>
                                                                            
                                                                            <div id="servicos-container">
                                                                                <div class="servico-item">
                                                                                    <div class="row">
                                                                                        <div class="col-md-4">
                                                                                            <label>
                                                                                                <i class="fa fa-tags"></i> 
                                                                                                <strong>Serviço</strong>
                                                                                            </label>
                                                                                            <select class="form-control servico-select" name="servico_id[]" required>
                                                                                                <option value="">🔍 Selecione um serviço...</option>
                                                                                                <?php
                                                                                                $sql_servicos = "SELECT * FROM servicos WHERE login = '$login' AND ativo = 1 ORDER BY nome";
                                                                                                $query_servicos = mysqli_query($conn, $sql_servicos);
                                                                                                while($servico = mysqli_fetch_array($query_servicos)) {
                                                                                                    echo '<option value="'.$servico['id'].'" 
                                                                                                          data-duracao="'.$servico['duracao_minutos'].'" 
                                                                                                          data-valor="'.$servico['valor'].'">🛠️ '.$servico['nome'].'</option>';
                                                                                                }
                                                                                                ?>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-clock-o"></i> 
                                                                                                <strong>Tempo (minutos)</strong>
                                                                                            </label>
                                                                                            <input type="number" class="form-control tempo-servico" name="tempo_servico[]" 
                                                                                                   min="15" step="15" readonly style="background-color: #f8f9fa;">
                                                                                            <small class="text-muted">Carregado automaticamente</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-money"></i> 
                                                                                                <strong>Valor R$</strong>
                                                                                            </label>
                                                                                            <input type="number" class="form-control valor-servico" name="valor_servico[]" 
                                                                                                   step="0.01" min="0" readonly style="background-color: #f8f9fa;">
                                                                                            <small class="text-muted">Carregado automaticamente</small>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <label>&nbsp;</label>
                                                                                            <button type="button" class="btn btn-danger btn-block btn-custom" onclick="removerServico(this)">
                                                                                                <i class="fa fa-trash"></i> Remover
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="text-center mt-3">
                                                                                <button type="button" class="btn btn-outline-info btn-custom" onclick="adicionarServico()">
                                                                                    <i class="fa fa-plus"></i> Adicionar Mais Serviços
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         BOTÃO SALVAR
                                                                         =================================== -->
                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-success btn-lg btn-custom">
                                                                            <i class="fa fa-save"></i> Salvar Configurações Completas
                                                                        </button>
                                                                        <br>
                                                                        <small class="text-muted mt-2">
                                                                            Certifique-se de preencher todos os campos obrigatórios antes de salvar
                                                                        </small>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ===================================
                                                         TAB 2: GERENCIAR HORÁRIOS
                                                         =================================== -->
                                                    <div class="tab-pane fade" id="gerenciar-horarios" role="tabpanel">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <h5 class="card-title mb-0">
                                                                        <i class="fa fa-calendar-check-o"></i> Horários Cadastrados
                                                                    </h5>
                                                                    <div class="card-tools d-flex align-items-center">
                                                                        <select class="form-control mr-2" id="filtro-profissional" onchange="filtrarPorProfissional()" 
                                                                                style="width: 250px;">
                                                                            <option value="">👥 Todos os Profissionais</option>
                                                                            <?php
                                                                            $sql_profs = "SELECT DISTINCT id, profissional_nome FROM profissional WHERE login = '$login' ORDER BY profissional_nome";
                                                                            $query_profs = mysqli_query($conn, $sql_profs);
                                                                            while($prof = mysqli_fetch_array($query_profs)) {
                                                                                echo '<option value="'.$prof['id'].'">👤 '.$prof['profissional_nome'].'</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <button class="btn btn-info btn-sm btn-custom" onclick="recarregarHorarios()">
                                                                            <i class="fa fa-refresh"></i> Atualizar
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if(mysqli_num_rows($query_horarios_existentes) > 0): ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped table-hover" id="tabelaHorarios">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th><i class="fa fa-user"></i> Profissional</th>
                                                                                <th><i class="fa fa-calendar"></i> Dia da Semana</th>
                                                                                <th><i class="fa fa-clock-o"></i> Entrada</th>
                                                                                <th><i class="fa fa-cutlery"></i> Almoço</th>
                                                                                <th><i class="fa fa-clock-o"></i> Saída</th>
                                                                                <th><i class="fa fa-toggle-on"></i> Status</th>
                                                                                <th><i class="fa fa-cog"></i> Ações</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $dias_map = [
                                                                                'segunda' => 'Segunda-feira',
                                                                                'terca' => 'Terça-feira', 
                                                                                'quarta' => 'Quarta-feira',
                                                                                'quinta' => 'Quinta-feira',
                                                                                'sexta' => 'Sexta-feira',
                                                                                'sabado' => 'Sábado',
                                                                                'domingo' => 'Domingo'
                                                                            ];
                                                                            
                                                                            mysqli_data_seek($query_horarios_existentes, 0);
                                                                            while($horario = mysqli_fetch_array($query_horarios_existentes)):
                                                                                $almoco_texto = '';
                                                                                if($horario['almoco_inicio'] && $horario['almoco_fim']) {
                                                                                    $almoco_texto = '<span class="text-success"><i class="fa fa-check"></i> '.$horario['almoco_inicio'] . ' - ' . $horario['almoco_fim'].'</span>';
                                                                                } else {
                                                                                    $almoco_texto = '<span class="text-muted"><i class="fa fa-times"></i> Sem intervalo</span>';
                                                                                }
                                                                                
                                                                                $status_badge = $horario['ativo'] == 1 ? 
                                                                                    '<span class="badge badge-success"><i class="fa fa-check"></i> Ativo</span>' : 
                                                                                    '<span class="badge badge-danger"><i class="fa fa-times"></i> Inativo</span>';
                                                                            ?>
                                                                            <tr data-profissional-id="<?=$horario['profissional_id']?>" data-horario-id="<?=$horario['horario_id']?>">
                                                                                <td>
                                                                                    <div>
                                                                                        <strong><i class="fa fa-user-circle"></i> <?=$horario['profissional_nome']?></strong>
                                                                                        <br><small class="text-muted"><i class="fa fa-briefcase"></i> <?=$horario['profissional_cargo']?></small>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge badge-info badge-lg">
                                                                                        <i class="fa fa-calendar-o"></i> <?=$dias_map[$horario['dia_semana']]?>
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="text-success">
                                                                                        <i class="fa fa-sign-in"></i> <?=$horario['hora_entrada']?>
                                                                                    </span>
                                                                                </td>
                                                                                <td><?=$almoco_texto?></td>
                                                                                <td>
                                                                                    <span class="text-danger">
                                                                                        <i class="fa fa-sign-out"></i> <?=$horario['hora_saida']?>
                                                                                    </span>
                                                                                </td>
                                                                                <td><?=$status_badge?></td>
                                                                                <td>
                                                                                    <div class="btn-group" role="group">
                                                                                        <button class="btn btn-warning btn-sm" onclick="editarHorario(<?=$horario['horario_id']?>)" title="Editar">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-info btn-sm" onclick="verDetalhesHorario(<?=$horario['horario_id']?>)" title="Ver Detalhes">
                                                                                            <i class="fa fa-eye"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-<?=$horario['ativo'] == 1 ? 'secondary' : 'success'?> btn-sm" 
                                                                                                onclick="toggleStatusHorario(<?=$horario['horario_id']?>, <?=$horario['ativo']?>)" 
                                                                                                title="<?=$horario['ativo'] == 1 ? 'Desativar' : 'Ativar'?>">
                                                                                            <i class="fa fa-power-off"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-danger btn-sm" 
                                                                                                onclick="excluirHorario(<?=$horario['horario_id']?>, '<?=$horario['profissional_nome']?>', '<?=$dias_map[$horario['dia_semana']]?>')" 
                                                                                                title="Excluir">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endwhile; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <?php else: ?>
                                                                <div class="alert alert-info text-center">
                                                                    <i class="fa fa-info-circle fa-3x mb-3 text-info"></i>
                                                                    <h4>Nenhum horário cadastrado ainda</h4>
                                                                    <p>Utilize a aba "Configurar Horários e Serviços" para cadastrar horários para seus profissionais.</p>
                                                                    <button class="btn btn-primary btn-custom" onclick="$('#servico-tab').tab('show')">
                                                                        <i class="fa fa-plus"></i> Cadastrar Primeiro Horário
                                                                    </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================
         MODAIS
         =================================== -->
    
    <!-- Modal para Gerenciar Serviços -->
    <div class="modal fade" id="modalServicos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-wrench"></i> Gerenciar Serviços Disponíveis
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulário para adicionar novo serviço -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fa fa-plus"></i> Adicionar Novo Serviço
                        </div>
                        <div class="card-body">
                            <form id="formNovoServico">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Nome do Serviço *</label>
                                        <input type="text" class="form-control" id="novo_servico_nome" placeholder="Ex: Corte de Cabelo" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Duração (min) *</label>
                                        <input type="number" class="form-control" id="novo_servico_duracao" placeholder="60" min="15" step="15" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Valor R$ *</label>
                                        <input type="number" class="form-control" id="novo_servico_valor" placeholder="50.00" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-success btn-block btn-custom" onclick="salvarNovoServico()">
                                            <i class="fa fa-save"></i> Salvar
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Descrição (opcional)</label>
                                        <textarea class="form-control" id="novo_servico_descricao" placeholder="Descrição detalhada do serviço..." rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de serviços existentes -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-list"></i> Serviços Cadastrados
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><i class="fa fa-tag"></i> Serviço</th>
                                            <th><i class="fa fa-clock-o"></i> Duração</th>
                                            <th><i class="fa fa-money"></i> Valor</th>
                                            <th><i class="fa fa-toggle-on"></i> Status</th>
                                            <th><i class="fa fa-cog"></i> Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaServicos">
                                        <?php
                                        $sql_lista_servicos = "SELECT * FROM servicos WHERE login = '$login' ORDER BY nome";
                                        $query_lista_servicos = mysqli_query($conn, $sql_lista_servicos);
                                        while($servico = mysqli_fetch_array($query_lista_servicos)) {
                                            $status_badge = $servico['ativo'] == 1 ? 
                                                '<span class="badge badge-success"><i class="fa fa-check"></i> Ativo</span>' : 
                                                '<span class="badge badge-danger"><i class="fa fa-times"></i> Inativo</span>';
                                            echo '<tr data-servico-id="'.$servico['id'].'">
                                                    <td><strong>'.$servico['nome'].'</strong></td>
                                                    <td><span class="badge badge-info">'.$servico['duracao_minutos'].' min</span></td>
                                                    <td><span class="text-success"><strong>R$ '.number_format($servico['valor'], 2, ',', '.').'</strong></span></td>
                                                    <td>'.$status_badge.'</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-warning" onclick="editarServico('.$servico['id'].')" title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-'.($servico['ativo'] == 1 ? 'secondary' : 'success').'" onclick="toggleServico('.$servico['id'].')" title="'.($servico['ativo'] == 1 ? 'Desativar' : 'Ativar').'">
                                                                <i class="fa fa-power-off"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                  </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Horário -->
    <div class="modal fade" id="modalEditarHorario" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-edit"></i> Editar Horário de Trabalho
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditarHorario" action="editar_horario.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_horario_id" name="horario_id">
                        
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong id="edit_profissional_info"></strong>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-sign-in text-success"></i> <strong>Entrada</strong></label>
                                    <input type="time" class="form-control" id="edit_entrada" name="entrada" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-cutlery text-warning"></i> <strong>Almoço - Início</strong></label>
                                    <input type="time" class="form-control" id="edit_almoco_inicio" name="almoco_inicio">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-cutlery text-warning"></i> <strong>Almoço - Fim</strong></label>
                                    <input type="time" class="form-control" id="edit_almoco_fim" name="almoco_fim">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-sign-out text-danger"></i> <strong>Saída</strong></label>
                                    <input type="time" class="form-control" id="edit_saida" name="saida" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="edit_ativo" name="ativo" value="1">
                                        <label class="custom-control-label" for="edit_ativo">
                                            <i class="fa fa-toggle-on"></i> <strong>Horário Ativo</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seção de Intervalos -->
                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-coffee"></i> Intervalos Adicionais</span>
                                    <button type="button" class="btn btn-light btn-sm" onclick="adicionarIntervaloEdicao()">
                                        <i class="fa fa-plus"></i> Adicionar
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="intervalos-edicao">
                                    <!-- Intervalos serão carregados aqui via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fa fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalhes -->
    <div class="modal fade" id="modalDetalhesHorario" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-eye"></i> Detalhes Completos do Horário
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detalhesHorarioConteudo">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Container para Alertas -->
    <div id="alert-container"></div>

    <!-- ===================================
         SCRIPTS
         =================================== -->
    
    <!-- jQuery -->
    <script type="text/javascript" src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- Slimscroll -->
    <script type="text/javascript" src="../files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    
    <!-- Modernizr -->
    <script type="text/javascript" src="../files/bower_components/modernizr/js/modernizr.js"></script>
    
    <!-- Chart.js -->
    <script type="text/javascript" src="../files/bower_components/chart.js/js/Chart.js"></script>
    
    <!-- Custom Scripts -->
    <script src="../files/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="../files/assets/js/SmoothScroll.js"></script>
    <script src="../files/assets/js/vartical-layout.min.js"></script>
    <script type="text/javascript" src="../files/assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="../files/assets/js/script.min.js"></script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?=$google;?>');
    </script>

    <!-- JavaScript Customizado -->
    <script>
    // ===================================
    // INICIALIZAÇÃO E EVENTOS
    // ===================================
    
    // Inicialização quando o documento estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Gerenciar checkboxes dos dias da semana
        const checkboxes = document.querySelectorAll('input[name="dias_ativos[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const dia = this.value;
                const configDiv = document.getElementById('config_' + dia);
                const statusSpan = this.parentElement.querySelector('.dia-status');
                
                if (this.checked) {
                    configDiv.style.display = 'block';
                    statusSpan.innerHTML = '<i class="fa fa-toggle-on text-success"></i> <small class="text-success">Ativo - configure os horários</small>';
                    this.closest('.dia-semana-card').style.borderColor = '#28a745';
                    this.closest('.dia-semana-card').style.boxShadow = '0 4px 15px rgba(40,167,69,0.3)';
                } else {
                    configDiv.style.display = 'none';
                    statusSpan.innerHTML = '<i class="fa fa-toggle-off text-muted"></i> <small>Clique para ativar</small>';
                    this.closest('.dia-semana-card').style.borderColor = '#ddd';
                    this.closest('.dia-semana-card').style.boxShadow = 'none';
                }
            });
        });

        // Auto-preencher valores do serviço quando selecionado
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('servico-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const servicoItem = e.target.closest('.servico-item');
                
                if (selectedOption.value) {
                    const duracao = selectedOption.getAttribute('data-duracao');
                    const valor = selectedOption.getAttribute('data-valor');
                    
                    servicoItem.querySelector('.tempo-servico').value = duracao || '';
                    servicoItem.querySelector('.valor-servico').value = valor || '';
                }
            }
        });
    });

    // ===================================
    // FUNÇÕES DO CADASTRO POR SERVIÇO
    // ===================================

    // Adicionar intervalo
    function adicionarIntervalo(dia) {
        const container = document.getElementById('intervalos_' + dia);
        const intervaloDiv = document.createElement('div');
        intervaloDiv.className = 'row mt-3 intervalo-item';
        intervaloDiv.style.background = 'linear-gradient(135deg, #fff3cd, #ffeaa7)';
        intervaloDiv.style.padding = '15px';
        intervaloDiv.style.borderRadius = '8px';
        intervaloDiv.style.border = '1px solid #ffc107';
        intervaloDiv.innerHTML = `
            <div class="col-md-4">
                <label><i class="fa fa-clock-o text-info"></i> <strong>Início do Intervalo</strong></label>
                <input type="time" class="form-control" name="intervalo_inicio_${dia}[]" placeholder="15:00">
            </div>
            <div class="col-md-4">
                <label><i class="fa fa-clock-o text-info"></i> <strong>Fim do Intervalo</strong></label>
                <input type="time" class="form-control" name="intervalo_fim_${dia}[]" placeholder="15:30">
            </div>
            <div class="col-md-3">
                <label><i class="fa fa-comment text-secondary"></i> <strong>Motivo (opcional)</strong></label>
                <input type="text" class="form-control" name="intervalo_motivo_${dia}[]" placeholder="Ex: Café, Lanche...">
            </div>
            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm btn-block btn-custom" onclick="this.closest('.intervalo-item').remove()">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(intervaloDiv);
        
        // Efeito visual de adição
        intervaloDiv.style.opacity = '0';
        intervaloDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            intervaloDiv.style.transition = 'all 0.3s ease';
            intervaloDiv.style.opacity = '1';
            intervaloDiv.style.transform = 'translateY(0)';
        }, 10);
    }

    // Adicionar serviço
    function adicionarServico() {
        const container = document.getElementById('servicos-container');
        const servicoClone = document.querySelector('.servico-item').cloneNode(true);
        
        // Limpar valores
        servicoClone.querySelectorAll('input').forEach(input => input.value = '');
        servicoClone.querySelector('select').selectedIndex = 0;
        
        container.appendChild(servicoClone);
        
        // Efeito visual de adição
        servicoClone.style.opacity = '0';
        servicoClone.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            servicoClone.style.transition = 'all 0.3s ease';
            servicoClone.style.opacity = '1';
            servicoClone.style.transform = 'translateY(0)';
        }, 10);
    }

    // Remover serviço
    function removerServico(btn) {
        const servicosCount = document.querySelectorAll('.servico-item').length;
        if (servicosCount > 1) {
            const item = btn.closest('.servico-item');
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                item.remove();
            }, 300);
        } else {
            alert('⚠️ Deve haver pelo menos um serviço associado ao profissional.');
        }
    }

    // ===================================
    // FUNÇÕES DO GERENCIAMENTO DE HORÁRIOS
    // ===================================
    
    // Filtrar horários por profissional
    function filtrarPorProfissional() {
        const profissionalId = document.getElementById('filtro-profissional').value;
        const linhas = document.querySelectorAll('#tabelaHorarios tbody tr');
        
        linhas.forEach(linha => {
            if (profissionalId === '' || linha.getAttribute('data-profissional-id') === profissionalId) {
                linha.style.display = '';
            } else {
                linha.style.display = 'none';
            }
        });
    }

    // Recarregar página
    function recarregarHorarios() {
        window.location.reload();
    }

    // Editar horário
    function editarHorario(horarioId) {
        fetch('buscar_horario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'horario_id=' + horarioId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                preencherModalEdicao(data.data);
                $('#modalEditarHorario').modal('show');
            } else {
                alert('❌ Erro ao carregar dados: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('❌ Erro ao carregar horário para edição');
        });
    }

    // Preencher modal de edição
    function preencherModalEdicao(dados) {
        document.getElementById('edit_horario_id').value = dados.horario.id;
        document.getElementById('edit_profissional_info').innerHTML = 
            '👤 <strong>' + dados.profissional.nome + '</strong> - 📅 ' + dados.horario.dia_semana_nome;
        
        document.getElementById('edit_entrada').value = dados.horario.hora_entrada;
        document.getElementById('edit_almoco_inicio').value = dados.horario.almoco_inicio || '';
        document.getElementById('edit_almoco_fim').value = dados.horario.almoco_fim || '';
        document.getElementById('edit_saida').value = dados.horario.hora_saida;
        document.getElementById('edit_ativo').checked = dados.horario.ativo == 1;
        
        // Carregar intervalos
        const intervalosContainer = document.getElementById('intervalos-edicao');
        intervalosContainer.innerHTML = '';
        
        if (dados.intervalos && dados.intervalos.length > 0) {
            dados.intervalos.forEach(intervalo => {
                adicionarIntervaloEdicao(intervalo);
            });
        }
    }

    // Adicionar intervalo na edição
    function adicionarIntervaloEdicao(dadosIntervalo = null) {
        const container = document.getElementById('intervalos-edicao');
        const intervaloDiv = document.createElement('div');
        intervaloDiv.className = 'row mb-3 intervalo-item';
        intervaloDiv.style.background = 'linear-gradient(135deg, #e3f2fd, #bbdefb)';
        intervaloDiv.style.padding = '15px';
        intervaloDiv.style.borderRadius = '8px';
        intervaloDiv.style.border = '1px solid #2196f3';
        
        const intervaloId = dadosIntervalo ? dadosIntervalo.id : '';
        const inicio = dadosIntervalo ? dadosIntervalo.intervalo_inicio : '';
        const fim = dadosIntervalo ? dadosIntervalo.intervalo_fim : '';
        const motivo = dadosIntervalo ? dadosIntervalo.motivo : '';
        
        intervaloDiv.innerHTML = `
            <input type="hidden" name="intervalo_ids[]" value="${intervaloId}">
            <div class="col-md-3">
                <label><i class="fa fa-clock-o text-info"></i> <strong>Início</strong></label>
                <input type="time" class="form-control" name="intervalos_inicio[]" value="${inicio}" placeholder="Início">
            </div>
            <div class="col-md-3">
                <label><i class="fa fa-clock-o text-info"></i> <strong>Fim</strong></label>
                <input type="time" class="form-control" name="intervalos_fim[]" value="${fim}" placeholder="Fim">
            </div>
            <div class="col-md-4">
                <label><i class="fa fa-comment text-secondary"></i> <strong>Motivo</strong></label>
                <input type="text" class="form-control" name="intervalos_motivo[]" value="${motivo}" placeholder="Motivo (opcional)">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm btn-block btn-custom" onclick="this.closest('.intervalo-item').remove()">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(intervaloDiv);
    }

    // Ver detalhes do horário
    function verDetalhesHorario(horarioId) {
        fetch('buscar_horario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'horario_id=' + horarioId + '&detalhes=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                exibirDetalhesHorario(data.data);
                $('#modalDetalhesHorario').modal('show');
            } else {
                alert('❌ Erro ao carregar detalhes: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('❌ Erro ao carregar detalhes');
        });
    }

    // Exibir detalhes do horário
    function exibirDetalhesHorario(dados) {
        const container = document.getElementById('detalhesHorarioConteudo');
        
        let intervalosHtml = '';
        if (dados.intervalos && dados.intervalos.length > 0) {
            intervalosHtml = '<div class="card mt-3"><div class="card-header bg-warning text-white"><h6 class="mb-0"><i class="fa fa-coffee"></i> Intervalos Adicionais</h6></div><div class="card-body"><ul class="list-group list-group-flush">';
            dados.intervalos.forEach(intervalo => {
                intervalosHtml += `<li class="list-group-item"><i class="fa fa-clock-o text-info"></i> <strong>${intervalo.intervalo_inicio} - ${intervalo.intervalo_fim}</strong>`;
                if (intervalo.motivo) {
                    intervalosHtml += ` <span class="badge badge-secondary">${intervalo.motivo}</span>`;
                }
                intervalosHtml += '</li>';
            });
            intervalosHtml += '</ul></div></div>';
        }
        
        let servicosHtml = '';
        if (dados.servicos && dados.servicos.length > 0) {
            servicosHtml = '<div class="card mt-3"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fa fa-wrench"></i> Serviços Associados</h6></div><div class="card-body"><ul class="list-group list-group-flush">';
            dados.servicos.forEach(servico => {
                servicosHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><i class="fa fa-tag text-primary"></i> <strong>${servico.nome}</strong></div>
                    <div>
                        <span class="badge badge-info">${servico.tempo_execucao_minutos}min</span>
                        <span class="badge badge-success">R$ ${parseFloat(servico.valor_profissional).toFixed(2).replace('.', ',')}</span>
                    </div>
                </li>`;
            });
            servicosHtml += '</ul></div></div>';
        }
        
        const statusBadge = dados.horario.ativo == 1 ? 
            '<span class="badge badge-success badge-lg"><i class="fa fa-check"></i> Ativo</span>' : 
            '<span class="badge badge-danger badge-lg"><i class="fa fa-times"></i> Inativo</span>';
        
        container.innerHTML = `
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📋 Informações Gerais</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fa fa-user-circle text-primary"></i> Profissional:</h6>
                            <p class="mb-2"><strong>${dados.profissional.nome}</strong></p>
                            <small class="text-muted"><i class="fa fa-briefcase"></i> ${dados.profissional.cargo}</small>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-calendar text-info"></i> Dia da Semana:</h6>
                            <p><span class="badge badge-info badge-lg">${dados.horario.dia_semana_nome}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">⏰ Horários de Trabalho</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h6><i class="fa fa-sign-in text-success"></i> Entrada</h6>
                            <span class="badge badge-success badge-lg">${dados.horario.hora_entrada}</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6><i class="fa fa-sign-out text-danger"></i> Saída</h6>
                            <span class="badge badge-danger badge-lg">${dados.horario.hora_saida}</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6><i class="fa fa-cutlery text-warning"></i> Almoço</h6>
                            <p>${dados.horario.almoco_inicio && dados.horario.almoco_fim ? 
                                '<span class="badge badge-warning badge-lg">' + dados.horario.almoco_inicio + ' - ' + dados.horario.almoco_fim + '</span>' : 
                                '<span class="text-muted">Sem intervalo</span>'}</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6><i class="fa fa-toggle-on"></i> Status</h6>
                            <p>${statusBadge}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            ${intervalosHtml}
            ${servicosHtml}
        `;
    }

    // Alternar status do horário
    function toggleStatusHorario(horarioId, statusAtual) {
        const novoStatus = statusAtual == 1 ? 0 : 1;
        const acao = novoStatus == 1 ? 'ativar' : 'desativar';
        
        if (confirm(`🔄 Deseja ${acao} este horário?`)) {
            fetch('alterar_status_horario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `horario_id=${horarioId}&novo_status=${novoStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('success', '✅ ' + data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarAlerta('danger', '❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarAlerta('danger', '❌ Erro ao alterar status');
            });
        }
    }

    // Excluir horário
    function excluirHorario(horarioId, profissionalNome, diaSemana) {
        if (confirm(`🗑️ Tem certeza que deseja EXCLUIR o horário de ${profissionalNome} para ${diaSemana}?\n\n⚠️ Esta ação não pode ser desfeita!`)) {
            fetch('excluir_horario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `horario_id=${horarioId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('success', '✅ ' + data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarAlerta('danger', '❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarAlerta('danger', '❌ Erro ao excluir horário');
            });
        }
    }

    // Validação do formulário de edição
    document.getElementById('formEditarHorario').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const entrada = document.getElementById('edit_entrada').value;
        const saida = document.getElementById('edit_saida').value;
        const almocoInicio = document.getElementById('edit_almoco_inicio').value;
        const almocoFim = document.getElementById('edit_almoco_fim').value;
        
        // Validações
        if (!entrada || !saida) {
            alert('⚠️ Horários de entrada e saída são obrigatórios!');
            return;
        }
        
        if (entrada >= saida) {
            alert('⚠️ Horário de entrada deve ser menor que o de saída!');
            return;
        }
        
        if ((almocoInicio && !almocoFim) || (!almocoInicio && almocoFim)) {
            alert('⚠️ Se definir horário de almoço, deve preencher início E fim!');
            return;
        }
        
        if (almocoInicio && almocoFim && almocoInicio >= almocoFim) {
            alert('⚠️ Horário de início do almoço deve ser menor que o fim!');
            return;
        }
        
        // Se passou nas validações, submeter
        this.submit();
    });

    // ===================================
    // FUNÇÕES AJAX PARA SERVIÇOS
    // ===================================
    
    // Salvar novo serviço
    function salvarNovoServico() {
        const nome = document.getElementById('novo_servico_nome').value;
        const duracao = document.getElementById('novo_servico_duracao').value;
        const valor = document.getElementById('novo_servico_valor').value;
        const descricao = document.getElementById('novo_servico_descricao').value;
        
        if (!nome || !duracao || !valor) {
            alert('⚠️ Por favor, preencha todos os campos obrigatórios.');
            return;
        }
        
        // Mostrar loading
        const btnSalvar = event.target;
        const textoOriginal = btnSalvar.innerHTML;
        btnSalvar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Salvando...';
        btnSalvar.disabled = true;
        
        // Fazer requisição AJAX
        const formData = new FormData();
        formData.append('acao', 'salvar');
        formData.append('nome', nome);
        formData.append('duracao', duracao);
        formData.append('valor', valor);
        formData.append('descricao', descricao);
        
        fetch('salvar_servico_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpar formulário
                document.getElementById('formNovoServico').reset();
                
                // Adicionar novo serviço à lista
                adicionarServicoNaLista(data.data);
                
                // Adicionar aos selects de serviços
                atualizarSelectsServicos(data.data);
                
                // Mostrar mensagem de sucesso
                mostrarAlerta('success', '✅ ' + data.message);
            } else {
                mostrarAlerta('danger', '❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('danger', '❌ Erro ao salvar serviço. Tente novamente.');
        })
        .finally(() => {
            // Restaurar botão
            btnSalvar.innerHTML = textoOriginal;
            btnSalvar.disabled = false;
        });
    }

    // Editar serviço
    function editarServico(id) {
        const formData = new FormData();
        formData.append('acao', 'buscar');
        formData.append('id', id);
        
        fetch('salvar_servico_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarModalEdicao(data.data);
            } else {
                mostrarAlerta('danger', '❌ Erro ao buscar dados do serviço');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('danger', '❌ Erro ao buscar serviço');
        });
    }

    // Alternar status do serviço
    function toggleServico(id) {
        if (!confirm('🔄 Deseja alterar o status deste serviço?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('acao', 'toggle');
        formData.append('id', id);
        
        fetch('salvar_servico_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar visual do status
                const linha = document.querySelector(`tr[data-servico-id="${id}"]`);
                const badgeStatus = linha.querySelector('.badge');
                
                if (data.data.novo_status == 1) {
                    badgeStatus.className = 'badge badge-success';
                    badgeStatus.innerHTML = '<i class="fa fa-check"></i> Ativo';
                } else {
                    badgeStatus.className = 'badge badge-danger';
                    badgeStatus.innerHTML = '<i class="fa fa-times"></i> Inativo';
                }
                
                mostrarAlerta('success', '✅ ' + data.message);
            } else {
                mostrarAlerta('danger', '❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('danger', '❌ Erro ao alterar status');
        });
    }

    // ===================================
    // FUNÇÕES AUXILIARES
    // ===================================
    
    // Adicionar serviço na lista visual
    function adicionarServicoNaLista(servico) {
        const tbody = document.getElementById('listaServicos');
        const novaLinha = document.createElement('tr');
        novaLinha.setAttribute('data-servico-id', servico.id);
        
        novaLinha.innerHTML = `
            <td><strong>${servico.nome}</strong></td>
            <td><span class="badge badge-info">${servico.duracao} min</span></td>
            <td><span class="text-success"><strong>R$ ${parseFloat(servico.valor).toFixed(2).replace('.', ',')}</strong></span></td>
            <td><span class="badge badge-success"><i class="fa fa-check"></i> Ativo</span></td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-warning" onclick="editarServico(${servico.id})" title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="toggleServico(${servico.id})" title="Desativar">
                        <i class="fa fa-power-off"></i>
                    </button>
                </div>
            </td>
        `;
        
        // Efeito visual de adição
        novaLinha.style.opacity = '0';
        novaLinha.style.transform = 'translateY(-20px)';
        tbody.appendChild(novaLinha);
        
        setTimeout(() => {
            novaLinha.style.transition = 'all 0.3s ease';
            novaLinha.style.opacity = '1';
            novaLinha.style.transform = 'translateY(0)';
        }, 10);
    }

    // Atualizar os selects de serviços
    function atualizarSelectsServicos(servico) {
        const selects = document.querySelectorAll('.servico-select');
        
        selects.forEach(select => {
            const option = document.createElement('option');
            option.value = servico.id;
            option.setAttribute('data-duracao', servico.duracao);
            option.setAttribute('data-valor', servico.valor);
            option.textContent = '🛠️ ' + servico.nome;
            select.appendChild(option);
        });
    }

    // Mostrar alertas
    function mostrarAlerta(tipo, mensagem) {
        const alertContainer = document.getElementById('alert-container');
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${mensagem}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        // Auto remover após 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Mostrar modal de edição
    function mostrarModalEdicao(servico) {
        // Criar modal de edição dinamicamente
        const modalHtml = `
            <div class="modal fade" id="modalEditarServico" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title"><i class="fa fa-edit"></i> Editar Serviço</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarServico">
                                <input type="hidden" id="editar_servico_id" value="${servico.id}">
                                <div class="form-group">
                                    <label><i class="fa fa-tag"></i> <strong>Nome do Serviço</strong></label>
                                    <input type="text" class="form-control" id="editar_servico_nome" value="${servico.nome}" required>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-clock-o"></i> <strong>Duração (minutos)</strong></label>
                                    <input type="number" class="form-control" id="editar_servico_duracao" value="${servico.duracao_minutos}" min="15" step="15" required>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-money"></i> <strong>Valor R$</strong></label>
                                    <input type="number" class="form-control" id="editar_servico_valor" value="${servico.valor}" step="0.01" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-comment"></i> <strong>Descrição</strong></label>
                                    <textarea class="form-control" id="editar_servico_descricao" rows="3">${servico.descricao || ''}</textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-primary btn-custom" onclick="salvarEdicaoServico()">
                                <i class="fa fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior se existir
        const modalExistente = document.getElementById('modalEditarServico');
        if (modalExistente) {
            modalExistente.remove();
        }
        
        // Adicionar novo modal ao body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Mostrar modal
        $('#modalEditarServico').modal('show');
    }

    // Salvar edição do serviço
    function salvarEdicaoServico() {
        const id = document.getElementById('editar_servico_id').value;
        const nome = document.getElementById('editar_servico_nome').value;
        const duracao = document.getElementById('editar_servico_duracao').value;
        const valor = document.getElementById('editar_servico_valor').value;
        const descricao = document.getElementById('editar_servico_descricao').value;
        
        const formData = new FormData();
        formData.append('acao', 'editar');
        formData.append('id', id);
        formData.append('nome', nome);
        formData.append('duracao', duracao);
        formData.append('valor', valor);
        formData.append('descricao', descricao);
        
        fetch('salvar_servico_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar linha na tabela
                const linha = document.querySelector(`tr[data-servico-id="${id}"]`);
                linha.cells[0].innerHTML = '<strong>' + nome + '</strong>';
                linha.cells[1].innerHTML = '<span class="badge badge-info">' + duracao + ' min</span>';
                linha.cells[2].innerHTML = '<span class="text-success"><strong>R$ ' + parseFloat(valor).toFixed(2).replace('.', ',') + '</strong></span>';
                
                // Fechar modal
                $('#modalEditarServico').modal('hide');
                
                // Mostrar mensagem
                mostrarAlerta('success', '✅ ' + data.message);
                
                // Recarregar página para atualizar selects
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarAlerta('danger', '❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('danger', '❌ Erro ao salvar alterações');
        });
    }

    // ===================================
    // VALIDAÇÕES DO FORMULÁRIO PRINCIPAL
    // ===================================
    
    // Validação do formulário principal
    document.getElementById('formPrincipal').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const profissional = document.getElementById('profissional_servico').value;
        const diasAtivos = document.querySelectorAll('input[name="dias_ativos[]"]:checked');
        const servicos = document.querySelectorAll('.servico-select');
        
        // Validações básicas
        if (!profissional) {
            alert('⚠️ Por favor, selecione um profissional!');
            document.getElementById('profissional_servico').focus();
            return;
        }
        
        if (diasAtivos.length === 0) {
            alert('⚠️ Por favor, selecione pelo menos um dia da semana!');
            return;
        }
        
        // Validar se pelo menos um serviço foi selecionado
        let servicoSelecionado = false;
        servicos.forEach(select => {
            if (select.value) {
                servicoSelecionado = true;
            }
        });
        
        if (!servicoSelecionado) {
            alert('⚠️ Por favor, selecione pelo menos um serviço!');
            return;
        }
        
        // Validar horários dos dias ativos
        let horariosValidos = true;
        let erroMsg = '';
        
        diasAtivos.forEach(checkbox => {
            const dia = checkbox.value;
            const entrada = document.querySelector(`input[name="entrada_${dia}"]`).value;
            const saida = document.querySelector(`input[name="saida_${dia}"]`).value;
            const almocoInicio = document.querySelector(`input[name="almoco_inicio_${dia}"]`).value;
            const almocoFim = document.querySelector(`input[name="almoco_fim_${dia}"]`).value;
            
            if (!entrada || !saida) {
                horariosValidos = false;
                erroMsg = `⚠️ Por favor, preencha os horários de entrada e saída para ${checkbox.closest('.dia-semana-card').querySelector('strong').textContent}!`;
                return;
            }
            
            if (entrada >= saida) {
                horariosValidos = false;
                erroMsg = `⚠️ Horário de entrada deve ser menor que o de saída para ${checkbox.closest('.dia-semana-card').querySelector('strong').textContent}!`;
                return;
            }
            
            if ((almocoInicio && !almocoFim) || (!almocoInicio && almocoFim)) {
                horariosValidos = false;
                erroMsg = `⚠️ Para ${checkbox.closest('.dia-semana-card').querySelector('strong').textContent}, se definir horário de almoço, deve preencher início E fim!`;
                return;
            }
            
            if (almocoInicio && almocoFim && almocoInicio >= almocoFim) {
                horariosValidos = false;
                erroMsg = `⚠️ Para ${checkbox.closest('.dia-semana-card').querySelector('strong').textContent}, horário de início do almoço deve ser menor que o fim!`;
                return;
            }
        });
        
        if (!horariosValidos) {
            alert(erroMsg);
            return;
        }
        
        // Se chegou até aqui, pode submeter
        const submitBtn = this.querySelector('button[type="submit"]');
        const textoOriginal = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Salvando Configurações...';
        submitBtn.disabled = true;
        
        // Submeter formulário
        this.submit();
    });

    // ===================================
    // EFEITOS VISUAIS E MELHORIAS UX
    // ===================================
    
    // Efeito hover nos cards de dias da semana
    document.querySelectorAll('.dia-semana-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.querySelector('.dia-checkbox').checked) {
                this.style.borderColor = '#007bff';
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.querySelector('.dia-checkbox').checked) {
                this.style.borderColor = '#ddd';
                this.style.transform = 'translateY(0)';
            }
        });
    });

    // Efeito nos inputs quando focados
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#007bff';
            this.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,0.25)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '#e9ecef';
            this.style.boxShadow = 'none';
        });
    });

    // Contador de dias ativos
    function atualizarContadorDias() {
        const diasAtivos = document.querySelectorAll('input[name="dias_ativos[]"]:checked').length;
        const contador = document.getElementById('contador-dias');
        if (contador) {
            contador.textContent = diasAtivos;
        }
    }

    // Adicionar contador visual (opcional)
    document.addEventListener('change', function(e) {
        if (e.target.name === 'dias_ativos[]') {
            atualizarContadorDias();
        }
    });

    // ===================================
    // FUNÇÕES DE INICIALIZAÇÃO
    // ===================================
    
    // Aplicar máscara de dinheiro nos campos de valor
    document.querySelectorAll('.valor-servico').forEach(input => {
        input.addEventListener('input', function() {
            let valor = this.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2);
            this.value = valor;
        });
    });

    // Auto-save em localStorage para recuperação (opcional)
    function salvarRascunho() {
        const dadosFormulario = {
            profissional: document.getElementById('profissional_servico').value,
            diasAtivos: Array.from(document.querySelectorAll('input[name="dias_ativos[]"]:checked')).map(cb => cb.value),
            timestamp: new Date().getTime()
        };
        
        // Não usar localStorage conforme instruções, mas manter a estrutura para possível implementação futura
        // localStorage.setItem('rascunho_horarios', JSON.stringify(dadosFormulario));
    }

    // Salvar rascunho a cada mudança significativa
    document.addEventListener('change', function(e) {
        if (e.target.form && e.target.form.id === 'formPrincipal') {
            // salvarRascunho(); // Desabilitado conforme instruções
        }
    });

    // ===================================
    // MENSAGENS DE FEEDBACK
    // ===================================
    
    // Mostrar dicas contextuais
    function mostrarDica(elemento, mensagem) {
        const dica = document.createElement('div');
        dica.className = 'alert alert-info alert-dismissible fade show mt-2';
        dica.innerHTML = `
            <i class="fa fa-lightbulb-o"></i> <strong>Dica:</strong> ${mensagem}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        elemento.parentNode.insertBefore(dica, elemento.nextSibling);
        
        setTimeout(() => {
            dica.remove();
        }, 10000);
    }

    // ===================================
    // TRATAMENTO DE ERROS GLOBAIS
    // ===================================
    
    // Capturar erros JavaScript
 

    // Capturar erros de promessa não tratados
    window.addEventListener('unhandledrejection', function(e) {
        console.error('Promessa rejeitada:', e.reason);
        mostrarAlerta('warning', '⚠️ Erro de conexão. Verifique sua internet e tente novamente.');
    });

    // ===================================
    // ATALHOS DE TECLADO
    // ===================================
    
    document.addEventListener('keydown', function(e) {
        // Ctrl + S = Salvar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab.id === 'cadastro-servico') {
                document.getElementById('formPrincipal').dispatchEvent(new Event('submit'));
            }
        }
        
        // Esc = Fechar modais
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });

    // ===================================
    // FINALIZAÇÃO
    // ===================================
    
    console.log('🎉 Sistema de Horários Profissionais carregado com sucesso!');
    console.log('📋 Funcionalidades disponíveis:');
    console.log('   ✅ Configuração completa de horários por profissional');
    console.log('   ✅ Associação de serviços');
    console.log('   ✅ Gerenciamento de intervalos');
    console.log('   ✅ Gerenciamento de horários cadastrados');
    console.log('   ✅ CRUD completo de serviços');
    console.log('   ✅ Validações em tempo real');
    console.log('   ✅ Interface responsiva e intuitiva');
    </script>

    <?php
    // Includes finais
    include 'pcoded.php';
    include 'erro.php';
    ?>
</body>
</html>