<?php
include 'conn.php';
include 'estilo.php';
include 'funcoes.php';

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);
while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $tema = $rows_config['tema'];
}

$sql_busca_usuario = "SELECT * FROM login";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if($total_busca_usuario == '0'){
    VaiPara('cadastro_adm.php');
} else {

$css_extra = '
    <link rel="stylesheet" type="text/css" href="../files/assets/icon/themify-icons/themify-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: \'Montserrat\', sans-serif;
            background: #000d1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: \'\';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(135deg, #001f3f 0%, #000d1a 50%, #001229 100%);
            z-index: 0;
        }

        .bg-hex {
            position: fixed;
            z-index: 0;
            opacity: 0.06;
        }
        .bg-hex-1 {
            width: 500px; height: 500px;
            top: -200px; left: -200px;
            background: #FF5500;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
        }
        .bg-hex-2 {
            width: 350px; height: 350px;
            bottom: -120px; right: -100px;
            background: #FF5500;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
        }
        .bg-hex-3 {
            width: 200px; height: 200px;
            top: 60%; left: 5%;
            background: #0057a8;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
            opacity: 0.08;
        }
        .bg-line {
            position: fixed;
            z-index: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,85,0,0.15), transparent);
            top: 50%;
        }

        .theme-loader {
            position: fixed;
            inset: 0;
            background: #000d1a;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .theme-loader .ball-scale { text-align: center; }
        .theme-loader .ball-scale .contain { display: inline-block; }
        .theme-loader .ball-scale .contain .ring {
            width: 50px; height: 50px;
            border-radius: 50%;
            border: 3px solid #FF5500;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
            display: block;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo img {
            height: 48px;
            width: auto;
            filter: drop-shadow(0 0 20px rgba(255,85,0,0.4));
        }

        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(20px);
            box-shadow:
                0 25px 60px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.05);
        }

        .auth-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            margin-bottom: 32px;
            letter-spacing: 0.5px;
        }
        .auth-card h3 span { color: #FF5500; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .form-group { margin-bottom: 22px; }

        .form-control {
            width: 100%;
            height: 50px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #ffffff;
            font-family: \'Montserrat\', sans-serif;
            font-size: 14px;
            padding: 0 16px;
            transition: all 0.3s ease;
            outline: none;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.25); }
        .form-control:focus {
            border-color: #FF5500;
            background: rgba(255,85,0,0.06);
            box-shadow: 0 0 0 3px rgba(255,85,0,0.12);
        }

        .iti { width: 100%; }
        .iti__selected-flag {
            background: rgba(255,255,255,0.05) !important;
            border-right: 1px solid rgba(255,255,255,0.1) !important;
        }
        .iti__flag-container:hover .iti__selected-flag {
            background: rgba(255,85,0,0.1) !important;
        }
        .iti__country-list {
            background: #001f3f;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            color: #fff;
            max-height: 200px;
        }
        .iti__country.iti__highlight { background: rgba(255,85,0,0.15); }
        .iti__country-name, .iti__dial-code { color: rgba(255,255,255,0.8); }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            font-weight: 600;
            color: #FF5500;
            text-decoration: none;
            margin-top: -12px;
            margin-bottom: 24px;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #ff7733; text-decoration: none; }

        .btn-enam {
            display: block;
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #FF5500, #e64a00);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-family: \'Montserrat\', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255,85,0,0.35);
        }
        .btn-enam:hover {
            background: linear-gradient(135deg, #ff6620, #FF5500);
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(255,85,0,0.5);
        }
        .btn-enam:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 20px;
        }
        .divider::before, .divider::after {
            content: \'\';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }
        .divider span {
            font-size: 11px;
            color: rgba(255,255,255,0.25);
            font-weight: 500;
            white-space: nowrap;
        }

        .signup-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .signup-row p {
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            margin: 0;
        }
        .signup-row a {
            font-weight: 700;
            color: #FF5500;
            text-decoration: none;
            transition: color 0.2s;
        }
        .signup-row a:hover { color: #ff7733; }
        .signup-row .mini-logo {
            height: 32px;
            width: auto;
            opacity: 0.5;
        }

        .auth-card::before {
            content: \'\';
            display: block;
            width: 60px;
            height: 3px;
            background: #FF5500;
            border-radius: 2px;
            margin: 0 auto 28px;
        }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 22px; }
            .login-logo img { height: 40px; }
        }
    </style>';

include 'header_auth.php';
?>

    <div class="login-wrapper" data-aos="fade-up" data-aos-duration="800">
        <div class="login-logo">
            <img src="<?php echo $logo; ?>" alt="<?=$titulo;?>">
        </div>

        <form action="validar.php" method="post" id="login-form">
            <input type="hidden" name="titulo" value="LOGIN">
            <div class="auth-card">
                <h3>Bem-vindo de volta 👋</h3>

                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                </div>

                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <a href="recuperar_senha.php" class="forgot-link">Esqueceu a senha?</a>

                <button type="submit" class="btn-enam">Entrar</button>

                <div class="divider"><span>ou</span></div>

                <div class="signup-row">
                    <p>Não tem conta? <a href="cadastro_conta.php">Criar conta grátis</a></p>
                    <img src="<?php echo $small_logo; ?>" alt="Logo" class="mini-logo">
                </div>
            </div>
        </form>
    </div>

<?php
$js_extra = '
    <script>
        $(document).ready(function() {
            AOS.init({ once: true, duration: 700 });

            var input = document.querySelector("#telefone");
            var iti = window.intlTelInput(input, {
                initialCountry: "br",
                preferredCountries: ["br", "pt", "us", "gb", "es"],
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });

            $("#login-form").on("submit", function(e) {
                e.preventDefault();
                var countryData = iti.getSelectedCountryData();
                var dialCode = countryData.dialCode;
                var phoneNumber = input.value.replace(/\D/g, \'\');
                var fullPhoneNumber = dialCode + phoneNumber;
                $(\'<input>\').attr({ type: \'hidden\', name: \'telefone\', value: fullPhoneNumber }).appendTo($(this));
                this.submit();
            });
        });
    </script>';

include 'footer_auth.php';
?>
<?php } ?>
