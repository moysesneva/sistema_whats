<?php
session_start();
$login = $_SESSION['login'];
include 'funcoes.php';
include 'conn.php';
include 'api/api_funcao.php';
include 'config_dados.php';

$servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
$servidor = barra($servidor_recebido);
$telefone = '553184767330';
$usuario = 'agenda_3184767330';
$id_msg = 2;
$msg = 'Bom dia como voce esta';
EnviarMsg($telefone,$msg,$id_msg,$usuario, $token, $servidor);
?>