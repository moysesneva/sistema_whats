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
<?php include 'header.php'; ?>




    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->


























































<!-- CSS específico para o sistema de grupos -->
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

.group-selector {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 8px;
    background: #f9f9f9;
}

.group-item {
    padding: 8px 12px;
    margin: 3px 0;
    background: white;
    border-radius: 6px;
    border: 1px solid #eee;
    transition: all 0.2s ease;
    font-size: 13px;
    display: flex;
    align-items: center;
}

.group-item:hover {
    background: #f0f8ff;
    border-color: #007bff;
}

.group-icon {
    width: 30px;
    height: 30px;
    background: #25d366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    margin-right: 10px;
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

.btn-search-groups {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
    border: none;
    color: white;
}

.btn-search-groups:hover {
    background: linear-gradient(45deg, #ee5a52, #ff6b6b);
    color: white;
}

.btn-extract-groups {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    border: none;
    color: white;
}

.btn-extract-groups:hover {
    background: linear-gradient(45deg, #44a08d, #4ecdc4);
    color: white;
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

.card-grupos {
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

.nav-tabs-custom {
    border-bottom: 2px solid #25d366;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #666;
    font-weight: 500;
    padding: 12px 20px;
}

.nav-tabs-custom .nav-link.active {
    background: #25d366;
    color: white;
    border-radius: 8px 8px 0 0;
}

.search-results {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #f9f9f9;
    margin: 10px 0;
}

.group-result-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    background: white;
    margin: 2px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.group-result-item:hover {
    background: #f0f8ff;
}

.group-result-item:last-child {
    border-bottom: none;
}
</style>

<!-- Sistema de Grupos WhatsApp -->
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>👥 Sistema de Grupos WhatsApp</h4>
                    <span>Extraia, busque e envie mensagens em massa para grupos</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <!-- Tabs de Navegação -->
    <ul class="nav nav-tabs nav-tabs-custom" id="gruposTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="extrair-tab" data-toggle="tab" href="#extrair" role="tab">
                📤 Extrair Grupos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="buscar-tab" data-toggle="tab" href="#buscar" role="tab">
                🔍 Buscar Grupos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="enviar-tab" data-toggle="tab" href="#enviar" role="tab">
                📢 Enviar Mensagem
            </a>
        </li>
    </ul>

    <div class="tab-content" id="gruposTabsContent">
        <!-- TAB 1: EXTRAIR GRUPOS -->
        <div class="tab-pane fade show active" id="extrair" role="tabpanel">
            <div class="row mt-3">
                <div class="col-lg-8 col-md-12">
                    <div class="card card-grupos">
                        <div class="card-header">
                            <h5>📤 Extrair Grupos do WhatsApp</h5>
                        </div>
                        <div class="card-block">
                            <form action="extrair_grupos.php" method="post" id="formExtrairGrupos">
                                
                                <div class="form-section">
                                    <h6>⚙️ Configurações de Extração</h6>
                                    
                                    <div class="form-group">
                                        <label>Tipos de grupos para extrair:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiposGrupos[]" value="todos" id="todosGrupos" checked>
                                            <label class="form-check-label" for="todosGrupos">
                                                📋 Todos os grupos
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiposGrupos[]" value="admin" id="gruposAdmin">
                                            <label class="form-check-label" for="gruposAdmin">
                                                👑 Apenas grupos onde sou admin
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiposGrupos[]" value="ativo" id="gruposAtivos">
                                            <label class="form-check-label" for="gruposAtivos">
                                                🟢 Apenas grupos ativos (últimas 24h)
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="filtroNome">Filtrar por nome (opcional):</label>
                                        <input type="text" class="form-control" id="filtroNome" name="filtroNome" placeholder="Digite palavras-chave para filtrar grupos">
                                    </div>

                                    <div class="form-group">
                                        <label for="limiteGrupos">Limite máximo de grupos:</label>
                                        <select class="form-control" id="limiteGrupos" name="limiteGrupos">
                                            <option value="50">50 grupos</option>
                                            <option value="100" selected>100 grupos</option>
                                            <option value="200">200 grupos</option>
                                            <option value="500">500 grupos</option>
                                            <option value="todos">Todos os grupos</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h6>📊 Dados para extrair:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="nome" id="extrairNome" checked>
                                                <label class="form-check-label" for="extrairNome">Nome do grupo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="descricao" id="extrairDescricao" checked>
                                                <label class="form-check-label" for="extrairDescricao">Descrição</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="participantes" id="extrairParticipantes">
                                                <label class="form-check-label" for="extrairParticipantes">Número de participantes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="foto" id="extrairFoto">
                                                <label class="form-check-label" for="extrairFoto">Foto do grupo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="convite" id="extrairConvite">
                                                <label class="form-check-label" for="extrairConvite">Link de convite</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="dadosExtrair[]" value="atividade" id="extrairAtividade">
                                                <label class="form-check-label" for="extrairAtividade">Última atividade</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-extract-groups btn-lg waves-effect waves-light">
                                        <i class="feather icon-download"></i>
                                        Extrair Grupos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>📋 Informações</h5>
                        </div>
                        <div class="card-block">
                            <div class="alert alert-info">
                                <h6>💡 Como funciona:</h6>
                                <ul class="mb-0" style="font-size: 13px;">
                                    <li>Conecta ao seu WhatsApp</li>
                                    <li>Extrai dados dos grupos</li>
                                    <li>Salva em formato CSV/Excel</li>
                                    <li>Permite filtros personalizados</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6>⚠️ Importante:</h6>
                                <p class="mb-0" style="font-size: 13px;">
                                    Respeite a privacidade dos grupos e use apenas para fins legítimos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: BUSCAR GRUPOS -->
        <div class="tab-pane fade" id="buscar" role="tabpanel">
            <div class="row mt-3">
                <div class="col-lg-8 col-md-12">
                    <div class="card card-grupos">
                        <div class="card-header">
                            <h5>🔍 Buscar Grupos na Internet</h5>
                        </div>
                        <div class="card-block">
                            <form action="buscar_grupos.php" method="post" id="formBuscarGrupos">
                                
                                <div class="form-section">
                                    <h6>🎯 Parâmetros de Busca</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="termoBusca">Termo de busca:</label>
                                                <input type="text" class="form-control" id="termoBusca" name="termoBusca" placeholder="Ex: grupos de vendas, network marketing, etc." required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="limiteBusca">Limite de resultados:</label>
                                                <select class="form-control" id="limiteBusca" name="limiteBusca">
                                                    <option value="20">20 grupos</option>
                                                    <option value="50" selected>50 grupos</option>
                                                    <option value="100">100 grupos</option>
                                                    <option value="200">200 grupos</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Categorias de interesse:</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="negocios" id="catNegocios">
                                                    <label class="form-check-label" for="catNegocios">💼 Negócios</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="vendas" id="catVendas">
                                                    <label class="form-check-label" for="catVendas">🛒 Vendas</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="marketing" id="catMarketing">
                                                    <label class="form-check-label" for="catMarketing">📈 Marketing</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="educacao" id="catEducacao">
                                                    <label class="form-check-label" for="catEducacao">📚 Educação</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="tecnologia" id="catTecnologia">
                                                    <label class="form-check-label" for="catTecnologia">💻 Tecnologia</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="saude" id="catSaude">
                                                    <label class="form-check-label" for="catSaude">🏥 Saúde</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="entretenimento" id="catEntretenimento">
                                                    <label class="form-check-label" for="catEntretenimento">🎮 Entretenimento</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="esportes" id="catEsportes">
                                                    <label class="form-check-label" for="catEsportes">⚽ Esportes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="outros" id="catOutros">
                                                    <label class="form-check-label" for="catOutros">🌟 Outros</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Filtros avançados:</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filtros[]" value="publicos" id="filtroPublicos" checked>
                                                    <label class="form-check-label" for="filtroPublicos">🌐 Apenas grupos públicos</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filtros[]" value="ativos" id="filtroAtivos">
                                                    <label class="form-check-label" for="filtroAtivos">🟢 Grupos ativos</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filtros[]" value="verificados" id="filtroVerificados">
                                                    <label class="form-check-label" for="filtroVerificados">✅ Grupos verificados</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filtros[]" value="grandes" id="filtroGrandes">
                                                    <label class="form-check-label" for="filtroGrandes">👥 Grupos grandes (500+ membros)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-search-groups btn-lg waves-effect waves-light">
                                        <i class="feather icon-search"></i>
                                        Buscar Grupos
                                    </button>
                                </div>
                            </form>

                            <!-- Resultados da Busca -->
                            <div id="resultadosBusca" class="search-results" style="display: none;">
                                <div class="p-3">
                                    <h6>📋 Resultados da Busca:</h6>
                                    <div id="listaResultados">
                                        <!-- Resultados serão inseridos aqui via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>🎯 Dicas de Busca</h5>
                        </div>
                        <div class="card-block">
                            <div class="alert alert-success">
                                <h6>✅ Termos eficazes:</h6>
                                <ul class="mb-0" style="font-size: 13px;">
                                    <li>"grupos de vendas"</li>
                                    <li>"network marketing"</li>
                                    <li>"empreendedores"</li>
                                    <li>"negócios online"</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6>⚠️ Lembre-se:</h6>
                                <p class="mb-0" style="font-size: 13px;">
                                    Sempre respeite as regras dos grupos e evite spam.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: ENVIAR MENSAGEM EM MASSA -->
        <div class="tab-pane fade" id="enviar" role="tabpanel">
            <div class="row mt-3">
                <!-- Formulário Principal -->
                <div class="col-lg-8 col-md-12">
                    <div class="card card-grupos">
                        <div class="card-header">
                            <h5>📢 Enviar Mensagem em Massa para Grupos</h5>
                        </div>
                        <div class="card-block">
                            <form action="enviar_massa_grupos.php" method="post" enctype="multipart/form-data" id="formMensagemGrupos">
                                
                                <!-- Habilitar IA -->
                                <div class="ai-toggle-section">
                                    <div class="form-group">
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="habilitarIAGrupos" name="habilitarIA" value="1">
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span>🤖 Habilitar reescrita com IA</span>
                                            </label>
                                        </div>
                                        <small>A IA pode ajudar a melhorar sua mensagem automaticamente</small>
                                    </div>
                                </div>

                                <!-- Seleção de Grupos -->
                                <div class="form-section">
                                    <h6>👥 Selecionar Grupos</h6>
                                    <div class="form-radio">
                                        <div class="radio radiofill radio-primary radio-inline">
                                            <label>
                                                <input type="radio" name="tipoEnvioGrupos" value="todos" checked onchange="toggleGroupSelector()">
                                                <i class="helper"></i>📋 Enviar para TODOS os grupos
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-primary radio-inline">
                                            <label>
                                                <input type="radio" name="tipoEnvioGrupos" value="selecionados" onchange="toggleGroupSelector()">
                                                <i class="helper"></i>✅ Selecionar grupos específicos
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Lista de Grupos -->
                                    <div id="groupSelector" class="group-selector" style="display: none;">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="selectAllGroups()">Selecionar Todos</button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="deselectAllGroups()">Desmarcar Todos</button>
                                            </div>
                                        </div>
                                        <!-- Lista de grupos -->
                                        <div class="group-item">
                                            <input type="checkbox" name="grupos[]" value="1" id="group1">
                                            <div class="group-icon">📈</div>
                                            <label for="group1"> Empreendedores BR - 1.2k membros</label>
                                        </div>
                                        <div class="group-item">
                                            <input type="checkbox" name="grupos[]" value="2" id="group2">
                                            <div class="group-icon">💼</div>
                                            <label for="group2"> Negócios Online - 850 membros</label>
                                        </div>
                                        <div class="group-item">
                                            <input type="checkbox" name="grupos[]" value="3" id="group3">
                                            <div class="group-icon">🛒</div>
                                            <label for="group3"> Vendas & Marketing - 2.1k membros</label>
                                        </div>
                                        <div class="group-item">
                                            <input type="checkbox" name="grupos[]" value="4" id="group4">
                                            <div class="group-icon">💻</div>
                                            <label for="group4"> Tech Startup BR - 640 membros</label>
                                        </div>
                                        <div class="group-item">
                                            <input type="checkbox" name="grupos[]" value="5" id="group5">
                                            <div class="group-icon">📚</div>
                                            <label for="group5"> Educação Digital - 920 membros</label>

<?php include 'footer.php'; ?>