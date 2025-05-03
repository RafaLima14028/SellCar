<?php

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";
require "./modelos/anunciante.php";

$acao = $_GET['acao'] ?? '';

$pdo = mysqlConnect();

switch ($acao) {
    case "excluirCarro":
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header("Content-Type: application/json; charset=UTF-8");

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Usuário não logado"
            ]);
            exit;
        }

        $idAnunciante = $_SESSION['user_id'];
        $idAnuncio = $_GET['idAnuncio'] ?? 0;

        if ($idAnuncio <= 0) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltou o ID do anúncio"
            ]);
            exit;
        }

        $queryVerificacao = "SELECT id FROM Anuncio WHERE id = :idAnuncio AND idAnunciante = :idAnunciante";

        $stmtVerificacao = $pdo->prepare($queryVerificacao);

        $stmtVerificacao->bindParam(':idAnuncio', $idAnuncio, PDO::PARAM_INT);
        $stmtVerificacao->bindParam(':idAnunciante', $idAnunciante, PDO::PARAM_INT);

        $stmtVerificacao->execute();

        if ($stmtVerificacao->rowCount() > 0) {
            $queryExcluirFotos = "DELETE FROM Foto WHERE idAnuncio = :idAnuncio";
            $stmtExcluirFotos = $pdo->prepare($queryExcluirFotos);
            $stmtExcluirFotos->bindParam(':idAnuncio', $idAnuncio, PDO::PARAM_INT);
            $stmtExcluirFotos->execute();

            $queryExcluirInteresses = "DELETE FROM Interesse WHERE idAnuncio = :idAnuncio";
            $stmtExcluirInteresses = $pdo->prepare($queryExcluirInteresses);
            $stmtExcluirInteresses->bindParam(':idAnuncio', $idAnuncio, PDO::PARAM_INT);
            $stmtExcluirInteresses->execute();

            $queryExcluirAnuncio = "DELETE FROM Anuncio WHERE id = :idAnuncio";
            $stmtExcluirAnuncio = $pdo->prepare($queryExcluirAnuncio);
            $stmtExcluirAnuncio->bindParam(':idAnuncio', $idAnuncio, PDO::PARAM_INT);
            $stmtExcluirAnuncio->execute();
        }

        http_response_code(200);
        echo json_encode([
            "status" => "success"
        ]);

        break;
    case "buscaCarrosDoUsuario":
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header("Content-Type: application/json; charset=UTF-8");

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Usuário não logado"
            ]);
            exit;
        }

        $idAnunciante = $_SESSION['user_id'];

        $query = <<<SQL
        SELECT 
            Anuncio.id AS anuncioId,
            Anuncio.marca,
            Anuncio.modelo,
            Anuncio.ano,
            Anuncio.cor,
            Anuncio.quilometragem,
            Anuncio.descricao,
            Anuncio.valor,
            Anuncio.dataHora,
            Anuncio.estado,
            Anuncio.cidade,
            Foto.nomeArqFoto
        FROM 
            Anuncio
        LEFT JOIN 
            Foto ON Anuncio.id = Foto.idAnuncio
        WHERE 
            Anuncio.idAnunciante = :idAnunciante
        SQL;

        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":idAnunciante", $idAnunciante, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $anuncioId = $row['anuncioId'];

                if (!isset($resultados[$anuncioId])) {
                    $resultados[$anuncioId] = [
                        'anuncioId' => $row['anuncioId'],
                        'marca' => $row['marca'],
                        'modelo' => $row['modelo'],
                        'ano' => $row['ano'],
                        'cor' => $row['cor'],
                        'quilometragem' => $row['quilometragem'],
                        'descricao' => $row['descricao'],
                        'valor' => $row['valor'],
                        'dataHora' => $row['dataHora'],
                        'estado' => $row['estado'],
                        'cidade' => $row['cidade'],
                        'fotos' => []
                    ];
                }

                if (!empty($row['nomeArqFoto'])) {
                    $resultados[$anuncioId]['fotos'][] = $row['nomeArqFoto'];
                }
            }

            http_response_code(200);
            echo json_encode(array_values($resultados));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "Não foi possível buscar os anúncios do usuário"
                ]
            );
        }

        break;
    case "buscaAnuncioComFotoPeloId":
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

        $idAnuncio = $_GET['idAnuncio'] ?? 0;

        header("Content-Type: application/json; charset=UTF-8");

        if ($idAnuncio <= 0) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltou o ID do anúncio"
            ]);
            exit;
        }

        try {
            $anuncio = Anuncio::GetAnuncioById($pdo, $idAnuncio);

            http_response_code(200);
            echo json_encode($anuncio);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Erro na busca pelo anúncio"
            ]);
        }

        break;

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

            if (!is_dir($pasta_destino)) {
                if (!mkdir($pasta_destino, 0777, true)) {
                    throw new Exception("Não foi possível criar o diretório para as imagens");
                }
                chmod($pasta_destino, 0777);
            }

            foreach ($_FILES['imgs-carro']['tmp_name'] as $index => $tmp_name) {
                if (empty($tmp_name) || $_FILES['imgs-carro']['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $nome_original = basename($_FILES['imgs-carro']['name'][$index]);
                $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
                $novo_nome = uniqid('img_', true) . "." . $extensao;
                $caminho_completo = $pasta_destino . '/' . $novo_nome;

                if (move_uploaded_file($tmp_name, $caminho_completo)) {
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
}
