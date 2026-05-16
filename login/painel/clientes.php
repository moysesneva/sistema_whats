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
include 'config_dados.php';

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

<?php $css_extra = '    <link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" href="../files/assets/vendor/intl-tel-input/css/intlTelInput.css">'; ?>
<?php include 'header.php'; ?>

    <div class="page-container">
        <!-- Cabeçalho da página -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title">
                    <h1>Gerenciamento de Clientes</h1>
                    <p class="header-subtitle">Gerencie seus clientes de forma fácil e organizada</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" data-fn="__window_print">
                        <i class="material-icons">print</i>
                        Imprimir
                    </button>
                    <button id="btnAdicionarCliente" class="btn btn-success" data-fn="openModal" data-args='["addClienteModal"]'>
                        <i class="material-icons">person_add</i>
                        Novo Cliente
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon clients">
                    <i class="material-icons">people</i>
                </div>
                <div class="stat-content">
                    <h3 id="totalClientes">
                        <?php 
$stmt_cli = $conn->prepare("SELECT * FROM clientes WHERE usuario_api = ? ORDER BY CASE WHEN nome IS NULL OR nome = '' THEN 1 ELSE 0 END, nome ASC");
                        $stmt_cli->bind_param("s", $usuario_api);
                        $stmt_cli->execute();
                        $query_busca_clientes = $stmt_cli->get_result();
                        $total_busca_clientes = $query_busca_clientes->num_rows;
                        $stmt_cli->close();
                        echo $total_busca_clientes;
                        ?>
                    </h3>
                    <p>Total de Clientes</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="clients-container">
            <div class="clients-header">
                <div class="clients-title">Lista de Clientes</div>
                <div class="clients-count">
                    <?php echo $total_busca_clientes; ?> cliente<?php echo $total_busca_clientes != 1 ? 's' : ''; ?> cadastrado<?php echo $total_busca_clientes != 1 ? 's' : ''; ?>
                </div>
            </div>
            
            <div class="table-container">
                <?php if ($total_busca_clientes > 0) { ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($rows_clientes = mysqli_fetch_array($query_busca_clientes)) {
                        $id_cliente = $rows_clientes['id'];
                        $nome = $rows_clientes['nome'];
                        $telefone = $rows_clientes['telefone'];
                        $endereco = isset($rows_clientes['endereco']) ? $rows_clientes['endereco'] : 'Endereço não cadastrado';
                        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                        
                        // Formatar telefone para exibição
                        $telefone_exibicao = $telefone;
                        if (strpos($telefone, 'undefined') === 0) {
                            $telefone_exibicao = substr($telefone, 9);
                        }
                        
                        if (strlen($telefone_exibicao) > 10) {
                            $codigo_pais = substr($telefone_exibicao, 0, 2);
                            if ($codigo_pais == "55") {
                                $ddd = substr($telefone_exibicao, 2, 2);
                                $numero = substr($telefone_exibicao, 4);
                                if (strlen($numero) >= 9) {
                                    $parte1 = substr($numero, 0, 5);
                                    $parte2 = substr($numero, 5);
                                    $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                                }
                            } else {
                                $telefone_exibicao = "+" . $telefone_exibicao;
                            }
                        }
                        
                        // Limpar telefone para WhatsApp
                        $telefone_whatsapp = preg_replace('/[^0-9]/', '', $telefone);
                        if (substr($telefone_whatsapp, 0, 9) === "undefined") {
                            $telefone_whatsapp = substr($telefone_whatsapp, 9);
                        }
                    ?>
                        <tr>
                            <td>
                                <div class="client-name"><?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?></div>
                            </td>
                            <td>
                                <div class="client-phone"><?php echo htmlspecialchars($telefone_limpo, ENT_QUOTES, 'UTF-8'); ?></div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $telefone_whatsapp; ?>" 
                                       class="btn btn-sm btn-whatsapp" target="_blank" title="Enviar WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-edit" 
                                            data-fn="editarCliente" data-args='[<?php echo (int)$id_cliente; ?>, "<?php echo htmlspecialchars(addslashes($nome), ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars(addslashes($telefone), ENT_QUOTES, 'UTF-8'); ?>", "<?php echo htmlspecialchars(addslashes($endereco), ENT_QUOTES, 'UTF-8'); ?>"]' 
                                            title="Editar Cliente">
                                        <i class="material-icons">edit</i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" 
                                            data-fn="confirmarExclusao" data-args='[<?php echo (int)$id_cliente; ?>, "<?php echo htmlspecialchars(addslashes($nome), ENT_QUOTES, 'UTF-8'); ?>"]' 
                                            title="Excluir Cliente">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="empty-state">
                    <div class="material-icons empty-icon">person_add</div>
                    <h3>Nenhum cliente encontrado</h3>
                    <p>Comece adicionando seu primeiro cliente!</p>
                    <button class="btn btn-success" data-fn="openModal" data-args='["addClienteModal"]' style="margin-top: 20px;">
                        <i class="material-icons">person_add</i>
                        Adicionar Primeiro Cliente
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Cliente -->
    <div class="modal" id="addClienteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-success">
                <h5 class="modal-title">Adicionar Novo Cliente</h5>
                <button type="button" class="modal-close" data-fn="closeModal" data-args='["addClienteModal"]'>
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formAddCliente" action="processa_cliente.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id_cliente" value="<?php echo (int)$id_cliente; ?>">
                    
                    <div class="form-group">
                        <label for="nome">Nome do Cliente</label>
                        <input type="text" class="form-control" id="nome" name="nome" required placeholder="Digite o nome completo">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" required placeholder="(11) 99999-9999">
                        <input type="hidden" id="codigo_pais" name="codigo_pais">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-fn="closeModal" data-args='["addClienteModal"]'>Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons">save</i>
                        Salvar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Cliente -->
    <div class="modal" id="editClienteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Editar Cliente</h5>
                <button type="button" class="modal-close" data-fn="closeModal" data-args='["editClienteModal"]'>
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formEditCliente" action="processa_cliente.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id_cliente" id="edit_id_cliente">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="form-group">
                        <label for="edit_nome">Nome do Cliente</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_telefone">Telefone</label>
                        <input type="tel" class="form-control" id="edit_telefone" name="telefone" required>
                        <input type="hidden" id="edit_codigo_pais" name="edit_codigo_pais">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-fn="closeModal" data-args='["editClienteModal"]'>Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">update</i>
                        Atualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="modal-close" data-fn="closeModal" data-args='["deleteModal"]'>
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <p>Tem certeza que deseja excluir o cliente <span id="clienteNome" style="font-weight: 600;"></span>?</p>
                    <p style="margin: 10px 0 0 0;"><strong>Esta ação não pode ser desfeita!</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-fn="closeModal" data-args='["deleteModal"]'>Cancelar</button>
                <form id="deleteForm" action="processa_cliente.php" method="POST" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" id="deleteClienteId" name="id_cliente" value="">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-delete">
                        <i class="material-icons">delete_forever</i>
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
(function(){
  // Fecha todos os modais; se reloadPage for true, faz um reload no fim
  function fecharTodosModais(reloadPage = false) {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.classList.remove('show');
      modal.style.display = 'none';
    });
    document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
    document.body.style.overflow = 'auto';
    if (reloadPage) {
      // garante um único reload
      window.location.reload();
    }
  }

  document.addEventListener('click', e => {
    // botões de fechar ("X") e "Cancelar"
    if (e.target.closest('.modal-close') || e.target.closest('.modal .btn-secondary')) {
      e.preventDefault();
      fecharTodosModais(true);
    }
    // clique na máscara
    else if (e.target.classList.contains('modal')) {
      fecharTodosModais(false);
    }
  });

  // ao carregar, limpa qualquer resto de modal/backdrop sem recarregar
  window.addEventListener('load', () => fecharTodosModais(false));
})();
</script>

    <script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        // Funções para controle de modais
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Fechar modal ao clicar fora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target.id);
            }
        });

     // Em vez de $('#editClienteModal').modal('show');
function editarCliente(id, nome, telefone, endereco) {
    document.getElementById('edit_id_cliente').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_telefone').value = telefone.replace(/\D/g, '').replace(/^undefined/, '');
    document.getElementById('edit_endereco').value = endereco;
    openModal('editClienteModal');
}

// Em vez de $('#deleteModal').modal('show');
function confirmarExclusao(id, nome) {
    document.getElementById('deleteClienteId').value = id;
    document.getElementById('clienteNome').textContent = nome;
    openModal('deleteModal');
}

        // Animação de entrada dos elementos
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            const tableRows = document.querySelectorAll('.modern-table tbody tr');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, (index * 50) + 300);
            });
        });
    </script>
                        
            </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para o plugin intl-tel-input */
        .iti {
            width: 100%;
        }
        .iti__country-list {
            position: absolute;
            z-index: 9999;
            max-height: 200px;
        }
        .form-group {
            margin-bottom: 25px; /* Espaço suficiente para o dropdown */
        }
        .iti--separate-dial-code .iti__selected-flag {
            background-color: #f8f9fa;
        }
    </style>

<?php include 'footer.php'; ?>