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

    // public function emailExiste()
    // {
    //     $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
    //     $stmt = $this->conn->prepare($query);
    //     $this->email = htmlspecialchars(strip_tags($this->email));
    //     $stmt->bindParam(1, $this->email);
    //     $stmt->execute();
    //     $num = $stmt->rowCount();

    //     if ($num > 0) {
    //         return true;
    //     }

    //     return false;
    // }

    // public function cpfExiste()
    // {
    //     $query = "SELECT id FROM " . $this->table_name . " WHERE cpf = ? LIMIT 0,1";
    //     $stmt = $this->conn->prepare($query);
    //     $this->cpf = htmlspecialchars(strip_tags($this->cpf));
    //     $stmt->bindParam(1, $this->cpf);
    //     $stmt->execute();
    //     $num = $stmt->rowCount();

    //     if ($num > 0) {
    //         return true;
    //     }

    //     return false;
    // }

    public static function login($pdo, $email, $senha)
    {
        $stmt = $pdo->prepare(
            <<<SQL
            SELECT id, senhaHash FROM Anunciante 
            WHERE email = ?;
            SQL
        );

        $stmt->execute([$email]);

        while ($row = $stmt->fetch()) {
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
