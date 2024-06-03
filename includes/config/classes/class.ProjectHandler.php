<?php
class ProjectHandler {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProjects() {
        $query = "SELECT * FROM proyectos WHERE calificado = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $projects;
    }
}
?>