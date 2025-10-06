<?php
class EvaluationsController extends BaseController {
    public function handleRequest($action) {
        switch ($action) {
            case 'save_evaluation':
                $this->saveEvaluation();
                break;
            case 'get_evaluation':
                $this->getEvaluation();
                break;
            default:
                ResponseHandler::sendError('Acción no válida para el módulo evaluations', 404);
                break;
        }
    }
    
    private function saveEvaluation() {
        $evaluationData = json_decode(file_get_contents('php://input'), true);
        
        if (!$evaluationData) {
            $evaluationData = $_POST;
        }
        
        if (!$evaluationData) {
            ResponseHandler::sendError('Datos de evaluación no válidos', 400);
        }
        
        $evaluationManager = new EvaluationManager($this->pdo);
        $result = $evaluationManager->saveEvaluation($evaluationData, $this->session['id'] ?? 0);
        
        if ($result) {
            ResponseHandler::sendSuccess(['message' => 'Evaluación guardada correctamente']);
        } else {
            ResponseHandler::sendError('Error al guardar la evaluación', 500);
        }
    }
    
    private function getEvaluation() {
        $evaluation_id = $_GET['evaluation_id'] ?? $_POST['evaluation_id'] ?? 0;
        
        if (empty($evaluation_id)) {
            ResponseHandler::sendError('Evaluation ID no proporcionado', 400);
        }
        
        $evaluationManager = new EvaluationManager($this->pdo);
        $evaluation = $evaluationManager->getEvaluation($evaluation_id);
        
        if ($evaluation) {
            ResponseHandler::sendSuccess($evaluation);
        } else {
            ResponseHandler::sendError('Evaluación no encontrada', 404);
        }
    }
}
?>