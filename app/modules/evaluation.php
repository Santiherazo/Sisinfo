<?php
if (!defined('__PATH_CACHE__') || !defined('AUTOSAVE_DIR') || !defined('SESSION_DATA_DIR')) {
    die('Configuración requerida no definida');
}

if (!file_exists(__PATH_CACHE__)) mkdir(__PATH_CACHE__, 0755, true);
if (!file_exists(AUTOSAVE_DIR)) mkdir(AUTOSAVE_DIR, 0755, true);
if (!file_exists(SESSION_DATA_DIR)) mkdir(SESSION_DATA_DIR, 0755, true);

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if (empty($token) || $project_id <= 0) {
    die('Parámetros inválidos');
}

$logger = new ErrorLogger();
$sessionManager = new EvaluationSessionManager($pdo, $logger);

if (!$sessionManager->isSessionActive($token)) {
    header('Location: ' . evalcp_base());
    exit;
}

$EvaluationCache = new Cache();
$EvaluationCache->configure('file', __PATH_CACHE__)
                ->setCachePath(SESSION_DATA_DIR, 'sessions')
                ->setCachePath(AUTOSAVE_DIR, 'autosave');

$projectManager = new ProjectManager($pdo);
$uploadManager = new UploadManager();
$projectDocuments = [];

$allProjects = $projectManager->getAllProjects();
$currentProject = null;

foreach ($allProjects as $project) {
    if ($project['id'] == $project_id) {
        $currentProject = $project;
        break;
    }
}

if ($currentProject && isset($currentProject['directorio'])) {
    $projectDir = $currentProject['directorio'];
    
    if ($projectDir) {
        $basePath = __PATH_UPLOADS__ . 'docs/projects/' . $projectDir . '/';
        if (is_dir($basePath)) {
            $files = $uploadManager->listAllContents($basePath);
            foreach ($files as $file) {
                $relativePath = 'docs/projects/' . $projectDir . '/' . $file;
                $fullPath = $basePath . $file;
                
                if (is_file($fullPath) && is_readable($fullPath)) {
                    $projectDocuments[] = [
                        'id' => md5($file),
                        'name' => $file,
                        'path' => $relativePath,
                        'full_path' => $fullPath,
                        'size' => filesize($fullPath),
                        'url' => $handler->getDocumentUrl($relativePath),
                        'previewable' => $handler->canPreviewInBrowser($file),
                        'size_formatted' => $handler->getFileSize($relativePath),
                        'tipo' => pathinfo($file, PATHINFO_EXTENSION)
                    ];
                }
            }
        }
    }
}

function simpleTransformSessionData($sessionData, $participants = [], $sessionManager, $sessionId) {
    $evaluadores = [];
    foreach ($participants as $participant) {
        $estado = $sessionManager->hasUserCompletedEvaluation($sessionId, $participant['id']) ? 'completada' : 'en progreso';
        
        $evaluadores[] = [
            'id_usuario' => $participant['id'] ?? 0,
            'nombre' => ($participant['firstname'] ?? '') . ' ' . ($participant['lastname'] ?? ''),
            'username' => $participant['username'] ?? '',
            'email' => $participant['email'] ?? '',
            'estado_evaluacion' => $estado,
            'estadisticas_evaluacion' => [
                'tiempo_dedicado_segundos' => 0,
                'porcentaje_completado' => 0,
            ]
        ];
    }

    return [
        'id_sesion' => $sessionData['id'] ?? $sessionData['token_acceso'] ?? '',
        'id_proyecto' => $sessionData['project_id'] ?? '',
        'tipo_cierre' => ($sessionData['cierre_manual'] ?? 0) ? 'manual' : 'automatico',
        'metadata_sesion' => [
            'fecha_inicio' => $sessionData['inicio'] ?? date('c'),
            'fecha_finalizacion' => $sessionData['fin'] ?? date('c', strtotime('+1 hour')),
            'duracion_segundos' => $sessionData['duracion'] ?? 3600
        ],
        'evaluadores' => $evaluadores,
        'analisis_general' => [
            'duracion_promedio_evaluacion' => 0,
            'total_errores_reportados' => 0
        ]
    ];
}

function updateSessionCache($EvaluationCache, $sessionManager, $token) {
    $sessionCacheKey = 'session_' . $token;
    $rawSessionData = $sessionManager->getByToken($token);
    if ($rawSessionData) {
        $sessionId = $rawSessionData['id'] ?? 0;
        $participants = $sessionManager->getSessionParticipants($sessionId);
        if (is_object($rawSessionData)) {
            $rawSessionData = (array)$rawSessionData;
        }
        $sessionData = simpleTransformSessionData($rawSessionData, $participants, $sessionManager, $sessionId);
        $EvaluationCache->storeSession($sessionCacheKey, $sessionData, 3600);
        return $sessionData;
    }
    return null;
}

function normalizeCriterionId($criterionName) {
    if (is_numeric($criterionName)) {
        return (string)$criterionName;
    }
    if (preg_match('/Criterio\s+(\d+)/i', $criterionName, $matches)) {
        return $matches[1];
    }
    if (preg_match('/Criterio_(\d+)/i', $criterionName, $matches)) {
        return $matches[1];
    }
    return $criterionName;
}

function handleAutoSave($EvaluationCache, $userId, $project_id, $postData) {
    $criteriosData = $postData['criterios'] ?? [];
    $generalComments = $postData['comentario_general'] ?? '';
    $activeStates = $postData['active_states'] ?? [];
    $elapsedTime = $postData['elapsed_time'] ?? 0;
    
    if (is_string($criteriosData)) {
        $criteriosData = json_decode($criteriosData, true);
    }
    if (is_string($activeStates)) {
        $activeStates = json_decode($activeStates, true);
    }
    
    $autoSaveData = [
        'criterios' => $criteriosData,
        'comentario_general' => $generalComments,
        'active_states' => $activeStates,
        'estadisticas' => [
            'tiempo_transcurrido' => $elapsedTime,
            'fecha_autoguardado' => date('Y-m-d H:i:s')
        ]
    ];
    
    $cacheKey = "autosave_{$userId}_{$project_id}";
    return $EvaluationCache->storeAutoSave($cacheKey, $autoSaveData, 86400, 'autosave');
}

