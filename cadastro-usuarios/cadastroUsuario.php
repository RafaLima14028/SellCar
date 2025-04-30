<?php
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Método não permitido."));
    exit;
}

$data = [
    'nome' => $_POST['nome'] ?? '',
    'cpf' => $_POST['cpf'] ?? '',
    'email' => $_POST['email'] ?? '',
    'senha' => $_POST['senha'] ?? '',
    'telefone' => $_POST['telefone'] ?? ''
];

require 'app/controlador.php';


?>