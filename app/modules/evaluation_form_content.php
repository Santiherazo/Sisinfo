<?php
$evaluationProjectId = $_GET['project_id'] ?? null;
$isReevaluate = isset($_GET['reevaluate']) && $_GET['reevaluate'] == 'true';
$action = $_POST['action'] ?? '';

if (!defined('__PATH_CACHE__')) {
    define('__PATH_CACHE__', __DIR__ . '/../cache/');
}
if (!defined('AUTOSAVE_DIR')) {
    define('AUTOSAVE_DIR', __PATH_CACHE__ . 'autosave/');
}

if (!file_exists(__PATH_CACHE__)) mkdir(__PATH_CACHE__, 0755, true);
if (!file_exists(AUTOSAVE_DIR)) mkdir(AUTOSAVE_DIR, 0755, true);

// ==================== MANEJO DE SOLICITUDES POST ====================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'request_reevaluation') {
    $projectId = $_POST['project_id'] ?? null;
    $lastEvaluationId = $_POST['last_evaluation_id'] ?? null;
    $lastEvaluationDate = $_POST['last_evaluation_date'] ?? null;
    $motivoSolicitud = $_POST['motivo_solicitud'] ?? '';
    
    $userId = $_SESSION['userid'] ?? 0;
    
    if ($projectId && $lastEvaluationId && $lastEvaluationDate) {
        try {
            $reevaluationManager = new ReevaluationManager($pdo);
            
            if ($reevaluationManager->canRequestReevaluation($projectId, $userId, $lastEvaluationDate)) {
                $success = $reevaluationManager->requestReevaluation(
                    $projectId, 
                    $userId, 
                    $lastEvaluationId, 
                    $lastEvaluationDate, 
                    $motivoSolicitud
                );
                
                if ($success) {
                    $_SESSION['success_message'] = 'Solicitud de re-evaluación enviada correctamente al coordinador.';
                } else {
                    $_SESSION['error_message'] = 'Error al enviar la solicitud.';
                }
            } else {
                $_SESSION['error_message'] = 'No puedes solicitar re-evaluación. Ya tienes una solicitud activa o ha pasado el tiempo límite.';
            }
            
        } catch (Exception $e) {
            error_log("Error processing reevaluation request: " . $e->getMessage());
            $_SESSION['error_message'] = 'Error interno del sistema.';
        }
    }
    
    header("Location: " . evalcp_base() . "?module=evaluations&project_id=" . $projectId . "&reevaluate=true");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'autosave') {
    $userId = $_SESSION['userid'] ?? 0;
    $projectId = $_POST['project_id'] ?? null;
    
    if ($projectId && $userId) {
        $EvaluationCache = new Cache();
        $EvaluationCache->configure('file', __PATH_CACHE__)
                        ->setCachePath(AUTOSAVE_DIR, 'autosave');
        
        $result = handleAutoSave($EvaluationCache, $userId, $projectId, $_POST);
        if ($result) {
            echo 'OK';
        } else {
            echo 'ERROR';
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit_evaluation') {
    $projectId = $_POST['project_id'] ?? null;
    $isReevaluate = $_POST['is_reevaluate'] ?? '0';
    $reevaluationId = $_POST['reevaluation_id'] ?? null;
    $criteriosData = $_POST['criterios'] ?? [];
    $comentarioGeneral = $_POST['comentario_general'] ?? '';
    
    $userId = $_SESSION['userid'] ?? 0;
    
    if ($projectId && $userId) {
        try {
            // Si es string JSON, decodificar
            if (is_string($criteriosData)) {
                $criteriosData = json_decode($criteriosData, true);
            }
            
            $evaluationManager = new EvaluationManager($pdo);
            $reevaluationManager = new ReevaluationManager($pdo);
            
            if ($isReevaluate === '1') {
                // RE-EVALUACIÓN: Actualizar registros existentes
                $success = handleReevaluation($evaluationManager, $projectId, $userId, $criteriosData, $comentarioGeneral, $reevaluationId);
                
                if ($success) {
                    // Limpiar cache de autoguardado
                    $cacheKey = "autosave_{$userId}_{$projectId}";
                    $EvaluationCache->deleteAutoSave($cacheKey, 'autosave');
                    
                    $_SESSION['success_message'] = 'Re-evaluación guardada correctamente.';
                    
                    // Si fue una re-evaluación aprobada, actualizar el estado
                    if ($reevaluationId) {
                        $reevaluationManager->markAsCompleted($reevaluationId);
                    }
                } else {
                    $_SESSION['error_message'] = 'Error al guardar la re-evaluación.';
                }
            } else {
                // EVALUACIÓN NORMAL: Comportamiento original
                $success = handleNewEvaluation($evaluationManager, $projectId, $userId, $criteriosData, $comentarioGeneral);
                
                if ($success) {
                    $cacheKey = "autosave_{$userId}_{$projectId}";
                    $EvaluationCache->deleteAutoSave($cacheKey, 'autosave');
                    $_SESSION['success_message'] = 'Evaluación enviada correctamente.';
                } else {
                    $_SESSION['error_message'] = 'Error al enviar la evaluación.';
                }
            }
            
        } catch (Exception $e) {
            error_log("Error processing evaluation: " . $e->getMessage());
            $_SESSION['error_message'] = 'Error interno del sistema.';
        }
    }
    
    header("Location: " . evalcp_base() . "?module=evaluations");
    exit;
}

// ==================== INICIALIZACIÓN Y VALIDACIONES ====================

if (!$evaluationProjectId) {
    echo '<div class="bg-white rounded-xl shadow-sm p-6 text-center">';
    echo '<p class="text-red-600">Error: No se especificó el proyecto a evaluar</p>';
    echo '</div>';
    return;
}

$projectManager = new ProjectManager($pdo);
$evaluationManager = new EvaluationManager($pdo);
$reevaluationManager = new ReevaluationManager($pdo);

$EvaluationCache = new Cache();
$EvaluationCache->configure('file', __PATH_CACHE__)
                ->setCachePath(AUTOSAVE_DIR, 'autosave');

try {
    $criteriaConfig = loadConfig('evaluation_config');
} catch (Exception $e) {
    error_log("Error loading criteria config: " . $e->getMessage());
    $criteriaConfig = ['criterios' => []];
}

$userId = $_SESSION['userid'] ?? 0;

$projectData = $projectManager->getProject($evaluationProjectId);
if (!$projectData) {
    echo '<div class="bg-white rounded-xl shadow-sm p-6 text-center">';
    echo '<p class="text-red-600">Error: Proyecto no encontrado</p>';
    echo '</div>';
    return;
}

$isEvaluator = false;
$allProjects = $projectManager->getAllProjects();
foreach ($allProjects as $proj) {
    if ($proj['id'] == $evaluationProjectId && !empty($proj['reviewers'])) {
        foreach ($proj['reviewers'] as $reviewer) {
            if ($reviewer['id'] == $userId) {
                $isEvaluator = true;
                break 2;
            }
        }
    }
}

if (!$isEvaluator) {
    echo '<div class="bg-white rounded-xl shadow-sm p-6 text-center">';
    echo '<p class="text-red-600">Error: No tienes permisos para evaluar este proyecto</p>';
    echo '</div>';
    return;
}

$hasUserEvaluated = $evaluationManager->hasUserEvaluatedProject($projectData['id'], $userId);
$userRatings = $evaluationManager->getRatingsByUser($projectData['id'], $userId);
$userSummary = $evaluationManager->getSummaryByUser($projectData['id'], $userId);

$project = [
    'id' => $projectData['id'],
    'titulo' => $projectData['titulo'],
    'descripcion' => $projectData['descripcion'] ?? null,
    'linea_investigacion_id' => $projectData['linea_investigacion_id'],
    'fase' => $projectData['fase'],
    'version' => $projectData['version'],
    'calificado' => $hasUserEvaluated ? 1 : 0,
    'puntuacion' => $userSummary['calificacion_total'] ?? null,
    'user_ratings' => $userRatings,
    'user_summary' => $userSummary
];

$reevaluationStatus = $reevaluationManager->getReevaluationStatus($projectData['id'], $userId);
$ultimaEvaluacion = null;
$isWithin72Hours = false;

if ($hasUserEvaluated && !empty($userRatings)) {
    $lastRating = end($userRatings);
    $ultimaEvaluacion = $lastRating;
    $lastEvaluationDate = $ultimaEvaluacion['updated_at'] ?? $ultimaEvaluacion['created_at'];
    $isWithin72Hours = $reevaluationManager->isWithinReevaluationTime($lastEvaluationDate);
}

$shouldShowRequestForm = false;

if ($isReevaluate && $hasUserEvaluated) {
    $canReevaluateDirectly = $isWithin72Hours || $reevaluationManager->canUserReevaluate($projectData['id'], $userId);
    
    if (!$canReevaluateDirectly) {
        $shouldShowRequestForm = true;
    }
}

if ($shouldShowRequestForm) {
    showReevaluationRequestForm($project, $ultimaEvaluacion, $reevaluationStatus, $isWithin72Hours);
    return;
}

$savedScores = [];
$savedComments = [];
$savedActiveStates = [];
$savedGeneralComments = '';

$cacheKey = "autosave_{$userId}_{$evaluationProjectId}";

// SI HAY EVALUACIÓN EN BD, CARGAR DESDE AHÍ Y GUARDAR EN CACHE
if ($hasUserEvaluated && !empty($userRatings)) {
    foreach ($userRatings as $rating) {
        if (is_array($rating) && isset($rating['criterio_nombre'])) {
            $criterionId = normalizeCriterionId($rating['criterio_nombre']);
            $savedScores[$criterionId] = floatval($rating['calificacion'] ?? 0);
            $savedComments[$criterionId] = $rating['observacion_personal'] ?? '';
            $savedActiveStates[$criterionId] = true;
        }
    }
    
    if (!empty($userSummary)) {
        $savedGeneralComments = $userSummary['comentario_general'] ?? '';
    }

    // GUARDAR EN CACHE PARA FUTURAS CARGAS
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
            'justificacion' => ''
        ];
    }
    
    $EvaluationCache->storeAutoSave($cacheKey, $autoSaveData, 86400, 'autosave');
} 
// SI NO HAY EVALUACIÓN, CARGAR DESDE CACHE
else {
    $cacheData = $EvaluationCache->getAutoSave($cacheKey, 'autosave');

    if ($cacheData && is_array($cacheData)) {
        $rawData = $cacheData;
        
        // SI VIENE CON ESTRUCTURA metadata/data, EXTRAER SOLO DATA
        if (isset($rawData['data']) && is_array($rawData['data'])) {
            $rawData = $rawData['data'];
        }
        
        if (isset($rawData['criterios']) && is_array($rawData['criterios'])) {
            foreach ($rawData['criterios'] as $criterionId => $criterio) {
                $savedScores[$criterionId] = floatval($criterio['valor'] ?? 0);
                $savedComments[$criterionId] = $criterio['comentario'] ?? '';
            }
        }
        
        if (isset($rawData['active_states']) && is_array($rawData['active_states'])) {
            foreach ($rawData['active_states'] as $criterionId => $isActive) {
                $savedActiveStates[$criterionId] = (bool)$isActive;
            }
        }
        
        if (isset($rawData['comentario_general'])) {
            $savedGeneralComments = $rawData['comentario_general'];
        }
    }
}

