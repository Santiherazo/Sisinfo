<?php
    $allUsers = $profileManager->getAllUsers();
    $allResearchLines = $researchLines->getAllActivas();
    $allProjects = $projectmanager->getAllProjects();
    $allEvaluations = $evaluationManager->getAllEvaluations();
?>

<div id="statistics-module" class="module-content">
    <!-- Header con controles avanzados -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 mb-2 flex items-center gap-3">
                    <i data-lucide="bar-chart-big" class="w-8 h-8 text-blue-600"></i>
                    Panel de Analítica Académica
                </h2>
                <p class="text-slate-600">Métricas avanzadas y análisis predictivo del sistema educativo</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-4 lg:mt-0">
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <select class="pl-10 pr-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Últimos 30 días</option>
                        <option>Últimos 7 días</option>
                        <option>Este mes</option>
                        <option>Trimestre actual</option>
                        <option>Semestre actual</option>
                        <option>Año académico</option>
                        <option>Personalizado</option>
                    </select>
                </div>
                
                <div class="relative">
                    <i data-lucide="filter" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <select class="pl-10 pr-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Todos los programas</option>
                        <option>Ingeniería de Sistemas</option>
                        <option>Ingeniería Industrial</option>
                        <option>Ingeniería Civil</option>
                        <option>Administración</option>
                        <option>Postgrados</option>
                    </select>
                </div>
                
                <button onclick="exportStatistics('pdf')" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    PDF
                </button>
                
                <button onclick="exportStatistics('excel')" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Excel
                </button>
                
                <button onclick="showAdvancedFilters()" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    Filtros Avanzados
                </button>
            </div>
        </div>

        <!-- Filtros avanzados (inicialmente ocultos) -->
        <div id="advanced-filters" class="hidden glassmorphism rounded-xl p-6 mt-4 shadow-lg">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Línea de Investigación</label>
                    <select class="w-full px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Todas las líneas</option>
                        <option>Inteligencia Artificial</option>
                        <option>Ingeniería de Software</option>
                        <option>Seguridad Informática</option>
                        <option>Redes y Telecomunicaciones</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Proyecto</label>
                    <select class="w-full px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Todos los tipos</option>
                        <option>Investigación</option>
                        <option>Desarrollo Tecnológico</option>
                        <option>Emprendimiento</option>
                        <option>Práctica Profesional</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                    <select class="w-full px-4 py-2 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Todos los estados</option>
                        <option>Activo</option>
                        <option>Finalizado</option>
                        <option>En pausa</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="applyAdvancedFilters()" class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg">
                    Aplicar Filtros
                </button>
                <button onclick="resetAdvancedFilters()" class="px-6 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                    Restablecer
                </button>
            </div>
        </div>
    </div>

    <!-- Tarjetas de KPI con análisis comparativo -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- KPI 1 -->
        <div class="glassmorphism rounded-xl p-6 hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg relative">
            <div class="absolute top-4 right-4 flex items-center gap-1 text-sm text-slate-500">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>KPI Académico</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl shadow-lg">
                    <i data-lucide="users" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-green-600 flex items-center justify-end gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        +12% vs período anterior
                    </span>
                    <span class="text-xs text-slate-500">Benchmark: +8%</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Participación Activa</p>
                <p class="text-3xl font-bold text-slate-900">87%</p>
                <div class="w-full bg-slate-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 87%"></div>
                </div>
                <p class="text-xs text-slate-500 mt-2">1,247 usuarios activos de 1,433 registrados</p>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="glassmorphism rounded-xl p-6 hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg relative">
            <div class="absolute top-4 right-4 flex items-center gap-1 text-sm text-slate-500">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>KPI de Proyectos</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl shadow-lg">
                    <i data-lucide="folder" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-green-600 flex items-center justify-end gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        +18% vs período anterior
                    </span>
                    <span class="text-xs text-slate-500">Benchmark: +12%</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Tasa de Finalización</p>
                <p class="text-3xl font-bold text-slate-900">72%</p>
                <div class="w-full bg-slate-200 rounded-full h-2 mt-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 72%"></div>
                </div>
                <p class="text-xs text-slate-500 mt-2">64 de 89 proyectos completados según cronograma</p>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="glassmorphism rounded-xl p-6 hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg relative">
            <div class="absolute top-4 right-4 flex items-center gap-1 text-sm text-slate-500">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>KPI de Evaluación</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl shadow-lg">
                    <i data-lucide="clipboard-check" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-green-600 flex items-center justify-end gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        +5% vs período anterior
                    </span>
                    <span class="text-xs text-slate-500">Benchmark: +3%</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">Calificación Promedio</p>
                <p class="text-3xl font-bold text-slate-900">4.3</p>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 bg-slate-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 86%"></div>
                    </div>
                    <span class="text-xs text-slate-600">/5.0</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Basado en 156 evaluaciones completadas</p>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="glassmorphism rounded-xl p-6 hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg relative">
            <div class="absolute top-4 right-4 flex items-center gap-1 text-sm text-slate-500">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>KPI de Satisfacción</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-orange-500 to-amber-500 rounded-xl shadow-lg">
                    <i data-lucide="star" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-green-600 flex items-center justify-end gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        +0.3 vs período anterior
                    </span>
                    <span class="text-xs text-slate-500">Benchmark: +0.2</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-600 mb-1">NPS Académico</p>
                <p class="text-3xl font-bold text-slate-900">68</p>
                <div class="flex items-center gap-3 mt-2">
                    <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="h-full flex">
                            <div class="bg-red-500" style="width: 15%"></div>
                            <div class="bg-yellow-500" style="width: 17%"></div>
                            <div class="bg-green-500" style="width: 68%"></div>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600">Promotores</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Encuesta a 230 participantes</p>
            </div>
        </div>
    </div>

    <!-- Sección de gráficos principales -->
    <div class="grid gap-8 lg:grid-cols-2 mb-8">
        <!-- Gráfico de actividad con segmentación -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Actividad Académica por Segmento</h3>
                    <p class="text-sm text-slate-600">Interacciones diarias desglosadas por tipo de usuario</p>
                </div>
                <div class="flex gap-2 mt-3 md:mt-0">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-xs">Estudiantes</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-xs">Docentes</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                        <span class="text-xs">Evaluadores</span>
                    </div>
                </div>
            </div>
            <div class="h-80">
                <canvas id="segmentedActivityChart"></canvas>
            </div>
            <div class="flex justify-between items-center mt-4 text-sm text-slate-600">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Actualizado hace 15 minutos</span>
                </div>
                <button onclick="toggleActivityDetails()" class="text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Ver detalles técnicos</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
            </div>
            
            <!-- Detalles técnicos (ocultos inicialmente) -->
            <div id="activity-details" class="hidden bg-white/60 rounded-xl p-4 mt-4 text-sm text-slate-700">
                <p class="font-medium mb-2">Metodología de medición:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Datos recolectados mediante eventos de interacción en plataforma</li>
                    <li>Segmentación por roles de usuario con validación cruzada</li>
                    <li>Suavizado de datos con media móvil de 7 días</li>
                    <li>Intervalo de confianza del 95% para tendencias</li>
                </ul>
            </div>
        </div>

        <!-- Distribución geográfica -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Distribución Geográfica</h3>
                    <p class="text-sm text-slate-600">Procedencia de estudiantes y evaluadores</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-sm flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        Mapa
                    </button>
                    <button class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors text-sm flex items-center gap-1">
                        <i data-lucide="table" class="w-3 h-3"></i>
                        Tabla
                    </button>
                </div>
            </div>
            <div class="h-80 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="map" class="w-12 h-12 text-slate-300 mx-auto"></i>
                        <p class="text-slate-400 mt-2">Mapa interactivo no disponible en vista previa</p>
                    </div>
                </div>
                <canvas id="geographicDistributionChart"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 text-center">
                <div class="bg-white/60 p-3 rounded-lg">
                    <p class="text-sm text-slate-600">Ciudad Principal</p>
                    <p class="font-bold text-slate-900">Bogotá</p>
                    <p class="text-xs text-slate-500">42% de usuarios</p>
                </div>
                <div class="bg-white/60 p-3 rounded-lg">
                    <p class="text-sm text-slate-600">Mayor Crecimiento</p>
                    <p class="font-bold text-slate-900">Medellín</p>
                    <p class="text-xs text-slate-500">+18% este año</p>
                </div>
                <div class="bg-white/60 p-3 rounded-lg">
                    <p class="text-sm text-slate-600">Internacional</p>
                    <p class="font-bold text-slate-900">7 países</p>
                    <p class="text-xs text-slate-500">5% de participación</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de rendimiento académico -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Rendimiento Académico por Programa</h3>
                <p class="text-sm text-slate-600">Comparativa de calificaciones con desviación estándar</p>
            </div>
            <div class="flex gap-2 mt-3 md:mt-0">
                <button class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-sm flex items-center gap-1">
                    <i data-lucide="bar-chart-2" class="w-3 h-3"></i>
                    Barras
                </button>
                <button class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors text-sm flex items-center gap-1">
                    <i data-lucide="line-chart" class="w-3 h-3"></i>
                    Líneas
                </button>
                <button onclick="showPerformanceHeatmap()" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors text-sm flex items-center gap-1">
                    <i data-lucide="grid" class="w-3 h-3"></i>
                    Heatmap
                </button>
            </div>
        </div>
        <div class="h-96">
            <canvas id="performanceComparisonChart"></canvas>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Mejor Programa</p>
                <p class="text-xl font-bold text-slate-900">Ing. Sistemas</p>
                <p class="text-xs text-slate-500">4.5 ± 0.3</p>
            </div>
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Mayor Mejora</p>
                <p class="text-xl font-bold text-slate-900">Ing. Industrial</p>
                <p class="text-xs text-slate-500">+0.4 vs anterior</p>
            </div>
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Menor Variación</p>
                <p class="text-xl font-bold text-slate-900">Administración</p>
                <p class="text-xs text-slate-500">± 0.2 desviación</p>
            </div>
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Correlación</p>
                <p class="text-xl font-bold text-slate-900">0.87</p>
                <p class="text-xs text-slate-500">Proyectos vs Calificaciones</p>
            </div>
        </div>
    </div>

    <!-- Tendencias y análisis predictivo -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Tendencias y Proyección</h3>
                <p class="text-sm text-slate-600">Análisis temporal con modelo predictivo ARIMA</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Modelo:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">ARIMA(2,1,1)</span>
                <span class="text-xs text-slate-500">AIC: 142.3</span>
            </div>
        </div>
        <div class="h-96">
            <canvas id="predictiveAnalysisChart"></canvas>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Próximo Trimestre</p>
                <p class="text-xl font-bold text-slate-900">+12%</p>
                <p class="text-xs text-slate-500">Crecimiento esperado</p>
            </div>
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Intervalo Confianza</p>
                <p class="text-xl font-bold text-slate-900">8-16%</p>
                <p class="text-xs text-slate-500">95% de certeza</p>
            </div>
            <div class="bg-white/60 p-4 rounded-lg">
                <p class="text-sm font-medium text-slate-600 mb-1">Factor Clave</p>
                <p class="text-xl font-bold text-slate-900">Evaluaciones</p>
                <p class="text-xs text-slate-500">R² = 0.78</p>
            </div>
        </div>
    </div>

    <!-- Tablero de indicadores completos -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Tablero de Indicadores Académicos</h3>
                <p class="text-sm text-slate-600">Métricas detalladas por programa y categoría</p>
            </div>
            <button onclick="exportDashboard()" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                <i data-lucide="download" class="w-4 h-4"></i>
                Exportar Tablero
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead>
                    <tr class="border-b border-white/20 text-left text-slate-600">
                        <th class="pb-3 font-medium pr-6">Programa</th>
                        <th class="pb-3 font-medium text-right pr-6">Proyectos</th>
                        <th class="pb-3 font-medium text-right pr-6">Evaluaciones</th>
                        <th class="pb-3 font-medium text-right pr-6">Calificación</th>
                        <th class="pb-3 font-medium text-right pr-6">Retención</th>
                        <th class="pb-3 font-medium text-right pr-6">Satisfacción</th>
                        <th class="pb-3 font-medium text-right pr-6">Tasa Éxito</th>
                        <th class="pb-3 font-medium text-right">Crecimiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/20">
                    <tr class="hover:bg-white/60 transition-colors">
                        <td class="py-3 pr-6">
                            <p class="font-medium text-slate-900">Ing. Sistemas</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">32</p>
                            <p class="text-xs text-slate-500">+5 vs anterior</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">58</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">4.5</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">92%</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">4.6</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">88%</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="flex items-center justify-end gap-1 text-green-600">
                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                15%
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/60 transition-colors">
                        <td class="py-3 pr-6">
                            <p class="font-medium text-slate-900">Ing. Industrial</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">28</p>
                            <p class="text-xs text-slate-500">+3 vs anterior</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">42</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">4.2</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">89%</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">4.3</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">82%</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="flex items-center justify-end gap-1 text-green-600">
                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                8%
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/60 transition-colors">
                        <td class="py-3 pr-6">
                            <p class="font-medium text-slate-900">Ing. Civil</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">18</p>
                            <p class="text-xs text-slate-500">+2 vs anterior</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">32</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">3.9</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">85%</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">4.0</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">75%</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="flex items-center justify-end gap-1 text-green-600">
                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                5%
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/60 transition-colors">
                        <td class="py-3 pr-6">
                            <p class="font-medium text-slate-900">Administración</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">15</p>
                            <p class="text-xs text-slate-500">+1 vs anterior</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <p class="text-slate-900 font-medium">24</p>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">4.1</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">87%</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">4.2</span>
                        </td>
                        <td class="py-3 text-right pr-6">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">80%</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="flex items-center justify-end gap-1 text-green-600">
                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                6%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-between items-center mt-6 text-sm text-slate-600">
            <div class="flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>Datos actualizados al 15 de marzo de 2024</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span>Por encima del objetivo</span>
                </span>
                <span class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span>En rango aceptable</span>
                </span>
                <span class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span>Requiere atención</span>
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicialización de gráficos avanzados
    function initAdvancedCharts() {
        // Gráfico de actividad segmentada
        const segmentedCtx = document.getElementById('segmentedActivityChart').getContext('2d');
        new Chart(segmentedCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 30}, (_, i) => `${i+1} Mar`),
                datasets: [
                    {
                        label: 'Estudiantes',
                        data: [80, 90, 95, 100, 110, 120, 115, 125, 140, 150, 155, 165, 175, 180, 185, 195, 205, 210, 215, 220, 225, 230, 235, 240, 245, 250, 255, 260, 265, 270],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Docentes',
                        data: [30, 35, 37, 40, 45, 50, 48, 52, 55, 60, 62, 65, 70, 72, 75, 78, 80, 82, 85, 88, 90, 92, 95, 98, 100, 102, 105, 108, 110, 112],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Evaluadores',
                        data: [10, 10, 10, 10, 15, 10, 12, 13, 15, 15, 13, 15, 15, 18, 20, 17, 20, 18, 20, 22, 25, 28, 30, 32, 35, 38, 40, 42, 45, 48],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.05)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            footer: (tooltipItems) => {
                                let sum = 0;
                                tooltipItems.forEach(item => {
                                    sum += item.parsed.y;
                                });
                                return `Total: ${sum}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Gráfico de distribución geográfica (simplificado)
        const geoCtx = document.getElementById('geographicDistributionChart').getContext('2d');
        new Chart(geoCtx, {
            type: 'doughnut',
            data: {
                labels: ['Bogotá', 'Medellín', 'Cali', 'Otras ciudades', 'Internacional'],
                datasets: [{
                    data: [42, 25, 15, 13, 5],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#8b5cf6',
                        '#f59e0b',
                        '#6366f1'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de comparación de rendimiento
        const performanceCtx = document.getElementById('performanceComparisonChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: ['Ing. Sistemas', 'Ing. Industrial', 'Ing. Civil', 'Administración'],
                datasets: [{
                    label: 'Calificación Promedio',
                    data: [4.5, 4.2, 3.9, 4.1],
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    borderWidth: 1,
                    borderColor: 'rgba(255, 255, 255, 0.8)'
                }, {
                    label: 'Desviación Estándar',
                    data: [0.3, 0.4, 0.5, 0.2],
                    backgroundColor: '#94a3b8',
                    borderRadius: 6,
                    borderWidth: 1,
                    borderColor: 'rgba(255, 255, 255, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += context.raw.toFixed(1) + '/5.0';
                                } else {
                                    label += '±' + context.raw.toFixed(1);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Gráfico de análisis predictivo
        const predictiveCtx = document.getElementById('predictiveAnalysisChart').getContext('2d');
        new Chart(predictiveCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar'],
                datasets: [{
                    label: 'Datos Históricos',
                    data: [120, 135, 142, null, null, null, null, null, null, null, null, null, null, null, null],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }, {
                    label: 'Proyección ARIMA',
                    data: [null, null, 142, 150, 158, 165, 172, 180, 187, 195, 202, 210, 217, 225, 232],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false
                }, {
                    label: 'Intervalo Confianza (95%)',
                    data: [null, null, 142, 148, 154, 160, 166, 172, 178, 184, 190, 196, 202, 208, 214],
                    borderColor: 'rgba(16, 185, 129, 0.3)',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    tension: 0.1,
                    fill: 1
                }, {
                    label: '',
                    data: [null, null, 142, 152, 162, 170, 178, 188, 196, 206, 214, 224, 232, 242, 250],
                    borderColor: 'rgba(16, 185, 129, 0.3)',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    tension: 0.1,
                    fill: '-1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label && context.datasetIndex > 0) {
                                    label += ': ';
                                    if (context.raw) {
                                        label += context.raw.toLocaleString();
                                    } else {
                                        label += 'No disponible';
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 100,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Funciones de interacción
    function showAdvancedFilters() {
        const filters = document.getElementById('advanced-filters');
        filters.classList.toggle('hidden');
    }

    function applyAdvancedFilters() {
        console.log('Aplicando filtros avanzados...');
        // Aquí iría la lógica para aplicar los filtros
    }

    function resetAdvancedFilters() {
        console.log('Restableciendo filtros...');
        // Aquí iría la lógica para resetear los filtros
    }

    function toggleActivityDetails() {
        const details = document.getElementById('activity-details');
        details.classList.toggle('hidden');
    }

    function showPerformanceHeatmap() {
        console.log('Mostrando heatmap de rendimiento...');
        // Aquí iría la lógica para cambiar a vista de heatmap
    }

    function exportStatistics(format) {
        console.log(`Exportando estadísticas en formato ${format}...`);
        // Aquí iría la lógica de exportación
    }

    function exportDashboard() {
        console.log('Exportando tablero completo...');
        // Aquí iría la lógica para exportar todo el dashboard
    }

    // Inicializar gráficos cuando se muestre el módulo
    function showStatisticsModule() {
        showModule('statistics');
        setTimeout(initAdvancedCharts, 50);
    }
</script>