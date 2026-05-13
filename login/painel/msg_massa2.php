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
    $usuario_api  = $rows_usuarios['usuario_api'];
    

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
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens Agendadas WhatsApp</title>
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
            --whatsapp-green: #25D366;
            --whatsapp-dark: #128C7E;
            --whatsapp-light: #DCF8C6;
            --whatsapp-bg: #E5DDD5;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --card-hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
            --border-radius: 20px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
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
            transform: translateY(-2px);
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

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-success-modern {
            background: var(--success-gradient);
            color: white;
        }

        .btn-whatsapp {
            background: var(--whatsapp-green);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        /* Estilos para Etiquetas */
        .tags-filter-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .tag-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 0.2rem;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .tag-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .tag-badge.active {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .tag-badge .tag-count {
            background: rgba(255, 255, 255, 0.3);
            padding: 0.1rem 0.3rem;
            border-radius: 10px;
            margin-left: 0.3rem;
            font-size: 0.7rem;
        }

        .tag-input-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            min-height: 45px;
            align-items: center;
        }

        .tag-item {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            gap: 0.3rem;
        }

        .tag-item .remove-tag {
            cursor: pointer;
            color: #0369a1;
            font-weight: bold;
            margin-left: 0.2rem;
            transition: color 0.2s;
        }

        .tag-item .remove-tag:hover {
            color: #dc2626;
        }

        .tag-filter-pill {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0.25rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .tag-filter-pill:hover {
            border-color: #667eea;
            background: #f0f9ff;
        }

        .tag-filter-pill.selected {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        .tags-quick-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-mode-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: center;
        }

        .filter-mode-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .filter-mode-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Modal de Edição de Etiquetas */
        .modal-edit-tags {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-edit-tags.show {
            display: flex;
        }

        .modal-content-tags {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .tags-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
        }

        .suggestion-tag {
            padding: 0.25rem 0.75rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .suggestion-tag:hover {
            border-color: #667eea;
            background: #f0f9ff;
        }

        /* Lista de Clientes */
        .clientes-container {
            max-height: 400px;
            overflow-y: auto;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fafafa;
        }

        .cliente-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .cliente-item:hover {
            background: #f0f9ff;
        }

        .cliente-item.selected {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
        }

        .cliente-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cliente-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .cliente-dados {
            flex: 1;
        }

        .cliente-dados h6 {
            margin: 0;
            font-weight: 600;
            color: #1f2937;
        }

        .cliente-dados small {
            color: #6b7280;
        }

        .cliente-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .cliente-tag-mini {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .edit-tags-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: var(--transition);
            opacity: 0;
        }

        .cliente-item:hover .edit-tags-btn {
            opacity: 1;
        }

        .edit-tags-btn:hover {
            background: #764ba2;
            transform: scale(1.05);
        }

        /* Seleção de Tipo de Mídia */
        .media-type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .media-type-btn {
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
        }

        .media-type-btn:hover {
            border-color: #667eea;
            background: #f8fafc;
        }

        .media-type-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .media-type-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Preview do WhatsApp */
        .whatsapp-preview {
            background: var(--whatsapp-bg);
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            min-height: 300px;
        }

        .whatsapp-header {
            background: var(--whatsapp-dark);
            color: white;
            padding: 1rem;
            border-radius: 12px 12px 0 0;
            margin: -2rem -2rem 1rem -2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .whatsapp-contact-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            color: var(--whatsapp-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .whatsapp-message {
            background: var(--whatsapp-light);
            padding: 0.75rem 1rem;
            border-radius: 18px 18px 18px 5px;
            margin: 0.5rem 0;
            max-width: 80%;
            margin-left: auto;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .whatsapp-message-media {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .whatsapp-message-media img,
        .whatsapp-message-media video {
            width: 100%;
            height: auto;
            display: block;
        }

        .whatsapp-message-audio {
            background: #f0f0f0;
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .whatsapp-time {
            font-size: 0.7rem;
            color: #666;
            text-align: right;
            margin-top: 0.25rem;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: #fafafa;
        }

        .upload-area:hover {
            border-color: #667eea;
            background: #f8fafc;
        }

        .upload-area.active {
            border-color: #667eea;
            background: #f0f9ff;
        }

        .selected-count {
            background: var(--primary-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .schedule-options {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }

        .send-option-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .send-option-item:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .send-option-item.active {
            border-color: #0d6efd;
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
        }

        .send-option-label {
            font-weight: 500;
            margin-bottom: 0;
            cursor: pointer;
        }

        .btn-advanced {
            background: linear-gradient(135deg, #6f42c1 0%, #8a2be2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 24px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-advanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(111, 66, 193, 0.4);
            color: white;
        }

        .advanced-options {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .days-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .day-checkbox {
            position: relative;
        }

        .day-checkbox input[type="checkbox"] {
            display: none;
        }

        .day-label {
            display: inline-block;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .day-checkbox input[type="checkbox"]:checked + .day-label {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .interval-preview {
            background: white;
            padding: 0.75rem;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            text-align: center;
        }

        .form-check-input:checked + .send-option-label {
            color: #1e40af;
            font-weight: 600;
        }

        .special-buttons {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            margin-bottom: 1rem;
        }

        .special-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .special-btn:hover {
            border-color: #667eea;
            background: #f0f9ff;
            transform: translateY(-1px);
        }

        .special-btn.variable {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-color: #3b82f6;
            color: #1e40af;
        }

        .special-btn.ai {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            border-color: #f59e0b;
            color: #92400e;
        }

        .special-btn.ai:hover {
            background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
            border-color: #d97706;
        }

        .form-check-input:checked + .special-btn.ai {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-color: #b45309;
            color: white;
        }

        .form-check-input {
            display: none !important;
        }

        .variable-tag {
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 0 0.2rem;
            display: inline-block;
        }

        .variables-preview {
            background: #f0f9ff;
            border: 2px solid #bae6fd;
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #0369a1;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 0 0.5rem;
            }

            .card-body-modern {
                padding: 1rem;
            }

            .media-type-selector {
                grid-template-columns: repeat(2, 1fr);
            }

            .special-btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fab fa-whatsapp"></i> Mensagens Agendadas WhatsApp</h3>
            <p class="page-subtitle">Envie mensagens personalizadas para seus clientes de forma agendada</p>
        </div>

        <form id="messageForm" action="msg_massa_confirma.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-users"></i> Selecione os Clientes</h5>
                            <div class="ms-auto">
                                <span class="selected-count" id="selectedCount">0 selecionados</span>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="toggleSelectAll()">
                                    <i class="fas fa-check-double"></i> Selecionar Todos
                                </button>
                            </div>
                        </div>

                        <div class="card-body-modern">
                            <!-- Seção de Filtro por Etiquetas -->
                            <div class="tags-filter-section">
                                <h6 class="mb-3">
                                    <i class="fas fa-tags text-primary"></i> 
                                    Filtrar por Etiquetas
                                </h6>
                                
                                <!-- Modo de Filtro -->
                                <div class="filter-mode-selector">
                                    <span class="text-muted">Modo:</span>
                                    <button type="button" class="filter-mode-btn active" data-mode="any" onclick="setFilterMode('any')">
                                        <i class="fas fa-plus-circle"></i> Qualquer etiqueta
                                    </button>
                                    <button type="button" class="filter-mode-btn" data-mode="all" onclick="setFilterMode('all')">
                                        <i class="fas fa-check-double"></i> Todas as etiquetas
                                    </button>
                                    <button type="button" class="filter-mode-btn" data-mode="none" onclick="setFilterMode('none')">
                                        <i class="fas fa-times-circle"></i> Sem filtro
                                    </button>
                                </div>
                                
                                <!-- Tags Disponíveis -->
                                <div class="tags-quick-filter" id="tagsFilter">
                                    <?php
                                    // Buscar todas as etiquetas únicas
                                    $sqlTags = "
                                        SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(etiqueta, ',', numbers.n), ',', -1)) as tag
                                        FROM clientes
                                        CROSS JOIN (
                                            SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
                                            UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                                        ) numbers
                                        WHERE usuario_api = '{$usuario_api}'
                                        AND LENGTH(etiqueta) - LENGTH(REPLACE(etiqueta, ',', '')) >= numbers.n - 1
                                        AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(etiqueta, ',', numbers.n), ',', -1)) != ''
                                        ORDER BY tag
                                    ";
                                    
                                    $resTags = mysqli_query($conn, $sqlTags);
                                    $allTags = [];
                                    while ($row = mysqli_fetch_assoc($resTags)) {
                                        $tag = trim($row['tag']);
                                        if (!empty($tag) && !in_array($tag, $allTags)) {
                                            $allTags[] = $tag;
                                        }
                                    }
                                    
                                    // Contar clientes por tag
                                    foreach ($allTags as $tag): 
                                        $sqlCount = "
                                            SELECT COUNT(*) as total 
                                            FROM clientes 
                                            WHERE usuario_api = '{$usuario_api}' 
                                            AND (etiqueta LIKE '%{$tag}%')
                                        ";
                                        $resCount = mysqli_query($conn, $sqlCount);
                                        $count = mysqli_fetch_assoc($resCount)['total'];
                                    ?>
                                    <div class="tag-filter-pill" onclick="toggleTagFilter('<?= htmlspecialchars($tag) ?>')">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($tag) ?> 
                                        <span class="badge bg-secondary ms-1"><?= $count ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <input type="hidden" name="selected_tags" id="selectedTags" value="">
                                <input type="hidden" name="filter_mode" id="filterMode" value="none">
                            </div>

                            <div class="mb-3">
                                <input type="text"
                                       class="form-control form-control-modern"
                                       id="searchClientes"
                                       placeholder="🔍 Buscar cliente por nome, telefone ou etiqueta..."
                                       onkeyup="filterClientes(this.value)">
                            </div>

                            <div class="clientes-container" id="clientesContainer">
                                <?php
                                $sql = "
                                    SELECT id, nome, telefone, etiqueta
                                    FROM clientes
                                    WHERE usuario_api = '{$usuario_api}'
                                    ORDER BY nome
                                ";
                                $res = mysqli_query($conn, $sql);
                                while ($cli = mysqli_fetch_assoc($res)):
                                    $id       = $cli['id'];
                                    $nome     = $cli['nome'] ?: 'Sem nome';
                                    $telefone = $cli['telefone'];
                                    $etiqueta = $cli['etiqueta'] ?: '';
                                    
                                    // Processar etiquetas
                                    $etiquetas = array_filter(array_map('trim', explode(',', $etiqueta)));
                                    
                                    // Gera iniciais
                                    $parts    = preg_split('/\s+/', trim($nome));
                                    $iniciais = '';
                                    foreach ($parts as $p) {
                                        $iniciais .= mb_strtoupper(mb_substr($p, 0, 1));
                                    }
                                ?>
                                <div class="cliente-item"
                                     data-id="<?= $id ?>"
                                     data-nome="<?= htmlspecialchars($nome) ?>"
                                     data-telefone="<?= htmlspecialchars($telefone) ?>"
                                     data-etiquetas="<?= htmlspecialchars($etiqueta) ?>"
                                     onclick="toggleCliente(this)">
                                    <button type="button" class="edit-tags-btn" onclick="event.stopPropagation(); openEditTagsModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>', '<?= htmlspecialchars($etiqueta) ?>')">
                                        <i class="fas fa-tags"></i> Editar
                                    </button>
                                    <div class="cliente-info">
                                        <input type="checkbox" name="clientes[]" value="<?= $id ?>" style="display:none;">
                                        <div class="cliente-avatar"><?= $iniciais ?></div>
                                        <div class="cliente-dados">
                                            <h6><?= htmlspecialchars($nome) ?></h6>
                                            <small>📱 <?= htmlspecialchars($telefone) ?></small>
                                            <?php if (!empty($etiquetas)): ?>
                                            <div class="cliente-tags">
                                                <?php foreach ($etiquetas as $tag): ?>
                                                <span class="cliente-tag-mini"><?= htmlspecialchars($tag) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="fas fa-check-circle text-success" style="display:none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Tipo de Mídia -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-paper-plane"></i> Tipo de Mensagem</h5>
                        </div>
                        <div class="card-body-modern">
                            <div class="media-type-selector">
                                <div class="media-type-btn active" data-type="text" onclick="selectMediaType('text')">
                                    <i class="fas fa-comment"></i>
                                    <div>Texto</div>
                                </div>
                                <div class="media-type-btn" data-type="image" onclick="selectMediaType('image')">
                                    <i class="fas fa-image"></i>
                                    <div>Imagem</div>
                                </div>
                                <div class="media-type-btn" data-type="video" onclick="selectMediaType('video')">
                                    <i class="fas fa-video"></i>
                                    <div>Vídeo</div>
                                </div>
                                <div class="media-type-btn" data-type="audio" onclick="selectMediaType('audio')">
                                    <i class="fas fa-microphone"></i>
                                    <div>Áudio</div>
                                </div>
                                <div class="media-type-btn" data-type="document" onclick="selectMediaType('document')">
                                    <i class="fas fa-file"></i>
                                    <div>Documento</div>
                                </div>
                            </div>

                            <input type="hidden" name="media_type" id="media_type" value="text">

                            <!-- Upload de Arquivo -->
                            <div id="uploadSection" style="display: none;">
                                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Clique para selecionar arquivo ou arraste aqui</p>
                                    <small class="text-muted">Tamanho máximo: 16MB</small>
                                </div>
                                <input type="file" id="fileInput" name="media_file" style="display: none;" 
                                       onchange="handleFileSelect(this)">
                                <div id="filePreview" style="display: none; margin-top: 1rem;"></div>
                            </div>

                            <!-- Campo de Texto/Legenda -->
                            <div class="mt-3" id="textSection">
                                <label class="form-label-modern" for="messageText">
                                    <i class="fas fa-edit"></i> <span id="textLabel">Mensagem</span>
                                </label>
                                
                                <!-- Botões Especiais -->
                                <div class="special-buttons">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <small class="text-muted me-2 mb-1"><strong>Botões Especiais:</strong></small>
                                        
                                        <!-- Variáveis Dinâmicas -->
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{nome}')">
                                            <i class="fas fa-user"></i> Nome do Cliente
                                        </button>
                                        
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{data}')">
                                            <i class="fas fa-calendar-day"></i> Data de Hoje
                                        </button>
                                        
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{telefone}')">
                                            <i class="fas fa-phone"></i> Telefone
                                        </button>
                                        
                                        <!-- Divisor -->
                                        <div style="width: 100%; margin: 0.5rem 0;"></div>
                                        
                                        <!-- IA para reescrever (apenas marcação) --><?php if($inativo){?>
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="usar_ia" id="usarIA" value="1">
                                            <label class="form-check-label special-btn ai" for="usarIA" style="cursor: pointer; margin: 0;">
                                                <i class="fas fa-magic"></i> Reescrever com IA
                                            </label>
                                        </div>
                                        <?php }?>
                                        <button type="button" class="special-btn" onclick="clearText()">
                                            <i class="fas fa-eraser"></i> Limpar Texto
                                        </button>
                                    </div>
                                    
                                    <!-- Preview das Variáveis -->
                                    <div class="variables-preview" id="variablesPreview" style="display: none;">
                                        <small><strong><i class="fas fa-info-circle"></i> Como ficará:</strong></small>
                                        <div id="variablesPreviewText"></div>
                                    </div>
                                </div>
                                
                                <textarea class="form-control form-control-modern" id="messageText" name="message_text" 
                                          rows="4" placeholder="Digite sua mensagem aqui..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Agendamento -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-clock"></i> Opções de Envio</h5>
                        </div>
                        <div class="card-body-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3 send-option-item" data-option="now">
                                        <input class="form-check-input" type="radio" name="send_option" id="sendNow" value="now" checked>
                                        <label class="form-check-label send-option-label" for="sendNow">
                                            <i class="fas fa-bolt text-warning"></i> Enviar Agora
                                        </label>
                                    </div>
                                    <div class="form-check mb-3 send-option-item" data-option="later">
                                        <input class="form-check-input" type="radio" name="send_option" id="sendLater" value="later">
                                        <label class="form-check-label send-option-label" for="sendLater">
                                            <i class="fas fa-calendar-alt text-info"></i> Agendar Envio
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="scheduleInputs" style="display: none;">
                                        <div class="mb-2">
                                            <input type="date" class="form-control form-control-modern" name="schedule_date" id="scheduleDate">
                                        </div>
                                        <div>
                                            <input type="time" class="form-control form-control-modern" name="schedule_time" id="scheduleTime">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botão Avançado -->
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-advanced" id="toggleAdvanced">
                                        <i class="fas fa-cog me-2"></i>Opções Avançadas
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Opções Avançadas -->
                            <div id="advancedOptions" class="advanced-options" style="display: none;">
                                <div class="row">
                                    <!-- Nome da Campanha -->
                                    <div class="col-md-6 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-tag text-primary"></i>
                                            Nome da Campanha
                                        </div>
                                        <input type="text" class="form-control form-control-modern" name="campaign_name" id="campaignName" placeholder="Digite o nome da campanha">
                                    </div>
                                    
                                    <!-- Horário de Funcionamento -->
                                    <div class="col-md-6 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-clock text-success"></i>
                                            Horário de Funcionamento
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="time" class="form-control form-control-modern" name="start_time" id="startTime" value="01:00">
                                            </div>
                                            <div class="col-6">
                                                <input type="time" class="form-control form-control-modern" name="end_time" id="endTime" value="23:00">
                                            </div>
                                        </div>
                                        <small class="text-muted">EX: das 09:00 às 18:00</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Repetição -->
                                    <div class="col-md-12 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-repeat text-info"></i>
                                            Repetição
                                        </div>
                                        <select class="form-control form-control-modern" name="repeat_option" id="repeatOption">
                                            <option value="once">Enviar apenas uma vez</option>
                                            <option value="daily">Repetir diariamente</option>
                                            <option value="weekly">Repetir semanalmente</option>
                                            <option value="monthly">Repetir mensalmente</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Dias da Semana -->
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-calendar-week text-danger"></i>
                                            Dias da Semana
                                        </div>
                                        <div class="days-selector">
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="monday" name="days[]" value="1" checked>
                                                <label for="monday" class="day-label">Segunda</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="tuesday" name="days[]" value="2" checked>
                                                <label for="tuesday" class="day-label">Terça</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="wednesday" name="days[]" value="3" checked>
                                                <label for="wednesday" class="day-label">Quarta</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="thursday" name="days[]" value="4" checked>
                                                <label for="thursday" class="day-label">Quinta</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="friday" name="days[]" value="5" checked>
                                                <label for="friday" class="day-label">Sexta</label>
                                            </div>
                                          <div class="day-checkbox">
                <input type="checkbox" id="saturday" name="days[]" value="6" checked>
                <label for="saturday" class="day-label">Sábado</label>
            </div>
            <div class="day-checkbox">
                <input type="checkbox" id="sunday" name="days[]" value="0" checked>
                <label for="sunday" class="day-label">Domingo</label>
            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Intervalo entre Mensagens -->
                            <div class="schedule-options mt-4">
                                <h6 class="mb-3"><i class="fas fa-stopwatch text-primary"></i> Intervalo entre Mensagens</h6>
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label-modern" for="intervalValue">
                                            <i class="fas fa-hashtag"></i> Quantidade
                                        </label>
                                        <input type="number" class="form-control form-control-modern" 
                                               id="intervalValue" name="interval_value" value="5" min="1" max="3600"
                                               placeholder="Ex: 5">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-modern" for="intervalUnit">
                                            <i class="fas fa-clock"></i> Unidade
                                        </label>
                                        <select class="form-control form-control-modern" id="intervalUnit" name="interval_unit">
                                            <option value="seconds">Segundos</option>
                                            <option value="minutes" selected>Minutos</option>
                                            <option value="hours">Horas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interval-preview">
                                            <small class="text-muted">Intervalo:</small><br>
                                            <strong class="text-primary" id="intervalDisplay">5 minutos (300s)</strong>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="interval_seconds" id="intervalSeconds" value="300">
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Recomendamos aguardar pelo menos 3-5 segundos entre mensagens para evitar bloqueios.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita - Preview -->
                <div class="col-lg-4">
                    <div class="modern-card sticky-top" style="top: 2rem;">
                        <div class="card-header-modern">
                            <h5><i class="fab fa-whatsapp"></i> Preview WhatsApp</h5>
                        </div>
                        <div class="card-body-modern p-0">
                            <div class="whatsapp-preview">
                                <div class="whatsapp-header">
                                    <div class="whatsapp-contact-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <strong id="previewContactName">Cliente Selecionado</strong><br>
                                        <small style="opacity: 0.8;">online</small>
                                    </div>
                                </div>
                                
                                <div id="messagePreview">
                                    <div class="whatsapp-message">
                                        <div id="previewContent">Digite sua mensagem para ver o preview...</div>
                                        <div class="whatsapp-time">
                                            <span id="previewTime">00:00</span>
                                            <i class="fas fa-check-double text-primary ms-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-modern btn-whatsapp btn-lg" id="sendButton">
                            <i class="fab fa-whatsapp"></i> Disparar Mensagem em Massa
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Botão para Visualizar Campanhas -->
        <div class="text-end mb-4">
            <a href="msg_relatorio.php" id="btnViewCampaigns" class="btn btn-primary">
                <i class="fas fa-bullhorn me-2"></i> Visualizar Campanhas
            </a>
        </div>
    </div>

    <!-- Modal para Editar Etiquetas -->
    <div class="modal-edit-tags" id="modalEditTags">
        <div class="modal-content-tags">
            <h5 class="mb-3">
                <i class="fas fa-tags text-primary"></i> 
                Editar Etiquetas
            </h5>
            <p class="text-muted" id="clienteNomeModal">Cliente: </p>
            
            <div class="tag-input-wrapper">
                <label class="form-label-modern">Etiquetas (separadas por vírgula)</label>
                <input type="text" 
                       class="form-control form-control-modern" 
                       id="tagsInput" 
                       placeholder="Ex: lista de leads, boletos atrasados, premium">
            </div>
            
            <div class="tags-suggestions">
                <small class="text-muted d-block w-100 mb-2">Sugestões:</small>
                <span class="suggestion-tag" onclick="addSuggestionTag('lista de leads')">lista de leads</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('boletos atrasados')">boletos atrasados</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('premium')">premium</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('vip')">vip</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('novo cliente')">novo cliente</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('inativo')">inativo</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('potencial')">potencial</span>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" onclick="saveClienteTags()">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeEditTagsModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedClients = [];
        let currentMediaType = 'text';
        let cursorPosition = 0;
        let selectedTags = [];
        let filterMode = 'none';
        let currentEditingClientId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar interface
            updateSendOptions();
            updateInterval();
            updatePreviewTime();
            setInterval(updatePreviewTime, 1000);
            
            // Event listeners para opções de envio
            const sendOptions = document.querySelectorAll('input[name="send_option"]');
            sendOptions.forEach(option => {
                option.addEventListener('change', updateSendOptions);
            });
            
            const sendOptionItems = document.querySelectorAll('.send-option-item');
            sendOptionItems.forEach(item => {
                item.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    updateSendOptions();
                });
            });
            
            // Gerenciar opções avançadas
            const toggleAdvanced = document.getElementById('toggleAdvanced');
            const advancedOptions = document.getElementById('advancedOptions');
            let advancedVisible = false;
            
            toggleAdvanced.addEventListener('click', function() {
                advancedVisible = !advancedVisible;
                
                if (advancedVisible) {
                    advancedOptions.style.display = 'block';
                    advancedOptions.classList.add('fade-in');
                    this.innerHTML = '<i class="fas fa-times me-2"></i>Fechar Opções Avançadas';
                    this.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                } else {
                    advancedOptions.style.display = 'none';
                    advancedOptions.classList.remove('fade-in');
                    this.innerHTML = '<i class="fas fa-cog me-2"></i>Opções Avançadas';
                    this.style.background = 'linear-gradient(135deg, #6f42c1 0%, #8a2be2 100%)';
                }
            });
            
            // Gerenciar seleção de dias da semana
            const dayCheckboxes = document.querySelectorAll('input[name="days[]"]');
            dayCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateDaySelection(this);
                });
                // Inicializar seleção
                updateDaySelection(checkbox);
            });
            
            // Gerenciar opção de repetição
            const repeatOption = document.getElementById('repeatOption');
            const daysSection = document.querySelector('.days-selector').parentElement;
            
            repeatOption.addEventListener('change', function() {
                if (this.value === 'once') {
                    daysSection.style.opacity = '0.5';
                    dayCheckboxes.forEach(cb => cb.disabled = true);
                } else {
                    daysSection.style.opacity = '1';
                    dayCheckboxes.forEach(cb => cb.disabled = false);
                }
            });
            
            // Event listeners para intervalo
            document.getElementById('intervalValue').addEventListener('input', updateInterval);
            document.getElementById('intervalUnit').addEventListener('change', updateInterval);

            // Event listeners para mensagem
            document.getElementById('messageText').addEventListener('input', function() {
                updatePreview();
                updateVariablesPreview();
            });

            // Salvar posição do cursor
            document.getElementById('messageText').addEventListener('click', function() {
                cursorPosition = this.selectionStart;
            });

            document.getElementById('messageText').addEventListener('keyup', function() {
                cursorPosition = this.selectionStart;
            });
            
            // Definir data mínima como hoje
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('scheduleDate').min = today;
        });

        // Funções de Etiquetas
        function toggleTagFilter(tag) {
            const pill = event.currentTarget;
            const index = selectedTags.indexOf(tag);
            
            if (index > -1) {
                selectedTags.splice(index, 1);
                pill.classList.remove('selected');
            } else {
                selectedTags.push(tag);
                pill.classList.add('selected');
            }
            
            document.getElementById('selectedTags').value = selectedTags.join(',');
            applyTagFilter();
        }

        function setFilterMode(mode) {
            filterMode = mode;
            document.getElementById('filterMode').value = mode;
            
            // Atualizar visual dos botões
            document.querySelectorAll('.filter-mode-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-mode="${mode}"]`).classList.add('active');
            
            // Se modo "none", limpar seleções e mostrar todos
            if (mode === 'none') {
                selectedTags = [];
                document.getElementById('selectedTags').value = '';
                document.querySelectorAll('.tag-filter-pill').forEach(pill => {
                    pill.classList.remove('selected');
                });
            }
            
            applyTagFilter();
        }

        function applyTagFilter() {
            const clientes = document.querySelectorAll('.cliente-item');
            
            clientes.forEach(cliente => {
                const clienteTags = cliente.dataset.etiquetas.toLowerCase().split(',').map(t => t.trim());
                let show = true;
                
                if (filterMode === 'none' || selectedTags.length === 0) {
                    show = true;
                } else if (filterMode === 'any') {
                    // Mostrar se tem QUALQUER uma das tags selecionadas
                    show = selectedTags.some(tag => 
                        clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                    );
                } else if (filterMode === 'all') {
                    // Mostrar se tem TODAS as tags selecionadas
                    show = selectedTags.every(tag => 
                        clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                    );
                }
                
                cliente.style.display = show ? 'block' : 'none';
            });
            
            // Atualizar contador se necessário
            updateVisibleCount();
        }

        function updateVisibleCount() {
            const visibleClientes = document.querySelectorAll('.cliente-item:not([style*="display: none"])');
            // Você pode adicionar um contador visual se desejar
        }

        function openEditTagsModal(clienteId, clienteNome, etiquetas) {
            currentEditingClientId = clienteId;
            document.getElementById('modalEditTags').classList.add('show');
            document.getElementById('clienteNomeModal').textContent = 'Cliente: ' + clienteNome;
            document.getElementById('tagsInput').value = etiquetas;
        }

        function closeEditTagsModal() {
            document.getElementById('modalEditTags').classList.remove('show');
            currentEditingClientId = null;
        }

        function addSuggestionTag(tag) {
            const input = document.getElementById('tagsInput');
            const currentTags = input.value.split(',').map(t => t.trim()).filter(t => t);
            
            if (!currentTags.includes(tag)) {
                if (currentTags.length > 0) {
                    input.value = currentTags.join(', ') + ', ' + tag;
                } else {
                    input.value = tag;
                }
            }
        }

        function saveClienteTags() {
            if (!currentEditingClientId) return;
            
            const tags = document.getElementById('tagsInput').value;
            
            // Fazer requisição AJAX para salvar no banco
            fetch('update_cliente_tags.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cliente_id=' + currentEditingClientId + '&etiquetas=' + encodeURIComponent(tags)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar a visualização
                    const clienteItem = document.querySelector(`[data-id="${currentEditingClientId}"]`);
                    if (clienteItem) {
                        clienteItem.dataset.etiquetas = tags;
                        
                        // Atualizar tags visuais
                        const tagsContainer = clienteItem.querySelector('.cliente-tags');
                        if (tagsContainer || tags) {
                            const clienteDados = clienteItem.querySelector('.cliente-dados');
                            let newTagsHtml = '';
                            
                            if (tags) {
                                const tagsList = tags.split(',').map(t => t.trim()).filter(t => t);
                                if (tagsList.length > 0) {
                                    newTagsHtml = '<div class="cliente-tags">';
                                    tagsList.forEach(tag => {
                                        newTagsHtml += `<span class="cliente-tag-mini">${tag}</span>`;
                                    });
                                    newTagsHtml += '</div>';
                                }
                            }
                            
                            // Remover tags antigas se existirem
                            if (tagsContainer) {
                                tagsContainer.remove();
                            }
                            
                            // Adicionar novas tags se houver
                            if (newTagsHtml) {
                                clienteDados.insertAdjacentHTML('beforeend', newTagsHtml);
                            }
                        }
                    }
                    
                    // Recarregar filtros de tags se necessário
                    // Aqui você pode adicionar código para atualizar os filtros disponíveis
                    
                    closeEditTagsModal();
                    
                    // Mostrar mensagem de sucesso (opcional)
                    alert('Etiquetas atualizadas com sucesso!');
                } else {
                    alert('Erro ao salvar etiquetas: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar etiquetas');
            });
        }

        function updateDaySelection(checkbox) {
            const label = checkbox.nextElementSibling;
            if (checkbox.checked) {
                label.style.background = '#0d6efd';
                label.style.color = 'white';
                label.style.borderColor = '#0d6efd';
            } else {
                label.style.background = '#f8f9fa';
                label.style.color = '#495057';
                label.style.borderColor = '#dee2e6';
            }
        }

        // Função para atualizar interface das opções de envio
        function updateSendOptions() {
            const sendOptionItems = document.querySelectorAll('.send-option-item');
            const scheduleInputs = document.getElementById('scheduleInputs');
            const sendButton = document.getElementById('sendButton');
            
            sendOptionItems.forEach(item => {
                const radio = item.querySelector('input[type="radio"]');
                if (radio.checked) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
            
            // Mostrar/ocultar campos de agendamento
            if (document.getElementById('sendLater').checked) {
                scheduleInputs.style.display = 'block';
                sendButton.innerHTML = '<i class="fas fa-clock"></i> Agendar Mensagem em Massa';
            } else {
                scheduleInputs.style.display = 'none';
                sendButton.innerHTML = '<i class="fab fa-whatsapp"></i> Disparar Mensagem em Massa';
            }
        }

        // Inserir variável na posição do cursor
        function insertVariable(variable) {
            const messageText = document.getElementById('messageText');
            const startPos = messageText.selectionStart;
            const endPos = messageText.selectionEnd;
            const textBefore = messageText.value.substring(0, startPos);
            const textAfter = messageText.value.substring(endPos);
            
            messageText.value = textBefore + variable + ' ' + textAfter;
            messageText.focus();
            
            // Posicionar cursor após a variável inserida
            const newPos = startPos + variable.length + 1;
            messageText.setSelectionRange(newPos, newPos);
            
            updatePreview();
            updateVariablesPreview();
        }

        // Limpar texto
        function clearText() {
            if (confirm('Tem certeza que deseja limpar todo o texto?')) {
                document.getElementById('messageText').value = '';
                updatePreview();
                updateVariablesPreview();
            }
        }

        // Atualizar intervalo entre mensagens
        function updateInterval() {
            const value = parseInt(document.getElementById('intervalValue').value) || 1;
            const unit = document.getElementById('intervalUnit').value;
            const display = document.getElementById('intervalDisplay');
            const hiddenSeconds = document.getElementById('intervalSeconds');
            
            let seconds = value;
            let unitText = '';
            
            switch(unit) {
                case 'seconds':
                    seconds = value;
                    unitText = value === 1 ? 'segundo' : 'segundos';
                    break;
                case 'minutes':
                    seconds = value * 60;
                    unitText = value === 1 ? 'minuto' : 'minutos';
                    break;
                case 'hours':
                    seconds = value * 3600;
                    unitText = value === 1 ? 'hora' : 'horas';
                    break;
            }
            
            display.innerHTML = `${value} ${unitText} <span class="text-muted">(${seconds}s)</span>`;
            hiddenSeconds.value = seconds;
        }

        // Atualizar preview das variáveis
        function updateVariablesPreview() {
            const messageText = document.getElementById('messageText').value;
            const preview = document.getElementById('variablesPreview');
            const previewText = document.getElementById('variablesPreviewText');
            
            // Verificar se há variáveis no texto
            const hasVariables = messageText.includes('{nome}') || messageText.includes('{data}') || 
                                messageText.includes('{telefone}');
            
            if (hasVariables && messageText.trim()) {
                // Buscar dados do cliente selecionado ou usar exemplos
                let nomeExemplo = 'João Silva';
                let telefoneExemplo = '(11) 99999-1111';
                
                if (selectedClients.length === 1) {
                    const clienteSelecionado = document.querySelector('.cliente-item.selected');
                    if (clienteSelecionado) {
                        nomeExemplo = clienteSelecionado.dataset.nome;
                        telefoneExemplo = clienteSelecionado.dataset.telefone;
                    }
                } else if (selectedClients.length > 1) {
                    nomeExemplo = '[Nome personalizado para cada cliente]';
                    telefoneExemplo = '[Telefone de cada cliente]';
                }
                
                // Substituir variáveis por valores de exemplo
                let exampleText = messageText
                    .replace(/{nome}/g, `<span class="variable-tag">${nomeExemplo}</span>`)
                    .replace(/{data}/g, '<span class="variable-tag">' + new Date().toLocaleDateString('pt-BR') + '</span>')
                    .replace(/{telefone}/g, `<span class="variable-tag">${telefoneExemplo}</span>`);
                
                previewText.innerHTML = exampleText;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        // Função melhorada para substituir variáveis no preview do WhatsApp
        function getPreviewText(originalText, clienteNome = '') {
            if (!originalText) return originalText;
            
            let previewText = originalText;
            
            // Buscar dados do cliente selecionado se houver apenas um
            let nomeParaUsar = 'João Silva';
            let telefoneParaUsar = '(11) 99999-1111';
            
            if (selectedClients.length === 1) {
                const clienteSelecionado = document.querySelector('.cliente-item.selected');
                if (clienteSelecionado) {
                    nomeParaUsar = clienteSelecionado.dataset.nome;
                    telefoneParaUsar = clienteSelecionado.dataset.telefone;
                }
            } else if (selectedClients.length > 1) {
                nomeParaUsar = '[Nome do Cliente]';
                telefoneParaUsar = '[Telefone]';
            }
            
            previewText = previewText
                .replace(/{nome}/g, nomeParaUsar)
                .replace(/{data}/g, new Date().toLocaleDateString('pt-BR'))
                .replace(/{telefone}/g, telefoneParaUsar);
            
            return previewText;
        }

        // Atualizar hora no preview
        function updatePreviewTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('previewTime').textContent = timeString;
        }

        // Selecionar/deselecionar cliente
        function toggleCliente(element) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            const checkIcon = element.querySelector('.fa-check-circle');
            const clienteId = element.dataset.id;
            const clienteNome = element.dataset.nome;

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                checkbox.checked = false;
                checkIcon.style.display = 'none';
                selectedClients = selectedClients.filter(id => id !== clienteId);
            } else {
                element.classList.add('selected');
                checkbox.checked = true;
                checkIcon.style.display = 'block';
                selectedClients.push(clienteId);
                
                // Atualizar preview com o nome do cliente
                if (selectedClients.length === 1) {
                    document.getElementById('previewContactName').textContent = clienteNome;
                } else {
                    document.getElementById('previewContactName').textContent = `${selectedClients.length} contatos`;
                }
            }

            updateSelectedCount();
            updatePreview(); // Atualizar preview para mostrar nome real nas variáveis
            updateVariablesPreview(); // Atualizar preview das variáveis
        }

        // Selecionar todos os clientes
        function toggleSelectAll() {
            const allClientes = document.querySelectorAll('.cliente-item:not([style*="display: none"])');
            const allSelected = selectedClients.length === allClientes.length;

            allClientes.forEach(cliente => {
                const checkbox = cliente.querySelector('input[type="checkbox"]');
                const checkIcon = cliente.querySelector('.fa-check-circle');
                
                if (allSelected) {
                    cliente.classList.remove('selected');
                    checkbox.checked = false;
                    checkIcon.style.display = 'none';
                } else {
                    cliente.classList.add('selected');
                    checkbox.checked = true;
                    checkIcon.style.display = 'block';
                }
            });

            selectedClients = allSelected ? [] : Array.from(allClientes).map(c => c.dataset.id);
            updateSelectedCount();
            updatePreview();
            updateVariablesPreview();
        }

        // Atualizar contador de selecionados
        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = `${selectedClients.length} selecionados`;
        }

        // Selecionar tipo de mídia
        function selectMediaType(type) {
            currentMediaType = type;
            
            // Atualizar botões
            document.querySelectorAll('.media-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-type="${type}"]`).classList.add('active');
            document.getElementById('media_type').value = type;

            // Mostrar/ocultar upload
            const uploadSection = document.getElementById('uploadSection');
            const textSection = document.getElementById('textSection');
            const textLabel = document.getElementById('textLabel');
            const messageText = document.getElementById('messageText');
            const fileInput = document.getElementById('fileInput');
            
            // Limpar arquivo anterior
            fileInput.value = '';
            document.getElementById('filePreview').style.display = 'none';
            
            if (type === 'text') {
                uploadSection.style.display = 'none';
                textSection.style.display = 'block';
                textLabel.textContent = 'Mensagem';
                messageText.placeholder = 'Digite sua mensagem aqui...';
                messageText.required = true;
            } else if (type === 'audio' || type === 'document') {
                // Áudio e documento não têm legenda no WhatsApp
                uploadSection.style.display = 'block';
                textSection.style.display = 'none';
                messageText.value = '';
                messageText.required = false;
                
                // Configurar accept do input
                if (type === 'audio') {
                    fileInput.accept = 'audio/*,.mp3,.wav,.ogg,.m4a,.aac';
                } else {
                    fileInput.accept = '.pdf,.doc,.docx,.txt';
                }
            } else {
                // Imagem e vídeo podem ter legenda
                uploadSection.style.display = 'block';
                textSection.style.display = 'block';
                textLabel.textContent = 'Legenda (opcional)';
                messageText.placeholder = 'Digite uma legenda para sua ' + (type === 'image' ? 'imagem' : 'vídeo') + '...';
                messageText.required = false;
                
                // Configurar accept do input
                if (type === 'image') {
                    fileInput.accept = 'image/*,.jpg,.jpeg,.png,.gif,.webp';
                } else {
                    fileInput.accept = 'video/*,.mp4,.avi,.mov,.mkv,.webm';
                }
            }

            updatePreview();
            updateVariablesPreview();
        }

        // Manipular seleção de arquivo
        function handleFileSelect(input) {
            const file = input.files[0];
            const preview = document.getElementById('filePreview');
            
            if (file) {
                // Validar tipo de arquivo baseado no tipo de mídia selecionado
                let validTypes = [];
                let errorMessage = '';
                
                switch(currentMediaType) {
                    case 'image':
                        validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                        errorMessage = 'Por favor, selecione apenas imagens (JPG, PNG, GIF, WebP).';
                        break;
                    case 'video':
                        validTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv', 'video/webm'];
                        errorMessage = 'Por favor, selecione apenas vídeos (MP4, AVI, MOV, MKV, WebM).';
                        break;
                    case 'audio':
                        validTypes = ['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/aac'];
                        errorMessage = 'Por favor, selecione apenas áudios (MP3, WAV, OGG, M4A, AAC).';
                        break;
                    case 'document':
                        validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
                        errorMessage = 'Por favor, selecione apenas documentos (PDF, DOC, DOCX, TXT).';
                        break;
                }
                
                // Validação flexível para tipos de arquivo
                let isValid = false;
                if (currentMediaType === 'image' && file.type.startsWith('image/')) isValid = true;
                else if (currentMediaType === 'video' && file.type.startsWith('video/')) isValid = true;
                else if (currentMediaType === 'audio' && file.type.startsWith('audio/')) isValid = true;
                else if (currentMediaType === 'document') {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (['pdf', 'doc', 'docx', 'txt'].includes(ext)) isValid = true;
                }
                
                if (!isValid) {
                    alert(errorMessage);
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewHTML = '';
                    
                    if (file.type.startsWith('image/')) {
                        previewHTML = `<img src="${e.target.result}" style="max-width: 100%; border-radius: 8px;">`;
                    } else if (file.type.startsWith('video/')) {
                        previewHTML = `<video controls style="max-width: 100%; border-radius: 8px;"><source src="${e.target.result}"></video>`;
                    } else if (file.type.startsWith('audio/')) {
                        previewHTML = `<audio controls style="width: 100%;"><source src="${e.target.result}"></audio>`;
                    } else {
                        previewHTML = `<div class="d-flex align-items-center gap-2 p-2 bg-light rounded"><i class="fas fa-file"></i> ${file.name}</div>`;
                    }
                    
                    preview.innerHTML = previewHTML;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                updatePreview();
            }
        }

        // Atualizar preview da mensagem
        function updatePreview() {
            const messageText = document.getElementById('messageText').value;
            const previewContent = document.getElementById('previewContent');
            const fileInput = document.getElementById('fileInput');
            
            let previewHTML = '';
            
            if (currentMediaType !== 'text' && fileInput.files[0]) {
                const file = fileInput.files[0];
                
                if (file.type.startsWith('image/')) {
                    previewHTML += `<div class="whatsapp-message-media"><img src="${URL.createObjectURL(file)}" alt="Imagem"></div>`;
                } else if (file.type.startsWith('video/')) {
                    previewHTML += `<div class="whatsapp-message-media"><video controls><source src="${URL.createObjectURL(file)}"></video></div>`;
                } else if (file.type.startsWith('audio/')) {
                    previewHTML += `<div class="whatsapp-message-audio"><i class="fas fa-play"></i><div>🎵 Mensagem de áudio</div><div>0:${Math.floor(Math.random() * 60).toString().padStart(2, '0')}</div></div>`;
                } else {
                    previewHTML += `<div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-file"></i> ${file.name}</div>`;
                }
            }
            
            // Só mostra texto se não for áudio ou documento
            if (currentMediaType !== 'audio' && currentMediaType !== 'document') {
                if (messageText) {
                    // Usar função para substituir variáveis no preview
                    const processedText = getPreviewText(messageText, selectedClients.length === 1 ? document.querySelector('.cliente-item.selected')?.dataset.nome : '');
                    previewHTML += `<div>${processedText}</div>`;
                } else if (currentMediaType === 'text') {
                    previewHTML = 'Digite sua mensagem para ver o preview...';
                }
            }
            
            // Se for áudio ou documento e não tem arquivo, mostra mensagem
            if ((currentMediaType === 'audio' || currentMediaType === 'document') && !fileInput.files[0]) {
                previewHTML = `Selecione um ${currentMediaType === 'audio' ? 'áudio' : 'documento'} para ver o preview...`;
            }
            
            previewContent.innerHTML = previewHTML;
        }

        // Busca de clientes
        function filterClientes(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            const clientes = document.querySelectorAll('.cliente-item');
            
            clientes.forEach(cliente => {
                const nome = cliente.dataset.nome.toLowerCase();
                const telefone = cliente.dataset.telefone.toLowerCase();
                const etiquetas = cliente.dataset.etiquetas.toLowerCase();
                
                // Também buscar nas etiquetas
                if (nome.includes(searchTerm) || telefone.includes(searchTerm) || etiquetas.includes(searchTerm)) {
                    // Respeitar filtro de tags se ativo
                    if (filterMode === 'none' || selectedTags.length === 0) {
                        cliente.style.display = 'block';
                    } else {
                        // Verificar se cliente passa no filtro de tags
                        const clienteTags = etiquetas.split(',').map(t => t.trim());
                        let show = true;
                        
                        if (filterMode === 'any') {
                            show = selectedTags.some(tag => 
                                clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                            );
                        } else if (filterMode === 'all') {
                            show = selectedTags.every(tag => 
                                clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                            );
                        }
                        
                        cliente.style.display = show ? 'block' : 'none';
                    }
                } else {
                    cliente.style.display = 'none';
                }
            });
        }
        
        // Validação do formulário
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            if (selectedClients.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos um cliente para enviar a mensagem.');
                return;
            }
            
            const messageText = document.getElementById('messageText').value.trim();
            const fileInput = document.getElementById('fileInput');
            
            // Para texto, obrigatório ter mensagem
            if (currentMediaType === 'text' && !messageText) {
                e.preventDefault();
                alert('Digite uma mensagem de texto.');
                return;
            }
            
            // Para mídia, obrigatório ter arquivo
            if (currentMediaType !== 'text' && !fileInput.files[0]) {
                e.preventDefault();
                alert(`Selecione um ${currentMediaType === 'image' ? 'imagem' : currentMediaType === 'video' ? 'vídeo' : currentMediaType === 'audio' ? 'áudio' : 'documento'} para enviar.`);
                return;
            }
            
            // Para agendamento, verificar data e hora
            const sendOption = document.querySelector('input[name="send_option"]:checked').value;
            if (sendOption === 'later') {
                const scheduleDate = document.getElementById('scheduleDate').value;
                const scheduleTime = document.getElementById('scheduleTime').value;
                
                if (!scheduleDate || !scheduleTime) {
                    e.preventDefault();
                    alert('Selecione a data e hora para agendamento.');
                    return;
                }
                
                // Verificar se a data/hora é futura
                const agendamento = new Date(scheduleDate + ' ' + scheduleTime);
                const agora = new Date();
                
                if (agendamento <= agora) {
                    e.preventDefault();
                    alert('A data e hora do agendamento deve ser futura.');
                    return;
                }
            }
            
            // Validar intervalo
            const intervalValue = parseInt(document.getElementById('intervalValue').value);
            if (!intervalValue || intervalValue < 1) {
                e.preventDefault();
                alert('O intervalo entre mensagens deve ser de pelo menos 1.');
                return;
            }
            
            // Aviso para muitos clientes com intervalo pequeno
            if (selectedClients.length > 50 && intervalValue < 3) {
                if (!confirm(`Você selecionou ${selectedClients.length} clientes com intervalo de ${intervalValue} segundos.\nIsso pode demorar muito tempo. Deseja continuar?`)) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Drag and drop para upload
        const uploadArea = document.querySelector('.upload-area');
        
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('active');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('active');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('active');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('fileInput').files = files;
                    handleFileSelect(document.getElementById('fileInput'));
                }
            });
        }
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
    <script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>
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

  gtag('config', 'UA-23581568-13');
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