<?

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";
require "./modelos/anunciante.php";

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

        try {
            $pdo->beginTransaction();

            if (
                empty($_POST['nome']) || empty($_POST['cpf']) || empty($_POST['email']) ||
                empty($_POST['senha']) || empty($_POST['telefone'])
            ) {
                throw new Exception("Dados incompletos");
            }

            $anunciante = new Anunciante($pdo);
            $nome = $_POST['nome'] ?? '';
            $cpf = $_POST['cpf'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $telefone = $_POST['telefone'] ?? '';

            $anunciante::cadastrar(
                $pdo,
                $nome,
                $cpf,
                $email,
                $senha,
                $telefone
            );

            $pdo->commit();

            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Anunciante cadastrado com sucesso."
            ]);
        } catch (Exception $e) {
            $pdo->rollBack();

            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }

        break;

    case "loginUsuario":
        header("Content-Type: application/json; charset=UTF-8");

        try {
            $email = $_POST["email"] ?? '';
            $senha = $_POST["senha"] ?? '';

            $resultado = Anunciante::login($pdo, $email, $senha);

            if ($resultado["status"] == "success") {
                session_start();

                $_SESSION['user_id'] = $resultado['id'];

                http_response_code(200);
                echo json_encode([
                    "status" => "success"
                ]);
            } else {
                http_response_code(401);
                echo json_encode($resultado);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }

        break;

    // case "logoutUsuario":
    //     $session->start();
    //     $session->destroy();

    //     echo json_encode([
    //         "status" => "success",
    //         "message" => "Logout realizado",
    //         "redirect" => "index.html"
    //     ]);
    //     break;


    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Ação não disponível"]);
        exit;
}
