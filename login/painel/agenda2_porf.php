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


$sql_busca_prof = "SELECT * FROM  profissional WHERE telefone = '$login'";
$sql_busca_profs = mysqli_query($conn, $sql_busca_prof);
$total_busca_profs = mysqli_num_rows($sql_busca_profs);

while($rows_usuarios = mysqli_fetch_array($sql_busca_profs)) {
    $id_profissional  = $rows_usuarios['id'];

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

$sql = "SELECT * FROM agendamento WHERE	id_profissional = '$id_profissional' ORDER BY horario ASC";
$result = mysqli_query($conn, $sql);

// Inicializar o array de eventos
$eventos = [];

// Loop através dos resultados e organizar os eventos por data
while ($row = $result->fetch_assoc()) {
    // Extrair a data e concatenar as informações
    $data = date('Y-m-d', strtotime($row['data'])); // Formato Y-m-d
    $horario = $row['horario'];
    $servico = $row['profissional_cargo']; // Serviço com quebra de linha
    
    // Inicializar o array para a data se ainda não existir
    if (!isset($eventos[$data])) {
        $eventos[$data] = [];
    }
    
    // Adicionar o serviço ao array da data
    $eventos[$data][] = htmlspecialchars($horario." - ".$servico); // Usar htmlspecialchars para segurança
}

/***********************************************
 * LÓGICA DO CALENDÁRIO
 ***********************************************/
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.utf8', 'Portuguese_Brazil');

// Verifica se recebeu mês/ano via GET
$mes = isset($_GET['m']) ? (int)$_GET['m'] : date('n');
$ano = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');

// Ajusta se o mês ficar fora do intervalo [1..12]
if ($mes < 1) {
    $mes = 12;
    $ano--;
} elseif ($mes > 12) {
    $mes = 1;
    $ano++;
}

// Total de dias do mês e dia da semana do 1º dia
$qtdDias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$primeiroDiaSemana = date('w', strtotime("$ano-$mes-1"));

// Filtra apenas eventos do mês/ano atual
$eventosDoMes = [];
$eventosOriginais = []; // Array para manter a contagem original
foreach ($eventos as $data => $listaEventos) {
    [$y, $m, $d] = explode('-', $data);
    if ((int)$y === $ano && (int)$m === $mes) {
        $eventosOriginais[$data] = $listaEventos; // Guarda todos os eventos
        $eventosDoMes[$data] = array_slice($listaEventos, 0, 3); // Limita a 3 para exibição
    }
}

function obterNomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes] ?? '';
}

function obterCorEvento($index) {
    $cores = [
        '#1976d2', '#388e3c', '#f57c00', '#d32f2f', 
        '#7b1fa2', '#00796b', '#fbc02d', '#5d4037'
    ];
    return $cores[$index % count($cores)];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Moderno</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>


        .calendar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .month-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .calendar-grid {
            padding: 30px;
        }

        .weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            margin-bottom: 20px;
        }

        .weekday {
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
        }

        .day-cell {
            background: white;
            height: 120px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .day-cell:hover {
            background: #f8f9ff;
            transform: scale(1.02);
            z-index: 10;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .day-number {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #333;
        }

        .today .day-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .today {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
        }

        .event {
            background: #1976d2;
            color: white;
            padding: 4px 8px;
            margin: 2px 0;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.2s ease;
            height: 20px;
            line-height: 12px;
            display: flex;
            align-items: center;
        }

        .event:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .event:nth-child(2) { background: #388e3c; }
        .event:nth-child(3) { background: #f57c00; }
        .event:nth-child(4) { background: #d32f2f; }

        .events-container {
            flex: 1;
            overflow: hidden;
            max-height: 70px;
        }

        .more-events {
            color: #666;
            font-size: 0.7rem;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Mobile Responsivo */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .calendar-header {
                padding: 20px;
            }

            .month-title {
                font-size: 1.8rem;
            }

            .calendar-grid {
                padding: 15px;
            }

            .day-cell {
                height: 100px;
                padding: 8px;
            }

            .nav-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .weekday {
                padding: 10px 5px;
                font-size: 0.8rem;
            }

            .events-container {
                max-height: 60px;
            }
        }

        @media (max-width: 480px) {
            .days-grid {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .day-cell {
                height: 60px;
                border-radius: 8px;
                flex-direction: row;
                align-items: center;
                gap: 15px;
            }

            .day-number {
                margin-bottom: 0;
                min-width: 40px;
            }

            .events-container {
                flex: 1;
                max-height: 40px;
            }

            .event {
                margin: 1px 0;
                font-size: 0.8rem;
                height: 18px;
                line-height: 10px;
            }

            .weekdays {
                display: none;
            }
        }

        .empty-day {
            background: #f5f5f5;
            cursor: default;
        }

        .empty-day:hover {
            background: #f5f5f5;
            transform: none;
            box-shadow: none;
        }

        .day-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 5;
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-nav">
                <a class="nav-btn" href="?m=<?php echo $mes - 1; ?>&y=<?php echo $ano; ?>">
                    <span class="material-icons">chevron_left</span>
                    Anterior
                </a>
                
                <h1 class="month-title"><?php echo obterNomeMes($mes) . " " . $ano; ?></h1>
                
                <a class="nav-btn" href="?m=<?php echo $mes + 1; ?>&y=<?php echo $ano; ?>">
                    Próximo
                    <span class="material-icons">chevron_right</span>
                </a>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="weekdays">
                <div class="weekday">Dom</div>
                <div class="weekday">Seg</div>
                <div class="weekday">Ter</div>
                <div class="weekday">Qua</div>
                <div class="weekday">Qui</div>
                <div class="weekday">Sex</div>
                <div class="weekday">Sáb</div>
            </div>

            <div class="days-grid">
                <?php
                $dia = 1;
                $totalCells = 42; // 6 semanas × 7 dias
                
                for ($i = 0; $i < $totalCells; $i++) {
                    if ($i < $primeiroDiaSemana) {
                        echo "<div class='day-cell empty-day'></div>";
                    } elseif ($dia <= $qtdDias) {
                        $dataAtual = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                        $isToday = ($dataAtual == date('Y-m-d'));
                        $todayClass = $isToday ? 'today' : '';
                        
                        echo "<div class='day-cell $todayClass'>";
                        echo "<div class='day-number'>$dia</div>";
                        
                        if (isset($eventosDoMes[$dataAtual])) {
                            echo "<div class='events-container'>";
                            $eventCount = 0;
                            foreach ($eventosDoMes[$dataAtual] as $evento) {
                                if ($eventCount < 3) {
                                    echo "<div class='event' title='" . htmlspecialchars($evento) . "'>$evento</div>";
                                    $eventCount++;
                                }
                            }
                            
                            // Mostrar quantos eventos a mais existem usando o array original
                            $totalEventos = count($eventosOriginais[$dataAtual] ?? []);
                            if ($totalEventos > 3) {
                                $maisEventos = $totalEventos - 3;
                                echo "<div class='more-events'>+$maisEventos mais</div>";
                            }
                            echo "</div>";
                        }
                        
                        echo "<a href='calendario2_prof.php?data=$dataAtual' class='day-link'></a>";
                        echo "</div>";
                        $dia++;
                    } else {
                        echo "<div class='day-cell empty-day'></div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Adicionar animações suaves ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const dayCells = document.querySelectorAll('.day-cell:not(.empty-day)');
            dayCells.forEach((cell, index) => {
                cell.style.opacity = '0';
                cell.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    cell.style.transition = 'all 0.3s ease';
                    cell.style.opacity = '1';
                    cell.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
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