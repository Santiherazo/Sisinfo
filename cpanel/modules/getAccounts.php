

<div class="bg-white/60 rounded-xl p-4 sm:p-6 hover:bg-white/80 transition-all duration-200 sm:hover:scale-105 shadow-lg relative">
            <!-- Overlay cuando está desactivado o bloqueado -->
            <div class="absolute inset-0 bg-black/5 rounded-xl pointer-events-none hidden" id="status-overlay"></div>
            
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-sm sm:text-base">MG</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 sm:w-4 sm:h-4 bg-green-500 rounded-full border-2 border-white" id="status-indicator"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base truncate">María González</h3>
                        <p class="text-xs sm:text-sm text-slate-500 truncate">maria.gonzalez@university.edu</p>
                    </div>
                </div>
                
                <!-- Contenedor de botones de estado - Añadido items-center para alineación vertical -->
                <div class="flex gap-2 items-center">  <!-- Aquí está el cambio clave -->
                    <!-- Toggle para activar/desactivar - Añadido my-auto para centrado vertical -->
                    <label class="relative inline-flex items-center cursor-pointer my-auto">  <!-- Ajuste añadido -->
                        <input type="checkbox" class="sr-only peer" id="active-toggle" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                    
                    <!-- Botón de candado para bloquear/desbloquear -->
                    <button class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                        <i data-lucide="unlock" class="w-4 h-4 text-blue-500" id="lock-icon"></i>
                    </button>
                </div>
            </div>
            
            <div class="space-y-2 mb-3 sm:mb-4">
                <div class="flex flex-wrap gap-1 sm:gap-2">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-semibold">Estudiante</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-semibold" id="status-badge">Activo</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 truncate">Ingeniería de Sistemas</p>
                <p class="text-xs text-slate-500">Semestre 8</p>
            </div>
            
            <div class="flex gap-2">
                <button class="flex-1 flex items-center justify-center gap-1 px-2 py-1 sm:px-3 sm:py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-xs sm:text-sm">
                    <i data-lucide="eye" class="w-3 h-3"></i>
                    <span>Ver</span>
                </button>
                <button class="flex-1 flex items-center justify-center gap-1 px-2 py-1 sm:px-3 sm:py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-xs sm:text-sm">
                    <i data-lucide="edit" class="w-3 h-3"></i>
                    <span>Editar</span>
                </button>
                <button class="flex items-center justify-center gap-1 px-2 py-1 sm:px-3 sm:py-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all text-xs sm:text-sm text-red-600">
                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
            </div>
        </div>