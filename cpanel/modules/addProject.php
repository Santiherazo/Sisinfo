<?php
if (!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpproject', 'action' => 'create']])) {
    die('No tienes permisos para acceder a este módulo.');
}

try {
    $lines = $researchLines->getAllActivas();
    $allResearchers = $roleManager->getAllStudents() ?? [];
    $allTeachers = $roleManager->getAllTeachers() ?? [];
    $allReviewers = $roleManager->getAllEvaluators() ?? [];

} catch (Exception $e) {
    echo "<div class='bg-red-100 text-red-800 p-3 rounded mb-4'>Error cargando datos: {$e->getMessage()}</div>";
}

try {
    if (isset($_POST['webengineRegister_submit'])) {
        try {
            $required_fields = [
                'titulo' => 'Título del proyecto',
                'linea_investigacion_id' => 'Línea de investigación',
                'fase' => 'Fase del proyecto',
                'version' => 'Versión',
                'visibilidad' => 'Visibilidad',
                'docentes' => 'Docentes',
                'revisores' => 'Revisores',
                'investigadores' => 'Investigadores'
            ];

            foreach ($required_fields as $field => $label) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo '$label' es obligatorio.");
                }
            }

            $uploader = new uploadManager();
            $projectDirName = 'proyecto_'.date('YmdHis').'_'.bin2hex(random_bytes(4));
            $documentPath = null;
            
            if (isset($_FILES['documento']) && $_FILES['documento']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploader->setAllowedExtensions(['pdf', 'doc', 'docx']);
                $uploader->setMaxFileSize(10 * 1024 * 1024);
                
                $documentPath = $uploader->upload($_FILES['documento'], $projectDirName, 'projects');
                
                if ($documentPath === false) {
                    throw new Exception("Error al subir documento: " . implode(", ", $uploader->getErrors()));
                }
            }

            $formData = [
                'titulo' => trim($_POST['titulo']),
                'linea_investigacion_id' => intval($_POST['linea_investigacion_id']),
                'fase' => trim($_POST['fase']),
                'version' => trim($_POST['version']),
                'timer_segundos' => isset($_POST['duracion_evaluacion']) && $_POST['duracion_evaluacion'] !== '' 
                    ? intval($_POST['duracion_evaluacion']) * 60 
                    : 0,
                'docentes' => $_POST['docentes'] ?? [],
                'evaluadores' => $_POST['revisores'] ?? [],
                'investigadores' => $_POST['investigadores'] ?? [],
                'descripcion' => $_POST['descripcion'] ?? null,
                'palabras_clave' => $_POST['palabras_clave'] ?? null,
                'documento' => $documentPath,
                'directorio' => $projectDirName,
                'visibilidad' => $_POST['visibilidad'] ?? 'privado',
                'hora_programada' => !empty($_POST['hora_programada']) ? $_POST['hora_programada'] : null,
                'fecha_presentacion' => !empty($_POST['fecha_presentacion']) ? $_POST['fecha_presentacion'] : null
            ];

            $proyectoId = $projectmanager->createProject($formData);

            if ($proyectoId === false) {
                if ($documentPath && file_exists($documentPath)) {
                    unlink($documentPath);
                }
                throw new Exception("No se pudo registrar el proyecto. Intenta nuevamente.");
            }

            echo '<div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-green-500 bg-gradient-to-r from-green-50/60 to-white/20 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-green-100 rounded-xl shadow-inner">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-green-900 mb-3">Proyecto registrado correctamente</h3>
                    </div>
                </div>
            </div>';

        } catch (Exception $ex) {
            if (isset($documentPath) && file_exists($documentPath)) {
                unlink($documentPath);
            }
            echo '<div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-red-500 bg-gradient-to-r from-red-50/60 to-white/20 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 rounded-xl shadow-inner">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-red-900 mb-3">Error</h3>
                        <p class="text-sm text-red-700">' . htmlspecialchars($ex->getMessage()) . '</p>
                    </div>
                </div>
            </div>';
        }
    }
} catch (Exception $e) {
    echo '<div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-red-500 bg-gradient-to-r from-red-50/60 to-white/20 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-red-100 rounded-xl shadow-inner">
                <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-red-900 mb-3">Error</h3>
                <p class="text-sm text-red-700">' . htmlspecialchars($e->getMessage()) . '</p>
            </div>
        </div>
    </div>';
}
?>

