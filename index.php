<?php
session_start();
require_once 'router.php'; // Asegúrate de que la ruta del archivo sea correcta
$routes = require_once 'routes.php'; // Asegúrate de que la ruta del archivo sea correcta


$router = new Router();

// Definimos las rutas públicas
foreach ($routes['public'] as $route => $handler) {
    $router->get($route, $handler);
}

// Definimos las rutas privadas
$router->setPrivateRoutes($routes['private']);

// Asignamos el manejador de vistas
$router->setViewHandler(function ($viewName) {
    include 'views/' . $viewName . '.php';
});

// Manejar la solicitud
$router->dispatch();
?>
