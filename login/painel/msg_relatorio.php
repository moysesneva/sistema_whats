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
    $usuario_api = $rows_usuarios['usuario_api'];

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

<?php
// Incluir conexão com banco de dados
require_once 'conn.php';
$login = $_SESSION['login'];

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
    $usuario_api = $rows_usuarios['usuario_api'];

}
// Buscar campanhas do banco de dados
$stmt_mr = $conn->prepare("SELECT id, campaign_name, total_clientes, enviados, erros, status, created_at, media_type, login, usuario_api FROM mensagens_massa WHERE usuario_api = ? ORDER BY created_at DESC");
$stmt_mr->bind_param("s", $usuario_api);
$stmt_mr->execute();
$result = $stmt_mr->get_result();
$stmt_mr->close();

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

$campanhas = [];
while ($row = $result->fetch_assoc()) {
    $campanhas[] = $row;
}
?>

    <div class="container">
        <!-- Header da Página -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fab fa-whatsapp"></i>
                Campanhas WhatsApp
            </h1>
            <p class="page-subtitle">
                Gerencie suas campanhas de marketing em massa
            </p>
        </div>

        <?php if (empty($campanhas)): ?>
            <!-- Mensagem quando não há campanhas -->
            <div class="no-campaigns">
                <i class="fas fa-inbox"></i>
                <h3>Nenhuma campanha encontrada</h3>
                <p>Você ainda não criou nenhuma campanha de marketing.</p>
            </div>
        <?php else: ?>
            <!-- Loop das Campanhas -->
            <?php foreach ($campanhas as $campanha): ?>
                <?php
                // Calcular progresso
                $total = (int)$campanha['total_clientes'];
                $enviados = (int)$campanha['enviados'];
                $faltam = $total - $enviados;
                $porcentagem = $total > 0 ? round(($enviados / $total) * 100, 1) : 0;
                
                // Formatação da data
                $data_criacao = new DateTime($campanha['created_at']);
                $data_formatada = $data_criacao->format('d/m/Y \à\s H:i');
                
                // Classe do status
                $status_class = '';
                switch ($campanha['status']) {
                    case 'concluida':
                        $status_class = 'status-concluida';
                        break;
                    case 'pausada':
                        $status_class = 'status-pausada';
                        break;
                    case 'erro':
                        $status_class = 'status-erro';
                        break;
                    default:
                        $status_class = '';
                }
                
                // Ícone do tipo de mídia
                $media_icon = 'fas fa-comment';
                switch ($campanha['media_type']) {
                    case 'image':
                        $media_icon = 'fas fa-image';
                        break;
                    case 'video':
                        $media_icon = 'fas fa-video';
                        break;
                    case 'audio':
                        $media_icon = 'fas fa-microphone';
                        break;
                    case 'document':
                        $media_icon = 'fas fa-file';
                        break;
                }
                ?>
                
                <div class="campaign-card">
                    <!-- Header -->
                    <div class="card-header-simple <?php echo $status_class; ?>">
                        <h2 class="campaign-title">
                            <i class="<?php echo $media_icon; ?>"></i>
                            <?php echo htmlspecialchars($campanha['campaign_name'] ?: 'Campanha sem nome', ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                        <div class="campaign-date">
                            <i class="fas fa-calendar-alt"></i>
                            Criada em <?php echo $data_formatada; ?>
                        </div>
                        <span class="campaign-status">
                            <?php echo ucfirst($campanha['status']); ?>
                        </span>
                    </div>

                    <!-- Body -->
                    <div class="card-body-simple">
                        <!-- Progresso -->
                        <div class="progress-section">
                            <div class="progress-header">
                                <div class="progress-title">
                                    <i class="fas fa-paper-plane"></i>
                                    Progresso do Envio
                                </div>
                                <div class="progress-numbers">
                                    <?php echo number_format($enviados, 0, ',', '.'); ?> / <?php echo number_format($total, 0, ',', '.'); ?>
                                </div>
                            </div>
                            
                            <div class="progress-bar-container">
                                <div class="progress-bar-custom" style="width: <?php echo $porcentagem; ?>%"></div>
                            </div>
                            
                            <div class="progress-details">
                                <span>Enviadas: <strong><?php echo number_format($enviados, 0, ',', '.'); ?></strong></span>
                                <span>Faltam: <strong><?php echo number_format($faltam, 0, ',', '.'); ?></strong></span>
                                <span class="progress-percentage"><?php echo $porcentagem; ?>%</span>
                            </div>
                            
                            <?php if ($campanha['erros'] > 0): ?>
                                <div class="progress-details mt-2">
                                    <span style="color: #ef4444;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Erros: <strong><?php echo number_format($campanha['erros'], 0, ',', '.'); ?></strong>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Botão Apagar -->
                        <button type="button" class="btn btn-delete" data-toggle="modal" data-target="#deleteModal" onclick="setCampaignToDelete(<?php echo $campanha['id']; ?>, '<?php echo htmlspecialchars($campanha['campaign_name'], ENT_QUOTES); ?>')">
                            <i class="fas fa-trash"></i>
                            Apagar Campanha
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="warning-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4 class="mb-3">Tem certeza que deseja apagar?</h4>
                    <p class="text-muted mb-3">
                        Esta ação não pode ser desfeita. A campanha "<span id="campaignNameToDelete"></span>" será permanentemente excluída.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-outline-modern" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-modern btn-danger-modern" onclick="deleteCampaign()">
                        <i class="fas fa-trash"></i>
                        Sim, Apagar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let campaignIdToDelete = null;

        // Função para definir qual campanha será deletada
        function setCampaignToDelete(id, name) {
            campaignIdToDelete = id;
            document.getElementById('campaignNameToDelete').textContent = name;
        }

        // Função para apagar campanha
        function deleteCampaign() {
            if (!campaignIdToDelete) {
                showNotification('Erro: ID da campanha não encontrado', 'error');
                return;
            }

            // Mostrar loading
            const deleteBtn = document.querySelector('#deleteModal .btn-danger-modern');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Apagando...';
            deleteBtn.disabled = true;
            
            // Fazer requisição AJAX para apagar
            fetch('delete_campaign.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    campaign_id: campaignIdToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botão
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                
                if (data.success) {
                    // Fechar modal
                    $('#deleteModal').modal('hide');
                    
                    // Mostrar notificação de sucesso
                    showNotification('Campanha apagada com sucesso!', 'success');
                    
                    // Recarregar página após 2 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Erro ao apagar campanha', 'error');
                }
            })
            .catch(error => {
                // Restaurar botão
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                
                console.error('Erro:', error);
                showNotification('Erro na requisição', 'error');
            });
        }
        
        // Função para mostrar notificações
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="${iconClass}"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            `;
            
            document.body.appendChild(notification);
            
            // Remover após 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Auto-refresh da página a cada 30 segundos (opcional)
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>

<?php include 'footer.php'; ?>