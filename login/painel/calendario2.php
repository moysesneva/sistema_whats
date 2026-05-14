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


 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
   
   <?php

// Função para conectar ao banco de dados
function conectarDB() {
    include 'conn.php';
    return $conn;
}

// Obter o valor de 'login' da sessão ou de outro local
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
} else {
    // Se 'login' não estiver definido, redirecionar ou exibir uma mensagem de erro
    die("Usuário não logado.");
}

// Obter a data selecionada pelo usuário ou usar a data atual como padrão
$data_selecionada = $_GET['data'] ?? date('Y-m-d');

// Preparar a consulta SQL para buscar agendamentos
$conn = conectarDB();
$sql = "SELECT * FROM agendamento WHERE login = ? AND data = ? ORDER BY horario ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $login, $data_selecionada);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fechar a conexão após obter os resultados
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos do Dia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>


        .header {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            text-align: center;
            color: #333;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .date-filter {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .date-filter label {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        .date-input {
            padding: 12px 20px;
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .date-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .appointments-container {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .appointments-header {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
            padding: 25px 30px;
            border-bottom: 1px solid #e0e6ed;
        }

        .date-display {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .appointments-count {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .appointments-grid {
            padding: 30px;
        }

        .appointment-card {
            background: white;
            border: 1px solid #e0e6ed;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .appointment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .client-info {
            flex: 1;
            min-width: 200px;
        }

        .client-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .service-name {
            color: #667eea;
            font-weight: 600;
            font-size: 1rem;
        }

        .appointment-time {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-icon {
            color: #667eea;
            font-size: 20px;
        }

        .detail-text {
            color: #555;
            font-weight: 500;
        }

        .appointment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-not-confirmed {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-no-response {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-whatsapp {
            background: #25d366;
            color: white;
            padding: 10px 15px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-whatsapp:hover {
            background: #128c7e;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: #c82333;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .no-appointments {
            text-align: center;
            padding: 60px 30px;
            color: #666;
        }

        .no-appointments-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-appointments h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
            }

            .appointment-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .appointment-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <a href="javascript:history.back()" class="back-button">
            <span class="material-icons">arrow_back</span>
            Voltar ao Calendário
        </a>

        <div class="header">
            <h1>Agendamentos do Dia</h1>
            
            <form method="GET" action="" class="date-filter">
                <label for="data">Selecione uma data:</label>
                <input type="date" name="data" id="data" class="date-input" value="<?= htmlspecialchars($_GET['data'] ?? date('Y-m-d')) ?>">
                <button type="submit" class="btn-search">
                    <span class="material-icons">search</span>
                    Buscar
                </button>
            </form>
        </div>

        <div class="appointments-container">
            <div class="appointments-header">
                <div class="date-display">
                    <?php
                    $data_formatada = date('d/m/Y', strtotime($data_selecionada));
                    $dia_semana = date('w', strtotime($data_selecionada));
                    $nomes_dias = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                    echo $nomes_dias[$dia_semana] . ', ' . $data_formatada;
                    ?>
                </div>
                <div class="appointments-count">
                    <?php
                    $total_agendamentos = mysqli_num_rows($result);
                    echo $total_agendamentos . ' agendamento' . ($total_agendamentos != 1 ? 's' : '') . ' encontrado' . ($total_agendamentos != 1 ? 's' : '');
                    ?>
                </div>
            </div>

            <div class="appointments-grid">
                <?php
                // Verificar se há resultados
                if (mysqli_num_rows($result) > 0) {
                    // Loop através dos resultados
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Extrair os dados
                        $id = $row['id'];
                        $cliente_nome = $row['cliente_nome'];
                        $servico = $row['profissional_cargo'];
                        $profissional_nome = $row['profissional_nome'];
                        $data = date('d/m/Y', strtotime($row['data']));
                        $horario = $row['horario'];
                        $cliente_telefone = preg_replace('/\D/', '', $row['cliente_telefone']);
                        $whatsapp_link = "https://wa.me/" . $cliente_telefone;
                        $status = $row['confirmacao'];
                        
                        // Definir status de confirmação
                        if ($status == '1') {
                            $confirmacao_class = 'status-confirmed';
                            $confirmacao_text = 'Confirmado';
                            $confirmacao_icon = 'check_circle';
                        } elseif ($status == '2') {
                            $confirmacao_class = 'status-not-confirmed';
                            $confirmacao_text = 'Não Confirmado';
                            $confirmacao_icon = 'cancel';
                        } else {
                            $confirmacao_class = 'status-no-response';
                            $confirmacao_text = 'Sem Resposta';
                            $confirmacao_icon = 'help';
                        }

                        echo '<div class="appointment-card">';
                        
                        echo '<div class="appointment-header">';
                        echo '<div class="client-info">';
                        echo '<div class="client-name">' . htmlspecialchars($cliente_nome) . '</div>';
                        echo '<div class="service-name">' . htmlspecialchars($servico) . '</div>';
                        echo '</div>';
                        echo '<div class="appointment-time">';
                        echo '<span class="material-icons">schedule</span>';
                        echo htmlspecialchars($horario);
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="appointment-details">';
                        echo '<div class="detail-item">';
                        echo '<span class="material-icons detail-icon">person</span>';
                        echo '<span class="detail-text">Profissional: ' . htmlspecialchars($profissional_nome) . '</span>';
                        echo '</div>';
                        echo '<div class="detail-item">';
                        echo '<span class="material-icons detail-icon">event</span>';
                        echo '<span class="detail-text">Data: ' . $data . '</span>';
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="appointment-actions">';
                        echo '<div class="status-badge ' . $confirmacao_class . '">';
                        echo '<span class="material-icons" style="font-size: 16px; margin-right: 5px;">' . $confirmacao_icon . '</span>';
                        echo $confirmacao_text;
                        echo '</div>';
                        
                        echo '<div class="action-buttons">';
                        echo '<a href="' . htmlspecialchars($whatsapp_link) . '" target="_blank" class="btn-whatsapp">';
                        echo '<span class="material-icons">chat</span>';
                        echo 'WhatsApp';
                        echo '</a>';
                        echo '<a href="cancelar_agendamento.php?id=' . $id . '" class="btn-cancel" onclick="return confirm(\'Tem certeza que deseja cancelar este agendamento?\')">';
                        echo '<span class="material-icons">cancel</span>';
                        echo 'Cancelar';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                } else {
                    // Se não houver agendamentos
                    echo '<div class="no-appointments">';
                    echo '<div class="material-icons no-appointments-icon">event_busy</div>';
                    echo '<h3>Nenhum agendamento encontrado</h3>';
                    echo '<p>Não há agendamentos para esta data.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
   
   
   
   
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->




   </div>
</div>
</div>
</div>


    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script><script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
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