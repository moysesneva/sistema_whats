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
 $credito  = $rows_usuarios['creditos'];
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


include 'bloqueio.php';


?>


<?php
// Arquivo atualizar_creditos.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inclui o arquivo de conexão se necessário
    // include 'conexao.php';
    
    // Verificar se os campos foram enviados
    if (isset($_POST['usuario_id']) && isset($_POST['quantidade_creditos'])) {
        $usuario_id = intval($_POST['usuario_id']);
        $quantidade_creditos = intval($_POST['quantidade_creditos']);
        
        // Validar os dados
        if ($usuario_id > 0 && $quantidade_creditos >= 0) {
            // Atualizar os créditos no banco de dados
            $sql = "UPDATE login SET creditos = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $quantidade_creditos, $usuario_id);
            
            if ($stmt->execute()) {
                // Redirecionar com mensagem de sucesso
                VaiPara('listar_bot.php?msg=Créditos atualizados com sucesso');
                exit;
            } else {
                // Erro ao atualizar
                VaiPara("listar_bot.php?erro=Erro ao atualizar os créditos:");
                exit;
            }}}}

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


    <!-- Favicon icon -->
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <!-- Required Framework -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- Feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\font-awesome\css\font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <!-- Toastr notifications -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   


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
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\font-awesome\css\font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <!-- Toastr notifications -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
                       <div class="card">
                    <?php
                    $desativado_class = '1';
                    ?>
                    <?php
                    if($desativado_class == 2){
                      ?> 
                <!-- Cabeçalho da página -->
                <div class="page-header card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Gerenciamento de Bots</h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card do Atualizador de Classes em outra linha -->
            
                 
                    <div class="card-header">
                        <h5>Atualizador de Classes</h5>
                    </div>
                    <div class="card-body">
                        <button id="btnAtualizar" class="btn btn-primary mb-3">Atualizar Classes</button>
                        <div class="progress mb-3">
                            <div id="barraPorcentagem"
                                 class="progress-bar"
                                 role="progressbar"
                                 style="width: 0%;"
                                 aria-valuenow="0"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                0%
                            </div>
                            
                        </div>
                        <div id="status" style="display: none;"></div>
                  
                    </div>
                 
                </div>
                      <?php
                        
                    }
                    ?>
                <!-- Aqui continua o resto do seu conteúdo/tabela de bots etc. -->
   
                                             <!-- Botão para abrir o modal -->
   <!DOCTYPE html>
<html lang="pt-br">
<head>
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
    <!-- Required Framework -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- Feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\font-awesome\css\font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
    <!-- Toastr notifications -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>

                                               <div class="col-md-4 text-right">


<script>
document.addEventListener('DOMContentLoaded', function() {
  const btnAtualizar = document.getElementById('btnAtualizar');
  const barraPorcentagem = document.getElementById('barraPorcentagem');
  const statusDiv = document.getElementById('status');

  btnAtualizar.addEventListener('click', iniciarAtualizacao);

  function iniciarAtualizacao() {
    btnAtualizar.disabled = true;
    barraPorcentagem.style.width = '0%';
    barraPorcentagem.textContent = '0%';
    mostrarStatus('Iniciando atualização de classes...', 'info');

    fetch('classes.php')
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro na requisição: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        const totalAtualizacoes = data.total;
        let atualizacoesFeitas = 0;

        if (totalAtualizacoes <= 0) {
          mostrarStatus('Nenhuma atualização necessária!', 'info');
          btnAtualizar.disabled = false;
          return;
        }

        const intervalo = setInterval(() => {
          atualizacoesFeitas++;
          const porcentagem = Math.round((atualizacoesFeitas / totalAtualizacoes) * 100);
          barraPorcentagem.style.width = porcentagem + '%';
          barraPorcentagem.textContent = porcentagem + '%';

          if (atualizacoesFeitas >= totalAtualizacoes) {
            clearInterval(intervalo);
            btnAtualizar.disabled = false;
            mostrarStatus('Atualização das classes concluída com sucesso!', 'success');
          }
        }, 1000);
      })
      .catch(error => {
        console.error('Erro ao processar a atualização:', error);
        mostrarStatus('Ocorreu um erro durante a atualização: ' + error.message, 'danger');
        btnAtualizar.disabled = false;
      });
  }

  function mostrarStatus(mensagem, tipo) {
    statusDiv.style.display = 'block';
    statusDiv.textContent = mensagem;
    // Mapeia os tipos para as classes de alerta do Bootstrap:
    // 'info', 'success' e 'danger'
    statusDiv.className = 'alert alert-' + tipo;
  }
});
</script>

