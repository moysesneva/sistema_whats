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
    <title>Gerenciamento de Clientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
      
        .page-header {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: #666;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            color: white;
        }

        .btn-success:hover {
            box-shadow: 0 8px 25px rgba(86, 171, 47, 0.3);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            color: white;
        }

        .clients-container {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .clients-header {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
            padding: 25px 30px;
            border-bottom: 1px solid #e0e6ed;
        }

        .clients-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .clients-count {
            color: #666;
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
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .modern-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            font-weight: 500;
        }

        .modern-table tr:hover {
            background: #f8f9ff;
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .client-name {
            font-weight: 600;
            color: #333;
            font-size: 1.05rem;
        }

        .client-phone {
            color: #667eea;
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
            background: #007bff;
            color: white;
        }

        .btn-edit:hover {
            background: #0056b3;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        /* Modal Styles - AJUSTADOS PARA CENTRALIZAÇÃO */
      /* Modal Styles – AJUSTADOS PARA CENTRALIZAÇÃO E CLIQUES */

/* 1) O backdrop padrão do Bootstrap (se usar) */
.modal-backdrop {
  z-index: 1040 !important;
}

/* 2) Sua máscara personalizada */
.modal {
  display: none;
  position: fixed;
  z-index: 1050;               /* acima do backdrop */
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  pointer-events: none;        /* não bloqueia cliques quando oculto */
}

/* 3) Quando aberto, recebe eventos e centraliza */
.modal.show {
  display: flex !important;
  align-items: center;
  justify-content: center;
  pointer-events: auto;
}

/* 4) O diálogo em si, sempre acima */
.modal .modal-dialog {
  position: relative !important;
  z-index: 1060 !important;    /* acima da máscara */
  pointer-events: auto;        /* garante que seja clicável */
  
  background: white;
  border-radius: 20px;
  max-width: 500px;
  width: 90%;
  margin: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  animation: modalSlideIn 0.3s ease;
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

        .modal-header.bg-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .modal-header.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .modal-header.bg-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
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
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #f8f9fa;
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
            background: #ffe6e6;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
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

        .stat-icon.clients {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #666;
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
        /* 1) Garantir que o backdrop do Bootstrap fique atrás do modal */


    </style>
</head>
<body>
    <div class="page-container">
        <!-- Cabeçalho da página -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title">
                    <h1>Gerenciamento de Clientes</h1>
                    <p class="header-subtitle">Gerencie seus clientes de forma fácil e organizada</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="material-icons">print</i>
                        Imprimir
                    </button>
                    <button id="btnAdicionarCliente" class="btn btn-success" onclick="openModal('addClienteModal')">
                        <i class="material-icons">person_add</i>
                        Novo Cliente
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon clients">
                    <i class="material-icons">people</i>
                </div>
                <div class="stat-content">
                    <h3 id="totalClientes">
                        <?php 
$sql_busca_clientes = "
    SELECT * FROM clientes 
    WHERE usuario_api = '$usuario_api' 
    ORDER BY 
        CASE 
            WHEN nome IS NULL OR nome = '' THEN 1 
            ELSE 0 
        END,
        nome ASC
";

                        $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
                        $total_busca_clientes = mysqli_num_rows($query_busca_clientes);
                        echo $total_busca_clientes;
                        ?>
                    </h3>
                    <p>Total de Clientes</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="clients-container">
            <div class="clients-header">
                <div class="clients-title">Lista de Clientes</div>
                <div class="clients-count">
                    <?php echo $total_busca_clientes; ?> cliente<?php echo $total_busca_clientes != 1 ? 's' : ''; ?> cadastrado<?php echo $total_busca_clientes != 1 ? 's' : ''; ?>
                </div>
            </div>
            
            <div class="table-container">
                <?php if ($total_busca_clientes > 0) { ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
                        $id_cliente = $rows_clientes['id'];
                        $nome = $rows_clientes['nome'];
                        $telefone = $rows_clientes['telefone'];
                        $endereco = isset($rows_clientes['endereco']) ? $rows_clientes['endereco'] : 'Endereço não cadastrado';
                        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                        
                        // Formatar telefone para exibição
                        $telefone_exibicao = $telefone;
                        if (strpos($telefone, 'undefined') === 0) {
                            $telefone_exibicao = substr($telefone, 9);
                        }
                        
                        if (strlen($telefone_exibicao) > 10) {
                            $codigo_pais = substr($telefone_exibicao, 0, 2);
                            if ($codigo_pais == "55") {
                                $ddd = substr($telefone_exibicao, 2, 2);
                                $numero = substr($telefone_exibicao, 4);
                                if (strlen($numero) >= 9) {
                                    $parte1 = substr($numero, 0, 5);
                                    $parte2 = substr($numero, 5);
                                    $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                                }
                            } else {
                                $telefone_exibicao = "+" . $telefone_exibicao;
                            }
                        }
                        
                        // Limpar telefone para WhatsApp
                        $telefone_whatsapp = preg_replace('/[^0-9]/', '', $telefone);
                        if (substr($telefone_whatsapp, 0, 9) === "undefined") {
                            $telefone_whatsapp = substr($telefone_whatsapp, 9);
                        }
                    ?>
                        <tr>
                            <td>
                                <div class="client-name"><?php echo htmlspecialchars($nome); ?></div>
                            </td>
                            <td>
                                <div class="client-phone"><?php echo htmlspecialchars($telefone_limpo); ?></div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $telefone_whatsapp; ?>" 
                                       class="btn btn-sm btn-whatsapp" target="_blank" title="Enviar WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-edit" 
                                            onclick="editarCliente(<?php echo $id_cliente; ?>, '<?php echo addslashes($nome); ?>', '<?php echo addslashes($telefone); ?>', '<?php echo addslashes($endereco); ?>')" 
                                            title="Editar Cliente">
                                        <i class="material-icons">edit</i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" 
                                            onclick="confirmarExclusao(<?php echo $id_cliente; ?>, '<?php echo addslashes($nome); ?>')" 
                                            title="Excluir Cliente">
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
                    <div class="material-icons empty-icon">person_add</div>
                    <h3>Nenhum cliente encontrado</h3>
                    <p>Comece adicionando seu primeiro cliente!</p>
                    <button class="btn btn-success" onclick="openModal('addClienteModal')" style="margin-top: 20px;">
                        <i class="material-icons">person_add</i>
                        Adicionar Primeiro Cliente
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Cliente -->
    <div class="modal" id="addClienteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-success">
                <h5 class="modal-title">Adicionar Novo Cliente</h5>
                <button type="button" class="modal-close" onclick="closeModal('addClienteModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formAddCliente" action="processa_cliente.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="usuario_api" value="<?php echo $usuario_api; ?>">
                    <input type="hidden" name="id_cliente" value="<?php echo $id_cliente; ?>">
                    
                    <div class="form-group">
                        <label for="nome">Nome do Cliente</label>
                        <input type="text" class="form-control" id="nome" name="nome" required placeholder="Digite o nome completo">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" required placeholder="(11) 99999-9999">
                        <input type="hidden" id="codigo_pais" name="codigo_pais">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addClienteModal')">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons">save</i>
                        Salvar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Cliente -->
    <div class="modal" id="editClienteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Editar Cliente</h5>
                <button type="button" class="modal-close" onclick="closeModal('editClienteModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formEditCliente" action="processa_cliente.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id_cliente" id="edit_id_cliente">
                    <input type="hidden" name="usuario_api" value="<?php echo $usuario_api; ?>">
                    
                    <div class="form-group">
                        <label for="edit_nome">Nome do Cliente</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_telefone">Telefone</label>
                        <input type="tel" class="form-control" id="edit_telefone" name="telefone" required>
                        <input type="hidden" id="edit_codigo_pais" name="edit_codigo_pais">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editClienteModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">update</i>
                        Atualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <p>Tem certeza que deseja excluir o cliente <span id="clienteNome" style="font-weight: 600;"></span>?</p>
                    <p style="margin: 10px 0 0 0;"><strong>Esta ação não pode ser desfeita!</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancelar</button>
                <form id="deleteForm" action="processa_cliente.php" method="POST" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" id="deleteClienteId" name="id_cliente" value="">
                    <input type="hidden" name="usuario_api" value="<?php echo $usuario_api; ?>">
                    <button type="submit" class="btn btn-delete">
                        <i class="material-icons">delete_forever</i>
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
<script>
(function(){
  // Fecha todos os modais; se reloadPage for true, faz um reload no fim
  function fecharTodosModais(reloadPage = false) {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.classList.remove('show');
      modal.style.display = 'none';
    });
    document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
    document.body.style.overflow = 'auto';
    if (reloadPage) {
      // garante um único reload
      window.location.reload();
    }
  }

  document.addEventListener('click', e => {
    // botões de fechar ("X") e "Cancelar"
    if (e.target.closest('.modal-close') || e.target.closest('.modal .btn-secondary')) {
      e.preventDefault();
      fecharTodosModais(true);
    }
    // clique na máscara
    else if (e.target.classList.contains('modal')) {
      fecharTodosModais(false);
    }
  });

  // ao carregar, limpa qualquer resto de modal/backdrop sem recarregar
  window.addEventListener('load', () => fecharTodosModais(false));
})();
</script>

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

     // Em vez de $('#editClienteModal').modal('show');
function editarCliente(id, nome, telefone, endereco) {
    document.getElementById('edit_id_cliente').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_telefone').value = telefone.replace(/\D/g, '').replace(/^undefined/, '');
    document.getElementById('edit_endereco').value = endereco;
    openModal('editClienteModal');
}

// Em vez de $('#deleteModal').modal('show');
function confirmarExclusao(id, nome) {
    document.getElementById('deleteClienteId').value = id;
    document.getElementById('clienteNome').textContent = nome;
    openModal('deleteModal');
}


        // Animação de entrada dos elementos
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
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