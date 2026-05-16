<?php
include 'conn.php';
include 'estilo.php';
#include 'conn.php';
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Painel Administrativo do Edita Código">
    <meta name="keywords" content="Admin, Responsivo, Painel, Bootstrap, Template, Mobile, Web">
    <meta name="author" content="Edita Código">
    <!-- Ícone Favicon -->
    <link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon">
    <!-- Fonte Google -->
    <link href="../files/assets/vendor/fonts/open-sans/open-sans.css" rel="stylesheet">
    <!-- Framework Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <!-- Ícones -->
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/icofont/css/icofont.css">
    <!-- Estilo CSS -->
    <link rel="stylesheet" type="text/css" href="../files/assets/css/style.css">
</head>

<body class="fix-menu">
    <!-- Início do pré-carregador -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <!-- Mais anéis para o efeito -->
            </div>
        </div>
    </div>
    <!-- Fim do pré-carregador -->

    <section class="login-block">
        <!-- Início do Container -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Início do formulário de autenticação -->
               <form class="md-float-material form-material" action="validar.php" method="post">
                   <!-- Campo oculto para enviar um título ou identificador da requisição -->
    <input type="hidden" name="titulo" value="DESBLOQUEAR">

                        <div class="text-center">
                            <!-- Exibe a logo dinamicamente a partir da variável PHP -->
                            <img src="<?php echo $logo; ?>" alt="Logo" style="max-width: 200px;">
                        </div>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center">Para desbloquear digite o código enviado para o seu telefone.</h3>
                                    </div>
                                </div>
                                
                                <!-- Alteração: Campo para digitar o telefone no formato brasileiro -->
                                <div class="form-group form-primary">
                                    <input type="text"  id="telefone" name="codigo" class="form-control" required="" placeholder="Digite o código AQUI">
                                    <span class="form-bar"></span>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Confirmar Código</button>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
        <!-- Botão Reenviar Código -->
        <button type="button" class="btn btn-warning btn-md btn-block waves-effect text-center m-b-20" data-fn="redirecionarParaCodigo">Reenviar Código</button>
    </div>
                                <p class="f-w-600 text-right">Voltar para <a href="login.php">Login.</a></p>
                                <div class="row">
                                    <div class="col-md-10">
                                       
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Exibe a logo pequena dinamicamente -->
                                        <img src="<?php echo $small_logo; ?>" alt="Pequena logo" style="max-width: 50px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Fim do formulário de autenticação -->
                </div>
            </div>
        </div>
        <!-- Fim do Container -->
    </section>
<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    function redirecionarParaCodigo() {
        window.location.href = 'codigo.php';
    }
</script>
    <!-- Seção de aviso para navegadores antigos -->
    <!--[if lt IE 10]>
    <div class="ie-warning">
        <h1>Aviso!</h1>
        <p>Você está utilizando uma versão desatualizada do Internet Explorer. Por favor, atualize para um navegador moderno.</p>
        <div class="iew-container">
            <ul class="iew-download">
                <li><a href="http://www.google.com/chrome/"><img src="../files/assets/images/browser/chrome.png" alt="Chrome"> <div>Chrome</div></a></li>
                <li><a href="https://www.mozilla.org/en-US/firefox/new/"><img src="../files/assets/images/browser/firefox.png" alt="Firefox"> <div>Firefox</div></a></li>
                <li><a href="http://www.opera.com"><img src="../files/assets/images/browser/opera.png" alt="Opera"> <div>Opera</div></a></li>
                <li><a href="https://www.apple.com/safari/"><img src="../files/assets/images/browser/safari.png" alt="Safari"> <div>Safari</div></a></li>
                <li><a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie"><img src="../files/assets/images/browser/ie.png" alt="Internet Explorer"> <div>IE (9 e acima)</div></a></li>
            </ul>
        </div>
        <p>Desculpe o transtorno!</p>
    </div>
    <![endif]-->

    <!-- Scripts necessários -->
    <script type="text/javascript" src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Slimscroll js -->
    <script type="text/javascript" src="../files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <!-- Modernizr js -->
    <script type="text/javascript" src="../files/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="../files/bower_components/modernizr/js/css-scrollbars.js"></script>
    <!-- i18next js -->
    <script type="text/javascript" src="../files/bower_components/i18next/js/i18next.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
    <script type="text/javascript" src="../files/assets/js/common-pages.js"></script>


    <!-- Script para formatar o telefone no estilo brasileiro -->
   

</body>
<?php
include 'erro.php';
?>
</html>
