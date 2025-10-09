<?php
try {
    $logger = new ErrorLogger();
    $projectManager = new ProjectManager($pdo);
    $sessionManager = new EvaluationSessionManager($pdo, $logger);
    $evaluationManager = new EvaluationManager($pdo);
    $researchManager = new researchLineManager($pdo);
    $uploadManager = new UploadManager();
    $filterCategory = $researchManager->getAllActivas();
    $criteriaConfig = loadConfig('evaluation_config');

    $lineasMap = [];
    foreach ($filterCategory as $linea) {
        $lineasMap[$linea['id']] = $linea['nombre'];
    }

    $sessionManager->syncSessionStatus();
    $userId = $_SESSION['userid'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $action = $_POST['action'] ?? '';
            $projectId = (int)($_POST['project_id'] ?? 0);
            $sessionId = (int)($_POST['session_id'] ?? 0);
            
            switch ($action) {
                case 'start_evaluation_session':
                    if (!$projectId || !$projectManager->projectExists($projectId)) {
                        throw new Exception("ID de proyecto no válido");
                    }

                    $activeSessions = $sessionManager->getActiveSessionsByProject($projectId);
                    
                    if (!empty($activeSessions)) {
                        $session = $activeSessions[0];
                        
                        if (!$sessionManager->isUserInSession($userId, $session[EVALUATION_SESSION_FIELD_ID])) {
                            $sessionManager->addUserToSession($userId, $session[EVALUATION_SESSION_FIELD_ID]);
                        }
                        
                        header("Location: " . evalcp_base() . "?module=evaluation&token=" . $session[EVALUATION_SESSION_FIELD_TOKEN] . "&project_id=" . $projectId);
                        exit;
                    } else {
                        $token = bin2hex(random_bytes(32));
                        $inicio = new DateTime();
                        $duracion = $projectManager->getProjectDuration($projectId) + 600;
                        
                        $sessionId = $sessionManager->createSession($projectId, $token, $inicio, $duracion, $userId);
                        $projectManager->setStatus($projectId, 'en_evaluacion');
                        
                        header("Location: " . evalcp_base() . "?module=evaluation&token=" . $token . "&project_id=" . $projectId);
                        exit;
                    }
                    break;
                    
                case 'join_evaluation_session':
                    if (!$sessionId) {
                        throw new Exception("ID de sesión no válido");
                    }

                    $session = $sessionManager->getById($sessionId);
                    
                    if (!$session || $session[EVALUATION_SESSION_FIELD_STATE] !== 'activa') {
                        throw new Exception("La sesión no está disponible o ha expirado");
                    }
                    
                    if (!$sessionManager->isUserInSession($userId, $sessionId)) {
                        $sessionManager->addUserToSession($userId, $sessionId);
                    }
                    
                    header("Location: " . evalcp_base() . "?module=evaluation&token=" . $session[EVALUATION_SESSION_FIELD_TOKEN] . "&project_id=" . $session[EVALUATION_SESSION_FIELD_PROJECT_ID]);
                    exit;
                    break;
                    
                case 'check_session_status':
                    $projectId = (int)($_POST['project_id'] ?? 0);
                    echo json_encode([
                        'hasActiveSession' => $sessionManager->hasActiveSessionForProject($projectId),
                        'isUserInSession' => $sessionManager->isUserInAnyActiveSession($userId, $projectId)
                    ]);
                    exit;
            }
            
        } catch (Exception $e) {
            $logger->logPhpError("Error en POST: " . $e->getMessage());
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: " . evalcp_base() . "?module=projects");
            exit;
        }
    }

    $allProjects = $projectManager->getAllProjects();
    $assignedProjects = $projectManager->getProjectsByUser($userId);
    $userEvaluations = $evaluationManager->getUserEvaluatedProjects($userId);

    $assignedProjectIds = array_column($assignedProjects, 'id');
    $evaluatedProjectIds = array_column($userEvaluations, 'project_id');

    $availableProjects = array_filter($allProjects, function($project) use ($assignedProjectIds, $evaluatedProjectIds, $userId) {
        return in_array($project['id'], $assignedProjectIds) && !in_array($project['id'], $evaluatedProjectIds);
    });

    $maxScore = $criteriaConfig['puntuacion']['valor_maximo'] ?? 5;
    $aprobadoMin = 3.0;

    $processedProjects = [];
    foreach ($availableProjects as $project) {
        $documents = [];
        $projectDir = $project['directorio'] ?? null;
        
        if ($projectDir) {
            $basePath = __PATH_UPLOADS__ . 'docs/projects/' . $projectDir . '/';
            if (is_dir($basePath)) {
                $files = $uploadManager->listAllContents($basePath);
                foreach ($files as $file) {
                    $relativePath = 'docs/projects/' . $projectDir . '/' . $file;
                    
                    $documents[] = [
                        'id' => md5($file),
                        'name' => $file,
                        'path' => $relativePath,
                        'full_path' => $basePath . $file,
                        'size' => filesize($basePath . $file),
                        'url' => $handler->getDocumentUrl($relativePath),
                        'previewable' => $handler->canPreviewInBrowser($file),
                        'size_formatted' => $handler->getFileSize($relativePath)
                    ];
                }
            }
        }
        
        $investigadores = [];
        if (!empty($project['researchers'])) {
            foreach ($project['researchers'] as $researcher) {
                $investigadores[] = [
                    'usuario_uid' => $researcher['id'],
                    'nombre_completo' => $researcher['firstname'] . ' ' . $researcher['lastname'],
                    'username' => $researcher['username'],
                    'email' => $researcher['email'],
                    'rol' => $researcher['role'],
                    'estado' => $researcher['state']
                ];
            }
        }

        $docentes = [];
        if (!empty($project['teachers'])) {
            foreach ($project['teachers'] as $teacher) {
                $docentes[] = [
                    'usuario_uid' => $teacher['id'],
                    'nombre_completo' => $teacher['firstname'] . ' ' . $teacher['lastname'],
                    'username' => $teacher['username'],
                    'email' => $teacher['email'],
                    'rol' => $teacher['role'],
                    'estado' => $teacher['state']
                ];
            }
        }
        
        $evaluadores = [];
        if (!empty($project['reviewers'])) {
            foreach ($project['reviewers'] as $reviewer) {
                $evaluadores[] = [
                    'usuario_uid' => $reviewer['id'],
                    'nombre_completo' => $reviewer['firstname'] . ' ' . $reviewer['lastname'],
                    'username' => $reviewer['username'],
                    'email' => $reviewer['email'],
                    'estado' => $reviewer['state']
                ];
            }
        }
        
        $activeSessions = $sessionManager->getActiveSessionsByProject($project['id']);
        $hasActiveSession = !empty($activeSessions);
        $isUserInSession = false;
        
        if ($hasActiveSession) {
            $isUserInSession = $sessionManager->isUserInSession($userId, $activeSessions[0][EVALUATION_SESSION_FIELD_ID]);
        }
        
        $lineaNombre = $lineasMap[$project['linea_investigacion_id']] ?? 'Línea ' . $project['linea_investigacion_id'];

        $ratings = $evaluationManager->getRatingSummary($project['id']);
        $calificacionProyecto = $ratings['average_rating'] ?? null;

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
            'docentes' => $docentes,
            'evaluadores' => $evaluadores,
            'investigadores' => $investigadores,       
            'calificado' => 0,
            'sesion_activa' => $hasActiveSession,
            'usuario_en_sesion' => $isUserInSession,
            'puntuacion' => $calificacionProyecto,
            'estado' => $project['estado'] ?? 'nuevo',
            'ratings_summary' => $ratings
        ];
    }

    $projectsJson = json_encode($processedProjects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $lineasJson = json_encode($lineasMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $evaluationsWithDetails = [];
    foreach ($userEvaluations as $evaluation) {
        $summary = $evaluationManager->getSummaryByUser($evaluation['project_id'], $userId);
        $ratings = $evaluationManager->getRatingsByUser($evaluation['project_id'], $userId);
        
        $evaluationsWithDetails[] = [
            'project_id' => $evaluation['project_id'],
            'titulo' => $evaluation['titulo'],
            'puntuacion' => $evaluation['puntuacion'],
            'fecha_evaluacion' => $evaluation['fecha_evaluacion'],
            'comentario_general' => $evaluation['comentario_general'],
            'criteria_count' => count($ratings),
            'summary_data' => $summary
        ];
    }

    $totalEvaluations = count($userEvaluations);
    $aprobadoCount = 0;
    $reprobadoCount = 0;
    $totalScore = 0;

    foreach ($userEvaluations as $evaluation) {
        $totalScore += $evaluation['puntuacion'];
        if ($evaluation['puntuacion'] >= $aprobadoMin) {
            $aprobadoCount++;
        } else {
            $reprobadoCount++;
        }
    }

    $averageScore = $totalEvaluations > 0 ? $totalScore / $totalEvaluations : 0;

    if (isset($_GET['evaluate']) && isset($_GET['project_id'])) {
        $projectId = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
        
        if ($projectId && $projectManager->projectExists($projectId)) {
            $activeSessions = $sessionManager->getActiveSessionsByProject($projectId);
            
            if (!empty($activeSessions)) {
                $session = $activeSessions[0];
                if (!$sessionManager->isUserInSession($userId, $session[EVALUATION_SESSION_FIELD_ID])) {
                    $sessionManager->addUserToSession($userId, $session[EVALUATION_SESSION_FIELD_ID]);
                }
                
                header("Location: " . evalcp_base() . "?module=evaluation&token=" . $session[EVALUATION_SESSION_FIELD_TOKEN] . "&project_id=" . $projectId);
                exit;
            } else {
                $token = bin2hex(random_bytes(32));
                $inicio = new DateTime();
                $duracion = $projectManager->getProjectDuration($projectId) + 600;
                
                $sessionId = $sessionManager->createSession($projectId, $token, $inicio, $duracion, $userId);
                $projectManager->setStatus($projectId, 'en_evaluacion');
                
                header("Location: " . evalcp_base() . "?module=evaluation&token=" . $token . "&project_id=" . $projectId);
                exit;
            }
        }
    }

} catch (Exception $ex) {
    $logger->logError("EXCEPCIÓN GLOBAL: " . $ex->getMessage());
    $_SESSION['error_message'] = 'Error al cargar los proyectos';
    header("Location: " . evalcp_base() . "?module=projects");
    die();
}
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Proyectos para evaluar</h2>
                <div class="flex items-center space-x-2">
                    <input type="text" id="searchInput" placeholder="Buscar proyectos..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="all">Todas las líneas</option>
                        <?php foreach ($filterCategory as $linea): ?>
                        <option value="<?= $linea['id'] ?>"><?= htmlspecialchars($linea['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div id="loadingIndicator" class="text-center py-8">
                <span class="material-icons text-4xl text-gray-300 animate-pulse">hourglass_empty</span>
                <p class="text-gray-500 mt-2">Cargando proyectos...</p>
            </div>
            
            <div id="projectsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden"></div>
            
            <div id="noResults" class="text-center py-8 hidden">
                <span class="material-icons text-4xl text-gray-300">search_off</span>
                <p class="text-gray-500 mt-2">No se encontraron proyectos</p>
            </div>
            
            <div id="pagination" class="flex justify-center items-center mt-6 space-x-2 hidden"></div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Próximas evaluaciones</h2>
        </div>
        
        <div class="space-y-4" id="upcomingEvaluations">
            <div class="text-center py-8 text-gray-500">
                <span class="material-icons text-4xl opacity-50 mb-2">event_busy</span>
                <p>Cargando evaluaciones...</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <div class="lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Evaluaciones recientes</h2>
            <a href="?module=evaluations" class="text-primary-600 text-sm font-medium hover:underline">Ver todas</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="recentEvaluations">
            <?php if (count($evaluationsWithDetails) > 0): ?>
                <?php foreach ($evaluationsWithDetails as $evaluation): 
                    $scoreColor = $evaluation['puntuacion'] >= $aprobadoMin ? 'text-green-600' : 'text-red-600';
                    $scoreBg = $evaluation['puntuacion'] >= $aprobadoMin ? 'bg-green-100' : 'bg-red-100';
                ?>
                <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition-all duration-300 border-l-4 border-primary-500">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800 flex-1 pr-2"><?= htmlspecialchars($evaluation['titulo'] ?? 'Proyecto evaluado') ?></h3>
                        <span class="<?= $scoreBg ?> <?= $scoreColor ?> text-sm font-bold px-2 py-1 rounded-full min-w-[60px] text-center">
                            <?= !empty($evaluation['puntuacion']) ? $evaluation['puntuacion'] : 'N/A' ?>
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="material-icons text-sm mr-2 text-gray-400">event</span>
                            <span><?= !empty($evaluation['fecha_evaluacion']) ? date('d M, Y H:i', strtotime($evaluation['fecha_evaluacion'])) : 'Fecha no disponible' ?></span>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="material-icons text-sm mr-2 text-gray-400">checklist</span>
                            <span><?= $evaluation['criteria_count'] ?> criterios evaluados</span>
                        </div>
                        
                        <?php if (!empty($evaluation['summary_data']['summary_tiempo_total'])): ?>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="material-icons text-sm mr-2 text-gray-400">timer</span>
                            <span><?= $evaluation['summary_data']['summary_tiempo_total'] ?> min</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($evaluation['comentario_general'])): ?>
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-gray-700 line-clamp-2"><?= htmlspecialchars($evaluation['comentario_general']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <?php if ($evaluation['puntuacion'] >= $aprobadoMin): ?>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Aprobado</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Reprobado</span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?= evalcp_base() ?>?module=evaluations&data-project-id=<?= $evaluation['project_id'] ?>" 
                           class="bg-primary-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-primary-700 transition-colors flex items-center">
                            <span class="material-icons text-sm mr-1">visibility</span>
                            Detalles
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-2 bg-white rounded-xl shadow-sm p-8 text-center">
                    <span class="material-icons text-4xl text-gray-300 mb-4">assignment_turned_in</span>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">Aún no has completado evaluaciones</h3>
                    <p class="text-gray-500">Comienza evaluando algunos de los proyectos disponibles en la sección superior.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Estadísticas</h2>
        </div>
        
        <div class="space-y-4">
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-700">Evaluaciones completadas</span>
                    <span class="text-sm font-medium text-primary-600"><?= $totalEvaluations ?> de <?= count($assignedProjects) ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: <?= count($assignedProjects) > 0 ? ($totalEvaluations / count($assignedProjects)) * 100 : 0 ?>%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-700">Proyectos pendientes</span>
                    <span class="text-sm font-medium text-orange-600"><?= count($availableProjects) ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: <?= count($assignedProjects) > 0 ? (count($availableProjects) / count($assignedProjects)) * 100 : 0 ?>%"></div>
                </div>
            </div>
            
            <?php if ($totalEvaluations > 0): 
                $scoreColor = $averageScore >= $aprobadoMin ? 'text-green-600' : 'text-red-600';
            ?>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-700">Puntuación promedio</span>
                    <span class="text-sm font-medium <?= $scoreColor ?>"><?= round($averageScore, 1) ?> / <?= $maxScore ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: <?= ($averageScore / $maxScore) * 100 ?>%"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalEvaluations > 0): ?>
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Resumen por estado</h4>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Aprobados</span>
                    <span class="text-sm font-medium text-green-600"><?= $aprobadoCount ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Reprobados</span>
                    <span class="text-sm font-medium text-red-600"><?= $reprobadoCount ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-11/12 max-w-4xl max-h-[100vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800" id="modalProjectTitle"></h3>
            <button onclick="closeProjectModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[70vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Información del proyecto</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Línea de investigación:</span>
                            <span class="font-medium" id="modalProjectLine"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fase:</span>
                            <span class="font-medium" id="modalProjectPhase"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Versión:</span>
                            <span class="font-medium" id="modalProjectVersion"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fecha presentación:</span>
                            <span class="font-medium" id="modalProjectPresentation"></span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Evaluación</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tiempo asignado:</span>
                            <span class="font-medium" id="modalProjectTimer"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-medium" id="modalProjectStatus"></span>
                        </div>
                        <div class="flex justify-between" id="modalProjectScore">
                            <span class="text-gray-600">Puntuación:</span>
                            <span class="font-medium" id="scoreValue"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Descripción</h4>
                <p class="text-gray-600" id="modalProjectDescription"></p>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Palabras clave</h4>
                <div class="flex flex-wrap gap-2" id="keywordsList"></div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Documentos (<span id="documentsCount">0</span>)</h4>
                <div class="space-y-2" id="modalDocuments"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Estudiantes (<span id="studentsCount">0</span>)</h4>
                    <div class="space-y-2" id="modalStudents"></div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Docentes (<span id="teachersCount">0</span>)</h4>
                    <div class="space-y-2" id="modalTeachers"></div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Evaluadores (<span id="reviewersCount">0</span>)</h4>
                    <div class="space-y-2" id="modalReviewers"></div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end p-6 border-t">
            <button onclick="closeProjectModal()" class="mr-4 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
            <button id="modalEvaluateBtn" onclick="handleEvaluationAction()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span id="evaluateBtnText">Iniciar Evaluación</span>
            </button>
        </div>
    </div>
</div>

<div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-11/12 max-w-6xl h-5/6">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="pdfModalTitle"></h3>
            <button onclick="closePdfModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="h-full p-4">
            <iframe id="pdfViewer" class="w-full h-[90%] border rounded-lg" frameborder="0"></iframe>
        </div>
    </div>
</div>

<script>
const allProjects = <?= $projectsJson ?>;
const lineasMap = <?= $lineasJson ?>;
const assignedProjectIds = <?= json_encode(array_column($assignedProjects, 'id')) ?>;
const userEvaluations = <?= json_encode($evaluationsWithDetails) ?>; 
const maxScore = <?= $maxScore ?>;
const aprobadoMin = <?= $aprobadoMin ?>;

const availableProjects = allProjects.filter(project => 
    assignedProjectIds.includes(project.id) && 
    !userEvaluations.some(evaluation => evaluation.project_id == project.id)
);

let currentProjectId = null;
let currentProjectHasActiveSession = false;
let currentPage = 1;
const projectsPerPage = 4;

function getPhaseColorClass(phase) {
    switch (phase) {
        case 'propuesta': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'desarrollo': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'aplicación': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
}

function getStatusColorClass(status) {
    switch (status) {
        case 'nuevo': return 'bg-blue-100 text-blue-800';
        case 'en_progreso': return 'bg-amber-100 text-amber-800';
        case 'completado': return 'bg-emerald-100 text-emerald-800';
        case 'evaluado': return 'bg-purple-100 text-purple-800';
        case 'en_evaluacion': return 'bg-indigo-100 text-indigo-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function formatSeconds(seconds) {
    if (!seconds) return 'No definido';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours}h ${minutes}m`;
}

function formatDate(dateString) {
    if (!dateString) return 'No definida';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function isFutureEvaluation(dateString, timeString) {
    if (!dateString || !timeString) return false;
    
    try {
        const now = new Date();
        const evaluationDateTime = new Date(`${dateString}T${timeString}`);
        return evaluationDateTime > now;
    } catch (error) {
        return false;
    }
}

function getTimeRemainingColor(dateString, timeString) {
    if (!dateString || !timeString) return 'text-gray-500';
    
    try {
        const now = new Date();
        const evaluationDateTime = new Date(`${dateString}T${timeString}`);
        const timeDiff = evaluationDateTime - now;
        const hoursDiff = timeDiff / (1000 * 60 * 60);
        
        if (hoursDiff < 0) return 'text-red-500';
        if (hoursDiff < 24) return 'text-orange-500';
        if (hoursDiff < 48) return 'text-yellow-500';
        return 'text-green-500';
    } catch (error) {
        return 'text-gray-500';
    }
}

function getTimeRemainingIcon(dateString, timeString) {
    if (!dateString || !timeString) return 'notifications_none';
    
    try {
        const now = new Date();
        const evaluationDateTime = new Date(`${dateString}T${timeString}`);
        const timeDiff = evaluationDateTime - now;
        const hoursDiff = timeDiff / (1000 * 60 * 60);
        
        if (hoursDiff < 0) return 'notifications_off';
        if (hoursDiff < 24) return 'notifications_active';
        if (hoursDiff < 48) return 'notifications';
        return 'notifications_none';
    } catch (error) {
        return 'notifications_none';
    }
}

function renderProjects() {
    const grid = document.getElementById('projectsGrid');
    const pagination = document.getElementById('pagination');
    const noResults = document.getElementById('noResults');
    
    const filteredProjects = getFilteredProjects();
    const totalPages = Math.ceil(filteredProjects.length / projectsPerPage);
    
    if (filteredProjects.length === 0) {
        grid.classList.add('hidden');
        pagination.classList.add('hidden');
        noResults.classList.remove('hidden');
        return;
    }
    
    noResults.classList.add('hidden');
    grid.classList.remove('hidden');
    pagination.classList.remove('hidden');
    
    const startIndex = (currentPage - 1) * projectsPerPage;
    const endIndex = startIndex + projectsPerPage;
    const currentProjects = filteredProjects.slice(startIndex, endIndex);
    
    grid.innerHTML = currentProjects.map(project => `
        <div class="relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-300 border border-blue-100">
            <span class="absolute top-3 right-3 bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">${escapeHtml(project.linea_nombre)}</span>
            <div class="mb-4 mt-6">
                <h3 class="font-semibold text-gray-800">${escapeHtml(project.titulo)}</h3>
                <div class="flex items-center text-sm text-gray-500 mt-1">
                    <span class="material-icons text-sm mr-1">event</span>
                    <span>Vence: ${project.fecha_presentacion ? formatDate(project.fecha_presentacion) : 'No definida'}</span>
                </div>
                <div class="flex items-center text-sm text-gray-500 mt-1">
                    <span class="material-icons text-sm mr-1">assignment</span>
                    <span>${project.investigadores.length} estudiantes</span>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">${escapeHtml(project.descripcion || 'Descripción no disponible')}</p>
            
            <div class="flex flex-col sm:flex-row gap-2">
                <button class="flex-1 bg-primary-600 text-white py-2 px-3 rounded-lg text-sm hover:bg-primary-700 transition-colors" onclick="startEvaluation(${project.id})">
                    ${project.sesion_activa ? 'Unirse a evaluación' : 'Iniciar evaluación'}
                </button>
                <button class="flex-1 border border-gray-300 py-2 px-3 rounded-lg text-sm hover:bg-gray-50 transition-colors" onclick="showProjectDetails(${project.id})">
                    Detalles
                </button>
            </div>
        </div>
    `).join('');
    
    renderPagination(totalPages);
    renderUpcomingEvaluations();
}

function renderPagination(totalPages) {
    const pagination = document.getElementById('pagination');
    
    if (totalPages <= 1) {
        pagination.classList.add('hidden');
        return;
    }
    
    let paginationHTML = '';
    
    if (currentPage > 1) {
        paginationHTML += `
            <button onclick="changePage(${currentPage - 1})" class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                <span class="material-icons text-sm">chevron_left</span>
            </button>
        `;
    }
    
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button onclick="changePage(${i})" class="px-3 py-1 border ${i === currentPage ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 hover:bg-gray-50'} rounded-lg text-sm">
                ${i}
            </button>
        `;
    }
    
    if (currentPage < totalPages) {
        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})" class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                <span class="material-icons text-sm">chevron_right</span>
            </button>
        `;
    }
    
    pagination.innerHTML = paginationHTML;
}

function renderUpcomingEvaluations() {
    const container = document.getElementById('upcomingEvaluations');
    
    const upcomingProjects = allProjects.filter(project => 
        project.fecha_presentacion && 
        project.hora_programada &&
        isFutureEvaluation(project.fecha_presentacion, project.hora_programada)
    );
    
    if (upcomingProjects.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <span class="material-icons text-4xl opacity-50 mb-2">event_busy</span>
                <p>No hay evaluaciones programadas</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = upcomingProjects.map(project => {
        const timeColor = getTimeRemainingColor(project.fecha_presentacion, project.hora_programada);
        const timeIcon = getTimeRemainingIcon(project.fecha_presentacion, project.hora_programada);
        
        return `
            <div class="flex justify-between items-start p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">${escapeHtml(project.titulo)}</h4>
                    <div class="flex items-center text-sm ${timeColor} mt-1">
                        <span class="material-icons text-sm mr-1">event</span>
                        <span>${formatDate(project.fecha_presentacion)}</span>
                    </div>
                    <div class="flex items-center text-sm ${timeColor} mt-1">
                        <span class="material-icons text-sm mr-1">schedule</span>
                        <span>${project.hora_programada}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mt-1">
                        <span class="material-icons text-sm mr-1">timer</span>
                        <span>${Math.floor(project.timer_segundos / 60)} minutos</span>
                    </div>
                </div>
                <span class="material-icons ${timeColor}">${timeIcon}</span>
            </div>
        `;
    }).join('');
}

function getFilteredProjects() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;

    return availableProjects.filter(project => {
        const matchesSearch = project.titulo.toLowerCase().includes(searchTerm) ||
                            (project.palabras_clave && project.palabras_clave.toLowerCase().includes(searchTerm)) ||
                            project.linea_nombre.toLowerCase().includes(searchTerm);
        const matchesCategory = categoryFilter === 'all' || project.linea_investigacion_id.toString() === categoryFilter;
        
        return matchesSearch && matchesCategory;
    });
}

function changePage(page) {
    currentPage = page;
    renderProjects();
}

function showProjectDetails(projectId) {
    const project = allProjects.find(p => p.id == projectId);
    if (!project) return;

    currentProjectId = projectId;
    currentProjectHasActiveSession = project.sesion_activa;
    
    updateEvaluationButton();
    
    document.getElementById('modalProjectTitle').textContent = project.titulo;
    document.getElementById('modalProjectLine').textContent = project.linea_nombre;
    
    const phaseSpan = document.getElementById('modalProjectPhase');
    phaseSpan.textContent = project.fase;
    phaseSpan.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getPhaseColorClass(project.fase)}`;
    
    const statusSpan = document.getElementById('modalProjectStatus');
    const estado = project.estado || 'nuevo';
    statusSpan.textContent = estado.replace('_', ' ');
    statusSpan.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColorClass(estado)}`;
    
    const scoreSpan = document.getElementById('modalProjectScore');
    if (project.puntuacion !== null && project.puntuacion !== undefined) {
        document.getElementById('scoreValue').textContent = project.puntuacion + ' / ' + maxScore;
        scoreSpan.classList.remove('hidden');
    } else {
        scoreSpan.classList.add('hidden');
    }

    document.getElementById('modalProjectVersion').textContent = project.version || 'No definida';
    document.getElementById('modalProjectPresentation').textContent = project.fecha_presentacion ? formatDate(project.fecha_presentacion) : 'No programada';
    document.getElementById('modalProjectTimer').textContent = formatSeconds(project.timer_segundos);
    document.getElementById('modalProjectDescription').textContent = project.descripcion || 'Descripción no disponible';

    const keywordsList = document.getElementById('keywordsList');
    if (project.palabras_clave) {
        keywordsList.innerHTML = project.palabras_clave.split(',').map(keyword => `
            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">${escapeHtml(keyword.trim())}</span>
        `).join('');
    } else {
        keywordsList.innerHTML = '<span class="text-gray-500">No hay palabras clave</span>';
    }

    const documentsContainer = document.getElementById('modalDocuments');
    const documentsCount = document.getElementById('documentsCount');
    documentsCount.textContent = project.documentos.length;
    
    if (project.documentos.length > 0) {
        documentsContainer.innerHTML = project.documentos.map(doc => {
            const isPdf = doc.name.toLowerCase().endsWith('.pdf');
            return `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer group" onclick="${isPdf ? `openPdfModal('${escapeHtml(doc.name)}', '${doc.url}')` : `window.open('${doc.url}', '_blank')`}">
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-lg ${isPdf ? 'bg-red-100' : 'bg-indigo-100'} flex items-center justify-center flex-shrink-0">
                            <span class="material-icons ${isPdf ? 'text-red-600' : 'text-indigo-600'}">${getFileIcon(doc.name)}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(doc.name)}</div>
                            <div class="text-xs text-gray-500">${doc.size_formatted}</div>
                        </div>
                    </div>
                    <span class="material-icons text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">${isPdf ? 'visibility' : 'download'}</span>
                </div>
            `;
        }).join('');
    } else {
        documentsContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <span class="material-icons text-4xl opacity-50 mb-2">folder_open</span>
                <p>No hay documentos disponibles</p>
            </div>
        `;
    }

    const studentsContainer = document.getElementById('modalStudents');
    const studentsCount = document.getElementById('studentsCount');
    studentsCount.textContent = project.investigadores.length;
    
    studentsContainer.innerHTML = project.investigadores.map(researcher => `
        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                <span class="material-icons text-indigo-600">person</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(researcher.nombre_completo)}</div>
                <div class="text-xs text-gray-500 truncate">${escapeHtml(researcher.email)}</div>
            </div>
            ${researcher.rol ? `<span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${escapeHtml(researcher.rol)}</span>` : ''}
        </div>
    `).join('');

    const teachersContainer = document.getElementById('modalTeachers');
    const teachersCount = document.getElementById('teachersCount');
    teachersCount.textContent = project.docentes.length;
    
    teachersContainer.innerHTML = project.docentes.map(teacher => `
        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                <span class="material-icons text-indigo-600">school</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(teacher.nombre_completo)}</div>
                <div class="text-xs text-gray-500 truncate">${escapeHtml(teacher.email)}</div>
            </div>
            ${teacher.rol ? `<span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${escapeHtml(teacher.rol)}</span>` : ''}
        </div>
    `).join('');

    const reviewersContainer = document.getElementById('modalReviewers');
    const reviewersCount = document.getElementById('reviewersCount');
    reviewersCount.textContent = project.evaluadores.length;
    
    reviewersContainer.innerHTML = project.evaluadores.map(reviewer => `
        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                <span class="material-icons text-indigo-600">engineering</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(reviewer.nombre_completo)}</div>
                <div class="text-xs text-gray-500 truncate">${escapeHtml(reviewer.email)}</div>
            </div>
        </div>
    `).join('');

    document.getElementById('projectModal').classList.remove('hidden');
}

