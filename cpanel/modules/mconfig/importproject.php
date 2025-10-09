<?php 
if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Project', 'action' => 'import']])) {
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
        
        .upload-section {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }
        
        .project-preview {
            max-height: 400px;
            overflow-y: auto;
            margin: 20px 0;
        }
        
        .mapping-section {
            border: 1px solid #eee;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .column-mapping {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            display: inline-block;
        }
        
        .status-planning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
    </style>

    <div class="upload-section" id="projectDropZone">
        <i class="material-icons" style="font-size: 48px; color: #666;">folder_open</i>
        <h3>Importar proyectos desde archivo</h3>
        <button class="import-button" onclick="document.getElementById('projectFileInput').click()">
            Seleccionar archivo
        </button>
        <input type="file" class="file-input" id="projectFileInput" accept=".csv, .xlsx, .xml">
        <div class="format-list">Formatos soportados: CSV, XLSX, XML (Max. 50MB)</div>
    </div>

    <div class="import-options">
        <h4>Opciones de importación:</h4>
        <label class="option-checkbox">
            <input type="checkbox" id="updateProjects"> Actualizar proyectos existentes
        </label>
        <br>
        <label class="option-checkbox">
            <input type="checkbox" id="importTasks"> Incluir tareas relacionadas
        </label>
        <br>
        <label class="option-checkbox">
            <input type="checkbox" id="notifyTeam"> Notificar al equipo por email
        </label>
    </div>

    <div class="mapping-section">
        <h4>Mapeo de columnas:</h4>
        <div class="column-mapping">
            <div>
                <label>Columna ID Proyecto:</label>
                <select class="filter-select">
                    <option value="auto">Detección automática</option>
                    <option value="project_id">project_id</option>
                    <option value="id">id</option>
                </select>
            </div>
            <div>
                <label>Columna Fecha Inicio:</label>
                <select class="filter-select">
                    <option value="auto">Detección automática</option>
                    <option value="start_date">start_date</option>
                    <option value="fecha_inicio">fecha_inicio</option>
                </select>
            </div>
        </div>
    </div>

    <div class="project-preview">
        <table>
            <thead>
                <tr>
                    <th>ID Proyecto</th>
                    <th>Nombre</th>
                    <th>Gerente</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Presupuesto</th>
                </tr>
            </thead>
            <tbody id="projectPreview">
                <tr>
                    <td>P-1001</td>
                    <td>Implementación CRM</td>
                    <td>Ana Rodríguez</td>
                    <td><span class="status-badge status-planning">Planificación</span></td>
                    <td>2024-04-01</td>
                    <td>2024-12-15</td>
                    <td>$125,000</td>
                </tr>
                <tr>
                    <td>P-1002</td>
                    <td>Migración a la nube</td>
                    <td>Carlos Mendoza</td>
                    <td><span class="status-badge status-progress">En progreso</span></td>
                    <td>2024-03-15</td>
                    <td>2024-09-30</td>
                    <td>$89,500</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="progress-bar">
        <div class="progress" id="projectProgress"></div>
    </div>

    <div class="log-section" id="projectLog">
        <div class="log-entry">Seleccione un archivo para comenzar</div>
    </div>
</div>

<script>
    // Manejo específico para proyectos
    const projectDropZone = document.getElementById('projectDropZone');
    
    projectDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        projectDropZone.classList.add('dragover');
    });

    projectDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        projectDropZone.classList.remove('dragover');
        handleProjectFiles(e.dataTransfer.files);
    });

    document.getElementById('projectFileInput').addEventListener('change', function(e) {
        handleProjectFiles(e.target.files);
    });

    function handleProjectFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('projectLog').innerHTML = 
                `<div class="log-entry">📁 Archivo cargado: ${file.name}</div>`;
            
            // Simulación de análisis de archivo
            setTimeout(() => {
                document.getElementById('projectLog').innerHTML += `
                    <div class="log-entry">🔍 Analizando estructura del archivo...</div>
                    <div class="log-entry log-success">✅ 15 proyectos detectados</div>
                    <div class="log-entry log-success">✅ 3 tareas por proyecto en promedio</div>
                `;
                document.querySelector('.project-preview').style.display = 'block';
            }, 1500);
        }
    }
</script>