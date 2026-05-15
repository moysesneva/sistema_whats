
<?php
// ===============================================
// ARQUIVO: editar_profissional.php
// ===============================================

session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';

$profissional_id = intval($_POST['profissional_id'] ?? 0);
$profissional_nome = $_POST['nomeProfissional'] ?? '';
$telefone_profissional = $_POST['telefoneProfissional'] ?? '';
$especialidade_profissional = $_POST['especialidadeProfissional'] ?? '';

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

if ($total_busca_usuario > 0) {
    while($rows_usuarios = $query_busca_usuario->fetch_array()) {
        $usuario_api = $rows_usuarios['usuario_api'];
    }
    $stmt_busca_usuario->close();
    
    $stmt_update = $conn->prepare("UPDATE profissional SET profissional_nome = ?, telefone = ?, profissional_cargo = ? WHERE id = ? AND login = ?");
    $stmt_update->bind_param("sssis", $profissional_nome, $telefone_profissional, $especialidade_profissional, $profissional_id, $login);
    $query = $stmt_update->execute();
    $stmt_update->close();
    
    if($query) {
        VaiPara('cadastrar_profissional.php?confirmacao=profissional_atualizado');    
    } else {
        VaiPara('cadastrar_profissional.php?erro=profissional_erro_edicao');
    }
} else {
    $stmt_busca_usuario->close();
    VaiPara('cadastrar_profissional.php?erro=usuario_nao_encontrado');
}

?>
