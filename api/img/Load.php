<?php
// Ruta de la carpeta de imágenes
$folder = "PATH_UPLOAD__GALLERY";

// Función para obtener la lista de imágenes
function getImages($folder) {
    $images = [];
    // Escanea la carpeta y agrega cada imagen encontrada al arreglo $images
    $files = scandir($folder);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($folder . '/' . $file)) {
            $images[] = [
                'name' => $file,
                'url' => "$folder/$file"
            ];
        }
    }
    return $images;
}

// Manejar solicitud GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener la lista de imágenes
    $images = getImages($folder);
    // Devolver la lista de imágenes como JSON
    header('Content-Type: application/json');
    echo json_encode($images);
} else {
    // Si la solicitud no es GET, devolver un error
    http_response_code(405);
    echo json_encode(array('message' => 'Method Not Allowed'));
}
?>
