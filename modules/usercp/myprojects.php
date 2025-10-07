<?php
if(!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
if(!accessManager()->canAccess($_SESSION['userid'], ['estudiante', 'docente'], [['module' => 'usercp', 'action' => 'projects']])) {
    die('No tienes permisos para acceder a este módulo.');
}
include(__PATH_MODULES__.'/header.php');

$projectManager = new ProjectManager($pdo);
$researchLineManager = new ResearchLineManager($pdo);
$roleManager = new RoleManager($pdo);
$uploadManager = new uploadManager();
$userId = $_SESSION['userid'];
$handler = new Handler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $projectId = (int)($_POST['id'] ?? 0);
        
        if (!$projectId) {
            throw new Exception("ID de proyecto no válido");
        }

        $project = $projectManager->getProject($projectId);
        if (!$project) {
            throw new Exception("Proyecto no encontrado");
        }

        $projectDirName = $project['directorio'] ?? 'proyecto_' . date('YmdHis');
        $documentPath = $project['documento'] ?? null;

        $formData = [
            'titulo' => isset($_POST['titulo']) ? trim($_POST['titulo']) : $project['titulo'],
            'linea_investigacion_id' => isset($_POST['linea_investigacion_id']) ? intval($_POST['linea_investigacion_id']) : $project['linea_investigacion_id'],
            'fase' => isset($_POST['fase']) ? trim($_POST['fase']) : $project['fase'],
            'version' => isset($_POST['version']) ? trim($_POST['version']) : $project['version'],
            'timer_segundos' => $project['timer_segundos'],
            'docentes' => [],
            'evaluadores' => [],
            'investigadores' => [],
            'descripcion' => $_POST['descripcion'] ?? $project['descripcion'],
            'palabras_clave' => $_POST['palabras_clave'] ?? $project['palabras_clave'],
            'documento' => $documentPath,
            'directorio' => $projectDirName,
            'visibilidad' => $project['visibilidad'] ?? 'privado',
            'hora_programada' => $project['hora_programada'],
            'fecha_presentacion' => $project['fecha_presentacion']
        ];

        if (isset($_POST['investigadores']) && is_array($_POST['investigadores'])) {
            foreach ($_POST['investigadores'] as $inv) {
                if (isset($inv['usuario_uid']) && !empty($inv['usuario_uid'])) {
                    $formData['investigadores'][] = [
                        'usuario_uid' => (int)$inv['usuario_uid'],
                        'rol' => in_array($inv['rol'] ?? 'colaborador', ['principal', 'colaborador']) 
                            ? $inv['rol'] 
                            : 'colaborador'
                    ];
                }
            }
        }
        
        if (isset($_POST['docentes']) && is_array($_POST['docentes'])) {
            foreach ($_POST['docentes'] as $doc) {
                if (isset($doc['usuario_uid']) && !empty($doc['usuario_uid'])) {
                    $formData['docentes'][] = [
                        'usuario_uid' => (int)$doc['usuario_uid'],
                        'rol' => in_array($doc['rol'] ?? 'asesor', ['director', 'asesor', 'jurado']) 
                            ? $doc['rol'] 
                            : 'asesor'
                    ];
                }
            }
        }
        
        if (isset($_POST['revisores']) && is_array($_POST['revisores'])) {
            foreach ($_POST['revisores'] as $revisorId) {
                if (!empty($revisorId)) {
                    $formData['evaluadores'][] = (int)$revisorId;
                }
            }
        }

        switch ($action) {
            case 'update':    
                $result = $projectManager->updateProject($projectId, $formData);
                
                if ($result) {
                    echo json_encode(['success' => true, 'projectId' => $projectId]);
                } else {
                    throw new Exception("Error al actualizar el proyecto");
                }
                exit;
                
            case 'delete':
                $result = $projectManager->deleteProject($projectId);
                if ($result) {
                    $project = $projectManager->getProject($projectId);
                    if ($project && !empty($project['directorio'])) {
                        $projectDir = __PATH_UPLOADS__ . 'docs/projects/' . $project['directorio'];
                        if (is_dir($projectDir)) {
                            $uploadManager->deleteDirectory($projectDir);
                        }
                    }
                    
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al eliminar el proyecto");
                }
                exit;
                
            case 'deleteDocument':
                $documentPath = $_POST['document_path'] ?? '';
                if (empty($documentPath)) {
                    throw new Exception("Ruta de documento no válida");
                }
                
                $result = $uploadManager->delete($documentPath);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al eliminar el documento");
                }
                exit;

            case 'updateDocument':
                $projectId = $_POST['id'] ?? '';
                $documentPath = $_POST['document_path'] ?? '';
                $documentName = $_POST['document_name'] ?? '';

                if (empty($projectId)) {
                    throw new Exception("ID de proyecto no válido");
                }

                if (empty($documentPath)) {
                    throw new Exception("Ruta de documento no válida");
                }

                $project = $projectManager->getProject($projectId);
                if (!$project) {
                    throw new Exception("Proyecto no encontrado");
                }

                if (!isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("No se proporcionó ningún documento válido");
                }

                $uploadManager->setAllowedExtensions(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                $uploadManager->setMaxFileSize(20 * 1024 * 1024);

                if (!empty($documentPath) && file_exists($documentPath)) {
                    if (!$uploadManager->delete($documentPath)) {
                        throw new Exception("Error al eliminar el documento existente");
                    }
                }

                $projectDirName = $project['directorio'] ?? 'proyecto_' . date('YmdHis');
                $newDocumentPath = $uploadManager->upload($_FILES['documento'], $projectDirName, 'projects');

                if ($newDocumentPath === false) {
                    throw new Exception("Error al subir nuevo documento: " . implode(", ", $uploadManager->getErrors()));
                }

                $currentProjectData = $projectManager->getProject($projectId);
                $updateData = [
                    'documento' => $newDocumentPath,
                    'titulo' => $currentProjectData['titulo'],
                    'linea_investigacion_id' => $currentProjectData['linea_investigacion_id'],
                    'fase' => $currentProjectData['fase'],
                    'version' => $currentProjectData['version'],
                    'timer_segundos' => $currentProjectData['timer_segundos'],
                    'descripcion' => $currentProjectData['descripcion'],
                    'palabras_clave' => $currentProjectData['palabras_clave'],
                    'visibilidad' => $currentProjectData['visibilidad'],
                    'hora_programada' => $currentProjectData['hora_programada'],
                    'fecha_presentacion' => $currentProjectData['fecha_presentacion'],
                    'docentes' => $currentProjectData['teachers'] ?? [],
                    'evaluadores' => $currentProjectData['reviewers'] ?? [],
                    'investigadores' => $currentProjectData['researchers'] ?? []
                ];

                $result = $projectManager->updateProject($projectId, $updateData);

                if ($result) {
                    echo json_encode(['success' => true, 'documentPath' => $newDocumentPath]);
                } else {
                    throw new Exception("Error al actualizar el documento en la base de datos");
                }
                exit;

            case 'addDocument':
                $project = $projectManager->getProject($projectId);
                if (!$project) {
                    throw new Exception("Proyecto no encontrado");
                }

                if (isset($_FILES['documento']) && $_FILES['documento']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadManager->setAllowedExtensions(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                    $uploadManager->setMaxFileSize(20 * 1024 * 1024);
                    
                    $projectDirName = $project['directorio'] ?? null;
                    
                    if (!$projectDirName) {
                        $projectDirName = 'proyecto_'.date('YmdHis').'_'.bin2hex(random_bytes(4));
                    }
                    
                    $existingDocumentPath = $project['documento'] ?? null;
                    
                    if ($existingDocumentPath && file_exists($existingDocumentPath)) {
                        if ($uploadManager->replaceFileDirectly($_FILES['documento'], $existingDocumentPath)) {
                            $documentPath = $existingDocumentPath;
                        } else {
                            throw new Exception("Error al reemplazar documento: " . implode(", ", $uploadManager->getErrors()));
                        }
                    } else {
                        $documentPath = $uploadManager->upload($_FILES['documento'], $projectDirName, 'projects');
                    }
                    
                    if ($documentPath === false) {
                        throw new Exception("Error al subir documento: " . implode(", ", $uploadManager->getErrors()));
                    }
                    
                    $currentProjectData = $projectManager->getProject($projectId);
                    $updateData = [
                        'documento' => $documentPath,
                        'titulo' => $currentProjectData['titulo'],
                        'linea_investigacion_id' => $currentProjectData['linea_investigacion_id'],
                        'fase' => $currentProjectData['fase'],
                        'version' => $currentProjectData['version'],
                        'timer_segundos' => $currentProjectData['timer_segundos'],
                        'descripcion' => $currentProjectData['descripcion'],
                        'palabras_clave' => $currentProjectData['palabras_clave'],
                        'visibilidad' => $currentProjectData['visibilidad'],
                        'hora_programada' => $currentProjectData['hora_programada'],
                        'fecha_presentacion' => $currentProjectData['fecha_presentacion'],
                        'docentes' => $currentProjectData['teachers'] ?? [],
                        'evaluadores' => $currentProjectData['reviewers'] ?? [],
                        'investigadores' => $currentProjectData['researchers'] ?? []
                    ];
                    
                    if (!$project['directorio']) {
                        $updateData['directorio'] = $projectDirName;
                    }
                    
                    $result = $projectManager->updateProject($projectId, $updateData);
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'documentPath' => $documentPath]);
                    } else {
                        throw new Exception("Error al actualizar el documento en la base de datos");
                    }
                } else {
                    throw new Exception("No se proporcionó ningún documento");
                }
                exit;
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

