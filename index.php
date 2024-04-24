<?php
session_start();
require_once 'router.php';
$routes = require_once 'routes.php';

// Establecemos la ruta base
$basePath = '/Sisinfo/';

$router = new Router($basePath);

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
