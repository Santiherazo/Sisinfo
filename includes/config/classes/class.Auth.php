<?php
class Auth {
    private $db;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function login($documento_identidad, $contrasena) {
        $stmt = $this->db->prepare('SELECT id, contrasena, rol, nombre_completo FROM usuarios WHERE documento_identidad = :documento_identidad');
        $stmt->execute(['documento_identidad' => $documento_identidad]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['contrasena'] === sha1($contrasena)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['user'] = $user['nombre_completo'];
            return true;
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserRole() {
        return $_SESSION['user_role'];
    }
}
?>
