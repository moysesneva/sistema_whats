<?php

###vamos buscar se o cl
$dataHoraCompleta =  gerarDataHoraCompleta();

function id_agenda() {
    return strtoupper(dechex(mt_rand(0, 0xFFFF)));
}

$id_agenda = id_agenda();
$sql = "INSERT INTO clientes (telefone,usuario_api,time_atendimento,id_agendamento) VALUES ('$telefone','$usuario_api','$dataHoraCompleta','$id_agenda')";
$query = mysqli_query($conn,$sql);

$user_id = $usuario_api;

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$IA_boas_vindas','2','$usuario_api')";
$query = mysqli_query($conn,$sql);
if($query){
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção


#$msg = $IA_boas_vindas;

#$response =  enviarMensagem($servidor, $porta, $user_id, $token, $telefone, $msg, $id_msg);
$response = enviarMensagem($servidor,$porta ,$usuario_api,$token,$telefone,$IA_boas_vindas,$id_msg);


file_put_contents('log_mensagens.txt', date('Y-m-d H:i:s') . " | Tel: {$response} \n", FILE_APPEND);
}

?>