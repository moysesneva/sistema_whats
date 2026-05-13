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

// 1) Processa o POST de salvar chave OpenAI
if (isset($_POST['salvar_openai']) && !empty($_POST['valor_chave_openai']) && !empty($_POST['plano_openai'])) {
    $chave_openai = mysqli_real_escape_string($conn, $_POST['valor_chave_openai']);
    $plano_selecionado = mysqli_real_escape_string($conn, $_POST['plano_openai']);
    
    // Verifica se já existe uma chave diferente (Gemini) no mesmo plano
    $sql_verifica_conflito = "
        SELECT nome 
        FROM chave_ia_geral 
        WHERE plano = '{$plano_selecionado}' AND nome != 'openai'
        LIMIT 1
    ";
    $res_conflito = mysqli_query($conn, $sql_verifica_conflito);
    
    if ($res_conflito && mysqli_num_rows($res_conflito) > 0) {
        $empresa_conflito = mysqli_fetch_assoc($res_conflito)['nome'];
        echo '<div class="alert alert-danger">Erro: Já existe uma chave da ' . ucfirst($empresa_conflito) . ' no ' . $plano_selecionado . '. Não é possível ter chaves de empresas diferentes no mesmo plano!</div>';
    } else {
        // Verifica se a chave já existe no mesmo plano
        $sql_verifica_openai = "
            SELECT id 
            FROM chave_ia_geral 
            WHERE chave = '{$chave_openai}' AND nome = 'openai' AND plano = '{$plano_selecionado}'
            LIMIT 1
        ";
        $res_verifica_openai = mysqli_query($conn, $sql_verifica_openai);
        
        if ($res_verifica_openai && mysqli_num_rows($res_verifica_openai) > 0) {
            echo '<div class="alert alert-warning">Esta chave da OpenAI já está cadastrada no ' . $plano_selecionado . '!</div>';
        } else {
            // Insere a nova chave
            $sql_insere_openai = "
                INSERT INTO chave_ia_geral (chave, nome, plano, date)
                VALUES (
                    '{$chave_openai}',
                    'openai',
                    '{$plano_selecionado}',
                    NOW()
                )
            ";
            if (mysqli_query($conn, $sql_insere_openai)) {
                echo '<div class="alert alert-success">Chave da OpenAI salva com sucesso no ' . $plano_selecionado . '!</div>';
            } else {
                echo '<div class="alert alert-danger">Erro ao salvar chave da OpenAI: ' . mysqli_error($conn) . '</div>';
            }
        }
    }
}

// 2) Processa o POST de salvar chave Gemini
if (isset($_POST['salvar_gemini']) && !empty($_POST['valor_chave_gemini']) && !empty($_POST['plano_gemini'])) {
    $chave_gemini = mysqli_real_escape_string($conn, $_POST['valor_chave_gemini']);
    $plano_selecionado = mysqli_real_escape_string($conn, $_POST['plano_gemini']);
    
    // Verifica se já existe uma chave diferente (OpenAI) no mesmo plano
    $sql_verifica_conflito = "
        SELECT nome 
        FROM chave_ia_geral 
        WHERE plano = '{$plano_selecionado}' AND nome != 'gemini'
        LIMIT 1
    ";
    $res_conflito = mysqli_query($conn, $sql_verifica_conflito);
    
    if ($res_conflito && mysqli_num_rows($res_conflito) > 0) {
        $empresa_conflito = mysqli_fetch_assoc($res_conflito)['nome'];
        echo '<div class="alert alert-danger">Erro: Já existe uma chave da ' . ucfirst($empresa_conflito) . ' no ' . $plano_selecionado . '. Não é possível ter chaves de empresas diferentes no mesmo plano!</div>';
    } else {
        // Verifica se a chave já existe no mesmo plano
        $sql_verifica_gemini = "
            SELECT id 
            FROM chave_ia_geral 
            WHERE chave = '{$chave_gemini}' AND nome = 'gemini' AND plano = '{$plano_selecionado}'
            LIMIT 1
        ";
        $res_verifica_gemini = mysqli_query($conn, $sql_verifica_gemini);
        
        if ($res_verifica_gemini && mysqli_num_rows($res_verifica_gemini) > 0) {
            echo '<div class="alert alert-warning">Esta chave da Gemini já está cadastrada no ' . $plano_selecionado . '!</div>';
        } else {
            // Insere a nova chave
            $sql_insere_gemini = "
                INSERT INTO chave_ia_geral (chave, nome, plano, date)
                VALUES (
                    '{$chave_gemini}',
                    'gemini',
                    '{$plano_selecionado}',
                    NOW()
                )
            ";
            if (mysqli_query($conn, $sql_insere_gemini)) {
                echo '<div class="alert alert-success">Chave da Gemini salva com sucesso no ' . $plano_selecionado . '!</div>';
            } else {
                echo '<div class="alert alert-danger">Erro ao salvar chave da Gemini: ' . mysqli_error($conn) . '</div>';
            }
        }
    }
}

