<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';
$login = $_SESSION['login'];

$stmt = $conn->prepare("UPDATE login SET situacao = 'aguarde' WHERE login = ?");
$stmt->bind_param("s", $login);
$query = $stmt->execute();
$stmt->close();
if($query){
$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
$servidor = barra($servidor_recebido);
$usuario = $_POST['usuario_api'] ?? '';

$stmt_ger = $conn->prepare("INSERT INTO gerenciador (usuario_api, comando) VALUES (?, 'reset_conta')");
$stmt_ger->bind_param("s", $usuario);
$stmt_ger->execute();
$stmt_ger->close();

VaiPara('qrcode.php?pagina_nome=3&aguarde=qrcode.php&tempo=45');

}
