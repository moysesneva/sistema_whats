<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';
session_destroy();
VaiPara('login.php');
?>