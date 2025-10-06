<?php
define('SESSION_DATA_DIR', __PATH_CACHE__ . 'session_data/');
if (!file_exists(SESSION_DATA_DIR)) {
    mkdir(SESSION_DATA_DIR, 0755, true);
}

class FileCache {
    public static function get($key, $default = null) {
        try {
            $filePath = SESSION_DATA_DIR . md5($key) . '.json';
            if (!file_exists($filePath)) {
                return $default;
            }
            
            $content = file_get_contents($filePath);
            if ($content === false) {
                return $default;
            }
            
            $data = json_decode($content, true);
            if (!$data || !isset($data['expiration']) || time() > $data['expiration']) {
                self::delete($key);
                return $default;
            }
            
            return $data['value'];
        } catch (Exception $e) {
            error_log("FileCache get error: " . $e->getMessage());
            return $default;
        }
    }

    public static function set($key, $value, $duration = 300) {
        try {
            $filePath = SESSION_DATA_DIR . md5($key) . '.json';
            $data = [
                'value' => $value,
                'expiration' => time() + $duration,
                'created' => time()
            ];
            
            $result = file_put_contents($filePath, json_encode($data), LOCK_EX);
            return $result !== false;
        } catch (Exception $e) {
            error_log("FileCache set error: " . $e->getMessage());
            return false;
        }
    }

    public static function delete($key) {
        try {
            $filePath = SESSION_DATA_DIR . md5($key) . '.json';
            if (file_exists($filePath)) {
                return unlink($filePath);
            }
            return true;
        } catch (Exception $e) {
            error_log("FileCache delete error: " . $e->getMessage());
            return false;
        }
    }

    public static function cleanup() {
        try {
            $files = glob(SESSION_DATA_DIR . '*.json');
            $now = time();
            $deleted = 0;
            
            foreach ($files as $file) {
                if (!file_exists($file)) continue;
                
                $content = file_get_contents($file);
                if ($content === false) continue;
                
                $data = json_decode($content, true);
                if (!$data || !isset($data['expiration']) || $now > $data['expiration']) {
                    unlink($file);
                    $deleted++;
                }
            }
            
            return $deleted;
        } catch (Exception $e) {
            error_log("FileCache cleanup error: " . $e->getMessage());
            return false;
        }
    }
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$token = $_POST['token'] ?? $_GET['token'] ?? '';
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado']);
    exit;
}

