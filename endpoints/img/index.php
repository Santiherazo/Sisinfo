<?php
$folder = '../../uploads/gallery';

function getImages($folder) {
    $images = [];
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $images = getImages($folder);
    echo json_encode($images);
} else {
    http_response_code(405);
    echo json_encode(array('message' => 'Method Not Allowed'));
}
?>
