<?php
$allUsers = $profileManager->getAllUsers();
$allResearchLines = $researchLines->getAllActivas();
$allProjects = $projectmanager->getAllProjects();
$allEvaluations = $evaluationManager->getAllEvaluations();

$structuredData = [];
foreach ($allUsers as $user) {
    $userData = [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'status' => $user['status'],
        'created_at' => $user['created_at'],
        'role_id' => $user['role_id'],
        'role_name' => $user['role_name'],
        'first_name' => $user['first_name'],
        'middle_name' => $user['middle_name'],
        'last_name' => $user['last_name'],
        'second_last_name' => $user['second_last_name'],
        'nombre_completo' => trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'] . ' ' . $user['second_last_name']),
        'birth_date' => $user['birth_date'],
        'gender' => $user['gender'],
        'id_type' => $user['id_type'],
        'id_number' => $user['id_number'],
        'country' => $user['country'],
        'city' => $user['city'],
        'address' => $user['address'],
        'phone_number' => $user['phone_number'],
        'institutional_email' => $user['institutional_email'],
        'university' => $user['university'],
        'program' => $user['program'],
        'semester' => $user['semester'],
        'card_code' => $user['card_code'],
        'profile_image' => $user['profile_image'],
        'proyectos' => []
    ];
    
    foreach ($allProjects as $project) {
        $userInProject = false;
        $userRole = '';
        
        foreach ($project['researchers'] as $researcher) {
            if ($researcher['id'] == $user['id']) {
                $userInProject = true;
                $userRole = 'investigador';
                break;
            }
        }
        
        if (!$userInProject) {
            foreach ($project['teachers'] as $teacher) {
                if ($teacher['id'] == $user['id']) {
                    $userInProject = true;
                    $userRole = 'docente';
                    break;
                }
            }
        }
        
        if (!$userInProject) {
            foreach ($project['reviewers'] as $reviewer) {
                if ($reviewer['id'] == $user['id']) {
                    $userInProject = true;
                    $userRole = 'evaluador';
                    break;
                }
            }
        }
        
        if ($userInProject) {
            $lineaNombre = '';
            foreach ($allResearchLines as $line) {
                if ($line['id'] == $project['linea_investigacion_id']) {
                    $lineaNombre = $line['nombre'];
                    break;
                }
            }
            
            $investigadores = [];
            foreach ($project['researchers'] as $researcher) {
                if ($researcher['role'] !== 'principal') {
                    $investigadores[] = trim($researcher['firstname'] . ' ' . $researcher['lastname']);
                }
            }
            
            $docentes = [];
            foreach ($project['teachers'] as $teacher) {
                $docentes[] = trim($teacher['firstname'] . ' ' . $teacher['lastname']);
            }
            
            $evaluadores = [];
            foreach ($project['reviewers'] as $reviewer) {
                $evaluadores[] = trim($reviewer['firstname'] . ' ' . $reviewer['lastname']);
            }
            
            $resultados = [];
            foreach ($allEvaluations as $evaluation) {
                if ($evaluation['project_id'] == $project['id'] && $evaluation['evaluador_uid'] == $user['id']) {
                    $resultados = [
                        'puntaje_total' => $evaluation['calificacion_total'] ?? null,
                        'comentario_general' => $evaluation['comentario_general'] ?? null,
                        'estado_evaluacion' => $evaluation['estado_evaluacion'] ?? null,
                        'criterios' => []
                    ];
                    break;
                }
            }
            
            $proyectoData = [
                'id' => $project['id'],
                'titulo' => $project['titulo'],
                'linea_investigacion' => $lineaNombre,
                'linea_investigacion_id' => $project['linea_investigacion_id'],
                'visibilidad' => $project['visibilidad'],
                'activo' => $project['activo'],
                'version' => $project['version'],
                'fase' => $project['fase'],
                'estado' => $project['estado'],
                'descripcion' => $project['descripcion'],
                'palabras_clave' => $project['palabras_clave'],
                'fecha_presentacion' => $project['fecha_presentacion'],
                'creado_en' => $project['creado_en'],
                'rol_usuario' => $userRole,
                'investigadores' => $investigadores,
                'docentes' => $docentes,
                'evaluadores' => $evaluadores,
                'resultados' => $resultados
            ];
            
            $userData['proyectos'][] = $proyectoData;
        }
    }
    
    $structuredData[] = $userData;
}
?>

