<?php
session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
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
    <!-- DADOS PARA INSERIR AQUI -->
   
   <!-- Formulário para solicitar confirmação de agendamento -->

<!-- Formulário para solicitar confirmação de agendamento estilo enquete -->
<!-- Estilos CSS Modernos -->
<!-- Estilos CSS Modernos -->
<?php
// APENAS CONSULTA - Puxar dados do banco
$sql = "SELECT agenda_verfica, agenda_confirma, agenda_cancela, confirma_prof, cancela_prof, tempo_verifica, solicitar_confirmacao FROM login WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $agendaVerifica = $row['agenda_verfica'] ?? '';
    $mensagemConfirmacao = $row['agenda_confirma'] ?? '';
    $mensagemCancelamento = $row['agenda_cancela'] ?? '';
    $mensagemConfirmacaoProfissional = $row['confirma_prof'] ?? '';
    $mensagemCancelamentoProfissional = $row['cancela_prof'] ?? '';
    $tempoVerifica = $row['tempo_verifica'] ?? '';
    $solicitarConfirmacao = $row['solicitar_confirmacao'] ?? 'nao';
} else {
    $agendaVerifica = '';
    $mensagemConfirmacao = '';
    $mensagemCancelamento = '';
    $mensagemConfirmacaoProfissional = '';
    $mensagemCancelamentoProfissional = '';
    $tempoVerifica = '';
    $solicitarConfirmacao = 'nao';
}
$stmt->close();
?>

