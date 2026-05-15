<?php
session_start();
include 'conn.php';
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


#print_r($_REQUEST);
include 'config_dados.php';

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

// Verificando se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtendo o valor da chave enviada pelo formulário
    $chave = $_POST['chave'];

    // Verificando se o campo chave não está vazio
    if (!empty($chave)) {
        // Inserindo a chave na tabela servicos
        $sql_inserir_chave = "INSERT INTO chave (chave,login,usuario_api) VALUES ('$chave','$login','$usuario_api')";
        $resultado_insercao = mysqli_query($conn, $sql_inserir_chave);
        
        


        // Verificando se a inserção foi bem-sucedida
        if ($resultado_insercao) {
            #$mensagem = '<div class="alert alert-success" role="alert">Chave adicionada com sucesso!</div>';
            VaiPara('chave_ia.php?pagina_nome=19');
        } else {
            $mensagem = '<div class="alert alert-danger" role="alert">Erro ao adicionar a chave. Tente novamente.</div>';
            slepp(3);
            VaiPara('chave_ia.php?pagina_nome=19');
            
        }
    } else {
        $mensagem = '<div class="alert alert-warning" role="alert">Por favor, insira uma chave válida.</div>';
        slepp(3);
            VaiPara('chave_ia.php?pagina_nome=19');
    }
}
?>