<?php
if(!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
if(!accessManager()->canAccess($_SESSION['userid'], ['estudiante', 'docente'], [['module' => 'usercp', 'action' => 'projects']])) {
    die('No tienes permisos para acceder a este módulo.');
}
include(__PATH_MODULES__.'/header.php');

try {
    $projectManager = new ProjectManager($pdo);
    $researchLineManager = new ResearchLineManager($pdo);
    $roleManager = new RoleManager($pdo);
    $uploadManager = new UploadManager();
    $evaluationManager = new EvaluationManager($pdo);
    
    $userId = $_SESSION['userid'];
    $handler = new Handler();
    $isTeacher = $roleManager->userHasRole($userId, 'Docente');

    $filterCategory = $researchLineManager->getAllActivas();
    $lineasMap = [];
    foreach ($filterCategory as $linea) {
        $lineasMap[$linea['id']] = $linea['nombre'];
    }

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
                'fase' => $project['fase'],
                'version' => $project['version'],
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
                        http_response_code(200);
                        exit;
                    } else {
                        throw new Exception("Error al actualizar el proyecto");
                    }
                    
                case 'deleteDocument':
                    $documentPath = $_POST['document_path'] ?? '';
                    if (empty($documentPath)) {
                        throw new Exception("Ruta de documento no válida");
                    }
                    
                    $result = $uploadManager->delete($documentPath);
                    if ($result) {
                        http_response_code(200);
                        exit;
                    } else {
                        throw new Exception("Error al eliminar el documento");
                    }

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
                        http_response_code(200);
                        exit;
                    } else {
                        throw new Exception("Error al actualizar el documento en la base de datos");
                    }

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
                            http_response_code(200);
                            exit;
                        } else {
                            throw new Exception("Error al actualizar el documento en la base de datos");
                        }
                    } else {
                        throw new Exception("No se proporcionó ningún documento");
                    }
            }
            
        } catch (Exception $e) {
            http_response_code(400);
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
            'puntuacion' => $calificacionProyecto,
            'estado' => $project['estado'] ?? 'nuevo',
            'ratings_summary' => $ratings,
            'visibilidad' => $project['visibilidad'] ?? 'privado',
            'activo' => $project['activo'] ?? 1,
            'fecha_creacion' => $project['creado_en'] ?? '',
            'fecha_actualizacion' => $project['actualizado_en'] ?? ''
        ];
    }

    $projectsJson = json_encode($processedProjects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $lineasJson = json_encode($lineasMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $researchLinesJson = json_encode($filterCategory, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $allResearchers = $roleManager->getAllStudents() ?? [];
    $allTeachers = $roleManager->getAllTeachers() ?? [];
    $allReviewers = $roleManager->getAllEvaluators() ?? [];

} catch (Exception $ex) {
    $_SESSION['error_message'] = 'Error al cargar los proyectos';
    header("Location: " . evalcp_base() . "?module=projects");
    die();
}
?>

<div class="max-w-[1400px] mx-auto mb-12">
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-accent)] text-[var(--color-navbar-text)] border border-[var(--color-accent)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Proyectos</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Resultados</span>
            </a>
            
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

    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-5 h-5 text-[var(--color-primary)]"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[var(--color-heading)]">Mis Proyectos</h2>
                    <p class="text-[var(--color-text-muted)] text-sm">Gestiona tus proyectos de investigación</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative flex-1 sm:flex-none">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Buscar proyectos..." 
                           class="w-full sm:w-64 px-4 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]">
                    <i data-lucide="search" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]"></i>
                </div>
                
                <select id="categoryFilter" class="px-4 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]">
                    <option value="all">Todas las líneas</option>
                    <?php foreach ($filterCategory as $linea): ?>
                    <option value="<?= $linea['id'] ?>"><?= htmlspecialchars($linea['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div id="loadingIndicator" class="text-center py-12 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] mb-6">
        <i data-lucide="loader-2" class="w-12 h-12 mx-auto text-[var(--color-primary)] animate-spin mb-4"></i>
        <p class="text-[var(--color-text-muted)] font-medium">Cargando proyectos...</p>
    </div>

    <div id="projectsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden"></div>
    
    <div id="noResults" class="text-center py-12 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hidden">
        <i data-lucide="search-x" class="w-12 h-12 mx-auto text-[var(--color-text-muted)] mb-4"></i>
        <h3 class="text-lg font-semibold text-[var(--color-heading)] mb-2">No se encontraron proyectos</h3>
        <p class="text-[var(--color-text-muted)]">Intenta ajustar los filtros de búsqueda</p>
    </div>

    <div id="pagination" class="flex justify-center items-center mt-8 space-x-2 hidden"></div>
</div>

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
    <div class="bg-[var(--color-surface)] rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto border border-[var(--color-border)]">
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
        
        <div class="p-6">
            <form id="projectEditForm" method="POST" class="space-y-8">            
                <input type="hidden" name="id" id="edit-project-id">
                <input type="hidden" name="action" value="update">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-primary)]/10 rounded-full flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Información Básica</h3>
                    </div>
                    
                    <div class="grid gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-[var(--color-text)]">Título del Proyecto *</label>
                            <input type="text" name="titulo" id="edit-titulo" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Ingrese el título del proyecto" required>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-[var(--color-text)]">Línea de Investigación *</label>
                            <select name="linea_investigacion_id" id="edit-linea-investigacion" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                                <option value="">Seleccionar línea</option>
                                <?php foreach ($filterCategory as $line): ?>
                                    <option value="<?= $line['id'] ?>"><?= htmlspecialchars($line['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-secondary)]/10 rounded-full flex items-center justify-center">
                            <i data-lucide="align-left" class="w-4 h-4 text-[var(--color-secondary)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Información Adicional</h3>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-[var(--color-text)]">Descripción *</label>
                        <textarea rows="4" name="descripcion" id="edit-descripcion" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Descripción detallada del proyecto..." required></textarea>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-[var(--color-text)]">Palabras Clave</label>
                        <input type="text" name="palabras_clave" id="edit-palabras-clave" class="w-full px-4 py-2.5 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" placeholder="Separadas por comas: IA, Machine Learning, Educación">
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-success)]/10 rounded-full flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4 text-[var(--color-success)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Investigadores</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-[var(--color-text)]">Investigadores *</h4>
                            <button type="button" onclick="addResearcher('edit')" class="flex items-center gap-1 text-xs text-[var(--color-primary)] hover:text-[var(--color-primary)]/80 transition-colors duration-200">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir investigador
                            </button>
                        </div>
                        
                        <div id="edit-researchersContainer" class="space-y-3">
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-warning)]/10 rounded-full flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-[var(--color-warning)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Docentes</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-[var(--color-text)]">Docentes *</h4>
                            <button type="button" onclick="addTeacher('edit')" class="flex items-center gap-1 text-xs text-[var(--color-primary)] hover:text-[var(--color-primary)]/80 transition-colors duration-200">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir docente
                            </button>
                        </div>
                        
                        <div id="edit-teachersContainer" class="space-y-3">
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-[var(--color-border)] pb-2">
                        <div class="w-8 h-8 bg-[var(--color-danger)]/10 rounded-full flex items-center justify-center">
                            <i data-lucide="clipboard-check" class="w-4 h-4 text-[var(--color-danger)]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--color-heading)]">Evaluadores</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-[var(--color-text)]">Evaluadores</h4>
                            <button type="button" onclick="addReviewer('edit')" class="flex items-center gap-1 text-xs text-[var(--color-primary)] hover:text-[var(--color-primary)]/80 transition-colors duration-200">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir evaluador
                            </button>
                        </div>
                        
                        <div id="edit-reviewersContainer" class="space-y-3">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-[var(--color-border)]">
                    <button type="button" onclick="closeModal('project-edit-modal')" class="order-2 sm:order-1 px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Cancelar
                    </button>
                    
                    <button type="submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:from-[var(--color-primary)]/90 hover:to-[var(--color-secondary)]/90 transition-all duration-200 font-medium flex items-center justify-center gap-2 shadow-lg">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="notification-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<script>
const allProjects = <?= $projectsJson ?>;
const lineasMap = <?= $lineasJson ?>;
const allResearchers = <?= json_encode($allResearchers) ?>;
const allTeachers = <?= json_encode($allTeachers) ?>;
const allReviewers = <?= json_encode($allReviewers) ?>;
let currentPage = 1;
const projectsPerPage = 6;
let currentEditingProject = null;

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
        pdf: { icon: 'file-text', color: 'bg-red-500' },
        doc: { icon: 'file-text', color: 'bg-blue-500' },
        docx: { icon: 'file-text', color: 'bg-blue-500' },
        xls: { icon: 'file-spreadsheet', color: 'bg-green-500' },
        xlsx: { icon: 'file-spreadsheet', color: 'bg-green-500' },
        ppt: { icon: 'file-presentation', color: 'bg-orange-500' },
        pptx: { icon: 'file-presentation', color: 'bg-orange-500' },
        default: { icon: 'file', color: 'bg-purple-500' }
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
    return status === 'activo' ? 'text-green-600' : 'text-red-600';
}

