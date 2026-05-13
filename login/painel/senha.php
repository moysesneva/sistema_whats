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


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Senha</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        

        .password-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .header-icon i {
            color: white;
            font-size: 2.5rem;
        }

        .header h2 {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .input-container:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
        }

        .input-prefix {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 15px 55px 15px 65px;
            border: 2px solid #e0e6ed;
            background: #f8f9fa;
            font-size: 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            background: #f0f0f0;
            color: #667eea;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 2px;
            background: #e0e6ed;
            overflow: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .password-strength.show {
            opacity: 1;
        }

        .strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { 
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); 
            width: 25%; 
        }

        .strength-medium { 
            background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%); 
            width: 50%; 
        }

        .strength-strong { 
            background: linear-gradient(135deg, #48dbfb 0%, #0abde3 100%); 
            width: 75%; 
        }

        .strength-very-strong { 
            background: linear-gradient(135deg, #1dd1a1 0%, #55a3ff 100%); 
            width: 100%; 
        }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: 500;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .strength-text.show {
            opacity: 1;
        }

        .password-requirements {
            background: #f8f9ff;
            border: 1px solid #e0e6ed;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .password-requirements.show {
            opacity: 1;
        }

        .requirements-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            margin-bottom: 5px;
            color: #666;
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: #1dd1a1;
        }

        .requirement i {
            font-size: 16px;
        }

        .error-message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-top: 20px;
            display: none;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            animation: slideInDown 0.3s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-message {
            background: linear-gradient(135deg, #1dd1a1 0%, #55a3ff 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-top: 20px;
            display: none;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            animation: slideInDown 0.3s ease;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .password-container {
                padding: 30px 20px;
            }

            .header h2 {
                font-size: 1.8rem;
            }

            .header-icon {
                width: 70px;
                height: 70px;
            }

            .header-icon i {
                font-size: 2rem;
            }

            .form-control {
                padding: 12px 45px 12px 55px;
            }
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="history.back()" title="Voltar">
        <i class="material-icons">arrow_back</i>
    </button>

    <div class="password-container">
        <div class="header">
            <div class="header-icon">
                <i class="material-icons">lock_reset</i>
            </div>
            <h2>Atualizar Senha</h2>
            <p>Crie uma nova senha segura para sua conta</p>
        </div>

        <form id="formSenha" action="atualizar_senha.php" method="POST" onsubmit="return validarSenhas()">
            <!-- Campo para Nova Senha -->
            <div class="form-group">
                <label for="senha" class="form-label">
                    <i class="material-icons">lock</i>
                    Nova Senha
                </label>
                <div class="input-container">
                    <div class="input-prefix">
                        <i class="material-icons">lock</i>
                    </div>
                    <input type="password" class="form-control" id="senha" name="senha" 
                           placeholder="Digite sua nova senha" required 
                           oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="password-toggle" onclick="togglePassword('senha')">
                        <i class="material-icons">visibility</i>
                    </button>
                </div>
                
                <!-- Indicador de Força da Senha -->
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="strength-text" id="strengthText"></div>

                <!-- Requisitos da Senha -->
                <div class="password-requirements" id="passwordRequirements">
                    <div class="requirements-title">Requisitos da senha:</div>
                    <div class="requirement" id="req-length">
                        <i class="material-icons">circle</i>
                        Pelo menos 8 caracteres
                    </div>
                    <div class="requirement" id="req-uppercase">
                        <i class="material-icons">circle</i>
                        Uma letra maiúscula
                    </div>
                    <div class="requirement" id="req-lowercase">
                        <i class="material-icons">circle</i>
                        Uma letra minúscula
                    </div>
                    <div class="requirement" id="req-number">
                        <i class="material-icons">circle</i>
                        Um número
                    </div>
                    <div class="requirement" id="req-special">
                        <i class="material-icons">circle</i>
                        Um caractere especial (!@#$%^&*)
                    </div>
                </div>
            </div>

            <!-- Campo para Confirmar Senha -->
            <div class="form-group">
                <label for="confirmar_senha" class="form-label">
                    <i class="material-icons">lock_clock</i>
                    Confirmar Nova Senha
                </label>
                <div class="input-container">
                    <div class="input-prefix">
                        <i class="material-icons">lock_clock</i>
                    </div>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                           placeholder="Confirme sua nova senha" required 
                           oninput="checkPasswordMatch()">
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmar_senha')">
                        <i class="material-icons">visibility</i>
                    </button>
                </div>
            </div>

            <!-- Mensagens de Erro e Sucesso -->
            <div class="error-message" id="mensagemErro">
                <i class="material-icons">error</i>
                <span id="errorText">As senhas não coincidem. Tente novamente.</span>
            </div>

            <div class="success-message" id="mensagemSucesso">
                <i class="material-icons">check_circle</i>
                <span>Senhas coincidem perfeitamente!</span>
            </div>

            <!-- Botão de Envio -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="material-icons">security</i>
                <span id="submitText">Atualizar Senha</span>
                <div class="loading" id="loading"></div>
            </button>
        </form>
    </div>

    <script>
        // Função para alternar visibilidade da senha
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        // Função para verificar força da senha
        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const requirements = document.getElementById('passwordRequirements');
            
            if (password.length === 0) {
                strengthIndicator.classList.remove('show');
                strengthText.classList.remove('show');
                requirements.classList.remove('show');
                return;
            }
            
            strengthIndicator.classList.add('show');
            strengthText.classList.add('show');
            requirements.classList.add('show');
            
            // Verificar requisitos
            checkRequirement('req-length', password.length >= 8);
            checkRequirement('req-uppercase', /[A-Z]/.test(password));
            checkRequirement('req-lowercase', /[a-z]/.test(password));
            checkRequirement('req-number', /\d/.test(password));
            checkRequirement('req-special', /[!@#$%^&*(),.?":{}|<>]/.test(password));
            
            // Calcular força
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
            
            // Atualizar indicador visual
            strengthBar.className = 'strength-bar';
            strengthText.className = 'strength-text show';
            
            switch (strength) {
                case 0:
                case 1:
                    strengthBar.classList.add('strength-weak');
                    strengthText.textContent = 'Senha muito fraca';
                    strengthText.style.color = '#ff6b6b';
                    break;
                case 2:
                    strengthBar.classList.add('strength-weak');
                    strengthText.textContent = 'Senha fraca';
                    strengthText.style.color = '#ff6b6b';
                    break;
                case 3:
                    strengthBar.classList.add('strength-medium');
                    strengthText.textContent = 'Senha média';
                    strengthText.style.color = '#feca57';
                    break;
                case 4:
                    strengthBar.classList.add('strength-strong');
                    strengthText.textContent = 'Senha forte';
                    strengthText.style.color = '#48dbfb';
                    break;
                case 5:
                    strengthBar.classList.add('strength-very-strong');
                    strengthText.textContent = 'Senha muito forte';
                    strengthText.style.color = '#1dd1a1';
                    break;
            }
        }

        // Função para verificar requisitos individuais
        function checkRequirement(reqId, isValid) {
            const requirement = document.getElementById(reqId);
            const icon = requirement.querySelector('i');
            
            if (isValid) {
                requirement.classList.add('valid');
                icon.textContent = 'check_circle';
            } else {
                requirement.classList.remove('valid');
                icon.textContent = 'circle';
            }
        }

        // Função para verificar se as senhas coincidem
        function checkPasswordMatch() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const errorMsg = document.getElementById('mensagemErro');
            const successMsg = document.getElementById('mensagemSucesso');
            
            if (confirmarSenha.length === 0) {
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
                return;
            }
            
            if (senha !== confirmarSenha) {
                errorMsg.style.display = 'flex';
                successMsg.style.display = 'none';
            } else {
                errorMsg.style.display = 'none';
                successMsg.style.display = 'flex';
            }
        }

        // Função principal de validação
        function validarSenhas() {
            const senha = document.getElementById("senha").value;
            const confirmarSenha = document.getElementById("confirmar_senha").value;
            const errorMsg = document.getElementById("mensagemErro");
            const errorText = document.getElementById("errorText");
            const submitBtn = document.getElementById("submitBtn");
            const submitText = document.getElementById("submitText");
            const loading = document.getElementById("loading");
            
            // Verificar se as senhas coincidem
            if (senha !== confirmarSenha) {
                errorText.textContent = "As senhas não coincidem. Tente novamente.";
                errorMsg.style.display = "flex";
                return false;
            }
            
            // Verificar força mínima da senha
            if (senha.length < 8) {
                errorText.textContent = "A senha deve ter pelo menos 8 caracteres.";
                errorMsg.style.display = "flex";
                return false;
            }
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            loading.style.display = 'block';
            
            // Simular delay de processamento
            setTimeout(() => {
                submitText.style.display = 'block';
                loading.style.display = 'none';
                submitBtn.disabled = false;
            }, 2000);
            
            return true;
        }

        // Adicionar listener para Enter
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formSenha').submit();
            }
        });

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.password-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>


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

  gtag('config', '<?=$google?>');
</script>
</body>

</html>


<?php

include 'pcoded.php';
include'erro.php';

?>