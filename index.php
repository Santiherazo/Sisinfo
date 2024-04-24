<?php
session_start();
require_once 'router.php';
$routes = require_once 'routes.php';

$router = new Router();

// Definimos las rutas públicas y privadas
foreach ($routes['public'] as $route => $handler) {
    $router->get($route, $handler);
}

$router->setPrivateRoutes($routes['private']);

$router->setViewHandler(function ($viewName) {
    include 'views/' . $viewName . '.php';
});

$router->dispatch();
?>