function getUserStatusIcon(user) {
    const status = getUserStatus(user);
    return status === 'activo' ? 'check-circle' : 'x-circle';
}

function getUserInitials(user) {
    const fullName = getUserFullName(user);
    return fullName.charAt(0) || 'U';
}

function showNotification(message, type = 'success') {
    const notification = document.createElement("div");
    notification.className = `px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 animate-fade-in ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-yellow-500 text-white'
    }`;
    
    notification.innerHTML = `
        <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-4 h-4"></i>
        <span>${message}</span>
    `;
    
    document.getElementById('notification-container').appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => {
        notification.classList.remove("animate-fade-in");
        notification.classList.add("animate-fade-out");
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
}

function renderProjects() {
    const grid = document.getElementById('projectsGrid');
    const pagination = document.getElementById('pagination');
    const noResults = document.getElementById('noResults');
    const loading = document.getElementById('loadingIndicator');
    
    const filteredProjects = getFilteredProjects();
    const totalPages = Math.ceil(filteredProjects.length / projectsPerPage);
    
    loading.classList.add('hidden');
    
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
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] hover:shadow-lg transition-all duration-300 project-card" data-id="${project.id}">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center shadow">
                            <span class="text-white font-bold text-sm">${project.titulo?.charAt(0) || 'P'}</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 ${project.activo ? 'bg-green-500' : 'bg-gray-400'} rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[var(--color-text)] text-lg">${escapeHtml(project.titulo)}</h3>
                        <p class="text-sm text-[var(--color-text-muted)] truncate">v${escapeHtml(project.version || '1.0')}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <button onclick="showProjectDetails(${project.id})" class="p-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200" title="Ver detalles">
                        <i data-lucide="eye" class="w-4 h-4 text-[var(--color-primary)]"></i>
                    </button>
                    
                    ${window.isTeacher ? `
                    <button onclick="editProject(${project.id})" class="p-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200" title="Editar proyecto">
                        <i data-lucide="edit" class="w-4 h-4 text-green-500"></i>
                    </button>
                    ` : ''}
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-md text-xs font-medium">
                        ${escapeHtml(project.fase ? project.fase.charAt(0).toUpperCase() + project.fase.slice(1) : 'Propuesta')}
                    </span>
                    <span class="px-3 py-1 ${project.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-md text-xs font-medium">
                        ${project.activo ? 'Activo' : 'Inactivo'}
                    </span>
                    <span class="px-3 py-1 bg-[var(--color-secondary)]/10 text-[var(--color-secondary)] rounded-md text-xs font-medium">
                        ${escapeHtml(project.linea_nombre)}
                    </span>
                    ${project.timer_segundos > 0 ? `
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium">
                        ${Math.floor(project.timer_segundos / 60)} min
                    </span>
                    ` : ''}
                    ${project.puntuacion ? `
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-md text-xs font-medium">
                        ${project.puntuacion} pts
                    </span>
                    ` : ''}
                </div>
                
                ${project.descripcion ? `
                <p class="text-sm text-[var(--color-text-muted)] line-clamp-2">${escapeHtml(project.descripcion)}</p>
                ` : ''}
                
                ${project.fecha_presentacion ? `
                <p class="text-sm text-[var(--color-text-muted)]">Presentación: ${formatDate(project.fecha_presentacion)}</p>
                ` : ''}
                
                ${project.investigadores?.length > 0 ? `
                <p class="text-sm text-[var(--color-text-muted)]">${project.investigadores.length} estudiante(s)</p>
                ` : ''}
                
                ${project.documentos?.length > 0 ? `
                <div class="flex flex-wrap gap-2 mt-3">
                    ${project.documentos.slice(0, 2).map(doc => `
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-md text-xs font-medium flex items-center space-x-1">
                            <i data-lucide="file-text" class="w-3 h-3"></i>
                            <span>${escapeHtml(doc.name)}</span>
                        </span>
                    `).join('')}
                    ${project.documentos.length > 2 ? `
                    <span class="px-3 py-1 bg-[var(--color-border)] text-[var(--color-text-muted)] rounded-md text-xs font-medium">
                        +${project.documentos.length - 2} más
                    </span>
                    ` : ''}
                </div>
                ` : ''}
            </div>

            <div class="flex space-x-3">
                <button onclick="showProjectDetails(${project.id})" class="flex-1 flex items-center justify-center space-x-1 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 text-sm">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>Ver Detalles</span>
                </button>
                ${window.isTeacher ? `
                <button onclick="editProject(${project.id})" class="flex-1 flex items-center justify-center space-x-1 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 text-sm">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    <span>Editar</span>
                </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    renderPagination(totalPages);
    lucide.createIcons();
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
            <button onclick="changePage(${currentPage - 1})" class="px-3 py-1 border border-[var(--color-border)] rounded-lg text-sm hover:bg-[var(--color-dropdown-hover)] transition-all duration-200">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
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
            <button onclick="changePage(${i})" class="px-3 py-1 border ${i === currentPage ? 'bg-[var(--color-primary)] text-white border-[var(--color-primary)]' : 'border-[var(--color-border)] hover:bg-[var(--color-dropdown-hover)]'} rounded-lg text-sm transition-all duration-200">
                ${i}
            </button>
        `;
    }
    
    if (currentPage < totalPages) {
        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})" class="px-3 py-1 border border-[var(--color-border)] rounded-lg text-sm hover:bg-[var(--color-dropdown-hover)] transition-all duration-200">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        `;
    }
    
    pagination.innerHTML = paginationHTML;
    lucide.createIcons();
}

