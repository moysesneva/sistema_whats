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

// Processa inserção de novo módulo de crédito
if (isset($_POST['adicionar_credito']) && !empty($_POST['nome_modulo']) && !empty($_POST['valor_credito'])) {
    $nome_modulo = mysqli_real_escape_string($conn, $_POST['nome_modulo']);
    $valor_credito = (int) $_POST['valor_credito'];
    $data_atual = date('Y-m-d H:i:s');
    
    // Verifica se o módulo já existe (apenas por nome, independente da versão)
    $sql_verifica = "
        SELECT id 
        FROM modulos_lista 
        WHERE nome_modulo = '{$nome_modulo}'
        LIMIT 1
    ";
    $res_verifica = mysqli_query($conn, $sql_verifica);
    
    if ($res_verifica && mysqli_num_rows($res_verifica) > 0) {
        echo '<div class="alert alert-warning">
                <i class="feather icon-alert-triangle"></i> 
                Este nome de módulo já existe! Por favor, escolha um nome diferente.
              </div>';
    } else {
        // Insere o novo módulo de crédito
        $sql_insere = "
            INSERT INTO modulos_lista (nome_modulo, versao, date_install, creditos)
            VALUES (
                '{$nome_modulo}',
                'creditos',
                '{$data_atual}',
                {$valor_credito}
            )
        ";
        
        if (mysqli_query($conn, $sql_insere)) {
            echo '<div class="alert alert-success">
                    <i class="feather icon-check-circle"></i> 
                    Módulo de crédito adicionado com sucesso!
                  </div>';
        } else {
            echo '<div class="alert alert-danger">
                    <i class="feather icon-x-circle"></i> 
                    Erro ao adicionar módulo: ' . mysqli_error($conn) . '
                  </div>';
        }
    }
}

// Processa exclusão de módulo de crédito
if (isset($_POST['excluir_credito']) && !empty($_POST['id_modulo'])) {
    $id_modulo = (int) $_POST['id_modulo'];
    
    $sql_excluir = "
        DELETE FROM modulos_lista 
        WHERE id = {$id_modulo} AND versao = 'creditos'
        LIMIT 1
    ";
    
    if (mysqli_query($conn, $sql_excluir)) {
        echo '<div class="alert alert-success">
                <i class="feather icon-check-circle"></i> 
                Módulo de crédito excluído com sucesso!
              </div>';
    } else {
        echo '<div class="alert alert-danger">
                <i class="feather icon-x-circle"></i> 
                Erro ao excluir módulo: ' . mysqli_error($conn) . '
              </div>';
    }
}

// Processa edição de módulo de crédito
if (isset($_POST['editar_credito']) && !empty($_POST['id_modulo_edit']) && !empty($_POST['nome_modulo_edit']) && !empty($_POST['valor_credito_edit'])) {
    $id_modulo = (int) $_POST['id_modulo_edit'];
    $nome_modulo = mysqli_real_escape_string($conn, $_POST['nome_modulo_edit']);
    $valor_credito = (int) $_POST['valor_credito_edit'];
    
    // Verifica se o novo nome já existe em outro módulo
    $sql_verifica_edicao = "
        SELECT id 
        FROM modulos_lista 
        WHERE nome_modulo = '{$nome_modulo}' AND id != {$id_modulo}
        LIMIT 1
    ";
    $res_verifica_edicao = mysqli_query($conn, $sql_verifica_edicao);
    
    if ($res_verifica_edicao && mysqli_num_rows($res_verifica_edicao) > 0) {
        echo '<div class="alert alert-warning">
                <i class="feather icon-alert-triangle"></i> 
                Este nome de módulo já existe! Por favor, escolha um nome diferente.
              </div>';
    } else {
        $sql_editar = "
            UPDATE modulos_lista 
            SET nome_modulo = '{$nome_modulo}', creditos = {$valor_credito}
            WHERE id = {$id_modulo} AND versao = 'creditos'
            LIMIT 1
        ";
        
        if (mysqli_query($conn, $sql_editar)) {
            echo '<div class="alert alert-success">
                    <i class="feather icon-check-circle"></i> 
                    Módulo de crédito editado com sucesso!
                  </div>';
        } else {
            echo '<div class="alert alert-danger">
                    <i class="feather icon-x-circle"></i> 
                    Erro ao editar módulo: ' . mysqli_error($conn) . '
                  </div>';
        }
    }
}

