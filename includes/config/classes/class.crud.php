<?php
class Crud {
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

    public function createUser() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
            return;
        }
    
        $requiredFields = ['nombre_completo', 'correo_electronico', 'documento_identidad', 'carnet', 'rol', 'institucion', 'ciudad', 'pais', 'contrasena'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'error' => 'Campo requerido faltante: ' . $field]);
                return;
            }
        }
    
        try {
            $query = "INSERT INTO usuarios (nombre_completo, correo_electronico, documento_identidad, carnet, rol, institucion, ciudad, pais, contrasena) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
    
            $stmt->execute([
                $data['nombre_completo'],
                $data['correo_electronico'],
                $data['documento_identidad'],
                $data['carnet'],
                $data['rol'],
                $data['institucion'],
                $data['ciudad'],
                $data['pais'],
                sha1($data['contrasena'])
            ]);
    
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $error_message = "Excepción al crear usuario: " . $e->getMessage();
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
        }
    }    

    public function updateUser() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
            return;
        }
    
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
            return;
        }
    
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
            return;
        }
    
        try {
            $this->db->beginTransaction();
    
            $updateFields = [];
            $params = [];
    
            $allowedFields = ['nombre_completo', 'correo_electronico', 'documento_identidad', 'carnet', 'rol', 'institucion', 'direccion', 'ciudad', 'estado_provincia', 'pais'];
    
            foreach ($data as $key => $value) {
                if ($key === 'contrasena' && $value !== '') {
                    $updateFields[] = "contrasena = SHA1(?)";
                    $params[] = $value;
                } elseif ($key !== 'id' && in_array($key, $allowedFields) && $value !== '') {
                    $updateFields[] = "$key = ?";
                    $params[] = $value;
                }
            }
    
            if (empty($updateFields)) {
                echo json_encode(['success' => false, 'error' => 'No hay campos para actualizar']);
                return;
            }
    
            $params[] = $data['id'];
    
            $query = "UPDATE usuarios SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
    
            error_log("Ejecutando consulta: $query con parámetros: " . implode(", ", $params));
    
            $stmt->execute($params);
    
            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $this->db->rollBack();
            $error_message = "Excepción al actualizar usuario: " . $e->getMessage();
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
        }
    }             

    public function deleteUser() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $response = ['success' => false, 'error' => 'Esta página solo acepta solicitudes POST'];
            echo json_encode($response);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            $response = ['success' => false, 'error' => 'Error al decodificar los datos JSON'];
            echo json_encode($response);
            return;
        }
    
        if (empty($data['id'])) {
            $response = ['success' => false, 'error' => 'ID de usuario requerido'];
            echo json_encode($response);
            return;
        }
    
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            $response = ['success' => false, 'error' => $error_message];
            echo json_encode($response);
            return;
        }
    
        try {
            $query = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $this->db->prepare($query);
    
            error_log("Ejecutando consulta: $query con ID: " . $data['id']);
    
            $stmt->execute([$data['id']]);
    
            error_log("Eliminación exitosa del usuario con ID: " . $data['id']);
            $response = ['success' => true];
            echo json_encode($response);
        } catch (Exception $e) {
            $error_message = "Excepción al eliminar usuario: " . $e->getMessage();
            error_log($error_message);
            $response = ['success' => false, 'error' => $error_message];
            echo json_encode($response);
        }
    }

    public function createProject() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
            return;
        }
    
        $requiredFields = ['titulo', 'estudiantes', 'evaluadores','docentes', 'linea', 'fase', 'timer'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'error' => 'Campo requerido faltante: ' . $field]);
                return;
            }
        }
    
        try {
            $query = "INSERT INTO proyectos (titulo, investigadores, evaluador, docentes, linea, fase, timer) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
    
            $stmt->execute([
                $data['titulo'],
                $data['estudiantes'],
                $data['evaluadores'],
                $data['docentes'],
                $data['linea'],
                $data['fase'],
                $data['timer']
            ]);
    
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $error_message = "Excepción al crear proyecto: " . $e->getMessage();
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
        }
    }

    public function fetchProjects() {
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }
    
        $query = "SELECT * FROM proyectos";
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
    
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$projects) {
            $error_message = "No se encontraron proyectos.";
            error_log($error_message);
            return ['error' => $error_message];
        }
    
        foreach ($projects as &$project) {
            $project['estudiantes'] = $this->fetchUserNamesByIds($project['investigadores']);
            $project['evaluadores'] = $this->fetchUserNamesByIds($project['evaluador']);
        }
    
        return $projects;
    }
    
    private function fetchUserNamesByIds($ids) {
        if (empty($ids)) {
            return ['N/A'];
        }
    
        $idsArray = explode(',', $ids);
        $placeholders = rtrim(str_repeat('?,', count($idsArray)), ',');
        $query = "SELECT nombre_completo FROM usuarios WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . implode(" ", $this->db->errorInfo()));
            return ['N/A'];
        }
    
        if (!$stmt->execute($idsArray)) {
            error_log("Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo()));
            return ['N/A'];
        }
    
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $users ?: ['N/A'];
    }    

    public function deleteProject() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $response = ['success' => false, 'error' => 'Esta página solo acepta solicitudes POST'];
            echo json_encode($response);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            $response = ['success' => false, 'error' => 'Error al decodificar los datos JSON'];
            echo json_encode($response);
            return;
        }
    
        if (empty($data['id'])) {
            $response = ['success' => false, 'error' => 'ID de proyecto requerido'];
            echo json_encode($response);
            return;
        }
    
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            $response = ['success' => false, 'error' => $error_message];
            echo json_encode($response);
            return;
        }
    
        try {
            $query = "DELETE FROM proyectos WHERE id = ?";
            $stmt = $this->db->prepare($query);
    
            error_log("Ejecutando consulta: $query con ID: " . $data['id']);
    
            $stmt->execute([$data['id']]);
    
            error_log("Eliminación exitosa del proyecto con ID: " . $data['id']);
            $response = ['success' => true];
            echo json_encode($response);
        } catch (Exception $e) {
            $error_message = "Excepción al eliminar proyecto: " . $e->getMessage();
            error_log($error_message);
            $response = ['success' => false, 'error' => $error_message];
            echo json_encode($response);
        }
    }

    public function updateProject() {
        header('Content-Type: application/json');
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
            return;
        }
    
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID del proyecto requerido']);
            return;
        }
    
        $projectId = $data['id'];
        error_log("ID del proyecto recibido: $projectId");
    
        if (!$this->validateProjectExists($projectId)) {
            $error_message = "Proyecto no válido: " . $projectId;
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
            return;
        }
    
        $currentProjectData = $this->getProjectData($projectId);
        error_log("Datos actuales del proyecto: " . json_encode($currentProjectData));
    
        try {
            $this->db->beginTransaction();
    
            $updateFields = [];
            $params = [];
    
            $allowedFields = ['titulo', 'docentes', 'linea', 'fase', 'timer', 'investigadores', 'evaluador'];
    
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields) && $value !== '' && $currentProjectData[$key] !== $value) {
                    $updateFields[] = "$key = ?";
                    $params[] = $value;
                    error_log("Campo actualizado: $key, Valor nuevo: $value, Valor actual: " . $currentProjectData[$key]);
                }
            }
    
            if (empty($updateFields)) {
                echo json_encode(['success' => false, 'error' => 'No hay campos para actualizar o los datos no han cambiado']);
                return;
            }
    
            $params[] = $projectId;
    
            $query = "UPDATE proyectos SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
    
            error_log("Ejecutando consulta: $query con parámetros: " . json_encode($params));
    
            $stmt->execute($params);
    
            if ($stmt->rowCount() === 0) {
                $error_message = "No se actualizó ningún registro. Verifica si el ID del proyecto es correcto.";
                error_log($error_message);
                throw new Exception($error_message);
            }
    
            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $this->db->rollBack();
            $error_message = "Excepción al actualizar proyecto: " . $e->getMessage();
            error_log($error_message);
            echo json_encode(['success' => false, 'error' => $error_message]);
        }
    }
    
    private function validateProjectExists($projectId) {
        $query = "SELECT COUNT(*) FROM proyectos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$projectId]);
        $exists = $stmt->fetchColumn() > 0;
        error_log("El proyecto con ID $projectId " . ($exists ? "existe." : "no existe."));
        return $exists;
    }
    
    private function getProjectData($projectId) {
        $query = "SELECT * FROM proyectos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }       
}
?>