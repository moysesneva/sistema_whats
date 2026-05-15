<?php
function verificaSimNao($texto) {
    // Converte o texto para minúsculas para garantir a comparação correta
    $texto = strtolower($texto);

    // Verifica se a string contém 's' ou 'n'
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



$sql = "UPDATE agendamento SET confirmacao = '1',lembrete = '3' WHERE usuario_api='$usuario_api' AND cliente_telefone = '$telefone' AND lembrete = '2'";

$query = mysqli_query($conn,$sql);

if (!$query) {
    die("Erro na atualização: " . mysqli_error($conn));
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$resultado','2','$usuario_api')";
$query = mysqli_query($conn,$sql);  
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);



#$reponse = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $msg, $id_msg) ;
#exit();



} else {
 $resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

 $response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
 salvando($response);

# exit();
}

}
 
 
 
 
 
 
if($texto == 'não'){



$sql = "UPDATE agendamento SET confirmacao = '2',lembrete = '3' WHERE usuario_api='$usuario_api' AND cliente_telefone = '$telefone' AND lembrete = '2'";

$query = mysqli_query($conn,$sql);

if (!$query) {
    die("Erro na atualização: " . mysqli_error($conn));
}


if (mysqli_affected_rows($conn) > 0) {
    $resultado = '*Agendamento:* processado';

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$resultado','2','$usuario_api')";
$query = mysqli_query($conn,$sql);  
$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);

#exit();

} else {

$resultado = 'Nenhum agendamento pendente para processar no momento.';

$id_msg = mysqli_insert_id($conn); // Pega o ID gerado na última inserção

$response = enviarMensagem($servidor,$porta , $user_id, $token, $telefone, $resultado, $id_msg);
salvando($response);

#exit();
}

}
 ?>