<!-- Formulário para solicitar confirmação de agendamento estilo enquete -->
<!-- Estilos CSS Modernos -->
<!-- Estilos CSS Modernos -->
<style>
.modern-card {
    background: linear-gradient(135deg, #6c7ce0 0%, #a29bfe 100%);
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.card-header-modern {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: none;
    padding: 20px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header-modern h3 {
    color: white;
    font-weight: 600;
    margin: 0;
    font-size: 1.2rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.card-body-modern {
    background: white;
    padding: 25px;
}

.btn-modern {
    border-radius: 25px;
    padding: 6px 12px;
    font-weight: 500;
    margin: 2px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    font-size: 0.85rem;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

.btn-tag {
    background: linear-gradient(45deg, #6c7ce0, #a29bfe);
    color: white;
}

.btn-model {
    background: linear-gradient(45deg, #fd79a8, #e84393);
    color: white;
    font-weight: 600;
}

.btn-save {
    background: linear-gradient(45deg, #00b894, #00cec9);
    color: white;
    padding: 10px 25px;
    font-weight: 600;
    border-radius: 25px;
    margin-top: 15px;
}

.btn-cancel {
    background: linear-gradient(45deg, #e17055, #fab1a0);
    color: white;
    padding: 10px 25px;
    font-weight: 600;
    border-radius: 25px;
    margin-top: 15px;
}

.form-control-modern {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s ease;
    resize: vertical;
}

.form-control-modern:focus {
    border-color: #6c7ce0;
    box-shadow: 0 0 0 0.2rem rgba(108, 124, 224, 0.25);
    outline: none;
}

.form-label-modern {
    font-weight: 600;
    color: #2d3436;
    margin-bottom: 8px;
    font-size: 1rem;
}

.whatsapp-preview {
    background: linear-gradient(135deg, #25d366, #128c7e);
    border-radius: 15px;
    padding: 20px;
    margin: 15px 0;
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
}

.whatsapp-message {
    background: white;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    position: relative;
}

.whatsapp-message::before {
    content: '';
    position: absolute;
    top: -6px;
    left: 15px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid white;
}

.poll-container {
    background: #f0f2f5;
    border-radius: 12px;
    padding: 15px;
    margin-top: 10px;
    border: 1px solid #e4e6ea;
}

.poll-title {
    font-weight: 600;
    color: #1c1e21;
    margin-bottom: 12px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.poll-option {
    background: white;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    border: 1px solid #dadde1;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.poll-option:hover {
    background: #f8f9fa;
    border-color: #25d366;
}

.poll-option-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
}

.poll-option-text {
    font-weight: 500;
    color: #1c1e21;
    font-size: 0.9rem;
}

.poll-percentage {
    font-weight: 600;
    color: #65676b;
    font-size: 0.85rem;
}

.poll-progress {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, rgba(37, 211, 102, 0.1), rgba(37, 211, 102, 0.05));
    border-radius: 8px;
    transition: width 0.3s ease;
    z-index: 1;
}

.poll-option.option-yes .poll-progress {
    width: 65%;
    background: linear-gradient(90deg, rgba(37, 211, 102, 0.15), rgba(37, 211, 102, 0.08));
}

.poll-option.option-no .poll-progress {
    width: 35%;
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.15), rgba(220, 53, 69, 0.08));
}

.poll-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.8rem;
    color: #65676b;
}

.poll-voters {
    display: flex;
    align-items: center;
}

.poll-time {
    color: #8a8d91;
}

.input-number-modern {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 10px 12px;
    transition: all 0.3s ease;
}

.input-number-modern:focus {
    border-color: #6c7ce0;
    box-shadow: 0 0 0 0.2rem rgba(108, 124, 224, 0.25);
    outline: none;
}

.select-modern {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 10px 12px;
    transition: all 0.3s ease;
    background: white;
}

.select-modern:focus {
    border-color: #6c7ce0;
    box-shadow: 0 0 0 0.2rem rgba(108, 124, 224, 0.25);
    outline: none;
}

.tags-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 2px dashed #dee2e6;
}

@media (max-width: 768px) {
    .btn-modern {
        margin: 2px 0;
        width: 100%;
    }
    
    .card-body-modern {
        padding: 20px;
    }
}
</style>

<!-- Formulário para solicitar confirmação de agendamento estilo enquete -->
<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header-modern">
                <h3>🔔 Configurar Solicitação de Confirmação de Agendamento</h3>
            </div>
            <div class="card-body-modern">
                <?php if($solicitarConfirmacao == 'nao'){ ?>     
                
                <form action="configurar_confirmacao.php" method="post">
                    <div class="form-group mb-3">
                        <label for="solicitarConfirmacao" class="form-label-modern">Solicitar confirmação de agendamento?</label>
                        <select class="form-control select-modern" id="solicitarConfirmacao" name="solicitarConfirmacao" onchange="toggleEnqueteFields()">
                            <option value="nao" selected>❌ Não</option>
                            <option value="sim">✅ Sim</option>
                        </select>
                    </div>

                    <div id="enqueteFields" style="display: none;">
                        <div class="form-group mb-3">
                            <label for="mensagemEnquete" class="form-label-modern">💬 Mensagem que será enviada com a enquete</label>

                            <div class="tags-container">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{nome}')">👤 {nome}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{data_agendamento}')">📅 {data_agendamento}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{profissional}')">👨‍⚕️ {profissional}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{serviço}')">🛠️ {serviço}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{preço_serviço}')">💰 {preço_serviço}</button>
                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloEnquete()">✨ Carregar Modelo</button>
                                </div>
                            </div>

                            <textarea class="form-control form-control-modern" id="mensagemEnquete" name="mensagemEnquete" placeholder="Digite a mensagem que será enviada junto com a enquete" rows="4"><?=$agendaVerifica;?></textarea>
                        </div>

                        <div class="whatsapp-preview">
                            <p class="text-white mb-2 text-center font-weight-bold">📱 Pré-visualização WhatsApp</p>
                            <div class="whatsapp-message">
                                <p id="previewMensagemEnquete" style="margin-bottom: 12px; line-height: 1.4;">[Sua mensagem aparecerá aqui]</p>
                                
                                <div class="poll-container">
                                    <div class="poll-title">
                                        📊 Você confirmará sua presença?
                                    </div>
                                    
                                    <div class="poll-option option-yes">
                                        <div class="poll-progress"></div>
                                        <div class="poll-option-content">
                                            <span class="poll-option-text">✅ Sim, estarei presente</span>
                                            <span class="poll-percentage">65%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="poll-option option-no">
                                        <div class="poll-progress"></div>
                                        <div class="poll-option-content">
                                            <span class="poll-option-text">❌ Não posso comparecer</span>
                                            <span class="poll-percentage">35%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="poll-footer">
                                        <div class="poll-voters">
                                            👥 127 votos
                                        </div>
                                        <div class="poll-time">
                                            Criado hoje às 14:30
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tempoAntesAgendamento" class="form-label-modern">⏰ Tempo antes do agendamento (minutos)</label>
                            <input type="number" class="form-control input-number-modern" id="tempoAntesAgendamento" name="tempoAntesAgendamento" placeholder="Ex: 60 minutos" min="0" value="<?=$tempoVerifica;?>">
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-save">💾 Salvar Configuração</button>
                    </div>
                </form>    
                    
                <?php } else { ?>
               
                <form action="configurar_confirmacao.php" method="post">
                    <div class="form-group mb-3">
                        <label for="solicitarConfirmacao" class="form-label-modern">Solicitar confirmação de agendamento?</label>
                        <select class="form-control select-modern" id="solicitarConfirmacao" name="solicitarConfirmacao" onchange="toggleEnqueteFields()">
                            <option value="sim" selected>✅ Sim</option>
                            <option value="nao">❌ Não</option>
                        </select>
                    </div>

                    <div id="enqueteFields">
                        <div class="form-group mb-3">
                            <label for="mensagemEnquete" class="form-label-modern">💬 Mensagem que será enviada com a enquete</label>

                            <div class="tags-container">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{nome}')">👤 {nome}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{data_agendamento}')">📅 {data_agendamento}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{profissional}')">👨‍⚕️ {profissional}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{serviço}')">🛠️ {serviço}</button>
                                    <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemEnquete', '{preço_serviço}')">💰 {preço_serviço}</button>
                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloEnquete()">✨ Carregar Modelo</button>
                                </div>
                            </div>

                            <textarea class="form-control form-control-modern" id="mensagemEnquete" name="mensagemEnquete" placeholder="Digite a mensagem que será enviada junto com a enquete" rows="4"><?=$agendaVerifica;?></textarea>
                        </div>

                        <div class="whatsapp-preview">
                            <p class="text-white mb-2 text-center font-weight-bold">📱 Pré-visualização WhatsApp</p>
                            <div class="whatsapp-message">
                                <p id="previewMensagemEnquete" style="margin-bottom: 12px; line-height: 1.4;">[Sua mensagem aparecerá aqui]</p>
                                
                                <div class="poll-container">
                                    <div class="poll-title">
                                        📊 Você confirmará sua presença?
                                    </div>
                                    
                                    <div class="poll-option option-yes">
                                        <div class="poll-progress"></div>
                                        <div class="poll-option-content">
                                            <span class="poll-option-text">✅ Sim, estarei presente</span>
                                            <span class="poll-percentage">65%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="poll-option option-no">
                                        <div class="poll-progress"></div>
                                        <div class="poll-option-content">
                                            <span class="poll-option-text">❌ Não posso comparecer</span>
                                            <span class="poll-percentage">35%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="poll-footer">
                                        <div class="poll-voters">
                                            👥 127 votos
                                        </div>
                                        <div class="poll-time">
                                            Criado hoje às 14:30
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tempoAntesAgendamento" class="form-label-modern">⏰ Tempo antes do agendamento (minutos)</label>
                            <input type="number" class="form-control input-number-modern" id="tempoAntesAgendamento" name="tempoAntesAgendamento" placeholder="Ex: 60 minutos" min="0" value="<?=$tempoVerifica;?>">
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-save">💾 Salvar Configuração</button>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Formulário para configurar mensagem de confirmação de agendamento -->
<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header-modern">
                <h3>📅 Configurar Mensagem de Confirmação de Agendamento</h3>
            </div>
            <div class="card-body-modern">
                <form action="configurar_mensagem_confirmacao.php" method="post">
                    <div class="form-group mb-3">
                        <label for="mensagemConfirmacao" class="form-label-modern">💬 Mensagem de Confirmação</label>

                        <div class="tags-container">
                            <div class="d-flex flex-wrap justify-content-center">
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{nome}')">👤 {nome}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{data_agendamento}')">📅 {data_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{profissional}')">👨‍⚕️ {profissional}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{serviço}')">🛠️ {serviço}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{preço_serviço}')">💰 {preço_serviço}</button>
                                <?php if($oculto){?>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacao', '{link_agendamento}')">🔗 {link_agendamento}</button>
                            </div>
                            <?php
                                }
                                ?>
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloConfirmacao()">✨ Carregar Modelo</button>
                            </div>
                        </div>

                        <textarea class="form-control form-control-modern" id="mensagemConfirmacao" name="mensagemConfirmacao" placeholder="Digite a mensagem de confirmação" rows="4"><?=$mensagemConfirmacao;?></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-save">💾 Salvar Mensagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Formulário para configurar mensagem de cancelamento -->
<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header-modern">
                <h3>❌ Configurar Mensagem de Cancelamento de Agendamento</h3>
            </div>
            <div class="card-body-modern">
                <form action="configurar_mensagem_cancelamento.php" method="post">
                    <div class="form-group mb-3">
                        <label for="mensagemCancelamento" class="form-label-modern">💬 Mensagem de Cancelamento</label>

                        <div class="tags-container">
                            <div class="d-flex flex-wrap justify-content-center">
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{nome}')">👤 {nome}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{data_agendamento}')">📅 {data_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{profissional}')">👨‍⚕️ {profissional}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{serviço}')">🛠️ {serviço}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamento', '{preço_serviço}')">💰 {preço_serviço}</button>
                            </div>
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloCancelamento()">✨ Carregar Modelo</button>
                            </div>
                        </div>

                        <textarea class="form-control form-control-modern" id="mensagemCancelamento" name="mensagemCancelamento" placeholder="Digite a mensagem de cancelamento" rows="4"><?=$mensagemCancelamento;?></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-cancel">💾 Salvar Mensagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Formulário para configurar mensagem de confirmação para o PROFISSIONAL -->
