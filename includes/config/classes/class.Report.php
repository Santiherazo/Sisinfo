<?php

class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getResults($type = 'all') {
        switch ($type) {
            case 'all':
                return $this->getAllCalificationsWithProjects();
            default:
                return json_encode(['error' => 'Tipo de consulta no válido.']);
        }
    }

    private function getAllCalificationsWithProjects() {
        try {
            $query = "
                SELECT p.id AS proyecto_id, p.investigadores, p.titulo AS proyecto_titulo, p.calificacion AS proyecto_calificacion, 
                       c.id AS calificacion_id, c.titulo_proyecto, c.planteamiento_problema, c.justificacion, c.objetivos, 
                       c.metodologia, c.resultados_iniciales, c.sustentacion, c.comentarios, c.total AS calificacion_total
                FROM proyectos p
                JOIN calificaciones c ON p.id = c.proyecto_id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Habilita el informe de errores y captura los errores en una variable
error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start(); // Inicia el almacenamiento en búfer de salida

$formattedResults = array_reduce($results, function($acc, $result) {
    $titulo = $result['proyecto_titulo'];
    $calificacion = floatval($result['proyecto_calificacion']);

    $item = array(
        "titulo" => $titulo,
        "estudiantes" => array_map('trim', explode(',', $result['investigadores'])),
        "calificacion" => $calificacion,
        "resto_de_calificaciones" => array(
            array(
                "criterio" => "Planteamiento del problema",
                "calificacion" => floatval($result['planteamiento_problema']),
                "comentario" => $result['comentarios']
            ),
            array(
                "criterio" => "Justificación",
                "calificacion" => floatval($result['justificacion']),
                "comentario" => $result['comentarios']
            ),
            array(
                "criterio" => "Objetivos",
                "calificacion" => floatval($result['objetivos']),
                "comentario" => $result['comentarios']
            ),
            array(
                "criterio" => "Metodología",
                "calificacion" => floatval($result['metodologia']),
                "comentario" => $result['comentarios']
            ),
            array(
                "criterio" => "Resultados iniciales",
                "calificacion" => floatval($result['resultados_iniciales']),
                "comentario" => $result['comentarios']
            ),
            array(
                "criterio" => "Sustentación",
                "calificacion" => floatval($result['sustentacion']),
                "comentario" => $result['comentarios']
            )
        )
    );

    $acc['items'][] = $item;
    $acc['titulos'][] = $titulo;
    $acc['calificaciones'][] = $calificacion;

    return $acc;
}, ['items' => [], 'titulos' => [], 'calificaciones' => []]);

// Captura cualquier salida no deseada
$output = ob_get_clean();

// Configura el encabezado para la respuesta JSON
header('Content-Type: application/json');

if (!empty($output)) {
    echo json_encode(['error' => 'Unexpected output: ' . $output]);
    exit;
}

$json_response = json_encode($formattedResults);
if ($json_response === false) {
    $error_message = json_last_error_msg();
    echo json_encode(['error' => 'Error al codificar JSON: ' . $error_message]);
} else {
    echo $json_response;
}
            

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode(['error' => "Error al obtener los resultados y proyectos: " . $e->getMessage()]);
        }
    }
}
?>