// 3) Processa o POST de apagar chave OpenAI
if (isset($_POST['apagar_openai']) && !empty($_POST['id_chave'])) {
    $id_chave = (int) $_POST['id_chave'];
    
    $sql_apagar_openai = "
        DELETE FROM chave_ia_geral 
        WHERE id = {$id_chave} AND nome = 'openai'
        LIMIT 1
    ";
    
    if (mysqli_query($conn, $sql_apagar_openai)) {
        echo '<div class="alert alert-success">Chave da OpenAI removida com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao remover chave da OpenAI: ' . mysqli_error($conn) . '</div>';
    }
}

// 4) Processa o POST de apagar chave Gemini
if (isset($_POST['apagar_gemini']) && !empty($_POST['id_chave'])) {
    $id_chave = (int) $_POST['id_chave'];
    
    $sql_apagar_gemini = "
        DELETE FROM chave_ia_geral 
        WHERE id = {$id_chave} AND nome = 'gemini'
        LIMIT 1
    ";
    
    if (mysqli_query($conn, $sql_apagar_gemini)) {
        echo '<div class="alert alert-success">Chave da Gemini removida com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao remover chave da Gemini: ' . mysqli_error($conn) . '</div>';
    }
}

// Inclui a conexão
include 'conn.php';
?>

<!-- CHAVES DA OPENAI -->
<div class="card">
    <div class="card-header">
        <h5>Adicionar Chave da OpenAI</h5>
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
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Chave da OpenAI</label>
                        <input type="text" name="valor_chave_openai" class="form-control" placeholder="Digite a chave da OpenAI" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plano</label>
                        <select name="plano_openai" class="form-control" required>
                            <option value="">Selecione o plano</option>
                            <option value="plano1">Plano 1</option>
                            <option value="plano2">Plano 2</option>
                            <option value="plano3">Plano 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" name="salvar_openai" class="btn btn-primary">
                <i class="feather icon-save"></i> Salvar Chave OpenAI
            </button>
        </form>
    </div>
</div>

<!-- CHAVES DA GEMINI -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h5>Adicionar Chave da Gemini</h5>
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
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Chave da Gemini</label>
                        <input type="text" name="valor_chave_gemini" class="form-control" placeholder="Digite a chave da Gemini" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plano</label>
                        <select name="plano_gemini" class="form-control" required>
                            <option value="">Selecione o plano</option>
                            <option value="plano1">Plano 1</option>
                            <option value="plano2">Plano 2</option>
                            <option value="plano3">Plano 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" name="salvar_gemini" class="btn btn-success">
                <i class="feather icon-save"></i> Salvar Chave Gemini
            </button>
        </form>
    </div>
</div>

