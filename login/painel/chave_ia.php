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
include 'config_dados.php';
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
<?php include 'header.php'; ?>




    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
   
   


   
   <!-- Formulário para inserir chave e selecionar modelo de IA -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h3>Adicionar Serviço com Chave</h3>
            <form action="adicionar_chave_ia.php" method="post">
                <div class="form-group">
                    <label for="chave">Chave</label>
                    <input type="text" class="form-control" id="chave" name="chave" placeholder="Insira a chave">
                </div>
                
                <button type="submit" class="btn btn-primary">Adicionar</button>
            </form>
        </div>
    </div>
</div>
<?php
// Consulta para buscar todas as chaves
$sql_busca_chaves = "SELECT * FROM chave WHERE login = '$login'";
$query_busca_chaves = mysqli_query($conn, $sql_busca_chaves);

?>
   <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h3>Lista de Chaves e Modelos de IA</h3>
                <ul class="list-group">
                    <?php
                    // Verifica se existem chaves
                    if (mysqli_num_rows($query_busca_chaves) > 0) {
                        // Itera por cada linha retornada
                        while ($row = mysqli_fetch_assoc($query_busca_chaves)) {
                            $id_chave = $row['id']; // ID da chave (deve existir na tabela)
                            $chave = $row['chave'];
                            $chave_mascarada = substr($chave, 0, 8) . '****'; // Máscara para ocultar parte da chave

                            // Exibindo cada chave como um item da lista com botão "Deletar"
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                            echo "Chave: $chave_mascarada";
                            echo "<a href='deletar_chave.php?id=$id_chave' class='btn btn-danger btn-sm'>Deletar</a>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>Nenhuma chave encontrada.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>


   <?php
   
// Consulta SQL para buscar uma chave aleatória que tenha `usuario_api` preenchido
$sql_busca_chave_aleatoria = "SELECT * FROM chave WHERE usuario_api	 = '$usuario_api' ORDER BY RAND() LIMIT 1";


$query_busca_chave_aleatoria = mysqli_query($conn, $sql_busca_chave_aleatoria);

// Verificando se a consulta retornou algum resultado
if (mysqli_num_rows($query_busca_chave_aleatoria) > 0) {
    $chave_aleatoria = mysqli_fetch_assoc($query_busca_chave_aleatoria);
    
    // Obtendo os dados da chave
    $chave = $chave_aleatoria['chave'];
    $usuario_api = $chave_aleatoria['usuario_api'];
    
   
}

#echo $chave;
?>

<?php include 'footer.php'; ?>