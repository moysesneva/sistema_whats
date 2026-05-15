<?php
session_start();
include 'conn.php';
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
$login = $_SESSION['login'];

include 'config_dados.php';

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
$stmt_busca_usuario->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $chave = $_POST['chave'] ?? '';

    if (!empty($chave)) {
        $stmt_insert = $conn->prepare("INSERT INTO chave (chave, login, usuario_api) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $chave, $login, $usuario_api);
        $resultado_insercao = $stmt_insert->execute();
        $stmt_insert->close();

        if ($resultado_insercao) {
            VaiPara('chave_ia.php?pagina_nome=19');
        } else {
            $mensagem = '<div class="alert alert-danger" role="alert">Erro ao adicionar a chave. Tente novamente.</div>';
            VaiPara('chave_ia.php?pagina_nome=19');
        }
    } else {
        $mensagem = '<div class="alert alert-warning" role="alert">Por favor, insira uma chave válida.</div>';
        VaiPara('chave_ia.php?pagina_nome=19');
    }
}
?>
