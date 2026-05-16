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

   <link rel="stylesheet" href="../files/assets/vendor/font-awesome-6/css/all.min.css">

   <div class="container mt-5">
    <h2 class="text-center">Gerenciar Agendamentos</h2>

    <!-- Selecionar o Profissional -->
    <div class="form-group">
        <label for="profissional">Selecione o Profissional</label>
        <select class="form-control" id="profissional" name="profissional" data-change-fn="carregarAgendamentos">
            <option value="">Escolha um profissional</option>
            <?php
            // Conexão com o banco de dados
         
            // Consulta para obter os profissionais
            $stmt_ea = $conn->prepare("SELECT * FROM profissional WHERE login = ?");
            $stmt_ea->bind_param("s", $login);
            $stmt_ea->execute();
            $result = $stmt_ea->get_result();
            $stmt_ea->close();

            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['profissional_nome'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($row['profissional_cargo'], ENT_QUOTES, 'UTF-8') . '</option>';
            }

            // Fechar a conexão
            mysqli_close($conn);
            ?>
        </select>
    </div>

    <!-- Tabela para exibir os agendamentos -->
    <div id="agendamentos" class="mt-4">
        <!-- A tabela de agendamentos será carregada aqui via AJAX -->
    </div>

</div>

<!-- Script AJAX para carregar agendamentos com base no profissional selecionado -->
<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
function carregarAgendamentos() {
    var profissionalId = $('#profissional').val();
    if (profissionalId !== '') {
        $.ajax({
            url: 'buscar_agendamentos.php',
            type: 'POST',
            data: { profissional_id: profissionalId },
            success: function(response) {
                $('#agendamentos').html(response);
            }
        });
    } else {
        $('#agendamentos').html('');
    }
}

// Função para deletar um agendamento
function deletarAgendamento(id) {
    if (confirm('Tem certeza que deseja deletar este agendamento?')) {
        $.ajax({
            url: 'deletar_agendamento.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert(response);
                carregarAgendamentos(); // Recarrega a tabela após a exclusão
            }
        });
    }
}
</script>

<?php include 'footer.php'; ?>