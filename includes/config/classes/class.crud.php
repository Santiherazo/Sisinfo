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

    public function updateUser() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ['success' => false, 'error' => 'Esta página solo acepta solicitudes POST'];
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            return ['success' => false, 'error' => 'Error al decodificar los datos JSON'];
        }

        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }

        try {
            $this->db->beginTransaction();

            $updateFields = [];
            $params = [];

            foreach ($data as $key => $value) {
                if (!empty($value) || $value === 0) {
                    $updateFields[] = "$key = ?";
                    $params[] = $value;
                }
            }

            if (empty($updateFields)) {
                return ['success' => false, 'error' => 'No hay campos para actualizar'];
            }

            $params[] = $data['id']; // Assuming 'id' is present in the incoming data

            $query = "UPDATE usuarios SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);

            if (!$stmt->execute($params)) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Error al actualizar los datos'];
            }

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteUser() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ['success' => false, 'error' => 'Esta página solo acepta solicitudes POST'];
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            return ['success' => false, 'error' => 'Error al decodificar los datos JSON'];
        }

        if (empty($data['id'])) {
            return ['success' => false, 'error' => 'ID de usuario requerido'];
        }

        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }

        try {
            $this->db->beginTransaction();

            $query = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $this->db->prepare($query);

            if (!$stmt->execute([$data['id']])) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Error al eliminar el usuario'];
            }

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>