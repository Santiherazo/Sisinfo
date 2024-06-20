<?php
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
                GROUP_CONCAT(DISTINCT i.nombre_completo SEPARATOR ', ') AS investigadores_nombres,
                GROUP_CONCAT(DISTINCT e.nombre_completo SEPARATOR ', ') AS evaluadores_nombres
            FROM 
                proyectos p
            LEFT JOIN 
                usuarios i ON FIND_IN_SET(i.id, p.investigadores)
            LEFT JOIN 
                usuarios e ON FIND_IN_SET(e.id, p.evaluador)
            WHERE 
                e.nombre_completo = :user
                AND p.calificado < 2
            GROUP BY 
                p.id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user', $this->user); // Corregido para utilizar $this->user
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($projects as $project) {
            $result[] = [
                'id' => $project['id'],
                'investigadores' => explode(', ', $project['investigadores_nombres']),
                'docentes' => explode(', ', $project['evaluadores_nombres']),
                'titulo' => $project['titulo'],
                'linea' => $project['linea'],
                'fase' => $project['fase'],
                'timer' => $project['timer']
            ];
        }

        return $result;
    }

    public function exportProjectsToJson() {
        $projects = $this->getProjects();

        $data = [
            'success' => true,
            'proyectos' => $projects
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents('proyectos.json', $json_data);

        echo "Archivo JSON creado exitosamente.";
    }
}
?>