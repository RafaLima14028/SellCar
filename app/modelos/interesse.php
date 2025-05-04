<?php

class Interesse
{

    static function Create($pdo, $nome, $telefone, $mensagem, $idAnuncio)
    {

        $stmt = $pdo->prepare("SELECT id FROM Anuncio WHERE id = ? LIMIT 1");
        $stmt->execute([$idAnuncio]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("O anúncio referenciado não existe");
        }

        $stmt = $pdo->prepare(
            <<<SQL
            INSERT INTO Interesse(nome, telfone, mensagem, idAnuncio)
            VALUES (?, ?, ?, ?)
            SQL
        );

        $stmt->execute([$nome, $telefone, $mensagem, $idAnuncio]);

        return $pdo->lastInsertId();
    }

    static function GetByAnuncio($pdo, $idAnuncio)
    {

        $stmt = $pdo->prepare(
            <<<SQL
            SELECT id, nome, telefone, mensagem, DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i') as data_hora
            FROM Interesse
            WHERE idAnuncio = ?
            ORDER BY data_hora DESC
            SQL
        );

        $stmt->execute([$idAnuncio]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function Remove($pdo, $id)
    {

        $stmt = $pdo->prepare(
            <<<SQL
            DELETE FROM Interesse
            WHERE id = ?
            LIMIT 1
            SQL
        );

        return $stmt->execute([$id]);
    }
}
