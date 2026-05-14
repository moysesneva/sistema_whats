<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
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


include 'bloqueio.php';


?>
<?php include 'header.php'; ?>

 
    <div class="container">
        <h2>Atualizar Configurações do Site</h2>

 <form action="upload.php" method="POST">
        <div class="form-group">
            <label for="titulo_site">Título da Página:</label>
            <input type="text" class="form-control" id="titulo_site" name="titulo_site" placeholder="Digite o novo título da página" required>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Título</button>
    </form>
    <hr>


<!-- Formulário para a Barra de Ícones -->
   

    <!-- Formulário para a Barra de Cima -->
    <form action="upload.php" method="POST">
        <div class="form-group">
            <label for="barra_cima">Barra Principal:</label>
            <select class="form-control" id="barra_cima" name="barra_cima">
            <option value="" selected>Selecione a opção</option>
             <option value="theme1">Branco</option>
                <option value="theme2">Rosa</option>
                <option value="theme3">Azul Clao</option>
                <option value="theme4">Verde</option>
                <option value="theme5">Azul</option>
                <option value="theme6">Azul escuro</option>
                <option value="theme7">Cinza</option>
      
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Barra Principal</button>
    </form>
    <hr>

    <!-- Formulário para a Cor do Menu -->


    <!-- Formulário para a Seleção do Menu -->
    <form action="upload.php" method="POST">
        <div class="form-group">
            <label for="selecao_menu">Seleção do Menu:</label>
            <select class="form-control" id="selecao_menu" name="selecao_menu">
            <option value="" selected>Selecione a opção</option>
               <option value="theme1">Salmão</option>
                <option value="theme2">Rosa</option>
                <option value="theme3">Verde</option>
                <option value="theme4">Azul</option>
                <option value="theme5">Laranja</option>
                <option value="theme6">Laranja escuro</option>
                <option value="theme7">Vermelho</option>
                <option value="theme8">Azul escuro</option>
     
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Seleção do Menu</button>
    </form>
    <hr>

    <!-- Formulário para o Tema -->
  

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="logo_site">Logo do Site:</label>
                <input type="file" class="form-control" id="logo_site" name="logo_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Logo</button>
        </form>
        <hr>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="emblema_site">Emblema do Site:</label>
                <input type="file" class="form-control" id="emblema_site" name="emblema_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Emblema</button>
        </form>
        <hr>

      
        <hr>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="icon_site">Ícone do Site:</label>
                <input type="file" class="form-control" id="icon_site" name="icon_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Ícone</button>
        </form>


       <br>
         <!-- Formulário para a Seleção do Menu -->
    <form action="upload.php" method="POST">
        <div class="form-group">
            <label for="selecao_menu">Cor da página principal</label>
            <select class="form-control" id="selecao_menu_tema" name="selecao_menu_tema">
            <option value="" selected>Selecione a opção</option>
               <option value="1">Roxo e Azul</option>
                <option value="2">Verde e Aqua</option>
                <option value="3">Vermelho e Laranja</option>
                <option value="4">Azul Escuro e Ciano</option>
                <option value="5">Roxo e Rosa</option>
                <option value="6">Cinza e Amarelo</option>
             
     
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
    <hr>
         </div>  
<?php
include 'parte1.php'

?>



<?php
include 'planos_dados.php'

?>
<?php include 'footer.php'; ?>
