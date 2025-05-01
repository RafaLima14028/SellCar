<?php
class Anunciante
{
    public static function cadastrar($pdo, $nome, $cpf, $email, $senha_hash, $telefone)
    {
        $stmt = $pdo->prepare(
            <<<SQL
            INSERT INTO Anunciante (nome, cpf, email, senhaHash, telefone)
            VALUES (?, ?, ?, ?, ?);
            SQL
        );

        $senha_hash = password_hash($senha_hash, PASSWORD_BCRYPT);

        $stmt->execute([$nome, $cpf, $email, $senha_hash, $telefone]);

        return $pdo->lastInsertId();
    }

    public static function login($pdo, $email, $senha)
    {
        $stmt = $pdo->prepare(
            <<<SQL
            SELECT id, senhaHash
            FROM Anunciante
            WHERE email = ?
            SQL
        );

        $stmt->execute([$email]);

        header("Info: " . $stmt->rowCount() . " email: " . $email);

        while ($row = $stmt->fetch()) {
            header("Info1: Entrou no while");

            if (password_verify($senha, $row["senhaHash"])) {
                return [
                    "status" => "success",
                    "id" => $row["id"]
                ];
            }
        }

        return [
            "status" => "error",
            "message" => "Credenciais inválidas"
        ];
    }
}
