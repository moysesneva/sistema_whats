<?php
include '../conn.php';
include '../funcoes.php';
include 'api_funcao.php';
include '../config_dados.php';

$usuario_api = $_POST['usuario'];   
$telefone = $_POST['telefone'];
 
?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $usuario_api = $_POST['usuario'];   
 #$telefone = $_POST['telefone'];
 #$telefone = '553184767330';
############# BUSCA DE LOGIN
$stmt_login1 = $conn->prepare("SELECT * FROM login WHERE usuario_api = ?");
$stmt_login1->bind_param("s", $usuario_api);
$stmt_login1->execute();
$query = $stmt_login1->get_result();
$total = $query->num_rows;

while($lista_login = $query->fetch_array()){
$tempo = $lista_login['tempo_final'];
$despedida = $lista_login['IA_despedida'];
$agenda_verfica = $lista_login['agenda_verfica'];
}
$stmt_login1->close();
#######################################
######################################
#agora envi a mensagemde despedida
 
// Ajustar o fuso horário para o Brasil
mysqli_query($conn, "SET time_zone = '-03:00'");

// Definir a consulta
$tempo_int = (int)$tempo;
$stmt_cli1 = $conn->prepare("SELECT * FROM clientes WHERE usuario_api = ? AND (situacao = '1' OR situacao IS NULL) AND time_atendimento <= DATE_SUB(NOW(), INTERVAL ? MINUTE) LIMIT 1");
$stmt_cli1->bind_param("si", $usuario_api, $tempo_int);
$stmt_cli1->execute();
$query = $stmt_cli1->get_result();

// Executar a consulta
$total = $query->num_rows;

if (!$query) {
    die("Erro na consulta SQL: " . $conn->error);
}

while($lista_login = $query->fetch_array()){
$id = $lista_login['id'];
$telefone = $lista_login['telefone'];
}
$stmt_cli1->close();
if($total > 0){

$stmt_upd = $conn->prepare("UPDATE clientes SET situacao = '2' WHERE id = ?");
$stmt_upd->bind_param("i", $id);
$upd_result = $stmt_upd->execute();
$stmt_upd->close();

if($upd_result){

$stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '1', ?)");
$stmt_env->bind_param("sss", $telefone, $despedida, $usuario_api);
$stmt_env->execute();
$stmt_env->close();

$stmt_del = $conn->prepare("DELETE FROM ia_historico WHERE usuario_api = ? AND telefone_usuario = ?");
$stmt_del->bind_param("ss", $usuario_api, $telefone);
$stmt_del->execute();
$stmt_del->close();

}
}
}

$usuario_api = $_POST['usuario'] ?? '';

$stmt_login2 = $conn->prepare("SELECT * FROM login WHERE usuario_api = ?");
$stmt_login2->bind_param("s", $usuario_api);
$stmt_login2->execute();
$query = $stmt_login2->get_result();
$total = $query->num_rows;

while($lista_login = $query->fetch_array()){
$tempo_verificar = $lista_login['tempo_verifica'];
}
$stmt_login2->close();
 
#$usuario_api = 'agenda_3184767335'; // Usuário específico para filtrar
$data_atual = date('Y-m-d'); // Data atual
#$data_atual = '2024-11-18';
$hora_atual = date('H:i'); // Hora atual (apenas horas e minutos)
$usuario_api = $_POST['usuario'] ?? '';
$telefone = $_POST['telefone'] ?? '';
// Consulta SQL para encontrar o horário mais próximo

// Data e hora atuais
$data_hora_atual = date('Y-m-d H:i:s');
$tempo_verificar_int = (int)$tempo_verificar;

$stmt_ag = $conn->prepare("SELECT *, TIMESTAMP(data, horario) AS agendamento_completo, TIMESTAMP(data, horario) - INTERVAL ? MINUTE AS lembrete_ajustado FROM agendamento WHERE usuario_api = ? AND TIMESTAMP(data, horario) - INTERVAL ? MINUTE <= ? AND lembrete = '0' ORDER BY agendamento_completo ASC LIMIT 1");
$stmt_ag->bind_param("isis", $tempo_verificar_int, $usuario_api, $tempo_verificar_int, $data_hora_atual);
$stmt_ag->execute();
$query = $stmt_ag->get_result();

