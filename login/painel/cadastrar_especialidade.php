<?php
// ===============================================
// ARQUIVO: cadastrar_profissional_confirma.php
// ===============================================

session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';

// Receber dados do formulário de profissional
$profissional_nome = $_POST['nomeProfissional'];
$telefone_profissional = $_POST['telefoneProfissional'];

// Processar especialidades selecionadas
$especialidades_selecionadas = '';
if (isset($_POST['especialidades']) && is_array($_POST['especialidades'])) {
    $especialidades_selecionadas = implode(', ', $_POST['especialidades']);
} else {
    // Se não veio como array, pode ser um campo único
    $especialidades_selecionadas = isset($_POST['especialidadeProfissional']) ? $_POST['especialidadeProfissional'] : '';
}

// Buscar dados do usuário
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if ($total_busca_usuario > 0) {
    while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
        $usuario_api = $rows_usuarios['usuario_api'];
    }
    
    // Inserir profissional na tabela
    $sql = "INSERT INTO profissional (usuario_api, login, profissional_nome, profissional_cargo, telefone) 
            VALUES ('$usuario_api', '$login', '$profissional_nome', '$especialidades_selecionadas', '$telefone_profissional')";
    
    $query = mysqli_query($conn, $sql);
    
    if($query) {
        VaiPara('cadastrar_profissional.php?confirmacao=profissional_cadastrado');    
    } else {
        VaiPara('cadastrar_profissional.php?erro=profissional_erro');
    }
} else {
    VaiPara('cadastrar_profissional.php?erro=usuario_nao_encontrado');
}

// Debug (remover em produção)
// print_r($_REQUEST);
?>