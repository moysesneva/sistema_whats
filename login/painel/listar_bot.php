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

$stmt_user = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_user, "s", $login);
mysqli_stmt_execute($stmt_user);
$query_busca_usuario = mysqli_stmt_get_result($stmt_user);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
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

<?php $css_extra = '    <link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/vendor/toastr/toastr.min.css">'; ?>
<?php include 'header.php'; ?>

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

                                               <div class="col-md-4 text-right">

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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

                                            </div>
                                        </div>
                                    </div>
                                    
                                  <!-- Conteúdo principal -->
<?php

// Processa alteração de créditos
if (isset($_POST['usuario_id']) && isset($_POST['quantidade_creditos'])) {
    $usuario_id = (int) $_POST['usuario_id'];
    $quantidade_creditos = (int) $_POST['quantidade_creditos'];
    
    $stmt_cr = $conn->prepare("UPDATE login SET creditos = ? WHERE id = ?");
    $stmt_cr->bind_param("ii", $quantidade_creditos, $usuario_id);

    if ($stmt_cr->execute()) {
        echo '<div class="alert alert-success">Créditos atualizados com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao atualizar créditos: ' . $conn->error . '</div>';
    }
    $stmt_cr->close();
}

// Processa alteração de plano
if (isset($_POST['usuario_id_plano']) && isset($_POST['novo_plano'])) {
    $usuario_id = (int) $_POST['usuario_id_plano'];
    $novo_plano = trim($_POST['novo_plano']);

    $stmt_plano = $conn->prepare("UPDATE login SET plano = ? WHERE id = ?");
    $stmt_plano->bind_param("si", $novo_plano, $usuario_id);

    if ($stmt_plano->execute()) {
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

                while($rows_usuarios = $query_busca_usuario->fetch_array()) {
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
                                <button type="button" class="btn btn-sm btn-success" data-fn="executarAcao" data-args='["<?=$usuario_api;?>", "iniciar"]' data-toggle="tooltip" title="Iniciar Bot">
                                    <i class="feather icon-play"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" data-fn="executarAcao" data-args='["<?=$usuario_api;?>", "parar"]' data-toggle="tooltip" title="Parar Bot">
                                    <i class="feather icon-pause"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" data-fn="executarAcao" data-args='["<?=$usuario_api;?>", "bloquear"]' data-toggle="tooltip" title="Bloquear Bot">
                                    <i class="feather icon-lock"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" data-fn="mostrarImagemBot" data-args='["<?=$usuario_api;?>", "<?=htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');?>"]' data-toggle="tooltip" title="Visualizar QR Code">
                                    <i class="feather icon-image"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-danger" data-fn="executarAcao" data-args='["<?=$usuario_api;?>", "deletar"]' data-toggle="tooltip" title="Deletar Bot">
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

<script type="text/javascript" nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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

<?php include 'footer.php'; ?>