$isTeacher = $roleManager->userHasRole($userId, 'Docente');
$projects = $projectManager->getProjectsByUser($userId);

$processedProjects = [];
foreach ($projects as $project) {
    $researchLine = $researchLineManager->getById($project[_CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_]);
    $researchers = $projectManager->getResearchers($project[_CLMN_WEBENGINE_PROJECT_ID_]);
    $teachers = $projectManager->getTeachers($project[_CLMN_WEBENGINE_PROJECT_ID_]);
    $reviewers = $projectManager->getReviewers($project[_CLMN_WEBENGINE_PROJECT_ID_]);
    
    $documents = [];
    $projectDir = $project[_CLMN_WEBENGINE_PROJECT_DIRECTORIO_] ?? null;
    
    if ($projectDir) {
        $basePath = __PATH_UPLOADS__ . 'docs/projects/' . $projectDir . '/';
        if (is_dir($basePath)) {
            $files = $uploadManager->listAllContents($basePath);
            foreach ($files as $file) {
                $relativePath = 'docs/projects/' . $projectDir . '/' . $file;
                $fullPath = $basePath . $file;
                
                if (file_exists($fullPath)) {
                    $documents[] = [
                        'id' => md5($file),
                        'name' => $file,
                        'path' => $relativePath,
                        'full_path' => $fullPath,
                        'size' => filesize($fullPath),
                        'url' => $handler->getDocumentUrl($relativePath),
                        'previewable' => $handler->canPreviewInBrowser($file),
                        'size_formatted' => $handler->getFileSize($relativePath)
                    ];
                }
            }
        }
    }
    
    $processedProjects[] = [
        'id' => $project[_CLMN_WEBENGINE_PROJECT_ID_],
        'titulo' => $project[_CLMN_WEBENGINE_PROJECT_TITULO_],
        'linea_investigacion_id' => $project[_CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_],
        'linea_investigacion' => $researchLine[DB_RESEARCH_LINE_NOMBRE] ?? 'No especificada',
        'fase' => $project[_CLMN_WEBENGINE_PROJECT_FASE_],
        'version' => $project[_CLMN_WEBENGINE_PROJECT_VERSION_],
        'timer_segundos' => $project[_CLMN_WEBENGINE_PROJECT_TIMER_SEGUNDOS_] ?? 0,
        'descripcion' => $project[_CLMN_WEBENGINE_PROJECT_DESCRIPCION_] ?? '',
        'palabras_clave' => $project[_CLMN_WEBENGINE_PROJECT_PALABRAS_CLAVE_] ?? '',
        'visibilidad' => $project[_CLMN_WEBENGINE_PROJECT_VISIBILIDAD_] ?? 'privado',
        'hora_programada' => $project[_CLMN_WEBENGINE_PROJECT_HORA_PROGRAMADA_] ?? '',
        'fecha_presentacion' => $project[_CLMN_WEBENGINE_PROJECT_FECHA_PRESENTACION_] ?? '',
        'activo' => $project[_CLMN_WEBENGINE_PROJECT_ACTIVO_] ?? 1,
        'estado' => $project[_CLMN_WEBENGINE_PROJECT_ESTADO_] ?? 'nuevo',
        'calificado' => $project[_CLMN_WEBENGINE_PROJECT_CALIFICADO_] ?? 0,
        'puntuacion' => $project[_CLMN_WEBENGINE_PROJECT_PUNTUACION_] ?? 0,
        'fecha_creacion' => $project[_CLMN_WEBENGINE_PROJECT_CREADO_EN_] ?? '',
        'fecha_actualizacion' => $project[_CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_] ?? '',
        'investigadores' => $researchers,
        'docentes' => $teachers,
        'evaluadores' => $reviewers,
        'documents' => $documents
    ];
}

