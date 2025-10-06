<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'User', 'action' => 'manage']])) {
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
        
        .export-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .export-option {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .export-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .export-button:hover {
            background-color: #218838;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .date-range {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .data-preview {
            max-height: 300px;
            overflow-y: auto;
            margin: 20px 0;
        }
        
        .format-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            margin: 10px 0;
        }
        
        .export-status {
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .selected-count {
            margin: 10px 0;
            color: #666;
        }
    </style>

    <div class="export-controls">
        <div class="export-option">
            <h4>Formato de Exportación</h4>
            <select class="format-select" id="exportFormat">
                <option value="csv">CSV (Excel)</option>
                <option value="xlsx">XLSX (Excel avanzado)</option>
                <option value="pdf">PDF (Informe)</option>
            </select>
        </div>

        <div class="export-option">
            <h4>Filtros</h4>
            <div class="filter-group">
                <label>Rango de fechas:</label>
                <div class="date-range">
                    <input type="date" class="filter-select" id="startDate">
                    <input type="date" class="filter-select" id="endDate">
                </div>
            </div>
            <div class="filter-group">
                <label>Tipo de usuario:</label>
                <select class="filter-select" id="userType">
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>
        </div>
    </div>

    <div class="data-preview">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Último acceso</th>
                </tr>
            </thead>
            <tbody id="previewTable">
                <tr>
                    <td><input type="checkbox" class="user-checkbox"></td>
                    <td>001</td>
                    <td>juan_perez</td>
                    <td>juan@example.com</td>
                    <td>2024-03-15</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="user-checkbox"></td>
                    <td>002</td>
                    <td>maria_gomez</td>
                    <td>maria@example.com</td>
                    <td>2024-03-14</td>
                </tr>
                <!-- Más filas de ejemplo -->
            </tbody>
        </table>
    </div>

    <div class="selected-count" id="selectedCount">
        0 usuarios seleccionados
    </div>

    <div class="export-status" id="exportStatus">
        <button class="export-button" onclick="startExport()">
            <i class="material-icons">download</i>
            Exportar selección
        </button>
        <button class="export-button" onclick="startExport(true)">
            <i class="material-icons">download_all</i>
            Exportar todos
        </button>
    </div>

    <div class="log-section" id="exportLog"></div>
</div>

<script>
    // Manejo de selección
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionCount);
    });
    
    document.getElementById('selectAll').addEventListener('change', function(e) {
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        updateSelectionCount();
    });

    function updateSelectionCount() {
        const selected = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = 
            `${selected} usuario${selected !== 1 ? 's' : ''} seleccionado${selected !== 1 ? 's' : ''}`;
    }

    // Simulación de exportación
    function startExport(exportAll = false) {
        const statusDiv = document.getElementById('exportStatus');
        const logSection = document.getElementById('exportLog');
        
        statusDiv.className = 'export-status status-processing';
        statusDiv.innerHTML = '⏳ Procesando exportación...';
        
        // Simulación de proceso
        setTimeout(() => {
            statusDiv.className = 'export-status status-completed';
            statusDiv.innerHTML = '✅ Exportación completada';
            
            logSection.innerHTML = `
                <div class="log-entry">📄 Archivo generado: usuarios_export_${Date.now()}.${document.getElementById('exportFormat').value}</div>
                <div class="log-entry">📦 Total registros exportados: ${exportAll ? '150' : document.querySelectorAll('.user-checkbox:checked').length}</div>
                <div class="log-entry">⏱️ Tiempo de proceso: 2.4 segundos</div>
            `;
        }, 2000);
    }
</script>