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
$stmt_chaves = $conn->prepare("SELECT * FROM chave WHERE login = ?");
$stmt_chaves->bind_param("s", $login);
$stmt_chaves->execute();
$query_busca_chaves = $stmt_chaves->get_result();
$stmt_chaves->close();

?>
   <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h3>Lista de Chaves e Modelos de IA</h3>
                <ul class="list-group">
                    <?php
                    // Verifica se existem chaves
                    if ($query_busca_chaves->num_rows > 0) {
                        while ($row = $query_busca_chaves->fetch_assoc()) {
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
$stmt_cha = $conn->prepare("SELECT * FROM chave WHERE usuario_api = ? ORDER BY RAND() LIMIT 1");
$stmt_cha->bind_param("s", $usuario_api);
$stmt_cha->execute();
$query_busca_chave_aleatoria = $stmt_cha->get_result();
$stmt_cha->close();

if ($query_busca_chave_aleatoria->num_rows > 0) {
    $chave_aleatoria = $query_busca_chave_aleatoria->fetch_assoc();
    
    // Obtendo os dados da chave
    $chave = $chave_aleatoria['chave'];
    $usuario_api = $chave_aleatoria['usuario_api'];
    
}

#echo $chave;
?>

<?php include 'footer.php'; ?>