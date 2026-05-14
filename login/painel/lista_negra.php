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
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\font-awesome\css\font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <!-- Toastr notifications -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
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

                        
                        
                        
                        
                        
                 <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Negra - Sistema de Controle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ff4757 0%, #c44569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: #ccc;
            font-size: 1.1rem;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4757 0%, #c44569 100%);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.3);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff3838 0%, #ff6b6b 100%);
            color: white;
        }

        .btn-danger:hover {
            box-shadow: 0 8px 25px rgba(255, 56, 56, 0.3);
            color: white;
        }

        .btn-secondary {
            background: #555;
            color: white;
        }

        .btn-secondary:hover {
            background: #666;
            color: white;
        }

        .blacklist-container {
            background: linear-gradient(135deg, #2c2c2c 0%, #1e1e1e 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .blacklist-header {
            background: linear-gradient(135deg, #333 0%, #1a1a1a 100%);
            padding: 25px 30px;
            border-bottom: 1px solid #444;
        }

        .blacklist-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ff4757;
            margin-bottom: 5px;
        }

        .blacklist-count {
            color: #aaa;
            font-size: 0.95rem;
        }

        .table-container {
            padding: 30px;
            overflow-x: auto;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #2c2c2c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .modern-table thead {
            background: linear-gradient(135deg, #ff4757 0%, #c44569 100%);
        }

        .modern-table th {
            padding: 20px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .modern-table td {
            padding: 20px;
            border-bottom: 1px solid #444;
            color: #ddd;
            font-weight: 500;
            background: #2c2c2c;
        }

        .modern-table tr:hover td {
            background: #3a3a3a;
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .contact-name {
            font-weight: 600;
            color: #ff4757;
            font-size: 1.05rem;
        }

        .contact-phone {
            color: #ff6b6b;
            font-weight: 500;
        }

        .motivo-badge {
            background: rgba(255, 71, 87, 0.2);
            color: #ff6b6b;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        .btn-whatsapp {
            background: #25d366;
            color: white;
        }

        .btn-whatsapp:hover {
            background: #128c7e;
            color: white;
        }

        .btn-edit {
            background: #ffa502;
            color: white;
        }

        .btn-edit:hover {
            background: #ff9500;
            color: white;
        }

        .btn-delete {
            background: #ff3838;
            color: white;
        }

        .btn-delete:hover {
            background: #ff1744;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #aaa;
        }

        .empty-icon {
            font-size: 4rem;
            color: #555;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #ff4757;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            pointer-events: none;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
            pointer-events: auto;
        }

        .modal .modal-dialog {
            position: relative !important;
            z-index: 1060 !important;
            pointer-events: auto;
            
            background: #2c2c2c;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            margin: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: modalSlideIn 0.3s ease;
            border: 1px solid #444;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 25px 30px;
            color: white;
            position: relative;
        }

        .modal-header.bg-danger {
            background: linear-gradient(135deg, #ff4757 0%, #c44569 100%);
        }

        .modal-header.bg-warning {
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
        }

        .modal-header.bg-delete {
            background: linear-gradient(135deg, #ff3838 0%, #ff1744 100%);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 25px;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
            background: #2c2c2c;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ddd;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #444;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #1a1a1a;
            color: #ddd;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff4757;
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
            background: #222;
        }

        .form-control.error {
            border-color: #ff3838 !important;
            box-shadow: 0 0 0 3px rgba(255, 56, 56, 0.1) !important;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #1a1a1a;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background: #2d1b1b;
            color: #ff6b6b;
            border-left: 4px solid #ff4757;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #2c2c2c 0%, #1e1e1e 100%);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #444;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.blacklist {
            background: linear-gradient(135deg, #ff4757 0%, #c44569 100%);
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff4757;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #aaa;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .header-actions {
                justify-content: center;
            }

            .header-title h1 {
                font-size: 2rem;
                text-align: center;
            }

            .table-container {
                padding: 15px;
            }

            .modern-table {
                font-size: 0.9rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 15px 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .modal-dialog {
                margin: 10px;
                width: calc(100% - 20px);
            }

            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
    // Incluir arquivo de conexão com banco
    include 'conn.php';
    
    // Função para preparar telefone para WhatsApp
    function prepararTelefoneWhatsApp($telefone) {
        // Remove tudo que não é número
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        
        // Se estiver vazio, retorna vazio
        if (empty($telefone_limpo)) {
            return '';
        }
        
        // Se já tem código do país (55), remove para reprocessar
        if (substr($telefone_limpo, 0, 2) == '55' && strlen($telefone_limpo) >= 12) {
            $telefone_limpo = substr($telefone_limpo, 2);
        }
        
        // Se tem 11 dígitos (DDD + 9 + 8 dígitos) - celular com 9
        if (strlen($telefone_limpo) == 11) {
            return '55' . $telefone_limpo;
        }
        
        // Se tem 10 dígitos (DDD + 8 dígitos) - fixo ou celular antigo
        if (strlen($telefone_limpo) == 10) {
            return '55' . $telefone_limpo;
        }
        
        // Se tem 9 dígitos, assume que faltou o DDD, adiciona um padrão (11 - SP)
        if (strlen($telefone_limpo) == 9) {
            return '5511' . $telefone_limpo;
        }
        
        // Se tem 8 dígitos, assume que faltou DDD, adiciona um padrão (11 - SP)
        if (strlen($telefone_limpo) == 8) {
            return '5511' . $telefone_limpo;
        }
        
        // Para outros casos, tenta adicionar o código do Brasil
        if (strlen($telefone_limpo) >= 8) {
            return '55' . $telefone_limpo;
        }
        
        // Se muito curto, retorna vazio
        return '';
    }
    
    // Obter usuário da API (ajuste conforme sua sessão/autenticação)
    #$usuario_api = $_SESSION['usuario_api'] ?? 'user_001';
    
    // Consulta SQL para buscar registros da lista negra
    $sql_busca_lista_negra = "
        SELECT 
            id,
            nome,
            telefone,
            motivo_bloqueio,
            data_bloqueio,
            tentativas_contato,
            observacoes,
            ultima_tentativa
        FROM lista_negra 
        WHERE usuario_api = ? 
        AND status = 'ativo'
        ORDER BY data_bloqueio DESC
    ";
    
    // Preparar e executar consulta
    $stmt = $conn->prepare($sql_busca_lista_negra);
    $stmt->bind_param("s", $usuario_api);
    $stmt->execute();
    $query_busca_lista_negra = $stmt->get_result();
    $total_busca_lista_negra = $query_busca_lista_negra->num_rows;
    ?>

    <div class="page-container">
        <!-- Cabeçalho da página -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title">
                    <h1><i class="fas fa-ban"></i> Bloqueados</h1>
                    <p class="header-subtitle">Sistema de controle e bloqueio de contatos indesejados</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="material-icons">print</i>
                        Imprimir
                    </button>
                    <button id="btnAdicionarContato" class="btn btn-danger" onclick="openModal('addContatoModal')">
                        <i class="material-icons">person_add_disabled</i>
                        Bloquear Contato
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon blacklist">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalBloqueados">
                        <?php echo $total_busca_lista_negra; ?>
                    </h3>
                    <p>Contatos Bloqueados</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="blacklist-container">
            <div class="blacklist-header">
                <div class="blacklist-title"><i class="fas fa-shield-alt"></i> Lista de Bloqueios</div>
                <div class="blacklist-count">
                    <?php echo $total_busca_lista_negra; ?> contato<?php echo $total_busca_lista_negra != 1 ? 's' : ''; ?> bloqueado<?php echo $total_busca_lista_negra != 1 ? 's' : ''; ?>
                </div>
            </div>
            
            <div class="table-container">
                <?php if ($total_busca_lista_negra > 0) { ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Nome/Identificação</th>
                            <th><i class="fas fa-phone"></i> Telefone</th>
                            <th><i class="fas fa-exclamation-triangle"></i> Motivo</th>
                            <th><i class="fas fa-calendar"></i> Data do Bloqueio</th>
                            <th><i class="fas fa-cog"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($row = $query_busca_lista_negra->fetch_assoc()) {
                        $id_contato = $row['id'];
                        $nome = $row['nome'] ?? 'Contato sem nome';
                        $telefone = $row['telefone'] ?? '';
                        $motivo_bloqueio = $row['motivo_bloqueio'] ?? 'Não especificado';
                        $data_bloqueio = !empty($row['data_bloqueio']) ? date('d/m/Y H:i', strtotime($row['data_bloqueio'])) : 'Data não informada';
                        $tentativas_contato = (int)($row['tentativas_contato'] ?? 0);
                        $observacoes = $row['observacoes'] ?? '';
                        
                        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                        
                        // Formatar telefone para exibição
                        $telefone_exibicao = $telefone;
                        if (strlen($telefone_limpo) == 11) {
                            $ddd = substr($telefone_limpo, 0, 2);
                            $numero = substr($telefone_limpo, 2);
                            if (strlen($numero) >= 9) {
                                $parte1 = substr($numero, 0, 5);
                                $parte2 = substr($numero, 5);
                                $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                            }
                        } elseif (strlen($telefone_limpo) == 10) {
                            $ddd = substr($telefone_limpo, 0, 2);
                            $numero = substr($telefone_limpo, 2);
                            if (strlen($numero) >= 8) {
                                $parte1 = substr($numero, 0, 4);
                                $parte2 = substr($numero, 4);
                                $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                            }
                        }
                        
                        // Preparar telefone para WhatsApp com código do país
                        $telefone_whatsapp = prepararTelefoneWhatsApp($telefone_limpo);
                    ?>
                        <tr>
                            <td>
                                <div class="contact-name"><?php echo htmlspecialchars($nome); ?></div>
                                <?php if($tentativas_contato > 0) { ?>
                                <small style="color: #ff6b6b;"><?php echo $tentativas_contato; ?> tentativa(s) após bloqueio</small>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="contact-phone"><?php echo htmlspecialchars($telefone_exibicao); ?></div>
                            </td>
                            <td>
                                <span class="motivo-badge"><?php echo htmlspecialchars($motivo_bloqueio); ?></span>
                            </td>
                            <td>
                                <div style="color: #aaa;"><?php echo $data_bloqueio; ?></div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if(!empty($telefone_whatsapp) && strlen($telefone_whatsapp) >= 10) { ?>
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $telefone_whatsapp; ?>&text=Olá!" 
                                       class="btn btn-sm btn-whatsapp" target="_blank" title="Contatar via WhatsApp - <?php echo $telefone_whatsapp; ?>">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <?php } ?>
                                  
                                    <button type="button" class="btn btn-sm btn-delete" 
                                            onclick="confirmarRemocao(<?php echo $id_contato; ?>, '<?php echo addslashes($nome); ?>')" 
                                            title="Remover da Lista">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="empty-state">
                    <div class="fas fa-shield-alt empty-icon"></div>
                    <h3>Lista negra vazia</h3>
                    <p>Nenhum contato foi bloqueado ainda!</p>
                    <button class="btn btn-danger" onclick="openModal('addContatoModal')" style="margin-top: 20px;">
                        <i class="material-icons">person_add_disabled</i>
                        Bloquear Primeiro Contato
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Contato à Lista Negra -->
    <div class="modal" id="addContatoModal">
        <div class="modal-dialog">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-ban"></i> Bloquear Novo Contato</h5>
                <button type="button" class="modal-close" onclick="closeModal('addContatoModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formAddContato" action="processa_lista_negra.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    
                    <div class="form-group">
                        <label for="nome">Nome/Identificação do Contato</label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               placeholder="Ex: João Silva (Spam), Empresa XYZ (Telemarketing)">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" required 
                               placeholder="(11) 99999-9999">
                    </div>

                    <div class="form-group">
                        <label for="motivo_bloqueio">Motivo do Bloqueio</label>
                        <select class="form-control" id="motivo_bloqueio" name="motivo_bloqueio" required>
                            <option value="">Selecione o motivo</option>
                            <option value="Spam/Mensagens indesejadas">Spam/Mensagens indesejadas</option>
                            <option value="Telemarketing agressivo">Telemarketing agressivo</option>
                            <option value="Cobrança indevida">Cobrança indevida</option>
                            <option value="Vendas não autorizadas">Vendas não autorizadas</option>
                            <option value="Contato inconveniente">Contato inconveniente</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações (Opcional)</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                                  placeholder="Detalhes adicionais sobre o motivo do bloqueio..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addContatoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i>
                        Bloquear Contato
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Contato -->
    <div class="modal" id="editContatoModal">
        <div class="modal-dialog">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Contato Bloqueado</h5>
                <button type="button" class="modal-close" onclick="closeModal('editContatoModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formEditContato" action="processa_lista_negra.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id_contato" id="edit_id_contato">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    
                    <div class="form-group">
                        <label for="edit_nome">Nome/Identificação do Contato</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_telefone">Telefone</label>
                        <input type="tel" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_motivo_bloqueio">Motivo do Bloqueio</label>
                        <select class="form-control" id="edit_motivo_bloqueio" name="motivo_bloqueio" required>
                            <option value="">Selecione o motivo</option>
                            <option value="Spam/Mensagens indesejadas">Spam/Mensagens indesejadas</option>
                            <option value="Telemarketing agressivo">Telemarketing agressivo</option>
                            <option value="Cobrança indevida">Cobrança indevida</option>
                            <option value="Vendas não autorizadas">Vendas não autorizadas</option>
                            <option value="Contato inconveniente">Contato inconveniente</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_observacoes">Observações</label>
                        <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editContatoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">update</i>
                        Atualizar Contato
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Remoção -->
    <div class="modal" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-delete">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Confirmar Remoção</h5>
                <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <p>Tem certeza que deseja remover <span id="contatoNome" style="font-weight: 600;"></span> da lista negra?</p>
                    <p style="margin: 10px 0 0 0;"><strong>Este contato poderá entrar em contato novamente!</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancelar</button>
                <form id="deleteForm" action="processa_lista_negra.php" method="POST" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" id="deleteContatoId" name="id_contato" value="">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    <button type="submit" class="btn btn-delete">
                        <i class="material-icons">delete_forever</i>
                        Confirmar Remoção
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Funções para controle de modais
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Fechar modal ao clicar fora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target.id);
            }
        });

        // Função para editar contato
        function editarContato(id, nome, telefone, motivo, observacoes) {
            document.getElementById('edit_id_contato').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_telefone').value = telefone;
            document.getElementById('edit_motivo_bloqueio').value = motivo;
            document.getElementById('edit_observacoes').value = observacoes;
            openModal('editContatoModal');
        }

        // Função para confirmar remoção
        function confirmarRemocao(id, nome) {
            document.getElementById('deleteContatoId').value = id;
            document.getElementById('contatoNome').textContent = nome;
            openModal('deleteModal');
        }

        // Máscara para telefone melhorada
        function aplicarMascaraTelefone(elemento) {
            elemento.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limita a 11 dígitos
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                if (value.length <= 11) {
                    if (value.length <= 2) {
                        value = value.replace(/(\d{0,2})/, '($1');
                    } else if (value.length <= 7) {
                        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                    }
                }
                e.target.value = value;
            });
            
            // Validação ao sair do campo
            elemento.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length < 10 || value.length > 11) {
                    e.target.style.borderColor = '#ff4757';
                    e.target.title = 'Telefone deve ter 10 ou 11 dígitos (DDD + número)';
                } else {
                    e.target.style.borderColor = '#444';
                    e.target.title = '';
                }
            });
        }

        // Aplicar máscara aos campos de telefone
        document.addEventListener('DOMContentLoaded', function() {
            aplicarMascaraTelefone(document.getElementById('telefone'));
            aplicarMascaraTelefone(document.getElementById('edit_telefone'));
            
            // Animação de entrada dos elementos
            const cards = document.querySelectorAll('.stat-card');
            const tableRows = document.querySelectorAll('.modern-table tbody tr');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, (index * 50) + 300);
            });
        });

        // Fechar todos os modais e recarregar
        (function(){
            function fecharTodosModais(reloadPage = false) {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                });
                document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
                document.body.style.overflow = 'auto';
                if (reloadPage) {
                    window.location.reload();
                }
            }

            document.addEventListener('click', e => {
                if (e.target.closest('.modal-close') || e.target.closest('.modal .btn-secondary')) {
                    e.preventDefault();
                    fecharTodosModais(true);
                }
                else if (e.target.classList.contains('modal')) {
                    fecharTodosModais(false);
                }
            });

            window.addEventListener('load', () => fecharTodosModais(false));
        })();
    </script>

    <?php
    // Fechar conexão
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
                        
                        
                        
                        
                        
 


            </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para o plugin intl-tel-input */
        .iti {
            width: 100%;
        }
        .iti__country-list {
            position: absolute;
            z-index: 9999;
            max-height: 200px;
        }
        .form-group {
            margin-bottom: 25px; /* Espaço suficiente para o dropdown */
        }
        .iti--separate-dial-code .iti__selected-flag {
            background-color: #f8f9fa;
        }
    </style>

    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="..\files\bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
    <!-- Toastr notifications -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Mask plugin -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <!-- Intl Tel Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
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
    
    <script>
        $(document).ready(function() {
            // Inicializa DataTables
            $('#clientesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"
                },
                "responsive": true
            });
            
            // Inicializa tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Variáveis para armazenar as instâncias do intl-tel-input
            var iti, itiEdit;
            
            // Pré-inicializa o plugin de telefone
            function initTelInput() {
                setTimeout(function() {
                    if (document.querySelector("#telefone")) {
                        var input = document.querySelector("#telefone");
                        if (!window.intlTelInputGlobals.getInstance(input)) {
                            iti = window.intlTelInput(input, {
                                initialCountry: "br",
                                preferredCountries: ["br", "pt", "us", "gb", "es"],
                                separateDialCode: true,
                                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                            });
                        }
                    }
                }, 300);
            }
            
            // Inicializar o telefone antes mesmo de abrir o modal
            initTelInput();
            
            // Inicializar o telefone quando o modal for aberto
            $('#addClienteModal').on('shown.bs.modal', function() {
                if (document.querySelector("#telefone")) {
                    var input = document.querySelector("#telefone");
                    
                    // Verifica se já existe uma instância
                    if (window.intlTelInputGlobals.getInstance(input)) {
                        window.intlTelInputGlobals.getInstance(input).destroy();
                    }
                    
                    // Inicializa o plugin IntlTelInput
                    iti = window.intlTelInput(input, {
                        initialCountry: "br",
                        preferredCountries: ["br", "pt", "us", "gb", "es"],
                        separateDialCode: true,
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    });
                    
                    // Limpar o campo de telefone ao abrir o modal
                    $('#telefone').val('');
                } else {
                    console.error("Elemento #telefone não encontrado");
                }
            });
            
            // Inicializar o telefone quando o modal de edição for aberto
            $('#editClienteModal').on('shown.bs.modal', function() {
                if (document.querySelector("#edit_telefone")) {
                    var inputEdit = document.querySelector("#edit_telefone");
                    
                    // Verifica se já existe uma instância
                    if (window.intlTelInputGlobals.getInstance(inputEdit)) {
                        window.intlTelInputGlobals.getInstance(inputEdit).destroy();
                    }
                    
                    // Inicializa o plugin IntlTelInput
                    itiEdit = window.intlTelInput(inputEdit, {
                        initialCountry: "br",
                        preferredCountries: ["br", "pt", "us", "gb", "es"],
                        separateDialCode: true,
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    });
                } else {
                    console.error("Elemento #edit_telefone não encontrado");
                }
            });
            
            // Impedir a inserção de caracteres não numéricos
            $('#telefone, #edit_telefone').on('input', function(e) {
                var value = $(this).val().replace(/\D/g, '');
                $(this).val(value);
            });
        });
        
        // Função para editar cliente
        function editarCliente(id, nome, telefone, endereco) {
            $('#edit_id_cliente').val(id);
            $('#edit_nome').val(nome);
            
            // Limpar o telefone de caracteres não numéricos e de "undefined"
            var telefone_limpo = telefone.replace(/\D/g, '');
            if (telefone_limpo.indexOf('undefined') === 0) {
                telefone_limpo = telefone_limpo.replace('undefined', '');
            }
            $('#edit_telefone').val(telefone_limpo);
            
            $('#edit_endereco').val(endereco);
            $('#editClienteModal').modal('show');
        }
        
        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            $('#clienteNome').text(nome);
            $('#deleteClienteId').val(id);
            $('#deleteModal').modal('show');
        }
        
        // Feedback de sucesso após submissão do formulário
        $('#formAddCliente, #formEditCliente, #deleteForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            
            // Processar o telefone antes do envio do formulário
            if (form.attr('id') === 'formAddCliente' && document.querySelector("#telefone")) {
                var input = document.querySelector("#telefone");
                var instance = window.intlTelInputGlobals.getInstance(input);
                
                if (instance) {
                    // Obter o código do país sem +
                    var countryData = instance.getSelectedCountryData();
                    if (countryData && countryData.dialCode) {
                        var dialCode = countryData.dialCode; // sem o +
                        var phoneNumber = input.value.replace(/\D/g, ''); // remove não-numéricos
                        
                        // Criar número completo apenas com dígitos (sem formatação)
                        var fullPhoneNumber = dialCode + phoneNumber;
                        
                        // Atualizar o campo para o formato desejado: 553184767330
                        $("#telefone").val(fullPhoneNumber);
                        console.log("Número de telefone formatado:", fullPhoneNumber);
                    } else {
                        // Se não conseguir obter o código, usar o Brasil (55) como padrão
                        var phoneNumber = input.value.replace(/\D/g, '');
                        $("#telefone").val("55" + phoneNumber);
                        console.log("Usando código 55 padrão:", "55" + phoneNumber);
                    }
                } else {
                    // Fallback para o Brasil (55) se o plugin não estiver inicializado
                    var phoneNumber = input.value.replace(/\D/g, '');
                    $("#telefone").val("55" + phoneNumber);
                    console.log("Fallback para código 55:", "55" + phoneNumber);
                }
            }
            
            // Processar o telefone de edição antes do envio
            if (form.attr('id') === 'formEditCliente' && document.querySelector("#edit_telefone")) {
                var inputEdit = document.querySelector("#edit_telefone");
                var instanceEdit = window.intlTelInputGlobals.getInstance(inputEdit);
                
                if (instanceEdit) {
                    // Obter o código do país sem +
                    var countryDataEdit = instanceEdit.getSelectedCountryData();
                    if (countryDataEdit && countryDataEdit.dialCode) {
                        var dialCodeEdit = countryDataEdit.dialCode; // sem o +
                        var phoneNumberEdit = inputEdit.value.replace(/\D/g, ''); // remove não-numéricos
                        
                        // Criar número completo apenas com dígitos (sem formatação)
                        var fullPhoneNumberEdit = dialCodeEdit + phoneNumberEdit;
                        
                        // Atualizar o campo para o formato desejado: 553184767330
                        $("#edit_telefone").val(fullPhoneNumberEdit);
                        console.log("Número de telefone editado formatado:", fullPhoneNumberEdit);
                    } else {
                        // Se não conseguir obter o código, usar o Brasil (55) como padrão
                        var phoneNumberEdit = inputEdit.value.replace(/\D/g, '');
                        $("#edit_telefone").val("55" + phoneNumberEdit);
                        console.log("Editando: usando código 55 padrão:", "55" + phoneNumberEdit);
                    }
                } else {
                    // Fallback para o Brasil (55) se o plugin não estiver inicializado
                    var phoneNumberEdit = inputEdit.value.replace(/\D/g, '');
                    $("#edit_telefone").val("55" + phoneNumberEdit);
                    console.log("Editando: fallback para código 55:", "55" + phoneNumberEdit);
                }
            }
            
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    // Fechar modal
                    $('.modal').modal('hide');
                    
                    // Mostrar mensagem de sucesso
                    toastr.success('Operação realizada com sucesso!');
                    
                    // Recarregar a página após 1.5 segundos
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function() {
                    toastr.error('Erro ao processar a solicitação. Tente novamente.');
                }
            });
        });
        
        $(document).ready(function () {
    // Inicialização do intl-tel-input
    var input = document.querySelector("#telefone");
    if (input) {
        window.intlTelInput(input, {
            initialCountry: "br",
            preferredCountries: ["br", "us", "gb", "pt"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });
    }

    // Envio de formulário com telefone concatenado com DDI
    $('#formAddContato').on('submit', function (e) {
        var input = document.querySelector("#telefone");
        var instance = window.intlTelInputGlobals.getInstance(input);

        if (instance) {
            var fullPhoneNumber = instance.getNumber().replace(/\D/g, ''); // remove +, espaços, traços
            $("#telefone").val(fullPhoneNumber);
            console.log("Telefone formatado e enviado:", fullPhoneNumber);
        }
    });
});

        
    </script>

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
include 'erro.php';
?>