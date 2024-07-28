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
    $crud = new crud($db, $user);
    $projectHandler = new ProjectHandler($db, $user);

    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);
    if (strpos($requestUri, $scriptName) === 0) {
        $route = substr($requestUri, strlen($scriptName));
    } elseif (strpos($requestUri, $scriptDir) === 0) {
        $route = substr($requestUri, strlen($scriptDir));
    } else {
        $route = $requestUri;
    }

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
                case 'users': 
                    echo json_encode($crud->fetchUsers());
                    break;
                case 'addUser': 
                    $crud->createUser();
                    break;
                case 'editUser':
                    $crud->updateUser();
                    break; 
                case 'deleteUser':
                    $crud->deleteUser();
                    break;
                case 'adminProjects': 
                    echo json_encode($crud->fetchProjects());
                    break;
                case 'addProjects': 
                    $crud->createProject();
                    break;
                case 'editProjects': 
                    $crud->updateProject();
                    break;  
                case 'deleteProjects': 
                    $crud->deleteProject();
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
        default:
            echo json_encode(["error" => "Página no encontrada."]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>