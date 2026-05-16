<?php
require_once __DIR__ . '/api_auth.php';
require_once('../conn.php');
include('../funcao.php');



if($_GET['comando'] == 'criar_conta'){
$id = (int)($_GET['id'] ?? 0);
$usuario_api =  $_GET['cpf'] ?? '';
$caminho =  $_GET['caminho'] ?? '';

$stmt1 = $conn->prepare("UPDATE gerenciador SET comando = 'conta_criada' WHERE id = ?");
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->close();

$stmt2 = $conn->prepare("UPDATE login SET caminho_vps = ? WHERE usuario_api = ?");
$stmt2->bind_param("ss", $caminho, $usuario_api);
$stmt2->execute();
$stmt2->close();

}#criar_conta



if($_GET['comando'] == 'delete'){
$id = (int)($_GET['id'] ?? 0);
$usuario_api =  $_GET['cpf'] ?? '';
$email =  $_GET['email'] ?? '';

$stmt3 = $conn->prepare("UPDATE gerenciador SET comando = 'conta_criada' WHERE id = ?");
$stmt3->bind_param("i", $id);
$stmt3->execute();
$stmt3->close();

$stmt4 = $conn->prepare("DELETE FROM login WHERE email = ?");
$stmt4->bind_param("s", $email);
$stmt4->execute();
$stmt4->close();

}#criar_conta




if($_GET['comando'] == 'restart'){
$id = (int)($_GET['id'] ?? 0);
$usuario_api =  $_GET['usuario'] ?? '';

$stmt5 = $conn->prepare("DELETE FROM gerenciador WHERE id = ?");
$stmt5->bind_param("i", $id);
$stmt5->execute();
$stmt5->close();

$stmt6 = $conn->prepare("UPDATE login SET situacao = 'ativado' WHERE usuario_api = ?");
$stmt6->bind_param("s", $usuario_api);
$stmt6->execute();
$stmt6->close();

}#stop


if($_GET['comando'] == 'stop'){
$id = (int)($_GET['id'] ?? 0);
$usuario_api =  $_GET['usuario'] ?? '';

$stmt7 = $conn->prepare("DELETE FROM gerenciador WHERE id = ?");
$stmt7->bind_param("i", $id);
$stmt7->execute();
$stmt7->close();

$stmt8 = $conn->prepare("UPDATE login SET situacao = 'desativado' WHERE usuario_api = ?");
$stmt8->bind_param("s", $usuario_api);
$stmt8->execute();
$stmt8->close();

}#stop

if($_GET['comando'] == 'start'){
$id = (int)($_GET['id'] ?? 0);
$email =  $_GET['email'] ?? '';
$cpf =  $_GET['cpf'] ?? '';

$stmt9 = $conn->prepare("UPDATE dados_compra SET comando = 'conta_criada', status = 'Ativo' WHERE email = ?");
$stmt9->bind_param("s", $email);
$stmt9->execute();
$stmt9->close();

$stmt10 = $conn->prepare("UPDATE login SET status = 'Ativo' WHERE email = ?");
$stmt10->bind_param("s", $email);
$stmt10->execute();
$stmt10->close();

}#stop



?>