function getFilteredProjects() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;

    return allProjects.filter(project => {
        const matchesSearch = project.titulo.toLowerCase().includes(searchTerm) ||
                            (project.palabras_clave && project.palabras_clave.toLowerCase().includes(searchTerm)) ||
                            project.linea_nombre.toLowerCase().includes(searchTerm) ||
                            (project.descripcion && project.descripcion.toLowerCase().includes(searchTerm));
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
    if (!project) {
        showNotification('Proyecto no encontrado', 'error');
        return;
    }
    
    const modalContent = document.getElementById('project-details-content');
    const isTeacher = window.isTeacher;
    
    modalContent.innerHTML = `
        <div class="flex items-center space-x-6 p-6 bg-[var(--color-surface-alt)] rounded-xl">
            <div class="relative">
                <div class="w-20 h-20 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white text-2xl font-bold">${project.titulo?.charAt(0) || 'P'}</span>
                </div>
                <div class="absolute -bottom-2 -right-2 w-6 h-6 ${project.activo ? 'bg-green-500' : 'bg-gray-400'} rounded-full border-4 border-white"></div>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold text-[var(--color-heading)]">${escapeHtml(project.titulo)}</h3>
                <p class="text-[var(--color-text-muted)] mb-2">Versión ${project.version || '1'}</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-full text-sm font-semibold">${capitalizeFirstLetter(project.fase)}</span>
                    <span class="px-3 py-1 ${project.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-full text-sm font-semibold">${project.activo ? 'Activo' : 'Inactivo'}</span>
                    ${project.calificado ? `
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">Calificado</span>
                    ` : ''}
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-[var(--color-heading)]">Información Básica</h4>
                <div class="space-y-3">
                    ${isTeacher ? `
                    <div class="flex items-center space-x-3">
                        <i data-lucide="calendar" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                        <span class="text-sm text-[var(--color-text-muted)]">Creado: ${formatDate(project.fecha_creacion)}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                        <span class="text-sm text-[var(--color-text-muted)]">Actualizado: ${formatDate(project.fecha_actualizacion)}</span>
                    </div>
                    ` : ''}
                    ${project.linea_nombre ? `
                    <div class="flex items-center space-x-3">
                        <i data-lucide="git-branch" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                        <span class="text-sm text-[var(--color-text-muted)]">${escapeHtml(project.linea_nombre)}</span>
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
                            <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white font-medium text-sm">
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
                        class="flex items-center space-x-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    <span>Agregar Documento</span>
                </button>
            </div>
            ` : ''}
            ${project.documentos?.length > 0 ? `
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
                ${project.documentos.map(doc => `
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
                                    class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-green-600 hover:bg-green-50 transition-colors duration-200">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                <span>Actualizar</span>
                            </button>
                            <button onclick="deleteDocument('${escapeSingleQuote(doc.full_path)}', ${project.id}, '${escapeSingleQuote(doc.name)}')" 
                                    class="flex-1 py-2 flex items-center justify-center space-x-1 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
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
        lucide.createIcons();
    }, 100);
    
    showModal('project-details-modal');
}

function editProject(projectId) {
    const project = allProjects.find(p => p.id == projectId);
    if (!project) {
        showNotification('Proyecto no encontrado', 'error');
        return;
    }
    
    currentEditingProject = project;
    
    document.getElementById('edit-project-id').value = project.id;
    document.getElementById('edit-titulo').value = project.titulo || '';
    document.getElementById('edit-linea-investigacion').value = project.linea_investigacion_id || '';
    document.getElementById('edit-descripcion').value = project.descripcion || '';
    document.getElementById('edit-palabras-clave').value = project.palabras_clave || '';
    
    const researchersContainer = document.getElementById('edit-researchersContainer');
    researchersContainer.innerHTML = '';
    
    if (project.investigadores?.length > 0) {
        project.investigadores.forEach((researcher, index) => {
            addResearcherField('edit', index, researcher);
        });
    } else {
        addResearcher('edit');
    }
    
    const teachersContainer = document.getElementById('edit-teachersContainer');
    teachersContainer.innerHTML = '';
    
    if (project.docentes?.length > 0) {
        project.docentes.forEach((teacher, index) => {
            addTeacherField('edit', index, teacher);
        });
    } else {
        addTeacher('edit');
    }
    
    const reviewersContainer = document.getElementById('edit-reviewersContainer');
    reviewersContainer.innerHTML = '';
    
    if (project.evaluadores?.length > 0) {
        project.evaluadores.forEach((reviewer, index) => {
            addReviewerField('edit', index, reviewer);
        });
    }
    
    showModal('project-edit-modal');
}

function addResearcher(prefix = '') {
    const container = document.getElementById(`${prefix}-researchersContainer`);
    const index = container.querySelectorAll('.participant-field[data-type="researcher"]').length;
    addResearcherField(prefix, index);
}

function addResearcherField(prefix, index, researcher = null) {
    const container = document.getElementById(`${prefix}-researchersContainer`);
    
    const researcherField = document.createElement('div');
    researcherField.className = 'participant-field flex gap-3 items-end bg-[var(--color-surface-alt)] p-3 rounded-lg border border-[var(--color-border)]';
    researcherField.dataset.type = 'researcher';
    
    researcherField.innerHTML = `
        <div class="flex-1 grid md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-3 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="">Seleccionar investigador</option>
                    ${allResearchers.map(user => `
                        <option value="${user.user_id}" ${researcher && user.user_id == researcher.usuario_uid ? 'selected' : ''}>
                            ${escapeHtml(user.first_name + ' ' + user.last_name)} (${user.username})
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="space-y-1">
                <select name="investigadores[${index}][rol]" class="w-full px-3 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="principal" ${researcher && researcher.rol === 'principal' ? 'selected' : ''}>Principal</option>
                    <option value="colaborador" ${researcher && researcher.rol === 'colaborador' ? 'selected' : ''}>Colaborador</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-[var(--color-text-muted)] hover:text-red-500 transition-colors duration-200 mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(researcherField);
    lucide.createIcons();
}

function addTeacher(prefix = '') {
    const container = document.getElementById(`${prefix}-teachersContainer`);
    const index = container.querySelectorAll('.participant-field[data-type="teacher"]').length;
    addTeacherField(prefix, index);
}

function addTeacherField(prefix, index, teacher = null) {
    const container = document.getElementById(`${prefix}-teachersContainer`);
    
    const teacherField = document.createElement('div');
    teacherField.className = 'participant-field flex gap-3 items-end bg-[var(--color-surface-alt)] p-3 rounded-lg border border-[var(--color-border)]';
    teacherField.dataset.type = 'teacher';
    
    teacherField.innerHTML = `
        <div class="flex-1 grid md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <select name="docentes[${index}][usuario_uid]" class="teacher-select w-full px-3 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="">Seleccionar docente</option>
                    ${allTeachers.map(user => `
                        <option value="${user.user_id}" ${teacher && user.user_id == teacher.usuario_uid ? 'selected' : ''}>
                            ${escapeHtml(user.first_name + ' ' + user.last_name)} (${user.username})
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="space-y-1">
                <select name="docentes[${index}][rol]" class="w-full px-3 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]" required>
                    <option value="asesor" ${teacher && teacher.rol === 'asesor' ? 'selected' : ''}>Asesor</option>    
                    <option value="director" ${teacher && teacher.rol === 'director' ? 'selected' : ''}>Director</option>
                    <option value="jurado" ${teacher && teacher.rol === 'jurado' ? 'selected' : ''}>Jurado</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="removeParticipant(this, 'teacher')" class="p-2 text-[var(--color-text-muted)] hover:text-red-500 transition-colors duration-200 mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(teacherField);
    lucide.createIcons();
}

function addReviewer(prefix = '') {
    const container = document.getElementById(`${prefix}-reviewersContainer`);
    const index = container.querySelectorAll('.participant-field[data-type="reviewer"]').length;
    addReviewerField(prefix, index);
}

function addReviewerField(prefix, index, reviewer = null) {
    const container = document.getElementById(`${prefix}-reviewersContainer`);
    
    const reviewerField = document.createElement('div');
    reviewerField.className = 'participant-field flex gap-3 items-end bg-[var(--color-surface-alt)] p-3 rounded-lg border border-[var(--color-border)]';
    reviewerField.dataset.type = 'reviewer';
    
    reviewerField.innerHTML = `
        <div class="flex-1 space-y-1">
            <select name="revisores[]" class="reviewer-select w-full px-3 py-2 bg-[var(--color-input-bg)] border border-[var(--color-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 text-[var(--color-input-text)]">
                <option value="">Seleccionar evaluador</option>
                ${allReviewers.map(user => `
                    <option value="${user.user_id}" ${reviewer && user.user_id == reviewer.usuario_uid ? 'selected' : ''}>
                        ${escapeHtml(user.first_name + ' ' + user.last_name)} (${user.username})
                    </option>
                `).join('')}
            </select>
        </div>
        <button type="button" onclick="removeParticipant(this, 'reviewer')" class="p-2 text-[var(--color-text-muted)] hover:text-red-500 transition-colors duration-200 mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(reviewerField);
    lucide.createIcons();
}

function removeParticipant(button, type) {
    const container = button.closest('.participant-field[data-type="' + type + '"]');
    if (container) {
        container.remove();
        reindexParticipantFields(type);
    }
}

function reindexParticipantFields(type) {
    const container = document.getElementById(`edit-${type}sContainer`);
    const fields = container.querySelectorAll('.participant-field[data-type="' + type + '"]');
    
    fields.forEach((field, index) => {
        const selects = field.querySelectorAll('select');
        selects.forEach(select => {
            const name = select.getAttribute('name');
            if (name.includes('[')) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                select.setAttribute('name', newName);
            }
        });
    });
}

function saveProjectChanges() {
    const form = document.getElementById('projectEditForm');
    const formData = new FormData(form);
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Guardando...';
    submitBtn.disabled = true;
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al guardar los cambios', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        lucide.createIcons();
    });
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
            showNotification('El archivo excede el tamaño máximo de 20MB', 'error');
            return;
        }
        
        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            showNotification('Tipo de archivo no permitido', 'error');
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
            if (!response.ok) {
                throw new Error('Error al agregar documento');
            }
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al agregar documento', 'error');
        });
    });
    
    document.body.appendChild(fileInput);
    fileInput.click();
    document.body.removeChild(fileInput);
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
            showNotification('El archivo excede el tamaño máximo de 20MB', 'error');
            return;
        }
        
        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            showNotification('Tipo de archivo no permitido', 'error');
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
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al actualizar documento');
            }
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al actualizar documento', 'error');
        });
    });
    
    document.body.appendChild(fileInput);
    fileInput.click();
    document.body.removeChild(fileInput);
}