function handleEvaluationSubmission($evaluationManager, $sessionManager, $EvaluationCache, $token, $project_id, $userId, $isAutoSubmit = false, $projectManager) {
    $criteriosData = $_POST['criterios'] ?? [];
    $generalComments = $_POST['comentario_general'] ?? '';
    $activeStates = $_POST['active_states'] ?? [];
    $elapsedTime = $_POST['elapsed_time'] ?? 0;
    
    if (is_string($criteriosData)) {
        $criteriosData = json_decode($criteriosData, true);
    }
    if (is_string($activeStates)) {
        $activeStates = json_decode($activeStates, true);
    }
    
    $success = true;
    $savedRatings = 0;
    $totalRatings = 0;
    $activeScores = [];
    $startTime = date('Y-m-d H:i:s');

    if (!empty($criteriosData) && is_array($criteriosData)) {
        foreach ($criteriosData as $criterionId => $criterio) {
            $isActive = $activeStates[$criterionId] ?? true;
            
            if (!$isActive) {
                $criterionName = 'Criterio ' . $criterionId;
                $formData = [
                    'session_token' => $token,
                    'project_id' => $project_id,
                    'evaluador_uid' => $userId,
                    'criterio_nombre' => $criterionName,
                    'criterio_valor' => 'Criterio desactivado',
                    'calificacion' => 0,
                    'observacion_personal' => '',
                    'criterio_activo' => 0
                ];
                $result = $evaluationManager->addRating($formData);
                if ($result) $savedRatings++;
                $totalRatings++;
                continue;
            }
            
            if (isset($criterio['valor']) && is_numeric($criterio['valor'])) {
                $criterionName = 'Criterio ' . $criterionId;
                $justification = $criterio['justificacion'] ?? 'Justificación automática';
                $formData = [
                    'session_token' => $token,
                    'project_id' => $project_id,
                    'evaluador_uid' => $userId,
                    'criterio_nombre' => $criterionName,
                    'criterio_valor' => $justification,
                    'calificacion' => $criterio['valor'],
                    'observacion_personal' => $criterio['comentario'] ?? '',
                    'criterio_activo' => 1
                ];
                $result = $evaluationManager->addRating($formData);
                if ($result) {
                    $savedRatings++;
                    $activeScores[] = $criterio['valor'];
                }
                $totalRatings++;
            }
        }
    }

    if ($savedRatings > 0) {
        $endTime = date('Y-m-d H:i:s');
        $duration = strtotime($endTime) - strtotime($startTime);

        $totalScore = !empty($activeScores) ? array_sum($activeScores) / count($activeScores) : 0;

        $summaryData = [
            'session_token' => $token,
            'project_id' => $project_id,
            'evaluador_uid' => $userId,
            'comentario_general' => $generalComments,
            'calificacion_total' => $totalScore,
            'tiempo_duracion' => $duration,
            'fecha_inicio' => $startTime,
            'fecha_fin' => $endTime
        ];

        $summaryResult = $evaluationManager->addRatingSummary($summaryData);

        if ($summaryResult) {
            $sessionId = $sessionManager->getByToken($token)['id'] ?? 0;
            if ($sessionId) {
                $sessionManager->markEvaluationCompleted($sessionId, $userId);
            }
            
            $projectManager->setStatus($project_id, 'completado');
            
            $cacheKey = "autosave_{$userId}_{$project_id}";
            $EvaluationCache->deleteAutoSave($cacheKey, 'autosave');
            
            if ($isAutoSubmit) {
                header('Location: ' . evalcp_base() . '?auto_submit=1');
                exit;
            } else {
                header('Location: ' . evalcp_base() . '?submit_success=1');
                exit;
            }
        }
    }
    
    header('Location: ' . evalcp_base() . '?submit_error=1');
    exit;
}

$sessionCacheKey = 'session_' . $token;
$sessionData = $EvaluationCache->getSession($sessionCacheKey);

if (!$sessionData) {
    $sessionData = updateSessionCache($EvaluationCache, $sessionManager, $token);
    if (!$sessionData) {
        header('Location: ' . evalcp_base());
        exit;
    }
}

$session = (object) $sessionData;
$userId = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
if ($userId <= 0) {
    die('Usuario no autenticado');
}

$profileManager = new ProfileManager($pdo, $db, $logger);
$evaluadorProfile = $profileManager->getProfile($userId);
$nombreEvaluador = $evaluadorProfile["full_name"] ?? 'Evaluador';

$totalEvaluadores = count($sessionData['evaluadores'] ?? []);
$evaluadoresConectados = 0;
$posicionActual = 1;

foreach ($sessionData['evaluadores'] as $index => $evaluador) {
    if ($evaluador['id_usuario'] == $userId) {
        $posicionActual = $index + 1;
    }
    if ($evaluador['estado_evaluacion'] === 'en progreso') {
        $evaluadoresConectados++;
    }
}

$criteriaConfig = loadConfig('evaluation_config');
$projectPhase = $projectManager->getFase($project_id);

$filteredCriteria = [];
if (isset($criteriaConfig['fases'][$projectPhase]['criterios'])) {
    foreach ($criteriaConfig['fases'][$projectPhase]['criterios'] as $criterionId => $criterion) {
        $criterion['id'] = $criterionId;
        $filteredCriteria[] = $criterion;
    }
}

$evaluationManager = new EvaluationManager($pdo);
$savedScores = [];
$savedComments = [];
$savedJustifications = [];
$savedActiveStates = [];
$savedGeneralComments = '';

$cacheKey = "autosave_{$userId}_{$project_id}";
$autoSaveData = $EvaluationCache->getAutoSave($cacheKey, 'autosave');

$cacheLoaded = false;
if ($autoSaveData && is_array($autoSaveData)) {
    $cacheLoaded = true;
    
    if (isset($autoSaveData['criterios']) && is_array($autoSaveData['criterios'])) {
        foreach ($autoSaveData['criterios'] as $criterionId => $criterio) {
            $savedScores[$criterionId] = $criterio['valor'] ?? 0;
            $savedComments[$criterionId] = $criterio['comentario'] ?? '';
            $savedJustifications[$criterionId] = $criterio['justificacion'] ?? '';
        }
    }
    if (isset($autoSaveData['active_states']) && is_array($autoSaveData['active_states'])) {
        foreach ($autoSaveData['active_states'] as $criterionId => $isActive) {
            $savedActiveStates[$criterionId] = (bool)$isActive;
        }
    }
    if (isset($autoSaveData['comentario_general'])) {
        $savedGeneralComments = $autoSaveData['comentario_general'];
    }
    if (isset($autoSaveData['estadisticas']['tiempo_transcurrido'])) {
        $elapsedTimeFromCache = $autoSaveData['estadisticas']['tiempo_transcurrido'];
    }
}

