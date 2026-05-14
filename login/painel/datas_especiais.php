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


if (isset($_POST['deletar'])) {
            $id = $_POST['id'];
            $sql_delete = "DELETE FROM datas_excluidas WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 's', $id);
            mysqli_stmt_execute($stmt);
        }
        // Fechar a conexão

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
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">


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





<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datas Especiais</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --card-hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
            --border-radius: 20px;
            --transition: all 0.3s ease;
        }

     

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 3rem 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="90" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .page-header h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .modern-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .card-header-modern {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header-modern i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .card-body-modern {
            padding: 2rem;
        }

        .form-label-modern {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label-modern i {
            color: #667eea;
            width: 16px;
        }

        .form-control-modern {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
            background: #fafafa;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-success-modern {
            background: var(--success-gradient);
            color: white;
        }

        .btn-success-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
            color: white;
        }

        .btn-danger-modern {
            background: var(--danger-gradient);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .btn-danger-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
            color: white;
        }

        .table-modern {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table-modern thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-modern tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-modern tbody tr {
            transition: var(--transition);
        }

        .table-modern tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }

        .filter-form {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .alert-modern {
            border: none;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-info-modern {
            background: var(--info-gradient);
            color: #2d3748;
            border-left: 4px solid #4facfe;
        }

        .resultado-busca {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow-y: auto;
        }

        .form-group-modern {
            position: relative;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 1rem;
            }

            .page-header h3 {
                font-size: 2rem;
            }

            .card-body-modern {
                padding: 1.5rem;
            }

            .btn-modern {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: var(--transition);
            z-index: 1000;
        }

        .floating-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php
    // Função para conectar ao banco de dados
    function conectarDB() {
       include 'conn.php';
        return $conn;
    }
    ?>

    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fas fa-calendar-star"></i> Datas Especiais</h3>
            <p class="page-subtitle">Gerencie datas especiais e indisponibilidades dos profissionais</p>
        </div>

        <!-- Div para exibir os resultados da pesquisa -->
        <div id="resultadoBusca" class="resultado-busca"></div>

        <!-- Card de Cadastro de Data Especial -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h5><i class="fas fa-plus-circle"></i> Cadastrar Nova Data Especial</h5>
            </div>
            <div class="card-body-modern">
                <!-- Formulário para Agendamento -->
                <form action="datas_especiais_confirma.php" method="post">
                    <div class="row">
                        <!-- Seleção do Profissional -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="profissional">
                                    <i class="fas fa-user-md"></i> Selecione o Profissional
                                </label>
                                <select class="form-control form-control-modern" id="profissional" name="profissional" required onchange="carregarDiasSemana()">
                                    <option value="">Escolha um profissional</option>
                                    <?php
                                    // Conexão com o banco de dados
                                    $conn = conectarDB();

                                    // Consulta para obter os profissionais
                                    $sql = "SELECT * FROM profissional WHERE login = '$login'";
                                    $result = mysqli_query($conn, $sql);

                                    // Preencher o campo options com os profissionais
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['id'] . '">' . $row['profissional_nome'] . ' - ' . $row['profissional_cargo'] . '</option>';
                                    }

                                    // Fechar a conexão
                                    mysqli_close($conn);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Inserção de Data -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="data">
                                    <i class="fas fa-calendar-alt"></i> Data Especial
                                </label>
                                <input type="date" class="form-control form-control-modern" id="data" name="data" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo de Descrição -->
                        <div class="col-12">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="texto">
                                    <i class="fas fa-edit"></i> Motivo (Opcional)
                                </label>
                                <input type="text" class="form-control form-control-modern" id="texto" name="texto" placeholder="Digite a descrição da data especial">
                            </div>
                        </div>
                    </div>

                    <!-- Botão para Agendar -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-modern btn-success-modern">
                            <i class="fas fa-save"></i> Cadastrar Data Especial
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card de Filtro e Listagem -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h5><i class="fas fa-list"></i> Datas Especiais Cadastradas</h5>
            </div>
            <div class="card-body-modern">
                <!-- Formulário para filtrar por Data -->
                <div class="filter-form">
                    <form action="" method="get" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label-modern" for="data_filtro">
                                <i class="fas fa-search"></i> Buscar por Data:
                            </label>
                            <input type="date" class="form-control form-control-modern" id="data_filtro" name="data_filtro" 
                                   value="<?= isset($_GET['data_filtro']) ? $_GET['data_filtro'] : '' ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-modern btn-primary-modern me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="?" class="btn btn-modern btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Resultados -->
                <div class="table-responsive">
                    <?php
                    // Conexão com o banco de dados
                    $conn = conectarDB();

                    // Verificar se o login foi definido
                    $sql_busca_profissional = "SELECT * FROM profissional WHERE login = '$login'";
                    $query_busca_profissional = mysqli_query($conn, $sql_busca_profissional);

                    // Criar o array para armazenar os IDs
                    $ID_ARRAY = [];

                    while ($rows_profissional = mysqli_fetch_array($query_busca_profissional)) {
                        // Adicionar cada ID ao array
                        $ID_ARRAY[] = $rows_profissional['id'];
                    }

                    // Verificar se o array não está vazio
                    if (!empty($ID_ARRAY)) {
                        // Transformar o array em uma string separada por vírgulas
                        $ids_para_busca = implode(',', $ID_ARRAY);

                        // Base da consulta SQL
                        $sql = "SELECT * FROM datas_excluidas WHERE id_profissional IN ($ids_para_busca)";
                        
                        // Verificar se há um filtro de data
                        if (isset($_GET['data_filtro']) && !empty($_GET['data_filtro'])) {
                            $data_filtro = $_GET['data_filtro'];
                            $sql .= " AND data_excluida = ?";
                        }

                        // Preparar a consulta SQL
                        $stmt = mysqli_prepare($conn, $sql);

                        // Associar parâmetros se houver um filtro de data
                        if (isset($data_filtro)) {
                            mysqli_stmt_bind_param($stmt, 's', $data_filtro);
                        }

                        // Executar a consulta
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        // Verificar se há resultados
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-modern">';
                            echo '<table class="table table-hover mb-0">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th><i class="fas fa-calendar-day"></i> Data Excluída</th>';
                            echo '<th><i class="fas fa-user-md"></i> Profissional</th>';
                            echo '<th><i class="fas fa-comment"></i> Motivo</th>';
                            echo '<th><i class="fas fa-cogs"></i> Ação</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                // Converter data_excluida para o formato brasileiro
                                $data_excluida = !empty($row['data_excluida']) ? date('d/m/Y', strtotime($row['data_excluida'])) : '';

                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($data_excluida) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['profissional']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['motivo']) . '</td>';
                                echo '<td>';
                                echo '<form method="post" action="" style="display:inline;">';
                                echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                                echo '<button type="submit" name="deletar" class="btn btn-modern btn-danger-modern" onclick="return confirm(\'Tem certeza que deseja excluir esta data especial?\')">';
                                echo '<i class="fas fa-trash"></i> Excluir';
                                echo '</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-modern alert-info-modern">';
                            echo '<i class="fas fa-info-circle"></i> ';
                            echo '<strong>Nenhuma data especial encontrada.</strong><br>';
                            echo 'Não há datas especiais cadastradas' . (isset($_GET['data_filtro']) && !empty($_GET['data_filtro']) ? ' para a data selecionada' : '') . '.';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-modern alert-info-modern">';
                        echo '<i class="fas fa-user-times"></i> ';
                        echo '<strong>Nenhum profissional encontrado.</strong><br>';
                        echo 'É necessário ter profissionais cadastrados para gerenciar datas especiais.';
                        echo '</div>';
                    }

                    // Fechar conexão com o banco de dados
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action" onclick="document.getElementById('profissional').focus()" title="Adicionar Data Especial">
        <i class="fas fa-plus"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <script>
        // Função para buscar clientes em tempo real
        function buscarCliente() {
            var nome = document.getElementById('nome').value;
            var telefone = document.getElementById('telefone').value;

            // Criação do objeto XMLHttpRequest para fazer a requisição AJAX
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Atualiza o conteúdo da div com os resultados da busca
                    document.getElementById('resultadoBusca').innerHTML = xhr.responseText;
                }
            };

            // Envia a requisição para o script PHP com os valores de nome e telefone
            xhr.open("GET", "buscar_cliente.php?nome=" + nome + "&telefone=" + telefone, true);
            xhr.send();
        }

        // Função para preencher os campos de nome e telefone ao clicar em um resultado da busca
        function preencherCampos(nome, telefone) {
            document.getElementById('nome').value = nome;
            document.getElementById('telefone').value = telefone;

            // Limpa os resultados da busca depois de selecionar um cliente
            document.getElementById('resultadoBusca').innerHTML = '';
        }

        // Scripts AJAX para carregar agendamentos disponíveis dinamicamente
        // Carregar os dias da semana disponíveis ao selecionar um profissional
        function carregarDiasSemana() {
            var profissionalId = $('#profissional').val();
            if (profissionalId !== '') {
                $.ajax({
                    url: 'buscar_dias_semana.php',
                    type: 'POST',
                    data: { profissional_id: profissionalId },
                    success: function(response) {
                        $('#dia_semana').html(response);
                        $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
                    }
                });
            } else {
                $('#dia_semana').html('<option value="">Escolha um dia da semana</option>');
                $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
            }
        }

        // Carregar as datas e horários disponíveis ao selecionar um dia da semana
        function carregarAgendamentosDisponiveis() {
            var profissionalId = $('#profissional').val();
            var diaSemana = $('#dia_semana').val();
            if (profissionalId !== '' && diaSemana !== '') {
                $.ajax({
                    url: 'buscar_agendamentos_disponiveis.php',
                    type: 'POST',
                    data: { profissional_id: profissionalId, dia_semana: diaSemana },
                    success: function(response) {
                        $('#agendamento').html(response);
                    }
                });
            } else {
                $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
            }
        }

        // Smooth scrolling para melhor UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animação suave para cards
        const cards = document.querySelectorAll('.modern-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
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
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script><script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
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



<?php

include 'pcoded.php';
include'erro.php';

?>