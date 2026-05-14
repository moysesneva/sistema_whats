<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 

$login = $_SESSION['login'];
include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;    
}

// Busca informações do usuário
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
    $usuario_api = $rows_usuarios['usuario_api'];
}

include 'menu.php';

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
    VaiPara('desbloquar.php');
}



$sql_busca_prof = "SELECT * FROM  profissional WHERE telefone = '$login'";
$sql_busca_profs = mysqli_query($conn, $sql_busca_prof);
$total_busca_profs = mysqli_num_rows($sql_busca_profs);

while($rows_usuarios = mysqli_fetch_array($sql_busca_profs)) {
    $id_profissional  = $rows_usuarios['id'];

}







// Filtros
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d', strtotime('+60 days'));
$filtro_profissional = isset($_GET['profissional']) ? $_GET['profissional'] : '';
$filtro_confirmacao = isset($_GET['confirmacao']) ? $_GET['confirmacao'] : '';

// Query base
$sql_agendamentos = "SELECT * FROM agendamento WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim' AND id_profissional = '$id_profissional'";

if ($filtro_profissional) {
    $sql_agendamentos .= " AND id_profissional = '$filtro_profissional'";
}
if ($filtro_confirmacao !== '') {
    $sql_agendamentos .= " AND confirmacao = '$filtro_confirmacao'";
}

$sql_agendamentos .= " ORDER BY data DESC, horario ASC";
$query_agendamentos = mysqli_query($conn, $sql_agendamentos);




?>




