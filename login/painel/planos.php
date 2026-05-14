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
    
    
    
    <?php

// 1) Processa o POST de remover módulo
if (isset($_POST['remover_modulo']) && !empty($_POST['remover_modulo'])) {
    $modulo_id = (int) $_POST['remover_modulo'];
    $plano_nome = mysqli_real_escape_string($conn, $_POST['plano']);
    
    // Remove o módulo da tabela planos_clientes
    $sql_remove = "
        DELETE FROM planos_clientes 
        WHERE id = {$modulo_id} AND nome_plano = '{$plano_nome}'
        LIMIT 1
    ";
    
    if (mysqli_query($conn, $sql_remove)) {
        echo '<div class="alert alert-success">Módulo removido com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao remover módulo: ' . mysqli_error($conn) . '</div>';
    }
}

// 2) Processa o POST de adicionar módulo
if (isset($_POST['adicionar_modulo']) && !empty($_POST['modulo']) && !empty($_POST['plano'])) {
    $modulo_id = (int) $_POST['modulo'];
    $nome_plano = mysqli_real_escape_string($conn, $_POST['plano']);

    // Busca o nome do módulo e tipo selecionado
    $sql_busca = "
        SELECT nome_modulo, tipo
        FROM modulos_lista
        WHERE id = {$modulo_id}
        LIMIT 1
    ";
    $res_busca = mysqli_query($conn, $sql_busca);

    if ($res_busca && mysqli_num_rows($res_busca) > 0) {
        $row = mysqli_fetch_assoc($res_busca);
        $nome_modulo = mysqli_real_escape_string($conn, $row['nome_modulo']);
        $tipo_modulo = mysqli_real_escape_string($conn, $row['tipo']);

        // Verifica se o módulo já existe no plano
        $sql_verifica = "
            SELECT id 
            FROM planos_clientes 
            WHERE nome_plano = '{$nome_plano}' 
            AND nome_modulo = '{$nome_modulo}'
            LIMIT 1
        ";
        $res_verifica = mysqli_query($conn, $sql_verifica);

        if ($res_verifica && mysqli_num_rows($res_verifica) > 0) {
            echo '<div class="alert alert-warning">Este módulo já está adicionado ao plano!</div>';
        } else {
            // Insere em planos_clientes incluindo o tipo
            $sql_insere = "
                INSERT INTO planos_clientes (nome_plano, nome_modulo, tipo, date)
                VALUES (
                    '{$nome_plano}',
                    '{$nome_modulo}',
                    '{$tipo_modulo}',
                    NOW()
                )
            ";
            if (mysqli_query($conn, $sql_insere)) {
                echo '<div class="alert alert-success">Módulo adicionado com sucesso! (Tipo: ' . htmlspecialchars($tipo_modulo, ENT_QUOTES) . ')</div>';
            } else {
                echo '<div class="alert alert-danger">Erro ao adicionar: ' . mysqli_error($conn) . '</div>';
            }
        }
    } else {
        echo '<div class="alert alert-warning">Módulo não encontrado.</div>';
    }
}

// 3) Busca todos os módulos disponíveis com tipo
$sql_mods = "
    SELECT id, nome_modulo, tipo
    FROM modulos_lista
    ORDER BY tipo ASC, nome_modulo ASC
";
$res_mods = mysqli_query($conn, $sql_mods);

// Inclui a conexão
include 'conn.php';
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Configuração de Planos e Módulos</h2>
    
    <div class="row">
        <!-- PLANO 1 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Plano 1</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano = 'plano1';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado = mysqli_query($conn, $sql);

                            // Exibe os módulos
                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                while ($modulo = mysqli_fetch_assoc($resultado)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                 
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano1"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano1" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                                   
                                ?>
                                    <option value="<?= $mod['id'] ?>">
                                        <?= htmlspecialchars($mod['nome_modulo'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano1">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>

        <!-- PLANO 2 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Plano 2</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano2 = 'plano2';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql2 = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano2}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado2 = mysqli_query($conn, $sql2);

                            // Exibe os módulos
                            if ($resultado2 && mysqli_num_rows($resultado2) > 0) {
                                while ($modulo2 = mysqli_fetch_assoc($resultado2)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                    switch($modulo2['tipo']) {
                                        case '1':
                                            $badge_class = 'badge-success';
                                  
                                            break;
                                        case '2':
                                            $badge_class = 'badge-info';
                                          
                                            break;
                                        case '3':
                                            $badge_class = 'badge-warning';
                                            
                                            break;
                                        default:
                                            $badge_class = 'badge-secondary';
                                           $modulo2['tipo'];
                                    }
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo2['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo2['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano2 . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano2"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano2" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod2 = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                                  
                                ?>
                                    <option value="<?= $mod2['id'] ?>">
                                        <?= htmlspecialchars($mod2['nome_modulo'], ENT_QUOTES, 'UTF-8') ?> 
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano2">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- PLANO 3 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Plano 3</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano3 = 'plano3';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql3 = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano3}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado3 = mysqli_query($conn, $sql3);

                            // Exibe os módulos
                            if ($resultado3 && mysqli_num_rows($resultado3) > 0) {
                                while ($modulo3 = mysqli_fetch_assoc($resultado3)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                   
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo3['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo3['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano3 . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano3"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano3" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod3 = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                         
                                ?>
                                    <option value="<?= $mod3['id'] ?>">
                                        <?= htmlspecialchars($mod3['nome_modulo'], ENT_QUOTES, 'UTF-8') ?> 
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano3">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resumo dos Planos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                
            </div>
        </div>
    </div>
</div>

<!-- Estilo adicional para os cartões de planos -->
<style>
.modulos-container {
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    padding: 10px;
    background-color: #f8f9fa;
}

.modulo-item {
    transition: all 0.2s ease;
}

.modulo-item:hover {
    background-color: #e9ecef !important;
}

.card {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin: 2px;
}

.badge-success {
    background-color: #28a745;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona confirmação para remoção de módulos
    document.querySelectorAll('button[type="submit"]').forEach(function(btn) {
        if (btn.innerHTML.includes('trash')) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja remover este módulo do plano?')) {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>
    
    
    
    
    
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