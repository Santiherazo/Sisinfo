<?php
class ProjectHandler {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProjects() {
        $query = "
            SELECT p.*, u.nombre_completo AS nombre_investigador
            FROM proyectos p
            JOIN usuarios u ON p.investigadores = u.id
            WHERE p.calificado = 0
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $projects;
    }
}
?>