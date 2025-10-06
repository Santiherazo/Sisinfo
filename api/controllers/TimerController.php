<?php
class TimerController extends BaseController {
    public function __construct($pdo, $session, $errorLogger) {
        parent::__construct($pdo, $session, $errorLogger);
    }

    public function handleRequest($action) {
        switch ($action) {
            case 'getTimeLeft':
                return $this->getTimeLeft();
            case 'syncTime':
                return $this->syncTime();
            case 'checkEvaluationStatus':
                return $this->checkEvaluationStatus();
            case 'startTimer':
                return $this->startTimer();
            default:
                ResponseHandler::sendError('Acción no válida', 404);
                break;
        }
    }

    public function getTimeLeft() {
        $this->errorLogger->log("TimerController: Ejecutando getTimeLeft", __FILE__, __LINE__);
        
        $token = $this->getToken();
        $cacheKey = "session_data_{$token}";
        
        $cachedData = $this->getCachedData($cacheKey);
        
        if ($cachedData === null) {
            $this->errorLogger->log("TimerController: No hay datos en caché, sincronizando", __FILE__, __LINE__);
            $this->syncTimeData($token);
            $cachedData = $this->getCachedData($cacheKey);
        } else {
            $this->errorLogger->log("TimerController: Actualizando tiempo en caché", __FILE__, __LINE__);
            $this->updateCachedTime($token, $cachedData);
            $cachedData = $this->getCachedData($cacheKey);
        }
        
        $this->errorLogger->log("TimerController: Tiempo restante: " . ($cachedData['time_left'] ?? 0), __FILE__, __LINE__);
        
        ResponseHandler::sendSuccess([
            'time_left' => $cachedData['time_left'] ?? 0,
            'time_elapsed' => $cachedData['time_elapsed'] ?? 0,
            'session_end' => $cachedData['session_end'] ?? null,
            'session_start' => $cachedData['session_start'] ?? null
        ]);
    }
    
    public function syncTime() {
        $this->errorLogger->log("TimerController: Ejecutando syncTime", __FILE__, __LINE__);
        
        $token = $this->getToken();
        $this->syncTimeData($token);
        
        ResponseHandler::sendSuccess([
            'synced' => true,
            'message' => 'Tiempo sincronizado correctamente'
        ]);
    }
    
    private function syncTimeData($token) {
        $this->errorLogger->log("TimerController: Sincronizando datos de tiempo", __FILE__, __LINE__);
        
        try {
            $now = new DateTime();
            $fin = new DateTime($this->session->{EVALUATION_SESSION_FIELD_END} ?? 'now');
            $start = new DateTime($this->session->{EVALUATION_SESSION_FIELD_START} ?? 'now');
            
            $timeLeft = max(0, $fin->getTimestamp() - $now->getTimestamp());
            $timeElapsed = max(0, $now->getTimestamp() - $start->getTimestamp());
            
            $sessionManager = new EvaluationSessionManager($this->pdo, $this->errorLogger);
            $sessionParticipants = $sessionManager->getSessionParticipants($this->session->{EVALUATION_SESSION_FIELD_ID}) ?? [];
            
            $sessionData = [
                'time_left' => $timeLeft,
                'time_elapsed' => $timeElapsed,
                'participants' => $sessionParticipants,
                'total_participants' => count($sessionParticipants),
                'session_end' => $this->session->{EVALUATION_SESSION_FIELD_END},
                'session_start' => $this->session->{EVALUATION_SESSION_FIELD_START},
                'last_updated' => time()
            ];
            
            $this->setCachedData("session_data_{$token}", $sessionData, 60);
            
            $this->errorLogger->log("TimerController: Datos sincronizados - Tiempo restante: $timeLeft, Participantes: " . count($sessionParticipants), __FILE__, __LINE__);
        } catch (Exception $e) {
            $this->errorLogger->log("TimerController: Error en syncTimeData - " . $e->getMessage(), __FILE__, __LINE__);
            throw $e;
        }
    }
    
