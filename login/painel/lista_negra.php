<?php
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

    <?php
    // Incluir arquivo de conexão com banco
    include 'conn.php';
    
    // Função para preparar telefone para WhatsApp
    function prepararTelefoneWhatsApp($telefone) {
        // Remove tudo que não é número
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        
        // Se estiver vazio, retorna vazio
        if (empty($telefone_limpo)) {
            return '';
        }
        
        // Se já tem código do país (55), remove para reprocessar
        if (substr($telefone_limpo, 0, 2) == '55' && strlen($telefone_limpo) >= 12) {
            $telefone_limpo = substr($telefone_limpo, 2);
        }
        
        // Se tem 11 dígitos (DDD + 9 + 8 dígitos) - celular com 9
        if (strlen($telefone_limpo) == 11) {
            return '55' . $telefone_limpo;
        }
        
        // Se tem 10 dígitos (DDD + 8 dígitos) - fixo ou celular antigo
        if (strlen($telefone_limpo) == 10) {
            return '55' . $telefone_limpo;
        }
        
        // Se tem 9 dígitos, assume que faltou o DDD, adiciona um padrão (11 - SP)
        if (strlen($telefone_limpo) == 9) {
            return '5511' . $telefone_limpo;
        }
        
        // Se tem 8 dígitos, assume que faltou DDD, adiciona um padrão (11 - SP)
        if (strlen($telefone_limpo) == 8) {
            return '5511' . $telefone_limpo;
        }
        
        // Para outros casos, tenta adicionar o código do Brasil
        if (strlen($telefone_limpo) >= 8) {
            return '55' . $telefone_limpo;
        }
        
        // Se muito curto, retorna vazio
        return '';
    }
    
    // Obter usuário da API (ajuste conforme sua sessão/autenticação)
    #$usuario_api = $_SESSION['usuario_api'] ?? 'user_001';
    
    // Consulta SQL para buscar registros da lista negra
    $sql_busca_lista_negra = "
        SELECT 
            id,
            nome,
            telefone,
            motivo_bloqueio,
            data_bloqueio,
            tentativas_contato,
            observacoes,
            ultima_tentativa
        FROM lista_negra 
        WHERE usuario_api = ? 
        AND status = 'ativo'
        ORDER BY data_bloqueio DESC
    ";
    
    // Preparar e executar consulta
    $stmt = $conn->prepare($sql_busca_lista_negra);
    $stmt->bind_param("s", $usuario_api);
    $stmt->execute();
    $query_busca_lista_negra = $stmt->get_result();
    $total_busca_lista_negra = $query_busca_lista_negra->num_rows;
    ?>

    <div class="page-container">
        <!-- Cabeçalho da página -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title">
                    <h1><i class="fas fa-ban"></i> Bloqueados</h1>
                    <p class="header-subtitle">Sistema de controle e bloqueio de contatos indesejados</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="material-icons">print</i>
                        Imprimir
                    </button>
                    <button id="btnAdicionarContato" class="btn btn-danger" onclick="openModal('addContatoModal')">
                        <i class="material-icons">person_add_disabled</i>
                        Bloquear Contato
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon blacklist">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalBloqueados">
                        <?php echo $total_busca_lista_negra; ?>
                    </h3>
                    <p>Contatos Bloqueados</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="blacklist-container">
            <div class="blacklist-header">
                <div class="blacklist-title"><i class="fas fa-shield-alt"></i> Lista de Bloqueios</div>
                <div class="blacklist-count">
                    <?php echo $total_busca_lista_negra; ?> contato<?php echo $total_busca_lista_negra != 1 ? 's' : ''; ?> bloqueado<?php echo $total_busca_lista_negra != 1 ? 's' : ''; ?>
                </div>
            </div>
            
            <div class="table-container">
                <?php if ($total_busca_lista_negra > 0) { ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Nome/Identificação</th>
                            <th><i class="fas fa-phone"></i> Telefone</th>
                            <th><i class="fas fa-exclamation-triangle"></i> Motivo</th>
                            <th><i class="fas fa-calendar"></i> Data do Bloqueio</th>
                            <th><i class="fas fa-cog"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($row = $query_busca_lista_negra->fetch_assoc()) {
                        $id_contato = $row['id'];
                        $nome = $row['nome'] ?? 'Contato sem nome';
                        $telefone = $row['telefone'] ?? '';
                        $motivo_bloqueio = $row['motivo_bloqueio'] ?? 'Não especificado';
                        $data_bloqueio = !empty($row['data_bloqueio']) ? date('d/m/Y H:i', strtotime($row['data_bloqueio'])) : 'Data não informada';
                        $tentativas_contato = (int)($row['tentativas_contato'] ?? 0);
                        $observacoes = $row['observacoes'] ?? '';
                        
                        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                        
                        // Formatar telefone para exibição
                        $telefone_exibicao = $telefone;
                        if (strlen($telefone_limpo) == 11) {
                            $ddd = substr($telefone_limpo, 0, 2);
                            $numero = substr($telefone_limpo, 2);
                            if (strlen($numero) >= 9) {
                                $parte1 = substr($numero, 0, 5);
                                $parte2 = substr($numero, 5);
                                $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                            }
                        } elseif (strlen($telefone_limpo) == 10) {
                            $ddd = substr($telefone_limpo, 0, 2);
                            $numero = substr($telefone_limpo, 2);
                            if (strlen($numero) >= 8) {
                                $parte1 = substr($numero, 0, 4);
                                $parte2 = substr($numero, 4);
                                $telefone_exibicao = "({$ddd}) {$parte1}-{$parte2}";
                            }
                        }
                        
                        // Preparar telefone para WhatsApp com código do país
                        $telefone_whatsapp = prepararTelefoneWhatsApp($telefone_limpo);
                    ?>
                        <tr>
                            <td>
                                <div class="contact-name"><?php echo htmlspecialchars($nome); ?></div>
                                <?php if($tentativas_contato > 0) { ?>
                                <small style="color: #ff6b6b;"><?php echo $tentativas_contato; ?> tentativa(s) após bloqueio</small>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="contact-phone"><?php echo htmlspecialchars($telefone_exibicao); ?></div>
                            </td>
                            <td>
                                <span class="motivo-badge"><?php echo htmlspecialchars($motivo_bloqueio); ?></span>
                            </td>
                            <td>
                                <div style="color: #aaa;"><?php echo $data_bloqueio; ?></div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if(!empty($telefone_whatsapp) && strlen($telefone_whatsapp) >= 10) { ?>
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $telefone_whatsapp; ?>&text=Olá!" 
                                       class="btn btn-sm btn-whatsapp" target="_blank" title="Contatar via WhatsApp - <?php echo $telefone_whatsapp; ?>">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <?php } ?>
                                  
                                    <button type="button" class="btn btn-sm btn-delete" 
                                            onclick="confirmarRemocao(<?php echo $id_contato; ?>, '<?php echo addslashes($nome); ?>')" 
                                            title="Remover da Lista">
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
                    <div class="fas fa-shield-alt empty-icon"></div>
                    <h3>Lista negra vazia</h3>
                    <p>Nenhum contato foi bloqueado ainda!</p>
                    <button class="btn btn-danger" onclick="openModal('addContatoModal')" style="margin-top: 20px;">
                        <i class="material-icons">person_add_disabled</i>
                        Bloquear Primeiro Contato
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Contato à Lista Negra -->
    <div class="modal" id="addContatoModal">
        <div class="modal-dialog">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-ban"></i> Bloquear Novo Contato</h5>
                <button type="button" class="modal-close" onclick="closeModal('addContatoModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formAddContato" action="processa_lista_negra.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    
                    <div class="form-group">
                        <label for="nome">Nome/Identificação do Contato</label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               placeholder="Ex: João Silva (Spam), Empresa XYZ (Telemarketing)">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" required 
                               placeholder="(11) 99999-9999">
                    </div>

                    <div class="form-group">
                        <label for="motivo_bloqueio">Motivo do Bloqueio</label>
                        <select class="form-control" id="motivo_bloqueio" name="motivo_bloqueio" required>
                            <option value="">Selecione o motivo</option>
                            <option value="Spam/Mensagens indesejadas">Spam/Mensagens indesejadas</option>
                            <option value="Telemarketing agressivo">Telemarketing agressivo</option>
                            <option value="Cobrança indevida">Cobrança indevida</option>
                            <option value="Vendas não autorizadas">Vendas não autorizadas</option>
                            <option value="Contato inconveniente">Contato inconveniente</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações (Opcional)</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                                  placeholder="Detalhes adicionais sobre o motivo do bloqueio..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addContatoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i>
                        Bloquear Contato
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Contato -->
    <div class="modal" id="editContatoModal">
        <div class="modal-dialog">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Contato Bloqueado</h5>
                <button type="button" class="modal-close" onclick="closeModal('editContatoModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <form id="formEditContato" action="processa_lista_negra.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id_contato" id="edit_id_contato">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    
                    <div class="form-group">
                        <label for="edit_nome">Nome/Identificação do Contato</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_telefone">Telefone</label>
                        <input type="tel" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_motivo_bloqueio">Motivo do Bloqueio</label>
                        <select class="form-control" id="edit_motivo_bloqueio" name="motivo_bloqueio" required>
                            <option value="">Selecione o motivo</option>
                            <option value="Spam/Mensagens indesejadas">Spam/Mensagens indesejadas</option>
                            <option value="Telemarketing agressivo">Telemarketing agressivo</option>
                            <option value="Cobrança indevida">Cobrança indevida</option>
                            <option value="Vendas não autorizadas">Vendas não autorizadas</option>
                            <option value="Contato inconveniente">Contato inconveniente</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_observacoes">Observações</label>
                        <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editContatoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">update</i>
                        Atualizar Contato
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Remoção -->
    <div class="modal" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header bg-delete">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Confirmar Remoção</h5>
                <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <p>Tem certeza que deseja remover <span id="contatoNome" style="font-weight: 600;"></span> da lista negra?</p>
                    <p style="margin: 10px 0 0 0;"><strong>Este contato poderá entrar em contato novamente!</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancelar</button>
                <form id="deleteForm" action="processa_lista_negra.php" method="POST" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" id="deleteContatoId" name="id_contato" value="">
                    <input type="hidden" name="usuario_api" value="<?php echo htmlspecialchars($usuario_api); ?>">
                    <button type="submit" class="btn btn-delete">
                        <i class="material-icons">delete_forever</i>
                        Confirmar Remoção
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Função para editar contato
        function editarContato(id, nome, telefone, motivo, observacoes) {
            document.getElementById('edit_id_contato').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_telefone').value = telefone;
            document.getElementById('edit_motivo_bloqueio').value = motivo;
            document.getElementById('edit_observacoes').value = observacoes;
            openModal('editContatoModal');
        }

        // Função para confirmar remoção
        function confirmarRemocao(id, nome) {
            document.getElementById('deleteContatoId').value = id;
            document.getElementById('contatoNome').textContent = nome;
            openModal('deleteModal');
        }

        // Máscara para telefone melhorada
        function aplicarMascaraTelefone(elemento) {
            elemento.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limita a 11 dígitos
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                if (value.length <= 11) {
                    if (value.length <= 2) {
                        value = value.replace(/(\d{0,2})/, '($1');
                    } else if (value.length <= 7) {
                        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                    }
                }
                e.target.value = value;
            });
            
            // Validação ao sair do campo
            elemento.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length < 10 || value.length > 11) {
                    e.target.style.borderColor = '#ff4757';
                    e.target.title = 'Telefone deve ter 10 ou 11 dígitos (DDD + número)';
                } else {
                    e.target.style.borderColor = '#444';
                    e.target.title = '';
                }
            });
        }

        // Aplicar máscara aos campos de telefone
        document.addEventListener('DOMContentLoaded', function() {
            aplicarMascaraTelefone(document.getElementById('telefone'));
            aplicarMascaraTelefone(document.getElementById('edit_telefone'));
            
            // Animação de entrada dos elementos
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

        // Fechar todos os modais e recarregar
        (function(){
            function fecharTodosModais(reloadPage = false) {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                });
                document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
                document.body.style.overflow = 'auto';
                if (reloadPage) {
                    window.location.reload();
                }
            }

            document.addEventListener('click', e => {
                if (e.target.closest('.modal-close') || e.target.closest('.modal .btn-secondary')) {
                    e.preventDefault();
                    fecharTodosModais(true);
                }
                else if (e.target.classList.contains('modal')) {
                    fecharTodosModais(false);
                }
            });

            window.addEventListener('load', () => fecharTodosModais(false));
        })();
    </script>

    <?php
    // Fechar conexão
    $stmt->close();
    $conn->close();
    ?>
                        
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