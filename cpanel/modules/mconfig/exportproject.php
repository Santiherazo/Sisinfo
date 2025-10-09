<?php 
if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Project', 'action' => 'export']])) {
        die('No tienes permisos para acceder a este módulo.');
    }
?>
<div class="container">
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .export-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .priority-high {
            background-color: #dc3545;
        }
        
        .priority-medium {
            background-color: #ffc107;
        }
        
        .priority-low {
            background-color: #28a745;
        }
        
        .budget-display {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 3px 6px;
            border-radius: 3px;
        }
        
        .timeline-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .export-presets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .preset-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .preset-card:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
    </style>

    <div class="export-header">
        <div>
            <h3>Exportar Proyectos</h3>
            <div class="selected-count" id="projectSelectedCount">
                0 proyectos seleccionados
            </div>
        </div>
        <div class="export-actions">
            <button class="export-button" onclick="startProjectExport()">
                <i class="material-icons">download_for_offline</i>
                Exportar selección
            </button>
            <button class="export-button" onclick="startProjectExport(true)">
                <i class="material-icons">database</i>
                Exportar completo
            </button>
        </div>
    </div>

    <div class="export-controls">
        <div class="export-option">
            <h4>Formato de Salida</h4>
            <select class="format-select" id="projectExportFormat">
                <option value="csv">CSV Estándar</option>
                <option value="xlsx">Excel (XLSX)</option>
                <option value="pdf">Informe PDF</option>
                <option value="xml">XML Estructurado</option>
            </select>
        </div>

        <div class="export-option">
            <h4>Filtros Avanzados</h4>
            <div class="filter-group">
                <label>Estado del proyecto:</label>
                <select class="filter-select" id="projectStatus">
                    <option value="all">Todos</option>
                    <option value="planning">Planificación</option>
                    <option value="progress">En progreso</option>
                    <option value="completed">Completado</option>
                    <option value="paused">Pausado</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Rango de fechas:</label>
                <div class="date-range">
                    <input type="date" class="filter-select" id="projectStartDate">
                    <input type="date" class="filter-select" id="projectEndDate">
                </div>
            </div>
        </div>
    </div>

    <div class="export-presets">
        <div class="preset-card" onclick="applyPreset('current')">
            <h5>📌 Proyectos Actuales</h5>
            <p>Proyectos activos este trimestre</p>
        </div>
        <div class="preset-card" onclick="applyPreset('financial')">
            <h5>💰 Reporte Financiero</h5>
            <p>Incluye datos presupuestarios y costos</p>
        </div>
        <div class="preset-card" onclick="applyPreset('complete')">
            <h5>📦 Exportación Completa</h5>
            <p>Todos los campos disponibles</p>
        </div>
    </div>

    <div class="data-preview">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllProjects"></th>
                    <th>ID Proyecto</th>
                    <th>Nombre</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Presupuesto</th>
                    <th>Progreso</th>
                    <th>Fecha Entrega</th>
                </tr>
            </thead>
            <tbody id="projectExportPreview">
                <tr>
                    <td><input type="checkbox" class="project-checkbox"></td>
                    <td>P-1024</td>
                    <td>Implementación AI</td>
                    <td><span class="priority-indicator priority-high"></span>Alta</td>
                    <td><span class="status-badge status-progress">En progreso</span></td>
                    <td><span class="budget-display">$250,000</span></td>
                    <td>
                        <div class="progress-bar" style="width: 120px;">
                            <div class="progress" style="width: 65%;"></div>
                        </div>
                    </td>
                    <td>2024-11-30</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="project-checkbox"></td>
                    <td>P-1025</td>
                    <td>Optimización Logística</td>
                    <td><span class="priority-indicator priority-medium"></span>Media</td>
                    <td><span class="status-badge status-planning">Planificación</span></td>
                    <td><span class="budget-display">$89,500</span></td>
                    <td>
                        <div class="progress-bar" style="width: 120px;">
                            <div class="progress" style="width: 15%;"></div>
                        </div>
                    </td>
                    <td>2025-02-15</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="export-options">
        <h4>Opciones Adicionales:</h4>
        <div class="option-checkbox">
            <input type="checkbox" id="includeTasks"> Incluir tareas asociadas
        </div>
        <div class="option-checkbox">
            <input type="checkbox" id="includeComments"> Incluir comentarios
        </div>
        <div class="option-checkbox">
            <input type="checkbox" id="includeAttachments"> Incluir archivos adjuntos
        </div>
    </div>

    <div class="log-section" id="projectExportLog">
        <div class="log-entry">Seleccione los proyectos a exportar</div>
    </div>
</div>

<script>
    // Manejo de selección de proyectos
    document.querySelectorAll('.project-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateProjectSelection);
    });

    document.getElementById('selectAllProjects').addEventListener('change', function(e) {
        document.querySelectorAll('.project-checkbox').forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        updateProjectSelection();
    });

    function updateProjectSelection() {
        const selected = document.querySelectorAll('.project-checkbox:checked').length;
        document.getElementById('projectSelectedCount').textContent = 
            `${selected} proyecto${selected !== 1 ? 's' : ''} seleccionado${selected !== 1 ? 's' : ''}`;
    }

    // Presets configurables
    function applyPreset(presetType) {
        const formatSelect = document.getElementById('projectExportFormat');
        const statusSelect = document.getElementById('projectStatus');
        
        switch(presetType) {
            case 'current':
                formatSelect.value = 'xlsx';
                statusSelect.value = 'progress';
                break;
            case 'financial':
                formatSelect.value = 'pdf';
                document.getElementById('includeTasks').checked = false;
                break;
            case 'complete':
                formatSelect.value = 'xml';
                document.getElementById('includeTasks').checked = true;
                document.getElementById('includeComments').checked = true;
                break;
        }
    }

    // Simulación de exportación
    function startProjectExport(fullExport = false) {
        const logSection = document.getElementById('projectExportLog');
        const selectedCount = fullExport ? 'todos' : document.querySelectorAll('.project-checkbox:checked').length;
        
        logSection.innerHTML = `
            <div class="log-entry">⏳ Iniciando exportación de ${selectedCount} proyectos...
                <div class="progress-bar">
                    <div class="progress" id="projectExportProgress"></div>
                </div>
            </div>
        `;

        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            document.getElementById('projectExportProgress').style.width = `${progress}%`;
            
            if (progress >= 100) {
                clearInterval(interval);
                logSection.innerHTML += `
                    <div class="log-entry log-success">✅ Exportación completada con éxito</div>
                    <div class="log-entry">📦 Archivo generado: proyectos_${Date.now()}.${document.getElementById('projectExportFormat').value}</div>
                    <div class="log-entry">⏱ Tiempo de proceso: 3.2 segundos</div>
                `;
            }
        }, 300);
    }
</script>