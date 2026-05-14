<?php
session_start();
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 
$login = $_SESSION['login'];

include 'conn.php';
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

while ($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome       = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo       = $rows_usuarios['tipo'];
}

include 'menu.php';

if ($total_busca_usuario != 1) {
    VaiPara('login.php');
}
if ($autorizado != 2) {
    VaiPara('desbloquar.php');
}
if ($tipo != 1) {
    VaiPara('login.php');
}



include 'bloqueio.php';



$pagina_atual = 'desconhecida';

if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $pagina_atual = $protocolo . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

?>


<?php

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
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
    <meta name="keywords" content="Admin, Responsive, Bootstrap, Template">
    <meta name="author" content="#">
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <style>
        /* CSS para exibir os módulos em lista (um abaixo do outro) */
        .module-card {
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s;
        }
        .module-card:hover {
            transform: translateY(-5px);
        }
        .module-card .card-img {
            height: 150px;
            object-fit: cover;
            width: 100%;
        }
        .btn-custom {
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
        }
        .btn-comprar {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .btn-instalar {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .btn-visualizar {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        .price-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ffc107;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class="contain">
                <?php for ($i = 0; $i < 10; $i++): ?>
                    <div class="ring"><div class="frame"></div></div>
                <?php endfor; ?>
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
                                <a href="#!" onclick="toggleFullScreen()">
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
                                    <ul class="show-notification profile-notification dropdown-menu">
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

            <!-- Sidebar -->
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
                            <!-- INÍCIO: Seção de Módulos (exibidos em lista) -->










<?php



// Configurações do cliente
$chave_cliente = $token; // Substitua por uma chave única para este cliente
$url_servidor = "https://tasmota.com.br/modulos_2025/servidor.php";










// Função para fazer requisição CURL para o servidor
function fazerRequisicaoServidor($url, $dados = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desativar em produção
    
    $resposta = curl_exec($ch);
    
    if(curl_error($ch)) {
        die("Erro na requisição: " . curl_error($ch));
    }
    
    curl_close($ch);
    return json_decode($resposta, true);
}

// Função para baixar um módulo
function baixarModulo($id_modulo) {
    global $chave_cliente, $url_servidor;
    
    // Faz requisição para obter dados do módulo
    $dados = [
        'acao' => 'baixar_modulo',
        'chave_cliente' => $chave_cliente,
         'pagina_origem' => $pagina_atual,
        'webhook' =>  $webhook,
        'chave_cliente' => $chave_cliente,
        'id_modulo' => $id_modulo
    ];
    
    $resposta = fazerRequisicaoServidor($url_servidor, $dados);
    
    if(isset($resposta['erro'])) {
        return ['status' => false, 'mensagem' => $resposta['erro']];
    }
    
    // Obtém o caminho de instalação
    $caminho_instalacao = $resposta['caminho_instalacao'];
    
    // Verifica se o caminho existe, se não, cria-o
    if(!file_exists($caminho_instalacao)) {
        // Tenta criar o diretório com permissões recursivas
        if(!mkdir($caminho_instalacao, 0755, true)) {
            return ['status' => false, 'mensagem' => 'Não foi possível criar o diretório de instalação: ' . $caminho_instalacao];
        }
    }
    
    // Salva os arquivos do módulo diretamente no caminho de instalação
    foreach($resposta['arquivos'] as $arquivo) {
        $caminho_arquivo = $caminho_instalacao . "/" . $arquivo['nome'];
        
        // Salva o arquivo
        $resultado = file_put_contents($caminho_arquivo, base64_decode($arquivo['conteudo']));
        
        if($resultado === false) {
            return ['status' => false, 'mensagem' => 'Erro ao salvar o arquivo: ' . $arquivo['nome']];
        }
    }
    
    return ['status' => true, 'mensagem' => 'Módulo baixado e instalado com sucesso em: ' . $caminho_instalacao];
}

// Verifica se há ação para baixar módulo
if(isset($_POST['baixar_modulo'])) {
    $resultado = baixarModulo($_POST['id_modulo']);
    
    if($resultado['status']) {
        $_SESSION['mensagem'] = $resultado['mensagem'];
    } else {
        $_SESSION['erro'] = $resultado['mensagem'];
    }
    
    // Redireciona para evitar reenvio do formulário
    VaiPara('modulos/instalador.php');
    exit;
}

// Busca a lista de módulos disponíveis
$dados = [
    'acao' => 'listar_modulos',
    'pagina_origem' => $pagina_atual,
     'webhook' =>  $webhook,
    'chave_cliente' => $chave_cliente
];

$modulos = fazerRequisicaoServidor($url_servidor, $dados);

// Se ocorreu erro na requisição
if(isset($modulos['erro'])) {
    $erro_modulos = $modulos['erro'];
    $modulos = [];
}

// Verificar quais módulos o cliente já adquiriu
$dados = [
    'acao' => 'listar_modulos_adquiridos',
    'chave_cliente' => $chave_cliente
];

$modulos_adquiridos = fazerRequisicaoServidor($url_servidor, $dados);

// Se ocorreu erro na requisição, inicializa como array vazio
if(isset($modulos_adquiridos['erro'])) {
    $modulos_adquiridos = [];
}

// Transforma a lista em um array associativo para facilitar a verificação
$modulos_adquiridos_array = [];
foreach($modulos_adquiridos as $mod) {
    $modulos_adquiridos_array[$mod['id_modulo']] = true;
}
?>


    <style>
     
        h1 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .modulos-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .modulo-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background: #fff;
            transition: transform 0.3s;
        }
        .modulo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .modulo-titulo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .modulo-descricao {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .modulo-video {
            margin-bottom: 15px;
        }
        .modulo-preco {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge-pago {
            background-color: #2196F3;
            color: white;
        }
        .badge-gratis {
            background-color: #4CAF50;
            color: white;
        }
        .badge-adquirido {
            background-color: #FF9800;
            color: white;
            margin-left: 5px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-pago {
            background: #2196F3;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .mensagem {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .sucesso {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .erro {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 800px;
            width: 90%;
            position: relative;
        }
        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Módulos Disponíveis</h1>
        
        <?php if(isset($_SESSION['mensagem'])): ?>
            <div class="mensagem sucesso">
                <?php 
                echo $_SESSION['mensagem']; 
                unset($_SESSION['mensagem']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['erro'])): ?>
            <div class="mensagem erro">
                <?php 
                echo $_SESSION['erro']; 
                unset($_SESSION['erro']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($erro_modulos)): ?>
            <div class="mensagem erro"><?php echo $erro_modulos; ?></div>
        <?php else: ?>
            
            <div class="modulos-lista">
                <?php foreach($modulos as $modulo): ?>
                    <div class="modulo-card">
                        <div>
                            <?php if($modulo['tipo'] == 'gratis'): ?>
                                <span class="badge badge-gratis">GRÁTIS</span>
                            <?php else: ?>
                                <span class="badge badge-pago">PAGO</span>
                            <?php endif; ?>
                            
                            <?php if(isset($modulos_adquiridos_array[$modulo['id']])): ?>
                                <span class="badge badge-adquirido">ADQUIRIDO</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="modulo-titulo"><?php echo htmlspecialchars($modulo['nome']); ?></div>
                        <div class="modulo-descricao"><?php echo htmlspecialchars($modulo['descricao']); ?></div>
                        
                        <?php if($modulo['tipo'] == 'pago' && !isset($modulos_adquiridos_array[$modulo['id']])): ?>
                            <div class="modulo-preco">
                                Preço: R$ <?php echo number_format($modulo['preco'], 2, ',', '.'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($modulo['video_youtube'])): ?>
                            <div class="modulo-video">
                                <button class="btn" onclick="abrirVideo('<?php echo $modulo['video_youtube']; ?>')">Ver Vídeo</button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($modulo['tipo'] == 'gratis' || isset($modulos_adquiridos_array[$modulo['id']])): ?>
                            <form method="post" action="">
                                <input type="hidden" name="id_modulo" value="<?php echo $modulo['id']; ?>">
                                <button type="submit" name="baixar_modulo" class="btn">Baixar Módulo</button>
                            </form>
                        <?php else: ?>
                            <a href="<?php echo $modulo['url_pagamento']; ?>" target="_blank" class="btn btn-pago">Adquirir Módulo</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
    
    <!-- Pop-up para vídeo do YouTube -->
    <div class="popup-overlay" id="videoPopup">
        <div class="popup-content">
            <span class="popup-close" onclick="fecharVideo()">&times;</span>
            <div id="videoContainer"></div>
        </div>
    </div>
    
    <script>
        function abrirVideo(videoId) {
            const popup = document.getElementById('videoPopup');
            const container = document.getElementById('videoContainer');
            
            // Cria iframe para o vídeo do YouTube
            container.innerHTML = `<iframe width="100%" height="450" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            
            // Exibe o popup
            popup.style.display = 'flex';
        }
        
        function fecharVideo() {
            const popup = document.getElementById('videoPopup');
            const container = document.getElementById('videoContainer');
            
            // Limpa o conteúdo do container
            container.innerHTML = '';
            
            // Esconde o popup
            popup.style.display = 'none';
        }
    </script>



















































</html>











    </div>
  </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts adicionais -->
    <script src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <script src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <script src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <script src="..\files\bower_components\chart.js\js\Chart.js"></script>
    <script src="..\files\assets\pages\widget\amchart\amcharts.js"></script>
    <script src="..\files\assets\pages\widget\amchart\serial.js"></script>
    <script src="..\files\assets\pages\widget\amchart\light.js"></script>
    <script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="..\files\assets\js\SmoothScroll.js"></script>
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script src="..\files\assets\js\script.min.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-23581568-13');
    </script>
</body>
</html>
<?php
include 'pcoded.php';
include 'erro.php';
?>
