<?php
header("Content-Type: application/json; charset=UTF-8");

// Verifica se é uma requisição POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Método não permitido."));
    exit;
}

// Recebe os dados do formulário
$data = [
    'nome' => $_POST['nome'] ?? '',
    'cpf' => $_POST['cpf'] ?? '',
    'email' => $_POST['email'] ?? '',
    'senha' => $_POST['senha'] ?? '',
    'telefone' => $_POST['telefone'] ?? ''
];

// Inclui o controller
require 'app/controlador.php';

// O controller irá processar os dados e retornar a resposta JSON
?>