function deleteDocument(documentPath, projectId, documentName) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el documento "${documentName}"?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'deleteDocument');
    formData.append('id', projectId);
    formData.append('document_path', documentPath);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al eliminar documento');
        }
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar documento', 'error');
    });
}

function previewDocument(fileUrl, fileName) {
    window.open(fileUrl, '_blank');
}

function initializeApp() {
    window.allProjects = allProjects;
    window.isTeacher = <?= $isTeacher ? 'true' : 'false' ?>;
    
    document.getElementById('searchInput').addEventListener('input', function() {
        currentPage = 1;
        renderProjects();
    });
    
    document.getElementById('categoryFilter').addEventListener('change', function() {
        currentPage = 1;
        renderProjects();
    });
    
    const editForm = document.getElementById('projectEditForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProjectChanges();
        });
    }
    
    setupModalCloseEvents();
    
    setTimeout(() => {
        renderProjects();
    }, 500);
}

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

const lucideScript = document.createElement('script');
lucideScript.src = 'https://unpkg.com/lucide@latest/dist/umd/lucide.js';
lucideScript.onload = function() {
    lucide.createIcons();
    initializeApp();
};
document.head.appendChild(lucideScript);
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

.animate-fade-out {
    animation: fadeOut 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(10px);
    }
}

.participant-field {
    transition: all 0.3s ease;
}

.participant-field:hover {
    border-color: var(--color-primary);
}
</style>