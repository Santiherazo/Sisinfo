<?php

class Handler {
    private $auth;

    public function __construct(Auth $auth) {
        $this->auth = $auth;
    }

    public function handleLogin() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        } else {
            $this->renderLogin();
        }
    }

    public function studentAuth() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = isset($_POST['idUser']) ? sanitizeInput($_POST['idUser']) : '';
            $response = [];

            $data = $this->auth->student($identifier);

            if ($data['success'] === 'true') {
                $response['success'] = $data['success'];
                $response['redirect'] = 'dashboard';
                $response['message'] = $data['message'];
                $response['data'] = [
                    'id' => $_SESSION['user_id'],
                    'rol' => $_SESSION['user_role']
                ];
            } else {
                $response['error'] = $data['success'];
                $response['message'] = $data['message'];
            }
    
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    public function adminAuth() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = isset($_POST['idUser']) ? sanitizeInput($_POST['idUser']) : '';
            $password = isset($_POST['password']) ? sanitizeInput($_POST['password']) : '';
            $response = [];

            $data = $this->auth->admin($identifier,$password);

           if ($data['success'] === 'true') {
                $response['success'] = $data['success'];
                $response['redirect'] = 'dashboard';
                $response['message'] = $data['message'];
                $response['data'] = [
                    'id' => $_SESSION['user_id'],
                    'rol' => $_SESSION['user_role']
                ];
            } else {
                $response['error'] = $data['success'];
                $response['message'] = $data['message'];
            }
    
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    private function renderLogin($error_message = '') {
        include 'views/login.php';
    }

    public function handleDashboard() {
        if (!$this->auth->isLoggedIn()) {
            header('Location: .');
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
        header("Location: .");
        exit;
    }

    public function getResults() {
        try {
            require_once '../classes/class.Report.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>