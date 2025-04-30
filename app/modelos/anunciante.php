<?php
class Anunciante {
    private $conn;
    private $table_name = "anunciante";

    public $id;
    public $nome;
    public $cpf;
    public $email;
    public $senha_hash;
    public $telefone;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome = :nome, cpf = :cpf, email = :email, 
                  senha_hash = :senha_hash, telefone = :telefone";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->cpf = htmlspecialchars(strip_tags($this->cpf));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));

        // Hash da senha
        $this->senha_hash = password_hash($this->senha_hash, PASSWORD_BCRYPT);

        // Bind dos parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":cpf", $this->cpf);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha_hash", $this->senha_hash);
        $stmt->bindParam(":telefone", $this->telefone);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0) {
            return true;
        }

        return false;
    }

    public function cpfExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cpf = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->cpf = htmlspecialchars(strip_tags($this->cpf));
        $stmt->bindParam(1, $this->cpf);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0) {
            return true;
        }

        return false;
    }

    public function login() {
        $query = "SELECT id, senhaHash FROM Anunciante WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->senha, $row['senhaHash'])) {
                return [
                    "status" => "success",
                    "id" => $row['id']
                ];
            }
        }
        
        return [
            "status" => "error",
            "message" => "Credenciais inválidas"
        ];
    }
}
?>