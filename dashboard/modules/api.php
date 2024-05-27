<?php
$filename = '../../includes/config/navbar.json';

function readData() {
    global $filename;
    return json_decode(file_get_contents($filename), true);
}

function writeData($data) {
    global $filename;
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

$data = readData();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        echo json_encode($data[$index]);
    } else {
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $data[] = $input;
    writeData($data);
    echo json_encode($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        $input = json_decode(file_get_contents('php://input'), true);
        $data[$index] = $input;
        writeData($data);
        echo json_encode($input);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        array_splice($data, $index, 1);
        writeData($data);
        echo json_encode(['status' => 'success']);
    }
}
?>