<!-- RESUMO DOS PLANOS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h5>Resumo das Chaves por Plano</h5>
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
            <?php
            $planos = ['plano1', 'plano2', 'plano3'];
            foreach ($planos as $plano) {
                // Busca informações do plano
                $sql_plano = "
                    SELECT nome, COUNT(*) as total_chaves
                    FROM chave_ia_geral 
                    WHERE plano = '{$plano}'
                    GROUP BY nome
                ";
                $res_plano = mysqli_query($conn, $sql_plano);
                
                echo "<div class='col-md-4'>";
                echo "<div class='card bg-light'>";
                echo "<div class='card-body text-center'>";
                echo "<h6>" . strtoupper($plano) . "</h6>";
                
                if ($res_plano && mysqli_num_rows($res_plano) > 0) {
                    while ($row_plano = mysqli_fetch_assoc($res_plano)) {
                        $empresa = ucfirst($row_plano['nome']);
                        $total = $row_plano['total_chaves'];
                        $badge_class = ($row_plano['nome'] == 'openai') ? 'badge-primary' : 'badge-success';
                        echo "<span class='badge {$badge_class}'>{$empresa}: {$total} chave(s)</span><br>";
                    }
                } else {
                    echo "<span class='text-muted'>Nenhuma chave cadastrada</span>";
                }
                
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- CHAVES CADASTRADAS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h5>Todas as Chaves Cadastradas</h5>
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
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Chave</th>
                        <th>Plano</th>
                        <th>Data de Inserção</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Busca todas as chaves no banco
                    $sql_todas_chaves = "
                        SELECT id, chave, nome, plano, date
                        FROM chave_ia_geral
                        ORDER BY plano ASC, nome ASC, date DESC
                    ";
                    $resultado_todas = mysqli_query($conn, $sql_todas_chaves);
                    
                    if ($resultado_todas && mysqli_num_rows($resultado_todas) > 0) {
                        while ($row_chave = mysqli_fetch_assoc($resultado_todas)) {
                            // Mascara a chave exibindo apenas os primeiros e últimos 4 caracteres
                            $chave_mascarada = substr($row_chave['chave'], 0, 4) . "..." . substr($row_chave['chave'], -4);
                            $data_formatada = date('d/m/Y H:i', strtotime($row_chave['date']));
                            
                            // Define badge da empresa
                            $empresa_badge = ($row_chave['nome'] == 'openai') ? 
                                '<span class="badge badge-primary">OpenAI</span>' : 
                                '<span class="badge badge-success">Gemini</span>';
                            
                            // Define badge do plano
                            $plano_colors = [
                                'plano1' => 'badge-info',
                                'plano2' => 'badge-warning', 
                                'plano3' => 'badge-danger'
                            ];
                            $plano_class = $plano_colors[$row_chave['plano']] ?? 'badge-secondary';
                            $plano_badge = '<span class="badge ' . $plano_class . '">' . strtoupper($row_chave['plano']) . '</span>';
                            
                            echo "<tr>";
                            echo "<td>{$empresa_badge}</td>";
                            echo "<td>" . htmlspecialchars($chave_mascarada, ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>{$plano_badge}</td>";
                            echo "<td>{$data_formatada}</td>";
                            echo "<td>";
                            
                            // Botão de excluir específico para cada empresa
                            if ($row_chave['nome'] == 'openai') {
                                echo "<form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='id_chave' value='{$row_chave['id']}'>
                                        <button type='submit' name='apagar_openai' class='btn btn-danger btn-sm' 
                                                onclick='return confirm(\"Tem certeza que deseja apagar esta chave da OpenAI?\");'>
                                            <i class='feather icon-trash'></i> Apagar
                                        </button>
                                      </form>";
                            } else {
                                echo "<form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='id_chave' value='{$row_chave['id']}'>
                                        <button type='submit' name='apagar_gemini' class='btn btn-danger btn-sm' 
                                                onclick='return confirm(\"Tem certeza que deseja apagar esta chave da Gemini?\");'>
                                            <i class='feather icon-trash'></i> Apagar
                                        </button>
                                      </form>";
                            }
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Nenhuma chave cadastrada</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Validação dos formulários
    var forms = document.querySelectorAll('form[method="post"]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var chave = form.querySelector('input[name*="chave"]');
            var plano = form.querySelector('select[name*="plano"]');
            
            if (chave && plano) {
                if (!chave.value.trim()) {
                    alert('Por favor, digite a chave!');
                    e.preventDefault();
                    return;
                }
                
                if (!plano.value) {
                    alert('Por favor, selecione um plano!');
                    e.preventDefault();
                    return;
                }
                
                // Validação básica do formato da chave
                if (chave.value.length < 10) {
                    alert('A chave parece estar muito curta. Verifique se está correta.');
                    e.preventDefault();
                    return;
                }
            }
        });
    });
});
</script>

<style>
.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    margin: 2px;
}

.badge-primary { background-color: #007bff; }
.badge-success { background-color: #28a745; }
.badge-info { background-color: #17a2b8; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; }
.badge-secondary { background-color: #6c757d; }

.card .card-body {
    padding: 1rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.card-header h5 {
    margin-bottom: 0;
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