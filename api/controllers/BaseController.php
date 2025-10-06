<?php
abstract class BaseController {
    protected $pdo;
    protected $session;
    protected $errorLogger;
    
    public function __construct($pdo, $session, $errorLogger) {
        $this->pdo = $pdo;
        $this->session = $session;
        $this->errorLogger = $errorLogger;
    }
    
    abstract public function handleRequest($action);
    
    protected function getProjectId() {
        $project_id = $_GET['project_id'] ?? $_POST['project_id'] ?? 0;
        
        if (empty($project_id)) {
            ResponseHandler::sendError('Project ID no proporcionado', 400);
        }
        
        return $project_id;
    }
    
    protected function getToken() {
        return $_GET['token'] ?? $_POST['token'] ?? '';
    }
}
?>