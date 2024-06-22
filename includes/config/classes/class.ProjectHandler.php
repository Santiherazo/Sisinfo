<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProjectHandler {
    private $db;
    private $user;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }

    public function getProjects() {
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
            error_log("Error al preparar la consulta: " . implode(" ", $this->db->errorInfo()));
            return [];
        }

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            error_log("Error al ejecutar la consulta: " . $errorInfo[2]);
            return [];
        }

        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Resultados de la consulta: " . json_encode($projects));

        if (!$projects) {
            error_log("No se encontraron proyectos.");
            return [];
        }

        $result = [];
        foreach ($projects as $project) {
            error_log("Procesando proyecto ID: " . $project['id']);
            $investigadores_ids = array_filter(explode(',', $project['investigadores']));
            $investigadores_nombres = $this->getUserNamesByIds($investigadores_ids);

            $evaluadores_ids = array_filter(explode(',', $project['evaluador']));
            error_log("Evaluadores IDs: " . json_encode($evaluadores_ids));
            $evaluadores_nombres = $this->getUserNamesByIds($evaluadores_ids);

            $user_is_evaluator = in_array($this->user, $evaluadores_ids);
            error_log("Usuario actual es evaluador: " . ($user_is_evaluator ? 'Sí' : 'No'));
}}}