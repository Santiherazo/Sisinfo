<?php
if (!defined('EVALUATION_SESSION_FIELD_ID')) {
    define('EVALUATION_SESSION_FIELD_ID', 'id');
}

$isEvaluationMode = isset($_GET['project_id']) && !empty($_GET['project_id']);
$evaluationProjectId = $_GET['project_id'] ?? null;
$isReevaluate = isset($_GET['reevaluate']) && $_GET['reevaluate'] == 'true';

if ($isEvaluationMode) {
    include 'evaluation_form_content.php';
    return;
}

$logger = new ErrorLogger();
$projectManager = new ProjectManager($pdo);
$sessionManager = new EvaluationSessionManager($pdo, $logger);
$evaluationManager = new EvaluationManager($pdo);
$researchManager = new researchLineManager($pdo);
$filterCategory = $researchManager->getAllActivas();
$criteriaConfig = loadConfig('evaluation_config');
$uploadManager = new UploadManager();

$userId = $_SESSION['userid'] ?? 0;

$allProjects = $projectManager->getAllProjects();
$userEvaluatedProjects = $evaluationManager->getUserEvaluatedProjects($userId);

$lineasMap = [];
foreach ($filterCategory as $linea) {
    $lineasMap[$linea['id']] = $linea['nombre'];
}

function getTotalCriteriaCount($config) {
    $count = 0;
    
    if (isset($config['criterios']) && is_array($config['criterios'])) {
        $count = count($config['criterios']);
    }
    
    if ($count === 0 && isset($config['categorias']) && is_array($config['categorias'])) {
        foreach ($config['categorias'] as $categoria) {
            if (isset($categoria['criterios']) && is_array($categoria['criterios'])) {
                $count += count($categoria['criterios']);
            }
        }
    }
    
    return $count > 0 ? $count : 9;
}

$evaluatorProjects = [];
foreach ($allProjects as $project) {
    $isEvaluator = false;
    if (!empty($project['reviewers'])) {
        foreach ($project['reviewers'] as $reviewer) {
            if ($reviewer['id'] == $userId) {
                $isEvaluator = true;
                break;
            }
        }
    }
    
    if ($isEvaluator) {
        $evaluatorProjects[] = $project;
    }
}

