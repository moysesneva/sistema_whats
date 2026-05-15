<?php
session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';
#print_r($_REQUEST);
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
   
    $usuario_api  = $rows_usuarios['usuario_api'];

}


$profissional_id = $_POST['profissional']; // Nome do profissional

$sql_busca_p = "SELECT * FROM profissional WHERE id = '$profissional_id'";
$query_busca_p = mysqli_query($conn, $sql_busca_p);
$total_busca_p = mysqli_num_rows($query_busca_p);

while($rows_p = mysqli_fetch_array($query_busca_p)) {
   
    $profissional_nome  = $rows_p['profissional_nome'];
    $profissional_cargo  = $rows_p['profissional_cargo'];

}



$segundaInicio = $_POST['segundaInicio'];
$tercaInicio = $_POST['tercaInicio'];
$quartaInicio = $_POST['quartaInicio'];
$quintaInicio = $_POST['quintaInicio'];
$sextaInicio = $_POST['sextaInicio'];
$sabadoInicio = $_POST['sabadoInicio'];
$domingoInicio = $_POST['domingoInicio'];

// Dicionário com os dias da semana e os respectivos horários
$dias_da_semana = [
    'segunda' => $segundaInicio,
    'terca' => $tercaInicio,
    'quarta' => $quartaInicio,
    'quinta' => $quintaInicio,
    'sexta' => $sextaInicio,
    'sabado' => $sabadoInicio,
    'domingo' => $domingoInicio
];

// Inserir os horários no banco de dados
foreach ($dias_da_semana as $dia_semana => $horarios) {
    foreach ($horarios as $horario) {
        // Verifica se o horário não está vazio
        if (!empty($horario)) {
            // Cria a query SQL para inserir no banco
            $sql = "INSERT INTO agenda_padrao (usuario_api, login, dia, horario, profissional_nome, profissional_cargo,id_profissional) 
                    VALUES ('$usuario_api', '$login', '$dia_semana', '$horario', '$profissional_nome', '$profissional_cargo','$profissional_id')";

            // Executa a query
            $query = mysqli_query($conn, $sql);

            // Verifica se a query foi executada com sucesso
            if (!$query) {
                echo "Erro ao inserir o horário $horario para $dia_semana: " . mysqli_error($conn);
            }
        }
    }
}

// Fechar a conexão
mysqli_close($conn);

VaiPara('cadastrar_horario.php?confirmacao=atualizado')


?>