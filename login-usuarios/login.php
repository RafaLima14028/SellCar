<?php
require_once 'config/config.php';
require_once 'controller/AuthController.php';

$auth = new AuthController();

if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $auth->login();
    exit();
}

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['logado']) && $_SESSION['logado']) {
    header('Location: view/restrito/dashboard.php');
    exit();
}

// Exibe a página de login
include 'view/login.php';
?>