$processedProjects = [];
foreach ($evaluatorProjects as $project) {
    $documents = [];
    $projectDir = $project['directorio'] ?? null;
    
    if ($projectDir) {
        $basePath = __PATH_UPLOADS__ . 'docs/projects/' . $projectDir . '/';
        if (is_dir($basePath)) {
            try {
                $files = $uploadManager->listAllContents($basePath);
                foreach ($files as $file) {
                    $filePath = $basePath . $file;
                    if (file_exists($filePath)) {
                        $relativePath = 'docs/projects/' . $projectDir . '/' . $file;
                        $documents[] = [
                            'id' => md5($file),
                            'name' => $file,
                            'path' => $relativePath,
                            'full_path' => $filePath,
                            'size' => filesize($filePath),
                            'url' => $handler->getDocumentUrl($relativePath),
                            'previewable' => $handler->canPreviewInBrowser($file),
                            'size_formatted' => $handler->getFileSize($relativePath)
                        ];
                    }
                }
            } catch (Exception $e) {
                error_log("Error reading project files for project {$project['id']}: " . $e->getMessage());
            }
        }
    }
    
    $lineaNombre = $lineasMap[$project['linea_investigacion_id']] ?? 'Línea ' . $project['linea_investigacion_id'];

    $hasUserEvaluated = $evaluationManager->hasUserEvaluatedProject($project['id'], $userId);
    $ratingsSummary = $evaluationManager->getRatingSummary($project['id']);
    $userRatings = $evaluationManager->getRatingsByUser($project['id'], $userId);
    $userSummary = $evaluationManager->getSummaryByUser($project['id'], $userId);
    
    $criteriosEvaluados = 0;
    $totalCriterios = getTotalCriteriaCount($criteriaConfig);
    
    if ($hasUserEvaluated && !empty($userRatings)) {
        $uniqueCriterios = [];
        foreach ($userRatings as $rating) {
            if (isset($rating[RATINGS_CRITERIO_NOMBRE])) {
                $uniqueCriterios[$rating[RATINGS_CRITERIO_NOMBRE]] = true;
            }
        }
        $criteriosEvaluados = count($uniqueCriterios);
        if ($criteriosEvaluados > $totalCriterios) {
            $criteriosEvaluados = $totalCriterios;
        }
    }
    
    $userRatingDetails = [];
    $lastEvaluationDate = null;
    if ($hasUserEvaluated) {
        $allRatings = $evaluationManager->getAllRatingsWithSummaries([
            'project_id' => $project['id'],
            'evaluador_uid' => $userId,
            'estado' => 'evaluado'
        ]);
        
        foreach ($allRatings as $rating) {
            if ($rating[RATINGS_EVALUADOR_UID] == $userId) {
                $userRatingDetails[] = [
                    'criterio' => $rating[RATINGS_CRITERIO_NOMBRE],
                    'justificacion' => $rating[RATINGS_CRITERIO_VALOR] ?? '',
                    'puntuacion' => $rating[RATINGS_CALIFICACION],
                    'comentarios' => $rating[RATINGS_OBSERVACION_PERSONAL] ?? ''
                ];
                
                $ratingDate = $rating[RATINGS_UPDATED_AT] ?? $rating[RATINGS_CREATED_AT];
                if (!$lastEvaluationDate || strtotime($ratingDate) > strtotime($lastEvaluationDate)) {
                    $lastEvaluationDate = $ratingDate;
                }
            }
        }
    }
    
    $canReevaluate = false;
    $hoursRemaining = 0;
    
    if ($hasUserEvaluated && !empty($userRatings) && $lastEvaluationDate) {
        $lastEvaluationTime = strtotime($lastEvaluationDate);
        $seventyTwoHoursLater = $lastEvaluationTime + (72 * 60 * 60);
        $currentTime = time();
        
        $canReevaluate = ($currentTime < $seventyTwoHoursLater);
        $hoursRemaining = max(0, floor(($seventyTwoHoursLater - $currentTime) / 3600));
    }

    $processedProjects[] = [
        'id' => $project['id'],
        'titulo' => $project['titulo'],
        'descripcion' => $project['descripcion'] ?? null,
        'palabras_clave' => $project['palabras_clave'] ?? null,
        'linea_investigacion_id' => $project['linea_investigacion_id'],
        'linea_nombre' => $lineaNombre,
        'fase' => $project['fase'],
        'version' => $project['version'],
        'fecha_presentacion' => $project['fecha_presentacion'] ?? null,
        'hora_programada' => $project['hora_programada'] ?? null,
        'timer_segundos' => $project['timer_segundos'] ?? 0,
        'documentos' => $documents,
        'calificado' => $hasUserEvaluated ? 1 : 0,
        'sesion_activa' => false,
        'usuario_en_sesion' => false,
        'puntuacion' => $userSummary[RATING_SUMMARY_CALIFICACION_TOTAL] ?? $ratingsSummary[RATING_SUMMARY_CALIFICACION_TOTAL] ?? null,
        'estado' => $project['estado'] ?? 'nuevo',
        'ratings_summary' => $ratingsSummary,
        'user_ratings' => $userRatings,
        'user_rating_details' => $userRatingDetails,
        'user_summary' => $userSummary,
        'puede_reevaluar' => $canReevaluate,
        'horas_restantes' => $hoursRemaining,
        'ultima_evaluacion' => !empty($userRatings) ? end($userRatings) : null,
        'fecha_ultima_evaluacion' => $lastEvaluationDate,
        'criterios_evaluados' => $criteriosEvaluados,
        'total_criterios' => $totalCriterios
    ];
}

