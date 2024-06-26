<?php 
class crud {
    private $db;
    private $user;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }

    public function fetchUsers() {
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }

        $query = "SELECT * FROM usuarios";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$users) {
            $error_message = "No se encontraron usuarios.";
            error_log($error_message);
            return ['error' => $error_message];
        }

        return $users;
    }

    private function validateUserExists() {
        $query = "SELECT id FROM usuarios WHERE nombre_completo = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en validateUserExists: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return false;
        }
        $stmt->bindValue(1, $this->user, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en validateUserExists: " . $errorInfo[2];
            error_log($error_message);
            return false;
        }
        $exists = $stmt->fetchColumn() !== false;
        error_log("Usuario {$this->user} existe: " . ($exists ? 'Sí' : 'No'));
        return $exists;
    }
}
?>