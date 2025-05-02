<?

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";
require "./modelos/anunciante.php";

$acao = $_GET['acao'] ?? '';

$pdo = mysqlConnect();

switch ($acao) {
    case "listarAnuncios":
        header("Content-Type: application/json; charset=UTF-8");

        $pagina = $_GET['pagina'] ?? 1;
        $limite = 20;
        $offset = ($pagina - 1) * $limite;

        try {
            $anuncios = Anuncio::GetAnuncios($pdo, $limite, $offset);

            http_response_code(200);
            echo json_encode($anuncios);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Ocorreu um erro durante os anuncios"
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

            $nome = $_POST['nome'] ?? '';
            $cpf = $_POST['cpf'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $telefone = $_POST['telefone'] ?? '';

            Anunciante::cadastrar(
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
