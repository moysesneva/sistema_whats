<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0);
$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;    
}

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
    $email = $rows_usuarios['email'];
    $pagamento_cliente = $rows_usuarios['pagamento_cliente'];
    $vencimento = $rows_usuarios['vencimento'];
    $id_assinatura = $rows_usuarios['id_assinatura'];
    $situacao = $rows_usuarios['situacao'];
}

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $link_pagamento = $rows_config['link_pagamento'];
    $preco = $rows_config['preco'];
    $telefone_adm = $rows_config['telefone'];
}

include 'menu.php';
if($tipo == '1'){
    VaiPara('config_adm.php?pagina_nome=1');    
}

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
    VaiPara('desbloquar.php');
}



if($tipo == '5'){
    
VaiPara('senha.php');
    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title><?=$titulo;?></title>
    <!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Estilos adicionais para os planos e elementos visuais -->
    <style>
        /* Estilos para o ribbon "Mais Popular" */
        .ribbon-wrapper {
            position: absolute;
            top: -5px;
            right: -5px;
            z-index: 1;
            overflow: hidden;
            width: 150px;
            height: 150px;
        }
        
        .ribbon {
            position: absolute;
            top: 35px;
            right: -50px;
            transform: rotate(45deg);
            width: 200px;
            padding: 10px 0;
            background-color: #ffc107;
            color: #000;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        /* Estilos para os cards de planos */
        .pricing-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        
        .pricing-card .card-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .pricing-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .pricing-period {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .pricing-features {
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        
        .pricing-features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        
        .pricing-features li:last-child {
            border-bottom: none;
        }
        
        .pricing-features i {
            color: #28a745;
        }
        
        .btn-select-plan {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-select-plan:hover {
            transform: translateY(-3px);
        }
        
        /* Efeito de pulsação para o botão do plano popular */
        @keyframes pulse-button {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }
        
        .pulse-animation {
            animation: pulse-button 2s infinite;
        }
    </style>
</head>

<body>
    <!-- Pre-loader -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
                <div class="ring"><div class="frame"></div></div>
            </div>
        </div>
    </div>
    
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <!-- Navbar -->
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

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <!-- Sidebar Menu -->
                    <nav class="pcoded-navbar">
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="pcoded-navigatio-lavel">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">
                                <?php 
                                if ($total_menu > 0) {
                                    while ($row_menu = mysqli_fetch_array($query_menu)) {
                                        $id = $row_menu['id'];
                                        $menu_nome = $row_menu['menu'];
                                        $menu_pagina_menu = $row_menu['menu_pagina'];
                                        $tipo_menu = $row_menu['tipo'];
                                        $icone_menu = $row_menu['icone_menu'];

                                        if ($id == $pagina_nome_recebe) {     
                                            echo '<li class="pcoded-hasmenu active">';
                                        } else {
                                            echo '<li class="pcoded-hasmenu">';
                                        }
                                        echo '
                                            <a href="' . $menu_pagina_menu . '?pagina_nome='.$id .'">
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
                    
                    <!-- Conteúdo Principal -->
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">

                                <?php
                                if(empty($email)) {
                                ?>
                                    <!-- Formulário de E-mail com Verificação Dupla -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-mail mr-2"></i>Configuração de E-mail</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8 mx-auto">
                                                    <h4 class="mb-4">Insira e Verifique seu E-mail</h4>
                                                    
                                                    <form action="verificar_email.php" method="post" onsubmit="return verificarEmails()">
                                                        <div class="form-group">
                                                            <label for="emailPerfil">E-mail</label>
                                                            <input type="email" class="form-control" id="emailPerfil" name="emailPerfil" placeholder="Digite seu e-mail" required onchange="mostrarAlerta()">
                                                            <small class="form-text text-info font-weight-bold">
                                                                <i class="fas fa-exclamation-circle"></i> Este e-mail será utilizado como forma de identificação de pagamento. Por favor, insira um e-mail válido e confirme-o com cuidado.
                                                            </small>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="emailPerfilConfirmar">Confirme seu E-mail</label>
                                                            <input type="email" class="form-control" id="emailPerfilConfirmar" name="emailPerfilConfirmar" placeholder="Digite seu e-mail novamente" required>
                                                            <small id="emailErro" class="form-text text-danger" style="display: none;">Os e-mails não coincidem. Por favor, tente novamente.</small>
                                                        </div>

                                                        <button type="submit" class="btn btn-success mt-3">Enviar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function verificarEmails() {
                                            var email = document.getElementById('emailPerfil').value;
                                            var emailConfirmar = document.getElementById('emailPerfilConfirmar').value;
                                            var emailErro = document.getElementById('emailErro');

                                            if (email !== emailConfirmar) {
                                                emailErro.style.display = 'block';
                                                return false;
                                            } else {
                                                emailErro.style.display = 'none';
                                                return true;
                                            }
                                        }

                                        function mostrarAlerta() {
                                            alert("Este e-mail será utilizado para identificação no pagamento. Verifique se está correto antes de prosseguir.");
                                        }
                                    </script>
                                <?php
                                } else {
                                    // Usuário já tem email
                                    $data_atual = date("Y-m-d");
                                    if ($vencimento >= $data_atual && $email) {
                                        $nome_usuario = $nome;
                                        $data_vencimento_formatada = date("d/m/Y", strtotime($vencimento));
                                ?>
                                    <!-- Detalhes da Fatura -->
                                    <div class="card shadow">
                                        <div class="card-header bg-primary text-white">
                                            <div class="d-flex align-items-center">
                                                <i class="feather icon-calendar mr-2"></i>
                                                <h4 class="mb-0">Detalhes da Fatura</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <div class="avatar mb-3">
                                                    <img src="<?php echo $img_perfil; ?>" alt="Perfil" class="img-radius" style="width: 80px; height: 80px;">
                                                </div>
                                                <h5 class="card-title">Olá, <span class="text-primary"><?php echo htmlspecialchars($nome_usuario); ?></span>!</h5>
                                            </div>
                                            
                                            <div class="alert alert-light border text-center mb-4">
                                                <div class="text-muted mb-2">Sua próxima fatura tem vencimento em:</div>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <i class="feather icon-clock text-danger mr-2"></i>
                                                    <h3 class="text-danger mb-0"><?php echo $data_vencimento_formatada; ?></h3>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center mt-5">
    <div class="col-md-6">
        <div class="border rounded p-4 mb-4 text-center shadow-sm" style="background-color: #f9f9f9;">
            <div class="text-muted small mb-2">Status</div>
            <div class="<?= ($situacao != 'ativado') ? 'text-danger' : 'text-success'; ?>" style="font-size: 1.5rem; font-weight: bold;">
                <i class="feather icon-check-circle mr-1"></i> 
                <?= ($situacao != 'ativado') ? 'Bloqueado' : 'Ativo'; ?>
            </div>
        </div>
    </div>
</div>

                                            
                                            <?php if ($situacao != 'ativado'): ?>
                                            <!-- Seção de Ativação do Bot -->
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                              <!-- SEÇÃO DE PLANOS CORRIGIDA - MELHOR CONTRASTE E BOTÕES -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-package mr-2"></i> Nossos Planos</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <h4>Escolha o plano ideal para o seu negócio</h4>
                                            </div>
                                            
                                            <div class="row justify-content-center">
                                                <?php 
                                                // Consulta os planos ativos
                                                $sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
                                                $result_planos = mysqli_query($conn, $sql_planos);
                                                
                                                $i = 0;
                                                if ($result_planos && mysqli_num_rows($result_planos) > 0) {
                                                    $total_planos = mysqli_num_rows($result_planos);
                                                    
                                                    while ($plano = mysqli_fetch_assoc($result_planos)) {
                                                        $id_plano = $plano['id'];
                                                        $titulo_plano = $plano['titulo'];
                                                        $preco_plano = $plano['preco'];
                                                        $link_pagamento_plano = $plano['link_pagamento'];
                                                        
                                                        // O plano do meio é destacado
                                                        $isMiddlePlan = ($i == 1 || ($total_planos == 2 && $i == 0));
                                                        
                                                        // Consulta as features do plano
                                                        $features = [];
                                                        $sql_features = "SELECT feature FROM planos_features WHERE id_plano = $id_plano ORDER BY id";
                                                        $result_features = mysqli_query($conn, $sql_features);
                                                        
                                                        if ($result_features && mysqli_num_rows($result_features) > 0) {
                                                            while ($feature = mysqli_fetch_assoc($result_features)) {
                                                                $features[] = $feature['feature'];
                                                            }
                                                        }
                                                ?>
                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card pricing-card h-100 <?php echo $isMiddlePlan ? 'shadow border-primary' : ''; ?>">
                                                        <?php if($isMiddlePlan): ?>
                                                        <div class="ribbon-wrapper">
                                                            <div class="ribbon">Mais Popular</div>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="card-header text-center py-4 <?php echo $isMiddlePlan ? 'bg-primary text-white' : ''; ?>">
                                                            <h3 class="font-weight-bold mb-0"><?php echo htmlspecialchars($titulo_plano); ?></h3>
                                                        </div>
                                                        
                                                        <div class="card-body d-flex flex-column">
                                                            <div class="text-center mb-4">
                                                                <div class="pricing-price">
                                                                    R$ <?php echo number_format(floatval($preco_plano), 2, ',', '.'); ?>
                                                                </div>
                                                                <div class="pricing-period">/ mês</div>
                                                            </div>
                                                            
                                                            <ul class="pricing-features list-unstyled">
                                                                <?php foreach($features as $feature): ?>
                                                                <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature); ?>
                                                                    <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            
                                                           <div class="mt-auto">
    <!-- Modificação aqui para concatenar o email ao link do plano -->
    <a href="<?php echo $link_pagamento_plano . (strpos($link_pagamento_plano, '?') !== false ? '&' : '?') . 'email=' . urlencode($email); ?>" target="_blank" 
       class="btn btn-block btn-lg <?php echo $isMiddlePlan ? 'btn-primary pulse-animation' : 'btn-outline-primary'; ?> btn-select-plan">
        Selecionar Plano
    </a>
</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info text-center">
                                                        <i class="feather icon-info-circle mr-2"></i>
                                                        Nenhum plano disponível no momento. Entre em contato com o administrador.
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <i class="feather icon-shield text-success mr-2" style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>Satisfação garantida ou seu dinheiro de volta</strong><br>
                                                    <small class="text-muted">Teste qualquer plano por 7 dias e cancele se não estiver satisfeito.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                           
                                            <?php else: ?>
                                            <div class="alert alert-success text-center">
                                                <i class="feather icon-check-circle mr-2"></i>
                                                <span>Sua assinatura está ativa até a data de vencimento</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="d-flex justify-content-between align-items-center small">
                                                <span><i class="feather icon-help-circle mr-1"></i> Precisa de ajuda?</span>
                                                <a href="https://wa.me/<?=$telefone_adm;?>?text=Preciso%20de%20ajuda" target="_blank" class="text-primary">Fale conosco</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    } else {
                                ?>
                                    <!-- SEÇÃO DE PLANOS CORRIGIDA - MELHOR CONTRASTE E BOTÕES -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-package mr-2"></i> Nossos Planos</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <h4>Escolha o plano ideal para o seu negócio</h4>
                                            </div>
                                            
                                            <div class="row justify-content-center">
                                                <?php 
                                                // Consulta os planos ativos
                                                $sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
                                                $result_planos = mysqli_query($conn, $sql_planos);
                                                
                                                $i = 0;
                                                if ($result_planos && mysqli_num_rows($result_planos) > 0) {
                                                    $total_planos = mysqli_num_rows($result_planos);
                                                    
                                                    while ($plano = mysqli_fetch_assoc($result_planos)) {
                                                        $id_plano = $plano['id'];
                                                        $titulo_plano = $plano['titulo'];
                                                        $preco_plano = $plano['preco'];
                                                        $link_pagamento_plano = $plano['link_pagamento'];
                                                        
                                                        // O plano do meio é destacado
                                                        $isMiddlePlan = ($i == 1 || ($total_planos == 2 && $i == 0));
                                                        
                                                        // Consulta as features do plano
                                                        $features = [];
                                                        $sql_features = "SELECT feature FROM planos_features WHERE id_plano = $id_plano ORDER BY id";
                                                        $result_features = mysqli_query($conn, $sql_features);
                                                        
                                                        if ($result_features && mysqli_num_rows($result_features) > 0) {
                                                            while ($feature = mysqli_fetch_assoc($result_features)) {
                                                                $features[] = $feature['feature'];
                                                            }
                                                        }
                                                ?>
                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card pricing-card h-100 <?php echo $isMiddlePlan ? 'shadow border-primary' : ''; ?>">
                                                        <?php if($isMiddlePlan): ?>
                                                        <div class="ribbon-wrapper">
                                                            <div class="ribbon">Mais Popular</div>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="card-header text-center py-4 <?php echo $isMiddlePlan ? 'bg-primary text-white' : ''; ?>">
                                                            <h3 class="font-weight-bold mb-0"><?php echo htmlspecialchars($titulo_plano); ?></h3>
                                                        </div>
                                                        
                                                        <div class="card-body d-flex flex-column">
                                                            <div class="text-center mb-4">
                                                                <div class="pricing-price">
                                                                    R$ <?php echo number_format(floatval($preco_plano), 2, ',', '.'); ?>
                                                                </div>
                                                                <div class="pricing-period">/ mês</div>
                                                            </div>
                                                            
                                                            <ul class="pricing-features list-unstyled">
                                                                <?php foreach($features as $feature): ?>
                                                                <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature); ?>
                                                                    <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            
                                                           <div class="mt-auto">
    <!-- Modificação aqui para concatenar o email ao link do plano -->
    <a href="<?php echo $link_pagamento_plano . (strpos($link_pagamento_plano, '?') !== false ? '&' : '?') . 'email=' . urlencode($email); ?>" target="_blank" 
       class="btn btn-block btn-lg <?php echo $isMiddlePlan ? 'btn-primary pulse-animation' : 'btn-outline-primary'; ?> btn-select-plan">
        Selecionar Plano
    </a>
</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info text-center">
                                                        <i class="feather icon-info-circle mr-2"></i>
                                                        Nenhum plano disponível no momento. Entre em contato com o administrador.
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <i class="feather icon-shield text-success mr-2" style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>Satisfação garantida ou seu dinheiro de volta</strong><br>
                                                    <small class="text-muted">Teste qualquer plano por 7 dias e cancele se não estiver satisfeito.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <script type="text/javascript" src="..\files\bower_components\chart.js\js\Chart.js"></script>
    <script src="..\files\assets\pages\widget\amchart\amcharts.js"></script>
    <script src="..\files\assets\pages\widget\amchart\serial.js"></script>
    <script src="..\files\assets\pages\widget\amchart\light.js"></script>
    <script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\SmoothScroll.js"></script>
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>
    
    <!-- Google Analytics -->
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