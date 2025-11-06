<?php
$logger = new ErrorLogger();
$projectManager = new ProjectManager($pdo);
$sessionManager = new EvaluationSessionManager($pdo, $logger);
$evaluationManager = new EvaluationManager($pdo);
$researchManager = new researchLineManager($pdo);
$userManager = new UserManager($pdo);
$filterCategory = $researchManager->getAllActivas();
$criteriaConfig = loadConfig('evaluation_config');
$uploadManager = new UploadManager();

$allProjects = $projectManager->getAllProjects();
$allEvaluations = $evaluationManager->getAllRatingsWithSummariesSimple(1000);

$allUsers = [];
try {
    $usersData = $userManager->getAllUsers();
    foreach ($usersData as $user) {
        if (isset($user['id'])) {
            $allUsers[$user['id']] = $user;
        }
    }
} catch (Exception $e) {
    error_log("Error obteniendo usuarios: " . $e->getMessage());
}

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

$processedProjects = [];
foreach ($allProjects as $project) {
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

    $projectEvaluations = [];
    $evaluators = [];
    $evaluationDetails = [];
    
    foreach ($allEvaluations as $evaluation) {
        if ($evaluation[RATINGS_PROJECT_ID] == $project['id']) {
            $projectEvaluations[] = $evaluation;
            
            $evaluatorId = $evaluation[RATINGS_EVALUADOR_UID] ?? null;
            if ($evaluatorId && !in_array($evaluatorId, $evaluators)) {
                $evaluators[] = $evaluatorId;
            }
            
            if ($evaluatorId) {
                if (!isset($evaluationDetails[$evaluatorId])) {
                    $evaluationDetails[$evaluatorId] = [
                        'user_info' => $allUsers[$evaluatorId] ?? ['nombre' => 'Usuario ' . $evaluatorId],
                        'ratings' => []
                    ];
                }
                
                $evaluationDetails[$evaluatorId]['ratings'][] = [
                    'criterio' => $evaluation[RATINGS_CRITERIO_NOMBRE] ?? '',
                    'puntuacion' => $evaluation[RATINGS_CALIFICACION] ?? 0,
                    'justificacion' => $evaluation[RATINGS_CRITERIO_VALOR] ?? '',
                    'comentarios' => $evaluation[RATINGS_OBSERVACION_PERSONAL] ?? '',
                    'fecha' => $evaluation[RATINGS_UPDATED_AT] ?? $evaluation[RATINGS_CREATED_AT] ?? ''
                ];
            }
        }
    }
    
    $hasEvaluations = !empty($projectEvaluations);
    $totalEvaluators = count($evaluators);
    
    $activeSessions = $sessionManager->getActiveSessionsByProject($project['id']);
    $hasActiveSession = !empty($activeSessions);
    
    $sessionParticipants = [];
    $sessionProgress = [];
    
    foreach ($activeSessions as $session) {
        $sessionId = $session[EVALUATION_SESSION_FIELD_ID];
        
        $participants = $sessionManager->getSessionParticipants($sessionId);
        $progress = $sessionManager->getSessionProgress($sessionId);
        
        $sessionParticipants[$sessionId] = $participants;
        $sessionProgress[$sessionId] = $progress;
        
        foreach ($sessionParticipants[$sessionId] as &$participant) {
            $userId = $participant['id'] ?? null;
            if ($userId) {
                $participant['user_info'] = $allUsers[$userId] ?? ['nombre' => 'Usuario ' . $userId];
            } else {
                $participant['user_info'] = ['nombre' => 'Usuario desconocido'];
            }
        }
    }
    
    $allRatings = $evaluationManager->getRatingsByProject($project['id']);
    $allSummaries = $evaluationManager->getRatingSummariesByProject($project['id']);
    
    $projectStats = $evaluationManager->getProjectEvaluationStats($project['id']);
    $averageScore = $projectStats['average_score'] ?? 0;
    
    $criteriosEvaluadosGlobal = 0;
    if (!empty($allRatings)) {
        $uniqueCriterios = [];
        foreach ($allRatings as $rating) {
            if (isset($rating[RATINGS_CRITERIO_NOMBRE])) {
                $uniqueCriterios[$rating[RATINGS_CRITERIO_NOMBRE]] = true;
            }
        }
        $criteriosEvaluadosGlobal = count($uniqueCriterios);
        $totalCriterios = getTotalCriteriaCount($criteriaConfig);
        if ($criteriosEvaluadosGlobal > $totalCriterios) {
            $criteriosEvaluadosGlobal = $totalCriterios;
        }
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
        'evaluado' => $hasEvaluations ? 1 : 0,
        'total_evaluadores' => $totalEvaluators,
        'puntuacion_promedio' => $averageScore,
        'evaluaciones' => $projectEvaluations,
        'all_ratings' => $allRatings,
        'all_summaries' => $allSummaries,
        'evaluation_details' => $evaluationDetails,
        'criterios_evaluados' => $criteriosEvaluadosGlobal,
        'total_criterios' => getTotalCriteriaCount($criteriaConfig),
        'sesion_activa' => $hasActiveSession,
        'total_sesiones' => count($activeSessions),
        'sesiones_activas' => $activeSessions,
        'session_participants' => $sessionParticipants,
        'session_progress' => $sessionProgress,
        'estado' => $project['estado'] ?? 'nuevo',
        'reviewers' => $project['reviewers'] ?? [],
        'created_at' => $project['created_at'] ?? null,
        'updated_at' => $project['updated_at'] ?? null
    ];
}

