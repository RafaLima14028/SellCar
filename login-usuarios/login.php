<?php
require_once __DIR__ . '/../app/conexaoMysql.php';
require_once __DIR__ . '/../app/controlador.php';

header("Content-Type: application/json");

$database = new conexaoMysql();
$db = $database->getConnection();

$controller = new controlador($db);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(!empty($data['email']) && !empty($data['senha'])) {
        $response = $controller->handleLogin($data['email'], $data['senha']);
        http_response_code($response['status'] === "success" ? 200 : 401);
        echo json_encode($response);
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Dados incompletos"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método não permitido"]);
}
?>