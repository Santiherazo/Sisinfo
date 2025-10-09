<?php
if (!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpproject', 'action' => 'manage']])) {
    die('No tienes permisos para acceder a este módulo.');
}

$researchLines = new ResearchLineManager($pdo);
$roleManager = new RoleManager($pdo);
$uploadManager = new uploadManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $projectId = (int)($_POST['id'] ?? 0);
        
        if (!$projectId) {
            throw new Exception("ID de proyecto no válido");
        }

        $project = $projectmanager->getProject($projectId);
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
            'timer_segundos' => isset($_POST['duracion_evaluacion']) && $_POST['duracion_evaluacion'] !== '' 
                ? intval($_POST['duracion_evaluacion']) * 60 
                : $project['timer_segundos'],
            'docentes' => [],
            'evaluadores' => [],
            'investigadores' => [],
            'descripcion' => $_POST['descripcion'] ?? $project['descripcion'],
            'palabras_clave' => $_POST['palabras_clave'] ?? $project['palabras_clave'],
            'documento' => $documentPath,
            'directorio' => $projectDirName,
            'visibilidad' => $_POST['visibilidad'] ?? $project['visibilidad'] ?? 'privado',
            'hora_programada' => $_POST['hora_programada'] ?? $project['hora_programada'],
            'fecha_presentacion' => $_POST['fecha_presentacion'] ?? $project['fecha_presentacion']
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
                $result = $projectmanager->updateProject($projectId, $formData);
                
                if ($result) {
                    echo json_encode(['success' => true, 'projectId' => $projectId]);
                } else {
                    throw new Exception("Error al actualizar el proyecto");
                }
                exit;
                
            case 'delete':
                $result = $projectmanager->deleteProject($projectId);
                if ($result) {
                    $project = $projectmanager->getProject($projectId);
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
                
            case 'activar':
                $result = $projectmanager->activateProject($projectId);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al activar el proyecto");
                }
                exit;
                
            case 'desactivar':
                $result = $projectmanager->deactivateProject($projectId);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al desactivar el proyecto");
                }
                exit;
                
            case 'publicar':
                $result = $projectmanager->makeProjectPublic($projectId);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al hacer público el proyecto");
                }
                exit;
                
            case 'privado':
                $result = $projectmanager->makeProjectPrivate($projectId);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error al hacer privado el proyecto");
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

                $project = $projectmanager->getProject($projectId);
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

                $currentProjectData = $projectmanager->getProject($projectId);
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

                $result = $projectmanager->updateProject($projectId, $updateData);

                if ($result) {
                    echo json_encode(['success' => true, 'documentPath' => $newDocumentPath]);
                } else {
                    throw new Exception("Error al actualizar el documento en la base de datos");
                }
                exit;

            case 'addDocument':
                $project = $projectmanager->getProject($projectId);
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
                    
                    $currentProjectData = $projectmanager->getProject($projectId);
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
                    
                    $result = $projectmanager->updateProject($projectId, $updateData);
                    
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

$allProjects = $projectmanager->getAllProjects();
$processedProjects = [];

foreach ($allProjects as $project) {
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
    
    $processedProjects[] = [
        'id' => $project['id'],
        'titulo' => $project['titulo'],
        'linea_investigacion_id' => $project['linea_investigacion_id'],
        'fase' => $project['fase'],
        'version' => $project['version'],
        'timer_segundos' => $project['timer_segundos'] ?? 0,
        'docentes' => $docentes,
        'evaluadores' => $evaluadores,
        'investigadores' => $investigadores,
        'descripcion' => $project['descripcion'] ?? null,
        'palabras_clave' => $project['palabras_clave'] ?? null,
        'visibilidad' => $project['visibilidad'] ?? 'privado',
        'hora_programada' => $project['hora_programada'] ?? null,
        'fecha_presentacion' => $project['fecha_presentacion'] ?? null,
        'documents' => $documents,
        'creado_en' => $project['creado_en'] ?? null,
        'actualizado_en' => $project['actualizado_en'] ?? null,
        'activo' => $project['activo'] ?? 1,
        'estado' => $project['estado'] ?? 'nuevo',
        'calificado' => $project['calificado'] ?? 0,
        'puntuacion' => $project['puntuacion'] ?? null
    ];
}

$projectsJson = json_encode($processedProjects);
$researchLinesJson = json_encode($researchLines->getAllActivas());
$allResearchers = $roleManager->getAllStudents() ?? [];
$allTeachers = $roleManager->getAllTeachers() ?? [];
$allReviewers = $roleManager->getAllEvaluators() ?? [];
?>