<!DOCTYPE html>
<html lang="pt-br">
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
    <!-- Adiciona DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
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
                                    if ($id == $pagina_nome_recebe) {     
                                        echo '<li class="pcoded-hasmenu active">';
                                    } else {
                                        echo '<li class="pcoded-hasmenu">';
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
                   
                   
                   

    
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --card-hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            margin-bottom: 0;
        }

        .page-header h5 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .page-header span {
            opacity: 0.9;
            font-size: 1rem;
        }

        .filter-section {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .form-group label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-filter {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-hover-shadow);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card.total::before {
            background: var(--primary-gradient);
        }

        .stat-card.confirmed::before {
            background: var(--success-gradient);
        }

        .stat-card.pending::before {
            background: var(--warning-gradient);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.total {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            color: #667eea;
        }

        .stat-icon.confirmed {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
            color: #4facfe;
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
            color: #fa709a;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #718096;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            border: none;
            font-weight: 600;
            color: #4a5568;
            padding: 1rem;
            font-size: 0.9rem;
        }

        .table tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-success {
            background: var(--success-gradient);
            color: white;
        }

        .badge-warning {
            background: var(--warning-gradient);
            color: white;
        }

        .badge-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #667eea;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-section {
                padding: 1.5rem;
            }
            
            .table-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>


                    
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- CONTEÚDO PRINCIPAL MODERNIZADO -->
                                    <div class="page-body" style="padding: 2rem;">
                                        <div class="container-fluid">
                                            
                                            <!-- Header da Página -->
                                            <div class="modern-card">
                                                <div class="page-header">
                                                    <h5><i class="feather icon-calendar"></i> Relatório de Agendamentos</h5>
                                                    <span>Visualize e exporte os dados de agendamentos com interface moderna e intuitiva</span>
                                                </div>
                                            </div>

                                            <!-- Seção de Filtros -->
                                            <div class="filter-section">
                                                <h6 class="section-title">
                                                    <i class="feather icon-filter"></i>
                                                    Filtros de Pesquisa
                                                </h6>
                                                <form method="GET">
                                                    <input type="hidden" name="pagina_nome" value="<?= $pagina_nome_recebe ?>">
                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Início</label>
                                                                <input type="date" name="data_inicio" class="form-control" value="<?= $filtro_data_inicio ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Fim</label>
                                                                <input type="date" name="data_fim" class="form-control" value="<?= $filtro_data_fim ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Status de Confirmação</label>
                                                                <select name="confirmacao" class="form-control">
                                                                    <option value="">Todos os Status</option>
                                                                    <option value="1" <?= $filtro_confirmacao === '1' ? 'selected' : '' ?>>Confirmado</option>
                                                                    <option value="0" <?= $filtro_confirmacao === '0' ? 'selected' : '' ?>>Não Confirmado</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <button type="submit" class="btn btn-filter btn-block">
                                                                    <i class="feather icon-search"></i> Aplicar Filtros
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Dashboard de Estatísticas -->
                                            <div class="stats-grid">
                                                <?php
                                                // Estatísticas (mantendo a lógica original)
                                                $sql_total = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND id_profissional = '$id_profissional'
                                                ";
                                                $query_total = mysqli_query($conn, $sql_total);
                                                $total_agendamentos = mysqli_fetch_assoc($query_total)['total'];

                                                $sql_confirmados = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND confirmacao = 1
                                                      AND id_profissional = '$id_profissional'
                                                ";
                                                $query_confirmados = mysqli_query($conn, $sql_confirmados);
                                                $total_confirmados = mysqli_fetch_assoc($query_confirmados)['total'];

                                                $sql_pendentes = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND confirmacao = 0
                                                      AND id_profissional = '$id_profissional'
                                                ";
                                                $query_pendentes = mysqli_query($conn, $sql_pendentes);
                                                $total_pendentes = mysqli_fetch_assoc($query_pendentes)['total'];
                                                ?>

                                                <div class="stat-card total">
                                                    <div class="stat-icon total">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_agendamentos) ?></div>
                                                    <div class="stat-label">Total de Agendamentos</div>
                                                </div>

                                                <div class="stat-card confirmed">
                                                    <div class="stat-icon confirmed">
                                                        <i class="feather icon-check-circle"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_confirmados) ?></div>
                                                    <div class="stat-label">Agendamentos Confirmados</div>
                                                </div>

                                                <div class="stat-card pending">
                                                    <div class="stat-icon pending">
                                                        <i class="feather icon-clock"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_pendentes) ?></div>
                                                    <div class="stat-label">Agendamentos Pendentes</div>
                                                </div>
                                            </div>

                                            <!-- Tabela de Resultados -->
                                            <div class="table-container">
                                                <h6 class="section-title">
                                                    <i class="feather icon-list"></i>
                                                    Detalhes dos Agendamentos
                                                </h6>
                                                <div class="table-responsive">
                                                    <table id="relatorio-table" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th><i class="feather icon-calendar"></i> Data</th>
                                                                <th><i class="feather icon-clock"></i> Horário</th>
                                                                <th><i class="feather icon-user"></i> Cliente</th>
                                                                <th><i class="feather icon-phone"></i> Telefone</th>
                                                                <th><i class="feather icon-briefcase"></i> Profissional</th>
                                                                <th><i class="feather icon-award"></i> Cargo</th>
                                                                <th><i class="feather icon-info"></i> Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            // Reiniciar a query para exibir os resultados (mantendo a lógica original)
                                                            $query_agendamentos = mysqli_query($conn, $sql_agendamentos);
                                                            
                                                            while($row = mysqli_fetch_array($query_agendamentos)) { 
                                                            ?>
                                                                <tr>
                                                                    <td><strong><?= date('d/m/Y', strtotime($row['data'])) ?></strong></td>
                                                                    <td><?= $row['horario'] ?></td>
                                                                    <td><?= $row['cliente_nome'] ?></td>
                                                                    <td><?= $row['cliente_telefone'] ?></td>
                                                                    <td><?= $row['profissional_nome'] ?></td>
                                                                    <td><?= $row['profissional_cargo'] ?></td>
                                                                    <td>
                                                                        <?php if ($row['confirmacao'] == 1) { ?>
                                                                            <span class="badge badge-success">
                                                                                <i class="feather icon-check"></i> Confirmado
                                                                            </span>
                                                                        <?php } elseif ($row['confirmacao'] == 2) { ?>
                                                                            <span class="badge badge-danger">
                                                                                <i class="feather icon-x"></i> Cancelado
                                                                            </span>
                                                                        <?php } else { ?>
                                                                            <span class="badge badge-warning">
                                                                                <i class="feather icon-clock"></i> Pendente
                                                                            </span>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
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

    <!-- Scripts originais mantidos -->
    <script src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    

                   
                   
                   
                   
                   

    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
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

    <!-- custom js -->
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>

    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#relatorio-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="feather icon-copy"></i> Copiar',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="feather icon-file-text"></i> CSV',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="feather icon-file"></i> Excel',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="feather icon-file-text"></i> PDF',
                        className: 'btn btn-secondary'
                    },
                    
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                }
            });
            
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });

        function visualizarDetalhes(id) {
            // Modal para visualizar detalhes
            window.location.href = 'visualizar_agendamento.php?id=' + id;
        }

        function confirmarAgendamento(id) {
            if (confirm('Confirmar este agendamento?')) {
                // Implementar confirmação via Ajax
                $.ajax({
                    url: 'confirmar_agendamento.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        alert('Agendamento confirmado com sucesso!');
                        location.reload();
                    },
                    error: function() {
                        alert('Erro ao confirmar agendamento.');
                    }
                });
            }
        }

        function excluirAgendamento(id) {
            if (confirm('Tem certeza que deseja excluir este agendamento?')) {
                // Implementar exclusão via Ajax
                $.ajax({
                    url: 'excluir_agendamento.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        alert('Agendamento excluído com sucesso!');
                        location.reload();
                    },
                    error: function() {
                        alert('Erro ao excluir agendamento.');
                    }
                });
            }
        }
    </script>
    
    <?php
    include 'pcoded.php';
    include 'erro.php';
    ?>
</body>
</html>