// Inclui a conexão
include 'conn.php';
?>

<!-- ADICIONAR NOVO MÓDULO DE CRÉDITO -->
<div class="card">
    <div class="card-header">
        <h5>Adicionar Módulo de Crédito</h5>
        <div class="card-header-right">
            <ul class="list-unstyled card-option">
                <li><i class="feather icon-maximize full-card"></i></li>
                <li><i class="feather icon-minus minimize-card"></i></li>
                <li><i class="feather icon-refresh-cw reload-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-block">
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome do Módulo</label>
                        <input type="text" name="nome_modulo" class="form-control" placeholder="Ex: Pacote 100 Créditos" required>
                        <small class="form-text text-muted">O nome deve ser único em todo o sistema.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Quantidade de Créditos</label>
                        <input type="number" name="valor_credito" class="form-control" placeholder="Ex: 100" min="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" name="adicionar_credito" class="btn btn-primary btn-block">
                            <i class="feather icon-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- LISTA DE MÓDULOS DE CRÉDITO -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h5>Módulos de Crédito Cadastrados</h5>
        <div class="card-header-right">
            <span class="badge badge-info">
                <?php
                $sql_count = "SELECT COUNT(*) as total FROM modulos_lista WHERE versao = 'creditos'";
                $res_count = mysqli_query($conn, $sql_count);
                $count_data = mysqli_fetch_assoc($res_count);
                echo $count_data['total'] . ' módulos';
                ?>
            </span>
            <ul class="list-unstyled card-option">
                <li><i class="feather icon-maximize full-card"></i></li>
                <li><i class="feather icon-minus minimize-card"></i></li>
                <li><i class="feather icon-refresh-cw reload-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Módulo</th>
                        <th>Quantidade de Créditos</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Busca apenas módulos de crédito
                    $sql_creditos = "
                        SELECT id, nome_modulo, creditos, date_install
                        FROM modulos_lista
                        WHERE versao = 'creditos'
                        ORDER BY date_install DESC
                    ";
                    $resultado_creditos = mysqli_query($conn, $sql_creditos);
                    
                    if ($resultado_creditos && mysqli_num_rows($resultado_creditos) > 0) {
                        while ($row_credito = mysqli_fetch_assoc($resultado_creditos)) {
                            $data_formatada = date('d/m/Y H:i', strtotime($row_credito['date_install']));
                            
                            echo "<tr>";
                            echo "<td><strong>{$row_credito['id']}</strong></td>";
                            echo "<td>" . htmlspecialchars($row_credito['nome_modulo'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td><span class='badge badge-success'>{$row_credito['creditos']} créditos</span></td>";
                            echo "<td>{$data_formatada}</td>";
                            echo "<td>
                                    <button type='button' class='btn btn-sm btn-primary btn-editar-modulo' 
                                            data-id='{$row_credito['id']}' 
                                            data-nome='" . htmlspecialchars($row_credito['nome_modulo'], ENT_QUOTES, 'UTF-8') . "' 
                                            data-creditos='{$row_credito['creditos']}' 
                                            data-toggle='tooltip' title='Editar Módulo'>
                                        <i class='feather icon-edit'></i>
                                    </button>
                                    <form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='id_modulo' value='{$row_credito['id']}'>
                                        <button type='submit' name='excluir_credito' class='btn btn-sm btn-danger' 
                                                onclick='return confirm(\"Tem certeza que deseja excluir este módulo de crédito?\n\nEsta ação não pode ser desfeita!\");' 
                                                data-toggle='tooltip' title='Excluir Módulo'>
                                            <i class='feather icon-trash'></i>
                                        </button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>
                                <i class='feather icon-database'></i><br>
                                Nenhum módulo de crédito cadastrado
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Edição de Módulo -->
<div class="modal fade" id="modalEditarModulo" tabindex="-1" role="dialog" aria-labelledby="modalEditarModuloLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarModuloLabel">Editar Módulo de Crédito</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarModulo" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" id="id_modulo_edit" name="id_modulo_edit">
                    
                    <div class="form-group">
                        <label for="nome_modulo_edit">Nome do Módulo:</label>
                        <input type="text" class="form-control" id="nome_modulo_edit" name="nome_modulo_edit" required>
                        <small class="form-text text-muted">O nome deve ser único em todo o sistema.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor_credito_edit">Quantidade de Créditos:</label>
                        <input type="number" class="form-control" id="valor_credito_edit" name="valor_credito_edit" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="feather icon-x"></i> Cancelar
                    </button>
                    <button type="submit" name="editar_credito" class="btn btn-primary">
                        <i class="feather icon-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    
    // Função para inicializar os eventos
    function initializeEvents() {
        console.log('Inicializando eventos dos módulos de crédito');
        
        // Inicializa os tooltips se disponível
        if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Evento para abrir o modal de edição
        document.querySelectorAll('.btn-editar-modulo').forEach(function(button) {
            button.addEventListener('click', function() {
                var moduloId = this.getAttribute('data-id');
                var moduloNome = this.getAttribute('data-nome');
                var moduloCreditos = this.getAttribute('data-creditos');
                
                document.getElementById('id_modulo_edit').value = moduloId;
                document.getElementById('nome_modulo_edit').value = moduloNome;
                document.getElementById('valor_credito_edit').value = moduloCreditos;
                
                openModal('modalEditarModulo');
            });
        });
        
        // Validação do formulário de edição
        document.getElementById('formEditarModulo').addEventListener('submit', function(e) {
            var nome = document.getElementById('nome_modulo_edit').value.trim();
            var creditos = document.getElementById('valor_credito_edit').value;
            
            if (!nome) {
                alert('Por favor, digite o nome do módulo!');
                e.preventDefault();
                return;
            }
            
            if (nome.length < 3) {
                alert('O nome do módulo deve ter pelo menos 3 caracteres!');
                e.preventDefault();
                return;
            }
            
            if (creditos < 1) {
                alert('A quantidade de créditos deve ser maior que zero!');
                e.preventDefault();
                return;
            }
        });
    }
    
    // Função universal para abrir modais
    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        
        // Tenta usar Bootstrap 5 se disponível
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        // Tenta usar jQuery + Bootstrap 4 se disponível
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
            
            // Fechar modal clicando no backdrop
            backdrop.addEventListener('click', function() {
                closeModal(modalId);
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
    
    // Inicializa os eventos
    initializeEvents();
    
    // Validação adicional do formulário de adição
    var formAdicionar = document.querySelector('form[method="post"]');
    if (formAdicionar) {
        formAdicionar.addEventListener('submit', function(e) {
            var nomeModulo = document.querySelector('input[name="nome_modulo"]').value.trim();
            var valorCredito = document.querySelector('input[name="valor_credito"]').value;
            
            if (!nomeModulo) {
                alert('Por favor, digite o nome do módulo!');
                e.preventDefault();
                return;
            }
            
            if (nomeModulo.length < 3) {
                alert('O nome do módulo deve ter pelo menos 3 caracteres!');
                e.preventDefault();
                return;
            }
            
            if (valorCredito < 1) {
                alert('A quantidade de créditos deve ser maior que zero!');
                e.preventDefault();
                return;
            }
        });
    }
    
    // Limpar campo de nome ao focar
    var inputNome = document.querySelector('input[name="nome_modulo"]');
    if (inputNome) {
        inputNome.addEventListener('focus', function() {
            this.select();
        });
    }
});
</script>

<style>
.badge-success {
    background-color: #28a745;
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.btn-group .btn {
    margin-right: 5px;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.card-header h5 {
    margin-bottom: 0;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.alert {
    border-radius: 0.5rem;
    border: none;
    padding: 1rem 1.25rem;
}

.alert i {
    margin-right: 0.5rem;
}

.form-text {
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}

.card-header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}
</style>







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