<?php 
class ProjectHandler {
    private $db;
    private $user;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }

    public function getProjects() {
        if (!$this->validateUserExists()) {
            $error_message = "Usuario no válido: " . $this->user;
            error_log($error_message);
            return ['error' => $error_message];
        }

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
            GROUP BY p.id
        ";

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
        error_log("Resultados de la consulta: " . json_encode($projects));

        if (!$projects) {
            $error_message = "No se encontraron proyectos.";
            error_log($error_message);
            return ['error' => $error_message];
        }

        $result = [];
        foreach ($projects as $project) {
            error_log("Procesando proyecto ID: " . $project['id']);
            
            $evaluadores_ids = array_filter(explode(',', $project['evaluador']));
            error_log("Evaluadores IDs: " . json_encode($evaluadores_ids));
            $queryEvaluadores = "SELECT nombre_completo FROM usuarios WHERE FIND_IN_SET(id, ?)";
            $stmtEvaluadores = $this->db->prepare($queryEvaluadores);
            $stmtEvaluadores->execute([$project['evaluador']]);
            $evaluadores = $stmtEvaluadores->fetchAll(PDO::FETCH_COLUMN);
            error_log("Nombres de evaluadores: " . json_encode($evaluadores));

            $user_is_evaluator = in_array($this->user, $evaluadores);
            error_log("Usuario actual es evaluador: " . ($user_is_evaluator ? 'Sí' : 'No'));

            if (!$user_is_evaluator) {
                continue;
            }

            $investigadores_ids = array_filter(explode(',', $project['investigadores']));
            $investigadores_nombres = $this->getUserNamesByIds($investigadores_ids);

            $evaluadores_nombres = $this->getUserNamesByIds($evaluadores_ids);

            if ($this->isProjectCalificado($project['id'])) {
                error_log("Proyecto '{$project['id']}' ya está calificado por el usuario.");
                continue;
            }

            if ($project['calificado'] >= count($evaluadores_ids)) {
                error_log("Proyecto '{$project['id']}' ya está completamente calificado.");
                continue;
            }

            $result[] = [
                'id' => $project['id'],
                'investigadores' => $investigadores_nombres,
                'docentes' => $project['docentes'],
                'evaluadores' => $evaluadores_nombres,
                'titulo' => $project['titulo'],
                'linea' => $project['linea'],
                'fase' => $project['fase'],
                'timer' => $project['timer'],
            ];
        }

        error_log("Proyectos finales: " . json_encode($result));
        return $result;
    }

    private function getUserNamesByIds($ids) {
        if (empty($ids)) {
            $error_message = "Lista de IDs de usuarios vacía.";
            error_log($error_message);
            return ['error' => $error_message];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT nombre_completo FROM usuarios WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            $error_message = "Error al preparar la consulta en getUserNamesByIds: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }

        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en getUserNamesByIds: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }

        $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
        error_log("Nombres de usuarios obtenidos: " . json_encode($names));
        return $names;
    }

    private function isProjectCalificado($projectId) {
        $query = "SELECT COUNT(*) FROM calificaciones WHERE idProject = ? AND assessor = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en isProjectCalificado: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }
        $stmt->bindValue(1, $projectId, PDO::PARAM_INT);
        $stmt->bindValue(2, $this->user, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en isProjectCalificado: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }
        $calificado = $stmt->fetchColumn() > 0;
        error_log("Proyecto ID '$projectId' calificado por usuario {$this->user}: " . ($calificado ? 'Sí' : 'No'));
        return $calificado;
    }

    public function exportProjectsToJson() {
        $projects = $this->getProjects();
        if (isset($projects['error'])) {
            echo "Error al obtener proyectos: " . $projects['error'];
            return;
        }
        $json_data = json_encode($projects, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents('proyectos.json', $json_data);
        echo "Archivo JSON creado exitosamente.";
    }

    public function validateUserExists() {
        $query = "SELECT id FROM usuarios WHERE nombre_completo = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $error_message = "Error al preparar la consulta en validateUserExists: " . implode(" ", $this->db->errorInfo());
            error_log($error_message);
            return ['error' => $error_message];
        }
        $stmt->bindValue(1, $this->user, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Error al ejecutar la consulta en validateUserExists: " . $errorInfo[2];
            error_log($error_message);
            return ['error' => $error_message];
        }
        $exists = $stmt->fetchColumn() !== false;
        error_log("Usuario {$this->user} existe: " . ($exists ? 'Sí' : 'No'));
        return $exists;
    }
}