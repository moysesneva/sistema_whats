<?php
function verificaSimNao($texto) {
    $texto = strtolower($texto);
    if (strpos($texto, 's') !== false) {
        return "sim";
    } elseif (strpos($texto, 'n') !== false) {
        return "não";
    } else {
        return "Valor não encontrado";
    }
}


$token =  $token_bd;
$texto  = $opcoes_enquete;
$texto  =   verificaSimNao($texto) ; 
 
 if($texto == 'sim'){

$stmt = $conn->prepare("UPDATE agendamento SET confirmacao = '1', lembrete = '3' WHERE usuario_api = ? AND cliente_telefone = ? AND lembrete = '2'");
$stmt->bind_param("ss", $usuario_api, $telefone);
$query = $stmt->execute();
$stmt->close();

if (!$query) {
    die("Erro na atualização: " . $conn->error);
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_env->bind_param("sss", $telefone, $resultado, $usuario_api);
$stmt_env->execute();
$stmt_env->close();
$id_msg = mysqli_insert_id($conn);

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);



} else {
 $resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn);

 $response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
 salvando($response);

}

}
 
 
 
 
 
 
if($texto == 'não'){

$stmt = $conn->prepare("UPDATE agendamento SET confirmacao = '2', lembrete = '3' WHERE usuario_api = ? AND cliente_telefone = ? AND lembrete = '2'");
$stmt->bind_param("ss", $usuario_api, $telefone);
$query = $stmt->execute();
$stmt->close();

if (!$query) {
    die("Erro na atualização: " . $conn->error);
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$stmt_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
$stmt_env->bind_param("sss", $telefone, $resultado, $usuario_api);
$stmt_env->execute();
$stmt_env->close();
$id_msg = mysqli_insert_id($conn);

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);

} else {

$resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn);

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);

}

}
 ?>
