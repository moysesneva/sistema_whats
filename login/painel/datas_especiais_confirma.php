<?php
session_start();

// Incluir arquivos necessários
include 'conn.php';
include 'funcoes.php';
print_r($_REQUEST);
#exit();
#include 'config_dados.php';
#include 'estilo.php';
#include 'css_de_icones.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['login'])) {
    VaiPara('login.php');
}

$login = $_SESSION['login'];





















// Verificar se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se a conexão com o banco de dados foi bem-sucedida
    if (!$conn) {
        die('Erro ao conectar ao banco de dados.');
    }


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $usuario_api  = $rows_usuarios['usuario_api'];

}

    // Exemplo de acesso aos dados específicos
    if (isset($_POST['profissional'])) {
        $id_profissional = $_POST['profissional'];
    }
    if (isset($_POST['data'])) {
        $data_excluida = $_POST['data'];
    }
    if (isset($_POST['texto'])) {
        $motivo =  $_POST['texto'];
    }

$sql_busca_prof = "SELECT * FROM  profissional WHERE id ='$id_profissional'";
$query_prof = mysqli_query($conn, $sql_busca_prof);

while($rows_prof = mysqli_fetch_array($query_prof)) {
   $profissional_nome =  $rows_prof['profissional_nome'];
}
#echo $profissional_nome;
$profissional = $profissional_nome;



$sql_busca_datas = "SELECT * FROM datas_excluidas WHERE data_excluida = '$data_excluida' AND id_profissional = '$id_profissional' ";
$query_datas = mysqli_query($conn, $sql_busca_datas);
$total = mysqli_num_rows($query_datas);
#echo $total;
#VaiPara('datas_especiais.php'); // Adciona quando da erro de DUplicidade

if($total == '0'){
#echo 'Tolta iagual a zero';
#echo '<br>' . $data_excluida . '<br>' . $id_profissional . '<br>' . $motivo . '<br>' . $profissional;




// Inserir na tabela datas_excluidas
$sql = "INSERT INTO datas_excluidas (data_excluida, id_profissional, motivo, profissional,usuario_api) 
        VALUES ('$data_excluida', '$id_profissional', '$motivo', '$profissional','$usuario_api')";
$query = mysqli_query($conn, $sql);

if ($query) {
    #echo "Dados inseridos com sucesso!";
    VaiPara('datas_especiais.php?sucesso=sucesso'); // Adciona quando da erro de DUplicidade

} else {
    #echo "Erro ao inserir os dados: " . mysqli_error($conn);
    VaiPara('datas_especiais.php?erro=erro'); // Adciona quando da erro de DUplicidade

}








}else{



VaiPara('datas_especiais.php'); // Adciona quando da erro de DUplicidade
#echo "Nao foi feita a cosulta";
#echo $total;


}

}

?>
