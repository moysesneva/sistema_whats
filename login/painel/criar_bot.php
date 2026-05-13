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


include 'bloqueio.php';


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
    <!-- Adicionar CSS do intl-tel-input -->
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


<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="feather icon-user-plus mr-2"></i>Criar Bot</h2>
        </div>
        <div class="card-body">
            <form action="criar_bot_confirma.php" method="POST">
                <div class="form-group mb-4">
                    <label for="nome_cliente" class="font-weight-bold"><i class="feather icon-user mr-1"></i>Nome do Cliente:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" placeholder="Digite o nome do cliente" required>
                    </div>
                    <small class="form-text text-muted">
                        <i class="feather icon-info mr-1"></i>Insira o nome completo do cliente.
                    </small>
                </div>

                <div class="form-group mb-4">
                    <label for="telefone_cliente" class="font-weight-bold"><i class="feather icon-phone mr-1"></i>Telefone (será o login):</label>
                    <input type="tel" class="form-control" id="telefone_cliente" name="telefone_cliente" required>
                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                    <small class="form-text text-muted">
                        <i class="feather icon-info mr-1"></i>Este número será utilizado como o login do cliente.
                    </small>
                </div>

                <div class="form-group mb-4">
                    <label for="email_cliente" class="font-weight-bold"><i class="feather icon-mail mr-1"></i>Email do Cliente:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-mail"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" placeholder="exemplo@dominio.com" required>
                    </div>
                    <div class="alert alert-info mt-2" style="font-weight: bold; color: #0056b3; background-color: #eaf4ff; border: 1px solid #007bff;">
                        <i class="feather icon-alert-circle mr-1"></i> Este email será utilizado como identificador único para o pagamento. Insira um email válido no formato exemplo@dominio.com.
                    </div>
                </div>
<div class="form-group mb-4">
  <label for="creditos" class="font-weight-bold">
    <i class="feather icon-dollar-sign mr-1"></i>Créditos de IA:
  </label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="feather icon-dollar-sign"></i>
      </span>
    </div>
    <input
      type="number"
      class="form-control"
      id="creditos"
      name="creditos"
      min="0"
      step="1"
      placeholder="Insira o número de créditos"
    >
  </div>
  <small class="form-text text-muted">
    <i class="feather icon-alert-triangle mr-1"></i>Informe quantos créditos devem ser disponibilizados.
  </small>
</div>





<div class="form-group mb-4">
  <label for="plano" class="font-weight-bold">
    <i class="feather icon-layers mr-1"></i>Plano:
  </label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="feather icon-layers"></i>
      </span>
    </div>
    <select class="form-control" id="plano" name="plano">
      <option value="">Selecione o plano</option>
      <option value="plano1">Plano 1</option>
      <option value="plano2">Plano 2</option>
      <option value="plano3">Plano 3</option>
    </select>
  </div>
  <small class="form-text text-muted">
    <i class="feather icon-alert-triangle mr-1"></i>Escolha um dos três planos disponíveis.
  </small>
</div>










                <div class="form-group mb-4">
                    <label for="senha_padrao" class="font-weight-bold"><i class="feather icon-lock mr-1"></i>Senha Padrão:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-lock"></i></span>
                        </div>
                        <input type="text" class="form-control" id="senha_padrao" name="senha_padrao" value="123456" readonly>
                    </div>
                    <small class="form-text text-muted">
                        <i class="feather icon-alert-triangle mr-1"></i>Ao criar o bot, a senha padrão é <strong>123456</strong>. Por favor, altere-a posteriormente para garantir a segurança.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block mt-4 shadow-sm">
                    <i class="feather icon-check-circle mr-2"></i>Criar Bot
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        padding: 15px 20px;
    }
    .form-control {
        border-radius: 5px;
        padding: 10px 15px;
        height: auto;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        border-color: #80bdff;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-radius: 5px 0 0 5px;
    }
    .btn-primary {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3) !important;
    }
    label {
        margin-bottom: 8px;
    }
    .iti {
        width: 100%;
    }
    /* Correção para o dropdown do telefone */
    .iti__country-list {
        position: absolute;
        z-index: 9999;
        max-height: 200px;
    }
    /* Garantir espaço entre campos para o dropdown */
    .form-group {
        margin-bottom: 35px;
    }
    /* Ajustes para o posicionamento do telefone */
    .iti--separate-dial-code .iti__selected-flag {
        background-color: #f8f9fa;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Adicionar JS do intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        var input = document.querySelector("#telefone_cliente");
        var iti = window.intlTelInput(input, {
            initialCountry: "br",
            preferredCountries: ["br", "pt", "us", "gb", "es"],
            separateDialCode: true,
            dropdownContainer: document.body,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Remover o evento de input que estava formatando o telefone
        $('#telefone_cliente').off('input');

        // Impedir a inserção de caracteres não numéricos no campo de telefone
        $('#telefone_cliente').on('input', function(e) {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);
        });

        // Modificar o comportamento do formulário no envio
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // Obter o código do país (sem o +) e o número de telefone limpo
            var countryData = iti.getSelectedCountryData();
            var dialCode = countryData.dialCode; // sem o +
            var phoneNumber = input.value.replace(/\D/g, ''); // remove qualquer caractere não numérico
            
            // Combinar o código do país com o número de telefone para criar um número único
            var fullPhoneNumber = dialCode + phoneNumber;
            
            // Atualizar o valor do campo de telefone
            $("#telefone_cliente").val(fullPhoneNumber);
            
            // Remover o campo oculto código do país para não enviá-lo
            $("#codigo_pais").remove();
            
            // Enviar o formulário
            this.submit();
        });
    });
</script>






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

  gtag('config', '<?=$google;?>');
</script>
</body>




<?php

include 'pcoded.php';
include'erro.php';

?>