try {
    if (!isset($pdo)) {
        throw new Exception('Conexión a base de datos no disponible');
    }

    $logger = new ErrorLogger();
    $sessionManager = new EvaluationSessionManager($pdo, $logger);
    $session = $sessionManager->getByToken($token);

    if (!$session) {
        http_response_code(401);
        echo json_encode(['error' => 'Sesión no encontrada o token inválido']);
        exit;
    }

    $cacheKey = "session_data_{$token}";
    
    if ($action === 'get_time_left') {
        $cachedData = FileCache::get($cacheKey);
        $timeLeft = 0;
        $timeElapsed = 0;
        
        if ($cachedData === null) {
            $now = new DateTime();
            $fin = new DateTime($session[EVALUATION_SESSION_FIELD_END] ?? 'now');
            $start = new DateTime($session[EVALUATION_SESSION_FIELD_START] ?? 'now');
            
            $timeLeft = max(0, $fin->getTimestamp() - $now->getTimestamp());
            $timeElapsed = max(0, $now->getTimestamp() - $start->getTimestamp());
            
            $sessionParticipants = $sessionManager->getSessionParticipants($session[EVALUATION_SESSION_FIELD_ID]) ?? [];
            $totalParticipants = count($sessionParticipants);
            
            $sessionData = [
                'time_left' => $timeLeft,
                'time_elapsed' => $timeElapsed,
                'participants' => $sessionParticipants,
                'total_participants' => $totalParticipants,
                'session_end' => $session[EVALUATION_SESSION_FIELD_END],
                'session_start' => $session[EVALUATION_SESSION_FIELD_START],
                'last_updated' => time()
            ];
            
            FileCache::set($cacheKey, $sessionData, 30);
        } else {
            $now = new DateTime();
            $fin = new DateTime($session[EVALUATION_SESSION_FIELD_END] ?? 'now');
            $start = new DateTime($session[EVALUATION_SESSION_FIELD_START] ?? 'now');
            
            $realTimeLeft = max(0, $fin->getTimestamp() - $now->getTimestamp());
            $realTimeElapsed = max(0, $now->getTimestamp() - $start->getTimestamp());
            
            $timeLeft = $cachedData['time_left'];
            $timeElapsed = $cachedData['time_elapsed'];
            $lastUpdated = $cachedData['last_updated'];
            $elapsed = time() - $lastUpdated;
            
            $timeLeft = max(0, $timeLeft - $elapsed);
            $timeElapsed = $timeElapsed + $elapsed;
            
            if (abs($realTimeLeft - $timeLeft) > 5) {
                $timeLeft = $realTimeLeft;
                $timeElapsed = $realTimeElapsed;
            }
            
            $cachedData['time_left'] = $timeLeft;
            $cachedData['time_elapsed'] = $timeElapsed;
            $cachedData['last_updated'] = time();
            
            FileCache::set($cacheKey, $cachedData, 30);
        }
        
        echo json_encode([
            'time_left' => $timeLeft,
            'time_elapsed' => $timeElapsed,
            'session_end' => $session[EVALUATION_SESSION_FIELD_END],
            'session_start' => $session[EVALUATION_SESSION_FIELD_START]
        ]);
        exit;
    }
    
    if ($action === 'sync_time') {
        $now = new DateTime();
        $fin = new DateTime($session[EVALUATION_SESSION_FIELD_END] ?? 'now');
        $start = new DateTime($session[EVALUATION_SESSION_FIELD_START] ?? 'now');
        
        $timeLeft = max(0, $fin->getTimestamp() - $now->getTimestamp());
        $timeElapsed = max(0, $now->getTimestamp() - $start->getTimestamp());
        
        $sessionParticipants = $sessionManager->getSessionParticipants($session[EVALUATION_SESSION_FIELD_ID]) ?? [];
        $totalParticipants = count($sessionParticipants);
        
        $sessionData = [
            'time_left' => $timeLeft,
            'time_elapsed' => $timeElapsed,
            'participants' => $sessionParticipants,
            'total_participants' => $totalParticipants,
            'session_end' => $session[EVALUATION_SESSION_FIELD_END],
            'session_start' => $session[EVALUATION_SESSION_FIELD_START],
            'last_updated' => time()
        ];
        
        FileCache::set($cacheKey, $sessionData, 60);
        
        echo json_encode([
            'time_left' => $timeLeft,
            'time_elapsed' => $timeElapsed,
            'synced' => true,
            'participants_count' => $totalParticipants
        ]);
        exit;
    }
    
    if ($action === 'check_evaluation_status') {
        $project_id = $_POST['project_id'] ?? $_GET['project_id'] ?? $session['project_id'] ?? 0;
        
        if (empty($project_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Project ID no proporcionado']);
            exit;
        }
        
        $evaluationManager = new EvaluationManager($pdo);
        $allReviewers = $evaluationManager->getReviewersWithEvaluationStatus($project_id);
        $reviewersWhoEvaluated = $evaluationManager->getReviewersWhoHaveEvaluated($project_id);
        
        $totalReviewers = count($allReviewers);
        $evaluatedReviewers = count($reviewersWhoEvaluated);
        
        $shouldCloseSession = ($totalReviewers > 0 && $evaluatedReviewers >= $totalReviewers);
        
        if ($shouldCloseSession) {
            $sessionManager->closeSession($session[EVALUATION_SESSION_FIELD_ID], 'completada', true);
            
            $projectManager = new ProjectManager($pdo);
            $projectManager->setStatus($project_id, 'completado');
        }
        
        $response = [
            'total_reviewers' => $totalReviewers,
            'evaluated_reviewers' => $evaluatedReviewers,
            'should_close_session' => $shouldCloseSession,
            'completion_percentage' => $totalReviewers > 0 ? round(($evaluatedReviewers / $totalReviewers) * 100, 2) : 0
        ];
        
        echo json_encode($response);
        exit;
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Acción no válida']);
    
} catch (Exception $e) {
    error_log("Error en timer.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}