<?php
require_once __DIR__ . '/auth_guard.php';
include 'funcoes.php';
session_destroy();
VaiPara('login.php');
?>