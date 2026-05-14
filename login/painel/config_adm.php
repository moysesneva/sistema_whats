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



include 'bloqueio.php';
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
                                        </li><?php if($tipo != 4){?>
                                        <li>
                                            <a href="perfil.php">
                                                <i class="feather icon-user"></i> Perfil
                                            </a>
                                        </li>
                                       <?php } ?>
                                        
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



<?php


$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $ip_vps  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $chave  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}

?>

 <div class="container">
    <h2>Configurar Painel</h2>

<?php if ($webhook && $chave){ ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="feather icon-settings"></i> Configurações Atuais</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label font-weight-bold">Chave da API:</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $chave; ?></p>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-4 col-form-label font-weight-bold">Seu Site:</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $webhook; ?></p>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-4 col-form-label font-weight-bold">IP da VPS:</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $ip_vps; ?></p>
                </div>
            </div>
            
            <?php if ($porta) { ?>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label font-weight-bold">Porta:</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $porta; ?></p>
                </div>
            </div>
            <?php } ?>
            
            <?php if ($link_pagamento){ ?>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label font-weight-bold">Link de pagamento:</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $link_pagamento; ?></p>
                </div>
            </div>
            <?php } ?>
            
            <div class="mt-4">
                <form action="config_adm_confirma.php" method="POST">
                    <input type="hidden" name="modo_edicao" value="1">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="feather icon-edit"></i> Editar Configurações
                    </button>
                </form>

<?php
$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor = $rows_config['ip_vps'];
    $porta = $rows_config['porta'];
    $nova_porta = $rows_config['nova_porta'];
    $token = $rows_config['chave'];
    $chave_painel = $rows_config['chave_painel'];
    $webhook = $rows_config['webhook'];
    $google = $rows_config['google'];
    $link_pagamento = $rows_config['link_pagamento'];
}

$site = 'https://web.whatsapp.com';
$api = 'https://editacodigo.com.br/api2/';

// Certifique-se de que o webhook termine com uma barra se necessário
if (substr($webhook, -1) != '/') {
    $webhook .= '/';
}

// Formate o token exatamente como no exemplo
// Exemplo format: CPF_13535958709_NOME_VICTOR_NERY
// (Se não estiver neste formato, você precisará formatá-lo adequadamente)

?>
<?php


function corrigirUrl($url) {
    // Remove barras duplas, exceto após http(s):
    return preg_replace("#([^:])/{2,}#", "$1/", $url);
}

$webhook1 = $webhook.'/login/painel/api/recebe.php'; 
$webhook1 = corrigirUrl($webhook1);