showEvaluationForm($project, $isReevaluate, $reevaluationStatus, $criteriaConfig, $ultimaEvaluacion, $isWithin72Hours, $savedScores, $savedComments, $savedActiveStates, $savedGeneralComments);

// ==================== FUNCIONES AUXILIARES ====================

function normalizeCriterionId($criterionName) {
    $mapping = [
        'Criterio titulo' => 'titulo',
        'Criterio introduccion' => 'introduccion', 
        'Criterio planteamiento_problema' => 'planteamiento_problema',
        'Criterio justificacion' => 'justificacion',
        'Criterio objetivos' => 'objetivos',
        'Criterio marco_teorico' => 'marco_teorico',
        'Criterio metodologia' => 'metodologia',
        'Criterio resultados' => 'resultados',
        'Criterio sustentacion' => 'sustentacion'
    ];
    
    return $mapping[$criterionName] ?? strtolower(trim($criterionName));
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

function handleReevaluation(EvaluationManager $evaluationManager, int $projectId, int $userId, array $criteriosData, string $comentarioGeneral, ?int $reevaluationId = null): bool {
    try {
        // 1. Obtener las evaluaciones existentes del usuario para este proyecto
        $existingRatings = $evaluationManager->getRatingsByUser($projectId, $userId);
        $existingSummary = $evaluationManager->getSummaryByUser($projectId, $userId);
        
        if (empty($existingRatings)) {
            throw new Exception("No se encontraron evaluaciones existentes para actualizar");
        }
        
        // 2. Actualizar cada criterio existente
        foreach ($existingRatings as $existingRating) {
            $criterionId = normalizeCriterionId($existingRating['criterio_nombre']);
            
            if (isset($criteriosData[$criterionId])) {
                $newData = $criteriosData[$criterionId];
                
                // Actualizar el rating existente
                $updateData = [
                    'calificacion' => floatval($newData['valor'] ?? 0),
                    'observacion_personal' => $newData['comentario'] ?? '',
                    'estado' => 'reevaluado' // Marcar como re-evaluado
                ];
                
                $evaluationManager->updateRating($existingRating['id'], $updateData);
            }
        }
        
        // 3. Actualizar el summary existente
        if ($existingSummary) {
            // Calcular nueva calificación total
            $totalScore = 0;
            $activeCount = 0;
            
            foreach ($criteriosData as $criterionData) {
                $score = floatval($criterionData['valor'] ?? 0);
                if ($score > 0) {
                    $totalScore += $score;
                    $activeCount++;
                }
            }
            
            $averageScore = $activeCount > 0 ? $totalScore / $activeCount : 0;
            
            $summaryData = [
                'comentario_general' => $comentarioGeneral,
                'calificacion_total' => $averageScore,
                'estado_evaluacion' => 'reevaluada',
                'tiempo_total' => $existingSummary['tiempo_total'] ?? null,
                'fecha_fin' => date('Y-m-d H:i:s')
            ];
            
            $evaluationManager->updateRatingSummary($existingSummary['id'], $summaryData);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error in handleReevaluation: " . $e->getMessage());
        return false;
    }
}

function handleNewEvaluation(EvaluationManager $evaluationManager, int $projectId, int $userId, array $criteriosData, string $comentarioGeneral): bool {
    try {
        $sessionToken = session_id();
        $totalScore = 0;
        $activeCount = 0;
        
        // Insertar cada criterio
        foreach ($criteriosData as $criterionId => $criterionData) {
            $score = floatval($criterionData['valor'] ?? 0);
            
            if ($score > 0) {
                $totalScore += $score;
                $activeCount++;
            }
            
            $ratingData = [
                'session_token' => $sessionToken,
                'project_id' => $projectId,
                'evaluador_uid' => $userId,
                'criterio_nombre' => $criterionId,
                'criterio_valor' => $criterionData['justificacion'] ?? '',
                'calificacion' => $score,
                'observacion_personal' => $criterionData['comentario'] ?? ''
            ];
            
            $evaluationManager->addRating($ratingData);
        }
        
        // Insertar summary
        $averageScore = $activeCount > 0 ? $totalScore / $activeCount : 0;
        
        $summaryData = [
            'session_token' => $sessionToken,
            'project_id' => $projectId,
            'evaluador_uid' => $userId,
            'comentario_general' => $comentarioGeneral,
            'calificacion_total' => $averageScore,
            'tiempo_duracion' => null,
            'fecha_inicio' => date('Y-m-d H:i:s'),
            'fecha_fin' => date('Y-m-d H:i:s')
        ];
        
        return $evaluationManager->addRatingSummary($summaryData);
        
    } catch (Exception $e) {
        error_log("Error in handleNewEvaluation: " . $e->getMessage());
        return false;
    }
}

// ==================== FUNCIONES DE VISUALIZACIÓN ====================

function showReevaluationRequestForm($project, $ultimaEvaluacion, $reevaluationStatus, $isWithin72Hours) {
    ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Solicitar Re-evaluación</h2>
            <button onclick="window.location.href = window.location.pathname + '?module=evaluations'" 
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Volver a la lista
            </button>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 text-lg mb-2"><?php echo htmlspecialchars($project['titulo']); ?></h3>
            <p class="text-blue-700"><?php echo htmlspecialchars($project['descripcion'] ?? 'Sin descripción'); ?></p>
        </div>

        <?php if ($project['calificado'] && $ultimaEvaluacion): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <span class="text-yellow-600 mr-2">📅</span>
                <div>
                    <span class="font-medium text-yellow-800">Evaluación anterior:</span>
                    <span class="ml-2 text-yellow-700">
                        <?php echo $project['puntuacion']; ?>/10 - 
                        <?php 
                        echo date('d/m/Y H:i', strtotime($ultimaEvaluacion['updated_at'] ?? $ultimaEvaluacion['created_at'])); 
                        ?>
                    </span>
                </div>
            </div>
            <?php if (!$isWithin72Hours): ?>
            <div class="mt-2 text-sm text-yellow-700">
                <strong>Fuera del período de 72 horas:</strong> 
                Han pasado más de 72 horas desde tu última evaluación. Debes solicitar autorización al coordinador.
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form method="POST">
                <input type="hidden" name="action" value="request_reevaluation">
                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="last_evaluation_id" value="<?php echo $ultimaEvaluacion['id'] ?? ''; ?>">
                <input type="hidden" name="last_evaluation_date" value="<?php echo $ultimaEvaluacion['updated_at'] ?? $ultimaEvaluacion['created_at'] ?? ''; ?>">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de la solicitud</label>
                    <textarea name="motivo_solicitud" 
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                              rows="4" 
                              placeholder="Explica detalladamente por qué solicitas una re-evaluación..."
                              required></textarea>
                </div>
                
                <div class="flex justify-between items-center">
                    <button type="button" 
                            onclick="window.location.href = window.location.pathname + '?module=evaluations'"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function showEvaluationForm($project, $isReevaluate, $reevaluationStatus, $criteriaConfig, $ultimaEvaluacion, $isWithin72Hours, $savedScores, $savedComments, $savedActiveStates, $savedGeneralComments) {
    $filteredCriteria = [];
    $projectPhase = $project['fase'];
    
    if (isset($criteriaConfig['fases'][$projectPhase]['criterios'])) {
        foreach ($criteriaConfig['fases'][$projectPhase]['criterios'] as $criterionId => $criterion) {
            $criterion['id'] = $criterionId;
            $filteredCriteria[] = $criterion;
        }
    }
    ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                <?php 
                if ($isReevaluate) {
                    echo 'Re-evaluar Proyecto';
                    if ($reevaluationStatus) {
                        echo ' <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full">Aprobada por Coordinador</span>';
                    } elseif ($isWithin72Hours) {
                        echo ' <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Modificación dentro de 72h</span>';
                    }
                } else {
                    echo 'Evaluar Proyecto';
                }
                ?>
            </h2>
            <button onclick="window.location.href = window.location.pathname + '?module=evaluations'" 
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Volver a la lista
            </button>
        </div>

        <?php if ($isReevaluate): ?>
        <div id="singleOpportunityAlert" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-yellow-800">¡Atención! Solo tienes UNA oportunidad de cambio</h4>
                    <p class="text-yellow-700 text-sm mt-1">
                        Esta es tu única oportunidad para modificar la evaluación. Una vez enviada, no podrás realizar más cambios.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 text-lg mb-2"><?php echo htmlspecialchars($project['titulo']); ?></h3>
            <p class="text-blue-700 mb-3"><?php echo htmlspecialchars($project['descripcion'] ?? 'Sin descripción'); ?></p>
            <div class="flex flex-wrap gap-2">
                <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">Fase <?php echo $project['fase']; ?></span>
                <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full">v<?php echo $project['version']; ?></span>
            </div>
        </div>

        <?php if ($isReevaluate && $project['calificado'] && $ultimaEvaluacion): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <span class="text-yellow-600 mr-2">📅</span>
                <div>
                    <span class="font-medium text-yellow-800">Evaluación anterior:</span>
                    <span class="ml-2 text-yellow-700">
                        <?php echo $project['puntuacion']; ?>/10 - 
                        <?php 
                        echo date('d/m/Y H:i', strtotime($ultimaEvaluacion['updated_at'] ?? $ultimaEvaluacion['created_at'])); 
                        ?>
                    </span>
                </div>
            </div>
            <?php if ($reevaluationStatus): ?>
            <div class="mt-2 text-sm text-yellow-700">
                <strong>Solicitud aprobada por coordinador:</strong> 
                Válida hasta <?php echo date('d/m/Y H:i', strtotime($reevaluationStatus['fecha_limite'])); ?>
            </div>
            <?php elseif ($isWithin72Hours): ?>
            <div class="mt-2 text-sm text-yellow-700">
                <strong>Modificación automática:</strong> 
                Estás dentro de tu ventana de 72 horas para modificar la evaluación.
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-gray-50 rounded-lg p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h4 class="text-lg md:text-xl font-semibold text-gray-800">Formulario de Evaluación</h4>
                <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg" id="activeCriteriaCount">
                    <span id="activeCountDisplay"><?php echo count(array_filter($savedActiveStates)); ?></span> criterios activos
                </div>
            </div>

            <div id="totalScoreDisplay" class="flex items-center justify-between bg-white/20 rounded-xl p-4 border border-white/20 mb-6">
                <div>
                    <span class="text-sm text-gray-600">Puntaje Total:</span>
                    <span class="ml-2 text-2xl font-bold text-gray-900" id="totalScore">0/10</span>
                </div>
                <div id="totalStatus" class="px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-800">Sin evaluar</div>
            </div>

            <form id="evaluationForm" method="POST">
                <input type="hidden" name="action" value="submit_evaluation">
                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="is_reevaluate" value="<?php echo $isReevaluate ? '1' : '0'; ?>">
                <?php if ($isReevaluate && $reevaluationStatus): ?>
                <input type="hidden" name="reevaluation_id" value="<?php echo $reevaluationStatus['id']; ?>">
                <?php endif; ?>
                
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
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors" onclick="window.location.href='?module=evaluations'">Cancelar</button>
                    <button type="submit" id="submitEvaluation" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <?php 
                        if ($isReevaluate) {
                            echo $reevaluationStatus ? 'Guardar Re-evaluación Aprobada' : 'Guardar Modificación';
                        } else {
                            echo 'Enviar evaluación';
                        }
                        ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // ... (todo el código JavaScript permanece igual) ...
    </script>
    <?php
}