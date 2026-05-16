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
    $situacao  = $rows_usuarios['situacao'];
    
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

if($situacao == 'ativado'){
    ?>

    <div class="container mt-5">
    <h2 class="text-center">Desconectar Chatbot</h2>

    <!-- Botão para abrir o modal de confirmação -->
    <div class="text-center mt-4">
        <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#confirmacaoModal">
            Desconectar Chatbot
        </button>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmacaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmacaoModalLabel">Confirmação de Ação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="lead">Tem certeza que deseja desconectar o chatbot?</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <!-- Botão "Não" redireciona para config_adm.php -->
                    <button type="button" class="btn btn-secondary btn-lg" data-fn="__navigate" data-args='["config_adm.php"]'>Não</button>

                    <!-- Botão "Sim" envia o POST para rest.php -->
                    <form action="desconecta_confirma.php" method="POST">
                        <input type="hidden" name="acao" value="desconectar_chatbot">
                        <input type="hidden" name="usuario_api" value="<?= htmlspecialchars($usuario_api ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-danger btn-lg">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

}
?>

<?php
if($situacao == 'desativado'){
    ?>
<div class="container mt-5">
    <h2 class="text-center">Ativar Chatbot</h2>

    <!-- Botão para abrir o modal de confirmação -->
    <div class="text-center mt-4">
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#confirmacaoModal">
            Ativar Chatbot
        </button>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmacaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmacaoModalLabel">Confirmação de Ação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="lead">Tem certeza que deseja ativar o chatbot?</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <!-- Botão "Não" redireciona para config_adm.php -->
                    <button type="button" class="btn btn-secondary btn-lg" data-fn="__navigate" data-args='["config_adm.php"]'>Não</button>

                    <!-- Botão "Sim" envia o POST para ativar_chatbot.php -->
                    <form action="ativar_chatbot.php" method="POST">
                        <input type="hidden" name="acao" value="ativar_chatbot">
                        <input type="hidden" name="usuario_api" value="<?= htmlspecialchars($usuario_api ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-success btn-lg">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php    
}
?>

<!-- Inclua o jQuery e Bootstrap para o modal funcionar corretamente -->

<!-- Custom CSS para melhorar a proporção e centralizar os elementos -->
<style>
    .container {
        max-width: 600px;
        margin: auto;
    }

    h2 {
        color: #333;
        margin-bottom: 30px;
        font-weight: 700;
    }

    .btn-danger {
        font-size: 18px;
        padding: 10px 20px;
    }

    .modal-header {
        background-color: #f8d7da;
    }

    .modal-header h5 {
        color: #721c24;
    }

    .modal-body p {
        font-size: 20px;
        color: #333;
        margin-bottom: 20px;
    }

    .modal-footer {
        padding: 15px;
        display: flex;
        justify-content: space-between;
    }

    .btn-lg {
        font-size: 18px;
        padding: 10px 20px;
        min-width: 120px;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        
    }

    .modal-content {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-dialog-centered {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-footer {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
</style>

<?php include 'footer.php'; ?>