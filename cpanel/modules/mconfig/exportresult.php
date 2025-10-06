<div class="container">
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .export-stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .stats-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .format-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .format-card {
            border: 2px solid #eee;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 180px;
        }

        .format-card.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .stats-preview {
            border: 1px solid #eee;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            max-height: 400px;
            overflow-y: auto;
        }

        .metric-selector {
            columns: 2;
            margin: 15px 0;
        }

        .preview-chart {
            height: 300px;
            margin: 20px 0;
        }
    </style>

    <div class="export-stats-header">
        <h2>Exportar Estadísticas</h2>
        <div class="selected-count" id="selectedStats">
            0 métricas seleccionadas
        </div>
    </div>

    <div class="stats-filters">
        <div>
            <label>Rango de Fechas:</label>
            <div class="date-range">
                <input type="date" class="form-input" id="statsStartDate">
                <input type="date" class="form-input" id="statsEndDate">
            </div>
        </div>
        
        <div>
            <label>Tipo de Estadística:</label>
            <select class="form-input" id="statsType">
                <option value="general">Generales</option>
                <option value="projects">Proyectos</option>
                <option value="users">Usuarios</option>
                <option value="financial">Financieras</option>
            </select>
        </div>
        
        <div>
            <label>Nivel de Detalle:</label>
            <select class="form-input" id="detailLevel">
                <option value="summary">Resumen</option>
                <option value="detailed">Detallado</option>
                <option value="complete">Completo</option>
            </select>
        </div>
    </div>

    <div class="metric-selector">
        <h4>Seleccionar Métricas:</h4>
        <label><input type="checkbox" class="metric-checkbox"> Proyectos Activos</label>
        <label><input type="checkbox" class="metric-checkbox"> Tareas Completadas</label>
        <label><input type="checkbox" class="metric-checkbox"> Horas Invertidas</label>
        <label><input type="checkbox" class="metric-checkbox"> Presupuesto vs Gastos</label>
        <label><input type="checkbox" class="metric-checkbox"> Rendimiento Equipos</label>
        <label><input type="checkbox" class="metric-checkbox"> Tasa de Éxito</label>
        <label><input type="checkbox" class="metric-checkbox"> Problemas Reportados</label>
        <label><input type="checkbox" class="metric-checkbox"> Uso de Recursos</label>
    </div>

    <div class="format-options">
        <div class="format-card" onclick="selectFormat('pdf')">
            <h4>📄 PDF</h4>
            <p>Reporte formateado para impresión</p>
            <small>Incluye gráficos y tablas</small>
        </div>
        
        <div class="format-card" onclick="selectFormat('excel')">
            <h4>📊 Excel</h4>
            <p>Datos crudos para análisis</p>
            <small>Formatos XLSX y CSV</small>
        </div>
        
        <div class="format-card" onclick="selectFormat('image')">
            <h4>🖼️ Imagen</h4>
            <p>Exportar visualizaciones</p>
            <small>PNG, JPG y SVG</small>
        </div>
        
        <div class="format-card" onclick="selectFormat("json")">
            <h4>📦 JSON</h4>
            <p>Datos estructurados</p>
            <small>Para integraciones</small>
        </div>
    </div>

    <div class="stats-preview">
        <h4>Vista Previa</h4>
        <div class="preview-chart">
            <canvas id="previewChart"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th>Valor</th>
                    <th>Variación</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Proyectos Activos</td>
                    <td>45</td>
                    <td>+12%</td>
                </tr>
                <!-- Más filas de preview -->
            </tbody>
        </table>
    </div>

    <div class="export-actions">
        <button class="export-button" onclick="handleExport()">
            <i class="material-icons">download</i>
            Generar Exportación
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let selectedFormat = '';
    const metrics = [];

    function selectFormat(format) {
        selectedFormat = format;
        document.querySelectorAll('.format-card').forEach(card => {
            card.classList.remove('selected');
        });
        event.target.closest('.format-card').classList.add('selected');
    }

    function updateSelectedMetrics() {
        const selected = document.querySelectorAll('.metric-checkbox:checked').length;
        document.getElementById('selectedStats').textContent = 
            `${selected} métrica${selected !== 1 ? 's' : ''} seleccionada${selected !== 1 ? 's' : ''}`;
    }

    document.querySelectorAll('.metric-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedMetrics);
    });

    // Gráfico de preview
    new Chart(document.getElementById('previewChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Proyectos Completados',
                data: [12, 19, 3, 5, 2],
                backgroundColor: '#007bff'
            }]
        }
    });

    function handleExport() {
        if (!selectedFormat) {
            alert('Selecciona un formato de exportación');
            return;
        }
        
        // Simular exportación
        const exportData = {
            format: selectedFormat,
            metrics: Array.from(document.querySelectorAll('.metric-checkbox:checked')).map(cb => cb.nextSibling.textContent.trim()),
            dateRange: {
                start: document.getElementById('statsStartDate').value,
                end: document.getElementById('statsEndDate').value
            }
        };

        console.log('Datos para exportar:', exportData);
        alert(`Generando archivo ${selectedFormat.toUpperCase()}...`);
    }
</script>