$projectsJson = json_encode($processedProjects);
$researchLinesJson = json_encode($researchLineManager->getAllActivas());
$allResearchers = $roleManager->getAllStudents() ?? [];
$allTeachers = $roleManager->getAllTeachers() ?? [];
$allReviewers = $roleManager->getAllEvaluators() ?? [];
?>

<div class="max-w-[1400px] mx-auto mb-12">
    <!-- Navegación Mejorada -->
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-accent)] text-[var(--color-navbar-text)] border border-[var(--color-accent)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Proyectos</span>
            </a>
            <?php endif; ?>
            
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Resultados</span>
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

    <!-- Header de la Página -->
    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                <i data-lucide="folder-open" class="w-5 h-5 text-[var(--color-primary)]"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-heading)]">Mis Proyectos</h2>
                <p class="text-[var(--color-text-muted)] text-sm">Gestiona tus proyectos de investigación</p>
            </div>
        </div>
    </div>

    <?php if (!empty($processedProjects)): ?>
        <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-1" id="projects-grid">
            <?php foreach ($processedProjects as $project): ?>
                <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp project-card">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center shadow">
                                    <span class="text-white font-bold text-sm"><?= substr($project['titulo'], 0, 1) ?: 'P' ?></span>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 <?= $project['visibilidad'] === 'publico' ? 'bg-[var(--color-success)]' : 'bg-[var(--color-text-muted)]' ?> rounded-full border-2 border-white"></div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-[var(--color-text)] text-lg"><?= htmlspecialchars($project['titulo']) ?></h3>
                                <p class="text-sm text-[var(--color-text-muted)] truncate">v<?= htmlspecialchars($project['version'] ?: '1.0') ?></p>
                            </div>
                        </div>

                        <?php if ($isTeacher): ?>
                        <div class="flex items-center space-x-2 shrink-0">
                            <button onclick="showProjectDetails(<?= $project['id'] ?>)" class="p-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200" title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4 text-[var(--color-primary)]"></i>
                            </button>
                            
                            <button onclick="editProject(<?= $project['id'] ?>)" class="p-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200" title="Editar proyecto">
                                <i data-lucide="edit" class="w-4 h-4 text-[var(--color-success)]"></i>
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center space-x-2 shrink-0">
                            <button onclick="showProjectDetails(<?= $project['id'] ?>)" class="p-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200" title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4 text-[var(--color-primary)]"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-md text-xs font-medium">
                                <?= ucfirst($project['fase'] ?: 'propuesta') ?>
                            </span>
                            <span class="px-3 py-1 <?= $project['visibilidad'] === 'publico' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-text-muted)]/10 text-[var(--color-text-muted)]' ?> rounded-md text-xs font-medium">
                                <?= $project['visibilidad'] === 'publico' ? 'Público' : 'Privado' ?>
                            </span>
                            <span class="px-3 py-1 <?= $project['activo'] ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-text-muted)]/10 text-[var(--color-text-muted)]' ?> rounded-md text-xs font-medium">
                                <?= $project['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <span class="px-3 py-1 bg-[var(--color-secondary)]/10 text-[var(--color-secondary)] rounded-md text-xs font-medium">
                                <?= htmlspecialchars($project['linea_investigacion']) ?>
                            </span>
                            <?php if ($project['timer_segundos'] > 0): ?>
                            <span class="px-3 py-1 bg-[var(--color-warning)]/10 text-[var(--color-warning)] rounded-md text-xs font-medium">
                                <?= floor($project['timer_segundos'] / 60) ?> min
                            </span>
                            <?php endif; ?>
                            <?php if ($project['puntuacion']): ?>
                            <span class="px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-md text-xs font-medium">
                                <?= $project['puntuacion'] ?> pts
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($project['descripcion']): ?>
                        <p class="text-sm text-[var(--color-text-muted)]"><?= htmlspecialchars($project['descripcion']) ?></p>
                        <?php endif; ?>
                        
                        <?php 
                        $principalResearcher = array_filter($project['investigadores'], function($researcher) {
                            return $researcher['rol'] === 'principal';
                        });
                        $principalResearcher = reset($principalResearcher);
                        $principalName = $principalResearcher ? $profileManager->getFullName($principalResearcher[_CLMN_PROJRES_UID_]) : 'Sin asignar';
                        ?>
                        <p class="text-sm text-[var(--color-text-muted)]">Investigador principal: <?= htmlspecialchars($principalName) ?></p>
                        
                        <?php if ($project['fecha_presentacion']): ?>
                        <p class="text-sm text-[var(--color-text-muted)]">Presentación: <?= date('d/m/Y', strtotime($project['fecha_presentacion'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['documents'])): ?>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <?php foreach ($project['documents'] as $doc): ?>
                                <span class="px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-md text-xs font-medium flex items-center space-x-1">
                                    <i data-lucide="file-text" class="w-3 h-3"></i>
                                    <span><?= htmlspecialchars($doc['name']) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex space-x-3">
                        <button onclick="showProjectDetails(<?= $project['id'] ?>)" class="flex-1 flex items-center justify-center space-x-1 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 text-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            <span>Ver</span>
                        </button>
                        <?php if ($isTeacher): ?>
                        <button onclick="editProject(<?= $project['id'] ?>)" class="flex-1 flex items-center justify-center space-x-1 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 text-sm">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Editar</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-8 border border-[var(--color-border)] text-center animate-slideUp">
            <i data-lucide="folder-x" class="w-12 h-12 mx-auto text-[var(--color-text-muted)] opacity-50"></i>
            <p class="mt-4 text-[var(--color-text-muted)] font-medium">No tienes proyectos asociados.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modales (mantienen la misma estructura pero con los nuevos estilos) -->
<div id="project-details-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="modal-content w-full max-w-4xl bg-[var(--color-surface)] rounded-2xl p-6 max-h-[90vh] overflow-y-auto border border-[var(--color-border)]">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-[var(--color-heading)]">Detalles del Proyecto</h2>
            <button onclick="closeModal('project-details-modal')" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-6" id="project-details-content"></div>
    </div>
</div>

<div id="project-edit-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-[var(--color-surface)] rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl mx-4 border border-[var(--color-border)]">
        <div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Editar Proyecto</h2>
                    <p class="text-sm text-white/80">Complete todos los campos obligatorios (*)</p>
                </div>
                <button onclick="closeModal('project-edit-modal')" class="p-2 text-white/80 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <form id="projectEditForm" method="POST" class="space-y-8">            
                <input type="hidden" name="id" id="edit-project-id">
                <input type="hidden" name="action" value="update">
                
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-primary)]/20 rounded-full flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Información Básica</h3>
                    </div>
                    
                    <div class="grid gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-[var(--color-text)]">Título del Proyecto *</label>
                            <input type="text" name="titulo" id="edit-titulo" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Ingrese el título del proyecto" required>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-[var(--color-text)]">Línea de Investigación *</label>
                                <select name="linea_investigacion_id" id="edit-linea-investigacion" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                                    <option value="">Seleccionar línea</option>
                                    <?php foreach ($researchLineManager->getAllActivas() as $line): ?>
                                        <option value="<?= $line['id'] ?>"><?= htmlspecialchars($line['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-secondary)]/20 rounded-full flex items-center justify-center">
                            <i data-lucide="git-commit" class="w-4 h-4 text-[var(--color-secondary)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Versión del Proyecto</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-[var(--color-text)]">Versión</label>
                            <input type="text" name="version" id="edit-version" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-[var(--color-text)]">Fase *</label>
                            <select name="fase" id="edit-fase" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                                <option value="propuesta">Propuesta</option>
                                <option value="desarrollo">Desarrollo</option>
                                <option value="aplicacion">Aplicación</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-[var(--color-text)]">Descripción *</label>
                        <textarea rows="4" name="descripcion" id="edit-descripcion" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Descripción detallada de esta versión..." required></textarea>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-[var(--color-text)]">Palabras Clave</label>
                        <input type="text" name="palabras_clave" id="edit-palabras-clave" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Separadas por comas: IA, Machine Learning, Educación" >
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-success)]/20 rounded-full flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4 text-[var(--color-success)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Investigadores</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-[var(--color-text)]">Investigadores *</h4>
                            <button type="button" onclick="addResearcher('edit')" class="flex items-center space-x-1 text-xs text-[var(--color-primary)] hover:text-[var(--color-primary)]/80">
                                <i data-lucide="plus" class="w-3 h-3"></i> 
                                <span>Añadir investigador</span>
                            </button>
                        </div>
                        
                        <div id="edit-researchersContainer">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between space-y-4 sm:space-y-0 pt-6 border-t border-[var(--color-border)]">
                    <button type="button" onclick="closeModal('project-edit-modal')" class="order-2 sm:order-1 px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Cancelar</span>
                    </button>
                    
                    <button type="submit" name="webengineEdit_submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:from-[var(--color-primary)]/90 hover:to-[var(--color-secondary)]/90 transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-lg">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="document-preview-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="modal-content w-full max-w-6xl bg-[var(--color-surface)] rounded-2xl p-6 max-h-[90vh] overflow-hidden flex flex-col border border-[var(--color-border)]" style="height: 85vh; width: 90vw;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[var(--color-heading)]" id="document-preview-title">Previsualización de Documento</h3>
            <button onclick="closeModal('document-preview-modal')" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex-1 border border-[var(--color-border)] rounded-lg overflow-hidden" style="min-height: 70vh;">
            <iframe id="document-preview-iframe" class="w-full h-full" frameborder="0" style="min-height: 70vh;"></iframe>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="downloadCurrentPreview()" class="px-4 py-2 bg-[var(--color-primary)] text-white rounded-lg hover:bg-[var(--color-primary)]/90 transition-all duration-200 flex items-center space-x-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Descargar Documento</span>
            </button>
        </div>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-[var(--color-surface)] rounded-xl p-6 max-w-md w-full mx-4 border border-[var(--color-border)]">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-[var(--color-danger)]/20 rounded-full flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-[var(--color-danger)]"></i>
            </div>
            <h3 class="text-lg font-semibold text-[var(--color-heading)]">Confirmar eliminación</h3>
        </div>
        <p class="text-[var(--color-text-muted)] mb-6">¿Estás seguro de que deseas eliminar permanentemente este proyecto? Esta acción no se puede deshacer.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeModal('delete-confirm-modal')" class="px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200">
                Cancelar
            </button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-[var(--color-danger)] text-white rounded-lg hover:bg-[var(--color-danger)]/90 transition-all duration-200">
                Eliminar Proyecto
            </button>
        </div>
    </div>
</div>

<script>
// Funciones de utilidad
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeSingleQuote(text) {
    if (!text) return '';
    return text.replace(/'/g, "\\'");
}

function capitalizeFirstLetter(text) {
    return text ? text.charAt(0).toUpperCase() + text.slice(1) : '';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-ES');
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        pdf: { icon: 'file-text', color: 'bg-[var(--color-danger)]' },
        doc: { icon: 'file-text', color: 'bg-[var(--color-primary)]' },
        docx: { icon: 'file-text', color: 'bg-[var(--color-primary)]' },
        xls: { icon: 'file-spreadsheet', color: 'bg-[var(--color-success)]' },
        xlsx: { icon: 'file-spreadsheet', color: 'bg-[var(--color-success)]' },
        ppt: { icon: 'file-presentation', color: 'bg-[var(--color-warning)]' },
        pptx: { icon: 'file-presentation', color: 'bg-[var(--color-warning)]' },
        default: { icon: 'file', color: 'bg-[var(--color-secondary)]' }
    };
    return icons[ext] || icons.default;
}

function formatFileSize(bytes) {
    if (!bytes) return '0 KB';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function getUserFullName(user) {
    return user.nombre_completo || user.first_name + ' ' + user.last_name || user.nombre || user.usuario || 'Usuario sin nombre';
}

function getUserEmail(user) {
    return user.email || user.correo || 'Sin correo electrónico';
}

function getUserUsername(user) {
    return user.username || user.usuario || 'Sin nombre de usuario';
}

function getUserStatus(user) {
    return user.estado || user.status || 'activo';
}

function getUserStatusClass(user) {
    const status = getUserStatus(user);
    return status === 'activo' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]';
}

function getUserStatusIcon(user) {
    const status = getUserStatus(user);
    return status === 'activo' ? 'check-circle' : 'x-circle';
}

function getUserInitials(user) {
    const fullName = getUserFullName(user);
    return fullName.charAt(0) || 'U';
}

// Funciones principales (se mantienen igual pero con los nuevos estilos CSS)
function showProjectDetails(projectId) {
    if (typeof window.allProjects !== 'undefined') {
        const project = window.allProjects.find(p => p.id == projectId);
        if (!project) {
            alert('Proyecto no encontrado');
            return;
        }
        
        const isTeacher = <?= $isTeacher ? 'true' : 'false' ?>;
        const modalContent = document.getElementById('project-details-content');
        
        modalContent.innerHTML = `
            <div class="flex items-center space-x-6 p-6 bg-[var(--color-surface-alt)] rounded-xl">
                <div class="relative">
                    <div class="w-20 h-20 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white text-2xl font-bold">${project.titulo?.charAt(0) || 'P'}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-6 h-6 ${project.visibilidad === 'publico' ? 'bg-[var(--color-success)]' : 'bg-[var(--color-text-muted)]'} rounded-full border-4 border-white"></div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-[var(--color-heading)]">${escapeHtml(project.titulo)}</h3>
                    <p class="text-[var(--color-text-muted)] mb-2">Versión ${project.version || '1'}</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-full text-sm font-semibold">${capitalizeFirstLetter(project.fase)}</span>
                        <span class="px-3 py-1 ${project.visibilidad === 'publico' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-text-muted)]/10 text-[var(--color-text-muted)]'} rounded-full text-sm font-semibold">${project.visibilidad === 'publico' ? 'Público' : 'Privado'}</span>
                        <span class="px-3 py-1 ${project.activo ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-text-muted)]/10 text-[var(--color-text-muted)]'} rounded-full text-sm font-semibold">${project.activo ? 'Activo' : 'Inactivo'}</span>
                        ${project.calificado ? `
                        <span class="px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-full text-sm font-semibold">Calificado</span>
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-[var(--color-heading)]">Información Básica</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="hash" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">ID: ${project.id}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i data-lucide="calendar" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Creado: ${formatDate(project.fecha_creacion)}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Actualizado: ${formatDate(project.fecha_actualizacion)}</span>
                        </div>
                        ${project.linea_investigacion ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="git-branch" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">${escapeHtml(project.linea_investigacion)}</span>
                        </div>
                        ` : ''}
                        ${project.descripcion ? `
                        <div class="flex items-start space-x-3">
                            <i data-lucide="file-text" class="w-4 h-4 text-[var(--color-text-muted)] mt-0.5"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">${escapeHtml(project.descripcion)}</span>
                        </div>
                        ` : ''}
                        ${project.palabras_clave ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="tags" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">${escapeHtml(project.palabras_clave)}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-[var(--color-heading)]">Evaluación</h4>
                    <div class="space-y-3">
                        ${project.fecha_presentacion ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="calendar" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Fecha presentación: ${formatDate(project.fecha_presentacion)}</span>
                        </div>
                        ` : ''}
                        ${project.hora_programada ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="clock" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Hora programada: ${project.hora_programada}</span>
                        </div>
                        ` : ''}
                        ${project.timer_segundos > 0 ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="hourglass" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Duración evaluación: ${Math.floor(project.timer_segundos / 60)} minutos</span>
                        </div>
                        ` : ''}
                        ${project.puntuacion ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="star" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Puntuación: ${project.puntuacion} pts</span>
                        </div>
                        ` : ''}
                        ${project.estado ? `
                        <div class="flex items-center space-x-3">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text-muted)]">Estado: ${project.estado}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            ${project.investigadores?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-[var(--color-heading)]">Investigadores</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.investigadores.map(researcher => `
                        <div class="border border-[var(--color-border)] rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${getUserInitials(researcher)}
                                </div>
                                <div>
                                    <h5 class="font-medium text-[var(--color-text)]">${escapeHtml(getUserFullName(researcher))}</h5>
                                    <p class="text-xs text-[var(--color-text-muted)]">${researcher.rol === 'principal' ? 'Investigador Principal' : 'Colaborador'}</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserUsername(researcher))}</p>
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserEmail(researcher))}</p>
                                <p class="text-xs ${getUserStatusClass(researcher)}">
                                    <i data-lucide="${getUserStatusIcon(researcher)}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(getUserStatus(researcher))}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${project.docentes?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-[var(--color-heading)]">Docentes</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.docentes.map(teacher => `
                        <div class="border border-[var(--color-border)] rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-secondary)] to-[var(--color-accent)] rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${getUserInitials(teacher)}
                                </div>
                                <div>
                                    <h5 class="font-medium text-[var(--color-text)]">${escapeHtml(getUserFullName(teacher))}</h5>
                                    <p class="text-xs text-[var(--color-text-muted)]">${teacher.rol === 'director' ? 'Director' : 'Asesor'}</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserUsername(teacher))}</p>
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserEmail(teacher))}</p>
                                <p class="text-xs ${getUserStatusClass(teacher)}">
                                    <i data-lucide="${getUserStatusIcon(teacher)}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(getUserStatus(teacher))}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${project.evaluadores?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-[var(--color-heading)]">Evaluadores</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.evaluadores.map(reviewer => `
                        <div class="border border-[var(--color-border)] rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-danger)] to-[var(--color-warning)] rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${getUserInitials(reviewer)}
                                </div>
                                <div>
                                    <h5 class="font-medium text-[var(--color-text)]">${escapeHtml(getUserFullName(reviewer))}</h5>
                                    <p class="text-xs text-[var(--color-text-muted)]">Evaluador</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserUsername(reviewer))}</p>
                                <p class="text-[var(--color-text-muted)]"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(getUserEmail(reviewer))}</p>
                                <p class="text-xs ${getUserStatusClass(reviewer)}">
                                    <i data-lucide="${getUserStatusIcon(reviewer)}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(getUserStatus(reviewer))}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-[var(--color-heading)]">Documentos</h4>
                ${isTeacher ? `
                <div class="mb-4">
                    <button onclick="addDocument(${project.id})" 
                            class="flex items-center space-x-2 px-4 py-2 bg-[var(--color-success)] text-white rounded-lg hover:bg-[var(--color-success)]/90 transition-all duration-200">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Agregar Documento</span>
                    </button>
                </div>
                ` : ''}
                ${project.documents?.length > 0 ? `
                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
                    ${project.documents.map(doc => `
                        <div class="flex flex-col border border-[var(--color-border)] rounded-lg overflow-hidden hover:shadow-md transition-all duration-200">
                            <div class="flex items-center space-x-3 p-4 bg-[var(--color-surface-alt)]">
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg ${getFileIcon(doc.name).color}">
                                    <i data-lucide="${getFileIcon(doc.name).icon}" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[var(--color-text)] truncate" title="${escapeHtml(doc.name)}">${escapeHtml(doc.name)}</p>
                                    <p class="text-xs text-[var(--color-text-muted)]">${formatFileSize(doc.size)}</p>
                                </div>
                            </div>
                            <div class="flex border-t border-[var(--color-border)] divide-x divide-[var(--color-border)]">
                                <button onclick="previewDocument('${escapeSingleQuote(doc.url)}', '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-[var(--color-primary)] hover:bg-[var(--color-primary)]/10 transition-colors duration-200">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    <span>Previsualizar</span>
                                </button>
                                <a href="${doc.url}" download="${escapeHtml(doc.name)}" 
                                   class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-[var(--color-text-muted)] hover:bg-[var(--color-dropdown-hover)] transition-colors duration-200">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    <span>Descargar</span>
                                </a>
                                ${isTeacher ? `
                                <button onclick="updateDocument('${escapeSingleQuote(doc.full_path)}', ${project.id}, '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-[var(--color-success)] hover:bg-[var(--color-success)]/10 transition-colors duration-200">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    <span>Actualizar</span>
                                </button>
                                <button onclick="deleteDocument('${escapeSingleQuote(doc.full_path)}', ${project.id}, '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-[var(--color-danger)] hover:bg-[var(--color-danger)]/10 transition-colors duration-200">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span>Eliminar</span>
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
                ` : `
                <div class="col-span-full flex items-center space-x-3 text-[var(--color-text-muted)] p-4 bg-[var(--color-surface-alt)] rounded-lg">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span class="text-sm">No hay documentos asociados</span>
                </div>
                `}
            </div>

            ${isTeacher ? `
            <div class="flex space-x-4 pt-6">
                <button onclick="editProject(${project.id})" class="flex-1 px-6 py-3 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-xl hover:from-[var(--color-primary)]/90 hover:to-[var(--color-secondary)]/90 transition-all duration-200 font-medium shadow-lg">
                    <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
                    Editar Proyecto
                </button>
            </div>
            ` : ''}
        `;
        
        setTimeout(() => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
        
        showModal('project-details-modal');
    } else {
        console.error('La aplicación no se ha inicializado correctamente');
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function previewDocument(fileUrl, fileName) {
    window.currentPreviewDocument = { url: fileUrl, name: fileName };
    document.getElementById('document-preview-title').textContent = `Previsualización: ${fileName}`;
    const iframe = document.getElementById('document-preview-iframe');
    
    const ext = fileName.split('.').pop().toLowerCase();
    
    if (ext === 'pdf') {
        iframe.src = fileUrl;
    } else {
        iframe.srcdoc = `
            <html>
                <head><title>Previsualización</title></head>
                <body style="display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
                    <div style="text-align: center; padding: 20px;">
                        <h2>Previsualización no disponible</h2>
                        <p>Descargue el archivo para verlo.</p>
                    </div>
                </body>
            </html>
        `;
    }
    
    showModal('document-preview-modal');
}

function downloadCurrentPreview() {
    if (window.currentPreviewDocument) {
        const link = document.createElement('a');
        link.href = window.currentPreviewDocument.url;
        link.download = window.currentPreviewDocument.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function editProject(projectId) {
    const project = window.allProjects.find(p => p.id == projectId);
    if (!project) {
        alert('Proyecto no encontrado');
        return;
    }
    
    window.currentEditingProject = project;
    
    document.getElementById('edit-project-id').value = project.id;
    document.getElementById('edit-titulo').value = project.titulo || '';
    document.getElementById('edit-linea-investigacion').value = project.linea_investigacion_id || '';
    document.getElementById('edit-version').value = project.version || '1.0';
    document.getElementById('edit-fase').value = project.fase || 'propuesta';
    document.getElementById('edit-descripcion').value = project.descripcion || '';
    document.getElementById('edit-palabras-clave').value = project.palabras_clave || '';
    
    const researchersContainer = document.getElementById('edit-researchersContainer');
    researchersContainer.innerHTML = '';
    
    if (project.investigadores?.length > 0) {
        project.investigadores.forEach((researcher, index) => {
            const researcherField = document.createElement('div');
            researcherField.className = 'participant-field flex space-x-3 mb-3 items-end';
            researcherField.dataset.type = 'researcher';
            
            researcherField.innerHTML = `
                <div class="flex-1 grid md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                            <option value="">Seleccionar investigador</option>
                            ${window.allResearchers.map(user => `
                                <option value="${user.user_id}" ${user.user_id == researcher.usuario_uid ? 'selected' : ''}>
                                    ${escapeHtml(user.first_name + ' ' + user.last_name)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="space-y-1">
                        <select name="investigadores[${index}][rol]" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                            <option value="principal" ${researcher.rol === 'principal' ? 'selected' : ''}>Principal</option>
                            <option value="colaborador" ${researcher.rol === 'colaborador' ? 'selected' : ''}>Colaborador</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-danger)] transition-colors duration-200 mb-1">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            
            researchersContainer.appendChild(researcherField);
        });
    } else {
        addResearcher('edit');
    }
    
    lucide.createIcons();
    showModal('project-edit-modal');
}

function addResearcher(prefix = '') {
    const container = document.getElementById(`${prefix}-researchersContainer`);
    const index = container.querySelectorAll('.participant-field[data-type="researcher"]').length;
    
    const researcherField = document.createElement('div');
    researcherField.className = 'participant-field flex space-x-3 mb-3 items-end';
    researcherField.dataset.type = 'researcher';
    
    researcherField.innerHTML = `
        <div class="flex-1 grid md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="">Seleccionar investigador</option>
                    ${window.allResearchers.map(user => `
                        <option value="${user.user_id}">
                            ${escapeHtml(user.first_name + ' ' + user.last_name)}
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="space-y-1">
                <select name="investigadores[${index}][rol]" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="principal">Principal</option>
                    <option value="colaborador">Colaborador</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-danger)] transition-colors duration-200 mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(researcherField);
    lucide.createIcons();
}

function removeParticipant(button, type) {
    const container = button.closest('.participant-field[data-type="' + type + '"]');
    if (container) {
        container.remove();
    }
}

function deleteDocument(documentPath, projectId, documentName) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el documento "${documentName}"?`)) {
        return;
    }

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=deleteDocument&id=${projectId}&document_path=${encodeURIComponent(documentPath)}`
    })
    .then(response => {
        if (response.ok) {
            alert('Documento eliminado correctamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error('Error al eliminar el documento');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el documento');
    });
}

function updateDocument(documentPath, projectId, documentName) {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx';
    fileInput.style.display = 'none';
    
    fileInput.addEventListener('change', function(e) {
        if (!this.files.length) return;
        
        const file = this.files[0];
        const maxSize = 20 * 1024 * 1024;
        
        if (file.size > maxSize) {
            alert('El archivo excede el tamaño máximo de 20MB');
            return;
        }
        
        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            alert('Tipo de archivo no permitido');
            return;
        }
        
        if (!confirm(`¿Estás seguro de reemplazar "${documentName}" por "${file.name}"?`)) return;
        
        const formData = new FormData();
        formData.append('action', 'updateDocument');
        formData.append('id', projectId);
        formData.append('document_path', documentPath);
        formData.append('document_name', documentName);
        formData.append('documento', file);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                alert('Documento actualizado correctamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al actualizar el documento');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el documento');
        });
    });
    
    document.body.appendChild(fileInput);
    fileInput.click();
    document.body.removeChild(fileInput);
}

