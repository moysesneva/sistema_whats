
<?php
// ===============================================
// ARQUIVO: editar_profissional.php
// ===============================================

session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';

// Receber dados do formulário de edição
$profissional_id = $_POST['profissional_id'];
$profissional_nome = $_POST['nomeProfissional'];
$telefone_profissional = $_POST['telefoneProfissional'];
$especialidade_profissional = $_POST['especialidadeProfissional'];

// Buscar dados do usuário
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if ($total_busca_usuario > 0) {
    while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
        $usuario_api = $rows_usuarios['usuario_api'];
    }
    
    // Atualizar profissional na tabela
    $sql = "UPDATE profissional SET 
            profissional_nome = '$profissional_nome',
            telefone = '$telefone_profissional',
            profissional_cargo = '$especialidade_profissional'
            WHERE id = '$profissional_id' AND login = '$login'";
    
    $query = mysqli_query($conn, $sql);
    
    if($query) {
        VaiPara('cadastrar_profissional.php?confirmacao=profissional_atualizado');    
    } else {
        VaiPara('cadastrar_profissional.php?erro=profissional_erro_edicao');
    }
} else {
    VaiPara('cadastrar_profissional.php?erro=usuario_nao_encontrado');
}

// Debug (remover em produção)
// print_r($_REQUEST);
?>