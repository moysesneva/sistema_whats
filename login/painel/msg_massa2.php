<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
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
    $usuario_api  = $rows_usuarios['usuario_api'];
    
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

    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fab fa-whatsapp"></i> Mensagens Agendadas WhatsApp</h3>
            <p class="page-subtitle">Envie mensagens personalizadas para seus clientes de forma agendada</p>
        </div>

        <form id="messageForm" action="msg_massa_confirma.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-users"></i> Selecione os Clientes</h5>
                            <div class="ms-auto">
                                <span class="selected-count" id="selectedCount">0 selecionados</span>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="toggleSelectAll()">
                                    <i class="fas fa-check-double"></i> Selecionar Todos
                                </button>
                            </div>
                        </div>

                        <div class="card-body-modern">
                            <!-- Seção de Filtro por Etiquetas -->
                            <div class="tags-filter-section">
                                <h6 class="mb-3">
                                    <i class="fas fa-tags text-primary"></i> 
                                    Filtrar por Etiquetas
                                </h6>
                                
                                <!-- Modo de Filtro -->
                                <div class="filter-mode-selector">
                                    <span class="text-muted">Modo:</span>
                                    <button type="button" class="filter-mode-btn active" data-mode="any" onclick="setFilterMode('any')">
                                        <i class="fas fa-plus-circle"></i> Qualquer etiqueta
                                    </button>
                                    <button type="button" class="filter-mode-btn" data-mode="all" onclick="setFilterMode('all')">
                                        <i class="fas fa-check-double"></i> Todas as etiquetas
                                    </button>
                                    <button type="button" class="filter-mode-btn" data-mode="none" onclick="setFilterMode('none')">
                                        <i class="fas fa-times-circle"></i> Sem filtro
                                    </button>
                                </div>
                                
                                <!-- Tags Disponíveis -->
                                <div class="tags-quick-filter" id="tagsFilter">
                                    <?php
                                    // Buscar todas as etiquetas únicas
                                    $stmt_tags = $conn->prepare("SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(etiqueta, ',', numbers.n), ',', -1)) as tag FROM clientes CROSS JOIN (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8) numbers WHERE usuario_api = ? AND LENGTH(etiqueta) - LENGTH(REPLACE(etiqueta, ',', '')) >= numbers.n - 1 AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(etiqueta, ',', numbers.n), ',', -1)) != '' ORDER BY tag");
                                    $stmt_tags->bind_param("s", $usuario_api);
                                    $stmt_tags->execute();
                                    $resTags = $stmt_tags->get_result();
                                    $stmt_tags->close();
                                    $allTags = [];
                                    while ($row = $resTags->fetch_assoc()) {
                                        $tag = trim($row['tag']);
                                        if (!empty($tag) && !in_array($tag, $allTags)) {
                                            $allTags[] = $tag;
                                        }
                                    }
                                    
                                    // Contar clientes por tag
                                    foreach ($allTags as $tag): 
                                        $stmt_cnt = $conn->prepare("SELECT COUNT(*) as total FROM clientes WHERE usuario_api = ? AND (etiqueta LIKE ?)");
                                        $tag_like = '%' . $tag . '%';
                                        $stmt_cnt->bind_param("ss", $usuario_api, $tag_like);
                                        $stmt_cnt->execute();
                                        $count = $stmt_cnt->get_result()->fetch_assoc()['total'];
                                        $stmt_cnt->close();
                                    ?>
                                    <div class="tag-filter-pill" onclick="toggleTagFilter('<?= htmlspecialchars($tag) ?>')">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($tag) ?> 
                                        <span class="badge bg-secondary ms-1"><?= $count ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <input type="hidden" name="selected_tags" id="selectedTags" value="">
                                <input type="hidden" name="filter_mode" id="filterMode" value="none">
                            </div>

                            <div class="mb-3">
                                <input type="text"
                                       class="form-control form-control-modern"
                                       id="searchClientes"
                                       placeholder="🔍 Buscar cliente por nome, telefone ou etiqueta..."
                                       onkeyup="filterClientes(this.value)">
                            </div>

                            <div class="clientes-container" id="clientesContainer">
                                <?php
                                $stmt_cli2 = $conn->prepare("SELECT id, nome, telefone, etiqueta FROM clientes WHERE usuario_api = ? ORDER BY nome");
                                $stmt_cli2->bind_param("s", $usuario_api);
                                $stmt_cli2->execute();
                                $res = $stmt_cli2->get_result();
                                $stmt_cli2->close();
                                while ($cli = $res->fetch_assoc()):
                                    $id       = $cli['id'];
                                    $nome     = $cli['nome'] ?: 'Sem nome';
                                    $telefone = $cli['telefone'];
                                    $etiqueta = $cli['etiqueta'] ?: '';
                                    
                                    // Processar etiquetas
                                    $etiquetas = array_filter(array_map('trim', explode(',', $etiqueta)));
                                    
                                    // Gera iniciais
                                    $parts    = preg_split('/\s+/', trim($nome));
                                    $iniciais = '';
                                    foreach ($parts as $p) {
                                        $iniciais .= mb_strtoupper(mb_substr($p, 0, 1));
                                    }
                                ?>
                                <div class="cliente-item"
                                     data-id="<?= $id ?>"
                                     data-nome="<?= htmlspecialchars($nome) ?>"
                                     data-telefone="<?= htmlspecialchars($telefone) ?>"
                                     data-etiquetas="<?= htmlspecialchars($etiqueta) ?>"
                                     onclick="toggleCliente(this)">
                                    <button type="button" class="edit-tags-btn" onclick="event.stopPropagation(); openEditTagsModal(<?= $id ?>, '<?= htmlspecialchars($nome) ?>', '<?= htmlspecialchars($etiqueta) ?>')">
                                        <i class="fas fa-tags"></i> Editar
                                    </button>
                                    <div class="cliente-info">
                                        <input type="checkbox" name="clientes[]" value="<?= $id ?>" style="display:none;">
                                        <div class="cliente-avatar"><?= $iniciais ?></div>
                                        <div class="cliente-dados">
                                            <h6><?= htmlspecialchars($nome) ?></h6>
                                            <small>📱 <?= htmlspecialchars($telefone) ?></small>
                                            <?php if (!empty($etiquetas)): ?>
                                            <div class="cliente-tags">
                                                <?php foreach ($etiquetas as $tag): ?>
                                                <span class="cliente-tag-mini"><?= htmlspecialchars($tag) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="fas fa-check-circle text-success" style="display:none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Tipo de Mídia -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-paper-plane"></i> Tipo de Mensagem</h5>
                        </div>
                        <div class="card-body-modern">
                            <div class="media-type-selector">
                                <div class="media-type-btn active" data-type="text" onclick="selectMediaType('text')">
                                    <i class="fas fa-comment"></i>
                                    <div>Texto</div>
                                </div>
                                <div class="media-type-btn" data-type="image" onclick="selectMediaType('image')">
                                    <i class="fas fa-image"></i>
                                    <div>Imagem</div>
                                </div>
                                <div class="media-type-btn" data-type="video" onclick="selectMediaType('video')">
                                    <i class="fas fa-video"></i>
                                    <div>Vídeo</div>
                                </div>
                                <div class="media-type-btn" data-type="audio" onclick="selectMediaType('audio')">
                                    <i class="fas fa-microphone"></i>
                                    <div>Áudio</div>
                                </div>
                                <div class="media-type-btn" data-type="document" onclick="selectMediaType('document')">
                                    <i class="fas fa-file"></i>
                                    <div>Documento</div>
                                </div>
                            </div>

                            <input type="hidden" name="media_type" id="media_type" value="text">

                            <!-- Upload de Arquivo -->
                            <div id="uploadSection" style="display: none;">
                                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Clique para selecionar arquivo ou arraste aqui</p>
                                    <small class="text-muted">Tamanho máximo: 16MB</small>
                                </div>
                                <input type="file" id="fileInput" name="media_file" style="display: none;" 
                                       onchange="handleFileSelect(this)">
                                <div id="filePreview" style="display: none; margin-top: 1rem;"></div>
                            </div>

                            <!-- Campo de Texto/Legenda -->
                            <div class="mt-3" id="textSection">
                                <label class="form-label-modern" for="messageText">
                                    <i class="fas fa-edit"></i> <span id="textLabel">Mensagem</span>
                                </label>
                                
                                <!-- Botões Especiais -->
                                <div class="special-buttons">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <small class="text-muted me-2 mb-1"><strong>Botões Especiais:</strong></small>
                                        
                                        <!-- Variáveis Dinâmicas -->
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{nome}')">
                                            <i class="fas fa-user"></i> Nome do Cliente
                                        </button>
                                        
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{data}')">
                                            <i class="fas fa-calendar-day"></i> Data de Hoje
                                        </button>
                                        
                                        <button type="button" class="special-btn variable" onclick="insertVariable('{telefone}')">
                                            <i class="fas fa-phone"></i> Telefone
                                        </button>
                                        
                                        <!-- Divisor -->
                                        <div style="width: 100%; margin: 0.5rem 0;"></div>
                                        
                                        <!-- IA para reescrever (apenas marcação) --><?php if($inativo){?>
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="usar_ia" id="usarIA" value="1">
                                            <label class="form-check-label special-btn ai" for="usarIA" style="cursor: pointer; margin: 0;">
                                                <i class="fas fa-magic"></i> Reescrever com IA
                                            </label>
                                        </div>
                                        <?php }?>
                                        <button type="button" class="special-btn" onclick="clearText()">
                                            <i class="fas fa-eraser"></i> Limpar Texto
                                        </button>
                                    </div>
                                    
                                    <!-- Preview das Variáveis -->
                                    <div class="variables-preview" id="variablesPreview" style="display: none;">
                                        <small><strong><i class="fas fa-info-circle"></i> Como ficará:</strong></small>
                                        <div id="variablesPreviewText"></div>
                                    </div>
                                </div>
                                
                                <textarea class="form-control form-control-modern" id="messageText" name="message_text" 
                                          rows="4" placeholder="Digite sua mensagem aqui..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Agendamento -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5><i class="fas fa-clock"></i> Opções de Envio</h5>
                        </div>
                        <div class="card-body-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3 send-option-item" data-option="now">
                                        <input class="form-check-input" type="radio" name="send_option" id="sendNow" value="now" checked>
                                        <label class="form-check-label send-option-label" for="sendNow">
                                            <i class="fas fa-bolt text-warning"></i> Enviar Agora
                                        </label>
                                    </div>
                                    <div class="form-check mb-3 send-option-item" data-option="later">
                                        <input class="form-check-input" type="radio" name="send_option" id="sendLater" value="later">
                                        <label class="form-check-label send-option-label" for="sendLater">
                                            <i class="fas fa-calendar-alt text-info"></i> Agendar Envio
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="scheduleInputs" style="display: none;">
                                        <div class="mb-2">
                                            <input type="date" class="form-control form-control-modern" name="schedule_date" id="scheduleDate">
                                        </div>
                                        <div>
                                            <input type="time" class="form-control form-control-modern" name="schedule_time" id="scheduleTime">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botão Avançado -->
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-advanced" id="toggleAdvanced">
                                        <i class="fas fa-cog me-2"></i>Opções Avançadas
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Opções Avançadas -->
                            <div id="advancedOptions" class="advanced-options" style="display: none;">
                                <div class="row">
                                    <!-- Nome da Campanha -->
                                    <div class="col-md-6 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-tag text-primary"></i>
                                            Nome da Campanha
                                        </div>
                                        <input type="text" class="form-control form-control-modern" name="campaign_name" id="campaignName" placeholder="Digite o nome da campanha">
                                    </div>
                                    
                                    <!-- Horário de Funcionamento -->
                                    <div class="col-md-6 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-clock text-success"></i>
                                            Horário de Funcionamento
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="time" class="form-control form-control-modern" name="start_time" id="startTime" value="01:00">
                                            </div>
                                            <div class="col-6">
                                                <input type="time" class="form-control form-control-modern" name="end_time" id="endTime" value="23:00">
                                            </div>
                                        </div>
                                        <small class="text-muted">EX: das 09:00 às 18:00</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Repetição -->
                                    <div class="col-md-12 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-repeat text-info"></i>
                                            Repetição
                                        </div>
                                        <select class="form-control form-control-modern" name="repeat_option" id="repeatOption">
                                            <option value="once">Enviar apenas uma vez</option>
                                            <option value="daily">Repetir diariamente</option>
                                            <option value="weekly">Repetir semanalmente</option>
                                            <option value="monthly">Repetir mensalmente</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Dias da Semana -->
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="section-title">
                                            <i class="fas fa-calendar-week text-danger"></i>
                                            Dias da Semana
                                        </div>
                                        <div class="days-selector">
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="monday" name="days[]" value="1" checked>
                                                <label for="monday" class="day-label">Segunda</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="tuesday" name="days[]" value="2" checked>
                                                <label for="tuesday" class="day-label">Terça</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="wednesday" name="days[]" value="3" checked>
                                                <label for="wednesday" class="day-label">Quarta</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="thursday" name="days[]" value="4" checked>
                                                <label for="thursday" class="day-label">Quinta</label>
                                            </div>
                                            <div class="day-checkbox">
                                                <input type="checkbox" id="friday" name="days[]" value="5" checked>
                                                <label for="friday" class="day-label">Sexta</label>
                                            </div>
                                          <div class="day-checkbox">
                <input type="checkbox" id="saturday" name="days[]" value="6" checked>
                <label for="saturday" class="day-label">Sábado</label>
            </div>
            <div class="day-checkbox">
                <input type="checkbox" id="sunday" name="days[]" value="0" checked>
                <label for="sunday" class="day-label">Domingo</label>
            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Intervalo entre Mensagens -->
                            <div class="schedule-options mt-4">
                                <h6 class="mb-3"><i class="fas fa-stopwatch text-primary"></i> Intervalo entre Mensagens</h6>
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label-modern" for="intervalValue">
                                            <i class="fas fa-hashtag"></i> Quantidade
                                        </label>
                                        <input type="number" class="form-control form-control-modern" 
                                               id="intervalValue" name="interval_value" value="5" min="1" max="3600"
                                               placeholder="Ex: 5">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-modern" for="intervalUnit">
                                            <i class="fas fa-clock"></i> Unidade
                                        </label>
                                        <select class="form-control form-control-modern" id="intervalUnit" name="interval_unit">
                                            <option value="seconds">Segundos</option>
                                            <option value="minutes" selected>Minutos</option>
                                            <option value="hours">Horas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interval-preview">
                                            <small class="text-muted">Intervalo:</small><br>
                                            <strong class="text-primary" id="intervalDisplay">5 minutos (300s)</strong>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="interval_seconds" id="intervalSeconds" value="300">
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Recomendamos aguardar pelo menos 3-5 segundos entre mensagens para evitar bloqueios.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita - Preview -->
                <div class="col-lg-4">
                    <div class="modern-card sticky-top" style="top: 2rem;">
                        <div class="card-header-modern">
                            <h5><i class="fab fa-whatsapp"></i> Preview WhatsApp</h5>
                        </div>
                        <div class="card-body-modern p-0">
                            <div class="whatsapp-preview">
                                <div class="whatsapp-header">
                                    <div class="whatsapp-contact-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <strong id="previewContactName">Cliente Selecionado</strong><br>
                                        <small style="opacity: 0.8;">online</small>
                                    </div>
                                </div>
                                
                                <div id="messagePreview">
                                    <div class="whatsapp-message">
                                        <div id="previewContent">Digite sua mensagem para ver o preview...</div>
                                        <div class="whatsapp-time">
                                            <span id="previewTime">00:00</span>
                                            <i class="fas fa-check-double text-primary ms-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-modern btn-whatsapp btn-lg" id="sendButton">
                            <i class="fab fa-whatsapp"></i> Disparar Mensagem em Massa
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Botão para Visualizar Campanhas -->
        <div class="text-end mb-4">
            <a href="msg_relatorio.php" id="btnViewCampaigns" class="btn btn-primary">
                <i class="fas fa-bullhorn me-2"></i> Visualizar Campanhas
            </a>
        </div>
    </div>

    <!-- Modal para Editar Etiquetas -->
    <div class="modal-edit-tags" id="modalEditTags">
        <div class="modal-content-tags">
            <h5 class="mb-3">
                <i class="fas fa-tags text-primary"></i> 
                Editar Etiquetas
            </h5>
            <p class="text-muted" id="clienteNomeModal">Cliente: </p>
            
            <div class="tag-input-wrapper">
                <label class="form-label-modern">Etiquetas (separadas por vírgula)</label>
                <input type="text" 
                       class="form-control form-control-modern" 
                       id="tagsInput" 
                       placeholder="Ex: lista de leads, boletos atrasados, premium">
            </div>
            
            <div class="tags-suggestions">
                <small class="text-muted d-block w-100 mb-2">Sugestões:</small>
                <span class="suggestion-tag" onclick="addSuggestionTag('lista de leads')">lista de leads</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('boletos atrasados')">boletos atrasados</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('premium')">premium</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('vip')">vip</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('novo cliente')">novo cliente</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('inativo')">inativo</span>
                <span class="suggestion-tag" onclick="addSuggestionTag('potencial')">potencial</span>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" onclick="saveClienteTags()">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeEditTagsModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let selectedClients = [];
        let currentMediaType = 'text';
        let cursorPosition = 0;
        let selectedTags = [];
        let filterMode = 'none';
        let currentEditingClientId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar interface
            updateSendOptions();
            updateInterval();
            updatePreviewTime();
            setInterval(updatePreviewTime, 1000);
            
            // Event listeners para opções de envio
            const sendOptions = document.querySelectorAll('input[name="send_option"]');
            sendOptions.forEach(option => {
                option.addEventListener('change', updateSendOptions);
            });
            
            const sendOptionItems = document.querySelectorAll('.send-option-item');
            sendOptionItems.forEach(item => {
                item.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    updateSendOptions();
                });
            });
            
            // Gerenciar opções avançadas
            const toggleAdvanced = document.getElementById('toggleAdvanced');
            const advancedOptions = document.getElementById('advancedOptions');
            let advancedVisible = false;
            
            toggleAdvanced.addEventListener('click', function() {
                advancedVisible = !advancedVisible;
                
                if (advancedVisible) {
                    advancedOptions.style.display = 'block';
                    advancedOptions.classList.add('fade-in');
                    this.innerHTML = '<i class="fas fa-times me-2"></i>Fechar Opções Avançadas';
                    this.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                } else {
                    advancedOptions.style.display = 'none';
                    advancedOptions.classList.remove('fade-in');
                    this.innerHTML = '<i class="fas fa-cog me-2"></i>Opções Avançadas';
                    this.style.background = 'linear-gradient(135deg, #6f42c1 0%, #8a2be2 100%)';
                }
            });
            
            // Gerenciar seleção de dias da semana
            const dayCheckboxes = document.querySelectorAll('input[name="days[]"]');
            dayCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateDaySelection(this);
                });
                // Inicializar seleção
                updateDaySelection(checkbox);
            });
            
            // Gerenciar opção de repetição
            const repeatOption = document.getElementById('repeatOption');
            const daysSection = document.querySelector('.days-selector').parentElement;
            
            repeatOption.addEventListener('change', function() {
                if (this.value === 'once') {
                    daysSection.style.opacity = '0.5';
                    dayCheckboxes.forEach(cb => cb.disabled = true);
                } else {
                    daysSection.style.opacity = '1';
                    dayCheckboxes.forEach(cb => cb.disabled = false);
                }
            });
            
            // Event listeners para intervalo
            document.getElementById('intervalValue').addEventListener('input', updateInterval);
            document.getElementById('intervalUnit').addEventListener('change', updateInterval);

            // Event listeners para mensagem
            document.getElementById('messageText').addEventListener('input', function() {
                updatePreview();
                updateVariablesPreview();
            });

            // Salvar posição do cursor
            document.getElementById('messageText').addEventListener('click', function() {
                cursorPosition = this.selectionStart;
            });

            document.getElementById('messageText').addEventListener('keyup', function() {
                cursorPosition = this.selectionStart;
            });
            
            // Definir data mínima como hoje
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('scheduleDate').min = today;
        });

        // Funções de Etiquetas
        function toggleTagFilter(tag) {
            const pill = event.currentTarget;
            const index = selectedTags.indexOf(tag);
            
            if (index > -1) {
                selectedTags.splice(index, 1);
                pill.classList.remove('selected');
            } else {
                selectedTags.push(tag);
                pill.classList.add('selected');
            }
            
            document.getElementById('selectedTags').value = selectedTags.join(',');
            applyTagFilter();
        }

        function setFilterMode(mode) {
            filterMode = mode;
            document.getElementById('filterMode').value = mode;
            
            // Atualizar visual dos botões
            document.querySelectorAll('.filter-mode-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-mode="${mode}"]`).classList.add('active');
            
            // Se modo "none", limpar seleções e mostrar todos
            if (mode === 'none') {
                selectedTags = [];
                document.getElementById('selectedTags').value = '';
                document.querySelectorAll('.tag-filter-pill').forEach(pill => {
                    pill.classList.remove('selected');
                });
            }
            
            applyTagFilter();
        }

        function applyTagFilter() {
            const clientes = document.querySelectorAll('.cliente-item');
            
            clientes.forEach(cliente => {
                const clienteTags = cliente.dataset.etiquetas.toLowerCase().split(',').map(t => t.trim());
                let show = true;
                
                if (filterMode === 'none' || selectedTags.length === 0) {
                    show = true;
                } else if (filterMode === 'any') {
                    // Mostrar se tem QUALQUER uma das tags selecionadas
                    show = selectedTags.some(tag => 
                        clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                    );
                } else if (filterMode === 'all') {
                    // Mostrar se tem TODAS as tags selecionadas
                    show = selectedTags.every(tag => 
                        clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                    );
                }
                
                cliente.style.display = show ? 'block' : 'none';
            });
            
            // Atualizar contador se necessário
            updateVisibleCount();
        }

        function updateVisibleCount() {
            const visibleClientes = document.querySelectorAll('.cliente-item:not([style*="display: none"])');
            // Você pode adicionar um contador visual se desejar
        }

        function openEditTagsModal(clienteId, clienteNome, etiquetas) {
            currentEditingClientId = clienteId;
            document.getElementById('modalEditTags').classList.add('show');
            document.getElementById('clienteNomeModal').textContent = 'Cliente: ' + clienteNome;
            document.getElementById('tagsInput').value = etiquetas;
        }

        function closeEditTagsModal() {
            document.getElementById('modalEditTags').classList.remove('show');
            currentEditingClientId = null;
        }

        function addSuggestionTag(tag) {
            const input = document.getElementById('tagsInput');
            const currentTags = input.value.split(',').map(t => t.trim()).filter(t => t);
            
            if (!currentTags.includes(tag)) {
                if (currentTags.length > 0) {
                    input.value = currentTags.join(', ') + ', ' + tag;
                } else {
                    input.value = tag;
                }
            }
        }

        function saveClienteTags() {
            if (!currentEditingClientId) return;
            
            const tags = document.getElementById('tagsInput').value;
            
            // Fazer requisição AJAX para salvar no banco
            fetch('update_cliente_tags.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cliente_id=' + currentEditingClientId + '&etiquetas=' + encodeURIComponent(tags)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar a visualização
                    const clienteItem = document.querySelector(`[data-id="${currentEditingClientId}"]`);
                    if (clienteItem) {
                        clienteItem.dataset.etiquetas = tags;
                        
                        // Atualizar tags visuais
                        const tagsContainer = clienteItem.querySelector('.cliente-tags');
                        if (tagsContainer || tags) {
                            const clienteDados = clienteItem.querySelector('.cliente-dados');
                            let newTagsHtml = '';
                            
                            if (tags) {
                                const tagsList = tags.split(',').map(t => t.trim()).filter(t => t);
                                if (tagsList.length > 0) {
                                    newTagsHtml = '<div class="cliente-tags">';
                                    tagsList.forEach(tag => {
                                        newTagsHtml += `<span class="cliente-tag-mini">${tag}</span>`;
                                    });
                                    newTagsHtml += '</div>';
                                }
                            }
                            
                            // Remover tags antigas se existirem
                            if (tagsContainer) {
                                tagsContainer.remove();
                            }
                            
                            // Adicionar novas tags se houver
                            if (newTagsHtml) {
                                clienteDados.insertAdjacentHTML('beforeend', newTagsHtml);
                            }
                        }
                    }
                    
                    // Recarregar filtros de tags se necessário
                    // Aqui você pode adicionar código para atualizar os filtros disponíveis
                    
                    closeEditTagsModal();
                    
                    // Mostrar mensagem de sucesso (opcional)
                    alert('Etiquetas atualizadas com sucesso!');
                } else {
                    alert('Erro ao salvar etiquetas: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar etiquetas');
            });
        }

        function updateDaySelection(checkbox) {
            const label = checkbox.nextElementSibling;
            if (checkbox.checked) {
                label.style.background = '#0d6efd';
                label.style.color = 'white';
                label.style.borderColor = '#0d6efd';
            } else {
                label.style.background = '#f8f9fa';
                label.style.color = '#495057';
                label.style.borderColor = '#dee2e6';
            }
        }

        // Função para atualizar interface das opções de envio
        function updateSendOptions() {
            const sendOptionItems = document.querySelectorAll('.send-option-item');
            const scheduleInputs = document.getElementById('scheduleInputs');
            const sendButton = document.getElementById('sendButton');
            
            sendOptionItems.forEach(item => {
                const radio = item.querySelector('input[type="radio"]');
                if (radio.checked) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
            
            // Mostrar/ocultar campos de agendamento
            if (document.getElementById('sendLater').checked) {
                scheduleInputs.style.display = 'block';
                sendButton.innerHTML = '<i class="fas fa-clock"></i> Agendar Mensagem em Massa';
            } else {
                scheduleInputs.style.display = 'none';
                sendButton.innerHTML = '<i class="fab fa-whatsapp"></i> Disparar Mensagem em Massa';
            }
        }

        // Inserir variável na posição do cursor
        function insertVariable(variable) {
            const messageText = document.getElementById('messageText');
            const startPos = messageText.selectionStart;
            const endPos = messageText.selectionEnd;
            const textBefore = messageText.value.substring(0, startPos);
            const textAfter = messageText.value.substring(endPos);
            
            messageText.value = textBefore + variable + ' ' + textAfter;
            messageText.focus();
            
            // Posicionar cursor após a variável inserida
            const newPos = startPos + variable.length + 1;
            messageText.setSelectionRange(newPos, newPos);
            
            updatePreview();
            updateVariablesPreview();
        }

        // Limpar texto
        function clearText() {
            if (confirm('Tem certeza que deseja limpar todo o texto?')) {
                document.getElementById('messageText').value = '';
                updatePreview();
                updateVariablesPreview();
            }
        }

        // Atualizar intervalo entre mensagens
        function updateInterval() {
            const value = parseInt(document.getElementById('intervalValue').value) || 1;
            const unit = document.getElementById('intervalUnit').value;
            const display = document.getElementById('intervalDisplay');
            const hiddenSeconds = document.getElementById('intervalSeconds');
            
            let seconds = value;
            let unitText = '';
            
            switch(unit) {
                case 'seconds':
                    seconds = value;
                    unitText = value === 1 ? 'segundo' : 'segundos';
                    break;
                case 'minutes':
                    seconds = value * 60;
                    unitText = value === 1 ? 'minuto' : 'minutos';
                    break;
                case 'hours':
                    seconds = value * 3600;
                    unitText = value === 1 ? 'hora' : 'horas';
                    break;
            }
            
            display.innerHTML = `${value} ${unitText} <span class="text-muted">(${seconds}s)</span>`;
            hiddenSeconds.value = seconds;
        }

        // Atualizar preview das variáveis
        function updateVariablesPreview() {
            const messageText = document.getElementById('messageText').value;
            const preview = document.getElementById('variablesPreview');
            const previewText = document.getElementById('variablesPreviewText');
            
            // Verificar se há variáveis no texto
            const hasVariables = messageText.includes('{nome}') || messageText.includes('{data}') || 
                                messageText.includes('{telefone}');
            
            if (hasVariables && messageText.trim()) {
                // Buscar dados do cliente selecionado ou usar exemplos
                let nomeExemplo = 'João Silva';
                let telefoneExemplo = '(11) 99999-1111';
                
                if (selectedClients.length === 1) {
                    const clienteSelecionado = document.querySelector('.cliente-item.selected');
                    if (clienteSelecionado) {
                        nomeExemplo = clienteSelecionado.dataset.nome;
                        telefoneExemplo = clienteSelecionado.dataset.telefone;
                    }
                } else if (selectedClients.length > 1) {
                    nomeExemplo = '[Nome personalizado para cada cliente]';
                    telefoneExemplo = '[Telefone de cada cliente]';
                }
                
                // Substituir variáveis por valores de exemplo
                let exampleText = messageText
                    .replace(/{nome}/g, `<span class="variable-tag">${nomeExemplo}</span>`)
                    .replace(/{data}/g, '<span class="variable-tag">' + new Date().toLocaleDateString('pt-BR') + '</span>')
                    .replace(/{telefone}/g, `<span class="variable-tag">${telefoneExemplo}</span>`);
                
                previewText.innerHTML = exampleText;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        // Função melhorada para substituir variáveis no preview do WhatsApp
        function getPreviewText(originalText, clienteNome = '') {
            if (!originalText) return originalText;
            
            let previewText = originalText;
            
            // Buscar dados do cliente selecionado se houver apenas um
            let nomeParaUsar = 'João Silva';
            let telefoneParaUsar = '(11) 99999-1111';
            
            if (selectedClients.length === 1) {
                const clienteSelecionado = document.querySelector('.cliente-item.selected');
                if (clienteSelecionado) {
                    nomeParaUsar = clienteSelecionado.dataset.nome;
                    telefoneParaUsar = clienteSelecionado.dataset.telefone;
                }
            } else if (selectedClients.length > 1) {
                nomeParaUsar = '[Nome do Cliente]';
                telefoneParaUsar = '[Telefone]';
            }
            
            previewText = previewText
                .replace(/{nome}/g, nomeParaUsar)
                .replace(/{data}/g, new Date().toLocaleDateString('pt-BR'))
                .replace(/{telefone}/g, telefoneParaUsar);
            
            return previewText;
        }

        // Atualizar hora no preview
        function updatePreviewTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('previewTime').textContent = timeString;
        }

        // Selecionar/deselecionar cliente
        function toggleCliente(element) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            const checkIcon = element.querySelector('.fa-check-circle');
            const clienteId = element.dataset.id;
            const clienteNome = element.dataset.nome;

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                checkbox.checked = false;
                checkIcon.style.display = 'none';
                selectedClients = selectedClients.filter(id => id !== clienteId);
            } else {
                element.classList.add('selected');
                checkbox.checked = true;
                checkIcon.style.display = 'block';
                selectedClients.push(clienteId);
                
                // Atualizar preview com o nome do cliente
                if (selectedClients.length === 1) {
                    document.getElementById('previewContactName').textContent = clienteNome;
                } else {
                    document.getElementById('previewContactName').textContent = `${selectedClients.length} contatos`;
                }
            }

            updateSelectedCount();
            updatePreview(); // Atualizar preview para mostrar nome real nas variáveis
            updateVariablesPreview(); // Atualizar preview das variáveis
        }

        // Selecionar todos os clientes
        function toggleSelectAll() {
            const allClientes = document.querySelectorAll('.cliente-item:not([style*="display: none"])');
            const allSelected = selectedClients.length === allClientes.length;

            allClientes.forEach(cliente => {
                const checkbox = cliente.querySelector('input[type="checkbox"]');
                const checkIcon = cliente.querySelector('.fa-check-circle');
                
                if (allSelected) {
                    cliente.classList.remove('selected');
                    checkbox.checked = false;
                    checkIcon.style.display = 'none';
                } else {
                    cliente.classList.add('selected');
                    checkbox.checked = true;
                    checkIcon.style.display = 'block';
                }
            });

            selectedClients = allSelected ? [] : Array.from(allClientes).map(c => c.dataset.id);
            updateSelectedCount();
            updatePreview();
            updateVariablesPreview();
        }

        // Atualizar contador de selecionados
        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = `${selectedClients.length} selecionados`;
        }

        // Selecionar tipo de mídia
        function selectMediaType(type) {
            currentMediaType = type;
            
            // Atualizar botões
            document.querySelectorAll('.media-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-type="${type}"]`).classList.add('active');
            document.getElementById('media_type').value = type;

            // Mostrar/ocultar upload
            const uploadSection = document.getElementById('uploadSection');
            const textSection = document.getElementById('textSection');
            const textLabel = document.getElementById('textLabel');
            const messageText = document.getElementById('messageText');
            const fileInput = document.getElementById('fileInput');
            
            // Limpar arquivo anterior
            fileInput.value = '';
            document.getElementById('filePreview').style.display = 'none';
            
            if (type === 'text') {
                uploadSection.style.display = 'none';
                textSection.style.display = 'block';
                textLabel.textContent = 'Mensagem';
                messageText.placeholder = 'Digite sua mensagem aqui...';
                messageText.required = true;
            } else if (type === 'audio' || type === 'document') {
                // Áudio e documento não têm legenda no WhatsApp
                uploadSection.style.display = 'block';
                textSection.style.display = 'none';
                messageText.value = '';
                messageText.required = false;
                
                // Configurar accept do input
                if (type === 'audio') {
                    fileInput.accept = 'audio/*,.mp3,.wav,.ogg,.m4a,.aac';
                } else {
                    fileInput.accept = '.pdf,.doc,.docx,.txt';
                }
            } else {
                // Imagem e vídeo podem ter legenda
                uploadSection.style.display = 'block';
                textSection.style.display = 'block';
                textLabel.textContent = 'Legenda (opcional)';
                messageText.placeholder = 'Digite uma legenda para sua ' + (type === 'image' ? 'imagem' : 'vídeo') + '...';
                messageText.required = false;
                
                // Configurar accept do input
                if (type === 'image') {
                    fileInput.accept = 'image/*,.jpg,.jpeg,.png,.gif,.webp';
                } else {
                    fileInput.accept = 'video/*,.mp4,.avi,.mov,.mkv,.webm';
                }
            }

            updatePreview();
            updateVariablesPreview();
        }

        // Manipular seleção de arquivo
        function handleFileSelect(input) {
            const file = input.files[0];
            const preview = document.getElementById('filePreview');
            
            if (file) {
                // Validar tipo de arquivo baseado no tipo de mídia selecionado
                let validTypes = [];
                let errorMessage = '';
                
                switch(currentMediaType) {
                    case 'image':
                        validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                        errorMessage = 'Por favor, selecione apenas imagens (JPG, PNG, GIF, WebP).';
                        break;
                    case 'video':
                        validTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv', 'video/webm'];
                        errorMessage = 'Por favor, selecione apenas vídeos (MP4, AVI, MOV, MKV, WebM).';
                        break;
                    case 'audio':
                        validTypes = ['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/aac'];
                        errorMessage = 'Por favor, selecione apenas áudios (MP3, WAV, OGG, M4A, AAC).';
                        break;
                    case 'document':
                        validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
                        errorMessage = 'Por favor, selecione apenas documentos (PDF, DOC, DOCX, TXT).';
                        break;
                }
                
                // Validação flexível para tipos de arquivo
                let isValid = false;
                if (currentMediaType === 'image' && file.type.startsWith('image/')) isValid = true;
                else if (currentMediaType === 'video' && file.type.startsWith('video/')) isValid = true;
                else if (currentMediaType === 'audio' && file.type.startsWith('audio/')) isValid = true;
                else if (currentMediaType === 'document') {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (['pdf', 'doc', 'docx', 'txt'].includes(ext)) isValid = true;
                }
                
                if (!isValid) {
                    alert(errorMessage);
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewHTML = '';
                    
                    if (file.type.startsWith('image/')) {
                        previewHTML = `<img src="${e.target.result}" style="max-width: 100%; border-radius: 8px;">`;
                    } else if (file.type.startsWith('video/')) {
                        previewHTML = `<video controls style="max-width: 100%; border-radius: 8px;"><source src="${e.target.result}"></video>`;
                    } else if (file.type.startsWith('audio/')) {
                        previewHTML = `<audio controls style="width: 100%;"><source src="${e.target.result}"></audio>`;
                    } else {
                        previewHTML = `<div class="d-flex align-items-center gap-2 p-2 bg-light rounded"><i class="fas fa-file"></i> ${file.name}</div>`;
                    }
                    
                    preview.innerHTML = previewHTML;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                updatePreview();
            }
        }

        // Atualizar preview da mensagem
        function updatePreview() {
            const messageText = document.getElementById('messageText').value;
            const previewContent = document.getElementById('previewContent');
            const fileInput = document.getElementById('fileInput');
            
            let previewHTML = '';
            
            if (currentMediaType !== 'text' && fileInput.files[0]) {
                const file = fileInput.files[0];
                
                if (file.type.startsWith('image/')) {
                    previewHTML += `<div class="whatsapp-message-media"><img src="${URL.createObjectURL(file)}" alt="Imagem"></div>`;
                } else if (file.type.startsWith('video/')) {
                    previewHTML += `<div class="whatsapp-message-media"><video controls><source src="${URL.createObjectURL(file)}"></video></div>`;
                } else if (file.type.startsWith('audio/')) {
                    previewHTML += `<div class="whatsapp-message-audio"><i class="fas fa-play"></i><div>🎵 Mensagem de áudio</div><div>0:${Math.floor(Math.random() * 60).toString().padStart(2, '0')}</div></div>`;
                } else {
                    previewHTML += `<div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-file"></i> ${file.name}</div>`;
                }
            }
            
            // Só mostra texto se não for áudio ou documento
            if (currentMediaType !== 'audio' && currentMediaType !== 'document') {
                if (messageText) {
                    // Usar função para substituir variáveis no preview
                    const processedText = getPreviewText(messageText, selectedClients.length === 1 ? document.querySelector('.cliente-item.selected')?.dataset.nome : '');
                    previewHTML += `<div>${processedText}</div>`;
                } else if (currentMediaType === 'text') {
                    previewHTML = 'Digite sua mensagem para ver o preview...';
                }
            }
            
            // Se for áudio ou documento e não tem arquivo, mostra mensagem
            if ((currentMediaType === 'audio' || currentMediaType === 'document') && !fileInput.files[0]) {
                previewHTML = `Selecione um ${currentMediaType === 'audio' ? 'áudio' : 'documento'} para ver o preview...`;
            }
            
            previewContent.innerHTML = previewHTML;
        }

        // Busca de clientes
        function filterClientes(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            const clientes = document.querySelectorAll('.cliente-item');
            
            clientes.forEach(cliente => {
                const nome = cliente.dataset.nome.toLowerCase();
                const telefone = cliente.dataset.telefone.toLowerCase();
                const etiquetas = cliente.dataset.etiquetas.toLowerCase();
                
                // Também buscar nas etiquetas
                if (nome.includes(searchTerm) || telefone.includes(searchTerm) || etiquetas.includes(searchTerm)) {
                    // Respeitar filtro de tags se ativo
                    if (filterMode === 'none' || selectedTags.length === 0) {
                        cliente.style.display = 'block';
                    } else {
                        // Verificar se cliente passa no filtro de tags
                        const clienteTags = etiquetas.split(',').map(t => t.trim());
                        let show = true;
                        
                        if (filterMode === 'any') {
                            show = selectedTags.some(tag => 
                                clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                            );
                        } else if (filterMode === 'all') {
                            show = selectedTags.every(tag => 
                                clienteTags.some(clienteTag => clienteTag.includes(tag.toLowerCase()))
                            );
                        }
                        
                        cliente.style.display = show ? 'block' : 'none';
                    }
                } else {
                    cliente.style.display = 'none';
                }
            });
        }
        
        // Validação do formulário
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            if (selectedClients.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos um cliente para enviar a mensagem.');
                return;
            }
            
            const messageText = document.getElementById('messageText').value.trim();
            const fileInput = document.getElementById('fileInput');
            
            // Para texto, obrigatório ter mensagem
            if (currentMediaType === 'text' && !messageText) {
                e.preventDefault();
                alert('Digite uma mensagem de texto.');
                return;
            }
            
            // Para mídia, obrigatório ter arquivo
            if (currentMediaType !== 'text' && !fileInput.files[0]) {
                e.preventDefault();
                alert(`Selecione um ${currentMediaType === 'image' ? 'imagem' : currentMediaType === 'video' ? 'vídeo' : currentMediaType === 'audio' ? 'áudio' : 'documento'} para enviar.`);
                return;
            }
            
            // Para agendamento, verificar data e hora
            const sendOption = document.querySelector('input[name="send_option"]:checked').value;
            if (sendOption === 'later') {
                const scheduleDate = document.getElementById('scheduleDate').value;
                const scheduleTime = document.getElementById('scheduleTime').value;
                
                if (!scheduleDate || !scheduleTime) {
                    e.preventDefault();
                    alert('Selecione a data e hora para agendamento.');
                    return;
                }
                
                // Verificar se a data/hora é futura
                const agendamento = new Date(scheduleDate + ' ' + scheduleTime);
                const agora = new Date();
                
                if (agendamento <= agora) {
                    e.preventDefault();
                    alert('A data e hora do agendamento deve ser futura.');
                    return;
                }
            }
            
            // Validar intervalo
            const intervalValue = parseInt(document.getElementById('intervalValue').value);
            if (!intervalValue || intervalValue < 1) {
                e.preventDefault();
                alert('O intervalo entre mensagens deve ser de pelo menos 1.');
                return;
            }
            
            // Aviso para muitos clientes com intervalo pequeno
            if (selectedClients.length > 50 && intervalValue < 3) {
                if (!confirm(`Você selecionou ${selectedClients.length} clientes com intervalo de ${intervalValue} segundos.\nIsso pode demorar muito tempo. Deseja continuar?`)) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Drag and drop para upload
        const uploadArea = document.querySelector('.upload-area');
        
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('active');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('active');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('active');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('fileInput').files = files;
                    handleFileSelect(document.getElementById('fileInput'));
                }
            });
        }
    </script>

<?php include 'footer.php'; ?>