</body>
</html>

                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                  <!-- Conteúdo principal -->
<?php

// Processa alteração de créditos
if (isset($_POST['usuario_id']) && isset($_POST['quantidade_creditos'])) {
    $usuario_id = (int) $_POST['usuario_id'];
    $quantidade_creditos = (int) $_POST['quantidade_creditos'];
    
    $sql_update_creditos = "UPDATE login SET creditos = {$quantidade_creditos} WHERE id = {$usuario_id}";
    
    if (mysqli_query($conn, $sql_update_creditos)) {
        echo '<div class="alert alert-success">Créditos atualizados com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao atualizar créditos: ' . mysqli_error($conn) . '</div>';
    }
}

// Processa alteração de plano
if (isset($_POST['usuario_id_plano']) && isset($_POST['novo_plano'])) {
    $usuario_id = (int) $_POST['usuario_id_plano'];
    $novo_plano = mysqli_real_escape_string($conn, $_POST['novo_plano']);
    
    $sql_update_plano = "UPDATE login SET plano = '{$novo_plano}' WHERE id = {$usuario_id}";
    
    if (mysqli_query($conn, $sql_update_plano)) {
        echo '<div class="alert alert-success">Plano alterado com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao alterar plano: ' . mysqli_error($conn) . '</div>';
    }
}

// Inclui a conexão
include 'conn.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="m-b-10">Gerenciamento de Bots</h2><p>
        <h5>Lista de Bots Ativos</h5>
        <div class="card-header-right">
            <ul class="list-unstyled card-option">
                <li><i class="feather icon-maximize full-card"></i></li>
                <li><i class="feather icon-minus minimize-card"></i></li>
                <li><i class="feather icon-refresh-cw reload-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table id="botTable" class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Plano</th>
                        <th>Créditos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql_busca_usuario = "SELECT * FROM login WHERE tipo = '2' OR tipo = '3'";
                $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
                $total_busca_usuario = mysqli_num_rows($query_busca_usuario);

                while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
                    $nome = $rows_usuarios['nome'];
                    $usuario_api = $rows_usuarios['usuario_api'];
                    $situacao = $rows_usuarios['situacao'];
                    $email = $rows_usuarios['email'];
                    $login = $rows_usuarios['login'];
                    $credito = $rows_usuarios['creditos'];
                    $plano = $rows_usuarios['plano'];
                    $id_usuario = $rows_usuarios['id'];
                    
                    // Define a classe de status para colorir
                    $status_class = '';
                    if($situacao == 'ativado') {
                        $status_class = 'badge badge-success';
                    } else if($situacao == 'desativado') {
                        $status_class = 'badge badge-danger';
                    } else if($situacao == 'bloqueado') {
                        $status_class = 'badge badge-warning';
                    }
                    
                    // Define a classe de plano para colorir
                    $plano_class = '';
                    if($plano == 'plano1') {
                        $plano_class = 'badge badge-primary';
                    } else if($plano == 'plano2') {
                        $plano_class = 'badge badge-success';
                    } else if($plano == 'plano3') {
                        $plano_class = 'badge badge-info';
                    } else {
                        $plano_class = 'badge badge-secondary';
                    }
                ?>
                    <tr>
                        <td><?=htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');?></td>
                        <td><?=htmlspecialchars($email, ENT_QUOTES, 'UTF-8');?></td>
                        <td><?=htmlspecialchars($login, ENT_QUOTES, 'UTF-8');?></td>
                        <td><span class="<?=$status_class;?>"><?=htmlspecialchars($situacao, ENT_QUOTES, 'UTF-8');?></span></td>
                        <td>
                            <button type="button" class="btn btn-link btn-editar-plano <?=$plano_class;?>" 
                                   data-id="<?=$id_usuario;?>" 
                                   data-plano="<?=htmlspecialchars($plano, ENT_QUOTES, 'UTF-8');?>">
                                <?=htmlspecialchars($plano ? $plano : 'Sem plano', ENT_QUOTES, 'UTF-8');?>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-editar-creditos" 
                                   data-id="<?=$id_usuario;?>" 
                                   data-creditos="<?=$credito;?>">
                                <?=$credito;?>
                            </button>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-success" onclick="executarAcao('<?=$usuario_api;?>', 'iniciar')" data-toggle="tooltip" title="Iniciar Bot">
                                    <i class="feather icon-play"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="executarAcao('<?=$usuario_api;?>', 'parar')" data-toggle="tooltip" title="Parar Bot">
                                    <i class="feather icon-pause"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="executarAcao('<?=$usuario_api;?>', 'bloquear')" data-toggle="tooltip" title="Bloquear Bot">
                                    <i class="feather icon-lock"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="mostrarImagemBot('<?=$usuario_api;?>', '<?=htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');?>')" data-toggle="tooltip" title="Visualizar QR Code">
                                    <i class="feather icon-image"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-danger" onclick="executarAcao('<?=$usuario_api;?>', 'deletar')" data-toggle="tooltip" title="Deletar Bot">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Edição de Créditos -->
