<?php
if (!defined('access') || access !== 'install') exit;

if (session_status() === PHP_SESSION_NONE) {
    session_name('SisinfoInstaller126');
    session_cache_limiter('nocache');
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
        'use_only_cookies' => true
    ]);
}

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

define('HTTP_HOST', htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, 'UTF-8'));
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') ? 'https://' : 'http://');
define('__ROOT_DIR__', rtrim(str_replace('\\', '/', dirname(dirname(__FILE__))), '/') . '/');
define('__RELATIVE_ROOT__', str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__));
define('__BASE_URL__', SERVER_PROTOCOL . HTTP_HOST . __RELATIVE_ROOT__);
define('__PATH_INCLUDES__', __ROOT_DIR__ . 'includes/');
define('__PATH_CLASSES__', __PATH_INCLUDES__ . 'classes/');
define('__PATH_CONFIGS__', __PATH_INCLUDES__ . 'config/');
define('__INSTALL_ROOT__', __ROOT_DIR__ . 'install/');
define('__INSTALL_URL__', __BASE_URL__ . 'install/');

try {
    $requiredIncludes = [
        __PATH_CONFIGS__ . 'webengine.tables.php',
        __INSTALL_ROOT__ . 'definitions.php'
    ];

    foreach ($requiredIncludes as $file) {
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception('Falta o no se puede acceder al archivo requerido: ' . basename($file));
        }
        include_once $file;
    }

    if (!defined('WEBENGINE_CONFIGURATION_FILE') || !defined('WEBENGINE_DEFAULT_CONFIGURATION_FILE') || !defined('WEBENGINE_WRITABLE_PATHS_FILE')) {
        throw new Exception('Faltan constantes requeridas en definitions.php.');
    }

    $configFile = __PATH_CONFIGS__ . WEBENGINE_CONFIGURATION_FILE;
    if (!file_exists($configFile) || !is_readable($configFile)) {
        throw new Exception('Archivo de configuración principal no accesible.');
    }
    if (!is_writable($configFile)) {
        throw new Exception('No se puede escribir en el archivo de configuración principal.');
    }

    $configData = json_decode(file_get_contents($configFile), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($configData)) {
        throw new Exception('Estructura del archivo de configuración principal inválida.');
    }
    if (!empty($configData['webengine_cms_installed'])) {
        throw new Exception('SISINFO CMS ya está instalado.');
    }

    $defaultConfigPath = __PATH_CONFIGS__ . WEBENGINE_DEFAULT_CONFIGURATION_FILE;
    if (!file_exists($defaultConfigPath) || !is_readable($defaultConfigPath)) {
        throw new Exception('Archivo de configuración predeterminada no accesible.');
    }

    $defaultConfigData = json_decode(file_get_contents($defaultConfigPath), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($defaultConfigData)) {
        throw new Exception('Archivo de configuración predeterminada con formato inválido.');
    }

    $requiredFiles = [
        __PATH_INCLUDES__ . 'functions.php',
        __PATH_CLASSES__ . 'class.validator.php',
        __PATH_CLASSES__ . 'class.database.php',
        __PATH_CLASSES__ . 'class.connection.php',
    ];

    foreach ($requiredFiles as $file) {
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception('No se pudo cargar: ' . basename($file));
        }
        include_once $file;
    }

    $writablePaths = loadJsonFile(__PATH_CONFIGS__ . WEBENGINE_WRITABLE_PATHS_FILE);
    if (!is_array($writablePaths)) {
        throw new Exception('Lista de rutas escribibles inválida.');
    }

    if (!isset($_SESSION['install_cstep'])) {
        $_SESSION['install_cstep'] = 0;
    }

    function stepListSidebar() {
        global $install;
        if (is_array($install['step_list'])) {
            echo '<div class="space-y-2">';
            foreach ($install['step_list'] as $key => $row) {
                $isActive = isset($_SESSION['install_cstep']) && $key === $_SESSION['install_cstep'];
                $classes = $isActive 
                    ? 'flex items-center gap-3 px-4 py-2 bg-blue-600 text-white rounded-lg shadow'
                    : 'flex items-center gap-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition';
                echo '<div class="' . $classes . '">';
                echo '<span class="w-6 h-6 flex items-center justify-center rounded-full font-bold ' . ($isActive ? 'bg-white text-blue-600' : 'bg-blue-200 text-blue-800') . '">' . ($key + 1) . '</span>';
                echo '<span class="text-sm font-medium">' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . '</span>';
                echo '</div>';
            }
            echo '</div>';
        }

        if (isset($_SESSION['install_cstep']) && $_SESSION['install_cstep'] > 0) {
            echo '<div class="mt-4">';
            echo '<a href="?action=restart" class="inline-block px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">Reiniciar instalación</a>';
            echo '</div>';
        }
    }

    if (isset($_GET['action']) && in_array($_GET['action'], ['restart'], true)) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Strict'
            ]);
        }
        session_destroy();
        header('Location: install.php');
        exit;
    }

} catch (Throwable $ex) {
    if (!function_exists('logPhpError')) {
        function logPhpError($msg) {
            error_log($msg);
        }
    }
    if (!function_exists('handleCriticalError')) {
        function handleCriticalError() {
            http_response_code(500);
            exit('Error crítico del sistema.');
        }
    }
    logPhpError('[Loader Error] ' . $ex->getMessage());
    handleCriticalError();
}