$projectsJson = json_encode($processedProjects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

$autoViewProjectId = $_GET['data-project-id'] ?? null;
if ($autoViewProjectId) {
    $autoViewProjectId = intval($autoViewProjectId);
    $projectExists = false;
    foreach ($processedProjects as $project) {
        if ($project['id'] == $autoViewProjectId) {
            $projectExists = true;
            break;
        }
    }
    if (!$projectExists) {
        $autoViewProjectId = null;
    }
}

$completedEvaluations = 0;
$pendingEvaluations = 0;
$totalAverage = 0;

foreach ($processedProjects as $project) {
    if ($project['calificado']) {
        $completedEvaluations++;
        $totalAverage += $project['puntuacion'] ?? 0;
    } else {
        $pendingEvaluations++;
    }
}

$generalAverage = $completedEvaluations > 0 ? $totalAverage / $completedEvaluations : 0;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-800">Mis Evaluaciones Asignadas</h2>
        <div class="flex flex-wrap gap-2">
            <select id="filterLinea" class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Todas las líneas</option>
                <?php foreach ($filterCategory as $linea): ?>
                <option value="<?php echo $linea['id']; ?>"><?php echo htmlspecialchars($linea['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterEstado" class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="completada">Completadas</option>
                <option value="pendiente">Pendientes</option>
                <option value="reevaluacion">Re-evaluación disponible</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Proyecto</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Versión</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Fecha Presentación</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Criterios Evaluados</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Calificación</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processedProjects as $project): ?>
                <?php
                $evaluationStatus = $project['calificado'] ? 'Completada' : 'Pendiente';
                $statusClass = $project['calificado'] ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
                
                if ($project['calificado'] && $project['puede_reevaluar']) {
                    $evaluationStatus = 'Re-evaluación disponible';
                    $statusClass = 'bg-blue-100 text-blue-800';
                }
                
                $scoreColorClass = 'text-gray-400';
                $progressColor = 'bg-gray-200';
                if ($project['calificado']) {
                    $scoreColorClass = ($project['puntuacion'] < 3) ? 'text-red-600' : 'text-green-600';
                    $progressColor = ($project['puntuacion'] < 3) ? 'bg-red-500' : 'bg-green-500';
                }
                ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50 project-row" 
                    data-linea="<?php echo $project['linea_investigacion_id']; ?>"
                    data-estado="<?php echo $project['calificado'] ? ($project['puede_reevaluar'] ? 'reevaluacion' : 'completada') : 'pendiente'; ?>">
                    <td class="py-4">
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($project['titulo']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($project['linea_nombre']); ?></p>
                            <p class="text-sm text-gray-500">Fase <?php echo $project['fase']; ?></p>
                        </div>
                    </td>
                    <td class="py-4">
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full">
                            v<?php echo $project['version']; ?>
                        </span>
                    </td>
                    <td class="py-4 text-sm text-gray-600">
                        <?php echo $project['fecha_presentacion'] ? date('d M, Y', strtotime($project['fecha_presentacion'])) : 'No programada'; ?>
                    </td>
                    <td class="py-4">
                        <span class="text-xs font-medium px-3 py-1 rounded-full <?php echo $statusClass; ?>">
                            <?php echo $project['criterios_evaluados'] . '/' . $project['total_criterios']; ?>
                        </span>
                    </td>
                    <td class="py-4">
                        <?php if ($project['calificado']): ?>
                        <div class="flex items-center">
                            <span class="text-sm font-medium <?php echo $scoreColorClass; ?> mr-2">
                                <?php echo number_format($project['puntuacion'], 1); ?>
                            </span>
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="<?php echo $progressColor; ?> h-2 rounded-full" style="width: <?php echo ($project['puntuacion'] ?? 0) * 10; ?>%"></div>
                            </div>
                        </div>
                        <?php else: ?>
                        <span class="text-sm text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4">
                        <div class="flex space-x-2">
                            <button class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors view-project" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    title="Ver Proyecto">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <?php if (!$project['calificado']): ?>
                            <button class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors evaluate-project" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    data-reevaluate="false"
                                    title="Evaluar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php elseif ($project['calificado']): ?>
                            <button class="p-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition-colors evaluate-project" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    data-reevaluate="true"
                                    title="Editar Evaluación">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($processedProjects)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        <div class="text-4xl text-gray-300 mb-2"><i class="fas fa-clipboard-list"></i></div>
                        <p>No tienes proyectos asignados para evaluar</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">Mostrando <?php echo count($processedProjects); ?> proyectos asignados</p>
        <div class="flex space-x-2">
            <button class="px-3 py-1 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-3 py-1 rounded-lg bg-primary-600 text-white">1</button>
            <button class="px-3 py-1 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-sm p-6 border border-blue-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
            <span class="bg-blue-100 p-2 rounded-lg mr-3"><i class="fas fa-chart-bar text-blue-600"></i></span>
            Resumen de Mis Evaluaciones
        </h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-blue-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <span class="text-blue-600 bg-blue-100 p-2 rounded-lg mr-3"><i class="fas fa-clipboard-list"></i></span>
                    <div>
                        <span class="text-gray-700 font-medium">Total Proyectos</span>
                        <p class="text-xs text-gray-500">Asignados para evaluar</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-blue-600"><?php echo count($processedProjects); ?></span>
            </div>
            <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-green-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <span class="text-green-600 bg-green-100 p-2 rounded-lg mr-3"><i class="fas fa-check-circle"></i></span>
                    <div>
                        <span class="text-gray-700 font-medium">Completadas</span>
                        <p class="text-xs text-gray-500">Evaluaciones finalizadas</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-green-600"><?php echo $completedEvaluations; ?></span>
            </div>
            <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-orange-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <span class="text-orange-600 bg-orange-100 p-2 rounded-lg mr-3"><i class="fas fa-clock"></i></span>
                    <div>
                        <span class="text-gray-700 font-medium">Pendientes</span>
                        <p class="text-xs text-gray-500">Por evaluar</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-orange-600"><?php echo $pendingEvaluations; ?></span>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-white to-purple-50 rounded-xl shadow-sm p-6 border border-purple-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
            <span class="bg-purple-100 p-2 rounded-lg mr-3"><i class="fas fa-bolt text-purple-600"></i></span>
            Acciones Rápidas
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <button class="flex flex-col items-center justify-center p-5 bg-white border border-blue-200 rounded-xl text-blue-700 hover:bg-blue-50 hover:shadow-md transition-all duration-200 group"
                    onclick="showHelp()">
                <i class="fas fa-question-circle text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-semibold">Ayuda</span>
                <span class="text-xs text-gray-500 mt-1">Guía de uso</span>
            </button>
            <button class="flex flex-col items-center justify-center p-5 bg-white border border-purple-200 rounded-xl text-purple-700 hover:bg-purple-50 hover:shadow-md transition-all duration-200 group"
                    onclick="resetFilters()">
                <i class="fas fa-sync-alt text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-semibold">Actualizar</span>
                <span class="text-xs text-gray-500 mt-1">Limpiar filtros</span>
            </button>
        </div>
    </div>
</div>

<div id="projectDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-6xl w-full max-h-[95vh] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Detalles del Proyecto y Evaluación</h3>
                <button class="text-gray-400 hover:text-gray-600 text-2xl" onclick="closeProjectDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto flex-1" id="projectDetailsContent">
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end flex-shrink-0">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 mr-2" onclick="closeProjectDetailsModal()">
                Cerrar
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 evaluate-from-modal hidden" id="evaluateFromModal">
                <i class="fas fa-edit mr-2"></i>Evaluar Proyecto
            </button>
            <button class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 edit-from-modal hidden" id="editFromModal">
                <i class="fas fa-edit mr-2"></i>Editar Evaluación
            </button>
        </div>
    </div>
</div>

<div id="confirmEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 p-3 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Confirmar Edición</h3>
            </div>
            <p class="text-gray-600 mb-4">
                ¿Está seguro que desea editar esta evaluación? 
                <span class="font-semibold text-orange-600">Solo tendrá un intento para realizar correcciones.</span>
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i class="fas fa-clock text-yellow-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Importante:</p>
                        <p class="text-sm text-yellow-700 mt-1">
                            Dispone de <span class="font-bold" id="hoursRemainingText">72 horas</span> para realizar correcciones. 
                            Pasado este tiempo, deberá solicitar una revisión especial al administrador.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 flex justify-end space-x-3">
            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors" 
                    onclick="closeConfirmEditModal()">
                Cancelar
            </button>
            <button class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors" 
                    id="confirmEditButton">
                <i class="fas fa-edit mr-2"></i>Sí, Editar Evaluación
            </button>
        </div>
    </div>
</div>

<script>
const projectsData = <?php echo $projectsJson; ?>;
let pendingEditProjectId = null;

function checkAutoOpenModal() {
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('data-project-id');
    
    if (projectId) {
        const project = projectsData.find(p => p.id == projectId);
        if (project) {
            setTimeout(() => {
                showProjectDetails(projectId);
            }, 100);
        }
    }
}

document.getElementById('filterLinea').addEventListener('change', filterProjects);
document.getElementById('filterEstado').addEventListener('change', filterProjects);

function filterProjects() {
    const lineaFilter = document.getElementById('filterLinea').value;
    const estadoFilter = document.getElementById('filterEstado').value;
    const projectRows = document.querySelectorAll('.project-row');
    
    let visibleCount = 0;
    
    projectRows.forEach(row => {
        const projectLinea = row.getAttribute('data-linea');
        const projectEstado = row.getAttribute('data-estado');
        
        const showLinea = !lineaFilter || projectLinea === lineaFilter;
        const showEstado = !estadoFilter || projectEstado === estadoFilter;
        
        if (showLinea && showEstado) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const counterElement = document.querySelector('.flex.justify-between.items-center.mt-6.pt-4 p:first-child');
    if (counterElement) {
        counterElement.textContent = `Mostrando ${visibleCount} de ${projectsData.length} proyectos`;
    }
}

document.querySelectorAll('.evaluate-project[data-reevaluate="false"]').forEach(button => {
    button.addEventListener('click', function() {
        const projectId = this.getAttribute('data-project-id');
        const url = new URL(window.location.href);
        url.searchParams.set('project_id', projectId);
        window.location.href = url.toString();
    });
});

document.querySelectorAll('.evaluate-project[data-reevaluate="true"]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const projectId = this.getAttribute('data-project-id');
        showConfirmEditModal(projectId);
    });
});

document.querySelectorAll('.view-project').forEach(button => {
    button.addEventListener('click', function() {
        const projectId = this.getAttribute('data-project-id');
        showProjectDetails(projectId);
    });
});

function showConfirmEditModal(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project) return;
    
    pendingEditProjectId = projectId;
    
    const hoursRemainingText = document.getElementById('hoursRemainingText');
    if (project.horas_restantes > 0) {
        hoursRemainingText.textContent = project.horas_restantes + ' horas';
    } else {
        hoursRemainingText.textContent = '72 horas';
    }
    
    closeProjectDetailsModal();
    
    setTimeout(() => {
        document.getElementById('confirmEditModal').classList.remove('hidden');
        
        const confirmButton = document.getElementById('confirmEditButton');
        confirmButton.onclick = function() {
            proceedToEdit(projectId);
        };
    }, 300);
}

function closeConfirmEditModal() {
    document.getElementById('confirmEditModal').classList.add('hidden');
    pendingEditProjectId = null;
}

function proceedToEdit(projectId) {
    const url = new URL(window.location.href);
    url.searchParams.set('project_id', projectId);
    url.searchParams.set('reevaluate', 'true');
    window.location.href = url.toString();
}

function showProjectDetails(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project) return;
    
    pendingEditProjectId = projectId;
    
    const projectDetailsContent = document.getElementById('projectDetailsContent');
    const evaluateButton = document.getElementById('evaluateFromModal');
    const editButton = document.getElementById('editFromModal');
    
    evaluateButton.classList.add('hidden');
    editButton.classList.add('hidden');
    
    let detailsHTML = `
        <div class="space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                <h4 class="text-xl font-bold text-gray-800 mb-2">${project.titulo}</h4>
                <p class="text-gray-700 mb-4">${project.descripcion || 'Sin descripción'}</p>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">${project.linea_nombre}</span>
                    <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">Fase ${project.fase}</span>
                    <span class="bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">v${project.version}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Información General
                    </h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fecha presentación:</span>
                            <span class="font-medium text-gray-700">${project.fecha_presentacion ? new Date(project.fecha_presentacion).toLocaleDateString() : 'No programada'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Palabras clave:</span>
                            <span class="font-medium text-gray-700">${project.palabras_clave || 'No especificadas'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-medium ${project.estado === 'activo' ? 'text-green-600' : 'text-gray-600'}">${project.estado}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i> Estado de Evaluación
                    </h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Calificado:</span>
                            <span class="font-medium ${project.calificado ? 'text-green-600' : 'text-orange-600'}">
                                ${project.calificado ? 'Sí' : 'No'}
                            </span>
                        </div>
                        ${project.calificado ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Puntuación:</span>
                            <span class="font-medium ${project.puntuacion < 3 ? 'text-red-600' : 'text-green-600'}">${project.puntuacion}/10</span>
                        </div>
                        ` : ''}
                        <div class="flex justify-between">
                            <span class="text-gray-600">Criterios evaluados:</span>
                            <span class="font-medium text-gray-700">${project.criterios_evaluados}/${project.total_criterios}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Re-evaluación:</span>
                            <span class="font-medium ${project.puede_reevaluar ? 'text-blue-600' : 'text-gray-600'}">
                                ${project.puede_reevaluar ? 'Disponible' : 'No disponible'}
                            </span>
                        </div>
                        ${project.fecha_ultima_evaluacion ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Última evaluación:</span>
                            <span class="font-medium text-gray-700">${new Date(project.fecha_ultima_evaluacion).toLocaleDateString()}</span>
                        </div>
                        ` : ''}
                        ${project.puede_reevaluar && project.horas_restantes > 0 ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tiempo restante:</span>
                            <span class="font-medium text-blue-600">${project.horas_restantes} horas</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            ${project.calificado && project.user_rating_details && project.user_rating_details.length > 0 ? `
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-star mr-2"></i> Detalles de Mi Evaluación
                </h5>
                <div class="space-y-4">
                    ${project.user_rating_details.map(rating => `
                        <div class="bg-gray-50 rounded-lg border p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h6 class="font-semibold text-gray-800 text-sm">${rating.criterio}</h6>
                                </div>
                                <div class="flex items-center ml-4">
                                    <span class="text-sm font-semibold ${rating.puntuacion < 3 ? 'text-red-600' : 'text-green-600'} mr-2">
                                        ${rating.puntuacion}/10
                                    </span>
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="${rating.puntuacion < 3 ? 'bg-red-500' : 'bg-green-500'} h-2 rounded-full" style="width: ${rating.puntuacion * 10}%"></div>
                                    </div>
                                </div>
                            </div>
                            ${rating.justificacion ? `
                            <div class="mt-2">
                                <p class="text-xs font-medium text-gray-700">Justificación:</p>
                                <p class="text-xs text-gray-600 bg-white p-2 rounded border mt-1">${rating.justificacion}</p>
                            </div>
                            ` : ''}
                            ${rating.comentarios ? `
                            <div class="mt-2">
                                <p class="text-xs font-medium text-gray-700">Comentarios adicionales:</p>
                                <p class="text-xs text-gray-600 bg-white p-2 rounded border mt-1">${rating.comentarios}</p>
                            </div>
                            ` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${project.user_summary && project.user_summary.comentario_general ? `
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-comment mr-2"></i> Mi Comentario General
                </h5>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded border">${project.user_summary.comentario_general}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    projectDetailsContent.innerHTML = detailsHTML;
    
    if (!project.calificado) {
        evaluateButton.classList.remove('hidden');
        evaluateButton.onclick = function() {
            const url = new URL(window.location.href);
            url.searchParams.set('project_id', project.id);
            window.location.href = url.toString();
        };
    } else if (project.calificado) {
        editButton.classList.remove('hidden');
        editButton.onclick = function(e) {
            e.preventDefault();
            showConfirmEditModal(project.id);
        };
    }
    
    document.getElementById('projectDetailsModal').classList.remove('hidden');
}

function closeProjectDetailsModal() {
    document.getElementById('projectDetailsModal').classList.add('hidden');
}

function showHelp() {
    const helpContent = `Guía de Evaluación

Ver Proyecto: Ver detalles completos del proyecto y evaluación
Evaluar: Completar evaluación (proyectos pendientes)
Editar Evaluación: Modificar evaluación existente

Estados:
Completada - Evaluación finalizada
Pendiente - Por evaluar  
Re-evaluación - Puede modificar su evaluación

Puntuaciones:
Rojo - Menor a 3.0
Verde - 3.0 o superior`;
    alert(helpContent);
}

function resetFilters() {
    document.getElementById('filterLinea').value = '';
    document.getElementById('filterEstado').value = '';
    filterProjects();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProjectDetailsModal();
        closeConfirmEditModal();
    }
});

document.getElementById('confirmEditModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmEditModal();
    }
});

document.getElementById('projectDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProjectDetailsModal();
    }
});

checkAutoOpenModal();
</script>

<style>
#confirmEditModal, #projectDetailsModal {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>