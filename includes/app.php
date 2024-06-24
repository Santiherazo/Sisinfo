<?php
session_start();

function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/config/classes/class.' . $class_name . '.php';
    if (file_exists($file)) {
        include $file;
    } else {
        throw new Exception("Class file for {$class_name} not found.");
    }
});

try {
    $db = new Database();
    $user = $_SESSION['user'];
    $auth = new Auth($db);
    $rubric = new Rubric($db, $user);
    $handler = new Handler($auth);
    $report = new Report($db, $user);
    $projectHandler = new ProjectHandler($db, $user);

    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];

    $route = str_replace(dirname($scriptName), '', $requestUri);
    $route = sanitizeInput(trim($route, '/'));

    $parts = explode('/', $route);
    switch ($parts[0]) {
        case '':
            $handler->handleLogin();
            break;
        case 'endpoint':
            $subroute = isset($parts[1]) ? $parts[1] : '';
            switch ($subroute) {
                case 'studentAuth':
                    $handler->studentAuth();
                    break;
                case 'adminAuth':
                    $handler->adminAuth();
                    break;
                case 'projects':
                    echo json_encode($projectHandler->getProjects());
                    break;
                case 'rubric':
                    $rubric->sendRubric();
                    break;
                case 'results':
                    echo json_encode($report->getResults());
                    break;
                default:
                    echo json_encode(["error" => "Subruta no encontrada."]);
                    break;
            }
            break;
        case 'logout':
            $handler->logout();
            break;
        case 'dashboard':
            $handler->handleDashboard();
            break;
            echo json_encode(["error" => "Página no encontrada."]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>