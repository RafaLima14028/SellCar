<?php

class Foto
{
    static function Criar($pdo, $idAnuncio, $nomeArqFoto)
    {
        $stmt = $pdo->prepare(
            <<<SQL
            INSERT INTO Foto (idAnuncio, nomeArqFoto)
            VALUES (?, ?);
            SQL
        );

        $stmt->execute([$idAnuncio, $nomeArqFoto]);

        return $pdo->lastInsertId();
    }
}
