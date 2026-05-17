<?php
require_once __DIR__ . '/auth_guard.php';
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

$css_extra = '<link rel="stylesheet" href="../files/assets/vendor/font-awesome-6/css/all.min.css">';
?>
<?php include 'header.php'; ?>


<!-- Menu de Navegação com botões personalizados -->
<div class="navigation-section fade-in">
    <h2 class="main-title">
        <i class="fas fa-calendar-alt me-3"></i>Sistema de Agendamento
    </h2>
    
    <div class="menu-grid">
        <!-- Cadastrar Profissional -->
        <div class="nav-card nav-card-primary" data-fn="__navigate" data-args='["cadastrar_profissional.php"]'>
            <i class="fas fa-user-plus nav-icon"></i>
            <h5 class="nav-title">Cadastrar Profissional</h5>
            <p class="nav-subtitle">Adicione novos profissionais ao sistema</p>
        </div>

        <!-- Cadastro Horários e Serviços -->
        <div class="nav-card nav-card-info" data-fn="__navigate" data-args='["cadastrar_horario.php"]'>
            <i class="fas fa-clock nav-icon"></i>
            <h5 class="nav-title">Horários e Serviços</h5>
            <p class="nav-subtitle">Configure horários e cadastre serviços</p>
        </div>

        <!-- Agendar Cliente -->
        <div class="nav-card nav-card-success pulse-animation" data-fn="__navigate" data-args='["agendar_servico.php"]'>
            <i class="fas fa-user-check nav-icon"></i>
            <h5 class="nav-title">Agendar Cliente</h5>
            <p class="nav-subtitle">Faça novos agendamentos para clientes</p>
        </div>

        <!-- Minhas Configurações -->
        <div class="nav-card nav-card-warning" data-fn="__navigate" data-args='["configuracoes.php"]'>
            <i class="fas fa-cog nav-icon"></i>
            <h5 class="nav-title">Minhas Configurações</h5>
            <p class="nav-subtitle">Personalize e ajuste suas preferências</p>
        </div>
    </div>
</div>

<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
// Adiciona efeito de clique suave
document.querySelectorAll('.nav-card').forEach(card => {
    card.addEventListener('click', function() {
        // Adiciona feedback visual ao clicar
        this.style.transform = 'translateY(-5px) scale(0.98)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Animação de entrada escalonada
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.animation = `fadeInUp 0.6s ease-out ${index * 0.2}s both`;
        }, 100);
    });
});
</script>

<?php include 'footer.php'; ?>