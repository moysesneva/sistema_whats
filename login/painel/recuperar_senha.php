<?php
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
    <link href="../files/assets/vendor/fonts/montserrat/montserrat.css" rel="stylesheet">
    <!-- Framework Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <!-- Ícones -->
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/icofont/css/icofont.css">
    <!-- Estilo CSS -->
    <link rel="stylesheet" type="text/css" href="../files/assets/css/style.css">
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="../files/assets/vendor/intl-tel-input/css/intlTelInput.css">
    <!-- AOS CSS - Animações ao rolar -->
    <link href="../files/assets/vendor/aos/aos.css" rel="stylesheet">
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
            padding: 30px 0;
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
        
        label.text-muted {
            color: #666 !important;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
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
        
        .text-left {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .info-text {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .f-w-600 {
            font-weight: 600;
        }
        
        .text-right a.text-primary {
            color: var(--primary-color) !important;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .text-right a.text-primary:hover {
            color: var(--secondary-color) !important;
            text-decoration: none;
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
                    <!-- Início do formulário de recuperação -->
                    <form class="md-float-material form-material" action="validar.php" method="post" id="recovery-form" data-aos="fade-up" data-aos-duration="1000">
                        <!-- Campo oculto para enviar um título ou identificador da requisição -->
                        <input type="hidden" name="titulo" value="RECUPRAR_SENHA">
                        <div class="text-center">
                            <!-- Exibe a logo dinamicamente a partir da variável PHP -->
                            <img src="<?php echo $logo; ?>" alt="Logo" data-aos="fade-up" data-aos-delay="200">
                        </div>
                        <div class="auth-box card" data-aos="fade-up" data-aos-delay="400">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-left">Recuperar sua senha</h3>
                                        <p class="info-text">Informe seu número de telefone para receber as instruções de recuperação de senha.</p>
                                    </div>
                                </div>
                                
                                <!-- Campo para telefone com seletor de país -->
                                <div class="form-group form-primary">
                                    <label class="text-muted mb-2">Telefone</label>
                                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">
                                            <i class="icofont icofont-lock mr-2"></i>Redefinir Senha
                                        </button>
                                    </div>
                                </div>
                                <p class="f-w-600 text-right">
                                    <i class="icofont icofont-long-arrow-left mr-1"></i>
                                    <a href="login.php" class="text-primary">Voltar para Login</a>
                                </p>
                                <div class="row">
                                    <div class="col-md-10">
                                       
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Exibe a logo pequena dinamicamente -->
                                        <img src="<?php echo $small_logo; ?>" alt="Pequena logo" class="small-logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Fim do formulário de recuperação -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <!-- AOS JS - Animações ao rolar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Script para inicializar o seletor de país e animações -->
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
            $("#recovery-form").on("submit", function(e) {
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
    </script>
</body>
</html>