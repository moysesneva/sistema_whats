<?php
session_start();
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];

$stmt = $conn->prepare("UPDATE login SET situacao = 'desativado', qr_quantidade = '1' WHERE login = ?");
$stmt->bind_param("s", $login);
$query = $stmt->execute();
$stmt->close();
if($query){
$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
$servidor = barra($servidor_recebido);
$usuario_api = $_POST['usuario_api'] ?? '';

$stmt_ger = $conn->prepare("INSERT INTO gerenciador (usuario_api, comando) VALUES (?, 'stop_conta')");
$stmt_ger->bind_param("s", $usuario_api);
$stmt_ger->execute();
$stmt_ger->close();

   VaiPara('desconecta.php?pagina_nome=7&aguarde=qrcode.php&tempo=45');
}
