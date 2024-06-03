<?php
session_start();

function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Autoload de clases
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
    $auth = new Auth($db);
    $rubric = new Rubric($db);
    $report = new Report($db);
    $handler = new Handler($auth, $db);
    $projectHandler = new ProjectHandler($db);

    $page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'login';

    switch ($page) {
        case 'login':
            $handler->handleLogin();
            break;
        case 'logout':
            $handler->logout();
            break;
        case 'dashboard':
            $handler->handleDashboard();
            break;
        case 'projects':
            echo json_encode($projectHandler->getProjects());
            break;
        case 'send':
            $rubric->sendRubric();
            break;
        case 'results':
            echo json_encode($report->getResults());
            break;
        default:
            echo json_encode(["error" => "Página no encontrada."]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>