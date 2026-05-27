<?php
require_once '../app/models/Usuario.php';

class AuthController {
    private $db;
    private $usuario;

    public function __construct($db) {
        $this->db = $db;
        $this->usuario = new Usuario($db);
    }

    public function login() {
        // Quitamos cualquier duplicado de session_start() para limpiar el aviso de Brave
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usr = $_POST['usuario'] ?? '';
            $pass = $_POST['password'] ?? '';
            
            if ($this->usuario->login($usr, $pass)) {
                // Inyectamos las variables limpias en la memoria global de XAMPP
                $_SESSION['user_id'] = $this->usuario->id_usuario;
                $_SESSION['user_nombre'] = $this->usuario->nombre;
                $_SESSION['user_rol'] = $this->usuario->rol;
                
                // Redirección directa
                header("Location: index.php?route=dashboard");
                exit();
            } else {
                header("Location: index.php?route=login&error=1");
                exit();
            }
        } else {
            require_once '../app/views/auth/login.php';
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: index.php?route=login");
        exit();
    }
}
?>