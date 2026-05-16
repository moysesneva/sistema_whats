<?php
require_once __DIR__ . '/auth_guard.php';
// Variáveis PHP para personalizar a logo, favicon e a imagem de fundo
include 'conn.php';
include 'estilo.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title><?php echo $titulo; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Painel de cadastro do sistema Edita Código">
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
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="../files/assets/vendor/intl-tel-input/css/intlTelInput.css">
    <style>
        .auth-box {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.95);
        }
        .card-block {
            padding: 30px;
        }
        .btn-primary {
            box-shadow: 0 5px 10px rgba(0, 123, 255, 0.2);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 123, 255, 0.3);
        }
        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
            height: 45px;
        }
        .form-primary input:focus {
            border-color: #4099ff;
        }
        .iti {
            width: 100%;
        }
        .iti__country-list {
            max-height: 170px;
            z-index: 999;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .text-center img {
            margin-bottom: 20px;
        }
        .login-block {
            padding: 50px 0;
        }
        .txt-primary {
            color: #4099ff;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 8px;
        }
    </style>
</head>

<body class="fix-menu" style="background-image: url('<?php echo $background_image; ?>'); background-size: cover;">
    <!-- Início do pré-carregador -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <!-- Mais anéis podem ser adicionados -->
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
                    <form class="md-float-material form-material" action="validar_adm.php" method="post" data-submit-fn="validarSenha" id="cadastro-form">
                        <div class="text-center">
                            <img src="<?php echo $logo; ?>" alt="Logo" style="max-width: 200px;">
                        </div>
                        <!-- Campo oculto para enviar um título ou identificador da requisição -->
                        <input type="hidden" name="titulo" value="CRIAR_CONTA">

                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center txt-primary">Cadastrar</h3>
                                        <h5 class="text-center txt-primary">Conta administrativa</h5>
                                    </div>
                                </div>
                                <!-- Campo para Nome de Usuário -->
                                <div class="form-group form-primary">
                                    <label>Nome de Usuário</label>
                                    <input type="text" name="user-name" class="form-control" required placeholder="Escolha seu nome de usuário">
                                    <span class="form-bar"></span>
                                </div>

                                <!-- Campo para Telefone -->
                                <div class="form-group form-primary">
                                    <label>Telefone (será seu login)</label>
                                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                                    <span class="form-bar"></span>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <label>Senha</label>
                                            <input type="password" id="password" name="password" class="form-control" required placeholder="Crie sua senha">
                                            <span class="form-bar"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <label>Confirmação</label>
                                            <input type="password" id="confirm-password" name="confirm-password" class="form-control" required placeholder="Confirme sua senha">
                                            <span class="form-bar"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Cadastrar agora</button>
                                    </div>
                                </div>
                                
                                <hr>

                                <!-- Link para voltar ao site -->
                                <div class="row">
                                    <div class="col-md-10">
                                        <p class="text-inverse text-left"><a href="login.php"><b class="f-w-600">Fazer Login</b></a></p>
                                    </div>
                                    <div class="col-md-2">
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

    <!-- Scripts necessários -->
    <script type="text/javascript" src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <script type="text/javascript" src="../files/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="../files/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script type="text/javascript" src="../files/bower_components/i18next/js/i18next.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
    <script type="text/javascript" src="../files/assets/js/common-pages.js"></script>
    <!-- Intl Tel Input JS -->
    <script src="../files/assets/vendor/intl-tel-input/js/intlTelInput.min.js"></script>

    <!-- Script para formatação do telefone e validações -->
    <script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        $(document).ready(function() {
            // Inicializa o plugin IntlTelInput
            var input = document.querySelector("#telefone");
            var iti = window.intlTelInput(input, {
                initialCountry: "br",
                preferredCountries: ["br", "pt", "us", "gb", "es"],
                separateDialCode: true,
                utilsScript: "../files/assets/vendor/intl-tel-input/js/utils.js",
            });
            
            // Atualiza o campo oculto com o código do país quando muda o país
            input.addEventListener("countrychange", function() {
                var countryData = iti.getSelectedCountryData();
                document.querySelector("#codigo_pais").value = "+" + countryData.dialCode;
            });
            
            // Define o valor inicial do código do país
            var countryData = iti.getSelectedCountryData();
            document.querySelector("#codigo_pais").value = "+" + countryData.dialCode;
            
            // Animações e efeitos visuais
            $(".auth-box").addClass("animated fadeInDown");
            
            // Pequeno atraso para remover a classe de animação
            setTimeout(function() {
                $(".auth-box").removeClass("animated fadeInDown");
            }, 1000);
            
            // Modifica o submit do formulário para combinar código do país e telefone
            $("#cadastro-form").on("submit", function(e) {
                if (!validarSenha()) {
                    return false;
                }
                
                e.preventDefault();
                
                // Obter o código do país e o número de telefone
                var countryData = iti.getSelectedCountryData();
                var dialCode = countryData.dialCode; // sem o +
                var phoneNumber = input.value.replace(/\D/g, ''); // remove qualquer caractere não numérico
                
                // Combinar o código do país com o número de telefone (sem o +)
                var fullPhoneNumber = dialCode + phoneNumber;
                
                // Atualizar o valor do campo de telefone
                $("#telefone").val(fullPhoneNumber);
                
                // Remover o campo oculto código do país para não enviá-lo
                $("#codigo_pais").remove();
                
                // Enviar o formulário
                this.submit();
            });
        });

        // Validação de senhas iguais
        function validarSenha() {
            var senha = document.getElementById("password").value;
            var confirmarSenha = document.getElementById("confirm-password").value;
            if (senha != confirmarSenha) {
                alert("As senhas não coincidem. Tente novamente.");
                return false;
            }
            return true;
        }
    </script>
</body>
<?php

include 'erro.php';

?>
</html>