<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header-modern">
                <h3>👨‍⚕️ Configurar Mensagem de Confirmação para o PROFISSIONAL</h3>
            </div>
            <div class="card-body-modern">
                <form action="configurar_mensagem_confirmacao_profissional.php" method="post">
                    <div class="form-group mb-3">
                        <label for="mensagemConfirmacaoProfissional" class="form-label-modern">💬 Mensagem de Confirmação para o Profissional</label>

                        <div class="tags-container">
                            <div class="d-flex flex-wrap justify-content-center">
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{nome}')">👤 {nome}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{data_agendamento}')">📅 {data_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{profissional}')">👨‍⚕️ {profissional}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{serviço}')">🛠️ {serviço}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemConfirmacaoProfissional', '{preço_serviço}')">💰 {preço_serviço}</button>
                            </div>
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloConfirmacaoProfissional()">✨ Carregar Modelo</button>
                            </div>
                        </div>

                        <textarea class="form-control form-control-modern" id="mensagemConfirmacaoProfissional" name="mensagemConfirmacaoProfissional" placeholder="Digite a mensagem de confirmação que será enviada ao profissional" rows="4"><?=$mensagemConfirmacaoProfissional;?></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-save">💾 Salvar Mensagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Formulário para configurar mensagem de cancelamento PARA O PROFISSIONAL -->
