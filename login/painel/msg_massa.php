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


$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
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

<?php include 'footer.php'; ?>