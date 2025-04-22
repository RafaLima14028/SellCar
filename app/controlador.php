<?

require "conexaoMysql.php";
require "./modelos/anuncio.php";
require "./modelos/foto.php";


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

    default:
        exit("Ação não disponível");
}
