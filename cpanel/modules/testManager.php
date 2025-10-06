  <div class="container mx-auto p-6">
    <div class="glassmorphism rounded-2xl shadow-xl overflow-hidden">
      <!-- Project Header -->
      <div class="gradient-bg p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <h1 class="text-3xl font-bold">Sistema de Gestión Inteligente</h1>
            <p class="text-white/90 mt-2">Desarrollo de un sistema de gestión basado en IA para optimizar procesos empresariales</p>
            
            <div class="flex flex-wrap gap-3 mt-4">
              <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">Ingeniería de Sistemas</span>
              <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">Inteligencia Artificial</span>
              <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">Desarrollo</span>
            </div>
          </div>
          
          <div class="flex gap-3">
            <button class="flex items-center gap-2 px-4 py-2 bg-white/20 rounded-xl hover:bg-white/30 transition-all">
              <i data-lucide="edit" class="w-5 h-5"></i>
              Editar
            </button>
            <button class="flex items-center gap-2 px-4 py-2 bg-white/20 rounded-xl hover:bg-white/30 transition-all">
              <i data-lucide="share-2" class="w-5 h-5"></i>
              Compartir
            </button>
          </div>
        </div>
      </div>
      
      <!-- Project Content -->
      <div class="p-8">
        <!-- Project Tabs -->
        <div class="flex border-b border-white/20 mb-6">
          <button class="px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 tab-active">
            <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
            Información
          </button>
          <button class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
            Documentos
          </button>
          <button class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i data-lucide="clipboard-check" class="w-4 h-4 inline mr-2"></i>
            Evaluaciones
          </button>
          <button class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
            Equipo
          </button>
          <button class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i data-lucide="activity" class="w-4 h-4 inline mr-2"></i>
            Cronograma
          </button>
        </div>
        
        <!-- Project Details -->
        <div class="grid gap-8 lg:grid-cols-3">
          <!-- Left Column -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Description Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Descripción del Proyecto</h3>
              <p class="text-slate-600 mb-4">
                Este proyecto busca desarrollar un sistema inteligente de gestión empresarial que utilice técnicas de inteligencia artificial para optimizar procesos internos, reducir costos operacionales y mejorar la toma de decisiones estratégicas. El sistema integrará módulos de análisis predictivo, automatización de procesos y dashboards interactivos para la visualización de datos.
              </p>
              <p class="text-slate-600">
                La solución propuesta se basa en un enfoque multidisciplinario que combina ingeniería de software, ciencia de datos y experiencia de usuario, con el objetivo de crear una herramienta escalable y adaptable a diferentes tipos de organizaciones.
              </p>
            </div>
            
            <!-- Objectives Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Objetivos</h3>
              <div class="space-y-3">
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                    <i data-lucide="target" class="w-3 h-3 text-blue-600"></i>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">Objetivo General</p>
                    <p class="text-sm text-slate-600">Desarrollar un sistema de gestión empresarial inteligente que mejore la eficiencia operacional mediante el uso de técnicas de IA.</p>
                  </div>
                </div>
                
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-1">
                    <i data-lucide="check-circle" class="w-3 h-3 text-green-600"></i>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">Objetivo Específico 1</p>
                    <p class="text-sm text-slate-600">Implementar módulos de análisis predictivo para la toma de decisiones estratégicas.</p>
                  </div>
                </div>
                
                <div class="flex items-start gap-3">
                  <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mt-1">
                    <i data-lucide="check-circle" class="w-3 h-3 text-purple-600"></i>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">Objetivo Específico 2</p>
                    <p class="text-sm text-slate-600">Automatizar procesos operativos recurrentes para reducir tiempos y costos.</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Progress Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Progreso del Proyecto</h3>
              
              <div class="space-y-4">
                <div>
                  <div class="flex justify-between text-sm text-slate-600 mb-2">
                    <span>Estado Actual</span>
                    <span>65% completado</span>
                  </div>
                  <div class="w-full bg-slate-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full" style="width: 65%"></div>
                  </div>
                </div>
                
                <div class="grid gap-4 md:grid-cols-3">
                  <div class="p-3 bg-white/60 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Fecha de Inicio</p>
                    <p class="font-medium text-slate-900">15/01/2024</p>
                  </div>
                  <div class="p-3 bg-white/60 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Fecha Estimada</p>
                    <p class="font-medium text-slate-900">15/06/2024</p>
                  </div>
                  <div class="p-3 bg-white/60 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Días Restantes</p>
                    <p class="font-medium text-slate-900">45 días</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Team Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Equipo</h3>
              
              <div class="space-y-4">
                <div class="flex items-center gap-3 p-3 bg-white/60 rounded-lg">
                  <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white text-sm font-semibold">MG</span>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">María González</p>
                    <p class="text-xs text-slate-600">Estudiante</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 p-3 bg-white/60 rounded-lg">
                  <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white text-sm font-semibold">CR</span>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">Dr. Carlos Rodríguez</p>
                    <p class="text-xs text-slate-600">Director</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 p-3 bg-white/60 rounded-lg">
                  <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white text-sm font-semibold">AM</span>
                  </div>
                  <div>
                    <p class="font-medium text-slate-900">Dra. Ana Martínez</p>
                    <p class="text-xs text-slate-600">Evaluadora</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Key Dates Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Fechas Clave</h3>
              
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Presentación Propuesta</p>
                    <p class="text-xs text-slate-500">15/02/2024 • Completado</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-yellow-600"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Revisión Intermedia</p>
                    <p class="text-xs text-slate-500">15/04/2024 • Próximo</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <i data-lucide="flag" class="w-4 h-4 text-gray-600"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Entrega Final</p>
                    <p class="text-xs text-slate-500">15/06/2024</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Metrics Card -->
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Métricas</h3>
              
              <div class="grid gap-4 md:grid-cols-2">
                <div class="p-3 bg-white/60 rounded-lg text-center">
                  <p class="text-2xl font-bold text-blue-600">4.5</p>
                  <p class="text-xs text-slate-600">Calificación Actual</p>
                </div>
                <div class="p-3 bg-white/60 rounded-lg text-center">
                  <p class="text-2xl font-bold text-green-600">3</p>
                  <p class="text-xs text-slate-600">Evaluaciones</p>
                </div>
                <div class="p-3 bg-white/60 rounded-lg text-center">
                  <p class="text-2xl font-bold text-purple-600">12</p>
                  <p class="text-xs text-slate-600">Documentos</p>
                </div>
                <div class="p-3 bg-white/60 rounded-lg text-center">
                  <p class="text-2xl font-bold text-orange-600">5</p>
                  <p class="text-xs text-slate-600">Tareas Pendientes</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    lucide.createIcons();
  </script>


