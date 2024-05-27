<?php
class EndpointController {
    public static function handle($module) {
        if (!self::isAjaxRequest()) {
            self::sendResponse(['success' => false, 'message' => 'Access denied']);
            return;
        }

        session_start();
        if (!self::isValidCsrfToken()) {
            self::sendResponse(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $module = self::sanitizeModuleName($module);

        $endpoint_folders = ['login', 'register', 'img', 'news', 'info'];
        $module_found = false;

        foreach ($endpoint_folders as $folder) {
            $module_path = __DIR__ . '/' . $folder . '/' . $module . '.php';

            if (file_exists($module_path)) {
                require_once $module_path;
                $module_found = true;
                break;
            }
        }

        if (!$module_found) {
            self::sendResponse(['success' => false, 'message' => 'Access denied']);
        }
    }

    private static function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private static function isValidCsrfToken() {
        if (!isset($_POST['csrf_token'])) {
            return false;
        }
        $csrf_token = $_POST['csrf_token'];
        return hash_equals($_SESSION['csrf_token'], $csrf_token);
    }

    private static function sanitizeModuleName($module) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $module);
    }

    private static function sendResponse($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

$module = $_GET['module'] ?? '';
EndpointController::handle($module);
