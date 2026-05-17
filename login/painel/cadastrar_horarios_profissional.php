<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $usuario_api  = $rows_usuarios['usuario_api'];
}
$stmt_busca_usuario->close();

$profissional_id = intval($_POST['profissional']);

$stmt_p = $conn->prepare("SELECT * FROM profissional WHERE id = ?");
$stmt_p->bind_param("i", $profissional_id);
$stmt_p->execute();
$query_busca_p = $stmt_p->get_result();
$total_busca_p = $query_busca_p->num_rows;

while($rows_p = $query_busca_p->fetch_array()) {
    $profissional_nome  = $rows_p['profissional_nome'];
    $profissional_cargo  = $rows_p['profissional_cargo'];
}
$stmt_p->close();

$segundaInicio = $_POST['segundaInicio'] ?? [];
$tercaInicio = $_POST['tercaInicio'] ?? [];
$quartaInicio = $_POST['quartaInicio'] ?? [];
$quintaInicio = $_POST['quintaInicio'] ?? [];
$sextaInicio = $_POST['sextaInicio'] ?? [];
$sabadoInicio = $_POST['sabadoInicio'] ?? [];
$domingoInicio = $_POST['domingoInicio'] ?? [];

$dias_da_semana = [
    'segunda' => $segundaInicio,
    'terca' => $tercaInicio,
    'quarta' => $quartaInicio,
    'quinta' => $quintaInicio,
    'sexta' => $sextaInicio,
    'sabado' => $sabadoInicio,
    'domingo' => $domingoInicio
];

$stmt_insert = $conn->prepare("INSERT INTO agenda_padrao (usuario_api, login, dia, horario, profissional_nome, profissional_cargo, id_profissional) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($dias_da_semana as $dia_semana => $horarios) {
    foreach ($horarios as $horario) {
        if (!empty($horario)) {
            $stmt_insert->bind_param("ssssssi", $usuario_api, $login, $dia_semana, $horario, $profissional_nome, $profissional_cargo, $profissional_id);
            $query = $stmt_insert->execute();
            if (!$query) {
                echo "Erro ao inserir o horário $horario para $dia_semana: " . $conn->error;
            }
        }
    }
}
$stmt_insert->close();

mysqli_close($conn);

VaiPara('cadastrar_horario.php?confirmacao=atualizado');
