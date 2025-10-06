<?php
define('SESSION_DATA_DIR', __PATH_CACHE__ . 'session_data/');
if (!file_exists(SESSION_DATA_DIR)) {
    mkdir(SESSION_DATA_DIR, 0755, true);
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

if (!@include_once('../includes/webengine.php')) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo cargar la configuración del sistema']);
    exit;
}

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
    
    if ($action === 'get_participants_count') {
        $sessionId = $session[EVALUATION_SESSION_FIELD_ID] ?? 0;
        $currentParticipants = $sessionManager->getSessionParticipants($sessionId) ?? [];
        
        $cachedData = FileCache::get($cacheKey);
        $totalParticipants = $cachedData['total_participants'] ?? count($currentParticipants);
        
        $sessionData = [
            'participants' => $currentParticipants,
            'total_participants' => $totalParticipants,
            'last_updated' => time(),
            'session_id' => $sessionId
        ];
        
        FileCache::set($cacheKey, $sessionData, 30);
        
        echo json_encode([
            'count' => count($currentParticipants), 
            'total' => $totalParticipants,
            'participants' => $currentParticipants,
            'session_id' => $sessionId,
            'timestamp' => time()
        ]);
        exit;
    }
    
    if ($action === 'get_reviewers_status') {
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
        
        $response = [
            'total_reviewers' => $totalReviewers,
            'evaluated_reviewers' => $evaluatedReviewers,
            'reviewers' => $allReviewers,
            'evaluated' => $reviewersWhoEvaluated,
            'completion_percentage' => $totalReviewers > 0 ? round(($evaluatedReviewers / $totalReviewers) * 100, 2) : 0,
            'project_id' => $project_id,
            'timestamp' => time()
        ];
        
        echo json_encode($response);
        exit;
    }
    
    if ($action === 'get_reviewer_details') {
        $reviewer_id = $_POST['reviewer_id'] ?? $_GET['reviewer_id'] ?? 0;
        $project_id = $_POST['project_id'] ?? $_GET['project_id'] ?? $session['project_id'] ?? 0;
        
        if (empty($reviewer_id) || empty($project_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros incompletos']);
            exit;
        }
        
        $evaluationManager = new EvaluationManager($pdo);
        $reviewerDetails = $evaluationManager->getReviewerDetails($project_id, $reviewer_id);
        
        if (!$reviewerDetails) {
            http_response_code(404);
            echo json_encode(['error' => 'Evaluador no encontrado']);
            exit;
        }
        
        $response = [
            'reviewer' => $reviewerDetails,
            'has_evaluated' => $evaluationManager->hasReviewerEvaluated($project_id, $reviewer_id),
            'project_id' => $project_id,
            'reviewer_id' => $reviewer_id,
            'timestamp' => time()
        ];
        
        echo json_encode($response);
        exit;
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Acción no válida']);
    
} catch (Exception $e) {
    error_log("Error en reviewers.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}