?>
  
  
  
  
  
      <style>
   .api-button {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            flex: 1;
            max-width: 200px;
        }
        
        .api-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .api-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .notification {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .command-section {
            display: none;
            margin-top: 30px;
        }
        
        .command-pre {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 15px;
            overflow-x: auto;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .copy-button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        
        .copy-button:hover {
            background-color: #45a049;
        }
        
        .loading {
            display: none;
            margin-left: 10px;
            color: #666;
        }
        
        .button-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 600px) {
            .button-container {
                flex-direction: column;
                align-items: center;
            }
            
            .api-button {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    
   <?php
   
function substituirBarrasDuplas($texto) {
    return str_replace('//', '/', $texto);
}   
   
   
$webhook_node = "https://agenda.tasmota.com.br/agenda/login/painel/api/teste/api.php";
$webhook_mensagens = substituirBarrasDuplas($webhook.'/login/painel/api/recebe.php');


$webhook_validate = substituirBarrasDuplas($webhook.'/login/painel/api/cron.php');
$token_node = $chave;
$porta_node = $porta;

$webhook_python =  substituirBarrasDuplas($webhook.'/login/painel/api/recebe.php');
$token_python = "$chave";
$porta_python = "$porta";
$site_python = "https://web.whatsapp.com";
$api_python = "https://editacodigo.com.br/api2/";
?>


<div class="container">
    <h2 style="margin-bottom: 20px; color: #333;">Escolha a API que vai usar:</h2>
    
    <div class="button-container">
        <button id="nodeButton" class="api-button">
            API em Node
        </button>
        
        <button id="pythonButton" class="api-button">
            API em Python
        </button>
    </div>
    
    <span id="loading" class="loading" style="display:none;">Processando...</span>
    
    <div id="notification" class="notification" style="display:none;">
        ✅ API ativada com sucesso! Clique no botão abaixo para copiar o comando.
    </div>
    
    <div id="commandSection" class="command-section" style="display:none;">
        <h3>Comando de Instalação:</h3>
        <pre id="comando" class="command-pre"></pre>
        <button id="copyButton" class="copy-button">Copiar comando</button>
    </div>
</div>

<script>
    const nodeButton = document.getElementById('nodeButton');
    const pythonButton = document.getElementById('pythonButton');
    const notification = document.getElementById('notification');
    const commandSection = document.getElementById('commandSection');
    const comandoElement = document.getElementById('comando');
    const copyButton = document.getElementById('copyButton');
    const loading = document.getElementById('loading');

    // Variáveis vindas do PHP
    const webhookNode = "<?php echo $webhook_node; ?>";
    const webhookMensagens = "<?php echo $webhook_mensagens; ?>";
    const webhookValidate = "<?php echo $webhook_validate; ?>";
    const tokenNode = "<?php echo $token_node; ?>";
    const portaNode = "<?php echo $porta_node; ?>";

    const webhookPython = "<?php echo $webhook_python; ?>";
    const tokenPython = "<?php echo $token_python; ?>";
    const portaPython = "<?php echo $porta_python; ?>";
    const sitePython = "<?php echo $site_python; ?>";
    const apiPython = "<?php echo $api_python; ?>";

    function gerarComando(tipoAPI) {
        if (tipoAPI === 'Node') {
            return `curl -o instalador.sh "https://tasmota.com.br/api/instalador.txt" && \\
sed -i 's/\\r$//' instalador.sh && \\
chmod +x instalador.sh && \\
PORTA="${portaNode}" \\
TOKEN="${tokenNode}" \\
WEBHOOK_FUNCOES="${webhookNode}" \\
WEBHOOK_MENSAGENS="${webhookMensagens}" \\
WEBHOOK_VALIDATE="${webhookValidate}" \\
./instalador.sh`;
        } else if (tipoAPI === 'Python') {
            return `curl -o instalador.sh "https://editacodigo.com.br/download/api/install/instalacao.sh.txt" && \\
chmod +x instalador.sh && \\
WEBHOOK="${webhookPython}" \\
TOKEN="${tokenPython}" \\
PORTA="${portaPython}" \\
SITE="${sitePython}" \\
API="${apiPython}" \\
./instalador.sh`;
        }
    }

    function mostrarComando(comando) {
        loading.style.display = 'none';
        notification.style.display = 'block';
        commandSection.style.display = 'block';
        comandoElement.textContent = comando;
    }

    nodeButton.addEventListener('click', () => {
        loading.style.display = 'inline';
        notification.style.display = 'none';
        commandSection.style.display = 'none';
        setTimeout(() => {
            const comando = gerarComando('Node');
            mostrarComando(comando);
        }, 500);
    });

    pythonButton.addEventListener('click', () => {
        loading.style.display = 'inline';
        notification.style.display = 'none';
        commandSection.style.display = 'none';
        setTimeout(() => {
            const comando = gerarComando('Python');
            mostrarComando(comando);
        }, 500);
    });

    copyButton.addEventListener('click', () => {
        navigator.clipboard.writeText(comandoElement.textContent)
            .then(() => {
                alert('Comando copiado!');
            })
            .catch(err => {
                console.error('Erro ao copiar:', err);
            });
    });


        // Função para processar clique nos botões
        async function processarEscolhaAPI(tipoAPI) {
            // Desabilita ambos os botões e mostra loading
            nodeButton.disabled = true;
            pythonButton.disabled = true;
            loading.style.display = 'inline';
            
            try {
                // Faz o POST para postapi.php (não espera resposta)
                fetch('postapi.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `login=${encodeURIComponent(login)}&tipo_api=${encodeURIComponent(tipoAPI)}&webhook=${encodeURIComponent(webhook1)}&token=${encodeURIComponent(token)}&porta=${encodeURIComponent(porta)}&site=${encodeURIComponent(site)}&api=${encodeURIComponent(api)}`
                });
                
                // Mostra a notificação imediatamente (sem esperar resposta)
                notification.innerHTML = `✅ API ${tipoAPI} ativada com sucesso! Clique no botão abaixo para copiar o comando.`;
                notification.style.display = 'block';
                
                // Gera comando específico para o tipo de API
                const comandoEspecifico = gerarComando(tipoAPI);
                
                // Mostra o comando após um pequeno delay
                setTimeout(() => {
                    comandoElement.textContent = comandoEspecifico;
                    commandSection.style.display = 'block';
                    
                    // Atualiza a função de copiar com o comando específico
                    copyButton.onclick = function() {
                        navigator.clipboard.writeText(comandoEspecifico).then(function() {
                            // Feedback visual
                            const originalText = copyButton.textContent;
                            copyButton.textContent = 'Copiado!';
                            copyButton.style.backgroundColor = '#2196F3';
                            
                            setTimeout(() => {
                                copyButton.textContent = originalText;
                                copyButton.style.backgroundColor = '#4CAF50';
                            }, 2000);
                        }).catch(function(err) {
                            // Fallback para navegadores mais antigos
                            const textArea = document.createElement('textarea');
                            textArea.value = comandoEspecifico;
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            
                            copyButton.textContent = 'Copiado!';
                            setTimeout(() => {
                                copyButton.textContent = 'Copiar comando';
                            }, 2000);
                        });
                    };
                }, 500);
                
            } catch (error) {
                console.log('POST enviado para postapi.php');
                // Mesmo se der erro, mostra o comando (já que não precisamos esperar resposta)
                notification.innerHTML = `✅ API ${tipoAPI} ativada! Clique no botão abaixo para copiar o comando.`;
                notification.style.display = 'block';
                
                // Gera comando específico para o tipo de API
                const comandoEspecifico = gerarComando(tipoAPI);
                
                setTimeout(() => {
                    comandoElement.textContent = comandoEspecifico;
                    commandSection.style.display = 'block';
                    
                    // Atualiza a função de copiar com o comando específico
                    copyButton.onclick = function() {
                        navigator.clipboard.writeText(comandoEspecifico).then(function() {
                            const originalText = copyButton.textContent;
                            copyButton.textContent = 'Copiado!';
                            copyButton.style.backgroundColor = '#2196F3';
                            
                            setTimeout(() => {
                                copyButton.textContent = originalText;
                                copyButton.style.backgroundColor = '#4CAF50';
                            }, 2000);
                        }).catch(function(err) {
                            const textArea = document.createElement('textarea');
                            textArea.value = comandoEspecifico;
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            
                            copyButton.textContent = 'Copiado!';
                            setTimeout(() => {
                                copyButton.textContent = 'Copiar comando';
                            }, 2000);
                        });
                    };
                }, 500);
            } finally {
                // Reabilita os botões e esconde loading
                nodeButton.disabled = false;
                pythonButton.disabled = false;
                loading.style.display = 'none';
            }
        }
        
        // Event listeners para os botões
        nodeButton.addEventListener('click', function() {
            processarEscolhaAPI('Node');
        });
        
        pythonButton.addEventListener('click', function() {
            processarEscolhaAPI('Python');
        });
        
        // Função para copiar o comando
        copyButton.addEventListener('click', function() {
            navigator.clipboard.writeText(comando).then(function() {
                // Feedback visual
                const originalText = copyButton.textContent;
                copyButton.textContent = 'Copiado!';
                copyButton.style.backgroundColor = '#2196F3';
                
                setTimeout(() => {
                    copyButton.textContent = originalText;
                    copyButton.style.backgroundColor = '#4CAF50';
                }, 2000);
            }).catch(function(err) {
                // Fallback para navegadores mais antigos
                const textArea = document.createElement('textarea');
                textArea.value = comando;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                copyButton.textContent = 'Copiado!';
                setTimeout(() => {
                    copyButton.textContent = 'Copiar comando';
                }, 2000);
            });
        });
    </script> 
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
<script>
document.getElementById('copyButton').addEventListener('click', function() {
    var commandText = document.getElementById('comando').innerText;
    navigator.clipboard.writeText(commandText).then(function() {
        alert('Comando copiado com sucesso!');
    }, function(err) {
        alert('Erro ao copiar: ' + err);
    });
});
</script>

            </div>
        </div>

    </div>





    
<?php
if($tipo == 4){
?>
    <div class="row">
        <div class="col-md-6">
            <form action="finalizar_instalacao.php" method="POST">
                <input type="hidden" name="ip_vps" value="<?= $ip_vps; ?>">
                <input type="hidden" name="porta" value="<?= $porta ? $porta : '2222'; ?>">
                <input type="hidden" name="chave" value="<?= $chave; ?>">
                <input type="hidden" name="webhook" value="<?= $webhook; ?>">
                <button type="submit" class="btn btn-success btn-lg btn-block mb-3">
                    <i class="feather icon-check-circle"></i> Finalizar Instalação
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <form action="config_adm_confirma.php" method="POST">
                <input type="hidden" name="webhook" value="0">
                <button type="submit" class="btn btn-warning btn-lg btn-block mb-3" id="btnEditar">
                    <i class="feather icon-edit-2"></i> Editar
                </button>
            </form>
        </div>
    </div>
<?php
}
?>
</div>

<!-- Custom CSS para estilização -->
<style>
    .container {
        max-width: 800px;
        margin: auto;
        background-color: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #1e3a8a;
        font-weight: bold;
        margin-bottom: 25px;
        text-align: center;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 15px;
    }

    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 6px 6px 0 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control-static {
        padding-top: 7px;
        font-weight: 500;
        color: #495057;
    }

    .btn-lg {
        padding: 12px 20px;
        font-weight: 600;
    }

    .btn i {
        margin-right: 8px;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeeba;
    }
</style>



    <?php
}
    ?>











<?php
if (empty($chave) || empty($webhook) || isset($_GET['modo']) && $_GET['modo'] == 'edicao'){
?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="feather icon-settings"></i> Configuração do Sistema</h5>
    </div>
    <div class="card-body">
        <!-- Formulário único para editar todas as configurações -->
        <form action="config_adm_confirma.php" method="POST" class="mb-4">
            <input type="hidden" name="editar_tudo" value="1">
            
            <div class="form-group">
                <label for="ip_vps" class="font-weight-bold">IP da VPS:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="feather icon-server"></i></span>
                    </div>
                    <input type="text" class="form-control" id="ip_vps" name="ip_vps" 
                           value="<?php if(isset($ip_vps)) { echo $ip_vps; } ?>" 
                           placeholder="Digite o IP da VPS (ex: 192.168.1.1)" required>
                </div>
                <small class="form-text text-muted">Digite o endereço IP da sua VPS onde o banco de dados está hospedado.</small>
            </div>
            
            <div class="form-group">
                <label for="porta" class="font-weight-bold">Porta:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="feather icon-hash"></i></span>
                    </div>
                    <input type="text" class="form-control" id="porta" name="porta" 
                           value="<?php if(isset($porta)) { echo $porta; } else { echo '2222'; } ?>" 
                           placeholder="Digite a porta (ex: 2222)" required>
                </div>
                <small class="form-text text-muted">Digite a porta utilizada para conexão com o servidor.</small>
            </div>
            
            <div class="form-group">
                <label for="webhook" class="font-weight-bold">Webhook / Site:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="feather icon-globe"></i></span>
                    </div>
                    <input type="url" class="form-control" id="webhook" name="webhook"
                           value="<?php if(isset($webhook)) { echo $webhook; } ?>" 
                           placeholder="Digite o URL do Webhook (ex: https://seusite.com/webhook)" required>
                </div>
                <small class="form-text text-muted">Insira o URL completo do seu site ou webhook.</small>
            </div>
            
            <div class="form-group">
                <label for="chave" class="font-weight-bold">Chave da API Edita Código:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="feather icon-key"></i></span>
                    </div>
                    <input type="text" class="form-control" id="chave" name="chave"  
                           value="<?php if(isset($chave)) { echo $chave; } ?>" 
                           placeholder="Digite a chave da API" required>
                </div>
                <small class="form-text text-muted">
                    Não tem uma chave? <a href="https://editacodigo.com.br/usuarios/token" target="_blank" class="text-primary">
                    <i class="feather icon-external-link"></i> Clique aqui para obter sua chave</a>.
                </small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="feather icon-save"></i> Salvar Todas as Configurações
            </button>
        </form>
    </div>
</div>
<?php
}
?>





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



<?php

include 'pcoded.php';
include'erro.php';

?>