    private function updateCachedTime($token, $cachedData) {
        $this->errorLogger->log("TimerController: Actualizando tiempo en caché", __FILE__, __LINE__);
        
        try {
            $now = new DateTime();
            $fin = new DateTime($this->session->{EVALUATION_SESSION_FIELD_END} ?? 'now');
            $start = new DateTime($this->session->{EVALUATION_SESSION_FIELD_START} ?? 'now');
            
            $realTimeLeft = max(0, $fin->getTimestamp() - $now->getTimestamp());
            $realTimeElapsed = max(0, $now->getTimestamp() - $start->getTimestamp());
            
            $timeLeft = $cachedData['time_left'];
            $timeElapsed = $cachedData['time_elapsed'];
            $lastUpdated = $cachedData['last_updated'];
            $elapsed = time() - $lastUpdated;
            
            $timeLeft = max(0, $timeLeft - $elapsed);
            $timeElapsed = $timeElapsed + $elapsed;
            
            if (abs($realTimeLeft - $timeLeft) > 5) {
                $this->errorLogger->log("TimerController: Desfase de tiempo detectado, recalculando", __FILE__, __LINE__);
                $timeLeft = $realTimeLeft;
                $timeElapsed = $realTimeElapsed;
            }
            
            $cachedData['time_left'] = $timeLeft;
            $cachedData['time_elapsed'] = $timeElapsed;
            $cachedData['last_updated'] = time();
            
            $this->setCachedData("session_data_{$token}", $cachedData, 30);
            
            $this->errorLogger->log("TimerController: Tiempo actualizado - Restante: $timeLeft, Transcurrido: $timeElapsed", __FILE__, __LINE__);
        } catch (Exception $e) {
            $this->errorLogger->log("TimerController: Error en updateCachedTime - " . $e->getMessage(), __FILE__, __LINE__);
            throw $e;
        }
    }
    
    public function checkEvaluationStatus() {
        $this->errorLogger->log("TimerController: Verificando estado de evaluación", __FILE__, __LINE__);
        
        try {
            $project_id = $this->getProjectId();
            
            $evaluationManager = new EvaluationManager($this->pdo);
            $allReviewers = $evaluationManager->getReviewersWithEvaluationStatus($project_id);
            $reviewersWhoEvaluated = $evaluationManager->getReviewersWhoHaveEvaluated($project_id);
            
            $totalReviewers = count($allReviewers);
            $evaluatedReviewers = count($reviewersWhoEvaluated);
            
            $shouldCloseSession = ($totalReviewers > 0 && $evaluatedReviewers >= $totalReviewers);
            
            if ($shouldCloseSession) {
                $this->errorLogger->log("TimerController: Cerrando sesión - Todos los evaluadores han completado", __FILE__, __LINE__);
                
                $sessionManager = new EvaluationSessionManager($this->pdo, $this->errorLogger);
                $sessionManager->closeSession($this->session->{EVALUATION_SESSION_FIELD_ID}, 'completada', true);
                
                $projectManager = new ProjectManager($this->pdo);
                $projectManager->setStatus($project_id, 'completado');
            }
            
            $this->errorLogger->log("TimerController: Estado de evaluación - Total: $totalReviewers, Evaluados: $evaluatedReviewers", __FILE__, __LINE__);
            
            ResponseHandler::sendSuccess([
                'total_reviewers' => $totalReviewers,
                'evaluated_reviewers' => $evaluatedReviewers,
                'should_close_session' => $shouldCloseSession
            ]);
        } catch (Exception $e) {
            $this->errorLogger->log("TimerController: Error en checkEvaluationStatus - " . $e->getMessage(), __FILE__, __LINE__);
            throw $e;
        }
    }
    
    public function startTimer() {
        $this->errorLogger->log("TimerController: Iniciando temporizador", __FILE__, __LINE__);
        
        try {
            ResponseHandler::sendSuccess(['message' => 'Temporizador iniciado']);
        } catch (Exception $e) {
            $this->errorLogger->log("TimerController: Error en startTimer - " . $e->getMessage(), __FILE__, __LINE__);
            throw $e;
        }
    }
    
    private function getCachedData($key) {
        $filePath = SESSION_DATA_DIR . md5($key) . '.json';
        if (!file_exists($filePath)) {
            $this->errorLogger->log("TimerController: No se encontró archivo de caché: $filePath", __FILE__, __LINE__);
            return null;
        }
        
        $data = json_decode(file_get_contents($filePath), true);
        if (!$data || !isset($data['expiration']) || time() > $data['expiration']) {
            $this->errorLogger->log("TimerController: Datos de caché expirados o inválidos", __FILE__, __LINE__);
            unlink($filePath);
            return null;
        }
        
        return $data['value'];
    }
    
    private function setCachedData($key, $value, $duration = 300) {
        $filePath = SESSION_DATA_DIR . md5($key) . '.json';
        $data = [
            'value' => $value,
            'expiration' => time() + $duration,
            'created' => time()
        ];
        
        $result = file_put_contents($filePath, json_encode($data)) !== false;
        
        if ($result) {
            $this->errorLogger->log("TimerController: Datos guardados en caché: $filePath", __FILE__, __LINE__);
        } else {
            $this->errorLogger->log("TimerController: Error al guardar en caché: $filePath", __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    protected function getToken() {
        return $this->session->token_acceso;
    }
    
    protected function getProjectId() {
        return $this->session->project_id;
    }
}