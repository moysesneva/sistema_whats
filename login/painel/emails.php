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

if($tipo != 1){
    VaiPara('login.php');
}


?>

<?php
// Processar formulário
if(isset($_POST['salvar'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha_app = mysqli_real_escape_string($conn, $_POST['senha_app']);
    $smtp_host = mysqli_real_escape_string($conn, $_POST['smtp_host']);
    $smtp_port = (int)$_POST['smtp_port'];
    $smtp_secure = mysqli_real_escape_string($conn, $_POST['smtp_secure']);
    
    // Verifica se já existe configuração
    $sql_check = "SELECT id FROM email_config WHERE login = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $login);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if(mysqli_num_rows($result_check) > 0) {
        // Atualiza configuração existente
        $sql = "UPDATE email_config 
                SET email = ?, senha_app = ?, smtp_host = ?, smtp_port = ?, smtp_secure = ? 
                WHERE login = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssiss", 
            $email, $senha_app, $smtp_host, $smtp_port, $smtp_secure, $login);
    } else {
        // Insere nova configuração
        $sql = "INSERT INTO email_config 
                (login, email, senha_app, smtp_host, smtp_port, smtp_secure) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssis", 
            $login, $email, $senha_app, $smtp_host, $smtp_port, $smtp_secure);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        $mensagem = "Configurações de email salvas com sucesso!";
        $tipo_mensagem = "success";
    } else {
        $mensagem = "Erro ao salvar configurações de email.";
        $tipo_mensagem = "danger";
    }
}

// Função para testar as configurações de email
if(isset($_POST['testar'])) {
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    $sql_config = "SELECT * FROM email_config WHERE login = ?";
    $stmt_config = mysqli_prepare($conn, $sql_config);
    mysqli_stmt_bind_param($stmt_config, "s", $login);
    mysqli_stmt_execute($stmt_config);
    $result_config = mysqli_stmt_get_result($stmt_config);
    $config = mysqli_fetch_assoc($result_config);

    if($config) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['email'];
            $mail->Password = $config['senha_app'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config['email'], 'Teste de Configuração');
            $mail->addAddress($config['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Teste de Configuração de Email';
            $mail->Body = 'Se você recebeu este email, suas configurações de SMTP estão funcionando corretamente!';

            $mail->send();
            $mensagem = "Email de teste enviado com sucesso! Verifique sua caixa de entrada.";
            $tipo_mensagem = "success";
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar email de teste: " . $mail->ErrorInfo;
            $tipo_mensagem = "danger";
        }
    }
}

// Buscar configuração atual
$sql_config = "SELECT * FROM email_config WHERE login = ?";
$stmt_config = mysqli_prepare($conn, $sql_config);
mysqli_stmt_bind_param($stmt_config, "s", $login);
mysqli_stmt_execute($stmt_config);
$result_config = mysqli_stmt_get_result($stmt_config);
$config = mysqli_fetch_assoc($result_config);
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
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="page-header">
                                        <div class="row align-items-end">
                                            <div class="col-lg-8">
                                                <div class="page-header-title">
                                                    <div class="d-inline">
                                                        <h4>Configuração de Email</h4>
                                                        <span>Configure seu email para receber notificações do sistema</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="page-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>Configurações do Servidor SMTP</h5>
                                                        <span>Preencha os dados do seu servidor de email</span>
                                                    </div>
                                                    <div class="card-block">
                                                        <?php if(isset($mensagem)): ?>
                                                        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show">
                                                            <?php echo $mensagem; ?>
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <?php endif; ?>

                                                        <form method="post" class="form-material">
                                                            <div class="form-group form-default">
                                                                <input type="email" name="email" class="form-control" required value="<?php echo $config['email'] ?? ''; ?>">
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Email</label>
                                                                <small class="text-muted">Use seu email do Gmail</small>
                                                            </div>

                                                            <div class="form-group form-default">
                                                                <input type="password" name="senha_app" class="form-control" required>
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Senha de App</label>
                                                                <small class="text-muted">
                                                                    Use uma senha de app do Gmail. 
                                                                    <a href="https://support.google.com/accounts/answer/185833" target="_blank">
                                                                        Como criar uma senha de app?
                                                                    </a>
                                                                </small>
                                                            </div>

                                                            <div class="form-group form-default">
                                                                <input type="text" name="smtp_host" class="form-control" required value="<?php echo $config['smtp_host'] ?? 'smtp.gmail.com'; ?>">
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Servidor SMTP</label>
                                                            </div>

                                                               <div class="row">
    <div class="col-sm-6">
        <div class="form-group form-default">
            <input type="number" name="smtp_port" class="form-control" required 
                   value="<?php echo $config['smtp_port'] ?? '587'; ?>">
            <span class="form-bar"></span>
            <label class="float-label">Porta SMTP</label>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group form-default">
            <select name="smtp_secure" class="form-control" required>
                <option value="tls" <?php echo ($config['smtp_secure'] ?? 'tls') == 'tls' ? 'selected' : ''; ?>>
                    TLS
                </option>
                <option value="ssl" <?php echo ($config['smtp_secure'] ?? '') == 'ssl' ? 'selected' : ''; ?>>
                    SSL
                </option>
            </select>
            <span class="form-bar"></span>
            <label class="float-label">Segurança</label>
        </div>
    </div>
</div>


                                                            <div class="form-group">
                                                                <button type="submit" name="salvar" class="btn btn-primary btn-round waves-effect waves-light">
                                                                    <i class="feather icon-save"></i> Salvar Configurações
                                                                </button>

                                                                <?php if($config): ?>
                                                               
                                                                <button type="button" class="btn btn-warning btn-round waves-effect waves-light" data-toggle="modal" data-target="#editConfigModal">
                                                                   <i class="feather icon-edit"></i> Editar Configurações
                                                                </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </form>
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

<!-- Modal de Edição -->
<div class="modal fade" id="editConfigModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Configurações de Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo $config['email'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Senha de App</label>
                        <input type="password" name="senha_app" class="form-control" required>
                        <small class="text-muted">Digite a nova senha de app ou mantenha em branco para não alterar</small>
                    </div>
                    <div class="form-group">
                        <label>Servidor SMTP</label>
                        <input type="text" name="smtp_host" class="form-control" required value="<?php echo $config['smtp_host'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Porta SMTP</label>
                        <input type="number" name="smtp_port" class="form-control" required value="<?php echo $config['smtp_port'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Segurança</label>
                        <select name="smtp_secure" class="form-control" required>
                            <option value="tls" <?php echo ($config['smtp_secure'] ?? '') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo ($config['smtp_secure'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" name="salvar" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>