if (!$cacheLoaded) {
    $existingRatings = $evaluationManager->getRatingsByUser($project_id, $userId);
    
    if (is_array($existingRatings) && !empty($existingRatings)) {
        foreach ($existingRatings as $rating) {
            if (is_array($rating)) {
                $criterioNombre = '';
                if (isset($rating[RATINGS_CRITERIO_NOMBRE])) {
                    $criterioNombre = $rating[RATINGS_CRITERIO_NOMBRE];
                } elseif (isset($rating['criterio_nombre'])) {
                    $criterioNombre = $rating['criterio_nombre'];
                } elseif (isset($rating['nombre_criterio'])) {
                    $criterioNombre = $rating['nombre_criterio'];
                }
                
                if (!empty($criterioNombre)) {
                    $criterionId = normalizeCriterionId($criterioNombre);
                    
                    $calificacion = 0;
                    if (isset($rating[RATINGS_CALIFICACION])) {
                        $calificacion = $rating[RATINGS_CALIFICACION];
                    } elseif (isset($rating['calificacion'])) {
                        $calificacion = $rating['calificacion'];
                    } elseif (isset($rating['valor'])) {
                        $calificacion = $rating['valor'];
                    }
                    
                    $comentario = '';
                    if (isset($rating[RATINGS_OBSERVACION_PERSONAL])) {
                        $comentario = $rating[RATINGS_OBSERVACION_PERSONAL];
                    } elseif (isset($rating['observacion_personal'])) {
                        $comentario = $rating['observacion_personal'];
                    } elseif (isset($rating['comentario'])) {
                        $comentario = $rating['comentario'];
                    }
                    
                    $justificacion = '';
                    if (isset($rating[RATINGS_CRITERIO_VALOR])) {
                        $justificacion = $rating[RATINGS_CRITERIO_VALOR];
                    } elseif (isset($rating['criterio_valor'])) {
                        $justificacion = $rating['criterio_valor'];
                    }
                    
                    $activo = true;
                    if (isset($rating['criterio_activo'])) {
                        $activo = $rating['criterio_activo'] == 1;
                    } elseif (isset($rating['activo'])) {
                        $activo = (bool)$rating['activo'];
                    }
                    
                    $savedScores[$criterionId] = $calificacion;
                    $savedComments[$criterionId] = $comentario;
                    $savedJustifications[$criterionId] = $justificacion;
                    $savedActiveStates[$criterionId] = $activo;
                }
            }
        }
    }

    if (empty($savedGeneralComments)) {
        $existingSummary = $evaluationManager->getSummaryByUser($project_id, $userId);
        if ($existingSummary && is_array($existingSummary)) {
            if (isset($existingSummary[RATING_SUMMARY_COMENTARIO])) {
                $savedGeneralComments = $existingSummary[RATING_SUMMARY_COMENTARIO];
            } elseif (isset($existingSummary['comentario_general'])) {
                $savedGeneralComments = $existingSummary['comentario_general'];
            }
        }
    }
    
    if (!empty($savedScores)) {
        $autoSaveData = [
            'criterios' => [],
            'active_states' => $savedActiveStates,
            'comentario_general' => $savedGeneralComments,
            'estadisticas' => [
                'tiempo_transcurrido' => 0,
                'fecha_autoguardado' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($savedScores as $criterionId => $score) {
            $autoSaveData['criterios'][$criterionId] = [
                'valor' => $score,
                'comentario' => $savedComments[$criterionId] ?? '',
                'justificacion' => $savedJustifications[$criterionId] ?? ''
            ];
        }
        
        $EvaluationCache->storeAutoSave($cacheKey, $autoSaveData, 86400, 'autosave');
    }
}

foreach ($filteredCriteria as $criterion) {
    $criterionId = $criterion['id'];
    if (!isset($savedActiveStates[$criterionId])) {
        $savedActiveStates[$criterionId] = true;
    }
    if (!isset($savedScores[$criterionId])) {
        $savedScores[$criterionId] = 0;
    }
    if (!isset($savedComments[$criterionId])) {
        $savedComments[$criterionId] = '';
    }
}

$totalCriteriosActivos = count(array_filter($savedActiveStates, function($state) {
    return $state === true;
}));

$sessionStartTime = $sessionData['metadata_sesion']['fecha_inicio'] ?? date('c');
$sessionEndTime = $sessionData['metadata_sesion']['fecha_finalizacion'] ?? date('c', strtotime('+1 hour'));
$currentTime = time();
$sessionEndTimestamp = strtotime($sessionEndTime);
$timeLeft = $sessionEndTimestamp - $currentTime;

if ($timeLeft <= 0) {
    $criteriosData = [];
    foreach ($filteredCriteria as $criterion) {
        $criterionId = $criterion['id'];
        if ($savedActiveStates[$criterionId] ?? true) {
            $criteriosData[$criterionId] = [
                'valor' => $savedScores[$criterionId] ?? 0,
                'comentario' => $savedComments[$criterionId] ?? '',
                'justificacion' => 'Evaluación automática por tiempo agotado'
            ];
        }
    }
    
    $_POST = [
        'action' => 'auto_submit',
        'token' => $token,
        'project_id' => $project_id,
        'criterios' => json_encode($criteriosData),
        'comentario_general' => $savedGeneralComments ?: 'Evaluación enviada automáticamente por tiempo agotado',
        'active_states' => json_encode($savedActiveStates),
        'elapsed_time' => $elapsedTimeFromCache ?? 0
    ];
    
    handleEvaluationSubmission($evaluationManager, $sessionManager, $EvaluationCache, $token, $project_id, $userId, true, $projectManager);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'submit_evaluation' || $action === 'auto_submit') {
        $isAutoSubmit = ($action === 'auto_submit');
        handleEvaluationSubmission($evaluationManager, $sessionManager, $EvaluationCache, $token, $project_id, $userId, $isAutoSubmit, $projectManager);
    }
    
    if ($action === 'autosave') {
        $result = handleAutoSave($EvaluationCache, $userId, $project_id, $_POST);
        if ($result) {
            echo 'OK';
        } else {
            echo 'ERROR';
        }
        exit;
    }
}

if (!isset($_POST['action']) || $_POST['action'] !== 'autosave') {
    $sessionData = updateSessionCache($EvaluationCache, $sessionManager, $token);
}
?>

<div id="popupModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all scale-95 opacity-0" id="popupContent">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-4" id="popupIcon">
                <i data-lucide="info" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h3 class="text-xl font-semibold text-center text-gray-900 mb-2" id="popupTitle">Título</h3>
            <p class="text-gray-600 text-center mb-6" id="popupMessage">Mensaje</p>
            <div class="flex justify-center space-x-3">
                <button id="popupCancel" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors hidden">Cancelar</button>
                <button id="popupConfirm" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<div id="timeAlert" class="hidden fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full"></div>

<div id="floatingTimer" class="fixed top-4 right-4 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg z-40 border border-gray-200 p-4">
    <div class="flex items-center space-x-2">
        <i data-lucide="clock" class="w-5 h-5 text-gray-600"></i>
        <span class="font-mono text-lg font-bold text-gray-700" id="floatingTimerDisplay">00:00:00</span>
    </div>
</div>

<div id="documentViewerModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 h-[90vh] transform transition-all scale-95 opacity-0 flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Visor de Documentos</h3>
            <button type="button" onclick="closeDocumentViewer()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="flex-1 p-6 overflow-hidden">
            <div id="documentViewerContent" class="h-full w-full bg-gray-50 rounded-lg flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <i data-lucide="file" class="w-16 h-16 mx-auto mb-4"></i>
                    <p>Selecciona un documento para previsualizar</p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <button type="button" onclick="changeDocumentPage(-1)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    <span>Anterior</span>
                </button>
                <button type="button" onclick="changeDocumentPage(1)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2">
                    <span>Siguiente</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="text-sm text-gray-600" id="documentPageInfo">Página 1 de 1</div>
            <button type="button" onclick="closeDocumentViewer()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                Cerrar Visor
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6">
    <form id="evaluationForm" method="POST" class="space-y-6">
        <input type="hidden" name="action" id="formAction" value="submit_evaluation">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
        <input type="hidden" name="elapsed_time" id="elapsedTimeInput" value="0">
        
        <div id="sessionHeader" class="glass rounded-2xl p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Sesión de Evaluación</h3>
                    <p class="text-lg font-semibold text-gray-900">ID: <?php echo htmlspecialchars($sessionData['id_sesion'] ?? 'N/A'); ?></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Inició</h3>
                    <p class="text-sm text-gray-900" id="sessionStart">
                        <?php echo isset($sessionData['metadata_sesion']['fecha_inicio']) ? 
                            date('H:i:s', strtotime($sessionData['metadata_sesion']['fecha_inicio'])) : 'N/A'; ?>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Finaliza</h3>
                    <p class="text-sm text-gray-900" id="sessionEnd">
                        <?php echo isset($sessionData['metadata_sesion']['fecha_finalizacion']) ? 
                            date('H:i:s', strtotime($sessionData['metadata_sesion']['fecha_finalizacion'])) : 'N/A'; ?>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Evaluadores</h3>
                    <p class="text-sm text-gray-900" id="evaluatorName"><?php echo htmlspecialchars($nombreEvaluador); ?> (<?php echo $posicionActual; ?>/<?php echo $totalEvaluadores; ?>)</p>
                    <p class="text-xs text-gray-500" id="evaluatorsConnected">Conectados: <?php echo $evaluadoresConectados; ?></p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Formulario de Evaluación</h2>
            <div class="flex items-center gap-4">
                <button type="button" onclick="openDocumentViewer()" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" id="documentsButton">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ver Documentos</span>
                </button>
                <div class="flex items-center space-x-2 bg-orange-100/50 rounded-lg px-3 py-2">
                    <i data-lucide="timer" class="w-5 h-5 text-gray-600"></i>
                    <span class="font-mono text-lg font-bold text-gray-700" id="evalTimerDisplay">00:00:00</span>
                </div>
                <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg" id="activeCriteriaCount">
                    <span id="activeCountDisplay"><?php echo $totalCriteriosActivos; ?></span> criterios activos
                </div>
            </div>
        </div>

        <div id="totalScoreDisplay" class="flex items-center justify-between bg-white/20 rounded-xl p-4 border border-white/20 mb-6">
            <div>
                <span class="text-sm text-gray-600">Puntaje Total:</span>
                <span class="ml-2 text-2xl font-bold text-gray-900" id="totalScore">0/10</span>
            </div>
            <div id="totalStatus" class="px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-800">Sin evaluar</div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Criterios de Evaluación</h3>
                <div id="criteriaCounter" class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">
                    <span id="evaluatedCountDisplay">0</span>/<span id="totalActiveDisplay"><?php echo count($filteredCriteria); ?></span> criterios evaluados
                </div>
            </div>
            
            <div id="criteriaContainer">
                <?php foreach ($filteredCriteria as $criterion): ?>
                <?php
                $criterionId = $criterion['id'];
                $savedScore = $savedScores[$criterionId] ?? 0;
                $savedComment = $savedComments[$criterionId] ?? '';
                $isActive = $savedActiveStates[$criterionId] ?? true;
                
                $currentCategory = 'Regular';
                $currentJustification = '';
                if (isset($criteriaConfig['categorias_calificacion'])) {
                    foreach ($criteriaConfig['categorias_calificacion'] as $category) {
                        if ($savedScore >= $category['minimo'] && $savedScore <= $category['maximo']) {
                            $currentCategory = $category['nombre'];
                            break;
                        }
                    }
                }
                
                if (isset($criterion['evaluaciones'])) {
                    foreach ($criterion['evaluaciones'] as $evalCategory) {
                        if ($evalCategory['categoria_id'] === strtolower($currentCategory)) {
                            $currentJustification = $evalCategory['condiciones'] ?? '';
                            break;
                        }
                    }
                }
                
                $minScore = $criteriaConfig['puntuacion']['valor_minimo'] ?? 0;
                $maxScore = $criteriaConfig['puntuacion']['valor_maximo'] ?? 10;
                $step = $criteriaConfig['puntuacion']['incremento'] ?? 1;
                ?>
                <div class="mb-6 p-4 bg-gray-50 rounded-lg criterion-container" data-criterion-id="<?php echo $criterionId; ?>">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <button type="button" class="criterion-toggle p-2 rounded-full transition-colors <?php echo $isActive ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'; ?>" onclick="toggleCriterion('<?php echo $criterionId; ?>')">
                                <i data-lucide="<?php echo $isActive ? 'check-circle' : 'x-circle'; ?>" class="w-5 h-5"></i>
                            </button>
                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($criterion['nombre'] ?? 'Criterio sin nombre'); ?></h4>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Ponderación: <?php echo $criterion['ponderacion'] ?? 0; ?>%</span>
                            <span class="criterion-status px-2 py-1 rounded text-xs font-medium <?php echo $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'; ?>">
                                <?php echo $isActive ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="criterion-content <?php echo !$isActive ? 'opacity-50' : ''; ?>">
                        <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($criterion['descripcion'] ?? 'Sin descripción'); ?></p>
                        
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Puntaje: <span id="score-value-<?php echo $criterionId; ?>"><?php echo $savedScore; ?></span>/<?php echo $maxScore; ?></span>
                                <span class="text-sm font-medium" id="rating-text-<?php echo $criterionId; ?>"><?php echo $currentCategory; ?></span>
                            </div>
                            <input type="range" min="<?php echo $minScore; ?>" max="<?php echo $maxScore; ?>" step="<?php echo $step; ?>" 
                                   value="<?php echo $savedScore; ?>" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb criterion-slider" 
                                   oninput="updateScoreSlider('<?php echo $criterionId; ?>', this.value)" <?php echo !$isActive ? 'disabled' : ''; ?>>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Justificación</label>
                            <div class="justification-display p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-800" id="justification-text-<?php echo $criterionId; ?>">
                                    <?php echo htmlspecialchars($currentJustification); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios Adicionales</label>
                            <textarea rows="2" class="w-full p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent criterion-comment" 
                                      placeholder="Escribe tus observaciones adicionales sobre este criterio" 
                                      oninput="updateComment('<?php echo $criterionId; ?>', this.value)" 
                                      <?php echo !$isActive ? 'disabled' : ''; ?>><?php echo htmlspecialchars($savedComment); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Evaluación General</h3>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Retroalimentación general</label>
                <textarea name="comentario_general" rows="4" class="w-full p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                          placeholder="Proporciona una retroalimentación general sobre el trabajo del estudiante..." 
                          required id="evaluationComments"><?php echo htmlspecialchars($savedGeneralComments); ?></textarea>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors" onclick="window.location.href='/'">Cancelar</button>
            <button type="submit" id="submitEvaluation" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Enviar evaluación</button>
        </div>
    </form>
</div>

<script>
const token = "<?php echo addslashes($token); ?>";
const projectId = <?php echo (int)$project_id; ?>;
const userId = <?php echo (int)$userId; ?>;
const escalaMaxima = <?php echo $criteriaConfig['puntuacion']['valor_maximo']; ?>;
const sessionStartTime = new Date("<?php echo addslashes($sessionStartTime); ?>");
const sessionEndTime = new Date("<?php echo addslashes($sessionEndTime); ?>");

let evaluationScores = <?php echo json_encode($savedScores); ?>;
let evaluationComments = <?php echo json_encode($savedComments); ?>;
let activeCriterionStates = <?php echo json_encode($savedActiveStates); ?>;
let savedGeneralComments = "<?php echo addslashes($savedGeneralComments); ?>";

const filteredCriteriaIds = <?php echo json_encode(array_column($filteredCriteria, 'id')); ?>;
const criteriaConfig = <?php echo json_encode($filteredCriteria); ?>;

let currentEvaluatorsData = {
    nombreEvaluador: "<?php echo addslashes($nombreEvaluador); ?>",
    posicionActual: <?php echo $posicionActual; ?>,
    totalEvaluadores: <?php echo $totalEvaluadores; ?>,
    evaluadoresConectados: <?php echo $evaluadoresConectados; ?>
};

const projectDocuments = <?php echo json_encode($projectDocuments ?? []); ?>;

filteredCriteriaIds.forEach(criterionId => {
    if (activeCriterionStates[criterionId] === undefined) {
        activeCriterionStates[criterionId] = true;
    }
    if (evaluationScores[criterionId] === undefined) {
        evaluationScores[criterionId] = 0;
    }
    if (evaluationComments[criterionId] === undefined) {
        evaluationComments[criterionId] = '';
    }
});

let timerInterval;
let countdownInterval;
let autoSaveInterval;
let evaluationSubmitted = false;
let elapsedTime = <?php echo isset($elapsedTimeFromCache) ? (int)$elapsedTimeFromCache : 0; ?>;
let alertShown10min = false;
let alertShown5min = false;
let alertShown1min = false;
let alertShown30sec = false;
let lastSaveTime = 0;
const AUTO_SAVE_DELAY = 30000;

let currentDocumentIndex = 0;

function openDocumentViewer() {
    const modal = document.getElementById('documentViewerModal');
    const content = modal.querySelector('.transform');
    const viewerContent = document.getElementById('documentViewerContent');
    
    if (!projectDocuments || projectDocuments.length === 0) {
        showPopup('info', 'No hay documentos disponibles para este proyecto.');
        viewerContent.innerHTML = `
            <div class="p-4 text-gray-500">Selecciona un documento para previsualizar.</div>
        `;
        return;
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    if (currentDocumentIndex < 0 || currentDocumentIndex >= projectDocuments.length) {
        currentDocumentIndex = 0;
    }

    loadDocument(currentDocumentIndex); 
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

function closeDocumentViewer() {
    const modal = document.getElementById('documentViewerModal');
    const content = modal.querySelector('.transform');
    
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function loadDocument(index) {
    if (index < 0 || index >= projectDocuments.length) return;
    
    currentDocumentIndex = index;
    const documentData = projectDocuments[index];
    const viewerContent = document.getElementById('documentViewerContent');

    viewerContent.innerHTML = `
        <div class="w-full h-full flex items-center justify-center">
            <div class="text-center">
                <i data-lucide="loader" class="w-8 h-8 animate-spin text-gray-400 mx-auto mb-2"></i>
                <p class="text-gray-500">Cargando: ${documentData.name}...</p>
            </div>
        </div>
    `;

    setTimeout(() => {
        renderDocument(documentData, viewerContent); 
    }, 100); 
    
    updateDocumentNavigation(); 
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

function renderDocument(document, container) {
    const fileExtension = getFileExtension(document.name);
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension.toLowerCase());
    const isPDF = fileExtension.toLowerCase() === 'pdf';
    const isText = ['txt', 'md', 'csv'].includes(fileExtension.toLowerCase());
    
    let contentHTML = '';
    
    if (isImage) {
        contentHTML = `
            <div class="w-full h-full flex items-center justify-center bg-gray-900 p-4">
                <img src="${document.url || document.full_path}" 
                     alt="${document.name}" 
                     class="max-w-full max-h-full object-contain"
                     onload="handleDocumentLoad()"
                     onerror="handleDocumentError(this)">
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/70 text-white px-3 py-1 rounded-lg text-sm">
                    ${document.name}
                </div>
            </div>
        `;
    } else if (isPDF && document.previewable) {
        contentHTML = `
            <div class="w-full h-full flex flex-col">
                <div class="flex items-center justify-between p-4 bg-gray-100 border-b">
                    <span class="text-sm font-medium text-gray-700">${document.name}</span>
                </div>
                <div class="flex-1 bg-gray-800">
                    <iframe src="${document.url || document.full_path}#view=fitH" 
                            class="w-full h-full border-0"
                            title="${document.name}"
                            onload="handleDocumentLoad()">
                    </iframe>
                </div>
            </div>
        `;
    } else if (isText) {
        fetch(document.url || document.full_path)
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar el archivo');
                return response.text();
            })
            .then(text => {
                container.innerHTML = `
                    <div class="w-full h-full flex flex-col">
                        <div class="flex items-center justify-between p-4 bg-gray-100 border-b">
                            <span class="text-sm font-medium text-gray-700">${document.name}</span>
                        </div>
                        <div class="flex-1 overflow-auto bg-white">
                            <pre class="p-6 text-sm font-mono whitespace-pre-wrap">${escapeHtml(text)}</pre>
                        </div>
                    </div>
                `;
                handleDocumentLoad();
                if (window.lucide && window.lucide.createIcons) {
                    window.lucide.createIcons();
                }
            })
            .catch(error => {
                console.error('Error cargando archivo de texto:', error);
                showDocumentError(container, document, 'No se pudo cargar el archivo de texto');
            });
        return;
    } else {
        contentHTML = `
            <div class="w-full h-full flex flex-col items-center justify-center p-8">
                <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 text-center">
                    <i data-lucide="file" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">${document.name}</h4>
                    <p class="text-gray-600 mb-4">Este tipo de archivo (${fileExtension.toUpperCase()}) no se puede previsualizar en el navegador.</p>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 text-left">
                        <p class="text-sm text-gray-700"><strong>Tipo:</strong> ${fileExtension.toUpperCase()}</p>
                        <p class="text-sm text-gray-700"><strong>Tamaño:</strong> ${document.size_formatted || 'Desconocido'}</p>
                        <p class="text-sm text-gray-700"><strong>Ruta:</strong> ${document.path || 'No disponible'}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (!isText) {
        container.innerHTML = contentHTML;
        if (isImage || isPDF) {
        } else {
            handleDocumentLoad();
        }
    }
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

function handleDocumentLoad() {
    console.log('Documento cargado correctamente');
}

function handleDocumentError(imgElement) {
    console.error('Error cargando documento');
    const container = imgElement.closest('#documentViewerContent');
    const document = projectDocuments[currentDocumentIndex];
    showDocumentError(container, document, 'No se pudo cargar el documento');
}

function showDocumentError(container, document, message) {
    container.innerHTML = `
        <div class="w-full h-full flex flex-col items-center justify-center p-8">
            <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 text-center">
                <i data-lucide="alert-triangle" class="w-16 h-16 text-yellow-500 mx-auto mb-4"></i>
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Error al cargar el documento</h4>
                <p class="text-gray-600 mb-2">${message}</p>
                <p class="text-sm text-gray-500 mb-4">${document.name}</p>
                <div class="bg-gray-50 rounded-lg p-4 mb-4 text-left">
                    <p class="text-sm text-gray-700"><strong>Tamaño:</strong> ${document.size_formatted || 'Desconocido'}</p>
                    <p class="text-sm text-gray-700"><strong>Ruta:</strong> ${document.path || 'No disponible'}</p>
                </div>
            </div>
        </div>
    `;
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

function changeDocumentPage(direction) {
    const newIndex = currentDocumentIndex + direction;
    if (newIndex >= 0 && newIndex < projectDocuments.length) {
        loadDocument(newIndex);
    }
}

function updateDocumentNavigation() {
    const pageInfo = document.getElementById('documentPageInfo');
    const prevButton = document.querySelector('button[onclick="changeDocumentPage(-1)"]');
    const nextButton = document.querySelector('button[onclick="changeDocumentPage(1)"]');
    
    if (pageInfo) {
        pageInfo.textContent = `Documento ${currentDocumentIndex + 1} de ${projectDocuments.length}`;
    }
    
    if (prevButton) {
        prevButton.disabled = currentDocumentIndex === 0;
        prevButton.classList.toggle('opacity-50', currentDocumentIndex === 0);
        prevButton.classList.toggle('cursor-not-allowed', currentDocumentIndex === 0);
        prevButton.classList.toggle('hover:bg-gray-50', currentDocumentIndex !== 0);
    }
    
    if (nextButton) {
        nextButton.disabled = currentDocumentIndex === projectDocuments.length - 1;
        nextButton.classList.toggle('opacity-50', currentDocumentIndex === projectDocuments.length - 1);
        nextButton.classList.toggle('cursor-not-allowed', currentDocumentIndex === projectDocuments.length - 1);
        nextButton.classList.toggle('hover:bg-gray-50', currentDocumentIndex !== projectDocuments.length - 1);
    }
}

function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateDocumentButton() {
    const docButton = document.getElementById('documentsButton');
    if (docButton && projectDocuments.length > 0) {
        const existingBadge = docButton.querySelector('.document-count-badge');
        if (existingBadge) {
            existingBadge.remove();
        }
        
        const countBadge = document.createElement('span');
        countBadge.className = 'document-count-badge ml-2 bg-blue-500 text-white text-xs rounded-full px-2 py-1';
        countBadge.textContent = projectDocuments.length;
        docButton.appendChild(countBadge);
    }
}

function showPopup(type, message, callback = null) {
    const modal = document.getElementById('popupModal');
    const content = document.getElementById('popupContent');
    const icon = document.getElementById('popupIcon');
    const title = document.getElementById('popupTitle');
    const messageEl = document.getElementById('popupMessage');
    const confirmBtn = document.getElementById('popupConfirm');
    
    let iconName = 'info';
    let iconColor = 'text-blue-500';
    let bgColor = 'bg-blue-100';
    let titleText = 'Información';
    
    switch(type) {
        case 'success':
            iconName = 'check-circle';
            iconColor = 'text-green-500';
            bgColor = 'bg-green-100';
            titleText = 'Éxito';
            break;
        case 'error':
            iconName = 'x-circle';
            iconColor = 'text-red-500';
            bgColor = 'bg-red-100';
            titleText = 'Error';
            break;
    }
    
    icon.className = `flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-4 ${bgColor}`;
    icon.innerHTML = `<i data-lucide="${iconName}" class="w-8 h-8 ${iconColor}"></i>`;
    title.textContent = titleText;
    messageEl.textContent = message;
    
    confirmBtn.onclick = function() {
        hidePopup();
        if (callback && typeof callback === 'function') {
            callback();
        }
    };
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

function hidePopup() {
    const modal = document.getElementById('popupModal');
    const content = document.getElementById('popupContent');
    
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function getJustificationForScore(criterionId, score) {
    const criterion = criteriaConfig.find(c => c.id === criterionId);
    if (!criterion || !criterion.evaluaciones) return '';
    
    const categories = <?php echo json_encode($criteriaConfig['categorias_calificacion'] ?? []); ?>;
    let currentCategory = '';
    
    for (const category of Object.values(categories)) {
        if (score >= category.minimo && score <= category.maximo) {
            currentCategory = category.id;
            break;
        }
    }
    
    for (const evalCategory of Object.values(criterion.evaluaciones)) {
        if (evalCategory.categoria_id === currentCategory) {
            return evalCategory.condiciones || '';
        }
    }
    
    return '';
}

function toggleCriterion(criterionId) {
    activeCriterionStates[criterionId] = !activeCriterionStates[criterionId];
    const container = document.querySelector(`[data-criterion-id="${criterionId}"]`);
    if (!container) return;
    
    const toggleBtn = container.querySelector('.criterion-toggle');
    const statusSpan = container.querySelector('.criterion-status');
    const contentDiv = container.querySelector('.criterion-content');
    const slider = container.querySelector('.criterion-slider');
    const textarea = container.querySelector('.criterion-comment');
    
    if (activeCriterionStates[criterionId]) {
        toggleBtn.className = 'criterion-toggle p-2 rounded-full transition-colors bg-green-100 text-green-600 hover:bg-green-200';
        statusSpan.className = 'criterion-status px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
        statusSpan.textContent = 'Activo';
        contentDiv.classList.remove('opacity-50');
        if (slider) slider.disabled = false;
        if (textarea) textarea.disabled = false;
        
        if (evaluationScores[criterionId] !== undefined && evaluationScores[criterionId] > 0) {
            slider.value = evaluationScores[criterionId];
            updateScoreSlider(criterionId, evaluationScores[criterionId]);
        }
        if (evaluationComments[criterionId] !== undefined) {
            textarea.value = evaluationComments[criterionId];
        }
    } else {
        toggleBtn.className = 'criterion-toggle p-2 rounded-full transition-colors bg-gray-200 text-gray-500 hover:bg-gray-300';
        statusSpan.className = 'criterion-status px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-600';
        statusSpan.textContent = 'Inactivo';
        contentDiv.classList.add('opacity-50');
        if (slider) slider.disabled = true;
        if (textarea) textarea.disabled = true;

        evaluationScores[criterionId] = 0;
        evaluationComments[criterionId] = '';
        
        const scoreValueElement = document.getElementById(`score-value-${criterionId}`);
        const ratingTextElement = document.getElementById(`rating-text-${criterionId}`);
        const justificationElement = document.getElementById(`justification-text-${criterionId}`);
        if (scoreValueElement) scoreValueElement.textContent = '0';
        if (ratingTextElement) ratingTextElement.textContent = 'Regular';
        if (justificationElement) justificationElement.textContent = '';
        if (slider) slider.value = 0;
        if (textarea) textarea.value = '';
    }
    
    const icon = toggleBtn.querySelector('i');
    if (icon) icon.setAttribute('data-lucide', activeCriterionStates[criterionId] ? 'check-circle' : 'x-circle');
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    
    updateActiveCriteriaCount();
    updateCriteriaCounter();
    calculateTotalScore();
    saveAutoSave();
}

function updateScoreSlider(id, value) {
    const numericValue = parseFloat(value);
    const scoreValueElement = document.getElementById(`score-value-${id}`);
    if (scoreValueElement) scoreValueElement.textContent = numericValue.toFixed(1);
    
    evaluationScores[id] = numericValue;
    
    let ratingText = 'Regular';
    const categories = <?php echo json_encode($criteriaConfig['categorias_calificacion'] ?? []); ?>;
    for (const category of Object.values(categories)) {
        if (numericValue >= category.minimo && numericValue <= category.maximo) {
            ratingText = category.nombre;
            break;
        }
    }
    
    const ratingTextElement = document.getElementById(`rating-text-${id}`);
    if (ratingTextElement) ratingTextElement.textContent = ratingText;
    
    const autoJustification = getJustificationForScore(id, numericValue);
    const justificationElement = document.getElementById(`justification-text-${id}`);
    
    if (justificationElement) {
        justificationElement.textContent = autoJustification;
    }
    
    calculateTotalScore();
    saveAutoSave();
}

function updateComment(criterionId, comment) {
    evaluationComments[criterionId] = comment;
    saveAutoSave();
}

function startTimeMonitoring() {
    updateCountdown();
    timerInterval = setInterval(updateCountdown, 1000);
}

function clearAllIntervals() {
    if (timerInterval) clearInterval(timerInterval);
    if (countdownInterval) clearInterval(countdownInterval);
    if (autoSaveInterval) clearInterval(autoSaveInterval);
}

function showTimeAlert(message, duration = 15000, type = 'info') {
    const alertElement = document.getElementById('timeAlert');
    if (!alertElement) return;
    
    const bgColor = type === 'warning' ? 'bg-yellow-500' : type === 'danger' ? 'bg-red-500' : 'bg-blue-500';
    const borderColor = type === 'warning' ? 'border-yellow-400' : type === 'danger' ? 'border-red-400' : 'border-blue-400';
    
    alertElement.innerHTML = `
        <div class="${bgColor} text-white p-4 rounded-lg shadow-lg border-l-4 ${borderColor} animate-pulse">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i data-lucide="${type === 'warning' ? 'alert-triangle' : type === 'danger' ? 'alert-circle' : 'clock'}" class="w-5 h-5"></i>
                    <span class="font-medium">${message}</span>
                </div>
                <button onclick="document.getElementById('timeAlert').classList.add('hidden')" class="text-white hover:opacity-70 ml-4 transition-opacity">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            </div>
    `;
    alertElement.classList.remove('hidden');
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    
    if (duration > 0) {
        setTimeout(() => {
            alertElement.classList.add('hidden');
        }, duration);
    }
}

function startFinalCountdown() {
    let secondsLeft = 30;
    
    const updateFinalCountdown = () => {
        if (secondsLeft <= 0) {
            clearInterval(countdownInterval);
            forceAutoSubmitEvaluation();
            return;
        }
        
        const message = `⏱️ <strong>${secondsLeft}</strong> segundo${secondsLeft !== 1 ? 's' : ''} restante${secondsLeft !== 1 ? 's' : ''} - La evaluación se enviará automáticamente`;
        showTimeAlert(message, 0, 'danger');
        
        secondsLeft--;
    };
    
    updateFinalCountdown();
    countdownInterval = setInterval(updateFinalCountdown, 1000);
}

function updateCountdown() {
    const now = new Date();
    const diff = sessionEndTime - now;
    
    if (diff <= 0) {
        clearAllIntervals();
        if (!evaluationSubmitted) {
            forceAutoSubmitEvaluation();
        }
        return;
    }
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    const displayTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    const evalTimer = document.getElementById('evalTimerDisplay');
    const floatingTimer = document.getElementById('floatingTimerDisplay');
    
    if (evalTimer) evalTimer.textContent = displayTime;
    if (floatingTimer) floatingTimer.textContent = displayTime;
    
    elapsedTime = Math.floor((now - sessionStartTime) / 1000);
    document.getElementById('elapsedTimeInput').value = elapsedTime;
    
    const totalSeconds = Math.floor(diff / 1000);
    
    if (totalSeconds <= 600 && !alertShown10min) {
        showTimeAlert('⏰ <strong>Quedan 10 minutos</strong> - Revisa tu evaluación antes de que finalice el tiempo', 15000, 'warning');
        alertShown10min = true;
    }
    
    if (totalSeconds <= 300 && !alertShown5min) {
        showTimeAlert('⚠️ <strong>Quedan 5 minutos</strong> - Estás a tiempo de completar tu evaluación', 15000, 'warning');
        alertShown5min = true;
    }
    
    if (totalSeconds <= 60 && !alertShown1min) {
        showTimeAlert('🚨 <strong>¡Último minuto!</strong> - Completa tu evaluación ahora', 10000, 'danger');
        alertShown1min = true;
        if (evalTimer) evalTimer.classList.add('text-red-600', 'animate-pulse');
        if (floatingTimer) floatingTimer.classList.add('text-red-600', 'animate-pulse');
    }
    
    if (totalSeconds <= 30 && !alertShown30sec && !evaluationSubmitted) {
        alertShown30sec = true;
        startFinalCountdown();
    }
}

function prepareEvaluationData() {
    const criteriosData = {};
    filteredCriteriaIds.forEach(criterionId => {
        const justification = getJustificationForScore(criterionId, evaluationScores[criterionId] || 0);
        criteriosData[criterionId] = {
            valor: evaluationScores[criterionId] || 0,
            comentario: evaluationComments[criterionId] || '',
            justificacion: justification
        };
    });
    
    return {
        criterios: JSON.stringify(criteriosData),
        active_states: JSON.stringify(activeCriterionStates),
        comentario_general: document.getElementById('evaluationComments').value,
        elapsed_time: elapsedTime
    };
}

function initializeAutoSave() {
    autoSaveInterval = setInterval(() => {
        saveAutoSave();
    }, AUTO_SAVE_DELAY);
    
    window.addEventListener('beforeunload', function() {
        if (!evaluationSubmitted) {
            saveAutoSave(true);
        }
    });
}

function saveAutoSave(isSync = false) {
    if (evaluationSubmitted) return;
    
    const currentTime = Date.now();
    if (!isSync && (currentTime - lastSaveTime < 10000)) {
        return;
    }
    
    const evaluationData = prepareEvaluationData();
    
    if (isSync) {
        saveAutoSaveSync(evaluationData);
    } else {
        saveAutoSaveAsync(evaluationData);
    }
}

function saveAutoSaveAsync(evaluationData) {
    const formData = new FormData();
    formData.append('action', 'autosave');
    formData.append('token', token);
    formData.append('project_id', projectId);
    formData.append('criterios', evaluationData.criterios);
    formData.append('comentario_general', evaluationData.comentario_general);
    formData.append('active_states', evaluationData.active_states);
    formData.append('elapsed_time', evaluationData.elapsed_time);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result === 'OK') {
            lastSaveTime = Date.now();
            showAutoSaveIndicator();
        }
    })
    .catch(error => {
        console.error('Error en autoguardado:', error);
    });
}

function saveAutoSaveSync(evaluationData) {
    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    formData.append('action', 'autosave');
    formData.append('token', token);
    formData.append('project_id', projectId);
    formData.append('criterios', evaluationData.criterios);
    formData.append('comentario_general', evaluationData.comentario_general);
    formData.append('active_states', evaluationData.active_states);
    formData.append('elapsed_time', evaluationData.elapsed_time);
    
    xhr.open('POST', '', false);
    xhr.send(formData);
}

function showAutoSaveIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-50';
    indicator.innerHTML = `
        <div class="flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span class="text-sm">Guardado automáticamente</span>
        </div>
    `;
    
    document.body.appendChild(indicator);
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    
    setTimeout(() => {
        indicator.remove();
    }, 2000);
}

function submitEvaluation() {
    if (evaluationSubmitted) return;
    
    const submitButton = document.getElementById('submitEvaluation');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';
    }
    
    evaluationSubmitted = true;
    clearAllIntervals();
    
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = '';
    
    const evaluationData = prepareEvaluationData();
    
    const fields = {
        'action': 'submit_evaluation',
        'token': token,
        'project_id': projectId,
        'criterios': evaluationData.criterios,
        'comentario_general': evaluationData.comentario_general,
        'active_states': evaluationData.active_states,
        'elapsed_time': evaluationData.elapsed_time
    };
    
    Object.keys(fields).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        tempForm.appendChild(input);
    });
    
    document.body.appendChild(tempForm);
    tempForm.submit();
}

function forceAutoSubmitEvaluation() {
    if (evaluationSubmitted) return;
    
    evaluationSubmitted = true;
    clearAllIntervals();
    
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = '';
    
    const evaluationData = prepareEvaluationData();
    
    const fields = {
        'action': 'auto_submit',
        'token': token,
        'project_id': projectId,
        'criterios': evaluationData.criterios,
        'comentario_general': evaluationData.comentario_general,
        'active_states': evaluationData.active_states,
        'elapsed_time': evaluationData.elapsed_time
    };
    
    Object.keys(fields).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        tempForm.appendChild(input);
    });
    
    document.body.appendChild(tempForm);
    tempForm.submit();
}

function calculateTotalScore() {
    let total = 0;
    let evaluatedCount = 0;
    let activeCount = 0;
    
    filteredCriteriaIds.forEach(criterionId => {
        if (activeCriterionStates[criterionId] === true) {
            activeCount++;
            const score = evaluationScores[criterionId] || 0;
            if (score > 0) {
                total += score;
                evaluatedCount++;
            }
        }
    });
    
    const average = evaluatedCount > 0 ? total / evaluatedCount : 0;
    const totalScoreElement = document.getElementById('totalScore');
    if (totalScoreElement) totalScoreElement.textContent = `${average.toFixed(1)}/${escalaMaxima}`;
    
    const statusElement = document.getElementById('totalStatus');
    if (statusElement) {
        if (evaluatedCount === activeCount && activeCount > 0) {
            statusElement.textContent = 'Evaluación completa';
            statusElement.className = 'px-4 py-2 rounded-lg font-medium bg-green-100 text-green-800';
        } else if (evaluatedCount > 0) {
            statusElement.textContent = `Criterios evaluados: ${evaluatedCount}/${activeCount}`;
            statusElement.className = 'px-4 py-2 rounded-lg font-medium bg-blue-100 text-blue-800';
        } else if (activeCount > 0) {
            statusElement.textContent = `${activeCount} criterios activos`;
            statusElement.className = 'px-4 py-2 rounded-lg font-medium bg-yellow-100 text-yellow-800';
        } else {
            statusElement.textContent = 'Sin criterios activos';
            statusElement.className = 'px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-800';
        }
    }
    
    updateActiveCriteriaCount();
    updateCriteriaCounter();
}

function updateActiveCriteriaCount() {
    let activeCount = 0;
    filteredCriteriaIds.forEach(criterionId => {
        if (activeCriterionStates[criterionId] === true) {
            activeCount++;
        }
    });
    
    const activeCountElement = document.getElementById('activeCountDisplay');
    if (activeCountElement) activeCountElement.textContent = activeCount;
}

function updateCriteriaCounter() {
    let activeCount = 0;
    let evaluatedCount = 0;
    
    filteredCriteriaIds.forEach(criterionId => {
        if (activeCriterionStates[criterionId] === true) {
            activeCount++;
            const score = evaluationScores[criterionId] || 0;
            if (score > 0) {
                evaluatedCount++;
            }
        }
    });
    
    const evaluatedElement = document.getElementById('evaluatedCountDisplay');
    const totalActiveElement = document.getElementById('totalActiveDisplay');
    if (evaluatedElement) evaluatedElement.textContent = evaluatedCount;
    if (totalActiveElement) totalActiveElement.textContent = activeCount;
}

function initializeFormValues() {
    filteredCriteriaIds.forEach(criterionId => {
        const slider = document.querySelector(`[data-criterion-id="${criterionId}"] .criterion-slider`);
        const textarea = document.querySelector(`[data-criterion-id="${criterionId}"] .criterion-comment`);
        const scoreValueElement = document.getElementById(`score-value-${criterionId}`);
        const ratingTextElement = document.getElementById(`rating-text-${criterionId}`);
        const justificationElement = document.getElementById(`justification-text-${criterionId}`);
        
        if (slider) {
            slider.value = evaluationScores[criterionId] || 0;
        }
        
        if (scoreValueElement) {
            scoreValueElement.textContent = (evaluationScores[criterionId] || 0).toFixed(1);
        }
        
        if (ratingTextElement) {
            let ratingText = 'Regular';
            const categories = <?php echo json_encode($criteriaConfig['categorias_calificacion'] ?? []); ?>;
            for (const category of Object.values(categories)) {
                if ((evaluationScores[criterionId] || 0) >= category.minimo && (evaluationScores[criterionId] || 0) <= category.maximo) {
                    ratingText = category.nombre;
                    break;
                }
            }
            ratingTextElement.textContent = ratingText;
        }
        
        if (justificationElement) {
            const autoJustification = getJustificationForScore(criterionId, evaluationScores[criterionId] || 0);
            justificationElement.textContent = autoJustification;
        }
        
        if (textarea) {
            textarea.value = evaluationComments[criterionId] || '';
        }
        
        const container = document.querySelector(`[data-criterion-id="${criterionId}"]`);
        if (container) {
            const toggleBtn = container.querySelector('.criterion-toggle');
            const statusSpan = container.querySelector('.criterion-status');
            const contentDiv = container.querySelector('.criterion-content');
            
            const isActive = activeCriterionStates[criterionId] ?? true;
            
            if (toggleBtn) {
                toggleBtn.className = `criterion-toggle p-2 rounded-full transition-colors ${isActive ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'}`;
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.setAttribute('data-lucide', isActive ? 'check-circle' : 'x-circle');
                }
            }
            
            if (statusSpan) {
                statusSpan.className = `criterion-status px-2 py-1 rounded text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'}`;
                statusSpan.textContent = isActive ? 'Activo' : 'Inactivo';
            }
            
            if (contentDiv) {
                contentDiv.classList.toggle('opacity-50', !isActive);
            }
            
            if (slider) {
                slider.disabled = !isActive;
            }
            
            if (textarea) {
                textarea.disabled = !isActive;
            }
        }
    });
    
    const generalComments = document.getElementById('evaluationComments');
    if (generalComments) {
        generalComments.value = savedGeneralComments;
    }
    
    calculateTotalScore();
    updateActiveCriteriaCount();
    updateCriteriaCounter();
    
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    
    initializeFormValues();
    updateDocumentButton();
    
    const generalComments = document.getElementById('evaluationComments');
    if (generalComments) {
        let generalCommentsTimeout;
        generalComments.addEventListener('input', function() {
            clearTimeout(generalCommentsTimeout);
            generalCommentsTimeout = setTimeout(() => {
                saveAutoSave();
            }, 1000);
        });
    }
    
    const now = new Date();
    const diff = sessionEndTime - now;
    
    if (diff <= 0 && !evaluationSubmitted) {
        forceAutoSubmitEvaluation();
        return;
    }
    
    if (!evaluationSubmitted && diff > 0) {
        startTimeMonitoring();
        initializeAutoSave();
    }
    
    document.getElementById('evaluationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitEvaluation();
    });
});

window.addEventListener('beforeunload', function(e) {
    if (!evaluationSubmitted) {
        e.preventDefault();
        e.returnValue = '¿Estás seguro de que quieres salir? Tu evaluación no ha sido enviada.';
    }
});
</script>