<div id="projects-module" class="module-content fade-in">
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Gestión de Proyectos</h2>
                <p class="text-sm text-slate-600">Administra los proyectos del sistema</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="<?php echo admincp_base(); ?>?module=addProject" class="flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg hover:from-blue-700 hover:to-blue-600 transition-all shadow-md whitespace-nowrap text-sm font-medium">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Nuevo Proyecto</span>
                </a>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <input type="text" id="project-search" placeholder="Buscar proyectos..." class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all pl-10">
                    <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
                </div>
                
                <div class="relative w-full md:w-48">
                    <select id="phase-filter" class="w-full px-4 py-2.5 pr-8 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all appearance-none">
                        <option value="">Todas las fases</option>
                        <option value="propuesta">Propuesta</option>
                        <option value="desarrollo">Desarrollo</option>
                        <option value="evaluacion">Evaluación</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                </div>

                <div class="relative w-full md:w-48">
                    <select id="visibility-filter" class="w-full px-4 py-2.5 pr-8 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all appearance-none">
                        <option value="">Todas las visibilidades</option>
                        <option value="publico">Públicos</option>
                        <option value="privado">Privados</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                </div>

                <div class="relative w-full md:w-48">
                    <select id="status-filter" class="w-full px-4 py-2.5 pr-8 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all appearance-none">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4" id="projects-grid">
        </div>

        <div class="flex justify-between items-center mt-6">
            <p class="text-sm text-slate-600">Mostrando <span id="projects-count">0</span> proyectos</p>
        </div>
    </div>
</div>

<div id="project-details-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="modal-content w-full max-w-3xl bg-white rounded-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Detalles del Proyecto</h2>
        </div>
        <div class="space-y-6" id="project-details-content"></div>
    </div>
</div>

