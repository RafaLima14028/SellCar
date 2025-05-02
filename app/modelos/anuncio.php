<?php

class Anuncio
{
    static function Criar(
        $pdo,
        $marca,
        $modelo,
        $ano,
        $cor,
        $quilometragem,
        $descricao,
        $valor,
        $estado,
        $cidade,
        $idAnunciante
    ) {
        $stmt = $pdo->prepare(
            <<<SQL
            INSERT INTO Anuncio (marca, modelo, ano, cor, quilometragem, descricao, valor, estado, cidade, idAnunciante)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
            SQL
        );

        $stmt->execute([$marca, $modelo, $ano, $cor, $quilometragem, $descricao, $valor, $estado, $cidade, $idAnunciante]);

        return $pdo->lastInsertId();
    }

    public static function GetAnuncios($pdo, $limite, $offset)
    {
        $sql = <<<SQL
        SELECT 
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
        ORDER BY 
            a.dataHora DESC
        LIMIT :limite OFFSET :offset
        SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $anuncios = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $idAnuncio = $row['idAnuncio'];

            if (!isset($anuncios[$idAnuncio])) {
                $anuncios[$idAnuncio] = [
                    'id' => $row['idAnuncio'],
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
                $anuncios[$idAnuncio]['fotos'][] = "uploads/anuncios/{$idAnuncio}/{$row['nomeArqFoto']}";
            }
        }

        return array_values($anuncios);
    }

    public static function GetAnuncioById($pdo, $idAnuncio)
    {
        $sql = <<<SQL
        SELECT 
            Anuncio.*, 
            Foto.nomeArqFoto 
        FROM 
            Anuncio 
        LEFT JOIN 
            Foto 
        ON 
            Anuncio.id = Foto.idAnuncio 
        WHERE 
            Anuncio.id = :idAnuncio
        SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idAnuncio', $idAnuncio, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultado)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Anúncio não encontrado"
            ]);
            exit;
        }

        $anuncio = null;
        $fotos = [];

        foreach ($resultado as $row) {
            if ($anuncio === null) {
                $anuncio = [
                    'id' => $row['id'],
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
                    'idAnunciante' => $row['idAnunciante'],
                ];
            }

            if (!empty($row['nomeArqFoto'])) {
                $fotos[] = $row['nomeArqFoto'];
            }
        }

        $anuncio['fotos'] = $fotos;

        return $anuncio;
    }
}