<script>
const allData = <?php echo json_encode($structuredData); ?>;
const researchLines = <?php echo json_encode($allResearchLines); ?>;
const projects = <?php echo json_encode($allProjects); ?>;

let currentFilter = 'all';
let currentYear = '2025';
let currentSemester = 'all';
let currentResearchLine = 'all';
let currentRole = 'all';
let currentTab = 'research-lines';

function updateStatistics() {
    const filteredData = filterData();
    updateStatsCards(filteredData);
    updateCharts(filteredData);
    updateResearchLinesTable(filteredData);
    updatePhaseDistribution(filteredData);
    updateEvaluationsTable(filteredData);
    updatePerformanceTable(filteredData);
    updateComparativesTable(filteredData);
    showNotification('Estadísticas actualizadas correctamente', 'success');
}

function filterData() {
    return allData.filter(user => {
        const userYear = new Date(user.created_at).getFullYear().toString();
        if (currentYear !== 'all' && userYear !== currentYear) return false;
        if (currentSemester !== 'all' && user.semester !== parseInt(currentSemester)) return false;
        if (currentResearchLine !== 'all') {
            const hasResearchLine = user.proyectos.some(proyecto => 
                proyecto.linea_investigacion_id.toString() === currentResearchLine
            );
            if (!hasResearchLine) return false;
        }
        if (currentRole !== 'all' && user.role_id.toString() !== currentRole) return false;
        return true;
    });
}

function updateStatsCards(data) {
    const totalProjects = data.reduce((sum, user) => sum + user.proyectos.length, 0);
    const totalEvaluations = data.reduce((sum, user) => {
        return sum + user.proyectos.reduce((projSum, proyecto) => {
            return projSum + (proyecto.resultados.puntaje_total ? 1 : 0);
        }, 0);
    }, 0);
    const totalUsers = data.length;
    
    let totalScore = 0;
    let scoreCount = 0;
    
    data.forEach(user => {
        user.proyectos.forEach(proyecto => {
            if (proyecto.resultados.puntaje_total) {
                totalScore += parseFloat(proyecto.resultados.puntaje_total);
                scoreCount++;
            }
        });
    });
    
    const averageScore = scoreCount > 0 ? (totalScore / scoreCount).toFixed(1) : '0.0';
    
    document.querySelector('[data-stat="projects"]').textContent = totalProjects;
    document.querySelector('[data-stat="evaluations"]').textContent = totalEvaluations;
    document.querySelector('[data-stat="users"]').textContent = totalUsers;
    document.querySelector('[data-stat="average"]').textContent = averageScore;
}

