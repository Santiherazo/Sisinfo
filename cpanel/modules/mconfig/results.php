<div class="container">
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .report-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .chart-container {
            margin: 25px 0;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 6px;
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .data-table {
            max-height: 400px;
            overflow-y: auto;
        }

        .metric-card {
            padding: 20px;
            border-radius: 6px;
            color: white;
            text-align: center;
        }

        .metric-total { background-color: #007bff; }
        .metric-success { background-color: #28a745; }
        .metric-warning { background-color: #ffc107; }
        .metric-danger { background-color: #dc3545; }

        .export-options {
            margin-top: 25px;
            text-align: right;
        }
    </style>

    <div class="report-header">
        <h2>Generador de Reportes</h2>
        <div class="export-options">
            <button class="export-button" onclick="generatePDF()">
                <i class="material-icons">picture_as_pdf</i>
                Exportar PDF
            </button>
            <button class="export-button" onclick="exportExcel()">
                <i class="material-icons">description</i>
                Exportar Excel
            </button>
        </div>
    </div>

    <div class="report-filters">
        <div>
            <label>Rango de Fechas:</label>
            <div class="date-range">
                <input type="date" class="form-input" id="startDate">
                <input type="date" class="form-input" id="endDate">
            </div>
        </div>
        
        <div>
            <label>Tipo de Reporte:</label>
            <select class="form-input" id="reportType">
                <option value="projects">Proyectos</option>
                <option value="tasks">Tareas</option>
                <option value="financial">Financiero</option>
                <option value="resources">Recursos</option>
            </select>
        </div>
        
        <div>
            <label>Filtro Adicional:</label>
            <select class="form-input" id="additionalFilter">
                <option value="all">Todos</option>
                <option value="completed">Completados</option>
                <option value="active">Activos</option>
                <option value="delayed">Retrasados</option>
            </select>
        </div>
    </div>

    <div class="report-grid">
        <div class="metric-card metric-total">
            <h3>45</h3>
            <p>Proyectos Activos</p>
        </div>
        
        <div class="metric-card metric-success">
            <h3>82%</h3>
            <p>Tasa de Éxito</p>
        </div>
        
        <div class="metric-card metric-warning">
            <h3>15</h3>
            <p>Proyectos en Riesgo</p>
        </div>
        
        <div class="metric-card metric-danger">
            <h3>5</h3>
            <p>Proyectos Retrasados</p>
        </div>
    </div>

    <div class="chart-container">
        <h4>Progreso de Proyectos</h4>
        <canvas id="projectsChart"></canvas>
    </div>

    <div class="chart-container">
        <h4>Distribución de Recursos</h4>
        <canvas id="resourcesChart"></canvas>
    </div>

    <div class="data-table">
        <h4>Detalle de Proyectos</h4>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Presupuesto</th>
                    <th>Progreso</th>
                    <th>Fecha Límite</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Migración Cloud</td>
                    <td><span class="status-badge status-progress">En Progreso</span></td>
                    <td>$125,000</td>
                    <td>
                        <div class="progress-bar" style="width: 120px;">
                            <div class="progress" style="width: 65%;"></div>
                        </div>
                    </td>
                    <td>2024-09-30</td>
                </tr>
                <!-- Más filas -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Proyectos
    const projectsCtx = document.getElementById('projectsChart').getContext('2d');
    new Chart(projectsCtx, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Proyectos Completados',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: '#28a745'
            }, {
                label: 'Proyectos en Riesgo',
                data: [4, 3, 5, 2, 6, 4],
                backgroundColor: '#ffc107'
            }]
        }
    });

    // Gráfico de Recursos
    const resourcesCtx = document.getElementById('resourcesChart').getContext('2d');
    new Chart(resourcesCtx, {
        type: 'pie',
        data: {
            labels: ['Desarrollo', 'Diseño', 'QA', 'Administración'],
            datasets: [{
                data: [45, 25, 15, 15],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        }
    });

    function generatePDF() {
        // Lógica para generar PDF
        alert('Generando reporte PDF...');
    }

    function exportExcel() {
        // Lógica para exportar Excel
        alert('Exportando a Excel...');
    }
</script>