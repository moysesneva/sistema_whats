
<?php
// ===============================================
// ARQUIVO: processar_mensagens.php (opcional)
// ===============================================

// Função para exibir mensagens de confirmação/erro na página
function exibirMensagens() {
    if (isset($_GET['confirmacao'])) {
        switch($_GET['confirmacao']) {
            case 'especialidade_adicionada':
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather icon-check"></i> Especialidade adicionada com sucesso!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
            case 'profissional_cadastrado':
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather icon-check"></i> Profissional cadastrado com sucesso!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
            case 'profissional_atualizado':
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather icon-check"></i> Profissional atualizado com sucesso!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
            case 'profissional_deletado':
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather icon-check"></i> Profissional deletado com sucesso!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
        }
    }
    
    if (isset($_GET['erro'])) {
        switch($_GET['erro']) {
            case 'especialidade_erro':
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="feather icon-x"></i> Erro ao adicionar especialidade!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
            case 'profissional_erro':
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="feather icon-x"></i> Erro ao cadastrar profissional!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
            case 'usuario_nao_encontrado':
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="feather icon-x"></i> Usuário não encontrado!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                break;
        }
    }
}

// Chamar esta função no início da página cadastrar_profissional.php:
// <?php include 'processar_mensagens.php'; exibirMensagens(); ?>
?>