function updateEvaluationButton() {
    const evaluateBtn = document.getElementById('modalEvaluateBtn');
    const evaluateBtnText = document.getElementById('evaluateBtnText');
    
    if (currentProjectHasActiveSession) {
        evaluateBtnText.textContent = 'Unirse a Evaluación';
    } else {
        evaluateBtnText.textContent = 'Iniciar Evaluación';
    }
    
    evaluateBtn.disabled = false;
    evaluateBtn.classList.add('bg-primary-600', 'hover:bg-primary-700');
    evaluateBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
}

function handleEvaluationAction() {
    if (currentProjectHasActiveSession) {
        joinEvaluation(currentProjectId);
    } else {
        startEvaluation(currentProjectId);
    }
}

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': 'picture_as_pdf',
        'doc': 'description',
        'docx': 'description',
        'xls': 'table_chart',
        'xlsx': 'table_chart',
        'ppt': 'slideshow',
        'pptx': 'slideshow',
        'jpg': 'image',
        'jpeg': 'image',
        'png': 'image',
        'gif': 'image',
        'zip': 'folder_zip',
        'rar': 'folder_zip'
    };
    return iconMap[extension] || 'insert_drive_file';
}

function openPdfModal(title, url) {
    document.getElementById('pdfModalTitle').textContent = title;
    document.getElementById('pdfViewer').src = url;
    document.getElementById('pdfModal').classList.remove('hidden');
}

