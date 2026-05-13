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


    <!-- DADOS PARA INSERIR AQUI -->
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
// Configuração da paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Conta o total de registros
$sqlCount = "SELECT COUNT(*) as total FROM leads";
$resultCount = mysqli_query($conn, $sqlCount);
$totalRegistros = mysqli_fetch_assoc($resultCount)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>

<style>
  .leads-wrapper {
    max-width: 1000px;
    margin: 30px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #e8e8e8;
  }

  .leads-header,
  .lead-row {
    display: grid;
    grid-template-columns: 2fr 3fr 2fr 2fr;
    align-items: center;
    padding: 16px 20px;
    gap: 12px;
  }

  .leads-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
  }

  .lead-row {
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.3s ease;
    color: #2d3748;
    font-size: 14px;
  }

  .lead-row:hover {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
  }

  .lead-row:nth-child(even) {
    background-color: #fafbfc;
  }

  .lead-row:nth-child(even):hover {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
  }

  .lead-row:last-child {
    border-bottom: none;
  }

  .leads-header div,
  .lead-row div {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding: 4px 0;
  }

  .lead-row div:first-child {
    font-weight: 500;
    color: #1a202c;
  }

  .lead-row div:nth-child(2) {
    color: #4a5568;
    font-family: 'Courier New', monospace;
    font-size: 13px;
  }

  .whatsapp-link {
    color: #25d366;
    font-weight: 600;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    background: #f0fff4;
    border: 1px solid #25d366;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
  }

  .whatsapp-link:hover {
    background: #25d366;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
  }

  .lead-row div:nth-child(4) {
    color: #718096;
    font-size: 12px;
    font-weight: 500;
  }

  /* Paginação */
  .pagination-wrapper {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #e8e8e8;
  }

  .pagination {
    display: inline-flex;
    gap: 8px;
    align-items: center;
  }

  .pagination a,
  .pagination span {
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    background: white;
    color: #4a5568;
  }

  .pagination a:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }

  .pagination .current {
    background: #667eea;
    color: white;
    border-color: #667eea;
  }

  .pagination .disabled {
    color: #a0aec0;
    cursor: not-allowed;
    background: #f7fafc;
  }

  .info-pagination {
    margin-top: 15px;
    color: #718096;
    font-size: 14px;
  }

  /* Responsividade */
  @media (max-width: 768px) {
    .leads-wrapper {
      margin: 20px 10px;
      border-radius: 12px;
    }
    
    .leads-header,
    .lead-row {
      grid-template-columns: 1.5fr 2fr 1.5fr 1fr;
      padding: 12px 16px;
      gap: 8px;
      font-size: 13px;
    }
    
    .leads-header {
      font-size: 12px;
    }
    
    .whatsapp-link {
      padding: 6px 8px;
      font-size: 12px;
    }
    
    .pagination a,
    .pagination span {
      padding: 8px 12px;
      font-size: 14px;
    }
  }

  @media (max-width: 480px) {
    .leads-header,
    .lead-row {
      grid-template-columns: 1fr 1.5fr 1fr;
      font-size: 12px;
    }
    
    .lead-row div:nth-child(4) {
      display: none;
    }
    
    .leads-header div:nth-child(4) {
      display: none;
    }
    
    .pagination {
      flex-wrap: wrap;
      gap: 4px;
    }
    
    .pagination a,
    .pagination span {
      padding: 6px 10px;
      font-size: 12px;
    }
  }
</style>

<div class="leads-wrapper">
  <div class="leads-header">
    <div>Nome</div>
    <div>Email</div>
    <div>WhatsApp</div>
    <div>Data</div>
  </div>
  <?php
  // Busca os leads com paginação
  $sql = "SELECT nome, email, whats, data FROM leads ORDER BY data DESC LIMIT $registrosPorPagina OFFSET $offset";
  $result = mysqli_query($conn, $sql);
  
  if (mysqli_num_rows($result) > 0):
    while ($row = mysqli_fetch_assoc($result)):
        // Formata para o padrão brasileiro
        $dataBR = date('d/m/Y H:i:s', strtotime($row['data']));
        
        // Limpa o número do WhatsApp (remove caracteres especiais)
        $whatsClean = preg_replace('/[^0-9]/', '', $row['whats']);
        
        // Se não começar com 55, adiciona o código do Brasil
        if (!str_starts_with($whatsClean, '55')) {
            $whatsClean = '55' . $whatsClean;
        }
  ?>
    <div class="lead-row">
      <div><?= htmlspecialchars($row['nome']) ?></div>
      <div><?= htmlspecialchars($row['email']) ?></div>
      <div>
        <a href="https://wa.me/<?= $whatsClean ?>" target="_blank" class="whatsapp-link">
          <?= htmlspecialchars($row['whats']) ?>
        </a>
      </div>
      <div><?= $dataBR ?></div>
    </div>
  <?php 
    endwhile;
  else:
  ?>
    <div class="lead-row">
      <div colspan="4" style="text-align: center; color: #718096; font-style: italic;">
        Nenhum lead encontrado
      </div>
    </div>
  <?php endif; ?>
  
  <!-- Paginação -->
  <div class="pagination-wrapper">
    <div class="pagination">
      <?php if ($paginaAtual > 1): ?>
        <a href="?pagina=1">&laquo; Primeira</a>
        <a href="?pagina=<?= $paginaAtual - 1 ?>">&lsaquo; Anterior</a>
      <?php else: ?>
        <span class="disabled">&laquo; Primeira</span>
        <span class="disabled">&lsaquo; Anterior</span>
      <?php endif; ?>
      
      <?php
      // Mostra páginas próximas
      $inicio = max(1, $paginaAtual - 2);
      $fim = min($totalPaginas, $paginaAtual + 2);
      
      for ($i = $inicio; $i <= $fim; $i++):
        if ($i == $paginaAtual):
      ?>
        <span class="current"><?= $i ?></span>
      <?php else: ?>
        <a href="?pagina=<?= $i ?>"><?= $i ?></a>
      <?php 
        endif;
      endfor;
      ?>
      
      <?php if ($paginaAtual < $totalPaginas): ?>
        <a href="?pagina=<?= $paginaAtual + 1 ?>">Próxima &rsaquo;</a>
        <a href="?pagina=<?= $totalPaginas ?>">Última &raquo;</a>
      <?php else: ?>
        <span class="disabled">Próxima &rsaquo;</span>
        <span class="disabled">Última &raquo;</span>
      <?php endif; ?>
    </div>
    
    <div class="info-pagination">
      Mostrando <?= ($offset + 1) ?> a <?= min($offset + $registrosPorPagina, $totalRegistros) ?> de <?= $totalRegistros ?> leads
    </div>
  </div>
</div>    
    
    
    
    
    
    
    
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

  gtag('config', 'UA-23581568-13');
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