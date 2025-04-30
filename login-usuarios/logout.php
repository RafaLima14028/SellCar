<?php
require_once __DIR__ . '/login-usuarios/authController.php';
require_once __DIR__ . '/app/conexaoMysql.php';

header("Content-Type: application/json");

$database = new conexaoMysql();
$db = $database->getConnection();

$authController = new AuthController($db);
$response = $authController->logout();

echo json_encode($response);
?>