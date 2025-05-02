<?

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";
require "./modelos/anunciante.php";

$acao = $_GET['acao'] ?? '';

$pdo = mysqlConnect();

switch ($acao) {
    case "filtrarAnuncios":
        header("Content-Type: application/json; charset=UTF-8");

        $marca = $_GET['marca'] ?? '';
        $modelo = $_GET['modelo'] ?? '';
        $cidade = $_GET['cidade'] ?? '';

        try {
            $sql = "SELECT 
                    a.id AS idAnuncio, 
                    a.marca, 
                    a.modelo, 
                    a.ano, 
                    a.cor, 
                    a.quilometragem, 
                    a.descricao, 
                    a.valor, 
                    a.dataHora, 
                    a.estado, 
                    a.cidade, 
                    f.nomeArqFoto 
                FROM 
                    Anuncio a
                LEFT JOIN 
                    Foto f ON a.id = f.idAnuncio
                WHERE 1=1";

            $params = [];
            $types = '';

            if (!empty($marca)) {
                $sql .= " AND a.marca = ?";
                $params[] = $marca;
                $types .= 's';
            }

            if (!empty($modelo)) {
                $sql .= " AND a.modelo = ?";
                $params[] = $modelo;
                $types .= 's';
            }

            if (!empty($cidade)) {
                $sql .= " AND a.cidade = ?";
                $params[] = $cidade;
                $types .= 's';
            }

            $sql .= " ORDER BY a.dataHora DESC LIMIT 20";

            $stmt = $pdo->prepare($sql);

            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            $anuncios = [];
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $idAnuncio = $row['idAnuncio'];

                if (!isset($anuncios[$idAnuncio])) {
                    $anuncios[$idAnuncio] = [
                        'id' => $idAnuncio,
                        'marca' => $row['marca'],
                        'modelo' => $row['modelo'],
                        'ano' => $row['ano'],
                        'cor' => $row['cor'],
                        'quilometragem' => $row['quilometragem'],
                        'descricao' => $row['descricao'],
                        'valor' => (float)$row['valor'],
                        'dataHora' => $row['dataHora'],
                        'estado' => $row['estado'],
                        'cidade' => $row['cidade'],
                        'fotos' => []
                    ];
                }

                if (!empty($row['nomeArqFoto'])) {
                    $anuncios[$idAnuncio]['fotos'][] = "uploads/anuncios/{$idAnuncio}/{$row['nomeArqFoto']}";
                }
            }

            echo json_encode(array_values($anuncios));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro ao listar anúncios: " . $e->getMessage()]);
        }
        
        break;

    case "cidadesDestintas":
        $marca = $_GET['marca'] ?? '';
        $modelo = $_GET['modelo'] ?? '';

        if (empty($marca) || empty($modelo)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Marca ou modelo não informados"]);
            exit;
        }

        try {
            $sql = <<<SQL
            SELECT DISTINCT cidade FROM Anuncio WHERE marca = ? AND modelo = ? AND cidade IS NOT NULL AND cidade != '' ORDER BY cidade
            SQL;

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$marca, $modelo]);

            $cidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo json_encode($cidades);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro ao listar cidades"]);
        }

        break;
    case "modelosDestintas":
        $marca = $_GET['marca'] ?? '';

        if (empty($marca)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Marca não informada"]);
            exit;
        }
        try {
            $sql = <<<SQL
            SELECT DISTINCT modelo FROM Anuncio WHERE marca = ? AND modelo IS NOT NULL AND modelo != '' ORDER BY modelo
            SQL;

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$marca]);

            $modelos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo json_encode($modelos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro ao listar modelos"]);
        }

        break;
    case "marcasDistintas":
        try {
            $stmt = $pdo->query(
                <<<SQL
                SELECT DISTINCT marca FROM Anuncio WHERE marca IS NOT NULL AND marca != '' ORDER BY marca
                SQL
            );

            $marcas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            http_response_code(200);
            echo json_encode($marcas);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro ao listar marcas"]);
        }

        break;

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