<div class="glassmorphism rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Nuevo Proyecto</h2>
                <p class="text-sm text-blue-100">Complete todos los campos obligatorios (*)</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <form id="projectForm" method="POST" enctype="multipart/form-data" class="space-y-8">            
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
                        <input type="text" name="titulo" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ingrese el título del proyecto" required>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Línea de Investigación *</label>
                            <select name="linea_investigacion_id" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="">Seleccionar línea</option>
                                <?php foreach($lines as $line): ?>
                                    <option value="<?= $line['id'] ?>"><?= htmlspecialchars($line['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Visibilidad *</label>
                            <select name="visibilidad" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
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
                        <input type="text" name="version" value="1.0" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Fase *</label>
                        <select name="fase" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                            <option value="propuesta">Propuesta</option>
                            <option value="desarrollo">Desarrollo</option>
                            <option value="aplicacion">Aplicación</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Fecha de Presentación</label>
                        <input type="date" name="fecha_presentacion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Hora de Evaluación</label>
                        <input type="time" name="hora_programada" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Duración Evaluación (min) *</label>
                        <input type="number" name="duracion_evaluacion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Minutos">
                    </div>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Descripción *</label>
                    <textarea rows="4" name="descripcion" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Descripción detallada de esta versión..." required></textarea>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Palabras Clave</label>
                    <input type="text" name="palabras_clave" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Separadas por comas: IA, Machine Learning, Educación" >
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Documento (PDF o Word)</label>
                    <div class="mt-1 flex items-center gap-3">
                        <label class="flex-1 cursor-pointer">
                            <div class="relative group">
                                <input type="file" name="documento" accept=".pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg group-hover:bg-white transition-all font-medium flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    <span id="fileNameDisplay">Seleccionar archivo</span>
                                </div>
                            </div>
                        </label>
                        <div class="text-xs text-slate-500">Máx. 10MB (PDF, DOC, DOCX)</div>
                    </div>
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
                        <button type="button" onclick="addResearcher()" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                            <i data-lucide="plus" class="w-3 h-3"></i> Añadir investigador
                        </button>
                    </div>
                    
                    <div id="researchersContainer">
                        <div class="participant-field flex gap-3 mb-3 items-end" data-type="researcher">
                            <div class="flex-1 grid md:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <select name="investigadores[0][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required onchange="handleResearcherSelect(this)">
                                        <option value="">Seleccionar investigador</option>
                                        <?php foreach($allResearchers as $user): ?>
                                            <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <select name="investigadores[0][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                        <option value="principal">Principal</option>
                                        <option value="colaborador">Colaborador</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" onclick="removeParticipant(this, 'researcher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
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
                        <button type="button" onclick="addTeacher()" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                            <i data-lucide="plus" class="w-3 h-3"></i> Añadir docente
                        </button>
                    </div>
                    
                    <div id="teachersContainer">
                        <div class="participant-field flex gap-3 mb-3 items-end" data-type="teacher">
                            <div class="flex-1 grid md:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <select name="docentes[0][usuario_uid]" class="teacher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required onchange="handleTeacherSelect(this)">
                                        <option value="">Seleccionar docente</option>
                                        <?php foreach($allTeachers as $user): ?>
                                            <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <select name="docentes[0][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                        <option value="asesor">Asesor</option>    
                                        <option value="director">Director</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" onclick="removeParticipant(this, 'teacher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
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
                        <button type="button" onclick="addReviewer()" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                            <i data-lucide="plus" class="w-3 h-3"></i> Añadir evaluador
                        </button>
                    </div>
                    
                    <div id="reviewersContainer">
                        <div class="participant-field flex gap-3 mb-3 items-end" data-type="reviewer">
                            <div class="flex-1 space-y-1">
                                <select name="revisores[]" class="reviewer-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" onchange="handleReviewerSelect(this)">
                                    <option value="">Seleccionar evaluador</option>
                                    <?php foreach($allReviewers as $user): ?>
                                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" onclick="removeParticipant(this, 'reviewer')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-slate-100">
                <button type="button" onclick="closeModal('project-modal')" class="order-2 sm:order-1 px-6 py-3 bg-white/80 border border-slate-200 text-slate-700 rounded-lg hover:bg-white transition-all font-medium flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Cancelar
                </button>
                
                <button type="submit" name="webengineRegister_submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all font-medium flex items-center justify-center gap-2 shadow-lg">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Crear Proyecto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedResearchers = new Set();
let selectedTeachers = new Set();
let selectedReviewers = new Set();

function updateSelectOptions(selectElement, type) {
    const selectedValue = selectElement.value;
    const allOptions = selectElement.querySelectorAll('option');
    
    allOptions.forEach(option => {
        option.style.display = 'block';
    });
    
    let selectedSet;
    switch(type) {
        case 'researcher':
            selectedSet = selectedResearchers;
            break;
        case 'teacher':
            selectedSet = selectedTeachers;
            break;
        case 'reviewer':
            selectedSet = selectedReviewers;
            break;
    }
    
    allOptions.forEach(option => {
        if (option.value !== "" && selectedSet.has(option.value) && option.value !== selectedValue) {
            option.style.display = 'none';
        }
    });
}

function addResearcher() {
    const container = document.getElementById('researchersContainer');
    const newField = document.createElement('div');
    newField.className = 'participant-field flex gap-3 mb-3 items-end';
    newField.setAttribute('data-type', 'researcher');
    
    const index = container.querySelectorAll('.participant-field').length;
    
    newField.innerHTML = `
        <div class="flex-1 grid md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <select name="investigadores[${index}][usuario_uid]" class="researcher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required onchange="handleResearcherSelect(this)">
                    <option value="">Seleccionar investigador</option>
                    <?php foreach($allResearchers as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                    <?php endforeach; ?>
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
    
    container.appendChild(newField);
    refreshLucideIcons();
}

function handleResearcherSelect(selectElement) {
    const previousValue = selectElement.getAttribute('data-previous-value');
    const newValue = selectElement.value;
    
    if (previousValue) {
        selectedResearchers.delete(previousValue);
    }
    
    if (newValue) {
        selectedResearchers.add(newValue);
        selectElement.setAttribute('data-previous-value', newValue);
    } else {
        selectElement.removeAttribute('data-previous-value');
    }
    
    document.querySelectorAll('.researcher-select').forEach(select => {
        updateSelectOptions(select, 'researcher');
    });
}

function addTeacher() {
    const container = document.getElementById('teachersContainer');
    const newField = document.createElement('div');
    newField.className = 'participant-field flex gap-3 mb-3 items-end';
    newField.setAttribute('data-type', 'teacher');
    
    const index = container.querySelectorAll('.participant-field').length;
    
    newField.innerHTML = `
        <div class="flex-1 grid md:grid-cols-2 gap-3">
            <div class="space-y-1">
                <select name="docentes[${index}][usuario_uid]" class="teacher-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required onchange="handleTeacherSelect(this)">
                    <option value="">Seleccionar docente</option>
                    <?php foreach($allTeachers as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-1">
                <select name="docentes[${index}][rol]" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                    <option value="director">Director</option>
                    <option value="jurado">Jurado</option>
                    <option value="asesor">Asesor</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="removeParticipant(this, 'teacher')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(newField);
    refreshLucideIcons();
}

function handleTeacherSelect(selectElement) {
    const previousValue = selectElement.getAttribute('data-previous-value');
    const newValue = selectElement.value;
    
    if (previousValue) {
        selectedTeachers.delete(previousValue);
    }
    
    if (newValue) {
        selectedTeachers.add(newValue);
        selectElement.setAttribute('data-previous-value', newValue);
    } else {
        selectElement.removeAttribute('data-previous-value');
    }
    
    document.querySelectorAll('.teacher-select').forEach(select => {
        updateSelectOptions(select, 'teacher');
    });
}

function addReviewer() {
    const container = document.getElementById('reviewersContainer');
    const newField = document.createElement('div');
    newField.className = 'participant-field flex gap-3 mb-3 items-end';
    newField.setAttribute('data-type', 'reviewer');
    
    const index = container.querySelectorAll('.participant-field').length;
    
    newField.innerHTML = `
        <div class="flex-1 space-y-1">
            <select name="revisores[]" class="reviewer-select w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" onchange="handleReviewerSelect(this)">
                <option value="">Seleccionar revisor</option>
                <?php foreach($allReviewers as $user): ?>
                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" onclick="removeParticipant(this, 'reviewer')" class="p-2 text-slate-400 hover:text-red-500 transition-colors mb-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(newField);
    refreshLucideIcons();
}

function handleReviewerSelect(selectElement) {
    const previousValue = selectElement.getAttribute('data-previous-value');
    const newValue = selectElement.value;
    
    if (previousValue) {
        selectedReviewers.delete(previousValue);
    }
    
    if (newValue) {
        selectedReviewers.add(newValue);
        selectElement.setAttribute('data-previous-value', newValue);
    } else {
        selectElement.removeAttribute('data-previous-value');
    }
    
    document.querySelectorAll('.reviewer-select').forEach(select => {
        updateSelectOptions(select, 'reviewer');
    });
}

function removeParticipant(button, type) {
    const container = button.closest('.participant-field');
    const parentContainer = document.getElementById(`${type}sContainer`);
    const fields = parentContainer.querySelectorAll('.participant-field');
    
    if ((type === 'researcher' || type === 'teacher') && fields.length <= 1) {
        showNotification(`Debe haber al menos un ${type === 'researcher' ? 'investigador' : 'docente'}`, 'error');
        return;
    }
    
    const selectElement = container.querySelector('select');
    if (selectElement) {
        const selectedValue = selectElement.value;
        switch(type) {
            case 'researcher':
                selectedResearchers.delete(selectedValue);
                break;
            case 'teacher':
                selectedTeachers.delete(selectedValue);
                break;
            case 'reviewer':
                selectedReviewers.delete(selectedValue);
                break;
        }
    }
    
    container.remove();
    refreshLucideIcons();
    
    switch(type) {
        case 'researcher':
            document.querySelectorAll('.researcher-select').forEach(select => {
                updateSelectOptions(select, 'researcher');
            });
            break;
        case 'teacher':
            document.querySelectorAll('.teacher-select').forEach(select => {
                updateSelectOptions(select, 'teacher');
            });
            break;
        case 'reviewer':
            document.querySelectorAll('.reviewer-select').forEach(select => {
                updateSelectOptions(select, 'reviewer');
            });
            break;
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement("div");
    notification.className = `fixed bottom-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in z-50`;
    notification.innerHTML = `<i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-4 h-4"></i> ${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove("animate-fade-in");
        notification.classList.add("animate-fade-out");
        setTimeout(() => notification.remove(), 300);
    }, 3000);
    
    refreshLucideIcons();
}

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="documento"]').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo';
        document.getElementById('fileNameDisplay').textContent = fileName;
    });
    refreshLucideIcons();
});
</script>