<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header-modern">
                <h3>🚫 Configurar Mensagem de Cancelamento para o PROFISSIONAL</h3>
            </div>
            <div class="card-body-modern">
                <form action="configurar_mensagem_cancelamento_profissional.php" method="post">
                    <div class="form-group mb-3">
                        <label for="mensagemCancelamentoProfissional" class="form-label-modern">💬 Mensagem de Cancelamento para o Profissional</label>

                        <div class="tags-container">
                            <div class="d-flex flex-wrap justify-content-center">
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{nome}')">👤 {nome}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{data_agendamento}')">📅 {data_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{hora_agendamento}')">🕒 {hora_agendamento}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{profissional}')">👨‍⚕️ {profissional}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{telefone_cliente}')">📞 {telefone_cliente}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{serviço}')">🛠️ {serviço}</button>
                                <button type="button" class="btn btn-modern btn-tag" onclick="inserirCampo('mensagemCancelamentoProfissional', '{preço_serviço}')">💰 {preço_serviço}</button>
                            </div>
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-modern btn-model" onclick="carregarModeloCancelamentoProfissional()">✨ Carregar Modelo</button>
                            </div>
                        </div>

                        <textarea class="form-control form-control-modern" id="mensagemCancelamentoProfissional" name="mensagemCancelamentoProfissional" placeholder="Digite a mensagem de cancelamento que será enviada ao profissional" rows="5"><?=$mensagemCancelamentoProfissional;?></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-modern btn-cancel">💾 Salvar Mensagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script JavaScript para inserir placeholders no campo de texto -->
