<div class="container">
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .chart-card {
            padding: 20px;
            margin: 15px 0;
            border: 1px solid #eee;
            border-radius: 8px;
            position: relative;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }

        .chart-switcher {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .chart-type-btn {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chart-type-btn.active {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .metric-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #f8f9fa;
        }
    </style>

    <div class="chart-header">
        <h2>Análisis de Proyectos</h2>
        <div class="export-options">
            <button class="export-button" onclick="exportChartAsImage()">
                <i class="material-icons">image</i>
                Exportar Imagen
            </button>
        </div>
    </div>

    <div class="chart-filters">
        <div>
            <label>Rango Temporal:</label>
            <select class="form-input" id="timeRange">
                <option value="7d">Últimos 7 días</option>
                <option value="30d">Últimos 30 días</option>
                <option value="6m">Últimos 6 meses</option>
                <option value="1y">Último año</option>
            </select>
        </div>
        
        <div>
            <label>Tipo de Proyecto:</label>
            <select class="form-input" id="projectType">
                <option value="all">Todos</option>
                <option value="software">Software</option>
                <option value="marketing">Marketing</option>
                <option value="infra">Infraestructura</option>
            </select>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-switcher">
            <div class="chart-type-btn active" onclick="switchChartType('bar')">Barras</div>
            <div class="chart-type-btn" onclick="switchChartType('line')">Líneas</div>
            <div class="chart-type-btn" onclick="switchChartType('pie')">Torta</div>
        </div>
        <span class="metric-badge">📊 45 Proyectos</span>
        <div class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    <div class="chart-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 25px;">
        <div class="chart-card">
            <h4>Estado de Proyectos</h4>
            <div class="chart-container">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h4>Progreso por Equipo</h4>
            <div class="chart-container">
                <canvas id="radarChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h4>Presupuesto vs Gastos</h4>
            <div class="chart-container">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración inicial de gráficos
    let mainChart;
    const chartConfig = {
        bar: {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Proyectos Completados',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: '#007bff'
                }]
            }
        },
        line: {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Progreso Mensual',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: '#28a745',
                    fill: false
                }]
            }
        },
        pie: {
            type: 'pie',
            data: {
                labels: ['Completados', 'En Progreso', 'Retrasados'],
                datasets: [{
                    data: [45, 30, 25],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            }
        }
    };

    // Inicializar gráfico principal
    function initChart() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        mainChart = new Chart(ctx, chartConfig.bar);
    }

    // Cambiar tipo de gráfico
    function switchChartType(type) {
        document.querySelectorAll('.chart-type-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        mainChart.destroy();
        mainChart = new Chart(document.getElementById('mainChart').getContext('2d'), chartConfig[type]);
    }

    // Gráficos secundarios
    new Chart(document.getElementById('doughnutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Completados', 'En Progreso', 'Planificación'],
            datasets: [{
                data: [45, 30, 25],
                backgroundColor: ['#28a745', '#007bff', '#6c757d']
            }]
        }
    });

    new Chart(document.getElementById('radarChart').getContext('2d'), {
        type: 'radar',
        data: {
            labels: ['Desarrollo', 'Diseño', 'QA', 'Documentación', 'Implementación'],
            datasets: [{
                label: 'Equipo A',
                data: [80, 60, 75, 50, 90],
                backgroundColor: 'rgba(0, 123, 255, 0.2)'
            }, {
                label: 'Equipo B',
                data: [65, 75, 55, 70, 80],
                backgroundColor: 'rgba(40, 167, 69, 0.2)'
            }]
        }
    });

    new Chart(document.getElementById('scatterChart').getContext('2d'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Presupuesto vs Gastos',
                data: [
                    {x: 100, y: 95},
                    {x: 150, y: 140},
                    {x: 200, y: 210},
                    {x: 80, y: 75},
                    {x: 120, y: 115}
                ],
                backgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                x: {
                    title: {display: true, text: 'Presupuesto (miles USD)'}
                },
                y: {
                    title: {display: true, text: 'Gastos (miles USD)'}
                }
            }
        }
    });

    // Exportar función
    function exportChartAsImage() {
        const chartCanvas = mainChart.canvas;
        const image = chartCanvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'grafico.png';
        link.href = image;
        link.click();
    }

    // Inicializar
    initChart();
</script>