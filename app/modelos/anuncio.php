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

        while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            header("Info: " . $row['idAnuncio']);

            $idAnuncio = $row['idAnuncio'];

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

            if (!empty($row['nomeArqFoto'])) {
                $anuncios[$idAnuncio]['fotos'][] = "uploads/anuncios/{$idAnuncio}/{$row['nomeArqFoto']}";
            }
        }

        $anuncios = array_values($anuncios);

        return $anuncios;
    }
}
