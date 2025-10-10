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
            if (is_string($criteriosData)) {
                $criteriosData = json_decode($criteriosData, true);
            }
            
            $evaluationManager = new EvaluationManager($pdo);
            $reevaluationManager = new ReevaluationManager($pdo);
            
            if ($isReevaluate === '1') {
                $success = handleReevaluation($evaluationManager, $reevaluationManager, $projectId, $userId, $criteriosData, $comentarioGeneral, $reevaluationId);
                
                if ($success) {
                    $cacheKey = "autosave_{$userId}_{$projectId}";
                    $EvaluationCache->deleteAutoSave($cacheKey, 'autosave');
                    
                    $_SESSION['success_message'] = 'Re-evaluación guardada correctamente.';
                    
                    if ($reevaluationId) {
                        $reevaluationManager->markAsCompleted($reevaluationId);
                    }
                } else {
                    $_SESSION['error_message'] = 'Error al guardar la re-evaluación.';
                }
            } else {
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

$savedScores = [];
$savedComments = [];
$savedActiveStates = [];
$savedGeneralComments = '';

$cacheKey = "autosave_{$userId}_{$evaluationProjectId}";

if ($isReevaluate && $hasUserEvaluated) {
    if (!$canReevaluateDirectly) {
        $EvaluationCache->deleteAutoSave($cacheKey, 'autosave');
        showReevaluationRequestForm($project, $ultimaEvaluacion, $reevaluationStatus, $isWithin72Hours);
        return;
    }
    
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
} elseif ($hasUserEvaluated && !empty($userRatings)) {
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
} else {
    $cacheData = $EvaluationCache->getAutoSave($cacheKey, 'autosave');

    if ($cacheData && is_array($cacheData)) {
        $rawData = $cacheData;
        
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

if ($shouldShowRequestForm) {
    showReevaluationRequestForm($project, $ultimaEvaluacion, $reevaluationStatus, $isWithin72Hours);
    return;
}

showEvaluationForm($project, $isReevaluate, $reevaluationStatus, $criteriaConfig, $ultimaEvaluacion, $isWithin72Hours, $savedScores, $savedComments, $savedActiveStates, $savedGeneralComments);

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

function handleReevaluation(EvaluationManager $evaluationManager, ReevaluationManager $reevaluationManager, int $projectId, int $userId, array $criteriosData, string $comentarioGeneral, ?int $reevaluationId = null): bool {
    try {
        $existingRatings = $evaluationManager->getRatingsByUser($projectId, $userId);
        $existingSummary = $evaluationManager->getSummaryByUser($projectId, $userId);
        
        if (empty($existingRatings)) {
            throw new Exception("No se encontraron evaluaciones existentes para actualizar");
        }
        
        $modificationType = $reevaluationId ? 'aprobada' : 'automatica';
        $modificationId = $reevaluationManager->registerModificationAttempt($projectId, $userId, $modificationType, $reevaluationId);
        
        $updatedCriteria = [];
        foreach ($existingRatings as $existingRating) {
            $criterionId = normalizeCriterionId($existingRating['criterio_nombre']);
            
            if (isset($criteriosData[$criterionId])) {
                $newData = $criteriosData[$criterionId];
                $oldScore = floatval($existingRating['calificacion'] ?? 0);
                $newScore = floatval($newData['valor'] ?? 0);
                
                if ($oldScore != $newScore || $existingRating['observacion_personal'] != ($newData['comentario'] ?? '')) {
                    $updateData = [
                        'calificacion' => $newScore,
                        'observacion_personal' => $newData['comentario'] ?? '',
                        'estado' => 'reevaluado'
                    ];
                    
                    if ($evaluationManager->updateRating($existingRating['id'], $updateData)) {
                        $updatedCriteria[] = [
                            'criterio_id' => $existingRating['id'],
                            'criterio_nombre' => $existingRating['criterio_nombre'],
                            'puntuacion_anterior' => $oldScore,
                            'puntuacion_nueva' => $newScore,
                            'comentario_anterior' => $existingRating['observacion_personal'] ?? '',
                            'comentario_nuevo' => $newData['comentario'] ?? ''
                        ];
                    }
                }
            }
        }
        
        if ($existingSummary) {
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
            $oldAverageScore = floatval($existingSummary['calificacion_total'] ?? 0);
            
            $summaryData = [
                'comentario_general' => $comentarioGeneral,
                'calificacion_total' => $averageScore,
                'estado_evaluacion' => 'reevaluada',
                'tiempo_total' => $existingSummary['tiempo_total'] ?? null,
                'fecha_fin' => date('Y-m-d H:i:s')
            ];
            
            $evaluationManager->updateRatingSummary($existingSummary['id'], $summaryData);
            
            $reevaluationManager->completeModificationAttempt($modificationId, [
                'criterios_modificados' => $updatedCriteria,
                'puntuacion_total_anterior' => $oldAverageScore,
                'puntuacion_total_nueva' => $averageScore,
                'comentario_general_anterior' => $existingSummary['comentario_general'] ?? '',
                'comentario_general_nuevo' => $comentarioGeneral
            ]);
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
    const projectId = <?php echo $project['id']; ?>;
    const userId = <?php echo $_SESSION['userid']; ?>;
    const isReevaluate = <?php echo $isReevaluate ? 'true' : 'false'; ?>;
    const isWithin72Hours = <?php echo $isWithin72Hours ? 'true' : 'false'; ?>;
    const escalaMaxima = <?php echo $criteriaConfig['puntuacion']['valor_maximo'] ?? 10; ?>;

    let evaluationScores = <?php echo json_encode($savedScores); ?>;
    let evaluationComments = <?php echo json_encode($savedComments); ?>;
    let activeCriterionStates = <?php echo json_encode($savedActiveStates); ?>;
    let savedGeneralComments = "<?php echo addslashes($savedGeneralComments); ?>";

    const filteredCriteriaIds = <?php echo json_encode(array_column($filteredCriteria, 'id')); ?>;
    const criteriaConfig = <?php echo json_encode($filteredCriteria); ?>;

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

    let autoSaveInterval;
    let evaluationSubmitted = false;
    const AUTO_SAVE_DELAY = 30000;
    let lastSaveTime = 0;

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
            elapsed_time: 0
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
        
        if (isReevaluate) {
            const confirmed = confirm('¿Estás seguro de que quieres enviar la re-evaluación? Esta es tu ÚNICA oportunidad de cambio. Una vez enviada, no podrás modificarla nuevamente.');
            if (!confirmed) {
                return;
            }
        }
        
        const submitButton = document.getElementById('submitEvaluation');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';
        }
        
        evaluationSubmitted = true;
        clearInterval(autoSaveInterval);
        
        document.getElementById('evaluationForm').submit();
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
        
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitEvaluation();
        });
        
        if (!evaluationSubmitted) {
            initializeAutoSave();
        }
    });

    window.addEventListener('beforeunload', function(e) {
        if (!evaluationSubmitted) {
            e.preventDefault();
            e.returnValue = '¿Estás seguro de que quieres salir? Tu evaluación no ha sido enviada.';
        }
    });
    </script>
    <?php
}