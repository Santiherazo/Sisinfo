<?php
// Verificar si ya existe una sesión activa antes de iniciar una nueva sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    public function login() {
        
    }

    public function logout() {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php"); // Redirigir al inicio después de cerrar sesión
        exit();
    }

    public function register() {
        
    }
}

// Verificar si se está solicitando el cierre de sesión
if (isset($_GET['logout'])) {
    $authController = new AuthController();
    $authController->logout();
}
?>