function closePdfModal() {
    document.getElementById('pdfModal').classList.add('hidden');
    document.getElementById('pdfViewer').src = '';
}

function closeProjectModal() {
    document.getElementById('projectModal').classList.add('hidden');
    currentProjectId = null;
    currentProjectHasActiveSession = false;
}

function checkSessionStatus(projectId) {
    return fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=check_session_status&project_id=${projectId}`
    })
    .then(response => response.json())
    .then(data => {
        return data;
    })
    .catch(error => {
        console.error('Error checking session status:', error);
        return { hasActiveSession: false, isUserInSession: false };
    });
}

async function updateProjectSessionStatus(projectId) {
    try {
        const status = await checkSessionStatus(projectId);
        
        const projectIndex = allProjects.findIndex(p => p.id == projectId);
        if (projectIndex !== -1) {
            allProjects[projectIndex].sesion_activa = status.hasActiveSession;
            allProjects[projectIndex].usuario_en_sesion = status.isUserInSession;
        }
        
        if (currentProjectId == projectId) {
            currentProjectHasActiveSession = status.hasActiveSession;
            updateEvaluationButton();
        }
        
        renderProjects();
        
    } catch (error) {
        console.error('Error updating session status:', error);
    }
}

function startSessionStatusPolling() {
    setInterval(() => {
        availableProjects.forEach(project => {
            updateProjectSessionStatus(project.id);
        });
    }, 5000);
}

async function startEvaluation(projectId) {
    const status = await checkSessionStatus(projectId);
    
    if (status.hasActiveSession) {
        if (confirm('Ya existe una sesión de evaluación activa. ¿Deseas unirte?')) {
            joinEvaluation(projectId);
        }
        return;
    }
    
    if (!confirm(`¿Estás seguro de iniciar la evaluación este proyecto?`)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'start_evaluation_session';
    
    const projectInput = document.createElement('input');
    projectInput.type = 'hidden';
    projectInput.name = 'project_id';
    projectInput.value = projectId;
    
    form.appendChild(actionInput);
    form.appendChild(projectInput);
    document.body.appendChild(form);
    form.submit();
}

function joinEvaluation(projectId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'join_evaluation_session';
    
    const projectInput = document.createElement('input');
    projectInput.type = 'hidden';
    projectInput.name = 'project_id';
    projectInput.value = projectId;
    
    form.appendChild(actionInput);
    form.appendChild(projectInput);
    document.body.appendChild(form);
    form.submit();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').addEventListener('input', function() {
        currentPage = 1;
        renderProjects();
    });
    
    document.getElementById('categoryFilter').addEventListener('change', function() {
        currentPage = 1;
        renderProjects();
    });
    
    setTimeout(() => {
        document.getElementById('loadingIndicator').classList.add('hidden');
        renderProjects();
        startSessionStatusPolling();
    }, 500);
});
</script>