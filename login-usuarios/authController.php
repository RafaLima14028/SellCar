<?php
require_once __DIR__ . '/app/modelos/anunciante.php';
require_once __DIR__ . '/app/config/session.php';

class AuthController {
    private $anunciante;
    private $session;
    
    public function __construct($db) {
        $this->anunciante = new Anunciante($db);
        $this->session = new Session();
    }
    
    public function login($email, $senha) {
        $this->anunciante->email = $email;
        $this->anunciante->senha = $senha;
        
        $result = $this->anunciante->login();
        
        if($result['status'] === "success") {
            $this->session->start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['logged_in'] = true;
        }
        
        return $result;
    }
    
    public function logout() {
        $this->session->destroy();
        return array("status" => "success");
    }
    
    public function isLoggedIn() {
        $this->session->start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}
?>