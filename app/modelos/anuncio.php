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

        return $pdo->lastInserId();
    }
}