<div id="project-edit-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="glassmorphism rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl mx-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Editar Proyecto</h2>
                    <p class="text-sm text-blue-100">Complete todos los campos obligatorios (*)</p>
                </div>
                <button onclick="closeModal('project-edit-modal')" class="p-2 text-white/80 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <form id="projectEditForm" method="POST" class="space-y-8">            
                <input type="hidden" name="id" id="edit-project-id">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Información Básica</h3>
                    </div>
                    
                    <div class="grid gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Título del Proyecto *</label>
                            <input type="text" name="titulo" id="edit-titulo" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ingrese el título del proyecto" required>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700">Línea de Investigación *</label>
                                <select name="linea_investigacion_id" id="edit-linea-investigacion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                    <option value="">Seleccionar línea</option>
                                    <?php foreach ($researchLines->getAllActivas() as $line): ?>
                                        <option value="<?= $line['id'] ?>"><?= htmlspecialchars($line['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700">Visibilidad *</label>
                                <select name="visibilidad" id="edit-visibilidad" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                    <option value="privado">Privado</option>
                                    <option value="publico">Público</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i data-lucide="git-commit" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Versión del Proyecto</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Versión</label>
                            <input type="text" name="version" id="edit-version" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Fase *</label>
                            <select name="fase" id="edit-fase" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="propuesta">Propuesta</option>
                                <option value="desarrollo">Desarrollo</option>
                                <option value="aplicacion">Aplicación</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Fecha de Presentación</label>
                            <input type="date" name="fecha_presentacion" id="edit-fecha-presentacion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Hora de Evaluación</label>
                            <input type="time" name="hora_programada" id="edit-hora-programada" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Duración Evaluación (min) *</label>
                            <input type="number" name="duracion_evaluacion" id="edit-duracion-evaluacion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Minutos">
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Descripción *</label>
                        <textarea rows="4" name="descripcion" id="edit-descripcion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Descripción detallada de esta versión..." required></textarea>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Palabras Clave</label>
                        <input type="text" name="palabras_clave" id="edit-palabras-clave" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Separadas por comas: IA, Machine Learning, Educación" >
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Investigadores</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-slate-700">Investigadores *</h4>
                            <button type="button" onclick="addResearcher('edit')" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir investigador
                            </button>
                        </div>
                        
                        <div id="edit-researchersContainer">
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Docentes</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-slate-700">Docentes *</h4>
                            <button type="button" onclick="addTeacher('edit')" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir docente
                            </button>
                        </div>
                        
                        <div id="edit-teachersContainer">
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i data-lucide="clipboard-check" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Evaluadores</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-slate-700">Evaluadores *</h4>
                            <button type="button" onclick="addReviewer('edit')" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                                <i data-lucide="plus" class="w-3 h-3"></i> Añadir evaluador
                            </button>
                        </div>
                        
                        <div id="edit-reviewersContainer">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-slate-100">
                    <button type="button" onclick="closeModal('project-edit-modal')" class="order-2 sm:order-1 px-6 py-3 bg-white/80 border border-slate-200 text-slate-700 rounded-lg hover:bg-white transition-all font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Cancelar
                    </button>
                    
                    <button type="submit" name="webengineEdit_submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all font-medium flex items-center justify-center gap-2 shadow-lg">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="document-preview-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="modal-content w-full max-w-6xl bg-white rounded-2xl p-6 max-h-[90vh] overflow-hidden flex flex-col" style="height: 85vh; width: 90vw;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-slate-900" id="document-preview-title">Previsualización de Documento</h3>
            <button onclick="closeModal('document-preview-modal')" class="p-2 text-slate-500 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex-1 border border-slate-200 rounded-lg overflow-hidden" style="min-height: 70vh;">
            <iframe id="document-preview-iframe" class="w-full h-full" frameborder="0" style="min-height: 70vh;"></iframe>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="downloadCurrentPreview()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Descargar Documento
            </button>
        </div>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Confirmar eliminación</h3>
        </div>
        <p class="text-slate-600 mb-6">¿Estás seguro de que deseas eliminar permanentemente este proyecto? Esta acción no se puede deshacer.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('delete-confirm-modal')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all">
                Cancelar
            </button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                Eliminar Proyecto
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const allProjects = <?= $projectsJson ?>;
    const researchLines = <?= $researchLinesJson ?>;
    const allResearchers = <?= json_encode($allResearchers) ?>;
    const allTeachers = <?= json_encode($allTeachers) ?>;
    const allReviewers = <?= json_encode($allReviewers) ?>;
    
    let filteredProjects = [...allProjects];
    let currentPreviewDocument = null;
    let currentEditingProject = null;
    
    renderProjects(allProjects);
    setupEventListeners();
    
    function renderProjects(projects) {
        const grid = document.getElementById('projects-grid');
        grid.innerHTML = '';
        
        if (projects.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i data-lucide="folder-x" class="w-10 h-10 mx-auto text-slate-400"></i>
                    <p class="mt-4 text-slate-600">No se encontraron proyectos</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }
        
        projects.forEach(project => {
            const line = researchLines.find(l => l.id === project.linea_investigacion_id);
            const lineName = line ? line.nombre : '';
            
            const principalResearcher = project.investigadores?.find(i => i.rol === 'principal');
            const principalName = principalResearcher ? principalResearcher.nombre_completo : 'Sin asignar';
            
            const projectCard = document.createElement('div');
            projectCard.className = 'bg-white rounded-lg p-4 border border-slate-100 hover:shadow-md transition-all project-card';
            projectCard.dataset.id = project.id;
            projectCard.dataset.title = project.titulo.toLowerCase();
            projectCard.dataset.line = lineName.toLowerCase();
            projectCard.dataset.phase = project.fase.toLowerCase();
            projectCard.dataset.visibility = project.visibilidad;
            projectCard.dataset.active = project.activo ? '1' : '0';
            
            projectCard.innerHTML = `
                <div class="flex justify-between items-start mb-4 px-1">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow">
                                <span class="text-white font-bold text-sm">${project.titulo?.charAt(0) || 'P'}</span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 ${project.visibilidad === 'publico' ? 'bg-green-500' : 'bg-gray-400'} rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">${escapeHtml(project.titulo)}</h3>
                            <p class="text-xs text-slate-500 truncate">v${escapeHtml(project.version)}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pr-2 shrink-0">
                        ${project.activo ? `
                            <button onclick="toggleProjectStatus(${project.id}, false)" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="Desactivar proyecto">
                                <i data-lucide="power" class="w-4 h-4 text-green-500"></i>
                            </button>
                        ` : `
                            <button onclick="toggleProjectStatus(${project.id}, true)" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="Activar proyecto">
                                <i data-lucide="power-off" class="w-4 h-4 text-gray-400"></i>
                            </button>
                        `}
                        
                        <button onclick="toggleProjectVisibility(${project.id}, '${project.visibilidad === 'publico' ? 'privado' : 'publico'}')" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="${project.visibilidad === 'publico' ? 'Hacer privado' : 'Hacer público'}">
                            <i data-lucide="${project.visibilidad === 'publico' ? 'eye' : 'eye-off'}" class="w-4 h-4 ${project.visibilidad === 'publico' ? 'text-green-500' : 'text-gray-400'}"></i>
                        </button>

                        <button onclick="confirmDeleteProject(${project.id})" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="Eliminar proyecto">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs font-medium">
                            ${capitalizeFirstLetter(project.fase)}
                        </span>
                        <span class="px-2 py-1 ${project.visibilidad === 'publico' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-md text-xs font-medium">
                            ${project.visibilidad === 'publico' ? 'Público' : 'Privado'}
                        </span>
                        <span class="px-2 py-1 ${project.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-md text-xs font-medium">
                            ${project.activo ? 'Activo' : 'Inactivo'}
                        </span>
                        ${lineName ? `
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-md text-xs font-medium">
                            ${escapeHtml(lineName)}
                        </span>
                        ` : ''}
                        ${project.timer_segundos > 0 ? `
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium">
                            ${Math.floor(project.timer_segundos / 60)} min
                        </span>
                        ` : ''}
                        ${project.puntuacion ? `
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md text-xs font-medium">
                            ${project.puntuacion} pts
                        </span>
                        ` : ''}
                    </div>
                    ${project.descripcion ? `
                    <p class="text-xs text-slate-600 truncate">${escapeHtml(project.descripcion)}</p>
                    ` : ''}
                    <p class="text-xs text-slate-500">Investigador principal: ${escapeHtml(principalName)}</p>
                    ${project.fecha_presentacion ? `
                    <p class="text-xs text-slate-500">Presentación: ${formatDate(project.fecha_presentacion)}</p>
                    ` : ''}
                    
                    ${project.documents?.length > 0 ? `
                    <div class="flex flex-wrap gap-1 mt-2">
                        ${project.documents.map(doc => `
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md text-xs font-medium flex items-center gap-1">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                ${escapeHtml(doc.name)}
                            </span>
                        `).join('')}
                    </div>
                    ` : ''}
                </div>

                <div class="flex gap-2">
                    <button onclick="showProjectDetails(${project.id})" class="flex-1 flex items-center justify-center gap-1 px-2 py-1 bg-slate-50 border border-slate-100 rounded-md hover:bg-slate-100 transition-all text-xs">
                        <i data-lucide="eye" class="w-3 h-3"></i>
                        Ver
                    </button>
                    <button onclick="editProject(${project.id})" class="flex-1 flex items-center justify-center gap-1 px-2 py-1 bg-slate-50 border border-slate-100 rounded-md hover:bg-slate-100 transition-all text-xs">
                        <i data-lucide="edit" class="w-3 h-3"></i>
                        Editar
                    </button>
                </div>
            `;
            
            grid.appendChild(projectCard);
        });
        
        document.getElementById('projects-count').textContent = projects.length;
        lucide.createIcons();
    }

    function setupEventListeners() {
        document.getElementById('project-search').addEventListener('input', filterProjects);
        document.getElementById('phase-filter').addEventListener('change', filterProjects);
        document.getElementById('visibility-filter').addEventListener('change', filterProjects);
        document.getElementById('status-filter').addEventListener('change', filterProjects);
        
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('[id$="-modal"]');
                closeModal(modal.id);
            });
        });

        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });

        document.getElementById('projectEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveProjectChanges();
        });
    }

    function filterProjects() {
        const searchTerm = document.getElementById('project-search').value.toLowerCase();
        const phase = document.getElementById('phase-filter').value.toLowerCase();
        const visibility = document.getElementById('visibility-filter').value.toLowerCase();
        const status = document.getElementById('status-filter').value;
        
        filteredProjects = allProjects.filter(project => {
            const line = researchLines.find(l => l.id === project.linea_investigacion_id);
            const lineName = line ? line.nombre.toLowerCase() : '';
            
            const matchesSearch = searchTerm === '' || 
                project.titulo.toLowerCase().includes(searchTerm) || 
                lineName.includes(searchTerm);
            
            const matchesPhase = phase === '' || project.fase.toLowerCase() === phase;
            const matchesVisibility = visibility === '' || project.visibilidad.toLowerCase() === visibility;
            const matchesStatus = status === '' || project.activo.toString() === status;
            
            return matchesSearch && matchesPhase && matchesVisibility && matchesStatus;
        });
        
        renderProjects(filteredProjects);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    function capitalizeFirstLetter(text) {
        if (!text) return '';
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

    function formatDate(dateString) {
        if (!dateString) return 'Fecha desconocida';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES');
    }

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement("div");
        notification.className = `fixed bottom-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in z-50`;
        notification.innerHTML = `<i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-4 h-4"></i> ${message}`;
        document.body.appendChild(notification);
        
        lucide.createIcons();
        
        setTimeout(() => {
            notification.classList.remove("animate-fade-in");
            notification.classList.add("animate-fade-out");
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    window.showProjectDetails = function(projectId) {
        const project = allProjects.find(p => p.id == projectId);
        if (!project) {
            showNotification('Proyecto no encontrado', 'error');
            return;
        }
        
        const line = researchLines.find(l => l.id === project.linea_investigacion_id);
        const lineName = line ? line.nombre : '';
        
        const modalContent = document.getElementById('project-details-content');
        modalContent.innerHTML = `
            <div class="flex items-center gap-6 p-6 bg-white/60 rounded-xl">
                <div class="relative">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white text-2xl font-bold">${project.titulo?.charAt(0) || 'P'}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-6 h-6 ${project.visibilidad === 'publico' ? 'bg-green-500' : 'bg-gray-400'} rounded-full border-4 border-white"></div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-slate-900">${escapeHtml(project.titulo)}</h3>
                    <p class="text-slate-600 mb-2">Versión ${project.version || '1'}</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">${capitalizeFirstLetter(project.fase)}</span>
                        <span class="px-3 py-1 ${project.visibilidad === 'publico' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-full text-sm font-semibold">${project.visibilidad === 'publico' ? 'Público' : 'Privado'}</span>
                        <span class="px-3 py-1 ${project.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-full text-sm font-semibold">${project.activo ? 'Activo' : 'Inactivo'}</span>
                        ${project.calificado ? `
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">Calificado</span>
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-slate-900">Información Básica</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i data-lucide="hash" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">ID: ${project.id}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Creado: ${formatDate(project.creado_en)}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Actualizado: ${formatDate(project.actualizado_en)}</span>
                        </div>
                        ${lineName ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="git-branch" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">${escapeHtml(lineName)}</span>
                        </div>
                        ` : ''}
                        ${project.descripcion ? `
                        <div class="flex items-start gap-3">
                            <i data-lucide="file-text" class="w-4 h-4 text-slate-500 mt-0.5"></i>
                            <span class="text-sm text-slate-600">${escapeHtml(project.descripcion)}</span>
                        </div>
                        ` : ''}
                        ${project.palabras_clave ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="tags" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">${escapeHtml(project.palabras_clave)}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-slate-900">Evaluación</h4>
                    <div class="space-y-3">
                        ${project.fecha_presentacion ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Fecha presentación: ${formatDate(project.fecha_presentacion)}</span>
                        </div>
                        ` : ''}
                        ${project.hora_programada ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Hora programada: ${project.hora_programada}</span>
                        </div>
                        ` : ''}
                        ${project.timer_segundos > 0 ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="hourglass" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Duración evaluación: ${Math.floor(project.timer_segundos / 60)} minutos</span>
                        </div>
                        ` : ''}
                        ${project.puntuacion ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="star" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Puntuación: ${project.puntuacion} pts</span>
                        </div>
                        ` : ''}
                        ${project.estado ? `
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm text-slate-600">Estado: ${project.estado}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            ${project.investigadores?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-slate-900">Investigadores</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.investigadores.map(researcher => `
                        <div class="border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${researcher.nombre_completo?.charAt(0) || 'U'}
                                </div>
                                <div>
                                    <h5 class="font-medium text-slate-800">${escapeHtml(researcher.nombre_completo)}</h5>
                                    <p class="text-xs text-slate-500">${researcher.rol === 'principal' ? 'Investigador Principal' : 'Colaborador'}</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-slate-600"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(researcher.username)}</p>
                                <p class="text-slate-600"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(researcher.email)}</p>
                                <p class="text-xs ${researcher.estado === 'activo' ? 'text-green-600' : 'text-red-600'}">
                                    <i data-lucide="${researcher.estado === 'activo' ? 'check-circle' : 'x-circle'}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(researcher.estado)}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${project.docentes?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-slate-900">Docentes</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.docentes.map(teacher => `
                        <div class="border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${teacher.nombre_completo?.charAt(0) || 'D'}
                                </div>
                                <div>
                                    <h5 class="font-medium text-slate-800">${escapeHtml(teacher.nombre_completo)}</h5>
                                    <p class="text-xs text-slate-500">${teacher.rol === 'director' ? 'Director' : 'Asesor'}</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-slate-600"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(teacher.username)}</p>
                                <p class="text-slate-600"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(teacher.email)}</p>
                                <p class="text-xs ${teacher.estado === 'activo' ? 'text-green-600' : 'text-red-600'}">
                                    <i data-lucide="${teacher.estado === 'activo' ? 'check-circle' : 'x-circle'}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(teacher.estado)}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${project.evaluadores?.length > 0 ? `
            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-slate-900">Evaluadores</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    ${project.evaluadores.map(reviewer => `
                        <div class="border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    ${reviewer.nombre_completo?.charAt(0) || 'E'}
                                </div>
                                <div>
                                    <h5 class="font-medium text-slate-800">${escapeHtml(reviewer.nombre_completo)}</h5>
                                    <p class="text-xs text-slate-500">Evaluador</p>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-slate-600"><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(reviewer.username)}</p>
                                <p class="text-slate-600"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> ${escapeHtml(reviewer.email)}</p>
                                <p class="text-xs ${reviewer.estado === 'activo' ? 'text-green-600' : 'text-red-600'}">
                                    <i data-lucide="${reviewer.estado === 'activo' ? 'check-circle' : 'x-circle'}" class="w-3 h-3 inline mr-1"></i> ${capitalizeFirstLetter(reviewer.estado)}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            <div class="space-y-4 pt-4">
                <h4 class="text-lg font-semibold text-slate-900">Documentos</h4>
                <div class="mb-4">
                    <button onclick="addDocument(${project.id})" 
                            class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Agregar Documento
                    </button>
                </div>
                ${project.documents?.length > 0 ? `
                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
                    ${project.documents.map(doc => `
                        <div class="flex flex-col border border-slate-200 rounded-lg overflow-hidden hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 p-4 bg-slate-50">
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg ${getFileIcon(doc.name).color}">
                                    <i data-lucide="${getFileIcon(doc.name).icon}" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate" title="${escapeHtml(doc.name)}">${escapeHtml(doc.name)}</p>
                                    <p class="text-xs text-slate-500">${formatFileSize(doc.size)}</p>
                                </div>
                            </div>
                            <div class="flex border-t border-slate-100 divide-x divide-slate-100">
                                <button onclick="previewDocument('${escapeSingleQuote(doc.url)}', '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center gap-1 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Previsualizar
                                </button>
                                <a href="${doc.url}" download="${escapeHtml(doc.name)}" 
                                   class="flex-1 py-2 flex items-center justify-center gap-1 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Descargar
                                </a>
                                <button onclick="updateDocument('${escapeSingleQuote(doc.full_path)}', ${project.id}, '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center gap-1 text-sm text-green-600 hover:bg-green-50 transition-colors">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    Actualizar
                                </button>
                                <button onclick="deleteDocument('${escapeSingleQuote(doc.full_path)}', ${project.id}, '${escapeSingleQuote(doc.name)}')" 
                                        class="flex-1 py-2 flex items-center justify-center gap-1 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
                ` : `
                <div class="col-span-full flex items-center gap-3 text-slate-400 p-4 bg-slate-50 rounded-lg">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span class="text-sm">No hay documentos asociados</span>
                </div>
                `}
            </div>

            <div class="flex gap-4 pt-6">
                <button onclick="editProject(${project.id})" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
                    <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
                    Editar Proyecto
                </button>
                <button onclick="${project.activo ? 'toggleProjectStatus(' + project.id + ', false)' : 'toggleProjectStatus(' + project.id + ', true)'}" class="flex-1 px-6 py-3 ${project.activo ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'} text-white rounded-xl transition-all font-medium shadow-lg">
                    <i data-lucide="power" class="w-4 h-4 inline mr-2"></i>
                    ${project.activo ? 'Desactivar Proyecto' : 'Activar Proyecto'}
                </button>
            </div>
        `;
        
        lucide.createIcons();
        showModal('project-details-modal');
    };

    window.editProject = function(projectId) {
        const project = allProjects.find(p => p.id == projectId);
        if (!project) {
            showNotification('Proyecto no encontrado', 'error');
            return;
        }
        
        currentEditingProject = project;
        
        document.getElementById('edit-project-id').value = project.id;
        document.getElementById('edit-titulo').value = project.titulo || '';
        document.getElementById('edit-linea-investigacion').value = project.linea_investigacion_id || '';
        document.getElementById('edit-visibilidad').value = project.visibilidad || 'privado';
        document.getElementById('edit-version').value = project.version || '1.0';
        document.getElementById('edit-fase').value = project.fase || 'propuesta';
        document.getElementById('edit-fecha-presentacion').value = project.fecha_presentacion || '';
        document.getElementById('edit-hora-programada').value = project.hora_programada || '';
        document.getElementById('edit-duracion-evaluacion').value = project.timer_segundos ? Math.floor(project.timer_segundos / 60) : '';
        document.getElementById('edit-descripcion').value = project.descripcion || '';
        document.getElementById('edit-palabras-clave').value = project.palabras_clave || '';
        
        const researchersContainer = document.getElementById('edit-researchersContainer');
        researchersContainer.innerHTML = '';
        
        if (project.investigadores?.length > 0) {
            project.investigadores.forEach((researcher, index) => {
                const researcherField = document.createElement('div');
                researcherField.className = 'participant-field flex gap-3 mb-3 items-end';
                researcherField.dataset.type = 'researcher';
                
                researcherField.innerHTML = `
                    <div class="flex-1 grid md:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="">Seleccionar investigador</option>
                                ${allResearchers.map(user => `
                                    <option value="${user.user_id}" ${user.user_id == researcher.usuario_uid ? 'selected' : ''}>
                                        ${escapeHtml(user.first_name + ' ' + user.last_name)}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="space-y-1">
                            <select name="investigadores[${index}][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="principal" ${researcher.rol === 'principal' ? 'selected' : ''}>Principal</option>
                                <option value="colaborador" ${researcher.rol === 'colaborador' ? 'selected' : ''}>Colaborador</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                `;
                
                researchersContainer.appendChild(researcherField);
            });
        } else {
            addResearcher('edit');
        }
        
        const teachersContainer = document.getElementById('edit-teachersContainer');
        teachersContainer.innerHTML = '';
        
        if (project.docentes?.length > 0) {
            project.docentes.forEach((teacher, index) => {
                const teacherField = document.createElement('div');
                teacherField.className = 'participant-field flex gap-3 mb-3 items-end';
                teacherField.dataset.type = 'teacher';
                
                teacherField.innerHTML = `
                    <div class="flex-1 grid md:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <select name="docentes[${index}][usuario_uid]" class="teacher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="">Seleccionar docente</option>
                                ${allTeachers.map(user => `
                                    <option value="${user.user_id}" ${user.user_id == teacher.usuario_uid ? 'selected' : ''}>
                                        ${escapeHtml(user.first_name + ' ' + user.last_name)}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="space-y-1">
                            <select name="docentes[${index}][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="asesor" ${teacher.rol === 'asesor' ? 'selected' : ''}>Asesor</option>    
                                <option value="director" ${teacher.rol === 'director' ? 'selected' : ''}>Director</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" onclick="removeParticipant(this, 'teacher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                `;
                
                teachersContainer.appendChild(teacherField);
            });
        } else {
            addTeacher('edit');
        }
        
        const reviewersContainer = document.getElementById('edit-reviewersContainer');
        reviewersContainer.innerHTML = '';
        
        if (project.evaluadores?.length > 0) {
            project.evaluadores.forEach((reviewer, index) => {
                const reviewerField = document.createElement('div');
                reviewerField.className = 'participant-field flex gap-3 mb-3 items-end';
                reviewerField.dataset.type = 'reviewer';
                
                reviewerField.innerHTML = `
                    <div class="flex-1 space-y-1">
                        <select name="revisores[]" class="reviewer-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            <option value="">Seleccionar evaluador</option>
                            ${allReviewers.map(user => `
                                <option value="${user.user_id}" ${user.user_id == reviewer.usuario_uid ? 'selected' : ''}>
                                    ${escapeHtml(user.first_name + ' ' + user.last_name)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <button type="button" onclick="removeParticipant(this, 'reviewer')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                `;
                
                reviewersContainer.appendChild(reviewerField);
            });
        } else {
            addReviewer('edit');
        }
        
        lucide.createIcons();
        showModal('project-edit-modal');
    };

    window.addResearcher = function(prefix = '') {
        const container = document.getElementById(`${prefix}-researchersContainer`);
        const index = container.querySelectorAll('.participant-field[data-type="researcher"]').length;
        
        const researcherField = document.createElement('div');
        researcherField.className = 'participant-field flex gap-3 mb-3 items-end';
        researcherField.dataset.type = 'researcher';
        
        researcherField.innerHTML = `
            <div class="flex-1 grid md:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        <option value="">Seleccionar investigador</option>
                        ${allResearchers.map(user => `
                            <option value="${user.user_id}">
                                ${escapeHtml(user.first_name + ' ' + user.last_name)}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="space-y-1">
                    <select name="investigadores[${index}][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        <option value="principal">Principal</option>
                        <option value="colaborador">Colaborador</option>
                    </select>
                </div>
            </div>
            <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        
        container.appendChild(researcherField);
        lucide.createIcons();
    };

    window.addTeacher = function(prefix = '') {
        const container = document.getElementById(`${prefix}-teachersContainer`);
        const index = container.querySelectorAll('.participant-field[data-type="teacher"]').length;
        
        const teacherField = document.createElement('div');
        teacherField.className = 'participant-field flex gap-3 mb-3 items-end';
        teacherField.dataset.type = 'teacher';
        
        teacherField.innerHTML = `
            <div class="flex-1 grid md:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <select name="docentes[${index}][usuario_uid]" class="teacher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        <option value="">Seleccionar docente</option>
                        ${allTeachers.map(user => `
                            <option value="${user.user_id}">
                                ${escapeHtml(user.first_name + ' ' + user.last_name)}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="space-y-1">
                    <select name="docentes[${index}][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        <option value="asesor">Asesor</option>    
                        <option value="director">Director</option>
                    </select>
                </div>
            </div>
            <button type="button" onclick="removeParticipant(this, 'teacher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        
        container.appendChild(teacherField);
        lucide.createIcons();
    };

    window.addReviewer = function(prefix = '') {
        const container = document.getElementById(`${prefix}-reviewersContainer`);
        const index = container.querySelectorAll('.participant-field[data-type="reviewer"]').length;
        
        const reviewerField = document.createElement('div');
        reviewerField.className = 'participant-field flex gap-3 mb-3 items-end';
        reviewerField.dataset.type = 'reviewer';
        
        reviewerField.innerHTML = `
            <div class="flex-1 space-y-1">
                <select name="revisores[]" class="reviewer-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    <option value="">Seleccionar evaluador</option>
                    ${allReviewers.map(user => `
                        <option value="${user.user_id}">
                            ${escapeHtml(user.first_name + ' ' + user.last_name)}
                        </option>
                    `).join('')}
                </select>
            </div>
            <button type="button" onclick="removeParticipant(this, 'reviewer')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        
        container.appendChild(reviewerField);
        lucide.createIcons();
    };

    window.removeParticipant = function(button, type) {
        const container = button.closest('.participant-field[data-type="' + type + '"]');
        if (container) {
            container.remove();
        }
    };

    window.saveProjectChanges = function() {
        const form = document.getElementById('projectEditForm');
        const formData = new FormData(form);
        formData.append('action', 'update');
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                showNotification('Proyecto actualizado correctamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al actualizar el proyecto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al actualizar el proyecto', 'error');
        });
        
        return false;
    };

    window.previewDocument = function(fileUrl, fileName) {
        currentPreviewDocument = { url: fileUrl, name: fileName };
        
        document.getElementById('document-preview-title').textContent = `Previsualización: ${fileName}`;
        const iframe = document.getElementById('document-preview-iframe');
        
        const ext = fileName.split('.').pop().toLowerCase();
        
        if (ext === 'pdf') {
            iframe.src = fileUrl;
        } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
            iframe.src = `https://docs.google.com/viewer?url=${encodeURIComponent(fileUrl)}&embedded=true`;
        } else {
            iframe.srcdoc = `
                <html>
                    <head><title>Previsualización no disponible</title></head>
                    <body style="display: flex; justify-content: center; align-items: center; height: 100%; margin: 0;">
                        <div style="text-align: center; padding: 20px;">
                            <h2>Previsualización no disponible</h2>
                            <p>El tipo de archivo no soporta previsualización en línea.</p>
                            <p>Por favor descargue el archivo para verlo.</p>
                        </div>
                    </body>
                </html>
            `;
        }
        
        showModal('document-preview-modal');
    };

    window.downloadCurrentPreview = function() {
        if (currentPreviewDocument) {
            const link = document.createElement('a');
            link.href = currentPreviewDocument.url;
            link.download = currentPreviewDocument.name;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    };

    window.toggleProjectStatus = function(projectId, activate) {
        if (!confirm(`¿Estás seguro de ${activate ? 'activar' : 'desactivar'} este proyecto?`)) {
            return;
        }

        const action = activate ? 'activar' : 'desactivar';
        
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=${action}&id=${projectId}`
        }).then(response => {
            if (response.ok) {
                showNotification(`Proyecto ${activate ? 'activado' : 'desactivado'} correctamente`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al cambiar el estado');
            }
        }).catch(error => {
            console.error('Error:', error);
            showNotification(`Error al ${activate ? 'activar' : 'desactivar'} el proyecto`, 'error');
        });
    };

    window.toggleProjectVisibility = function(projectId, visibility) {
        if (!confirm(`¿Estás seguro de cambiar la visibilidad a ${visibility === 'publico' ? 'público' : 'privado'}?`)) {
            return;
        }

        const action = visibility === 'publico' ? 'publicar' : 'privado';
        
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=${action}&id=${projectId}`
        }).then(response => {
            if (response.ok) {
                showNotification('Visibilidad actualizada correctamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al cambiar la visibilidad');
            }
        }).catch(error => {
            console.error('Error:', error);
            showNotification('Error al cambiar la visibilidad', 'error');
        });
    };

    window.confirmDeleteProject = function(projectId) {
        if (!confirm('¿Estás seguro de eliminar este proyecto?')) {
            return;
        }
        
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${projectId}`
        }).then(response => {
            if (response.ok) {
                showNotification('Proyecto eliminado correctamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al eliminar el proyecto');
            }
        }).catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar el proyecto', 'error');
        });
    };

    window.deleteDocument = function(documentPath, projectId, documentName) {
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
                showNotification('Documento eliminado correctamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error('Error al eliminar el documento');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar el documento', 'error');
        });
    };

    window.updateDocument = function(documentPath, projectId, documentName) {
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
            }).then(response => {
                if (response.ok) {
                    showNotification('Documento actualizado correctamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error('Error al actualizar el documento');
                }
            }).catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar el documento', 'error');
            });
        });
        
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    };

    window.addDocument = function(projectId) {
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
                if (response.ok) {
                    showNotification('Documento agregado correctamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error('Error al agregar el documento');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar el documento', 'error');
            });
        });
        
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    };

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
            txt: { icon: 'file-text', color: 'bg-gray-500' },
            csv: { icon: 'file-spreadsheet', color: 'bg-green-500' },
            default: { icon: 'file', color: 'bg-purple-500' }
        };
        return icons[ext] || icons.default;
    }

    function formatFileSize(bytes) {
        if (!bytes) return 'Tamaño desconocido';
        if (bytes < 1024) return bytes + ' bytes';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function escapeSingleQuote(str) {
        return str.replace(/'/g, "\\'");
    }
});
</script>