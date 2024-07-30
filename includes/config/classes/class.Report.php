<?php
class Report {
    private $db;
    private $user;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }

    public function getResults() {
        if (!$this->validateUserExistsInProjects()) {
            $error_message = "Usuario no válido o no está en la tabla de proyectos: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }

        $projects = $this->getUserProjects();
        if (isset($projects['error'])) {
            return $projects;
        }

        $result = [];
        foreach ($projects as $project) {
            error_log("Procesando proyecto ID: " . $project['id']);

            $investigadores_ids = array_filter(explode(',', $project['investigadores']));
            $investigadores_nombres = $this->getUserNamesByIds($investigadores_ids);

            $evaluadores_ids = array_filter(explode(',', $project['evaluador']));
            $evaluadores_nombres = $this->getUserNamesByIds($evaluadores_ids);

            $calificaciones = $this->getCalificacionesByProjectId($project['id']);

            $result[] = [
                'id' => $project['id'],
                'investigadores' => $investigadores_nombres,
                'docentes' => $project['docentes'],
                'evaluadores' => $evaluadores_nombres,
                'titulo' => $project['titulo'],
                'linea' => $project['linea'],
                'fase' => $project['fase'],
                'timer' => $project['timer'],
                'calificaciones' => $calificaciones,
            ];
        }

        error_log("Proyectos finales: " . json_encode($result));
        return $result;
    }

    public function getReport() {
        $projects = $this->getAllProjects();
        if (isset($projects['error'])) {
            return $projects;
        }

        $result = [];
        foreach ($projects as $project) {
            error_log("Procesando proyecto ID: " . $project['id']);

            $investigadores_ids = array_filter(explode(',', $project['investigadores']));
            $investigadores_nombres = $this->getUserNamesByIds($investigadores_ids);

            $evaluadores_ids = array_filter(explode(',', $project['evaluador']));
            $evaluadores_nombres = $this->getUserNamesByIds($evaluadores_ids);

            $calificaciones = $this->getCalificacionesByProjectId($project['id']);

            $result[] = [
                'id' => $project['id'],
                'investigadores' => $investigadores_nombres,
                'docentes' => $project['docentes'],
                'evaluadores' => $evaluadores_nombres,
                'titulo' => $project['titulo'],
                'linea' => $project['linea'],
                'fase' => $project['fase'],
                'timer' => $project['timer'],
                'calificaciones' => $calificaciones,
            ];
        }

        error_log("Proyectos finales: " . json_encode($result));
        return $result;
    }

    private function getUserNamesByIds($ids) {
        if (empty($ids)) {
            $error_message = "Lista de IDs de usuarios vacía.";
            error_log($error_message);
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT nombre_completo FROM usuarios WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            $error_message = "Error al preparar la consulta en getUserNamesByIds: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return [];
        }

        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en getUserNamesByIds: " . $errorInfo[2];
            error_log($error_message);
            return [];
        }

        $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
        error_log("Nombres de usuarios obtenidos: " . json_encode($names));
        return $names;
    }

    private function getCalificacionesByProjectId($projectId) {
        $query = "SELECT * FROM calificaciones WHERE idProject = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en getCalificacionesByProjectId: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return [];
        }
        $stmt->bindValue(1, $projectId, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en getCalificacionesByProjectId: " . $errorInfo[2];
            error_log($error_message);
            return [];
        }
        $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Calificaciones obtenidas para el proyecto ID '$projectId': " . json_encode($calificaciones));
        return $calificaciones;
    }

    public function exportProjectsToJson() {
        $projects = $this->getResults();
        if (isset($projects['error'])) {
            echo "Error al obtener proyectos: " . $projects['error'];
            return;
        }
        $json_data = json_encode($projects, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents('proyectos.json', $json_data);
        echo "Archivo JSON creado exitosamente.";
    }

    private function validateUserExistsInProjects() {
        $query = "SELECT p.id 
                  FROM proyectos p
                  LEFT JOIN usuarios u_investigadores ON FIND_IN_SET(u_investigadores.id, p.investigadores)
                  LEFT JOIN usuarios u_evaluadores ON FIND_IN_SET(u_evaluadores.id, p.evaluador)
                  WHERE u_investigadores.nombre_completo = ? OR u_evaluadores.nombre_completo = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en validateUserExistsInProjects: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return false;
        }
        $stmt->bindValue(1, $this->user, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->user, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en validateUserExistsInProjects: " . $errorInfo[2];
            error_log($error_message);
            return false;
        }
        $exists = $stmt->fetchColumn() !== false;
        error_log("Usuario {$this->user} existe en proyectos: " . ($exists ? 'Sí' : 'No'));
        return $exists;
    }

    private function getUserProjects() {
        $query = "
            SELECT 
                p.id,
                p.titulo,
                p.linea,
                p.fase,
                p.timer,
                p.investigadores,
                p.evaluador,
                p.calificado,
                p.docentes,
                GROUP_CONCAT(DISTINCT u_investigadores.nombre_completo) AS investigadores_nombres,
                GROUP_CONCAT(DISTINCT u_evaluadores.nombre_completo) AS evaluadores_nombres
            FROM 
                proyectos p
            LEFT JOIN usuarios u_investigadores ON FIND_IN_SET(u_investigadores.id, p.investigadores)
            LEFT JOIN usuarios u_evaluadores ON FIND_IN_SET(u_evaluadores.id, p.evaluador)
            WHERE (u_investigadores.nombre_completo = ? OR u_evaluadores.nombre_completo = ?) 
              AND p.calificado = 2
            GROUP BY p.id
        ";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en getUserProjects: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }
        $stmt->bindValue(1, $this->user, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->user, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en getUserProjects: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }

        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Proyectos obtenidos para el usuario {$this->user}: " . json_encode($projects));
        return $projects;
    }

    private function getAllProjects() {
        $query = "
            SELECT 
                p.id,
                p.titulo,
                p.linea,
                p.fase,
                p.timer,
                p.investigadores,
                p.evaluador,
                p.calificado,
                p.docentes,
                GROUP_CONCAT(DISTINCT u_investigadores.nombre_completo) AS investigadores_nombres,
                GROUP_CONCAT(DISTINCT u_evaluadores.nombre_completo) AS evaluadores_nombres
            FROM 
                proyectos p
            LEFT JOIN usuarios u_investigadores ON FIND_IN_SET(u_investigadores.id, p.investigadores)
            LEFT JOIN usuarios u_evaluadores ON FIND_IN_SET(u_evaluadores.id, p.evaluador)
            WHERE p.calificado = 2
            GROUP BY p.id
        ";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en getAllProjects: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en getAllProjects: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }

        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Proyectos obtenidos: " . json_encode($projects));
        return $projects;
    }
}
?>