<script>
    function inserirCampo(idCampo, placeholder) {
        var campoTexto = document.getElementById(idCampo);
        
        // Posição atual do cursor
        var cursorPos = campoTexto.selectionStart;
        var textoAntes = campoTexto.value.substring(0, cursorPos);
        var textoDepois = campoTexto.value.substring(cursorPos);
        
        // Insere o placeholder na posição do cursor
        campoTexto.value = textoAntes + placeholder + textoDepois;
        
        // Foca novamente o textarea
        campoTexto.focus();
        
        // Ajusta a posição do cursor após o placeholder
        var novaPosicao = cursorPos + placeholder.length;
        campoTexto.selectionStart = novaPosicao;
        campoTexto.selectionEnd = novaPosicao;
    }
</script>

<script>
    // Função para mostrar/ocultar campos da enquete com base na seleção
    function toggleEnqueteFields() {
        var solicitarConfirmacao = document.getElementById('solicitarConfirmacao').value;
        var enqueteFields = document.getElementById('enqueteFields');
        if (solicitarConfirmacao === 'sim') {
            enqueteFields.style.display = 'block';
        } else {
            enqueteFields.style.display = 'none';
        }
    }

    // Atualiza a simulação da mensagem conforme o usuário digita
    document.getElementById('mensagemEnquete').addEventListener('input', function() {
        document.getElementById('previewMensagemEnquete').textContent = this.value || "[Sua mensagem aparecerá aqui]";
    });

    // Adiciona interatividade à enquete de demonstração
    document.addEventListener('DOMContentLoaded', function() {
        const pollOptions = document.querySelectorAll('.poll-option');
        
        pollOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove seleção anterior
                pollOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Adiciona seleção atual
                this.classList.add('selected');
                
                // Simula uma pequena mudança nos percentuais
                if (this.classList.contains('option-yes')) {
                    this.querySelector('.poll-percentage').textContent = '66%';
                    document.querySelector('.option-no .poll-percentage').textContent = '34%';
                    document.querySelector('.poll-voters').innerHTML = '👥 128 votos';
                } else {
                    this.querySelector('.poll-percentage').textContent = '36%';
                    document.querySelector('.option-yes .poll-percentage').textContent = '64%';
                    document.querySelector('.poll-voters').innerHTML = '👥 128 votos';
                }
            });
        });
    });

    // -- FUNÇÕES PARA CARREGAR MODELO EM CADA CAMPO --

 function carregarModeloEnquete() { document.getElementById('mensagemEnquete').value = "Olá {nome}! Este é um lembrete do seu agendamento: Data: {data_agendamento} Horário: {hora_agendamento} Profissional: {profissional} Serviço: {serviço} Valor: {preço_serviço} Por favor, confirme se você poderá comparecer no horário marcado."; 

        
        // Atualiza a pré-visualização
        document.getElementById('previewMensagemEnquete').textContent =
            document.getElementById('mensagemEnquete').value;
        
        // Define valor padrão para o tempo antes do agendamento
        document.getElementById('tempoAntesAgendamento').value = "60"; // 60 minutos
    }

    // 2. Modelo para a Mensagem de Confirmação
    function carregarModeloConfirmacao() {
        document.getElementById('mensagemConfirmacao').value =
            "✅ Perfeito, {nome}!\n\n" +
            "Seu agendamento foi CONFIRMADO com sucesso! 🎉\n\n" +
            "📋 Detalhes do seu atendimento:\n" +
            "📅 Data: {data_agendamento}\n" +
            "🕒 Horário: {hora_agendamento}\n" +
            "👨‍⚕️ Profissional: {profissional}\n" +
            "🛠️ Serviço: {serviço}\n" +
            "💰 Valor: {preço_serviço}\n" +
            "📞 Contato: {telefone_cliente}\n\n" +
          //  "🔗 Acesse nosso portal: {link_agendamento}\n\n" +
            "Agradecemos a preferência e esperamos vê-lo(a) em breve! 🙌";
    }

    // 3. Modelo para a Mensagem de Cancelamento
    function carregarModeloCancelamento() {
        document.getElementById('mensagemCancelamento').value =
            "😔 Olá {nome},\n\n" +
            "Lamentamos informar que seu agendamento foi CANCELADO:\n\n" +
            "📅 Data: {data_agendamento}\n" +
            "🕒 Horário: {hora_agendamento}\n" +
            "👨‍⚕️ Profissional: {profissional}\n" +
            "🛠️ Serviço: {serviço}\n" +
            "💰 Valor: {preço_serviço}\n\n" +
            "Caso deseje reagendar ou esclarecer dúvidas, estamos à disposição.\n" +
            "📞 Entre em contato conosco: {telefone_cliente}\n\n" +
            "Obrigado pela compreensão! 🙏";
    }

    // 4. Modelo para a Mensagem de Confirmação PARA O PROFISSIONAL
    function carregarModeloConfirmacaoProfissional() {
        document.getElementById('mensagemConfirmacaoProfissional').value =
            "✅ Olá {profissional}!\n\n" +
            "Informamos que o cliente CONFIRMOU o agendamento:\n\n" +
            "👤 Cliente: {nome}\n" +
            "📅 Data: {data_agendamento}\n" +
            "🕒 Horário: {hora_agendamento}\n" +
            "🛠️ Serviço: {serviço}\n" +
            "💰 Valor: {preço_serviço}\n" +
            "📞 Telefone: {telefone_cliente}\n\n" +
            "O atendimento está confirmado e o cliente comparecerá no horário marcado. 👍\n\n" +
            "Tenha um ótimo atendimento! 🎯";
    }

    // 5. Modelo para a Mensagem de Cancelamento PARA O PROFISSIONAL
    function carregarModeloCancelamentoProfissional() {
        document.getElementById('mensagemCancelamentoProfissional').value =
            "⚠️ Olá {profissional}!\n\n" +
            "Informamos que o cliente {nome} CANCELOU o agendamento:\n\n" +
            "📅 Data: {data_agendamento}\n" +
            "🕒 Horário: {hora_agendamento}\n" +
            "🛠️ Serviço: {serviço}\n" +
            "💰 Valor: {preço_serviço}\n" +
            "📞 Telefone: {telefone_cliente}\n\n" +
            "O horário está novamente disponível em sua agenda. 📅";
    }
</script>

<?php include 'footer.php'; ?>