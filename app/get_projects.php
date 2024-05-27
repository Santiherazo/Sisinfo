<?php
require 'config.php';

$stmt = $pdo->query('SELECT * FROM proyectos');
$projects = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($projects);
?>
