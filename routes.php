<?php
// Definimos las rutas y sus controladores utilizando define()
define('ROUTE_HOME', 'HomeController@index');
define('ROUTE_ABOUT', 'AboutController@index');
define('ROUTE_DASHBOARD', 'DashboardController@index');
define('ROUTE_LOGIN', 'AuthController@login');
define('ROUTE_LOGOUT', 'AuthController@logout');

// Definimos las rutas públicas y privadas
$publicRoutes = [
    '/' => ROUTE_HOME,
    '/about' => ROUTE_ABOUT,
    '/login' => ROUTE_LOGIN,
    '/dashboard' => ROUTE_DASHBOARD
];

$privateRoutes = [
    '/logout' => ROUTE_LOGOUT
];

// Exportamos las rutas públicas y privadas
return [
    'public' => $publicRoutes,
    'private' => $privateRoutes
];
?>
