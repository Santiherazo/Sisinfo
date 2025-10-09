<?php
if(!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de resultados está deshabilitado.');

if(!accessManager()->canAccess($_SESSION['userid'], ['estudiante', 'docente'], [['module' => 'usercp', 'action' => 'results']])) {
    die('No tienes permisos para acceder a este módulo.');
}

include(__PATH_MODULES__.'/header.php');

$evaluationManager = new EvaluationManager($pdo);
$roleManager = new RoleManager($pdo);
$userManager = new UserManager($pdo);
$fullName = new UserDetailsManager($db, $logger);
$projectManager = new ProjectManager($pdo);
$profileManager = new ProfileManager($pdo, $db, $logger);

$isDocente = $roleManager->userHasRole($_SESSION['userid'], 'docente');
$isEstudiante = $roleManager->userHasRole($_SESSION['userid'], 'estudiante');

$searchTerm = $_GET['search'] ?? '';
$searchResults = [];
$selectedUser = null;
$userEvaluations = [];
$userProjectsData = [];
$processedProjects = [];
$allStudentData = [];

if ($isEstudiante) {
    $selectedUser = $userManager->getUserById($_SESSION['userid']);
    
    $studentProjectIds = $projectManager->getProjectsByUser($_SESSION['userid']);
    
    foreach ($studentProjectIds as $projectData) {
        $projectId = $projectData['id'];
        
        $project = $projectManager->getProject($projectId);
        if (!$project) continue;
        
        $projectEvaluations = $evaluationManager->getRatingsWithSummariesByProject($projectId);
        
        $totalScore = 0;
        $totalCalificaciones = 0;
        $criteriosMap = [];
        $evaluadoresMap = [];
        $evaluadoresUnicos = [];

        foreach ($projectEvaluations as $evaluation) {
            if ($evaluation[RATINGS_CALIFICACION] > 0) {
                $totalScore += floatval($evaluation[RATINGS_CALIFICACION]);
                $totalCalificaciones++;
                
                $evaluadorId = $evaluation[RATINGS_EVALUADOR_UID];
                if (!in_array($evaluadorId, $evaluadoresUnicos)) {
                    $evaluadoresUnicos[] = $evaluadorId;
                }
                
                $criterio = $evaluation[RATINGS_CRITERIO_NOMBRE];
                if (!isset($criteriosMap[$criterio])) {
                    $criteriosMap[$criterio] = [
                        'calificaciones' => [],
                        'comentarios' => []
                    ];
                }
                $criteriosMap[$criterio]['calificaciones'][] = floatval($evaluation[RATINGS_CALIFICACION]);
                if (!empty($evaluation[RATINGS_OBSERVACION_PERSONAL])) {
                    $criteriosMap[$criterio]['comentarios'][] = [
                        'comentario' => $evaluation[RATINGS_OBSERVACION_PERSONAL],
                        'evaluador_nombre' => $fullName->getFullName($evaluation['evaluador_uid'])
                    ];
                }
                
                if (!isset($evaluadoresMap[$evaluadorId])) {
                    $evaluadoresMap[$evaluadorId] = [
                        'nombre_completo' => $fullName->getFullName($evaluadorId),
                        'calificaciones' => [],
                        'criterios' => [],
                        'comentario_general' => $evaluation['comentario_general'] ?? '',
                        'promedio' => 0
                    ];
                }
                $evaluadoresMap[$evaluadorId]['calificaciones'][] = floatval($evaluation[RATINGS_CALIFICACION]);
                $evaluadoresMap[$evaluadorId]['criterios'][] = [
                    'nombre' => $evaluation[RATINGS_CRITERIO_NOMBRE],
                    'calificacion' => floatval($evaluation[RATINGS_CALIFICACION]),
                    'comentario' => $evaluation[RATINGS_OBSERVACION_PERSONAL] ?? ''
                ];
            }
        }
        
        foreach ($evaluadoresMap as $evaluadorId => &$evaluadorData) {
            if (count($evaluadorData['calificaciones']) > 0) {
                $evaluadorData['promedio'] = array_sum($evaluadorData['calificaciones']) / count($evaluadorData['calificaciones']);
            }
        }
        
        $averageScore = $totalCalificaciones > 0 ? $totalScore / $totalCalificaciones : 0;
        $totalCriterios = count($criteriosMap);
        $totalEvaluadores = count($evaluadoresUnicos);
        
        $processedProjects[] = [
            'id' => $project['id'],
            'titulo' => $project['titulo'],
            'descripcion' => $project['descripcion'] ?? '',
            'linea_nombre' => $project['linea_investigacion_nombre'] ?? 'General',
            'fase' => $project['fase'],
            'version' => $project['version'],
            'fecha_presentacion' => $project['fecha_presentacion'] ?? null,
            'calificado' => $totalCalificaciones > 0,
            'puntuacion' => $averageScore,
            'criterios_evaluados' => $totalCriterios,
            'total_calificaciones' => $totalCalificaciones,
            'total_evaluadores' => $totalEvaluadores,
            'criterios' => $criteriosMap,
            'evaluadores' => $evaluadoresMap,
            'evaluaciones_completas' => $projectEvaluations
        ];
    }
    
    $projectsJson = json_encode($processedProjects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

if ($isDocente) {
    if (!empty($searchTerm)) {
        $allUsers = $profileManager->getAllUsers();
        $searchResults = [];
        
        foreach ($allUsers as $user) {
            if (!isset($user['id']) || empty($user['id'])) {
                continue;
            }
            
            if (!$roleManager->userHasRole($user['id'], 'estudiante')) {
                continue;
            }
            
            $fullNameText = $fullName->getFullName((int)$user['id']);
            
            $searchFields = [
                $user['username'] ?? '',
                $user['email'] ?? '',
                $fullNameText,
                $user['first_name'] ?? '',
                $user['last_name'] ?? '',
                $user['second_last_name'] ?? '',
                $user['card_code'] ?? ''
            ];
            
            $found = false;
            foreach ($searchFields as $field) {
                if (stripos($field, $searchTerm) !== false) {
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                $user['full_name'] = $fullNameText;
                
                $userProjects = $projectManager->getProjectsByUser($user['id']);
                $userProjectData = [];
                $hasEvaluations = false;
                
                foreach ($userProjects as $projectData) {
                    $projectId = $projectData['id'];
                    $project = $projectManager->getProject($projectId);
                    if (!$project) continue;
                    
                    $projectEvaluations = $evaluationManager->getRatingsWithSummariesByProject($projectId);
                    
                    $totalScore = 0;
                    $totalCalificaciones = 0;
                    $criteriosMap = [];
                    $evaluadoresMap = [];
                    $evaluadoresUnicos = [];

                    foreach ($projectEvaluations as $evaluation) {
                        if ($evaluation[RATINGS_CALIFICACION] > 0) {
                            $totalScore += floatval($evaluation[RATINGS_CALIFICACION]);
                            $totalCalificaciones++;
                            
                            $evaluadorId = $evaluation[RATINGS_EVALUADOR_UID];
                            if (!in_array($evaluadorId, $evaluadoresUnicos)) {
                                $evaluadoresUnicos[] = $evaluadorId;
                            }
                            
                            $criterio = $evaluation[RATINGS_CRITERIO_NOMBRE];
                            if (!isset($criteriosMap[$criterio])) {
                                $criteriosMap[$criterio] = [
                                    'calificaciones' => [],
                                    'comentarios' => []
                                ];
                            }
                            $criteriosMap[$criterio]['calificaciones'][] = floatval($evaluation[RATINGS_CALIFICACION]);
                            if (!empty($evaluation[RATINGS_OBSERVACION_PERSONAL])) {
                                $criteriosMap[$criterio]['comentarios'][] = [
                                    'comentario' => $evaluation[RATINGS_OBSERVACION_PERSONAL],
                                    'evaluador_nombre' => $fullName->getFullName($evaluation['evaluador_uid'])
                                ];
                            }
                            
                            if (!isset($evaluadoresMap[$evaluadorId])) {
                                $evaluadoresMap[$evaluadorId] = [
                                    'nombre_completo' => $fullName->getFullName($evaluadorId),
                                    'calificaciones' => [],
                                    'criterios' => [],
                                    'comentario_general' => $evaluation['comentario_general'] ?? '',
                                    'promedio' => 0
                                ];
                            }
                            $evaluadoresMap[$evaluadorId]['calificaciones'][] = floatval($evaluation[RATINGS_CALIFICACION]);
                            $evaluadoresMap[$evaluadorId]['criterios'][] = [
                                'nombre' => $evaluation[RATINGS_CRITERIO_NOMBRE],
                                'calificacion' => floatval($evaluation[RATINGS_CALIFICACION]),
                                'comentario' => $evaluation[RATINGS_OBSERVACION_PERSONAL] ?? ''
                            ];
                        }
                    }
                    
                    foreach ($evaluadoresMap as $evaluadorId => &$evaluadorData) {
                        if (count($evaluadorData['calificaciones']) > 0) {
                            $evaluadorData['promedio'] = array_sum($evaluadorData['calificaciones']) / count($evaluadorData['calificaciones']);
                        }
                    }
                    
                    $averageScore = $totalCalificaciones > 0 ? $totalScore / $totalCalificaciones : 0;
                    $totalCriterios = count($criteriosMap);
                    $totalEvaluadores = count($evaluadoresUnicos);
                    
                    $userProjectData[] = [
                        'id' => $project['id'],
                        'titulo' => $project['titulo'],
                        'descripcion' => $project['descripcion'] ?? '',
                        'linea_nombre' => $project['linea_investigacion_nombre'] ?? 'General',
                        'fase' => $project['fase'],
                        'version' => $project['version'],
                        'fecha_presentacion' => $project['fecha_presentacion'] ?? null,
                        'calificado' => $totalCalificaciones > 0,
                        'puntuacion' => $averageScore,
                        'criterios_evaluados' => $totalCriterios,
                        'total_calificaciones' => $totalCalificaciones,
                        'total_evaluadores' => $totalEvaluadores,
                        'criterios' => $criteriosMap,
                        'evaluadores' => $evaluadoresMap,
                        'evaluaciones_completas' => $projectEvaluations
                    ];
                    
                    if ($totalCalificaciones > 0) {
                        $hasEvaluations = true;
                    }
                }
                
                $user['has_evaluations'] = $hasEvaluations;
                $user['projects_data'] = $userProjectData;
                $user['projects_json'] = json_encode($userProjectData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                
                $searchResults[] = $user;
                $allStudentData[$user['id']] = $userProjectData;
            }
        }
        
        $selectedUserId = $_GET['user_id'] ?? null;
        if ($selectedUserId) {
            $selectedUser = $userManager->getUserById($selectedUserId);
            if ($selectedUser && isset($selectedUser['id'])) {
                $userProjectsData = $allStudentData[$selectedUserId] ?? [];
                $userProjectsJson = json_encode($userProjectsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            }
        }
    }
    
    $allStudentDataJson = json_encode($allStudentData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}
?>

<div class="max-w-[1400px] mx-auto mb-12">
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Proyectos</span>
            </a>
            <?php endif; ?>
            
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-accent)] text-[var(--color-navbar-text)] border border-[var(--color-accent)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Resultados</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-success)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-success)]/10 rounded flex items-center justify-center">
                    <i data-lucide="user" class="w-3 h-3 text-[var(--color-success)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Perfil</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-danger)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-danger)]/10 rounded flex items-center justify-center">
                    <i data-lucide="shield" class="w-3 h-3 text-[var(--color-danger)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Seguridad</span>
            </a>
        </div>
    </div>

    <?php if ($isEstudiante): ?>
    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-[var(--color-accent)]/10 rounded-lg flex items-center justify-center">
                <i data-lucide="award" class="w-5 h-5 text-[var(--color-accent)]"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-heading)]">Mis Resultados de Evaluación</h2>
                <p class="text-[var(--color-text-muted)] text-sm">Revisa los resultados detallados de tus proyectos</p>
            </div>
        </div>

        <?php
        $totalProjects = count($processedProjects);
        $evaluatedProjects = 0;
        $totalScore = 0;
        $totalCalificacionesGlobal = 0;
        
        foreach ($processedProjects as $project) {
            if ($project['calificado']) {
                $evaluatedProjects++;
                $totalScore += $project['puntuacion'] * $project['total_calificaciones'];
                $totalCalificacionesGlobal += $project['total_calificaciones'];
            }
        }
        
        $averageScore = $totalCalificacionesGlobal > 0 ? $totalScore / $totalCalificacionesGlobal : 0;
        ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-[var(--color-surface-alt)] rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-[var(--color-accent)]"><?php echo $totalProjects; ?></div>
                <div class="text-sm text-[var(--color-text-muted)]">Total Proyectos</div>
            </div>
            <div class="bg-[var(--color-surface-alt)] rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-[var(--color-success)]"><?php echo $evaluatedProjects; ?></div>
                <div class="text-sm text-[var(--color-text-muted)]">Proyectos Evaluados</div>
            </div>
            <div class="bg-[var(--color-surface-alt)] rounded-lg p-4 text-center">
                <div class="text-2xl font-bold <?php echo $averageScore >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'; ?>"><?php echo number_format($averageScore, 1); ?>/5</div>
                <div class="text-sm text-[var(--color-text-muted)]">Promedio General</div>
            </div>
        </div>
    </div>

    <div class="bg-[var(--color-surface)] rounded-xl shadow-sm p-4 md:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-[var(--color-heading)]">Mis Proyectos Evaluados</h2>
            <div class="flex flex-wrap gap-2">
                <select id="filterEstado" class="p-2 rounded-lg border border-[var(--color-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="evaluado">Evaluados</option>
                    <option value="pendiente">Pendientes</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[var(--color-border)]">
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Proyecto</th>
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Línea</th>
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Versión</th>
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Evaluadores</th>
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Calificación</th>
                        <th class="pb-3 text-left text-sm font-semibold text-[var(--color-text)]">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($processedProjects as $project): ?>
                    <?php
                    $scoreColorClass = 'text-[var(--color-text-muted)]';
                    $scoreText = '-';
                    if ($project['calificado']) {
                        $scoreColorClass = $project['puntuacion'] >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]';
                        $scoreText = number_format($project['puntuacion'], 1) . '/5';
                    }
                    ?>
                    <tr class="border-b border-[var(--color-border)] hover:bg-[var(--color-dropdown-hover)] project-row" 
                        data-estado="<?php echo $project['calificado'] ? 'evaluado' : 'pendiente'; ?>">
                        <td class="py-4">
                            <div>
                                <p class="font-medium text-[var(--color-text)]"><?php echo htmlspecialchars($project['titulo']); ?></p>
                                <p class="text-sm text-[var(--color-text-muted)]"><?php echo htmlspecialchars($project['descripcion']); ?></p>
                            </div>
                        </td>
                        <td class="py-4 text-sm text-[var(--color-text)]">
                            <?php echo htmlspecialchars($project['linea_nombre']); ?>
                        </td>
                        <td class="py-4">
                            <span class="bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-xs font-medium px-3 py-1 rounded-full">
                                v<?php echo $project['version']; ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="text-sm text-[var(--color-text)]">
                                <?php echo $project['calificado'] ? $project['total_evaluadores'] . ' eval.' : 'Sin evaluar'; ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="text-sm font-medium <?php echo $scoreColorClass; ?>">
                                <?php echo $scoreText; ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <div class="flex space-x-2">
                                <?php if ($project['calificado']): ?>
                                <button class="flex items-center gap-2 px-4 py-2 bg-[var(--color-primary)] text-[var(--color-navbar-text)] rounded-lg hover:bg-[var(--color-primary)]/90 transition-colors" 
                                        onclick="openEvaluationModal('<?php echo $project['id']; ?>')"
                                        title="Ver Evaluación Completa">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                    <span>Ver Detalles</span>
                                </button>
                                <?php else: ?>
                                <span class="flex items-center gap-2 px-4 py-2 bg-[var(--color-text-muted)] text-[var(--color-navbar-text)] rounded-lg cursor-not-allowed">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>Pendiente</span>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($processedProjects)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-[var(--color-text-muted)]">
                            <div class="text-4xl text-[var(--color-border)] mb-2"><i data-lucide="clipboard-list" class="w-8 h-8 mx-auto"></i></div>
                            <p>No tienes proyectos registrados</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php elseif ($isDocente): ?>
    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-[var(--color-accent)]/10 rounded-lg flex items-center justify-center">
                <i data-lucide="search" class="w-5 h-5 text-[var(--color-accent)]"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-heading)]">Consulta de Resultados</h2>
                <p class="text-[var(--color-text-muted)] text-sm">Busca estudiantes para ver sus resultados de evaluación</p>
            </div>
        </div>

        <form method="GET" action="" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($searchTerm); ?>"
                           placeholder="Buscar por nombre, apellido o código de estudiante..."
                           class="w-full p-3 rounded-lg border border-[var(--color-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent">
                </div>
                <button type="submit" 
                        class="px-6 py-3 bg-[var(--color-primary)] text-[var(--color-navbar-text)] rounded-lg hover:bg-[var(--color-primary)]/90 transition-colors font-medium flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Buscar
                </button>
            </div>
        </form>

        <?php if (!empty($searchTerm)): ?>
            <?php if (!empty($searchResults)): ?>
                <div class="bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-4">
                    <h3 class="text-lg font-semibold text-[var(--color-heading)] mb-4">Resultados de la búsqueda</h3>
                    <div class="space-y-3">
                        <?php foreach ($searchResults as $user): ?>
                            <div class="flex items-center justify-between p-4 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-[var(--color-primary)]/10 rounded-full flex items-center justify-center">
                                        <i data-lucide="user" class="w-6 h-6 text-[var(--color-primary)]"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-[var(--color-text)]"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h4>
                                        <p class="text-sm text-[var(--color-text-muted)]">Usuario: <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
                                        <?php if (!empty($user['card_code'])): ?>
                                        <p class="text-sm text-[var(--color-text-muted)]">Código: <?php echo htmlspecialchars($user['card_code']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm <?php echo $user['has_evaluations'] ? 'text-[var(--color-success)]' : 'text-[var(--color-text-muted)]'; ?>">
                                        <?php echo $user['has_evaluations'] ? 'Con evaluaciones' : 'Sin evaluaciones'; ?>
                                    </span>
                                    <button class="px-4 py-2 bg-[var(--color-primary)] text-[var(--color-navbar-text)] rounded-lg hover:bg-[var(--color-primary)]/90 transition-colors text-sm font-medium flex items-center gap-2 show-student-results"
                                            data-user-id="<?php echo $user['id']; ?>"
                                            data-user-name="<?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        Ver Resultados
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="studentResultsSection" class="mt-8 hidden">
                </div>

            <?php else: ?>
                <div class="text-center py-8 bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)]">
                    <i data-lucide="users" class="w-16 h-16 text-[var(--color-border)] mx-auto mb-4"></i>
                    <h3 class="text-lg font-semibold text-[var(--color-text-muted)] mb-2">No se encontraron estudiantes</h3>
                    <p class="text-[var(--color-text-muted)]">No hay estudiantes que coincidan con tu búsqueda.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($selectedUser && isset($selectedUser['id'])): ?>
            <div class="mt-8 bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-6" id="currentStudentResults">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-[var(--color-heading)]">Resultados de <?php echo htmlspecialchars($fullName->getFullName((int)$selectedUser['id'])); ?></h3>
                        <p class="text-[var(--color-text-muted)]">Usuario: <?php echo htmlspecialchars($selectedUser['username'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="text-right">
                        <?php
                        $totalScore = 0;
                        $totalCalificacionesGlobal = 0;
                        foreach ($userProjectsData as $project) {
                            if ($project['calificado']) {
                                $totalScore += $project['puntuacion'] * $project['total_calificaciones'];
                                $totalCalificacionesGlobal += $project['total_calificaciones'];
                            }
                        }
                        $averageScore = $totalCalificacionesGlobal > 0 ? $totalScore / $totalCalificacionesGlobal : 0;
                        ?>
                        <div class="text-2xl font-bold <?php echo $averageScore >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'; ?>">
                            <?php echo number_format($averageScore, 1); ?>/5
                        </div>
                        <div class="text-sm text-[var(--color-text-muted)]">Promedio General</div>
                    </div>
                </div>

                <?php if (!empty($userProjectsData)): ?>
                    <div class="space-y-4">
                        <?php foreach ($userProjectsData as $project): ?>
                            <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-[var(--color-text)]"><?php echo htmlspecialchars($project['titulo']); ?></h4>
                                        <p class="text-sm text-[var(--color-text-muted)]">
                                            <?php echo $project['calificado'] ? $project['total_evaluadores'] . ' evaluadores' : 'Sin evaluar'; ?>
                                        </p>
                                    </div>
                                    <?php if ($project['calificado']): ?>
                                    <span class="text-lg font-bold <?php echo $project['puntuacion'] >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'; ?>">
                                        <?php echo number_format($project['puntuacion'], 1); ?>/5
                                    </span>
                                    <?php else: ?>
                                    <span class="text-lg font-bold text-[var(--color-text-muted)]">-</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($project['calificado']): ?>
                                <div class="space-y-2">
                                    <?php 
                                    $criteriosMostrados = [];
                                    foreach ($project['evaluaciones_completas'] as $evaluation): 
                                        if ($evaluation[RATINGS_CALIFICACION] > 0 && !in_array($evaluation[RATINGS_CRITERIO_NOMBRE], $criteriosMostrados)):
                                            $criteriosMostrados[] = $evaluation[RATINGS_CRITERIO_NOMBRE];
                                    ?>
                                        <div class="flex justify-between items-center p-2 bg-[var(--color-dropdown-hover)] rounded">
                                            <span class="text-sm text-[var(--color-text)]">
                                                <?php echo htmlspecialchars($evaluation[RATINGS_CRITERIO_NOMBRE]); ?>
                                            </span>
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($evaluation[RATINGS_OBSERVACION_PERSONAL])): ?>
                                                    <span class="text-xs text-[var(--color-text-muted)]" title="<?php echo htmlspecialchars($evaluation[RATINGS_OBSERVACION_PERSONAL]); ?>">
                                                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="text-sm font-medium <?php echo $evaluation[RATINGS_CALIFICACION] >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'; ?>">
                                                    <?php echo number_format($evaluation[RATINGS_CALIFICACION], 1); ?>/5
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; endforeach; ?>
                                </div>
                                <div class="mt-3">
                                    <button class="flex items-center gap-2 px-4 py-2 bg-[var(--color-primary)] text-[var(--color-navbar-text)] rounded-lg hover:bg-[var(--color-primary)]/90 transition-colors text-sm"
                                            onclick="openEvaluationModal('<?php echo $project['id']; ?>')">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Ver Evaluación Completa
                                    </button>
                                </div>
                                <?php else: ?>
                                <p class="text-[var(--color-text-muted)] text-sm">Este proyecto aún no ha sido evaluado.</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i data-lucide="clipboard-list" class="w-16 h-16 text-[var(--color-border)] mx-auto mb-4"></i>
                        <h3 class="text-lg font-semibold text-[var(--color-text-muted)] mb-2">Sin proyectos</h3>
                        <p class="text-[var(--color-text-muted)]">Este estudiante no tiene proyectos registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (empty($searchTerm)): ?>
            <div class="text-center py-12 bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)]">
                <i data-lucide="search" class="w-20 h-20 text-[var(--color-border)] mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-[var(--color-text-muted)] mb-2">Buscar Estudiantes</h3>
                <p class="text-[var(--color-text-muted)] text-lg mb-4">Ingresa el nombre, apellido o código de un estudiante para ver sus resultados.</p>
                <div class="flex justify-center">
                    <div class="bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 rounded-lg p-4 max-w-md">
                        <p class="text-sm text-[var(--color-primary)] flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Puedes buscar por: nombre, apellido o código de estudiante
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="studentEvaluationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-[var(--color-surface)] rounded-lg shadow-xl w-full max-w-6xl max-h-[95vh] overflow-y-auto m-4">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-[var(--color-heading)]" id="modalTitle">Evaluación Completa del Proyecto</h2>
                    <p class="text-[var(--color-text-muted)] text-sm" id="modalSubtitle">Resultados detallados por evaluador y criterio</p>
                </div>
                <button onclick="closeStudentEvaluationModal()" class="p-2 rounded-full hover:bg-[var(--color-dropdown-hover)] transition-all">
                    <i data-lucide="x" class="w-6 h-6 text-[var(--color-text-muted)]"></i>
                </button>
            </div>

            <div id="studentModalContent">
            </div>
        </div>
    </div>
</div>

<script>
<?php if ($isEstudiante): ?>
const studentProjectsData = <?php echo $projectsJson; ?>;
<?php elseif ($isDocente): ?>
const allStudentData = <?php echo $allStudentDataJson ?? '{}'; ?>;
<?php endif; ?>

function showStudentResults(userId, userName) {
    const studentData = allStudentData[userId];
    
    if (!studentData || studentData.length === 0) {
        document.getElementById('studentResultsSection').innerHTML = `
            <div class="bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-6">
                <div class="text-center py-8">
                    <i data-lucide="clipboard-list" class="w-16 h-16 text-[var(--color-border)] mx-auto mb-4"></i>
                    <h3 class="text-lg font-semibold text-[var(--color-text-muted)] mb-2">Sin proyectos</h3>
                    <p class="text-[var(--color-text-muted)]">Este estudiante no tiene proyectos registrados.</p>
                </div>
            </div>
        `;
    } else {
        let totalScore = 0;
        let totalCalificacionesGlobal = 0;
        
        studentData.forEach(project => {
            if (project.calificado) {
                totalScore += project.puntuacion * project.total_calificaciones;
                totalCalificacionesGlobal += project.total_calificaciones;
            }
        });
        
        const averageScore = totalCalificacionesGlobal > 0 ? totalScore / totalCalificacionesGlobal : 0;
        
        let projectsHTML = '';
        studentData.forEach(project => {
            projectsHTML += `
                <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-[var(--color-text)]">${escapeHtml(project.titulo)}</h4>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                ${project.calificado ? project.total_evaluadores + ' evaluadores' : 'Sin evaluar'}
                            </p>
                        </div>
                        ${project.calificado ? `
                        <span class="text-lg font-bold ${project.puntuacion >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}">
                            ${project.puntuacion.toFixed(1)}/5
                        </span>
                        ` : `
                        <span class="text-lg font-bold text-[var(--color-text-muted)]">-</span>
                        `}
                    </div>
                    
                    ${project.calificado ? `
                    <div class="space-y-2">
                        ${getCriteriosHTML(project)}
                    </div>
                    <div class="mt-3">
                        <button class="flex items-center gap-2 px-4 py-2 bg-[var(--color-primary)] text-[var(--color-navbar-text)] rounded-lg hover:bg-[var(--color-primary)]/90 transition-colors text-sm"
                                onclick="openEvaluationModal('${project.id}')">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Ver Evaluación Completa
                        </button>
                    </div>
                    ` : `
                    <p class="text-[var(--color-text-muted)] text-sm">Este proyecto aún no ha sido evaluado.</p>
                    `}
                </div>
            `;
        });
        
        document.getElementById('studentResultsSection').innerHTML = `
            <div class="bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-[var(--color-heading)]">Resultados de ${escapeHtml(userName)}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold ${averageScore >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}">
                            ${averageScore.toFixed(1)}/5
                        </div>
                        <div class="text-sm text-[var(--color-text-muted)]">Promedio General</div>
                    </div>
                </div>
                <div class="space-y-4">
                    ${projectsHTML}
                </div>
            </div>
        `;
    }
    
    document.getElementById('studentResultsSection').classList.remove('hidden');
    
    const currentResults = document.getElementById('currentStudentResults');
    if (currentResults) {
        currentResults.style.display = 'none';
    }
    
    if (window.lucide) {
        lucide.createIcons();
    }
}

function getCriteriosHTML(project) {
    const criteriosMostrados = [];
    let criteriosHTML = '';
    
    project.evaluaciones_completas.forEach(evaluation => {
        const calificacion = parseFloat(evaluation[6]);
        const criterioNombre = evaluation[7];
        const observacion = evaluation[8];
        
        if (calificacion > 0 && !criteriosMostrados.includes(criterioNombre)) {
            criteriosMostrados.push(criterioNombre);
            criteriosHTML += `
                <div class="flex justify-between items-center p-2 bg-[var(--color-dropdown-hover)] rounded">
                    <span class="text-sm text-[var(--color-text)]">
                        ${escapeHtml(criterioNombre)}
                    </span>
                    <div class="flex items-center gap-3">
                        ${observacion ? `
                        <span class="text-xs text-[var(--color-text-muted)]" title="${escapeHtml(observacion)}">
                            <i data-lucide="message-circle" class="w-3 h-3"></i>
                        </span>
                        ` : ''}
                        <span class="text-sm font-medium ${calificacion >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}">
                            ${calificacion.toFixed(1)}/5
                        </span>
                    </div>
                </div>
            `;
        }
    });
    
    return criteriosHTML;
}

function openEvaluationModal(projectId) {
    let project;
    
    <?php if ($isEstudiante): ?>
    project = studentProjectsData.find(p => p.id == projectId);
    <?php elseif ($isDocente): ?>
    for (const userId in allStudentData) {
        project = allStudentData[userId].find(p => p.id == projectId);
        if (project) break;
    }
    <?php endif; ?>
    
    if (!project) {
        showErrorInModal('Proyecto no encontrado');
        return;
    }
    
    if (!project.calificado) {
        showErrorInModal('Este proyecto aún no ha sido evaluado');
        return;
    }
    
    showFullEvaluationDetails(project);
    document.getElementById('studentEvaluationModal').classList.remove('hidden');
    
    if (window.lucide) {
        lucide.createIcons();
    }
}

function showErrorInModal(message) {
    document.getElementById('studentModalContent').innerHTML = `
        <div class="text-center py-12">
            <i data-lucide="alert-circle" class="w-20 h-20 text-[var(--color-border)] mx-auto mb-4"></i>
            <h3 class="text-xl font-semibold text-[var(--color-text-muted)] mb-2">${message}</h3>
        </div>
    `;
    document.getElementById('studentEvaluationModal').classList.remove('hidden');
}

function showFullEvaluationDetails(project) {
    document.getElementById('modalTitle').textContent = `Evaluación: ${escapeHtml(project.titulo)}`;
    document.getElementById('modalSubtitle').textContent = 'Resultados detallados por evaluador y criterio';
    
    let modalContent = `
        <div class="space-y-6">
            <div class="bg-[var(--color-surface-alt)] rounded-lg p-6 border border-[var(--color-border)]">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-[var(--color-heading)] mb-2">${escapeHtml(project.titulo)}</h3>
                        <p class="text-[var(--color-text-muted)] text-lg mb-4">${escapeHtml(project.descripcion)}</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-sm px-3 py-1 rounded-full font-medium">${project.linea_nombre}</span>
                            <span class="bg-[var(--color-success)]/10 text-[var(--color-success)] text-sm px-3 py-1 rounded-full font-medium">Fase ${project.fase}</span>
                            <span class="bg-[var(--color-accent)]/10 text-[var(--color-accent)] text-sm px-3 py-1 rounded-full font-medium">v${project.version}</span>
                        </div>
                    </div>
                    <div class="text-center lg:text-right">
                        <div class="flex items-center justify-center lg:justify-end gap-2 mb-2">
                            <i data-lucide="star" class="w-8 h-8 fill-yellow-400 text-yellow-400"></i>
                            <span class="text-3xl font-bold text-[var(--color-heading)]">${project.puntuacion.toFixed(1)}</span>
                            <span class="text-lg text-[var(--color-text-muted)]">/5.0</span>
                        </div>
                        <span class="text-lg px-4 py-2 rounded-full ${
                            project.puntuacion >= 3 ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-danger)]/10 text-[var(--color-danger)]'
                        } font-semibold">
                            ${project.puntuacion >= 3 ? 'APROBADO' : 'REPROBADO'}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                <div class="bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-primary)]">${project.total_evaluadores}</div>
                    <div class="text-sm text-[var(--color-text-muted)] font-medium">Evaluadores</div>
                </div>
                <div class="bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold text-[var(--color-success)]">${project.criterios_evaluados}</div>
                    <div class="text-sm text-[var(--color-text-muted)] font-medium">Criterios</div>
                </div>
                <div class="bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-lg p-4">
                    <div class="text-2xl font-bold ${
                        project.puntuacion >= 3 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'
                    }">
                        ${project.puntuacion >= 3 ? 'A' : 'R'}
                    </div>
                    <div class="text-sm text-[var(--color-text-muted)] font-medium">Estado</div>
                </div>
            </div>`;

    Object.entries(project.evaluadores).forEach(([evaluadorId, evaluadorData]) => {
        const nombreCompleto = evaluadorData.nombre_completo || 'Evaluador';
        
        modalContent += `
            <div class="bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                    <div>
                        <h4 class="text-xl font-semibold text-[var(--color-heading)] mb-1">${escapeHtml(nombreCompleto)}</h4>
                    </div>
                    <div class="mt-2 lg:mt-0">
                        <span class="text-xl font-bold ${getScoreColor(evaluadorData.promedio)}">
                            ${evaluadorData.promedio.toFixed(1)}/5
                        </span>
                        <span class="text-sm text-[var(--color-text-muted)] ml-2">Promedio individual</span>
                    </div>
                </div>
                
                ${evaluadorData.comentario_general ? `
                <div class="bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 rounded-lg p-4 mb-4">
                    <h5 class="font-semibold text-[var(--color-primary)] mb-2 flex items-center">
                        <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                        Comentario General del Evaluador
                    </h5>
                    <p class="text-[var(--color-primary)]">${escapeHtml(evaluadorData.comentario_general)}</p>
                </div>
                ` : ''}
                
                <div class="space-y-3">
                    <h5 class="font-semibold text-[var(--color-text)] mb-3">Calificaciones por Criterio:</h5>`;
        
        evaluadorData.criterios.forEach(criterio => {
            const porcentaje = (criterio.calificacion / 5) * 100;
            modalContent += `
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)]">
                        <div class="flex-1">
                            <span class="font-medium text-[var(--color-text)]">${escapeHtml(criterio.nombre)}</span>
                            ${criterio.comentario ? `
                            <div class="mt-1">
                                <p class="text-xs text-[var(--color-text-muted)]"><strong>Comentario:</strong> ${escapeHtml(criterio.comentario)}</p>
                            </div>
                            ` : ''}
                        </div>
                        <div class="flex items-center gap-3 ml-4">
                            <span class="text-sm font-semibold ${getScoreColor(criterio.calificacion)}">
                                ${criterio.calificacion.toFixed(1)}/5
                            </span>
                            <div class="w-20 bg-[var(--color-border)] rounded-full h-2">
                                <div class="h-2 rounded-full ${getScoreBarColor(criterio.calificacion)}" style="width: ${porcentaje}%"></div>
                            </div>
                        </div>
                    </div>`;
        });
        
        modalContent += `
                </div>
            </div>`;
    });

    modalContent += `
            <div class="bg-[var(--color-surface-alt)] rounded-lg border border-[var(--color-border)] p-6">
                <h4 class="text-xl font-semibold text-[var(--color-heading)] mb-6 flex items-center">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 mr-3 text-[var(--color-success)]"></i>
                    Resumen General por Criterio
                </h4>
                <div class="space-y-4">`;
    
    Object.entries(project.criterios).forEach(([criterio, data]) => {
        const promedio = data.calificaciones.length > 0 ? 
            data.calificaciones.reduce((a, b) => a + b, 0) / data.calificaciones.length : 0;
        const porcentaje = (promedio / 5) * 100;
        
        modalContent += `
                    <div class="border border-[var(--color-border)] rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="text-lg font-semibold text-[var(--color-text)]">${escapeHtml(criterio)}</h5>
                            <span class="text-lg font-bold ${getScoreColor(promedio)}">
                                ${promedio.toFixed(1)}/5
                            </span>
                        </div>
                        <div class="w-full bg-[var(--color-border)] rounded-full h-3 mb-4">
                            <div class="h-3 rounded-full ${getScoreBarColor(promedio)}" style="width: ${porcentaje}%"></div>
                        </div>`;
        
        if (data.comentarios && data.comentarios.length > 0) {
            modalContent += `
                        <div class="space-y-2">
                            <h6 class="font-medium text-[var(--color-text)] text-sm">Comentarios de los evaluadores:</h6>`;
            
            data.comentarios.forEach(comentarioData => {
                const evaluadorNombre = comentarioData.evaluador_nombre || 'Evaluador';
                modalContent += `
                            <div class="bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 rounded p-3">
                                <p class="text-sm text-[var(--color-primary)] font-medium mb-1">${escapeHtml(evaluadorNombre)}:</p>
                                <p class="text-sm text-[var(--color-primary)]">${escapeHtml(comentarioData.comentario)}</p>
                            </div>`;
            });
            
            modalContent += `</div>`;
        }
        
        modalContent += `</div>`;
    });
    
    modalContent += `
                </div>
            </div>
        </div>`;
    
    document.getElementById('studentModalContent').innerHTML = modalContent;
}

function closeStudentEvaluationModal() {
    document.getElementById('studentEvaluationModal').classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getScoreColor(score) {
    const numScore = parseFloat(score);
    if (numScore >= 3) return 'text-[var(--color-success)]';
    return 'text-[var(--color-danger)]';
}

function getScoreBarColor(score) {
    const numScore = parseFloat(score);
    if (numScore >= 3) return 'bg-[var(--color-success)]';
    return 'bg-[var(--color-danger)]';
}

document.addEventListener('DOMContentLoaded', function() {
    const filterEstado = document.getElementById('filterEstado');
    if (filterEstado) {
        filterEstado.addEventListener('change', function() {
            const estadoFilter = this.value;
            const projectRows = document.querySelectorAll('.project-row');
            
            projectRows.forEach(row => {
                const projectEstado = row.getAttribute('data-estado');
                const showEstado = !estadoFilter || projectEstado === estadoFilter;
                row.style.display = showEstado ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.show-student-results').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            showStudentResults(userId, userName);
        });
    });
});
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-fadeIn {
    animation: fadeIn 0.8s ease-out;
}

.animate-slideUp {
    animation: slideUp 1s ease-out;
}
</style>