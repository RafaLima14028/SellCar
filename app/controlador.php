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

        header("Content-Type: application/json; charset=UTF-8");

        try {
            $pdo->beginTransaction();

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode([
                    "status" => "error",
                    "message" => "Usuário não logado"
                ]);
                exit;
            }

            $idAnunciante = $_SESSION['user_id'];

            if (!isset($_FILES['imgs-carro']) || !is_array($_FILES['imgs-carro']['name'])) {
                error_log("Debug - FILES structure: " . print_r($_FILES, true));
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Problema com o envio das imagens",
                    "debug" => $_FILES
                ]);
                exit;
            }

            $idAnuncio = Anuncio::Criar($pdo, $marca, $modelo, $ano, $cor, $quilometragem, $descricao, $valor, $estado, $cidade, $idAnunciante);

            $pasta_destino = "uploads/anuncios/" . $idAnuncio;

            // Create directory with better error handling
            if (!is_dir($pasta_destino)) {
                if (!mkdir($pasta_destino, 0777, true)) {
                    throw new Exception("Não foi possível criar o diretório para as imagens");
                }
                chmod($pasta_destino, 0777); // Ensure directory has proper permissions
            }

            // Process files with better error handling
            foreach ($_FILES['imgs-carro']['tmp_name'] as $index => $tmp_name) {
                // Skip if no file was uploaded in this slot
                if (empty($tmp_name) || $_FILES['imgs-carro']['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $nome_original = basename($_FILES['imgs-carro']['name'][$index]);
                $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
                $novo_nome = uniqid('img_', true) . "." . $extensao;
                $caminho_completo = $pasta_destino . '/' . $novo_nome;

                if (move_uploaded_file($tmp_name, $caminho_completo)) {
                    // Ensure the file got proper permissions
                    chmod($caminho_completo, 0644);
                    Foto::Criar($pdo, $idAnuncio, $novo_nome);
                } else {
                    throw new Exception("Falha ao salvar o arquivo: " . $nome_original);
                }
            }

            $pdo->commit();

            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Veículo criado com sucesso",
                "id" => $idAnuncio
            ]);
        } catch (Exception $e) {
            $pdo->rollBack();

            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Ocorreu um erro durante o cadastro do veículo: " . $e->getMessage()
            ]);
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
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $resultado['id'];

                http_response_code(200);
                echo json_encode([
                    "status" => "success"
                ]);
            } else {
                // $_SESSION['user_id'] = null;

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

    case "logoutUsuario":
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            session_unset();
            session_destroy();
        }

        echo json_encode([
            "status" => "success",
            "message" => "Logout realizado"
        ]);

        break;

    case "estaLogado":
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            http_response_code(200);

            echo json_encode([
                "status" => "logado"
            ]);
        } else {
            http_response_code(401);

            echo json_encode([
                "status" => "deslogado"
            ]);
        }

        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Ação não disponível"]);
        exit;
}
