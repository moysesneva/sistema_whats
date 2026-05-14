<?php
session_start();
include 'conn.php';
include 'funcoes.php';
include 'config_dados.php';
if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
error_reporting(0);
ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];








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



<div class="container mt-5">
    <h2 class="text-center">Finalizando a Instalação...</h2>

    <!-- Barra de progresso -->
    <div class="progress" style="height: 30px;">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
            style="width: 0%; background-color: #4caf50;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0%
        </div>
    </div>
</div>

<!-- Script para animar a barra de progresso e redirecionar -->
<script>
    let progressBar = document.getElementById('progressBar');
    let width = 0;
    let interval = setInterval(function () {
        if (width >= 100) {
            clearInterval(interval);
            window.location.href = "config_adm.php?pagina_nome=1"; // Redireciona ao atingir 100%
        } else {
            width++;
            progressBar.style.width = width + '%';
            progressBar.innerHTML = width + '%';
        }
    }, 350); // 350ms * 100 = 35 segundos para chegar a 100%
</script>

<!-- CSS para personalizar a barra de progresso -->
<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 5px;
    }

    .progress-bar {
        font-size: 18px;
        line-height: 30px; /* Alinha o texto no centro vertical */
        color: white;
        text-align: center;
    }
</style>

<?php include 'footer.php'; ?>