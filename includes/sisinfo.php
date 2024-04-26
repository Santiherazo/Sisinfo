<?php

# Set Encoding
@ini_set('default_charset', 'utf-8');

# CloudFlare IP Workaround
if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

# CloudFlare HTTPS Workaround
if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
	$_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'on' : 'off';
}

# Global Paths
define('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
define('__ROOT_DIR__', str_replace('\\','/',dirname(dirname(__FILE__))).'/'); // /home/user/public_html/
define('__RELATIVE_ROOT__', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__) : '/');// /
define('__BASE_URL__', SERVER_PROTOCOL.HTTP_HOST.__RELATIVE_ROOT__); // http(s)://www.mysite.com/

# Private Paths
define('__PATH_INCLUDES__', __ROOT_DIR__.'includes/');
define('__PATH_LANGUAGES__', __PATH_INCLUDES__ . 'languages/');
define('__PATH_CLASSES__', __PATH_INCLUDES__.'classes/');
define('__PATH_FUNCTIONS__', __PATH_INCLUDES__.'functions/');
define('__PATH_MODULES__', __ROOT_DIR__.'modules/');

# Public Paths
define('__PATH_ADMINCP_HOME__', __BASE_URL__.'admincp/');
define('__PATH_IMG__', __BASE_URL__.'img/');
define('__PATH_UPLOAD__PROFILE__',__PATH_IMG__.'uploads/profile');
define('__PATH_UPLOAD__COVER__',__PATH_IMG__.'uploads/cover');
define('__PATH_API__', __BASE_URL__.'api/');

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




