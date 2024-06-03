<?php

class Handler {
    private $auth;

    public function __construct(Auth $auth) {
        $this->auth = $auth;
    }

    public function handleLogin() {
        if ($this->auth->isLoggedIn()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $documento_identidad = isset($_POST['documento_identidad']) ? sanitizeInput($_POST['documento_identidad']) : '';
            $contrasena = isset($_POST['contrasena']) ? sanitizeInput($_POST['contrasena']) : '';

            if ($this->auth->login($documento_identidad, $contrasena)) {
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $this->renderLogin('Documento de identidad o contraseña incorrectos');
            }
        } else {
            $this->renderLogin();
        }
    }

    private function renderLogin($error_message = '') {
        include 'views/login.php';
    }

    public function handleDashboard() {
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $role = $this->auth->getUserRole();

        switch ($role) {
            case 'Estudiante':
                $this->renderDashboard('estudiante.php');
                break;
            case 'Administrador':
                $this->renderDashboard('administrador.php');
                break;
            case 'Coordinador':
                $this->renderDashboard('coordinador.php');
                break;
            case 'Evaluador':
                $this->renderDashboard('evaluador.php');
                break;
            default:
                echo "Rol no reconocido.";
                break;
        }
    }

    private function renderDashboard($dashboard) {
        include 'modules/' . $dashboard;
    }

    public function handleGetProjects() {
        try {
            require_once '../classes/class.Project.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function sendRubric() {
        try {
            require_once '../classes/class.Rubric.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }

    public function getResults(){
        try {
            require_once '../classes/class.Report.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}


?>
