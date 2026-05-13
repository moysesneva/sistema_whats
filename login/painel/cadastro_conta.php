<?php
// Variáveis PHP para personalizar a logo, favicon e a imagem de fundo
include 'conn.php';
include 'estilo.php';

// Definição do tema de cores (1 a 6)
$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
            $tema    = $rows_config['tema'];

}
// Definição das cores de acordo com o tema selecionado
switch ($tema) {
    case 1: // Roxo e Azul (Default)
        $primary_color = '#3a0ca3';
        $secondary_color = '#4cc9f0';
        $accent_color = '#f72585';
        $dark_color = '#2b2d42';
        $light_color = '#f8f9fa';
        $gradient = 'linear-gradient(120deg, #7209b7, #3a0ca3)';
        $gradient_hover = 'linear-gradient(120deg, #3a0ca3, #7209b7)';
        break;
    case 2: // Verde e Aqua
        $primary_color = '#06d6a0';
        $secondary_color = '#1b9aaa';
        $accent_color = '#ff9f1c';
        $dark_color = '#1d3557';
        $light_color = '#f1faee';
        $gradient = 'linear-gradient(120deg, #06d6a0, #1b9aaa)';
        $gradient_hover = 'linear-gradient(120deg, #1b9aaa, #06d6a0)';
        break;
    case 3: // Vermelho e Laranja
        $primary_color = '#e63946';
        $secondary_color = '#f77f00';
        $accent_color = '#fcbf49';
        $dark_color = '#003049';
        $light_color = '#f1faee';
        $gradient = 'linear-gradient(120deg, #e63946, #f77f00)';
        $gradient_hover = 'linear-gradient(120deg, #f77f00, #e63946)';
        break;
    case 4: // Azul Escuro e Ciano
        $primary_color = '#003459';
        $secondary_color = '#00a8e8';
        $accent_color = '#ff6b6b';
        $dark_color = '#00171f';
        $light_color = '#f5f5f5';
        $gradient = 'linear-gradient(120deg, #003459, #00a8e8)';
        $gradient_hover = 'linear-gradient(120deg, #00a8e8, #003459)';
        break;
    case 5: // Roxo e Rosa
        $primary_color = '#9b5de5';
        $secondary_color = '#f15bb5';
        $accent_color = '#fee440';
        $dark_color = '#1b1b1b';
        $light_color = '#f8f9fa';
        $gradient = 'linear-gradient(120deg, #9b5de5, #f15bb5)';
        $gradient_hover = 'linear-gradient(120deg, #f15bb5, #9b5de5)';
        break;
    case 6: // Cinza e Amarelo
        $primary_color = '#2b2d42';
        $secondary_color = '#ffd166';
        $accent_color = '#ef476f';
        $dark_color = '#191923';
        $light_color = '#edf2f4';
        $gradient = 'linear-gradient(120deg, #2b2d42, #1a1a2e)';
        $gradient_hover = 'linear-gradient(120deg, #1a1a2e, #2b2d42)';
        break;
    default: // Roxo e Azul (Default)
        $primary_color = '#3a0ca3';
        $secondary_color = '#4cc9f0';
        $accent_color = '#f72585';
        $dark_color = '#2b2d42';
        $light_color = '#f8f9fa';
        $gradient = 'linear-gradient(120deg, #7209b7, #3a0ca3)';
        $gradient_hover = 'linear-gradient(120deg, #3a0ca3, #7209b7)';
}
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Framework Bootstrap -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- Ícones -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\themify-icons\themify-icons.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\icofont\css\icofont.css">
    <!-- Estilo CSS -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <!-- AOS CSS - Animações ao rolar -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
            --dark-color: <?php echo $dark_color; ?>;
            --light-color: <?php echo $light_color; ?>;
            --white: #ffffff;
            --gradient: <?php echo $gradient; ?>;
            --gradient-hover: <?php echo $gradient_hover; ?>;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-color);
            overflow-x: hidden;
        }
        
        .login-block {
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            min-height: 100vh;
            position: relative;
            padding: 50px 0;
        }
        
        .login-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0.03;
            z-index: -1;
        }
        
        .auth-box {
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: var(--white);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .auth-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        .card-block {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            height: 50px;
            font-size: 14px;
            border: 1px solid rgba(0,0,0,0.1);
            background-color: rgba(0,0,0,0.01);
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(58, 12, 163, 0.1);
            background-color: var(--white);
        }
        
        .form-group label {
            color: #666 !important;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }
        
        .btn-primary {
            background: var(--gradient);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.5s ease;
            z-index: -1;
        }
        
        .btn-primary:hover::before {
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }
        
        .iti {
            width: 100%;
        }
        
        .iti__country-list {
            max-height: 170px;
            z-index: 999;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .text-center img {
            max-width: 180px;
            margin-bottom: 30px;
            filter: drop-shadow(0px 5px 10px rgba(0,0,0,0.1));
        }
        
        .txt-primary {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .text-inverse a {
            color: var(--primary-color);
            font-weight: 600;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .text-inverse a:hover {
            color: var(--secondary-color);
        }
        
        .text-inverse b {
            color: var(--primary-color);
            transition: var(--transition);
        }
        
        .text-inverse a:hover b {
            color: var(--secondary-color);
        }
        
        /* Loader personalizado */
        .theme-loader {
            background: rgba(255, 255, 255, 0.96);
        }
        
        .theme-loader .ball-scale .contain .ring {
            border-color: var(--primary-color);
        }
        
        /* Elementos decorativos */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: var(--gradient);
            opacity: 0.05;
            z-index: 0;
        }
        
        .decoration-circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
        }
        
        .decoration-circle-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -100px;
        }
        
        .small-logo {
            max-width: 40px !important;
            margin-bottom: 0 !important;
            opacity: 0.8;
        }
        
        @media (max-width: 767px) {
            .card-block {
                padding: 30px 20px;
            }
            
            .form-control {
                height: 45px;
            }
            
            .btn-primary {
                padding: 10px 25px;
                font-size: 14px;
            }
            
            .login-block {
                padding: 30px 0;
            }
        }
    </style>
</head>

<body class="fix-menu">
    <!-- Início do pré-carregador -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    <!-- Fim do pré-carregador -->

    <section class="login-block">
        <!-- Elementos decorativos -->
        <div class="decoration-circle decoration-circle-1"></div>
        <div class="decoration-circle decoration-circle-2"></div>
        
        <!-- Início do Container -->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Início do formulário de autenticação -->
                    <form class="md-float-material form-material" action="validar.php" method="post" onsubmit="return validarSenha()" id="cadastro-form" data-aos="fade-up" data-aos-duration="1000">
                        <div class="text-center">
                            <img src="<?php echo $logo; ?>" alt="Logo" data-aos="fade-up" data-aos-delay="200">
                        </div>
                        <!-- Campo oculto para enviar um título ou identificador da requisição -->
                        <input type="hidden" name="titulo" value="CRIAR_CONTA">

                        <div class="auth-box card" data-aos="fade-up" data-aos-delay="400">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center txt-primary">Cadastro</h3>
                                    </div>
                                </div>
                                <!-- Campo para Nome de Usuário -->
                                <div class="form-group form-primary">
                                    <label>Nome de Usuário</label>
                                    <input type="text" name="user-name" class="form-control" required placeholder="Escolha seu nome de usuário">
                                </div>

                                <!-- Campo para Telefone -->
                                <div class="form-group form-primary">
                                    <label>Telefone (será seu login)</label>
                                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <label>Senha</label>
                                            <input type="password" id="password" name="password" class="form-control" required placeholder="Crie sua senha">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <label>Confirmação</label>
                                            <input type="password" id="confirm-password" name="confirm-password" class="form-control" required placeholder="Confirme sua senha">
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
                                        <img src="<?php echo $small_logo; ?>" alt="Pequena logo" class="small-logo">
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
    <script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\css-scrollbars.js"></script>
    <script type="text/javascript" src="..\files\bower_components\i18next\js\i18next.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\common-pages.js"></script>
    <!-- Intl Tel Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <!-- AOS JS - Animações ao rolar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Script para formatação do telefone e validações -->
    <script>
        $(document).ready(function() {
            // Inicializa AOS (Animate On Scroll)
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
                delay: 100
            });
            
            // Inicializa o plugin IntlTelInput
            var input = document.querySelector("#telefone");
            var iti = window.intlTelInput(input, {
                initialCountry: "br",
                preferredCountries: ["br", "pt", "us", "gb", "es"],
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });
            
            // Atualiza o campo oculto com o código do país quando muda o país
            input.addEventListener("countrychange", function() {
                var countryData = iti.getSelectedCountryData();
                document.querySelector("#codigo_pais").value = "+" + countryData.dialCode;
            });
            
            // Define o valor inicial do código do país
            var countryData = iti.getSelectedCountryData();
            document.querySelector("#codigo_pais").value = "+" + countryData.dialCode;
            
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
    
    <?php include 'erro.php'; ?>
</body>
</html>