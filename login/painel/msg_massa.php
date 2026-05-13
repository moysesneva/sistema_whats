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










































<!-- CSS específico para o trecho de mensagem em massa - AJUSTADO -->
<style>
.whatsapp-preview {
    background: linear-gradient(135deg, #075e54, #128c7e);
    border-radius: 15px;
    padding: 15px;
    color: white;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.chat-bubble {
    background: #dcf8c6;
    color: #000;
    padding: 10px 14px;
    border-radius: 18px 18px 4px 18px;
    margin: 8px 0;
    max-width: 250px;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    word-wrap: break-word;
    font-size: 14px;
}

.chat-bubble::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: -8px;
    width: 0;
    height: 0;
    border: 8px solid transparent;
    border-top-color: #dcf8c6;
    border-bottom: 0;
    margin-left: -8px;
    margin-bottom: -8px;
}

.media-preview-whatsapp {
    background: #e8e8e8;
    border-radius: 8px;
    padding: 8px;
    margin: 5px 0;
    font-size: 12px;
    color: #666;
    text-align: center;
    max-width: 200px;
    max-height: 150px;
    overflow: hidden;
    position: relative;
}

.media-preview-whatsapp img {
    width: 100%;
    height: auto;
    border-radius: 6px;
    max-height: 120px;
    object-fit: cover;
}

.media-preview-whatsapp video {
    width: 100%;
    height: auto;
    border-radius: 6px;
    max-height: 120px;
    object-fit: cover;
}

.media-preview-whatsapp .audio-preview {
    background: #075e54;
    color: white;
    padding: 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
}

.play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.file-upload-area {
    border: 2px dashed #007bff;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    margin: 10px 0;
}

.file-upload-area:hover {
    background: #e3f2fd;
    border-color: #0056b3;
}

.file-upload-area.dragover {
    background: #e3f2fd;
    border-color: #0056b3;
    transform: scale(1.02);
}

.contact-selector {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 8px;
    background: #f9f9f9;
}

.contact-item {
    padding: 6px 10px;
    margin: 2px 0;
    background: white;
    border-radius: 4px;
    border: 1px solid #eee;
    transition: all 0.2s ease;
    font-size: 13px;
}

.contact-item:hover {
    background: #f0f8ff;
    border-color: #007bff;
}

.schedule-section {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 12px;
    margin: 10px 0;
}

.btn-whatsapp {
    background: #25d366;
    border-color: #25d366;
    color: white;
}

.btn-whatsapp:hover {
    background: #128c7e;
    border-color: #128c7e;
    color: white;
}

.btn-ai-rewrite {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-ai-rewrite:hover {
    background: linear-gradient(45deg, #764ba2 0%, #667eea 100%);
    color: white;
    transform: translateY(-1px);
}

.media-type-tabs {
    display: flex;
    gap: 8px;
    margin: 10px 0;
    flex-wrap: wrap;
}

.media-tab {
    padding: 6px 12px;
    border: 2px solid #ddd;
    border-radius: 15px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 12px;
}

.media-tab.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.media-tab:hover {
    border-color: #007bff;
}

.card-mensagem-massa {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: none;
}

.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
}

.ai-toggle-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 12px;
    margin: 10px 0;
}
</style>

<!-- NOVA SEÇÃO: Sistema de Mensagem em Massa -->
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>📢 Sistema de Mensagem em Massa</h4>
                    <span>Envie mensagens, imagens, áudios e vídeos para seus contatos</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="row">
        <!-- Formulário Principal -->
        <div class="col-lg-8 col-md-12">
            <div class="card card-mensagem-massa">
                <div class="card-header">
                    <h5>💬 Configurar Mensagem</h5>
                </div>
                <div class="card-block">
                    <form action="msgg_massa_envio.php" method="post" enctype="multipart/form-data" id="formMensagemMassa">
                        
                        <!-- Seleção de Destinatários -->
                        <div class="form-section">
                            <h6>👥 Selecionar Destinatários</h6>
                            <div class="form-radio">
                                <div class="radio radiofill radio-primary radio-inline">
                                    <label>
                                        <input type="radio" name="tipoEnvio" value="todos" checked onchange="toggleContactSelector()">
                                        <i class="helper"></i>📋 Enviar para TODOS os contatos
                                    </label>
                                </div>
                                <div class="radio radiofill radio-primary radio-inline">
                                    <label>
                                        <input type="radio" name="tipoEnvio" value="selecionados" onchange="toggleContactSelector()">
                                        <i class="helper"></i>✅ Selecionar contatos específicos
                                    </label>
                                </div>
                            </div>

                            <!-- Lista de Contatos -->
                            <div id="contactSelector" class="contact-selector" style="display: none;">
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="selectAllContacts()">Selecionar Todos</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="deselectAllContacts()">Desmarcar Todos</button>
                                    </div>
                                </div>
                                <!-- Lista de contatos -->
                                <div class="contact-item">
                                    <input type="checkbox" name="contatos[]" value="1" id="contact1">
                                    <label for="contact1"> João Silva - (11) 99999-0001</label>
                                </div>
                                <div class="contact-item">
                                    <input type="checkbox" name="contatos[]" value="2" id="contact2">
                                    <label for="contact2"> Maria Santos - (11) 99999-0002</label>
                                </div>
                                <div class="contact-item">
                                    <input type="checkbox" name="contatos[]" value="3" id="contact3">
                                    <label for="contact3"> Pedro Costa - (11) 99999-0003</label>
                                </div>
                                <div class="contact-item">
                                    <input type="checkbox" name="contatos[]" value="4" id="contact4">
                                    <label for="contact4"> Ana Oliveira - (11) 99999-0004</label>
                                </div>
                                <div class="contact-item">
                                    <input type="checkbox" name="contatos[]" value="5" id="contact5">
                                    <label for="contact5"> Carlos Ferreira - (11) 99999-0005</label>
                                </div>
                            </div>
                        </div>

                        <!-- Habilitar IA -->
                        <div class="ai-toggle-section">
                            <div class="form-group">
                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input type="checkbox" id="habilitarIA" name="habilitarIA" value="1">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>🤖 Habilitar reescrita com IA</span>
                                    </label>
                                </div>
                                <small>A IA pode ajudar a melhorar sua mensagem automaticamente</small>
                            </div>
                        </div>

                        <!-- Campos dinâmicos e Texto -->
                        <div class="form-section">
                            <div class="form-group">
                                <label>Campos dinâmicos disponíveis:</label><br>
                                <button type="button" class="btn btn-secondary btn-sm m-1" onclick="inserirCampo('mensagemTexto', '{nome}')">{nome}</button>
                                <button type="button" class="btn btn-secondary btn-sm m-1" onclick="inserirCampo('mensagemTexto', '{saudacao}')">{saudacao}</button>
                                <button type="button" class="btn btn-secondary btn-sm m-1" onclick="inserirCampo('mensagemTexto', '{datahora}')">{datahora}</button>
                                <button type="button" class="btn btn-info btn-sm m-1" onclick="carregarModeloMassa()">📋 Modelo</button>
                            </div>

                            <div class="form-group">
                                <label for="mensagemTexto">Texto da Mensagem</label>
                                <textarea class="form-control" id="mensagemTexto" name="mensagemTexto" placeholder="Digite sua mensagem aqui..." rows="4"></textarea>
                                <div id="textoOpcional" class="mt-2">
                                    <small class="text-muted">💡 Dica: Para áudio, apenas o arquivo será enviado (sem texto)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Seleção de Mídia -->
                        <div class="form-section">
                            <label>📎 Anexar Mídia (opcional)</label>
                            <div class="media-type-tabs">
                                <div class="media-tab active" onclick="selectMediaType('none', this)">🚫 Sem mídia</div>
                                <div class="media-tab" onclick="selectMediaType('image', this)">🖼️ Imagem</div>
                                <div class="media-tab" onclick="selectMediaType('video', this)">🎥 Vídeo</div>
                                <div class="media-tab" onclick="selectMediaType('audio', this)">🎵 Áudio</div>
                            </div>
                            <input type="hidden" name="tipoMidia" id="tipoMidia" value="none">

                            <!-- Área de upload -->
                            <div id="uploadArea" class="file-upload-area" style="display: none;" onclick="document.getElementById('arquivoMidia').click()">
                                <input type="file" id="arquivoMidia" name="arquivoMidia" style="display: none;" onchange="handleFileSelect(this)">
                                <div id="uploadText">
                                    <i class="feather icon-upload"></i><br>
                                    <span>Clique aqui ou arraste o arquivo</span><br>
                                    <small class="text-muted">Formatos aceitos: JPG, PNG, MP4, MP3, WAV</small>
                                </div>
                                <div id="fileSelected" style="display: none;">
                                    <i class="feather icon-check-circle text-success"></i><br>
                                    <span id="fileName"></span><br>
                                    <small class="text-success">Arquivo selecionado!</small>
                                </div>
                            </div>
                        </div>

                        <!-- Agendamento -->
                        <div class="schedule-section">
                            <h6>⏰ Opções de Envio</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-radio">
                                        <div class="radio radiofill radio-success">
                                            <label>
                                                <input type="radio" name="opcaoEnvio" value="agora" checked onchange="toggleSchedule()">
                                                <i class="helper"></i>🚀 Enviar Agora
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-warning">
                                            <label>
                                                <input type="radio" name="opcaoEnvio" value="agendar" onchange="toggleSchedule()">
                                                <i class="helper"></i>📅 Agendar Envio
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="scheduleFields" style="display: none;">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Data</label>
                                                <input type="date" class="form-control" id="dataAgendamento" name="dataAgendamento">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Hora</label>
                                                <input type="time" class="form-control" id="horaAgendamento" name="horaAgendamento">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-whatsapp waves-effect waves-light btn-lg">
                                <i class="feather icon-send"></i>
                                <span id="btnText">Enviar Mensagem em Massa</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview do WhatsApp -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>📱 Prévia do WhatsApp</h5>
                </div>
                <div class="card-block p-0">
                    <div class="whatsapp-preview">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #075e54; font-weight: bold; font-size: 12px;">EU</span>
                            </div>
                            <div class="ml-2">
                                <div style="font-weight: bold; font-size: 14px;">Você</div>
                                <small class="text-light">online</small>
                            </div>
                        </div>
                        
                        <div class="chat-bubble" id="previewMensagem">
                            <div id="previewMedia" class="media-preview-whatsapp" style="display: none;">
                                <span>📎 Mídia anexada</span>
                            </div>
                            <div id="previewTexto">[Sua mensagem aparecerá aqui]</div>
                            <div class="text-right mt-1">
                                <small style="color: #666; font-size: 10px;">
                                    <span id="previewHora">15:30</span> ✓✓
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidades -->
<script>
    // Função para inserir campos dinâmicos
    function inserirCampo(idCampo, placeholder) {
        var campoTexto = document.getElementById(idCampo);
        var cursorPos = campoTexto.selectionStart;
        var textoAntes = campoTexto.value.substring(0, cursorPos);
        var textoDepois = campoTexto.value.substring(cursorPos);
        
        campoTexto.value = textoAntes + placeholder + textoDepois;
        campoTexto.focus();
        
        var novaPosicao = cursorPos + placeholder.length;
        campoTexto.selectionStart = novaPosicao;
        campoTexto.selectionEnd = novaPosicao;
        
        updatePreview();
    }

    // Carregar modelo de mensagem
    function carregarModeloMassa() {
        document.getElementById('mensagemTexto').value = 
            "{saudacao} {nome}! 👋\n\n" +
            "Esperamos que você esteja bem!\n\n" +
            "Esta mensagem foi enviada em {datahora}.\n\n" +
            "Atenciosamente,\nNossa Equipe 💙";
        updatePreview();
    }

    // Toggle seletor de contatos
    function toggleContactSelector() {
        var radios = document.getElementsByName('tipoEnvio');
        var selecionados = false;
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked && radios[i].value === 'selecionados') {
                selecionados = true;
                break;
            }
        }
        var selector = document.getElementById('contactSelector');
        selector.style.display = selecionados ? 'block' : 'none';
    }

    // Selecionar/Desselecionar contatos
    function selectAllContacts() {
        var checkboxes = document.querySelectorAll('input[name="contatos[]"]');
        checkboxes.forEach(cb => cb.checked = true);
    }

    function deselectAllContacts() {
        var checkboxes = document.querySelectorAll('input[name="contatos[]"]');
        checkboxes.forEach(cb => cb.checked = false);
    }

    // Selecionar tipo de mídia
    function selectMediaType(type, element) {
        document.querySelectorAll('.media-tab').forEach(tab => tab.classList.remove('active'));
        element.classList.add('active');
        
        document.getElementById('tipoMidia').value = type;
        
        var uploadArea = document.getElementById('uploadArea');
        var textoOpcional = document.getElementById('textoOpcional');
        var mensagemTexto = document.getElementById('mensagemTexto');
        
        uploadArea.style.display = type === 'none' ? 'none' : 'block';
        
        // Controlar visibilidade do texto baseado no tipo de mídia
        if (type === 'audio') {
            textoOpcional.innerHTML = '<small class="text-warning">⚠️ Para áudio, apenas o arquivo será enviado (sem texto)</small>';
            mensagemTexto.disabled = true;
            mensagemTexto.placeholder = 'Texto não será enviado com áudio';
        } else {
            textoOpcional.innerHTML = '<small class="text-muted">💡 Dica: Vídeos e imagens podem ter texto como legenda</small>';
            mensagemTexto.disabled = false;
            mensagemTexto.placeholder = 'Digite sua mensagem aqui...';
        }
        
        if (type !== 'none') {
            updateUploadText(type);
        }
        
        // Limpar arquivo anterior quando mudar tipo
        document.getElementById('arquivoMidia').value = '';
        document.getElementById('uploadText').style.display = 'block';
        document.getElementById('fileSelected').style.display = 'none';
        
        updatePreview();
    }

    // Atualizar texto de upload
    function updateUploadText(type) {
        var formats = {
            'image': 'JPG, PNG, GIF',
            'video': 'MP4, AVI, MOV',
            'audio': 'MP3, WAV, OGG'
        };
        
        document.getElementById('uploadText').innerHTML = `
            <i class="feather icon-upload"></i><br>
            <span>Clique aqui ou arraste o arquivo de ${type}</span><br>
            <small class="text-muted">Formatos aceitos: ${formats[type]}</small>
        `;
    }

    // Handle file selection
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var tipoMidia = document.getElementById('tipoMidia').value;
            
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('uploadText').style.display = 'none';
            document.getElementById('fileSelected').style.display = 'block';
            
            // Criar preview real do arquivo
            createFilePreview(file, tipoMidia);
            updatePreview();
        }
    }

    // Criar preview real do arquivo
    function createFilePreview(file, tipoMidia) {
        var reader = new FileReader();
        var previewMedia = document.getElementById('previewMedia');
        
        reader.onload = function(e) {
            var fileUrl = e.target.result;
            
            if (tipoMidia === 'image') {
                previewMedia.innerHTML = '<img src="' + fileUrl + '" alt="Preview da imagem">';
            } else if (tipoMidia === 'video') {
                previewMedia.innerHTML = 
                    '<div style="position: relative;">' +
                    '<video src="' + fileUrl + '" style="width: 100%; height: auto; border-radius: 6px; max-height: 120px; object-fit: cover;"></video>' +
                    '<div class="play-button">▶</div>' +
                    '</div>';
            } else if (tipoMidia === 'audio') {
                previewMedia.innerHTML = 
                    '<div class="audio-preview">' +
                    '<i class="feather icon-volume-2"></i> ' +
                    '<span>' + file.name + '</span>' +
                    '</div>';
            }
            
            previewMedia.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
    }

    // Toggle agendamento
    function toggleSchedule() {
        var radios = document.getElementsByName('opcaoEnvio');
        var agendar = false;
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked && radios[i].value === 'agendar') {
                agendar = true;
                break;
            }
        }
        
        var scheduleFields = document.getElementById('scheduleFields');
        var btnText = document.getElementById('btnText');
        
        scheduleFields.style.display = agendar ? 'block' : 'none';
        btnText.textContent = agendar ? 'Agendar Mensagem em Massa' : 'Enviar Mensagem em Massa';
    }

    // Atualizar preview
    function updatePreview() {
        var texto = document.getElementById('mensagemTexto').value;
        var tipoMidia = document.getElementById('tipoMidia').value;
        var arquivo = document.getElementById('arquivoMidia').files[0];
        
        // Para áudio, não mostrar texto
        if (tipoMidia === 'audio') {
            document.getElementById('previewTexto').style.display = 'none';
        } else {
            document.getElementById('previewTexto').style.display = 'block';
            document.getElementById('previewTexto').textContent = texto || '[Sua mensagem aparecerá aqui]';
        }
        
        // Atualizar mídia apenas se não foi criada pelo createFilePreview
        var previewMedia = document.getElementById('previewMedia');
        if (tipoMidia === 'none' || !arquivo) {
            previewMedia.style.display = 'none';
        }
        // Se tem arquivo, o preview já foi criado em createFilePreview
        
        // Atualizar hora
        var agora = new Date();
        var hora = agora.getHours().toString().padStart(2, '0');
        var minuto = agora.getMinutes().toString().padStart(2, '0');
        document.getElementById('previewHora').textContent = `${hora}:${minuto}`;
    }

    // Preview da mensagem
    function previewMessage() {
        var texto = document.getElementById('mensagemTexto').value || 'Sem texto';
        var tipoMidia = document.getElementById('tipoMidia').value;
        var tipoEnvio = document.querySelector('input[name="tipoEnvio"]:checked').value;
        
        alert('Preview da mensagem:\n\n' + 
              'Texto: ' + texto + '\n' +
              'Mídia: ' + (tipoMidia !== 'none' ? 'Sim (' + tipoMidia + ')' : 'Não') + '\n' +
              'Destinatários: ' + (tipoEnvio === 'todos' ? 'Todos os contatos' : 'Contatos selecionados'));
    }

    // Event listeners
    document.getElementById('mensagemTexto').addEventListener('input', updatePreview);

    // Drag and drop
    var uploadArea = document.getElementById('uploadArea');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        var files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('arquivoMidia').files = files;
            handleFileSelect(document.getElementById('arquivoMidia'));
        }
    });

    // Inicializar
    updatePreview();
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