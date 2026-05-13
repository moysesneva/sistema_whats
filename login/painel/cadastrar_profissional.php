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


  
<!-- APAGUE ESTAS LINHAS -->
<script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


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
    
 <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 15px;
        }

        .form-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin: 1rem 0;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .section-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 10px solid rgba(255, 255, 255, 0.1);
        }

        .section-header h4 {
            margin: 0;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .form-group-enhanced {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label-enhanced {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.95rem;
        }

        .input-group-enhanced {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .input-group-enhanced:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .input-group-text-enhanced {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 500;
        }

        .form-control-enhanced {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control-enhanced:focus {
            box-shadow: none;
            background: white;
            transform: scale(1.02);
        }

        .form-select-enhanced {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .form-select-enhanced:focus {
            box-shadow: none;
            background: white;
            border-color: transparent;
        }

        .country-select {
            min-width: 140px;
            max-width: 140px;
            border-right: 2px solid rgba(255, 255, 255, 0.3);
        }

        .country-option {
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-enhanced {
            background: var(--success-gradient);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: var(--secondary-gradient);
        }

        .btn-add-specialty {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 0 10px 10px 0;
            color: white;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-add-specialty:hover {
            background: var(--primary-gradient);
            transform: scale(1.1);
        }

        .table-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
            overflow: hidden;
        }

        .table-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
        }

        .table-enhanced {
            margin: 0;
        }

        .table-enhanced th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table-enhanced td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-enhanced tbody tr {
            transition: all 0.3s ease;
        }

        .table-enhanced tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
            transform: scale(1.01);
        }

        .badge-specialty {
            background: var(--primary-gradient);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 0.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .badge-specialty:hover {
            background: var(--secondary-gradient);
            transform: scale(1.1);
        }

        .btn-action {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0.2rem;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: scale(1.2);
        }

        .btn-danger-action {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            color: white;
        }

        .btn-primary-action {
            background: var(--success-gradient);
            border: none;
            color: white;
        }

        .flag-icon {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            border-radius: 2px;
        }

        .modal-content-enhanced {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--hover-shadow);
        }

        .modal-header-enhanced {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .form-floating-enhanced {
            position: relative;
        }

        .form-floating-enhanced .form-control-enhanced {
            padding: 1rem 0.75rem 0.25rem 0.75rem;
        }

        .form-floating-enhanced label {
            position: absolute;
            top: 0;
            left: 0.75rem;
            height: 100%;
            padding: 1rem 0;
            pointer-events: none;
            border: none;
            transform-origin: 0 0;
            transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .form-section {
                margin: 0.5rem 0;
                padding: 1rem;
            }
            
            .section-header {
                margin: -1rem -1rem 1rem -1rem;
                padding: 1rem;
            }
            
            .country-select {
                min-width: 120px;
                max-width: 120px;
            }
        }

        .animate-in {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- Formulário para Cadastrar Profissional -->
<div class="form-section animate-in">
    <div class="section-header">
        <h4>
            <i class="fas fa-user-plus me-3"></i>Cadastrar Novo Profissional
        </h4>
    </div>
    
    <form action="cadastrar_profissional_confirma.php" method="post" onsubmit="return validarCadastroProfissional()">
        
        <!-- Campo: Nome do Profissional -->
        <div class="form-group-enhanced">
            <label for="nomeProfissional" class="form-label-enhanced">
                <i class="fas fa-user text-primary me-2"></i>Nome do Profissional
            </label>
            <div class="input-group-enhanced">
                <span class="input-group-text-enhanced">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" class="form-control-enhanced" id="nomeProfissional" name="nomeProfissional" placeholder="Digite o nome completo do profissional" required>
            </div>
        </div>

        <!-- Campo: Telefone com Código do País -->
        <div class="form-group-enhanced">
            <label for="telefoneProfissional" class="form-label-enhanced">
                <i class="fas fa-phone text-primary me-2"></i>Telefone de Contato
            </label>
            <div class="input-group-enhanced">
                <span class="input-group-text-enhanced">
                    <i class="fas fa-phone"></i>
                </span>
                <select class="form-select-enhanced country-select" id="codigoPais" name="codigoPais">
                    <option value="+55" selected>🇧🇷 +55 (Brasil)</option>
                    <option value="+351">🇵🇹 +351 (Portugal)</option>
                    <option value="+1">🇺🇸 +1 (EUA)</option>
                    <option value="+1">🇨🇦 +1 (Canadá)</option>
                    <option value="+44">🇬🇧 +44 (Reino Unido)</option>
                    <option value="+34">🇪🇸 +34 (Espanha)</option>
                    <option value="+33">🇫🇷 +33 (França)</option>
                    <option value="+39">🇮🇹 +39 (Itália)</option>
                    <option value="+49">🇩🇪 +49 (Alemanha)</option>
                    <option value="+31">🇳🇱 +31 (Holanda)</option>
                    <option value="+32">🇧🇪 +32 (Bélgica)</option>
                    <option value="+41">🇨🇭 +41 (Suíça)</option>
                    <option value="+43">🇦🇹 +43 (Áustria)</option>
                    <option value="+45">🇩🇰 +45 (Dinamarca)</option>
                    <option value="+46">🇸🇪 +46 (Suécia)</option>
                    <option value="+47">🇳🇴 +47 (Noruega)</option>
                    <option value="+358">🇫🇮 +358 (Finlândia)</option>
                    <option value="+54">🇦🇷 +54 (Argentina)</option>
                    <option value="+56">🇨🇱 +56 (Chile)</option>
                    <option value="+57">🇨🇴 +57 (Colômbia)</option>
                    <option value="+58">🇻🇪 +58 (Venezuela)</option>
                    <option value="+51">🇵🇪 +51 (Peru)</option>
                    <option value="+593">🇪🇨 +593 (Equador)</option>
                    <option value="+598">🇺🇾 +598 (Uruguai)</option>
                    <option value="+591">🇧🇴 +591 (Bolívia)</option>
                    <option value="+595">🇵🇾 +595 (Paraguai)</option>
                    <option value="+52">🇲🇽 +52 (México)</option>
                    <option value="+81">🇯🇵 +81 (Japão)</option>
                    <option value="+82">🇰🇷 +82 (Coreia do Sul)</option>
                    <option value="+86">🇨🇳 +86 (China)</option>
                    <option value="+91">🇮🇳 +91 (Índia)</option>
                    <option value="+61">🇦🇺 +61 (Austrália)</option>
                    <option value="+64">🇳🇿 +64 (Nova Zelândia)</option>
                    <option value="+27">🇿🇦 +27 (África do Sul)</option>
                    <option value="+20">🇪🇬 +20 (Egito)</option>
                    <option value="+212">🇲🇦 +212 (Marrocos)</option>
                    <option value="+234">🇳🇬 +234 (Nigéria)</option>
                    <option value="+254">🇰🇪 +254 (Quênia)</option>
                    <option value="+7">🇷🇺 +7 (Rússia)</option>
                    <option value="+380">🇺🇦 +380 (Ucrânia)</option>
                    <option value="+48">🇵🇱 +48 (Polônia)</option>
                    <option value="+420">🇨🇿 +420 (Rep. Tcheca)</option>
                    <option value="+36">🇭🇺 +36 (Hungria)</option>
                    <option value="+40">🇷🇴 +40 (Romênia)</option>
                    <option value="+359">🇧🇬 +359 (Bulgária)</option>
                    <option value="+385">🇭🇷 +385 (Croácia)</option>
                    <option value="+381">🇷🇸 +381 (Sérvia)</option>
                    <option value="+30">🇬🇷 +30 (Grécia)</option>
                    <option value="+90">🇹🇷 +90 (Turquia)</option>
                    <option value="+972">🇮🇱 +972 (Israel)</option>
                    <option value="+971">🇦🇪 +971 (EAU)</option>
                    <option value="+966">🇸🇦 +966 (Arábia Saudita)</option>
                    <option value="+65">🇸🇬 +65 (Singapura)</option>
                    <option value="+60">🇲🇾 +60 (Malásia)</option>
                    <option value="+66">🇹🇭 +66 (Tailândia)</option>
                    <option value="+84">🇻🇳 +84 (Vietnã)</option>
                    <option value="+63">🇵🇭 +63 (Filipinas)</option>
                    <option value="+62">🇮🇩 +62 (Indonésia)</option>
                </select>
                <input type="text" class="form-control-enhanced" id="telefoneProfissional" name="telefoneProfissional" placeholder="(00) 00000-0000" required>
            </div>
        </div>

        <!-- Campo: Especialidade do Profissional -->
        <div class="form-group-enhanced">
            <label for="especialidadeProfissional" class="form-label-enhanced">
                <i class="fas fa-star text-primary me-2"></i>Especialidade Profissional
            </label>
            <div class="input-group-enhanced">
                <span class="input-group-text-enhanced">
                    <i class="fas fa-star"></i>
                </span>
                <select class="form-select-enhanced" id="especialidadeProfissional" name="especialidadeProfissional" required>
                    <option value="" disabled selected>Selecione a especialidade do profissional</option>
                    <?php
                    // A lógica PHP permanece a mesma
                    include 'conn.php';
                  $sql_esp = "SELECT * FROM especialidades WHERE login = '$login' ORDER BY especialidades ASC";

                    $query_esp = mysqli_query($conn, $sql_esp);
                    while($row_esp = mysqli_fetch_array($query_esp)) {
                        $especialidade = htmlspecialchars($row_esp['especialidades']);
                        echo '<option value="'.$especialidade.'">'.$especialidade.'</option>';
                    }
                    ?>
                </select>
                <button type="button" class="btn-add-specialty" onclick="abrirModalEspecialidade()" title="Adicionar nova especialidade">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Botão de Envio -->
        <div class="text-center mt-4">
            <button type="submit" class="btn-enhanced">
                <i class="fas fa-check me-2"></i>Cadastrar Profissional
            </button>
        </div>
    </form>
</div>

<!-- Lista de Profissionais -->
<div class="table-section animate-in">
    <div class="table-header">
        <h4 class="mb-0">
            <i class="fas fa-users me-3"></i>Profissionais Cadastrados
        </h4>
    </div>
    <div class="table-responsive">
        <table class="table table-enhanced">
            <thead>
                <tr>
                    <th scope="col"><i class="fas fa-user me-2"></i>Nome</th>
                    <th scope="col"><i class="fas fa-phone me-2"></i>Telefone</th>
                    <th scope="col"><i class="fas fa-star me-2"></i>Especialidades</th>
                    <th scope="col" class="text-center"><i class="fas fa-cogs me-2"></i>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // A lógica PHP permanece a mesma
                include 'conn.php';
                $sql_busca_profissional = "SELECT * FROM profissional WHERE login = '$login'";
                $query_busca_profissional = mysqli_query($conn, $sql_busca_profissional);
                
                if(mysqli_num_rows($query_busca_profissional) > 0){
                    while($rows_profissional = mysqli_fetch_assoc($query_busca_profissional)) {
                        $profissional_id = $rows_profissional['id'];
                        $profissional_nome = htmlspecialchars($rows_profissional['profissional_nome']);
                        $profissional_cargo = htmlspecialchars($rows_profissional['profissional_cargo']);
                        $telefone = htmlspecialchars($rows_profissional['telefone']);
                ?>
                <tr>
                    <td>
                        <strong><?= $profissional_nome ?></strong>
                    </td>
                    <td>
                        <i class="fas fa-phone text-muted me-2"></i><?= $telefone ?>
                    </td>
                    <td>
                        <?php
                        if(!empty($profissional_cargo)) {
                            $especialidades = explode(',', $profissional_cargo);
                            foreach($especialidades as $especialidade) {
                                $especialidade = trim($especialidade);
                                if(!empty($especialidade)) {
                        ?>
                        <span class="badge-specialty especialidade-removivel" 
                              data-especialidade="<?= $especialidade ?>" 
                              data-profissional-id="<?= $profissional_id ?>"
                              title="Clique para remover: <?= $especialidade ?>"
                              onclick="removerEspecialidade('<?= $especialidade ?>', <?= $profissional_id ?>, '<?= $especialidade ?>')">
                            <?= $especialidade ?> <i class="fa fa-times ms-1"></i>
                        </span>
                        <?php
                                }
                            }
                        }
                        ?>
                        <button type="button" class="btn btn-primary-action btn-action" onclick="gerenciarEspecialidades(<?= $profissional_id ?>, '<?= addslashes($profissional_nome) ?>')" title="Adicionar Especialidade">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        <form action="deletar_profissional.php" method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $profissional_id ?>">
                            <button type="submit" class="btn btn-danger-action btn-action" onclick="return confirm('Tem certeza que deseja excluir este profissional?')" title="Deletar Profissional">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3 text-muted"></i><br>
                        <strong>Nenhum profissional cadastrado ainda.</strong><br>
                        <small>Comece cadastrando o primeiro profissional acima.</small>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar Nova Especialidade (Global) -->
<div class="modal fade" id="modalAddEspecialidade" tabindex="-1" role="dialog" aria-labelledby="modalAddEspecialidadeLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content modal-content-enhanced">
            <div class="modal-header modal-header-enhanced">
                <h5 class="modal-title" id="modalAddEspecialidadeLabel">
                    <i class="fas fa-plus-circle me-2"></i>Adicionar Especialidade
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group-enhanced">
                    <label for="novaEspecialidadeInput" class="form-label-enhanced">Nova Especialidade:</label>
                    <div class="input-group-enhanced">
                        <span class="input-group-text-enhanced">
                            <i class="fas fa-star"></i>
                        </span>
                        <input type="text" class="form-control-enhanced" id="novaEspecialidadeInput" placeholder="Digite o nome da especialidade">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-enhanced" onclick="salvarNovaEspecialidade()">
                    <i class="fas fa-save me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Gerenciar Especialidades de um Profissional -->
<div class="modal fade" id="modalAddEspecialidadeProfissional" tabindex="-1" role="dialog" aria-labelledby="modalAddEspecialidadeProfissionalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-enhanced">
            <div class="modal-header modal-header-enhanced">
                <h5 class="modal-title" id="modalAddEspecialidadeProfissionalLabel">
                    <i class="fas fa-user-plus me-2"></i>Adicionar Especialidade para <strong id="nomeProfissionalModal"></strong>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="profissionalIdModal">
                <div class="form-group-enhanced">
                    <label for="especialidadeModal" class="form-label-enhanced">Selecione a Especialidade:</label>
                    <div class="input-group-enhanced">
                        <span class="input-group-text-enhanced">
                            <i class="fas fa-star"></i>
                        </span>
                        <select class="form-select-enhanced" id="especialidadeModal">
                            <option value="">Selecione uma especialidade...</option>
                            <?php
                            // Reutilizando a consulta de especialidades
                            #$query_esp_modal = mysqli_query($conn, "SELECT * FROM especialidades ORDER BY especialidades ASC");
                            $query_esp_modal = mysqli_query($conn, "SELECT * FROM especialidades WHERE login = '$login' ORDER BY especialidades ASC");
                            while($row_esp = mysqli_fetch_array($query_esp_modal)) {
                                $especialidade = htmlspecialchars($row_esp['especialidades']);
                                echo '<option value="'.$especialidade.'">'.$especialidade.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="especialidadesAtuais" class="mt-4">
                    <!-- Especialidades atuais do profissional serão carregadas aqui via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn-enhanced" onclick="adicionarEspecialidadeProfissional()">
                    <i class="fas fa-plus me-2"></i>Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Função para validar o formulário
function validarCadastroProfissional() {
    var nome = document.getElementById('nomeProfissional').value;
    var telefone = document.getElementById('telefoneProfissional').value;
    var especialidade = document.getElementById('especialidadeProfissional').value;

    if (nome === "" || telefone === "" || especialidade === "") {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return false;
    }

    var telefoneNumeros = telefone.replace(/\D/g, '');
    if (telefoneNumeros.length < 8) {
        alert("Por favor, insira um telefone válido.");
        return false;
    }

    return true;
}

// Função para abrir modal de adicionar especialidade
function abrirModalEspecialidade() {
    $('#modalAddEspecialidade').modal('show');
    $('#novaEspecialidadeInput').val('');
}

// Função para salvar nova especialidade
function salvarNovaEspecialidade() {
    var novaEspecialidade = $('#novaEspecialidadeInput').val();
    
    if(novaEspecialidade.trim() === '') {
        alert('Por favor, digite uma especialidade');
        return;
    }
    
    $.ajax({
        url: 'ajax_add_especialidade.php',
        type: 'POST',
        data: {
            especialidade: novaEspecialidade
        },
        success: function(response) {
            if(response === 'sucesso') {
                $('#modalAddEspecialidade').modal('hide');
                
                // Adiciona a nova especialidade ao select
                $('#especialidadeProfissional').append(
                    $('<option>', {
                        value: novaEspecialidade,
                        text: novaEspecialidade,
                        selected: true
                    })
                );
                
                alert('Especialidade adicionada com sucesso!');
            } else {
                alert('Erro ao adicionar especialidade: ' + response);
            }
        }
    });
}
</script>
  
  
  
  
  
  
  
  <!-- Lista de Profissionais Cadastrados -->


<!-- Modal para Adicionar Especialidade ao Profissional -->
<div class="modal fade" id="modalAddEspecialidadeProfissional" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Especialidade - <span id="nomeProfissionalModal"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="profissionalIdModal">
                
                <div class="form-group">
                    <label>Selecione a Especialidade:</label>
                    <select class="form-control" id="especialidadeModal">
                        <option value="">Selecione uma especialidade</option>
                        <?php
                        $sql_esp = "SELECT * FROM especialidades ORDER BY especialidades ASC";
                        $query_esp = mysqli_query($conn, $sql_esp);
                        while($row_esp = mysqli_fetch_array($query_esp)) {
                            echo '<option value="'.$row_esp['especialidades'].'">'.$row_esp['especialidades'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div id="especialidadesAtuais" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="adicionarEspecialidadeProfissional()">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS para especialidades removíveis -->
<style>
.especialidade-removivel {
    transition: all 0.3s ease;
    position: relative;
}

.especialidade-removivel:hover {
    background-color: #dc3545 !important;
    transform: scale(1.05);
    border-color: #dc3545 !important;
}

.icone-remover {
    font-size: 10px;
    opacity: 0.7;
    margin-left: 5px;
}

.especialidade-removivel:hover .icone-remover {
    opacity: 1;
    color: white;
}

.especialidade-removivel:active {
    transform: scale(0.95);
}
</style>

<!-- JavaScript para gerenciar especialidades -->
<script>
function removerEspecialidade(especialidade, profissionalId, especialidadeNome) {
    // Confirmar se o usuário realmente quer remover
    if(confirm('Tem certeza que deseja remover a especialidade "' + especialidadeNome + '"?')) {
        
        // Fazer requisição AJAX para remover
        $.ajax({
            url: 'remover_especialidade.php',
            type: 'POST',
            data: {
                especialidade: especialidade,
                profissional_id: profissionalId
            },
            dataType: 'json',
            beforeSend: function() {
                // Mostrar que está processando
                $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '0.5');
            },
            success: function(response) {
                if(response.success) {
                    // Remover o elemento da tela com animação
                    $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    // Restaurar opacidade em caso de erro
                    $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '1');
                    alert('Erro ao remover especialidade: ' + response.message);
                }
            },
            error: function() {
                // Restaurar opacidade em caso de erro
                $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '1');
                alert('Erro ao processar solicitação');
            }
        });
    }
}

function gerenciarEspecialidades(profissionalId, profissionalNome) {
    $('#profissionalIdModal').val(profissionalId);
    $('#nomeProfissionalModal').text(profissionalNome);
    $('#modalAddEspecialidadeProfissional').modal('show');
}

function adicionarEspecialidadeProfissional() {
    var profissionalId = $('#profissionalIdModal').val();
    var especialidade = $('#especialidadeModal').val();
    
    if(especialidade == '') {
        alert('Selecione uma especialidade');
        return;
    }
    
    // Fazer requisição AJAX para adicionar especialidade
    $.ajax({
        url: 'adicionar_especialidade.php',
        type: 'POST',
        data: {
            profissional_id: profissionalId,
            especialidade: especialidade
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Recarregar a página ou atualizar a lista
                location.reload();
            } else {
                alert('Erro ao adicionar especialidade: ' + response.message);
            }
        },
        error: function() {
            alert('Erro ao processar solicitação');
        }
    });
}
</script>

<!-- JavaScript para gerenciar especialidades -->
<script>
function removerEspecialidade(especialidade, profissionalId, especialidadeNome) {
    // Confirmar se o usuário realmente quer remover
    if(confirm('Tem certeza que deseja remover a especialidade "' + especialidadeNome + '"?')) {
        
        // Fazer requisição AJAX para remover
        $.ajax({
            url: 'remover_especialidade.php',
            type: 'POST',
            data: {
                especialidade: especialidade,
                profissional_id: profissionalId
            },
            dataType: 'json',
            beforeSend: function() {
                // Mostrar que está processando
                $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '0.5');
            },
            success: function(response) {
                if(response.success) {
                    // Remover o elemento da tela com animação
                    $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    // Restaurar opacidade em caso de erro
                    $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '1');
                    alert('Erro ao remover especialidade: ' + response.message);
                }
            },
            error: function() {
                // Restaurar opacidade em caso de erro
                $('[data-especialidade="' + especialidade + '"][data-profissional-id="' + profissionalId + '"]').css('opacity', '1');
                alert('Erro ao processar solicitação');
            }
        });
    }
}

function gerenciarEspecialidades(profissionalId, profissionalNome) {
    $('#profissionalIdModal').val(profissionalId);
    $('#nomeProfissionalModal').text(profissionalNome);
    $('#modalAddEspecialidadeProfissional').modal('show');
}

function adicionarEspecialidadeProfissional() {
    var profissionalId = $('#profissionalIdModal').val();
    var especialidade = $('#especialidadeModal').val();
    
    if(especialidade == '') {
        alert('Selecione uma especialidade');
        return;
    }
    
    // Fazer requisição AJAX para adicionar especialidade
    $.ajax({
        url: 'adicionar_especialidade.php',
        type: 'POST',
        data: {
            profissional_id: profissionalId,
            especialidade: especialidade
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Recarregar a página ou atualizar a lista
                location.reload();
            } else {
                alert('Erro ao adicionar especialidade: ' + response.message);
            }
        },
        error: function() {
            alert('Erro ao processar solicitação');
        }
    });
}
</script>

<script>
function gerenciarEspecialidades(profissionalId, nomeProfissional) {
    $('#profissionalIdModal').val(profissionalId);
    $('#nomeProfissionalModal').text(nomeProfissional);
    $('#especialidadeModal').val('');
    
    // Carrega especialidades atuais
    carregarEspecialidadesProfissional(profissionalId);
    
    $('#modalAddEspecialidadeProfissional').modal('show');
}

function carregarEspecialidadesProfissional(profissionalId) {
    $.ajax({
        url: 'ajax_buscar_especialidades_profissional.php',
        type: 'POST',
        data: {
            profissional_id: profissionalId
        },
        success: function(response) {
            $('#especialidadesAtuais').html(response);
        }
    });
}

function adicionarEspecialidadeProfissional() {
    var profissionalId = $('#profissionalIdModal').val();
    var especialidade = $('#especialidadeModal').val();
    
    if(especialidade === '') {
        alert('Por favor, selecione uma especialidade');
        return;
    }
    
    $.ajax({
        url: 'ajax_adicionar_especialidade_profissional.php',
        type: 'POST',
        data: {
            profissional_id: profissionalId,
            especialidade: especialidade
        },
        success: function(response) {
            if(response === 'sucesso') {
                carregarEspecialidadesProfissional(profissionalId);
                $('#especialidadeModal').val('');
                alert('Especialidade adicionada com sucesso!');
            } else {
                alert('Erro: ' + response);
            }
        }
    });
}
</script>  
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
    <script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
    
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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">







































































































































































































































































































