function addDocument(projectId) {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx';
    fileInput.style.display = 'none';
    
    fileInput.addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const file = this.files[0];
        const maxSize = 20 * 1024 * 1024;
        
        if (file.size > maxSize) {
            alert('El archivo excede el tamaño máximo de 20MB');
            return;
        }
        
        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            alert('Tipo de archivo no permitido');
            return;
        }
        
        if (!confirm(`¿Estás seguro de que deseas agregar el documento "${file.name}"?`)) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'addDocument');
        formData.append('id', projectId);
        formData.append('documento', file);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                alert('Documento agregado correctamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al agregar el documento');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar el documento');
        });
    });
    
    document.body.appendChild(fileInput);
    fileInput.click();
    document.body.removeChild(fileInput);
}

// Inicialización
function setupModalCloseEvents() {
    const modals = document.querySelectorAll('[id$="-modal"]');
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal(modal.id);
            }
        });
    });
}

function initializeApp() {
    window.allProjects = <?= $projectsJson ?>;
    window.isTeacher = <?= $isTeacher ? 'true' : 'false' ?>;
    window.currentPreviewDocument = null;
    window.allResearchers = <?= json_encode($allResearchers) ?>;
    window.allTeachers = <?= json_encode($allTeachers) ?>;
    window.allReviewers = <?= json_encode($allReviewers) ?>;
    window.researchLines = <?= $researchLinesJson ?>;
    
    // Configurar el formulario de edición
    document.getElementById('projectEditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'update');
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proyecto actualizado correctamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.error || 'Error al actualizar el proyecto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error al actualizar el proyecto');
        });
    });
}

// Cargar Lucide Icons
const lucideScript = document.createElement('script');
lucideScript.src = 'https://unpkg.com/lucide@latest/dist/umd/lucide.js';
lucideScript.onload = function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    initializeApp();
    setupModalCloseEvents();
};
document.head.appendChild(lucideScript);

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    setupModalCloseEvents();
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

.animate-slideUp {
    animation: slideUp 0.5s ease-out;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>