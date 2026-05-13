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
    $funcao = $rows_usuarios['funcao'];
    $usuario_api = $rows_usuarios['$usuario_api'];
    $plano =  $rows_usuarios['plano'];
        $modo_atuante  = $rows_usuarios['modo_atuante'];


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
 
 
 
 
 
 <?php
// Inclui a conexão
include 'conn.php';

// VARIÁVEL DO PLANO - DEFINIR MANUALMENTE
#$plano = 'plano1'; // Altere aqui conforme necessário: plano1, plano2, plano3, etc.

// Verificando se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1) Processar ativação/desativação do robô
    if (isset($_POST['acao_robo'])) {
        $acao = $_POST['acao_robo'];
        $status_robo = ($acao == 'ativar') ? 'IA' : 'desativado';
        $login = $_SESSION['login'];
        $sql_atualizar_status = "UPDATE login SET funcao = '$status_robo' WHERE login = '$login' ";
        $resultado_status = mysqli_query($conn, $sql_atualizar_status);
        
        
  
        $login2 = 'agenda_'.$login;
            
        $sql_atualizar_status = "UPDATE clientes SET time_resposta = '' WHERE usuario_api = '$login2'";
        $resultado_status = mysqli_query($conn, $sql_atualizar_status);
        
        
        if ($resultado_status) {
            if ($acao == 'ativar') {
                echo '<div class="alert alert-success" role="alert">
                        <i class="feather icon-check-circle"></i> Robô de notificações ativado! Agora respondendo automaticamente.
                      </div>';
            } else {
                echo '<div class="alert alert-warning" role="alert">
                        <i class="feather icon-alert-triangle"></i> Robô pausado! Não está mais respondendo notificações.
                      </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Erro ao atualizar status do robô de notificações.
                  </div>';
        }
    }
    
    // 2) Processar seleção de módulo para atualizar tabela funcao
    if (isset($_POST['modulo_selecionado']) && !empty($_POST['modulo_selecionado'])) {
        $id_modulo = (int) $_POST['modulo_selecionado'];
        
        // Busca o nome do módulo selecionado na tabela planos_clientes
        $sql_busca_modulo = "
            SELECT nome_modulo 
            FROM planos_clientes 
            WHERE id = $id_modulo 
            AND tipo = '1' 
            AND nome_plano = '$plano'
            LIMIT 1
        ";
        $res_modulo = mysqli_query($conn, $sql_busca_modulo);
        
        if ($res_modulo && mysqli_num_rows($res_modulo) > 0) {
            $modulo_info = mysqli_fetch_assoc($res_modulo);
            $nome_modulo = $modulo_info['nome_modulo'];
            
            // Verifica se já existe um registro na tabela funcao para este login
            $sql_verifica_usuario = "SELECT id FROM funcao WHERE login = '$login' LIMIT 1";
            $res_verifica_usuario = mysqli_query($conn, $sql_verifica_usuario);
            
            if ($res_verifica_usuario && mysqli_num_rows($res_verifica_usuario) > 0) {
                // Usuário já existe, apenas atualiza
                $sql_atualizar_funcao = "
                    UPDATE login 
                    SET modo_atuante = '$nome_modulo'
                    WHERE login = '$login'
                ";
                $resultado_funcao = mysqli_query($conn, $sql_atualizar_funcao);
                $acao_realizada = "atualizada";
            } else {
                // Usuário não existe, insere novo registro
                $sql_inserir_funcao = "
                    INSERT INTO funcao (funcao, id_funcao, login)
                    VALUES ('$nome_modulo', $id_modulo, '$login')
                ";
                $resultado_funcao = mysqli_query($conn, $sql_inserir_funcao);
                $acao_realizada = "inserida";
            }
            
            if ($resultado_funcao) {
                            VaiPara('modo.php'); 

                echo '<div class="alert alert-success" role="alert">
                        <i class="feather icon-check-circle"></i> Função ' . $acao_realizada . ' com sucesso! Tipo: <strong>' . htmlspecialchars($nome_modulo, ENT_QUOTES) . '</strong>
                      </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">
                        <i class="feather icon-x-circle"></i> Erro ao processar função: ' . mysqli_error($conn) . '
                      </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Módulo não encontrado no plano selecionado.
                  </div>';
        }
    }
    
    // 3) Processar modo do robô (formulário original)
    if (isset($_POST['modoRobo']) && !empty($_POST['modoRobo'])) {
        $modoRobo = $_POST['modoRobo'];
        
        $sql_atualizar_modo = "UPDATE login SET funcao = '$modoRobo' WHERE login = '$login'";
        $resultado_modo = mysqli_query($conn, $sql_atualizar_modo);
        
        if ($resultado_modo) {
            echo '<div class="alert alert-success" role="alert">
                    <i class="feather icon-check-circle"></i> Modo do robô atualizado com sucesso!
                  </div>';
                 
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Erro ao atualizar o modo do robô.
                  </div>';
        }
    }
}

// Busca informações do usuário atual
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$funcao_atual = 'desativado';
$status_robo = false;

if ($query_busca_usuario && mysqli_num_rows($query_busca_usuario) > 0) {
    $rows_usuarios = mysqli_fetch_array($query_busca_usuario);
    $nome = $rows_usuarios['nome'];
    $funcao_atual = $rows_usuarios['funcao'];
    $status_robo = ($funcao_atual == 'IA');
}

// Busca a função atual da tabela funcao para este login específico
$sql_funcao_atual = "SELECT funcao, id_funcao FROM funcao WHERE login = '$login' LIMIT 1";
$res_funcao_atual = mysqli_query($conn, $sql_funcao_atual);
$funcao_ativa = null;
$id_funcao_ativa = null;

if ($res_funcao_atual && mysqli_num_rows($res_funcao_atual) > 0) {
    $funcao_data = mysqli_fetch_assoc($res_funcao_atual);
    $funcao_ativa = $funcao_data['funcao'];
    $id_funcao_ativa = $funcao_data['id_funcao'];
}
?>

<!-- INFORMAÇÃO DO PLANO ATUAL -->
<div class="alert alert-info" role="alert">
    <i class="feather icon-info"></i> <strong>Plano Ativo:</strong> <?php echo strtoupper($plano); ?> | <strong>Usuário:</strong> <?php echo htmlspecialchars($login, ENT_QUOTES); ?>
</div>

<!-- STATUS ATUAL DO ROBÔ -->
<div class="card">
    <div class="card-header">
        <h5>Status do Robô de Notificações</h5>
        <div class="card-header-right">
            <ul class="list-unstyled card-option">
                <li><i class="feather icon-maximize full-card"></i></li>
                <li><i class="feather icon-minus minimize-card"></i></li>
                <li><i class="feather icon-refresh-cw reload-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <?php if ($status_robo): ?>
                        <div class="status-indicator bg-success"></div>
                        <span class="ml-2"><strong>Status:</strong> Respondendo Notificações</span>
                    <?php else: ?>
                        <div class="status-indicator bg-danger"></div>
                        <span class="ml-2"><strong>Status:</strong> Não Respondendo</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <?php if ($funcao_ativa): ?>
                    <span><strong>Função Ativa:</strong> <?php echo htmlspecialchars($modo_atuante, ENT_QUOTES); ?></span>
                <?php else: ?>
                    <span class="text-muted">Nenhuma função definida para este usuário</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- CONTROLES RÁPIDOS DO ROBÔ -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h5>Controles do Robô de Notificações</h5>
    </div>
    <div class="card-block">
        <div class="row">
            <!-- ATIVAR/DESATIVAR ROBÔ -->
            <div class="col-md-6">
                <h6>Ativar/Desativar Respostas Automáticas</h6>
                <form method="post" action="" style="display: inline;">
                    <?php if (!$status_robo): ?>
                        <button type="submit" name="acao_robo" value="ativar" class="btn btn-success btn-lg">
                            <i class="feather icon-message-circle"></i> Ativar Respostas
                        </button>
                    <?php else: ?>
                        <button type="submit" name="acao_robo" value="desativar" class="btn btn-danger btn-lg">
                            <i class="feather icon-pause-circle"></i> Pausar Respostas
                        </button>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- SELEÇÃO DE FUNÇÃO -->
            <div class="col-md-6">
                <form method="post" action="">
                    <h6>Selecionar Tipo de Função</h6>
                    <div class="form-group">
                        <select name="modulo_selecionado" class="form-control" required>
                            <option value="">Escolha o tipo de função</option>
                            <?php
                            // Busca módulos na tabela planos_clientes com tipo = 1 e nome_plano específico
                            $sql_modulos = "
                                SELECT id, nome_modulo 
                                FROM planos_clientes 
                                WHERE tipo = '1' 
                                AND nome_plano = '$plano'
                                ORDER BY nome_modulo ASC
                            ";
                            $res_modulos = mysqli_query($conn, $sql_modulos);
                            
                            if ($res_modulos && mysqli_num_rows($res_modulos) > 0) {
                                while ($modulo = mysqli_fetch_assoc($res_modulos)) {
                                    $selected = ($modulo['id'] == $id_funcao_ativa) ? 'selected' : '';
                                    echo "<option value='{$modulo['id']}' {$selected}>" . 
                                         htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES) . 
                                         "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Nenhuma função disponível neste plano</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-settings"></i> Definir Tipo de Função
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if($login == '123'){
    
    ?>
<!-- LISTA DE TIPOS DE FUNÇÃO DISPONÍVEIS -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h5>Tipos de Funções Disponíveis no <?php echo strtoupper($plano); ?></h5>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tipo de Função</th>
                        <th>Data de Adição</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_lista_modulos = "
                        SELECT id, nome_modulo, date
                        FROM planos_clientes 
                        WHERE tipo = '1' 
                        AND nome_plano = '$plano'
                        ORDER BY nome_modulo ASC
                    ";
                    $res_lista = mysqli_query($conn, $sql_lista_modulos);
                    
                    if ($res_lista && mysqli_num_rows($res_lista) > 0) {
                        while ($modulo = mysqli_fetch_assoc($res_lista)) {
                            $is_active = ($modulo['id'] == $id_funcao_ativa);
                            $status_badge = $is_active ? 
                                '<span class="badge badge-success">Em Uso</span>' : 
                                '<span class="badge badge-secondary">Disponível</span>';
                            
                            $data_formatada = date('d/m/Y H:i', strtotime($modulo['date']));
                            
                            echo "<tr" . ($is_active ? ' class="table-success"' : '') . ">";
                            echo "<td>" . htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES) . "</td>";
                            echo "<td>{$data_formatada}</td>";
                            echo "<td>{$status_badge}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Nenhum tipo de função disponível neste plano</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}

?>
<!-- INFORMAÇÕES DEBUG (OPCIONAL - REMOVER EM PRODUÇÃO) -->
<?php if (isset($_GET['debug'])): ?>
<div class="card" style="margin-top: 20px; border-color: #ffc107;">
    <div class="card-header" style="background-color: #fff3cd;">
        <h6><i class="feather icon-info"></i> Debug - Informações da Tabela Funcao</h6>
    </div>
    <div class="card-block">
        <?php
        $sql_debug = "SELECT * FROM funcao WHERE login = '$login'";
        $res_debug = mysqli_query($conn, $sql_debug);
        
        if ($res_debug && mysqli_num_rows($res_debug) > 0) {
            $debug_data = mysqli_fetch_assoc($res_debug);
            echo "<small>";
            echo "<strong>ID:</strong> " . $debug_data['id'] . "<br>";
            echo "<strong>Função:</strong> " . htmlspecialchars($debug_data['funcao'], ENT_QUOTES) . "<br>";
            echo "<strong>ID Função:</strong> " . $debug_data['id_funcao'] . "<br>";
            echo "<strong>Login:</strong> " . htmlspecialchars($debug_data['login'], ENT_QUOTES) . "<br>";
            echo "</small>";
        } else {
            echo "<small class='text-muted'>Nenhum registro encontrado na tabela funcao para este login.</small>";
        }
        ?>
    </div>
</div>
<?php endif; ?>

<style>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.table-success {
    background-color: rgba(40, 167, 69, 0.1);
}

.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.card-header h5 {
    margin-bottom: 0;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.alert-info {
    border-color: #bee5eb;
    background-color: #d1ecf1;
    color: #0c5460;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmação para pausar respostas automáticas
    document.querySelectorAll('button[value="desativar"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja pausar as respostas automáticas?')) {
                e.preventDefault();
            }
        });
    });
    
    // Validação do formulário de tipo de resposta
    var formFuncao = document.querySelector('form[method="post"]');
    if (formFuncao) {
        formFuncao.addEventListener('submit', function(e) {
            var select = this.querySelector('select[name="modulo_selecionado"]');
            if (select && !select.value) {
                alert('Por favor, selecione um tipo de função!');
                e.preventDefault();
            }
        });
    }
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