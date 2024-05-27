<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require 'config.php';
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        $stmt = $pdo->prepare('INSERT INTO calificaciones (proyecto_id, evaluador, titulo_proyecto, planteamiento_problema, justificacion, objetivos, metodologia, resultados_iniciales, sustentacion, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        
        $success = $stmt->execute([
            $data['proyecto_id'],
            $data['evaluador'],
            $data['titulo_proyecto'],
            $data['planteamiento_problema'],
            $data['justificacion'],
            $data['objetivos'],
            $data['metodologia'],
            $data['resultados_iniciales'],
            $data['sustentacion'],
            $data['total']
        ]);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar los datos en la base de datos']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
}
?>