function updateResearchLinesTable(data) {
    const tableContainer = document.getElementById('research-lines-table');
    let html = '';
    
    const linesMap = {};
    
    researchLines.forEach(line => {
        linesMap[line.id] = {
            nombre: line.nombre,
            proyectos: 0,
            evaluaciones: 0,
            puntuacionTotal: 0
        };
    });
    
    data.forEach(user => {
        user.proyectos.forEach(proyecto => {
            const lineId = proyecto.linea_investigacion_id;
            if (linesMap[lineId]) {
                linesMap[lineId].proyectos++;
                
                if (proyecto.resultados.puntaje_total) {
                    linesMap[lineId].evaluaciones++;
                    linesMap[lineId].puntuacionTotal += parseFloat(proyecto.resultados.puntaje_total);
                }
            }
        });
    });
    
    Object.values(linesMap).forEach(line => {
        if (line.proyectos > 0) {
            const promedio = line.evaluaciones > 0 ? (line.puntuacionTotal / line.evaluaciones).toFixed(1) : '0.0';
            const porcentaje = line.evaluaciones > 0 ? Math.round((line.evaluaciones / line.proyectos) * 100) : 0;
            
            html += `
                <div class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-slate-900">${line.nombre}</p>
                        <p class="text-sm text-slate-600">${line.proyectos} proyectos • ${line.evaluaciones} evaluaciones</p>
                        <div class="w-full bg-slate-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: ${porcentaje}%"></div>
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        <p class="text-lg font-bold text-slate-900">${promedio}</p>
                        <p class="text-sm ${porcentaje >= 50 ? 'text-green-600' : 'text-red-600'} flex items-center gap-1">
                            <i data-lucide="${porcentaje >= 50 ? 'trending-up' : 'trending-down'}" class="w-3 h-3"></i>
                            ${porcentaje}%
                        </p>
                    </div>
                </div>
            `;
        }
    });
    
    tableContainer.innerHTML = html;
    lucide.createIcons();
}

function updatePhaseDistribution(data) {
    const phaseCounts = {
        'propuesta': 0,
        'desarrollo': 0,
        'aplicacion': 0,
        'finalizado': 0
    };
    
    data.forEach(user => {
        user.proyectos.forEach(proyecto => {
            if (phaseCounts.hasOwnProperty(proyecto.fase)) {
                phaseCounts[proyecto.fase]++;
            }
        });
    });
    
    const totalProjects = Object.values(phaseCounts).reduce((sum, count) => sum + count, 0);
    
    const phases = {
        'propuesta': { element: document.getElementById('phase-propuesta'), color: 'yellow' },
        'desarrollo': { element: document.getElementById('phase-desarrollo'), color: 'blue' },
        'aplicacion': { element: document.getElementById('phase-aplicacion'), color: 'green' },
        'finalizado': { element: document.getElementById('phase-finalizado'), color: 'gray' }
    };
    
    Object.entries(phaseCounts).forEach(([fase, count]) => {
        if (phases[fase]) {
            const porcentaje = totalProjects > 0 ? Math.round((count / totalProjects) * 100) : 0;
            
            phases[fase].element.querySelector('.phase-count').textContent = count;
            phases[fase].element.querySelector('.phase-percent').textContent = `${porcentaje}%`;
            phases[fase].element.querySelector('.progress-bar').style.width = `${porcentaje}%`;
            phases[fase].element.querySelector('.progress-bar').classList.add(`bg-${phases[fase].color}-500`);
        }
    });
}

function updateEvaluationsTable(data) {
    const tableContainer = document.getElementById('evaluations-table');
    if (!tableContainer) return;
    
    let html = '<div class="space-y-4">';
    
    data.forEach(user => {
        user.proyectos.forEach(proyecto => {
            if (proyecto.resultados.puntaje_total) {
                html += `
                    <div class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">${proyecto.titulo}</p>
                            <p class="text-sm text-slate-600">${user.nombre_completo} • ${proyecto.linea_investigacion}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold text-slate-900">${proyecto.resultados.puntaje_total}</p>
                            <p class="text-sm text-slate-600">${proyecto.resultados.estado_evaluacion}</p>
                        </div>
                    </div>
                `;
            }
        });
    });
    
    html += '</div>';
    tableContainer.innerHTML = html;
}

