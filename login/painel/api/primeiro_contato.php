<?php

###vamos buscar se o cl
$dataHoraCompleta =  gerarDataHoraCompleta();

function id_agenda() {
    return strtoupper(dechex(mt_rand(0, 0xFFFF)));
}

$id_agenda = id_agenda();
$stmt = $conn->prepare("INSERT INTO clientes (telefone, usuario_api, time_atendimento, id_agendamento) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $telefone, $usuario_api, $dataHoraCompleta, $id_agenda);
$query = $stmt->execute();
$stmt->close();

$user_id = $usuario_api;

$stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_env->bind_param("sss", $telefone, $IA_boas_vindas, $usuario_api);
$query = $stmt_env->execute();
$stmt_env->close();
if($query){
$id_msg = mysqli_insert_id($conn);

$response = enviarMensagem($servidor,$porta ,$usuario_api,$token,$telefone,$IA_boas_vindas,$id_msg);

file_put_contents('log_mensagens.txt', date('Y-m-d H:i:s') . " | Tel: {$response} \n", FILE_APPEND);
}

?>
