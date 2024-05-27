<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$allowed_origins = ['http://sisinfo.local', 'https://sisinfo.local'];
if (isset($_SERVER['HTTP_ORIGIN']) && !in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    http_response_code(403);
    echo json_encode(['error' => 'Origen no permitido']);
    exit();
}

header('Content-Type: application/json');

if(!@include_once('../../includes/classes/class.database.php')) throw new Exception('Opps, estamos presentando inconvenientes');

try {
    $stmt = $conexion->query("SELECT * FROM proyectos ORDER BY id DESC");
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($proyectos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
