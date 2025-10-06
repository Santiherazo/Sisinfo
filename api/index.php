<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

define('access', 'api');

$requiredFiles = [
    '../includes/webengine.php',
    'utils/ResponseHandler.php',
    'utils/AuthMiddleware.php',
    'controllers/BaseController.php',
    'controllers/TimerController.php',
    'controllers/ReviewersController.php',
    'controllers/EvaluationsController.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Archivo de configuración no encontrado: ' . $file]);
        exit();
    }
    require_once $file;
}

try {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = '';
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = trim($matches[1]);
    }
    
    if (empty($token)) {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
    }
    
    if (empty($token)) {
        ResponseHandler::sendError('Token no proporcionado', 401);
    }

    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 
              filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?? '';
              
    $module = filter_input(INPUT_GET, 'module', FILTER_SANITIZE_STRING) ?? 
              filter_input(INPUT_POST, 'module', FILTER_SANITIZE_STRING) ?? '';

    $errorLogger = new ErrorLogger();
    
    $db = Connection::Database('sisinfo');
    $pdo = $db->getConnection();
    
    $sessionManager = new EvaluationSessionManager($pdo, $errorLogger);
    $sessionData = $sessionManager->getByToken($token);
    
    if (!$sessionData) {
        ResponseHandler::sendError('Token inválido o sesión expirada', 401);
    }
    
    $session = (object) $sessionData;

    $controllers = [
        'timer' => 'TimerController',
        'reviewers' => 'ReviewersController', 
        'evaluations' => 'EvaluationsController'
    ];

    if (!isset($controllers[$module])) {
        ResponseHandler::sendError('Módulo no válido', 404);
    }

    $controllerClass = $controllers[$module];
    $controller = new $controllerClass($pdo, $session, $errorLogger);
    
    if (!method_exists($controller, $action)) {
        ResponseHandler::sendError('Acción no válida', 404);
    }

    $controller->$action();
    
} catch (Throwable $e) {
    $errorLogger = new ErrorLogger();
    $errorLogger->log("EXCEPCIÓN: " . $e->getMessage(), $e->getFile(), $e->getLine());
    ResponseHandler::sendError('Error interno del servidor', 500);
}