// Verifica se há resultados
if ($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
       $id = $row['id'];
       $telefone= $row['cliente_telefone'];
        $profissional_nome = $row['profissional_nome'];
        $data =  $row['data'] ;
         $horario = $row['horario'] ;
    }
$stmt_ag->close();

    $stmt_bu3 = $conn->prepare("SELECT * FROM login WHERE usuario_api = ?");
    $stmt_bu3->bind_param("s", $usuario_api);
    $stmt_bu3->execute();
    $query_busca_usuario = $stmt_bu3->get_result();
    if ($row_usuario = $query_busca_usuario->fetch_array()) {
        $login = $row_usuario['login'];
       $agenda_verfica = $row_usuario['agenda_verfica'];
    }
    $stmt_bu3->close();

    $stmt_cli2 = $conn->prepare("SELECT * FROM clientes WHERE telefone = ? AND usuario_api = ?");
    $stmt_cli2->bind_param("ss", $telefone, $usuario_api);
    $stmt_cli2->execute();
    $query_busca_clientes = $stmt_cli2->get_result();
    if ($row = $query_busca_clientes->fetch_array()) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    }
    $stmt_cli2->close();

       function novo_texto($string, $nome, $agendamento, $profissional) {
            $substituicoes = [
                '{nome}' => $nome,
                '{agendamento}' => $agendamento,
                '{profissional}' => $profissional
            ];
            return str_replace(array_keys($substituicoes), array_values($substituicoes), $string);
        }

        function formatar_data_brasileira($data) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                return date("d/m/Y", strtotime($data));
            } else {
                return $data;
            }
        }
        
        $data_formatada = formatar_data_brasileira($data);
        $agendamento = $horario . " " . $data_formatada;
        $profissional = $profissional_nome;
            
    $agenda_verfica =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
    
function formatarTexto($texto) {
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    $texto .= "\nSim\nNão";
    return $texto;
}

$agenda_verfica2 =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
$agenda_verfica3 = formatarTexto($agenda_verfica2);

$stmt_env2 = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('Enquete', ?, ?, '1', ?)");
$stmt_env2->bind_param("sss", $telefone, $agenda_verfica3, $usuario_api);
$stmt_env2->execute();
$stmt_env2->close();

$stmt_upd_ag = $conn->prepare("UPDATE agendamento SET lembrete = '2' WHERE id = ?");
$stmt_upd_ag->bind_param("i", $id);
$stmt_upd_ag->execute();
$stmt_upd_ag->close();

} else {
    if (isset($stmt_ag)) $stmt_ag->close();
   # echo "Nenhum agendamento encontrado para as condições fornecidas.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $usuario_api = $_POST['usuario'] ?? '';

    $stmt_envc = $conn->prepare("SELECT * FROM envio WHERE status = '1' AND usuario_api = ? ORDER BY id DESC");
    $stmt_envc->bind_param("s", $usuario_api);
    $stmt_envc->execute();
    $query_config = $stmt_envc->get_result();
    $total_config = $query_config->num_rows;
    $stmt_envc->close();

    while ($rows_usuarios = $query_config->fetch_array()) {
        $id_msg = $rows_usuarios['id'];
        $telefone = $rows_usuarios['telefone'];
        $msg = $rows_usuarios['msg'];
        $status = $rows_usuarios['status'];
        $usuario = $rows_usuarios['usuario_api'];
        $comando = $rows_usuarios['comando'];
}

if ($total_config > 0){
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);    

$response = [
            'id' => '1',
            'id_msg' => $id_msg,
            'comando' => $comando,
            'msg' => $msg,
            'telefone' => $telefone,
            'url' => null,
            'tipo' => 'MsgTexto'
        ];    
        echo json_encode($response);

        $stmt_upd_env = $conn->prepare("UPDATE envio SET status = '2' WHERE id = ?");
        $stmt_upd_env->bind_param("i", $id_msg);
        $stmt_upd_env->execute();
        $stmt_upd_env->close();
    
}}
 
 ?>
 