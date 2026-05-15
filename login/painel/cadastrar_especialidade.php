<?php
// ===============================================
// ARQUIVO: cadastrar_profissional_confirma.php
// ===============================================

session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';

$profissional_nome = $_POST['nomeProfissional'] ?? '';
$telefone_profissional = $_POST['telefoneProfissional'] ?? '';

$especialidades_selecionadas = '';
if (isset($_POST['especialidades']) && is_array($_POST['especialidades'])) {
    $especialidades_selecionadas = implode(', ', $_POST['especialidades']);
} else {
    $especialidades_selecionadas = isset($_POST['especialidadeProfissional']) ? $_POST['especialidadeProfissional'] : '';
}

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

    $stmt_insert = $conn->prepare("INSERT INTO profissional (usuario_api, login, profissional_nome, profissional_cargo, telefone) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssss", $usuario_api, $login, $profissional_nome, $especialidades_selecionadas, $telefone_profissional);
    $query = $stmt_insert->execute();
    $stmt_insert->close();
    
    if($query) {
        VaiPara('cadastrar_profissional.php?confirmacao=profissional_cadastrado');    
    } else {
        VaiPara('cadastrar_profissional.php?erro=profissional_erro');
    }
} else {
    $stmt_busca_usuario->close();
    VaiPara('cadastrar_profissional.php?erro=usuario_nao_encontrado');
}

?>