$projectsJson = json_encode($processedProjects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

$totalProjects = count($processedProjects);
$evaluatedProjects = 0;
$pendingProjects = 0;
$projectsWithActiveSessions = 0;
$totalSessions = 0;
$activeSessionsCount = 0;
$totalAverageScore = 0;
$totalEvaluators = 0;

foreach ($processedProjects as $project) {
    if ($project['evaluado']) {
        $evaluatedProjects++;
        $totalAverageScore += $project['puntuacion_promedio'] ?? 0;
        $totalEvaluators += $project['total_evaluadores'];
    } else {
        $pendingProjects++;
    }
    
    if ($project['sesion_activa']) {
        $projectsWithActiveSessions++;
    }
    
    $totalSessions += $project['total_sesiones'];
    $activeSessionsCount += count($project['sesiones_activas']);
}

$systemAverage = $evaluatedProjects > 0 ? $totalAverageScore / $evaluatedProjects : 0;
$averageEvaluatorsPerProject = $evaluatedProjects > 0 ? $totalEvaluators / $evaluatedProjects : 0;
?>

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Panel Administrativo - Evaluaciones y Sesiones</h2>
            <p class="text-sm text-gray-600 mt-1">Visión completa de todas las evaluaciones y sesiones del sistema</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <select id="filterLinea" class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Todas las líneas</option>
                <?php foreach ($filterCategory as $linea): ?>
                <option value="<?php echo $linea['id']; ?>"><?php echo htmlspecialchars($linea['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterEstado" class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="evaluado">Con evaluaciones</option>
                <option value="pendiente">Sin evaluaciones</option>
                <option value="sesion_activa">Con sesión activa</option>
                <option value="con_sesiones">Con sesiones</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Proyecto</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Línea - Fase</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Evaluaciones</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Sesiones</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Calificación</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Estado</th>
                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processedProjects as $project): ?>
                <?php
                $statusClass = $project['evaluado'] ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
                $statusText = $project['evaluado'] ? 'Evaluado' : 'Pendiente';
                
                if ($project['sesion_activa']) {
                    $statusClass = 'bg-purple-100 text-purple-800';
                    $statusText = 'Sesión Activa';
                }
                
                $scoreColorClass = 'text-gray-400';
                $progressColor = 'bg-gray-200';
                if ($project['evaluado']) {
                    $scoreColorClass = ($project['puntuacion_promedio'] < 3) ? 'text-red-600' : 'text-green-600';
                    $progressColor = ($project['puntuacion_promedio'] < 3) ? 'bg-red-500' : 'bg-green-500';
                }
                ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50 project-row" 
                    data-linea="<?php echo $project['linea_investigacion_id']; ?>"
                    data-estado="<?php 
                        if ($project['sesion_activa']) echo 'sesion_activa';
                        elseif ($project['evaluado']) echo 'evaluado';
                        else echo 'pendiente';
                    ?>"
                    data-sesiones="<?php echo $project['total_sesiones'] > 0 ? 'con_sesiones' : 'sin_sesiones'; ?>">
                    <td class="py-4">
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($project['titulo']); ?></p>
                            <p class="text-sm text-gray-500">v<?php echo $project['version']; ?></p>
                            <?php if ($project['sesion_activa']): ?>
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                <i class="fas fa-play-circle mr-1"></i>Sesión Activa
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4">
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($project['linea_nombre']); ?></p>
                        <p class="text-xs text-gray-500">Fase <?php echo $project['fase']; ?></p>
                    </td>
                    <td class="py-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">
                                <?php echo $project['total_evaluadores']; ?>
                            </span>
                            <span class="text-xs text-gray-500">evaluadores</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php echo $project['criterios_evaluados'] . '/' . $project['total_criterios']; ?> criterios
                        </div>
                    </td>
                    <td class="py-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">
                                <?php echo $project['total_sesiones']; ?>
                            </span>
                            <span class="text-xs text-gray-500">sesiones</span>
                        </div>
                        <?php if ($project['sesion_activa']): ?>
                        <div class="text-xs text-purple-600 mt-1">
                            <?php echo count($project['sesiones_activas']); ?> activas
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4">
                        <?php if ($project['evaluado']): ?>
                        <div class="flex items-center">
                            <span class="text-sm font-medium <?php echo $scoreColorClass; ?> mr-2">
                                <?php echo number_format($project['puntuacion_promedio'], 1); ?>
                            </span>
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="<?php echo $progressColor; ?> h-2 rounded-full" style="width: <?php echo ($project['puntuacion_promedio'] ?? 0) * 10; ?>%"></div>
                            </div>
                        </div>
                        <?php else: ?>
                        <span class="text-sm text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4">
                        <span class="text-xs font-medium px-3 py-1 rounded-full <?php echo $statusClass; ?>">
                            <?php echo $statusText; ?>
                        </span>
                    </td>
                    <td class="py-4">
                        <div class="flex space-x-1">
                            <button class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors view-project" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <button class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors view-evaluations" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    title="Ver Evaluaciones">
                                <i class="fas fa-list-check"></i>
                            </button>
                            
                            <?php if ($project['sesion_activa']): ?>
                            <button class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-colors view-session" 
                                    data-project-id="<?php echo $project['id']; ?>"
                                    title="Ver Sesión Activa">
                                <i class="fas fa-play-circle"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($processedProjects)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-500">
                        <div class="text-4xl text-gray-300 mb-2"><i class="fas fa-clipboard-list"></i></div>
                        <p>No hay proyectos en el sistema</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">Mostrando <?php echo count($processedProjects); ?> proyectos del sistema</p>
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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-sm p-6 border border-blue-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Proyectos</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $totalProjects; ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-folder-open text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-2">
            <span><?php echo $evaluatedProjects; ?> evaluados</span>
            <span><?php echo $pendingProjects; ?> pendientes</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-white to-green-50 rounded-xl shadow-sm p-6 border border-green-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Sesiones Activas</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $activeSessionsCount; ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-play-circle text-green-600 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">En curso</p>
    </div>

    <div class="bg-gradient-to-br from-white to-purple-50 rounded-xl shadow-sm p-6 border border-purple-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Calificación Promedio</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo number_format($systemAverage, 1); ?></p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Promedio del sistema</p>
    </div>

    <div class="bg-gradient-to-br from-white to-orange-50 rounded-xl shadow-sm p-6 border border-orange-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Evaluadores Activos</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $totalEvaluators; ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-users text-orange-600 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            <?php echo number_format($averageEvaluatorsPerProject, 1); ?> por proyecto
        </p>
    </div>
</div>

<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <span class="bg-gray-100 p-2 rounded-lg mr-3"><i class="fas fa-cogs text-gray-600"></i></span>
        Acciones Administrativas
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button class="flex flex-col items-center justify-center p-5 bg-white border border-blue-200 rounded-xl text-blue-700 hover:bg-blue-50 hover:shadow-md transition-all duration-200 group"
                onclick="showAllEvaluations()">
            <i class="fas fa-list-check text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-semibold">Todas las Evaluaciones</span>
            <span class="text-xs text-gray-500 mt-1">Ver completas</span>
        </button>
        <button class="flex flex-col items-center justify-center p-5 bg-white border border-purple-200 rounded-xl text-purple-700 hover:bg-purple-50 hover:shadow-md transition-all duration-200 group"
                onclick="showActiveSessions()">
            <i class="fas fa-play-circle text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-semibold">Sesiones Activas</span>
            <span class="text-xs text-gray-500 mt-1"><?php echo $activeSessionsCount; ?> en curso</span>
        </button>
        <button class="flex flex-col items-center justify-center p-5 bg-white border border-green-200 rounded-xl text-green-700 hover:bg-green-50 hover:shadow-md transition-all duration-200 group"
                onclick="exportData()">
            <i class="fas fa-file-export text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-semibold">Exportar Datos</span>
            <span class="text-xs text-gray-500 mt-1">JSON/CSV</span>
        </button>
        <button class="flex flex-col items-center justify-center p-5 bg-white border border-orange-200 rounded-xl text-orange-700 hover:bg-orange-50 hover:shadow-md transition-all duration-200 group"
                onclick="showAdvancedStats()">
            <i class="fas fa-chart-bar text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-semibold">Estadísticas</span>
            <span class="text-xs text-gray-500 mt-1">Reportes del sistema</span>
        </button>
    </div>
</div>

<div id="projectDetailsModal" class="modal-overlay">
    <div class="modal-content w-full max-w-4xl">
        <div class="modal-header">
            <h3 class="text-lg font-semibold" id="projectModalTitle">Detalles del Proyecto</h3>
            <button class="close-modal p-2 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="projectModalBody">
        </div>
        <div class="modal-footer">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 close-modal">Cerrar</button>
        </div>
    </div>
</div>

<script>
const projectsData = <?php echo $projectsJson; ?>;

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
        const projectSesiones = row.getAttribute('data-sesiones');
        
        const showLinea = !lineaFilter || projectLinea === lineaFilter;
        let showEstado = true;
        
        if (estadoFilter) {
            switch(estadoFilter) {
                case 'con_sesiones':
                    showEstado = projectSesiones === 'con_sesiones';
                    break;
                default:
                    showEstado = projectEstado === estadoFilter;
            }
        }
        
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

function showAllEvaluations() {
    document.getElementById('filterEstado').value = 'evaluado';
    filterProjects();
}

function showActiveSessions() {
    document.getElementById('filterEstado').value = 'sesion_activa';
    filterProjects();
}

function exportData() {
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(projectsData, null, 2));
    const downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", "evaluaciones_sistema_" + new Date().toISOString().split('T')[0] + ".json");
    document.body.appendChild(downloadAnchorNode);
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
    
    alert('Datos exportados correctamente. Se ha descargado un archivo JSON con toda la información.');
}

function showAdvancedStats() {
    const totalScore = projectsData.reduce((sum, project) => sum + (project.puntuacion_promedio || 0), 0);
    const avgScore = totalScore / projectsData.filter(p => p.puntuacion_promedio > 0).length;
    
    const statsHtml = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800">Proyectos por Estado</h4>
                    <p class="text-sm">Evaluados: ${projectsData.filter(p => p.evaluado).length}</p>
                    <p class="text-sm">Pendientes: ${projectsData.filter(p => !p.evaluado).length}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800">Sesiones</h4>
                    <p class="text-sm">Totales: ${projectsData.reduce((sum, p) => sum + p.total_sesiones, 0)}</p>
                    <p class="text-sm">Activas: ${projectsData.reduce((sum, p) => sum + p.sesiones_activas.length, 0)}</p>
                </div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <h4 class="font-semibold text-purple-800">Calificaciones</h4>
                <p class="text-sm">Promedio del sistema: ${avgScore.toFixed(2)}</p>
                <p class="text-sm">Rango: ${Math.min(...projectsData.filter(p => p.puntuacion_promedio > 0).map(p => p.puntuacion_promedio)).toFixed(2)} - ${Math.max(...projectsData.filter(p => p.puntuacion_promedio > 0).map(p => p.puntuacion_promedio)).toFixed(2)}</p>
            </div>
        </div>
    `;
    
    showCustomModal('Estadísticas del Sistema', statsHtml);
}

function showCustomModal(title, content) {
    const modal = document.getElementById('projectDetailsModal');
    const titleEl = document.getElementById('projectModalTitle');
    const bodyEl = document.getElementById('projectModalBody');
    
    titleEl.textContent = title;
    bodyEl.innerHTML = content;
    modal.style.display = 'flex';
}

function showProjectDetails(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project) return;
    
    const html = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Información General</h4>
                    <p><strong>Título:</strong> ${project.titulo}</p>
                    <p><strong>Línea:</strong> ${project.linea_nombre}</p>
                    <p><strong>Fase:</strong> ${project.fase}</p>
                    <p><strong>Versión:</strong> ${project.version}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Estadísticas</h4>
                    <p><strong>Evaluadores:</strong> ${project.total_evaluadores}</p>
                    <p><strong>Sesiones:</strong> ${project.total_sesiones}</p>
                    <p><strong>Criterios evaluados:</strong> ${project.criterios_evaluados}/${project.total_criterios}</p>
                    ${project.puntuacion_promedio ? `<p><strong>Calificación promedio:</strong> <span class="${project.puntuacion_promedio < 3 ? 'text-red-600' : 'text-green-600'}">${project.puntuacion_promedio.toFixed(2)}</span></p>` : ''}
                </div>
            </div>
            
            ${project.descripcion ? `
            <div>
                <h4 class="font-semibold text-gray-700">Descripción</h4>
                <p class="text-sm text-gray-600">${project.descripcion}</p>
            </div>
            ` : ''}
            
            ${project.palabras_clave ? `
            <div>
                <h4 class="font-semibold text-gray-700">Palabras Clave</h4>
                <p class="text-sm text-gray-600">${project.palabras_clave}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    showCustomModal(`Detalles: ${project.titulo}`, html);
}

function showProjectEvaluations(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project) return;
    
    let evaluationsHtml = '';
    
    if (project.evaluado && Object.keys(project.evaluation_details).length > 0) {
        evaluationsHtml = `
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h4 class="font-semibold text-gray-700">Evaluaciones realizadas</h4>
                    <span class="badge badge-success">${Object.keys(project.evaluation_details).length} evaluadores</span>
                </div>
                
                ${Object.entries(project.evaluation_details).map(([userId, data]) => `
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="font-semibold">${data.user_info.nombre || data.user_info.username || 'Usuario ' + userId}</h5>
                                <p class="text-sm text-gray-600">${data.user_info.email || ''}</p>
                            </div>
                            <span class="badge badge-info">${data.ratings.length} criterios</span>
                        </div>
                        
                        <div class="space-y-2">
                            ${data.ratings.map(rating => `
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <span class="text-sm">${rating.criterio}</span>
                                    <span class="font-semibold ${rating.puntuacion < 3 ? 'text-red-600' : 'text-green-600'}">${rating.puntuacion}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    } else {
        evaluationsHtml = `
            <div class="text-center py-8">
                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No hay evaluaciones para este proyecto</p>
            </div>
        `;
    }
    
    showCustomModal(`Evaluaciones: ${project.titulo}`, evaluationsHtml);
}

function showActiveSession(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project || !project.sesion_activa) return;
    
    const activeSession = project.sesiones_activas[0];
    const sessionId = activeSession[EVALUATION_SESSION_FIELD_ID];
    const participants = project.session_participants[sessionId] || [];
    const progress = project.session_progress[sessionId] || { total_participants: 0, completed_count: 0, completion_percentage: 0 };
    
    const html = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Información de la Sesión</h4>
                    <p><strong>Token:</strong> <code>${activeSession[EVALUATION_SESSION_FIELD_TOKEN]}</code></p>
                    <p><strong>Inicio:</strong> ${new Date(activeSession[EVALUATION_SESSION_FIELD_START]).toLocaleString()}</p>
                    <p><strong>Duración:</strong> ${activeSession[EVALUATION_SESSION_FIELD_DURATION]} segundos</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Progreso</h4>
                    <p><strong>Participantes:</strong> ${progress.total_participants}</p>
                    <p><strong>Completadas:</strong> ${progress.completed_count}</p>
                    <p><strong>Progreso:</strong> ${progress.completion_percentage}%</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Participantes</h4>
                <div class="space-y-2">
                    ${participants.map(participant => `
                        <div class="flex justify-between items-center p-3 border rounded-lg">
                            <div>
                                <p class="font-medium">${participant.user_info.nombre || participant.firstname + ' ' + participant.lastname || participant.username}</p>
                                <p class="text-sm text-gray-600">${participant.user_info.email || ''}</p>
                            </div>
                            <span class="badge ${participant.completed ? 'badge-success' : 'badge-warning'}">
                                ${participant.completed ? 'Completado' : 'Pendiente'}
                            </span>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    showCustomModal(`Sesión Activa: ${project.titulo}`, html);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.style.display = 'none';
            });
        });
    });
    
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
    
    document.querySelectorAll('.view-project').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            showProjectDetails(projectId);
        });
    });
    
    document.querySelectorAll('.view-evaluations').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            showProjectEvaluations(projectId);
        });
    });
    
    document.querySelectorAll('.view-session').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            showActiveSession(projectId);
        });
    });
});

filterProjects();
</script>