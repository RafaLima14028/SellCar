<?

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";
require "./modelos/Anunciante.php"; // Adicionei esta linha

$acao = $_GET['acao'] ?? '';

$pdo = mysqlConnect();

switch ($acao) {
    case "criacaoAnuncios":
        $marca = $_POST['marca'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
        $ano = $_POST['ano'] ?? 0;
        $cor = $_POST['cor'] ?? '';
        $quilometragem = $_POST['quilemetragem'] ?? 0;
        $descricao = $_POST['descricao'] ?? '';
        $valor = $_POST['valor'] ?? 0;
        $estado = $_POST['estado'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $fotos = $_FILES['imgs-carro'] ?? '';

        try {
            $pdo->beginTransaction();

            $idAnunciante = 1; // Precisa vir da session

            $idAnuncio = Anuncio::Criar($pdo, $marca, $modelo, $ano, $cor, $quilometragem, $descricao, $valor, $estado, $cidade, $idAnunciante);

            $pasta_destino = "uploads/anuncios/" . $idAnuncio;

            if (!is_dir($pasta_destino))
                mkdir($pasta_destino, 0777, true);

            foreach ($fotos['tmp_name'] as $index => $tmp_name) {
                $nome_original = basename($fotos['name'][$index]);
                $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
                $novo_nome = uniqid('img_', true) . "." . $extensao;

                if (move_uploaded_file($tmp_name, $pasta_destino . '/' . $novo_nome)) {
                    Foto::Criar($pdo, $idAnuncio, $novo_nome);
                } else {
                    throw new Exception("Falha ao salvar o arquivo: " . $nome_original);
                }
            }

            $pdo->commit();

            header("Location: ../index.html");
        } catch (Exception $e) {
            $pdo->rollBack();

            throw new Exception($e->getMessage());
        }
        break;

    case "cadastroUsuario":
        header("Content-Type: application/json; charset=UTF-8");

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(array(
                "status" => "error",
                "message" => "Método não permitido"
            ));
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Verifica se todos os campos estão presentes
            if(empty($_POST['nome']) || empty($_POST['cpf']) || empty($_POST['email']) || 
               empty($_POST['senha']) || empty($_POST['telefone'])) {
                throw new Exception("Dados incompletos");
            }

            // Cria instância do Anunciante
            $anunciante = new Anunciante($pdo);
            $anunciante->nome = $_POST['nome'];
            $anunciante->cpf = $_POST['cpf'];
            $anunciante->email = $_POST['email'];
            $anunciante->senha = $_POST['senha'];
            $anunciante->telefone = $_POST['telefone'];

            // Valida os dados
            $validationErrors = $anunciante->validarDados();
            
            if(!empty($validationErrors)) {
                throw new Exception(json_encode([
                    "status" => "error",
                    "message" => "Dados inválidos",
                    "errors" => $validationErrors
                ]));
            }

            // Tenta cadastrar
            $result = $anunciante->cadastrar();

            if($result['status'] !== "success") {
                throw new Exception(json_encode($result));
            }

            $pdo->commit();
            
            http_response_code(201);
            echo json_encode($result);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            
            // Verifica se a mensagem é um JSON
            $errorData = json_decode($e->getMessage());
            if (json_last_error() === JSON_ERROR_NONE) {
                http_response_code(400);
                echo $e->getMessage();
            } else {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]);
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Ação não disponível"]);
        exit;
}
