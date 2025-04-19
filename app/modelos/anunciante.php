<?php

class Anunciante
{
    static function Criar($pdo, $nome, $cpf, $email, $senhaHash, $telefone)
    {
        $stmt = $pdo->prepare(
            <<<SQL
            INSERT INTO Anunciante (nome, cpf, email, senhaHash, telefone)
            VALUES (?, ?, ?, ?, ?);
            SQL
        );

        $stmt->execute([$nome, $cpf, $email, $senhaHash, $telefone]);

        return $pdo->lastInsertId();
    }
}
