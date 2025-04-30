<?php
class Session {
    public function start() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function destroy() {
        $this->start();
        session_unset();
        session_destroy();
    }
}
?>