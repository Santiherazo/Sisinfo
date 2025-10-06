<?php
$projectManager = new ProjectManager($pdo);
$evaluationManager = new EvaluationManager($pdo);
$researchLines = new ResearchLineManager($pdo);
$uploadManager = new UploadManager();

$researchLinesJson = $researchLines->getAllActivas();
$allProyects = $projectManager->getAllProjects();
$allEvaluations = $evaluationManager->getAllRatingsWithSummaries();

// Crear un mapeo de ID de línea de investigación a nombre
$researchLinesMap = [];
foreach ($researchLinesJson as $line) {
    $researchLinesMap[$line['id']] = $line['nombre'];
}

$processedProjects = [];
foreach ($allProyects as $project) {
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
    
    // Organizar evaluaciones para este proyecto por evaluador
    $projectEvaluations = [];
    foreach ($allEvaluations as $evaluation) {
        if ($evaluation['project_id'] == $project['id']) {
            $evaluadorId = $evaluation['evaluador_uid'];
            $sessionToken = $evaluation['session_token'];
            
            if (!isset($projectEvaluations[$evaluadorId])) {
                $projectEvaluations[$evaluadorId] = [
                    'evaluador' => [
                        'id' => $evaluation['evaluador_uid'],
                        'username' => $evaluation['evaluador_username'],
                        'firstname' => $evaluation['evaluador_firstname'],
                        'lastname' => $evaluation['evaluador_lastname']
                    ],
                    'evaluaciones' => []
                ];
            }
            
            // Agrupar por sesión de evaluación
            if (!isset($projectEvaluations[$evaluadorId]['evaluaciones'][$sessionToken])) {
                $projectEvaluations[$evaluadorId]['evaluaciones'][$sessionToken] = [
                    'summary' => [
                        'id' => $evaluation['summary_id'],
                        'comentario' => $evaluation['summary_comentario'],
                        'calificacion_total' => $evaluation['summary_calificacion_total'],
                        'estado_evaluacion' => $evaluation['summary_estado_evaluacion'],
                        'tiempo_total' => $evaluation['summary_tiempo_total'],
                        'fecha_inicio' => $evaluation['summary_fecha_inicio'],
                        'fecha_fin' => $evaluation['summary_fecha_fin']
                    ],
                    'criterios' => []
                ];
            }
            
            // Agregar criterio a la evaluación
            $projectEvaluations[$evaluadorId]['evaluaciones'][$sessionToken]['criterios'][] = [
                'nombre' => $evaluation['criterio_nombre'],
                'valor' => $evaluation['criterio_valor'],
                'calificacion' => $evaluation['calificacion'],
                'observacion_personal' => $evaluation['observacion_personal']
            ];
        }
    }
    
    // Reorganizar las evaluaciones para una estructura más limpia
    $evaluacionesFinales = [];
    foreach ($projectEvaluations as $evaluadorData) {
        foreach ($evaluadorData['evaluaciones'] as $evaluacion) {
            $evaluacionesFinales[] = [
                'evaluador' => $evaluadorData['evaluador'],
                'summary' => $evaluacion['summary'],
                'criterios' => $evaluacion['criterios']
            ];
        }
    }
    
    $researchers = [];
    foreach ($project['researchers'] as $researcher) {
        $researchers[] = [
            'id' => $researcher['id'],
            'username' => $researcher['username'],
            'email' => $researcher['email'],
            'firstname' => $researcher['firstname'],
            'lastname' => $researcher['lastname'],
            'role' => $researcher['role'],
            'state' => $researcher['state']
        ];
    }
    
    $teachers = [];
    foreach ($project['teachers'] as $teacher) {
        $teachers[] = [
            'id' => $teacher['id'],
            'username' => $teacher['username'],
            'email' => $teacher['email'],
            'firstname' => $teacher['firstname'],
            'lastname' => $teacher['lastname'],
            'role' => $teacher['role'],
            'state' => $teacher['state']
        ];
    }
    
    $reviewers = [];
    foreach ($project['reviewers'] as $reviewer) {
        $reviewers[] = [
            'id' => $reviewer['id'],
            'username' => $reviewer['username'],
            'email' => $reviewer['email'],
            'firstname' => $reviewer['firstname'],
            'lastname' => $reviewer['lastname'],
            'state' => $reviewer['state']
        ];
    }
    
    // Obtener el nombre de la línea de investigación
    $lineaNombre = isset($researchLinesMap[$project['linea_investigacion_id']]) 
        ? $researchLinesMap[$project['linea_investigacion_id']] 
        : 'Línea no especificada';
    
    $processedProjects[] = [
        'id' => $project['id'],
        'titulo' => $project['titulo'],
        'linea_investigacion_id' => $project['linea_investigacion_id'],
        'linea_investigacion_nombre' => $lineaNombre,
        'visibilidad' => $project['visibilidad'],
        'activo' => $project['activo'],
        'directorio' => $project['directorio'],
        'version' => $project['version'],
        'fase' => $project['fase'],
        'estado' => $project['estado'],
        'descripcion' => $project['descripcion'],
        'palabras_clave' => $project['palabras_clave'],
        'calificado' => $project['calificado'],
        'puntuacion' => $project['puntuacion'],
        'timer_segundos' => $project['timer_segundos'],
        'hora_programada' => $project['hora_programada'],
        'fecha_presentacion' => $project['fecha_presentacion'],
        'creado_en' => $project['creado_en'],
        'actualizado_en' => $project['actualizado_en'],
        'researchers' => $researchers,
        'teachers' => $teachers,
        'reviewers' => $reviewers,
        'documents' => $documents,
        'evaluations' => $evaluacionesFinales
    ];
}

