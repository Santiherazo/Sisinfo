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

<script>
const allProjects = <?= $projectsJson ?>;
const lineasMap = <?= $lineasJson ?>;
let currentPage = 1;
const projectsPerPage = 6;

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

function formatSeconds(seconds) {
    if (!seconds) return 'No definido';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours}h ${minutes}m`;
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

function getPhaseColorClass(phase) {
    switch (phase) {
        case 'propuesta': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'desarrollo': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'aplicacion': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
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
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] hover:shadow-lg transition-all duration-300 project-card">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] rounded-full flex items-center justify-center shadow">
                            <span class="text-white font-bold text-sm">${project.titulo?.charAt(0) || 'P'}</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 ${project.activo ? 'bg-[var(--color-success)]' : 'bg-[var(--color-text-muted)]'} rounded-full border-2 border-white"></div>
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
                        <i data-lucide="edit" class="w-4 h-4 text-[var(--color-success)]"></i>
                    </button>
                    ` : ''}
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-md text-xs font-medium">
                        ${escapeHtml(project.fase ? project.fase.charAt(0).toUpperCase() + project.fase.slice(1) : 'Propuesta')}
                    </span>
                    <span class="px-3 py-1 ${project.activo ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--color-text-muted)]/10 text-[var(--color-text-muted)]'} rounded-md text-xs font-medium">
                        ${project.activo ? 'Activo' : 'Inactivo'}
                    </span>
                    <span class="px-3 py-1 bg-[var(--color-secondary)]/10 text-[var(--color-secondary)] rounded-md text-xs font-medium">
                        ${escapeHtml(project.linea_nombre)}
                    </span>
                    ${project.timer_segundos > 0 ? `
                    <span class="px-3 py-1 bg-[var(--color-warning)]/10 text-[var(--color-warning)] rounded-md text-xs font-medium">
                        ${Math.floor(project.timer_segundos / 60)} min
                    </span>
                    ` : ''}
                    ${project.puntuacion ? `
                    <span class="px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-md text-xs font-medium">
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
                        <span class="px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-md text-xs font-medium flex items-center space-x-1">
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
        alert('Proyecto no encontrado');
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
                <div class="absolute -bottom-2 -right-2 w-6 h-6 ${project.activo ? 'bg-[var(--color-success)]' : 'bg-[var(--color-text-muted)]'} rounded-full border-4 border-white"></div>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold text-[var(--color-heading)]">${escapeHtml(project.titulo)}</h3>
                <p class="text-[var(--color-text-muted)] mb-2">Versión ${project.version || '1'}</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-full text-sm font-semibold">${capitalizeFirstLetter(project.fase)}</span>
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
        lucide.createIcons();
    }, 100);
    
    showModal('project-details-modal');
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

function editProject(projectId) {
    alert('Funcionalidad de edición no implementada en este ejemplo');
}

function previewDocument(fileUrl, fileName) {
    window.open(fileUrl, '_blank');
}

function addDocument(projectId) {
    alert('Funcionalidad de agregar documento no implementada');
}

function updateDocument(documentPath, projectId, documentName) {
    alert('Funcionalidad de actualizar documento no implementada');
}

function deleteDocument(documentPath, projectId, documentName) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el documento "${documentName}"?`)) {
        return;
    }
    alert('Funcionalidad de eliminar documento no implementada');
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
</style>