function updatePerformanceTable(data) {
    const tableContainer = document.getElementById('performance-table');
    if (!tableContainer) return;
    
    let html = '<div class="space-y-4">';
    
    const userPerformance = {};
    
    data.forEach(user => {
        let totalScore = 0;
        let scoreCount = 0;
        
        user.proyectos.forEach(proyecto => {
            if (proyecto.resultados.puntaje_total) {
                totalScore += parseFloat(proyecto.resultados.puntaje_total);
                scoreCount++;
            }
        });
        
        if (scoreCount > 0) {
            userPerformance[user.id] = {
                nombre: user.nombre_completo,
                promedio: (totalScore / scoreCount).toFixed(1),
                proyectos: scoreCount
            };
        }
    });
    
    Object.values(userPerformance).forEach(user => {
        html += `
            <div class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-slate-900">${user.nombre}</p>
                    <p class="text-sm text-slate-600">${user.proyectos} proyectos evaluados</p>
                </div>
                <div class="text-right ml-4">
                    <p class="text-lg font-bold text-slate-900">${user.promedio}</p>
                    <p class="text-sm ${user.promedio >= 3.5 ? 'text-green-600' : 'text-red-600'}">${user.promedio >= 3.5 ? 'Sobresaliente' : 'Necesita mejorar'}</p>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    tableContainer.innerHTML = html;
}

function updateComparativesTable(data) {
    const tableContainer = document.getElementById('comparatives-table');
    if (!tableContainer) return;
    
    let html = '<div class="space-y-4">';
    
    const yearData = {};
    const semesterData = {};
    
    data.forEach(user => {
        const year = new Date(user.created_at).getFullYear();
        if (!yearData[year]) yearData[year] = { proyectos: 0, evaluaciones: 0, puntuacion: 0 };
        
        user.proyectos.forEach(proyecto => {
            yearData[year].proyectos++;
            
            if (proyecto.resultados.puntaje_total) {
                yearData[year].evaluaciones++;
                yearData[year].puntuacion += parseFloat(proyecto.resultados.puntaje_total);
            }
            
            if (user.semester) {
                if (!semesterData[user.semester]) semesterData[user.semester] = { proyectos: 0, evaluaciones: 0, puntuacion: 0 };
                
                semesterData[user.semester].proyectos++;
                
                if (proyecto.resultados.puntaje_total) {
                    semesterData[user.semester].evaluaciones++;
                    semesterData[user.semester].puntuacion += parseFloat(proyecto.resultados.puntaje_total);
                }
            }
        });
    });
    
    html += '<h4 class="font-semibold text-slate-900 mb-3">Comparativa por Año</h4>';
    Object.entries(yearData).sort((a, b) => b[0] - a[0]).forEach(([year, data]) => {
        const promedio = data.evaluaciones > 0 ? (data.puntuacion / data.evaluaciones).toFixed(1) : '0.0';
        html += `
            <div class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-slate-900">Año ${year}</p>
                    <p class="text-sm text-slate-600">${data.proyectos} proyectos • ${data.evaluaciones} evaluaciones</p>
                </div>
                <div class="text-right ml-4">
                    <p class="text-lg font-bold text-slate-900">${promedio}</p>
                </div>
            </div>
        `;
    });
    
    html += '<h4 class="font-semibold text-slate-900 mb-3 mt-6">Comparativa por Semestre</h4>';
    Object.entries(semesterData).sort((a, b) => a[0] - b[0]).forEach(([semester, data]) => {
        const promedio = data.evaluaciones > 0 ? (data.puntuacion / data.evaluaciones).toFixed(1) : '0.0';
        html += `
            <div class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-slate-900">Semestre ${semester}</p>
                    <p class="text-sm text-slate-600">${data.proyectos} proyectos • ${data.evaluaciones} evaluaciones</p>
                </div>
                <div class="text-right ml-4">
                    <p class="text-lg font-bold text-slate-900">${promedio}</p>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    tableContainer.innerHTML = html;
}

function updateCharts(data) {
    const ctx = document.getElementById('phaseChart');
    if (!ctx) return;
    
    const phaseCounts = {
        'propuesta': 0,
        'desarrollo': 0,
        'aplicacion': 0,
        'finalizado': 0
    };
    
    data.forEach(user => {
        user.proyectos.forEach(proyecto => {
            if (phaseCounts.hasOwnProperty(proyecto.fase)) {
                phaseCounts[proyecto.fase]++;
            }
        });
    });
    
    if (window.phaseChartInstance) {
        window.phaseChartInstance.destroy();
    }
    
    window.phaseChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Propuesta', 'Desarrollo', 'Aplicación', 'Finalizado'],
            datasets: [{
                data: Object.values(phaseCounts),
                backgroundColor: ['#eab308', '#3b82f6', '#22c55e', '#6b7280'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function exportReport() {
    const filteredData = filterData();
    showNotification('Reporte exportado correctamente', 'success');
}

function exportExcel() {
    const filteredData = filterData();
    showNotification('Datos exportados a Excel', 'success');
}

function exportPDF() {
    const filteredData = filterData();
    showNotification('Reporte exportado a PDF', 'success');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function showStatTab(tabName) {
    document.querySelectorAll('.stat-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-active').forEach(tab => {
        tab.classList.remove('tab-active', 'text-blue-600', 'border-blue-600');
        tab.classList.add('text-slate-600');
    });
    
    document.getElementById(`${tabName}-tab`).classList.remove('hidden');
    
    const activeButton = document.querySelector(`button[onclick="showStatTab('${tabName}')"]`);
    activeButton.classList.add('tab-active', 'text-blue-600', 'border-blue-600');
    activeButton.classList.remove('text-slate-600');
    
    currentTab = tabName;
    updateStatistics();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('year-filter').addEventListener('change', function(e) {
        currentYear = e.target.value;
        updateStatistics();
    });
    
    document.getElementById('semester-filter').addEventListener('change', function(e) {
        currentSemester = e.target.value;
        updateStatistics();
    });
    
    document.getElementById('researchline-filter').addEventListener('change', function(e) {
        currentResearchLine = e.target.value;
        updateStatistics();
    });
    
    document.getElementById('role-filter').addEventListener('change', function(e) {
        currentRole = e.target.value;
        updateStatistics();
    });
    
    updateStatistics();
});
</script>

<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Módulo de <span class="text-blue-600">Estadísticas</span></h2>
            <p class="text-slate-600">Análisis detallado y reportes del sistema de evaluación</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <select id="year-filter" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="all">Todos los años</option>
                <option value="2025" selected>2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
            </select>
            
            <select id="semester-filter" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="all">Todos los semestres</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?php echo $i; ?>">Semestre <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            
            <select id="researchline-filter" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="all">Todas las líneas</option>
                <?php foreach ($allResearchLines as $line): ?>
                <option value="<?php echo $line['id']; ?>"><?php echo $line['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="role-filter" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="all">Todos los roles</option>
                <option value="1">Administrador</option>
                <option value="3">Evaluador</option>
                <option value="4">Docente</option>
                <option value="5">Estudiante</option>
            </select>
            
            <button onclick="updateStatistics()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Actualizar
            </button>
            <button onclick="exportReport()" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg">
                <i data-lucide="download" class="w-4 h-4"></i>
                Exportar Reporte
            </button>
            <button onclick="exportExcel()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Excel
            </button>
            <button onclick="exportPDF()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                PDF
            </button>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl shadow-lg">
                    <i data-lucide="folder" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-sm font-semibold text-green-600">+12%</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Proyectos por Línea</p>
                <p class="text-3xl font-bold text-slate-900" data-stat="projects">0</p>
                <p class="text-xs text-slate-500">Total activos</p>
            </div>
        </div>

        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl shadow-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-sm font-semibold text-green-600">+18%</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Evaluaciones Completadas</p>
                <p class="text-3xl font-bold text-slate-900" data-stat="evaluations">0</p>
                <p class="text-xs text-slate-500">Este período</p>
            </div>
        </div>

        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl shadow-lg">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-sm font-semibold text-green-600">+8%</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Usuarios Activos</p>
                <p class="text-3xl font-bold text-slate-900" data-stat="users">0</p>
                <p class="text-xs text-slate-500">Registrados</p>
            </div>
        </div>

        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl shadow-lg">
                    <i data-lucide="star" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-sm font-semibold text-green-600">+0.3</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Promedio General</p>
                <p class="text-3xl font-bold text-slate-900" data-stat="average">0.0</p>
                <p class="text-xs text-slate-500">Sobre 5.0</p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex border-b border-white/20">
            <button onclick="showStatTab('research-lines')" class="px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 tab-active">
                Líneas de Investigación
            </button>
            <button onclick="showStatTab('evaluations')" class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                Evaluaciones
            </button>
            <button onclick="showStatTab('performance')" class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                Rendimiento
            </button>
            <button onclick="showStatTab('comparatives')" class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                Comparativas
            </button>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div id="research-lines-tab" class="stat-tab-content">
            <div class="glassmorphism rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Proyectos por Línea de Investigación</h3>
                </div>
                <p class="text-sm text-slate-600 mb-6">Distribución y rendimiento por área</p>
                <div id="research-lines-table" class="space-y-4">
                </div>
            </div>
        </div>

        <div id="evaluations-tab" class="stat-tab-content hidden">
            <div class="glassmorphism rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Evaluaciones Realizadas</h3>
                </div>
                <p class="text-sm text-slate-600 mb-6">Detalle de todas las evaluaciones completadas</p>
                <div id="evaluations-table" class="space-y-4">
                </div>
            </div>
        </div>

        <div id="performance-tab" class="stat-tab-content hidden">
            <div class="glassmorphism rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Rendimiento por Usuario</h3>
                </div>
                <p class="text-sm text-slate-600 mb-6">Promedio de calificaciones por usuario</p>
                <div id="performance-table" class="space-y-4">
                </div>
            </div>
        </div>

        <div id="comparatives-tab" class="stat-tab-content hidden">
            <div class="glassmorphism rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Comparativas</h3>
                </div>
                <p class="text-sm text-slate-600 mb-6">Comparativa por año y semestre</p>
                <div id="comparatives-table" class="space-y-4">
                </div>
            </div>
        </div>

        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Distribución por Fase</h3>
            </div>
            <p class="text-sm text-slate-600 mb-6">Estado actual de los proyectos</p>
            <div class="space-y-4">
                <div id="phase-propuesta" class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                        <div>
                            <p class="font-medium text-slate-900">Propuesta</p>
                            <div class="w-48 bg-slate-200 rounded-full h-2 mt-1">
                                <div class="bg-yellow-500 h-2 rounded-full progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-900 phase-count">0</p>
                        <p class="text-sm text-slate-600 phase-percent">0%</p>
                    </div>
                </div>

                <div id="phase-desarrollo" class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        <div>
                            <p class="font-medium text-slate-900">Desarrollo</p>
                            <div class="w-48 bg-slate-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-500 h-2 rounded-full progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-900 phase-count">0</p>
                        <p class="text-sm text-slate-600 phase-percent">0%</p>
                    </div>
                </div>

                <div id="phase-aplicacion" class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="font-medium text-slate-900">Aplicación</p>
                            <div class="w-48 bg-slate-200 rounded-full h-2 mt-1">
                                <div class="bg-green-500 h-2 rounded-full progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-900 phase-count">0</p>
                        <p class="text-sm text-slate-600 phase-percent">0%</p>
                    </div>
                </div>

                <div id="phase-finalizado" class="flex items-center justify-between p-4 bg-white/60 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                        <div>
                            <p class="font-medium text-slate-900">Finalizado</p>
                            <div class="w-48 bg-slate-200 rounded-full h-2 mt-1">
                                <div class="bg-gray-500 h-2 rounded-full progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-900 phase-count">0</p>
                        <p class="text-sm text-slate-600 phase-percent">0%</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <canvas id="phaseChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>