// Contar evaluaciones completadas y pendientes
$completedEvaluations = 0;
$pendingEvaluations = 0;

foreach ($processedProjects as $project) {
    foreach ($project['evaluations'] as $evaluation) {
        if ($evaluation['summary']['estado_evaluacion'] === 'completa') {
            $completedEvaluations++;
        } else {
            $pendingEvaluations++;
        }
    }
}
?>

<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Módulo de Evaluaciones</h2>
            <p class="text-slate-600">Gestione evaluaciones y calificaciones de proyectos académicos</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600"><?php echo $completedEvaluations; ?></p>
                <p class="text-xs text-slate-500">Completadas</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-orange-600"><?php echo $pendingEvaluations; ?></p>
                <p class="text-xs text-slate-500">Pendientes</p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Proyectos Evaluados</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full font-medium">Todos</button>
                <button class="px-3 py-1 text-sm bg-white/60 text-slate-700 rounded-full font-medium">Recientes</button>
                <button class="px-3 py-1 text-sm bg-white/60 text-slate-700 rounded-full font-medium">Mejores</button>
            </div>
        </div>
        
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($processedProjects as $project): ?>
            <div class="bg-white/60 rounded-xl p-6 hover:bg-white/80 transition-all duration-200 shadow-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-900 mb-2"><?php echo htmlspecialchars($project['titulo']); ?></h4>
                        <p class="text-sm text-slate-600 mb-1"><?php echo htmlspecialchars($project['descripcion']); ?></p>
                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                            <?php echo htmlspecialchars($project['linea_investigacion_nombre']); ?>
                        </span>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-1">
                            <i data-lucide="star" class="w-4 h-4 fill-yellow-400 text-yellow-400"></i>
                            <span class="font-bold text-slate-900">
                                <?php 
                                $totalScore = 0;
                                $evaluationCount = 0;
                                foreach ($project['evaluations'] as $evaluation) {
                                    if ($evaluation['summary']['estado_evaluacion'] === 'completa') {
                                        $totalScore += floatval($evaluation['summary']['calificacion_total']);
                                        $evaluationCount++;
                                    }
                                }
                                echo $evaluationCount > 0 ? number_format($totalScore / $evaluationCount, 1) : '0.0';
                                ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-500">/5.0</span>
                    </div>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                        <span class="text-sm text-slate-600">
                            <?php
                            $principalResearchers = array_filter($project['researchers'], function($researcher) {
                                return $researcher['role'] === 'principal';
                            });
                            if (!empty($principalResearchers)) {
                                $researcher = reset($principalResearchers);
                                echo 'Estudiante: ' . htmlspecialchars($researcher['firstname'] . ' ' . $researcher['lastname']);
                            } else {
                                echo 'Estudiante: No asignado';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-slate-500"></i>
                        <span class="text-sm text-slate-600">
                            <?php
                            if (!empty($project['teachers'])) {
                                $teacher = $project['teachers'][0];
                                echo 'Asesor: ' . htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']);
                            } else {
                                echo 'Asesor: No asignado';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                        <span class="text-sm text-slate-600">
                            <?php
                            $lastEvaluationDate = '';
                            foreach ($project['evaluations'] as $evaluation) {
                                if ($evaluation['summary']['estado_evaluacion'] === 'completa') {
                                    $evalDate = date('d M Y', strtotime($evaluation['summary']['fecha_fin']));
                                    if ($evalDate > $lastEvaluationDate) {
                                        $lastEvaluationDate = $evalDate;
                                    }
                                }
                            }
                            echo 'Evaluado: ' . ($lastEvaluationDate ?: 'No evaluado');
                            ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-slate-500"></i>
                        <span class="text-sm text-slate-600">
                            Evaluadores: <?php echo count($project['reviewers']); ?>
                        </span>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>Progreso</span>
                        <span>
                            <?php
                            $totalEvaluations = count($project['evaluations']);
                            $completedEvaluations = 0;
                            foreach ($project['evaluations'] as $evaluation) {
                                if ($evaluation['summary']['estado_evaluacion'] === 'completa') {
                                    $completedEvaluations++;
                                }
                            }
                            $progress = $totalEvaluations > 0 ? ($completedEvaluations / $totalEvaluations) * 100 : 0;
                            echo round($progress) . '%';
                            ?>
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>
                <button onclick="openEvaluationModal(<?php echo $project['id']; ?>)" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Detalles Completo
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal para Ver Evaluación -->
<div id="evaluationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="glassmorphism rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Detalles de Evaluación</h2>
                    <p class="text-slate-600">Información completa sobre la evaluación realizada</p>
                </div>
                <button onclick="closeEvaluationModal()" class="p-2 rounded-full hover:bg-white/20 transition-all">
                    <i data-lucide="x" class="w-6 h-6 text-slate-600"></i>
                </button>
            </div>

            <div id="modalContent">
                <!-- El contenido se cargará dinámicamente con JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
// Datos de proyectos para usar en el modal
const projectsData = <?php echo json_encode($processedProjects); ?>;

// Mapa de líneas de investigación
const researchLinesMap = <?php echo json_encode($researchLinesMap); ?>;

function openEvaluationModal(projectId) {
    const project = projectsData.find(p => p.id == projectId);
    if (!project) return;
    
    // Construir el contenido del modal
    let modalContent = `
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2">
                <div class="bg-white/60 rounded-xl p-6 shadow-lg mb-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Proyecto: ${escapeHtml(project.titulo)}</h3>
                    <p class="text-slate-700 mb-4">${escapeHtml(project.descripcion)}</p>
                    
                    <div class="flex items-center gap-2 mb-6">
                        <span class="inline-block px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full font-medium">
                            ${escapeHtml(project.linea_investigacion_nombre)}
                        </span>
                        <span class="inline-block px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded-full font-medium">
                            ${project.fase}
                        </span>
                        <span class="inline-block px-3 py-1 text-sm ${
                            project.estado === 'aprobado' ? 'bg-green-100 text-green-800' : 
                            project.estado === 'rechazado' ? 'bg-red-100 text-red-800' : 
                            'bg-yellow-100 text-yellow-800'
                        } rounded-full font-medium">
                            ${project.estado}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="font-semibold text-slate-900 mb-2">Información del Proyecto</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700">
                                        ${getPrincipalResearcher(project)}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="user-check" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700">
                                        ${getAdvisor(project)}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700">
                                        Fecha presentación: ${project.fecha_presentacion}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700">
                                        Hora programada: ${project.hora_programada}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-900 mb-2">Detalles Técnicos</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="git-branch" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-sm text-slate-700">Fase: ${project.fase}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="award" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-sm text-slate-700">Versión: ${project.version}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="eye" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-sm text-slate-700">Visibilidad: ${project.visibilidad}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-sm text-slate-700">Documentos: ${project.documents.length}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${project.palabras_clave ? `
                    <div class="bg-slate-100/50 rounded-lg p-4">
                        <h4 class="font-semibold text-slate-900 mb-2">Palabras Clave</h4>
                        <div class="flex flex-wrap gap-2">
                            ${project.palabras_clave.split(',').map(word => 
                                `<span class="px-2 py-1 text-xs bg-white text-slate-700 rounded-full">${escapeHtml(word.trim())}</span>`
                            ).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>`;
    
    // Añadir evaluaciones si existen
    if (project.evaluations && project.evaluations.length > 0) {
        modalContent += `<div class="bg-white/60 rounded-xl p-6 shadow-lg mb-6">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Evaluaciones Realizadas</h3>
            <div class="space-y-6">`;
        
        project.evaluations.forEach((evaluation, index) => {
            const evaluationDate = evaluation.summary.fecha_fin ? new Date(evaluation.summary.fecha_fin).toLocaleDateString() : 'En progreso';
            const totalTime = evaluation.summary.tiempo_total;
            const hours = Math.floor(totalTime / 3600);
            const minutes = Math.floor((totalTime % 3600) / 60);
            
            modalContent += `
            <div class="pb-6 ${index < project.evaluations.length - 1 ? 'border-b border-slate-200' : ''}">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="font-semibold text-slate-800">Evaluación de ${evaluation.evaluador.firstname} ${evaluation.evaluador.lastname}</h4>
                        <p class="text-sm text-slate-500">${evaluation.evaluador.username}</p>
                    </div>
                    <span class="text-sm ${evaluation.summary.estado_evaluacion === 'completa' ? 'text-green-600' : 'text-orange-600'} font-medium">
                        ${evaluation.summary.estado_evaluacion === 'completa' ? 'Completada' : 'En progreso'}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-slate-600"><strong>Fecha:</strong> ${evaluationDate}</p>
                        <p class="text-sm text-slate-600"><strong>Duración:</strong> ${hours > 0 ? hours + 'h ' : ''}${minutes}m</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-900">Calificación: ${evaluation.summary.calificacion_total}/5.0</p>
                        ${evaluation.summary.comentario ? `<p class="text-sm text-slate-600 mt-1"><strong>Comentario:</strong> ${evaluation.summary.comentario}</p>` : ''}
                    </div>
                </div>
                
                <div class="space-y-3">
                    <h5 class="font-medium text-slate-800">Criterios evaluados:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">`;
            
            evaluation.criterios.forEach(criterio => {
                const percentage = (parseFloat(criterio.calificacion) / 5) * 100;
                modalContent += `
                        <div class="bg-slate-50/50 p-3 rounded-lg">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-slate-800">${criterio.nombre}</span>
                                <span class="text-sm font-semibold">${criterio.calificacion}/5</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 mb-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                            <p class="text-xs text-slate-600 mb-1"><strong>Descripción:</strong> ${criterio.valor}</p>
                            ${criterio.observacion_personal ? `<p class="text-xs text-slate-500"><strong>Comentario:</strong> ${criterio.observacion_personal}</p>` : ''}
                        </div>`;
            });
            
            modalContent += `</div></div></div>`;
        });
        
        modalContent += `</div></div>`;
    } else {
        modalContent += `<div class="bg-white/60 rounded-xl p-6 shadow-lg mb-6">
            <div class="text-center py-8">
                <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                <h4 class="text-lg font-semibold text-slate-700 mb-2">No hay evaluaciones aún</h4>
                <p class="text-slate-500">Este proyecto no ha sido evaluado todavía.</p>
            </div>
        </div>`;
    }
    
    modalContent += `</div><div class="space-y-6">`;
    
    // Resumen de calificaciones
    let totalScore = 0;
    let evaluationCount = 0;
    
    if (project.evaluations && project.evaluations.length > 0) {
        project.evaluations.forEach(evaluation => {
            if (evaluation.summary.estado_evaluacion === 'completa') {
                totalScore += parseFloat(evaluation.summary.calificacion_total);
                evaluationCount++;
            }
        });
    }
    
    const averageScore = evaluationCount > 0 ? totalScore / evaluationCount : 0;
    const percentage = (averageScore / 5) * 100;
    
    modalContent += `
        <div class="bg-white/60 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Resumen de Calificaciones</h3>
            <div class="flex items-center justify-center mb-4">
                <div class="relative w-32 h-32">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#e6e6e6" stroke-width="3"/>
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#4ade80" stroke-width="3" stroke-dasharray="${percentage}, 100"/>
                        <text x="18" y="20.5" text-anchor="middle" font-size="8" fill="#334155" font-weight="bold">${averageScore.toFixed(1)}/5</text>
                        <text x="18" y="24.5" text-anchor="middle" font-size="5" fill="#64748b">Puntuación</text>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Evaluaciones completadas:</span>
                    <span class="font-medium text-slate-800">${evaluationCount}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Puntuación promedio:</span>
                    <span class="font-medium text-slate-800">${averageScore.toFixed(1)}/5.0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Estado:</span>
                    <span class="font-medium ${project.estado === 'aprobado' ? 'text-green-600' : project.estado === 'rechazado' ? 'text-red-600' : 'text-blue-600'}">${project.estado}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Calificado:</span>
                    <span class="font-medium text-slate-800">${project.calificado ? 'Sí' : 'No'}</span>
                </div>
            </div>
        </div>`;
    
    // Documentos del proyecto
    modalContent += `
        <div class="bg-white/60 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Documentos del Proyecto</h3>
            <div class="space-y-3">`;
    
    if (project.documents && project.documents.length > 0) {
        project.documents.forEach(document => {
            modalContent += `
                <div class="flex items-center justify-between p-3 bg-white/40 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-text" class="w-5 h-5 text-slate-500"></i>
                        <div>
                            <p class="text-sm font-medium text-slate-800">${document.name}</p>
                            <p class="text-xs text-slate-500">${document.size_formatted}</p>
                        </div>
                    </div>
                    <a href="${document.url}" target="_blank" class="p-2 text-slate-600 hover:text-blue-600 transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                    </a>
                </div>`;
        });
    } else {
        modalContent += `
            <div class="text-center py-4">
                <i data-lucide="file-x" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                <p class="text-slate-500">No hay documentos disponibles</p>
            </div>`;
    }
    
    modalContent += `</div></div>`;
    
    // Información de evaluadores asignados
    modalContent += `
        <div class="bg-white/60 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Evaluadores Asignados</h3>
            <div class="space-y-3">`;
    
    if (project.reviewers && project.reviewers.length > 0) {
        project.reviewers.forEach(reviewer => {
            modalContent += `
                <div class="flex items-center gap-3 p-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-700">${reviewer.firstname.charAt(0)}${reviewer.lastname.charAt(0)}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">${reviewer.firstname} ${reviewer.lastname}</p>
                        <p class="text-xs text-slate-500">${reviewer.username}</p>
                    </div>
                </div>`;
        });
    } else {
        modalContent += `
            <div class="text-center py-4">
                <i data-lucide="users" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                <p class="text-slate-500">No hay evaluadores asignados</p>
            </div>`;
    }
    
    modalContent += `</div></div></div></div>`;
    
    // Insertar el contenido en el modal
    document.getElementById('modalContent').innerHTML = modalContent;
    
    // Mostrar el modal
    document.getElementById('evaluationModal').classList.remove('hidden');
    
    // Renderizar iconos de Lucide
    if (window.lucide) {
        lucide.createIcons();
    }
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').classList.add('hidden');
}

// Funciones auxiliares
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getPrincipalResearcher(project) {
    const principal = project.researchers.find(r => r.role === 'principal');
    return principal ? `Estudiante: ${principal.firstname} ${principal.lastname}` : 'Estudiante: No asignado';
}

function getAdvisor(project) {
    return project.teachers.length > 0 ? `Asesor: ${project.teachers[0].firstname} ${project.teachers[0].lastname}` : 'Asesor: No asignado';
}
</script>