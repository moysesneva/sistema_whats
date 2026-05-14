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

#include 'bloqueio.php';


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
    #VaiPara('login.php');
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


  <div class="container">
    <h2>Criar Novo Menu</h2>

    <!-- Formulário para criar um novo menu -->
    <form action="inserir_menu.php" method="POST">
        
        <!-- Campo para Nome do Menu -->
        <div class="form-group">
            <label for="menu">Nome do Menu:</label>
            <input type="text" class="form-control" id="menu" name="menu" placeholder="Digite o nome do menu" required>
            <small class="form-text text-muted">
                Insira o nome que aparecerá no menu.
            </small>
        </div>

        <!-- Campo para a Página do Menu -->
        <div class="form-group">
            <label for="menu_pagina">Página do Menu:</label>
            <input type="text" class="form-control" id="menu_pagina" name="menu_pagina" placeholder="Digite o caminho do arquivo ex: config_adm.php" required>
            <small class="form-text text-muted">
                Insira o caminho do arquivo que será carregado ao clicar no menu (ex: config_adm.php).
            </small>
        </div>

        <!-- Campo para Tipo de Acesso -->
        <div class="form-group">
            <label for="tipo">Tipo de Acesso:</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="" selected>Selecione o Tipo de Acesso</option>
                <option value="1">Administrador</option>
                <option value="2">Usuário</option>
                 <option value="5">Profissional</option>
                <option value="3">Inadimplente</option>
            </select>
            <small class="form-text text-muted">
                Selecione o tipo de acesso que terá permissão para ver o menu.
            </small>
        </div>

        <!-- Campo para Ordem do Menu -->
        <div class="form-group">
            <label for="ordem">Ordem do Menu:</label>
            <input type="text" class="form-control" id="ordem" name="ordem" placeholder="Digite a ordem do menu (ex: 1, 2, 3)" required>
            <small class="form-text text-muted">
                Defina a ordem de exibição no menu (ex: 1, 2, 3).
            </small>
        </div>

        <!-- Campo para Ícone do Menu -->
        <div class="form-group">
            <label for="icone_menu">Ícone do Menu:</label>
            <select class="form-control" id="icone_menu" name="icone_menu" required>
    <option value="" selected>Selecione um Ícone</option>
    <option value="feather icon-shield">Escudo (Segurança)</option>
    <option value="fa fa-key">Chave</option>
    <option value="fa fa-qrcode">QR Code</option>
    <option value="feather icon-plus-circle">Círculo com Sinal de Mais</option>
    <option value="feather icon-list">Lista</option>
    <option value="feather icon-lock">Cadeado</option>
    <option value="feather icon-log-out">Sair</option>
    <option value="feather icon-menu">Menu</option>
    <option value="feather icon-settings">Configurações</option>
    <option value="feather icon-home">Casa (Início)</option>
    <option value="fa fa-user">Usuário</option>
    <option value="fa fa-users">Usuários (Múltiplos)</option>
    <option value="fa fa-envelope">Envelope (Mensagens)</option>
    <option value="fa fa-bell">Sino (Notificações)</option>
    <option value="fa fa-cog">Engrenagem (Configurações Gerais)</option>
    <option value="feather icon-search">Lupa (Busca)</option>
    <option value="fa fa-calendar">Calendário</option>
    <option value="fa fa-dashboard">Painel de Controle (Dashboard)</option>
    <option value="fa fa-file">Arquivo</option>
    <option value="fa fa-folder">Pasta</option>
    <option value="feather icon-edit">Editar</option>
    <option value="fa fa-trash">Lixeira (Excluir)</option>
    <option value="fa fa-download">Baixar</option>
    <option value="fa fa-upload">Carregar</option>
    <option value="feather icon-bar-chart">Gráfico</option>
    <option value="fa fa-credit-card">Cartão de Crédito (Pagamento)</option>
    <option value="fa fa-money">Dinheiro</option>
    <option value="fa fa-shopping-cart">Carrinho de Compras</option>
    <option value="fa fa-lock">Cadeado (Proteção)</option>
</select>

            <small class="form-text text-muted">
                Escolha o ícone que será exibido ao lado do nome do menu.
            </small>
        </div>

        <!-- Botão para enviar o formulário -->
        <button type="submit" class="btn btn-primary">Criar Menu</button>
    </form>
</div>




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