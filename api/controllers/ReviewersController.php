<?php
class ReviewersController extends BaseController {
    public function __construct($pdo, $session, $errorLogger) {
        parent::__construct($pdo, $session, $errorLogger);
    }

    public function getParticipantsCount() {
        $this->errorLogger->log("ReviewersController: Obteniendo conteo de participantes", __FILE__, __LINE__);
        
        try {
            $token = $this->getToken();
            $cacheKey = "session_data_{$token}";
            
            $sessionManager = new EvaluationSessionManager($this->pdo, $this->errorLogger);
            $sessionId = $this->session->{EVALUATION_SESSION_FIELD_ID} ?? 0;
            $currentParticipants = $sessionManager->getSessionParticipants($sessionId) ?? [];
            
            $cachedData = FileCache::get($cacheKey);
            $totalParticipants = $cachedData['total_participants'] ?? count($currentParticipants);
            
            $sessionData = [
                'participants' => $currentParticipants,
                'total_participants' => $totalParticipants,
                'last_updated' => time()
            ];
            
            FileCache::set($cacheKey, $sessionData, 30);
            
            $this->errorLogger->log("ReviewersController: Conteo obtenido - Actuales: " . count($currentParticipants) . ", Total: $totalParticipants", __FILE__, __LINE__);
            
            ResponseHandler::sendSuccess([
                'count' => count($currentParticipants), 
                'total' => $totalParticipants,
                'participants' => $currentParticipants,
                'session_id' => $sessionId
            ]);
            
        } catch (Exception $e) {
            $this->errorLogger->log("ReviewersController: Error en getParticipantsCount - " . $e->getMessage(), __FILE__, __LINE__);
            ResponseHandler::sendError('Error al obtener el conteo de participantes', 500);
        }
    }
    
    public function getReviewersStatus() {
        $this->errorLogger->log("ReviewersController: Obteniendo estado de evaluadores", __FILE__, __LINE__);
        
        try {
            $project_id = $this->getProjectId();
            
            if (empty($project_id)) {
                $this->errorLogger->log("ReviewersController: Project ID no proporcionado", __FILE__, __LINE__);
                ResponseHandler::sendError('Project ID no proporcionado', 400);
            }
            
            $evaluationManager = new EvaluationManager($this->pdo);
            $allReviewers = $evaluationManager->getReviewersWithEvaluationStatus($project_id);
            $reviewersWhoEvaluated = $evaluationManager->getReviewersWhoHaveEvaluated($project_id);
            
            $totalReviewers = count($allReviewers);
            $evaluatedReviewers = count($reviewersWhoEvaluated);
            
            $this->errorLogger->log("ReviewersController: Estado obtenido - Total: $totalReviewers, Evaluados: $evaluatedReviewers", __FILE__, __LINE__);
            
            ResponseHandler::sendSuccess([
                'total_reviewers' => $totalReviewers,
                'evaluated_reviewers' => $evaluatedReviewers,
                'reviewers' => $allReviewers,
                'evaluated' => $reviewersWhoEvaluated,
                'completion_percentage' => $totalReviewers > 0 ? round(($evaluatedReviewers / $totalReviewers) * 100, 2) : 0,
                'project_id' => $project_id
            ]);
            
        } catch (Exception $e) {
            $this->errorLogger->log("ReviewersController: Error en getReviewersStatus - " . $e->getMessage(), __FILE__, __LINE__);
            ResponseHandler::sendError('Error al obtener el estado de evaluadores', 500);
        }
    }
    
    public function getReviewerDetails() {
        $this->errorLogger->log("ReviewersController: Obteniendo detalles de evaluador", __FILE__, __LINE__);
        
        try {
            $reviewer_id = $_GET['reviewer_id'] ?? $_POST['reviewer_id'] ?? 0;
            $project_id = $this->getProjectId();
            
            if (empty($reviewer_id) || empty($project_id)) {
                $this->errorLogger->log("ReviewersController: Parámetros incompletos", __FILE__, __LINE__);
                ResponseHandler::sendError('Parámetros incompletos', 400);
            }
            
            $evaluationManager = new EvaluationManager($this->pdo);
            $reviewerDetails = $evaluationManager->getReviewerDetails($project_id, $reviewer_id);
            
            if (!$reviewerDetails) {
                $this->errorLogger->log("ReviewersController: Evaluador no encontrado", __FILE__, __LINE__);
                ResponseHandler::sendError('Evaluador no encontrado', 404);
            }
            
            $this->errorLogger->log("ReviewersController: Detalles obtenidos para evaluador: $reviewer_id", __FILE__, __LINE__);
            
            ResponseHandler::sendSuccess([
                'reviewer' => $reviewerDetails,
                'has_evaluated' => $evaluationManager->hasReviewerEvaluated($project_id, $reviewer_id)
            ]);
            
        } catch (Exception $e) {
            $this->errorLogger->log("ReviewersController: Error en getReviewerDetails - " . $e->getMessage(), __FILE__, __LINE__);
            ResponseHandler::sendError('Error al obtener detalles del evaluador', 500);
        }
    }

    protected function getToken() {
        return $this->session->token_acceso ?? '';
    }
    
    protected function getProjectId() {
        return $this->session->project_id ?? 0;
    }

    public function handleRequest($action) {
        switch ($action) {
            case 'getParticipantsCount':
                $this->getParticipantsCount();
                break;
            case 'getReviewersStatus':
                $this->getReviewersStatus();
                break;
            case 'getReviewerDetails':
                $this->getReviewerDetails();
                break;
            default:
                ResponseHandler::sendError("Acción no reconocida: $action", 400);
                break;
        }
    }
}