<div class="modal fade" id="modalEditarCreditos" tabindex="-1" role="dialog" aria-labelledby="modalEditarCreditosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCreditosLabel">Editar Créditos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarCreditos" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    <div class="form-group">
                        <label for="quantidade_creditos">Quantidade de Créditos:</label>
                        <input type="number" class="form-control" id="quantidade_creditos" name="quantidade_creditos" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Edição de Plano -->
<div class="modal fade" id="modalEditarPlano" tabindex="-1" role="dialog" aria-labelledby="modalEditarPlanoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPlanoLabel">Alterar Plano do Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarPlano" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" id="usuario_id_plano" name="usuario_id_plano">
                    <div class="form-group">
                        <label for="novo_plano">Selecione o Plano:</label>
                        <select class="form-control" id="novo_plano" name="novo_plano" required>
                            <option value="">Selecione um plano</option>
                            <option value="plano1">Plano 1</option>
                            <option value="plano2">Plano 2</option>
                            <option value="plano3">Plano 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <small class="form-text text-muted">
                            <strong>Plano 1:</strong> Recursos básicos<br>
                            <strong>Plano 2:</strong> Recursos intermediários<br>
                            <strong>Plano 3:</strong> Todos os recursos
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Plano</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o bot <span id="botName" class="font-weight-bold"></span>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="processa_acao.php" method="POST">
                    <input type="hidden" id="deleteUsuario" name="usuario" value="">
                    <input type="hidden" name="comando" value="deletar">
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Print do WhatsApp -->
<div class="modal fade" id="printWhatsAppModal" tabindex="-1" role="dialog" aria-labelledby="printWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="printWhatsAppModalLabel">Print do WhatsApp</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="printWhatsAppContainer">
                    <!-- A imagem será carregada aqui -->
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando print do WhatsApp...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnDownloadPrint">
                    <i class="feather icon-download"></i> Baixar Imagem
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Imagem do Bot -->
<div class="modal fade" id="imagemBotModal" tabindex="-1" role="dialog" aria-labelledby="imagemBotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="imagemBotModalLabel">QR Code do Bot</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <!-- A imagem será carregada aqui -->
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando QR Code...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnDownloadQR">
                    <i class="feather icon-download"></i> Baixar QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    
    // Função para inicializar os modais
    function initializeModals() {
        console.log('Inicializando eventos dos modais');
        
        // Inicializa os tooltips
        if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Evento para abrir o modal de edição de créditos
        document.querySelectorAll('.btn-editar-creditos').forEach(function(button) {
            button.addEventListener('click', function() {
                var usuarioId = this.getAttribute('data-id');
                var creditos = this.getAttribute('data-creditos');
                
                document.getElementById('usuario_id').value = usuarioId;
                document.getElementById('quantidade_creditos').value = creditos;
                
                openModal('modalEditarCreditos');
            });
        });
        
        // Evento para abrir o modal de edição de plano
        document.querySelectorAll('.btn-editar-plano').forEach(function(button) {
            button.addEventListener('click', function() {
                var usuarioId = this.getAttribute('data-id');
                var planoAtual = this.getAttribute('data-plano');
                
                document.getElementById('usuario_id_plano').value = usuarioId;
                document.getElementById('novo_plano').value = planoAtual;
                
                openModal('modalEditarPlano');
            });
        });
        
        // Validação do formulário de créditos
        document.getElementById('formEditarCreditos').addEventListener('submit', function(e) {
            var creditos = document.getElementById('quantidade_creditos').value;
            
            if (creditos < 0) {
                alert('A quantidade de créditos não pode ser negativa!');
                e.preventDefault();
            }
        });
        
        // Validação do formulário de plano
        document.getElementById('formEditarPlano').addEventListener('submit', function(e) {
            var plano = document.getElementById('novo_plano').value;
            
            if (!plano) {
                alert('Por favor, selecione um plano!');
                e.preventDefault();
            }
        });
    }
    
    // Função universal para abrir modais
    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        
        // Tenta usar Bootstrap se disponível
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        // Tenta usar jQuery + Bootstrap se disponível
        else if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
            jQuery('#' + modalId).modal('show');
        }
        // Fallback para mostrar o modal sem animação
        else {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Cria um backdrop manualmente
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'backdrop-' + modalId;
            document.body.appendChild(backdrop);
            
            // Adiciona listeners para fechar o modal
            var closeButtons = modal.querySelectorAll('[data-dismiss="modal"]');
            closeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    closeModal(modalId);
                });
            });
        }
    }
    
    // Função para fechar modal manualmente
    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        var backdrop = document.getElementById('backdrop-' + modalId);
        
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        if (backdrop) {
            document.body.removeChild(backdrop);
        }
    }
    
    // Verifica se jQuery está disponível e inicializa
    if (typeof jQuery === 'undefined') {
        console.warn('jQuery não encontrado, usando JavaScript puro');
        initializeModals();
    } else {
        // jQuery disponível, verifica Bootstrap
        if (typeof jQuery.fn.modal === 'undefined') {
            console.warn('Bootstrap Modal não encontrado, usando JavaScript puro');
            initializeModals();
        } else {
            // Tudo OK, inicializa normalmente
            initializeModals();
        }
    }
});
</script>
    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="..\files\bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
    <!-- Toastr notifications -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Chart js -->
    <script type="text/javascript" src="..\files\bower_components\chart.js\js\Chart.js"></script>
    <!-- amchart js -->
    <script src="..\files\assets\pages\widget\amchart\amcharts.js"></script>
    <script src="..\files\assets\pages\widget\amchart\serial.js"></script>
    <script src="..\files\assets\pages\widget\amchart\light.js"></script>
    <script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\SmoothScroll.js"></script>
    <!-- custom js -->
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializa DataTables
            $('#botTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"
                },
                "responsive": true
            });
            
            // Inicializa tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Verifica se há uma mensagem de status na URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const mensagem = urlParams.get('mensagem');
            
            if (status && mensagem) {
                if (status === 'sucesso') {
                    toastr.success(mensagem);
                } else if (status === 'erro') {
                    toastr.error(mensagem);
                }
            }
        });
        
        // Função para mostrar o print do WhatsApp
        function mostrarPrintWhatsApp(usuario, nome) {
            $('#printWhatsAppModalLabel').text('Print do WhatsApp: ' + nome);
            $('#printWhatsAppContainer').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Carregando...</span></div><p class="mt-2">Carregando print do WhatsApp...</p>');
            $('#printWhatsAppModal').modal('show');
            
            // Carregar a imagem do print do WhatsApp (usando a abordagem do qrcode.php)
            $.ajax({
                url: 'puxar_imagem.php',
                type: 'GET',
                data: {
                    usuario: usuario,
                    opcao: 'PrintWhatsApp'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success){
                        $('#printWhatsAppContainer').html('<img src="' + response.img_path + '" class="img-fluid" alt="Print do WhatsApp" id="printWhatsAppImage">');
                        
                        // Configurar o botão de download
                        $('#btnDownloadPrint').off('click').on('click', function() {
                            // Criar um link temporário para download
                            var link = document.createElement('a');
                            link.href = response.img_path;
                            link.download = 'print_whatsapp_' + usuario + '.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        $('#printWhatsAppContainer').html('<div class="alert alert-danger">Erro ao carregar a imagem. Tente novamente mais tarde.</div>');
                    }
                },
                error: function() {
                    $('#printWhatsAppContainer').html('<div class="alert alert-danger">Erro ao carregar a imagem. Tente novamente mais tarde.</div>');
                }
            });
        }
        
        // Função para executar ações nos bots
        function executarAcao(usuario, comando) {
            // Mostrar indicador de carregamento
            toastr.info('Executando ação ' + comando + '...');
            
            $.ajax({
                type: "POST",
                url: "processa_acao.php",
                data: { usuario: usuario, comando: comando },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'sucesso') {
                        toastr.success(response.mensagem || 'Ação executada com sucesso!');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        //toastr.error(response.mensagem || 'Erro ao executar ação. Tente novamente.');
                         setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr, status, error) {
                    //console.error('Erro na requisição AJAX:', error);
                    //toastr.error('Erro de comunicação com o servidor. Tente novamente.');
                     setTimeout(function() {
                            location.reload();
                        }, 1500);
                }
            });
        }
        
        // Função para confirmar exclusão
        function confirmarExclusao(usuario, nome) {
            $('#botName').text(nome);
            $('#deleteUsuario').val(usuario);
            $('#deleteModal').modal('show');
            
            // Configurar o formulário de exclusão para usar AJAX
            $('#deleteForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        if (response.status === 'sucesso') {
                            toastr.success(response.mensagem || 'Bot excluído com sucesso!');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            //toastr.error(response.mensagem || 'Erro ao excluir bot. Tente novamente.');
                        }
                    },
                    error: function() {
                        $('#deleteModal').modal('hide');
                        //toastr.error('Erro de comunicação com o servidor. Tente novamente.');
                    }
                });
            });
        }
        
        // Função para mostrar a imagem do QR Code do bot
        function mostrarImagemBot(usuario, nome) {
            $('#imagemBotModalLabel').text('QR Code do Bot: ' + nome);
            $('#qrCodeContainer').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Carregando...</span></div><p class="mt-2">Carregando QR Code...</p>');
            $('#imagemBotModal').modal('show');
            
            // Carregar a imagem do QR Code (usando a abordagem do qrcode.php)
            function loadQRCode() {
                var qrCodeUrl = 'qr/gerar_qrcode.php?usuario=' + usuario + '&opcao=Gerarcode';
                $.get(qrCodeUrl, function(data) {
                    $('#qrCodeContainer').html(data);
                    
                    // Configurar o botão de download (usando a URL da imagem gerada)
                    $('#btnDownloadQR').off('click').on('click', function() {
                        var qrImg = $('#qrCodeContainer img').attr('src');
                        if (qrImg) {
                            // Se temos uma imagem base64 embutida
                            var link = document.createElement('a');
                            link.href = qrImg;
                            link.download = 'qrcode_' + usuario + '.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            // Se temos uma imagem carregada via src
                            var link = document.createElement('a');
                            link.href = qrCodeUrl;
                            link.download = 'qrcode_' + usuario + '.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    });
                }).fail(function() {
                    $('#qrCodeContainer').html('<div class="alert alert-danger">Erro ao carregar o QR Code. Tente novamente mais tarde.</div>');
                });
            }
            
            // Carregar o QR Code e solicitar geração via API
            loadQRCode();
            
            // Solicitar geração do QR Code via API
            $.ajax({
                url: 'processa_acao.php',
                type: 'POST',
                data: {
                    usuario: usuario,
                    comando: 'gerar_qrcode'
                },
                success: function(response) {
                    console.log('QR Code gerado com sucesso');
                    loadQRCode(); // Recarregar após geração
                },
                error: function() {
                    console.error('Erro ao solicitar geração de QR Code');
                }
            });
        }
    </script>

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

<?php
include 'pcoded.php';
include 'erro.php';
?>