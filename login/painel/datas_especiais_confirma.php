<?php
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die('Erro ao conectar ao banco de dados.');
    }

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $usuario_api  = $rows_usuarios['usuario_api'];

}
$stmt_busca_usuario->close();

    if (isset($_POST['profissional'])) {
        $id_profissional = (int)$_POST['profissional'];
    }
    if (isset($_POST['data'])) {
        $data_excluida = $_POST['data'];
    }
    if (isset($_POST['texto'])) {
        $motivo = $_POST['texto'];
    }

$stmt_prof = $conn->prepare("SELECT * FROM profissional WHERE id = ?");
$stmt_prof->bind_param("i", $id_profissional);
$stmt_prof->execute();
$query_prof = $stmt_prof->get_result();

while($rows_prof = $query_prof->fetch_array()) {
   $profissional_nome = $rows_prof['profissional_nome'];
}
$stmt_prof->close();
$profissional = $profissional_nome;

$stmt_datas = $conn->prepare("SELECT * FROM datas_excluidas WHERE data_excluida = ? AND id_profissional = ?");
$stmt_datas->bind_param("si", $data_excluida, $id_profissional);
$stmt_datas->execute();
$query_datas = $stmt_datas->get_result();
$total = $query_datas->num_rows;
$stmt_datas->close();

if($total == '0'){

$stmt_insert = $conn->prepare("INSERT INTO datas_excluidas (data_excluida, id_profissional, motivo, profissional, usuario_api) VALUES (?, ?, ?, ?, ?)");
$stmt_insert->bind_param("sisss", $data_excluida, $id_profissional, $motivo, $profissional, $usuario_api);
$query = $stmt_insert->execute();
$stmt_insert->close();

if ($query) {
    VaiPara('datas_especiais.php?sucesso=sucesso');
} else {
    VaiPara('datas_especiais.php?erro=erro');
}

}else{

VaiPara('datas_especiais.php');

}

}

?>
