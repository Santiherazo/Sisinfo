      <!-- Tabs para módulos -->
      <div class="flex border-b border-slate-200 mb-6">
        <button id="posts-tab" class="px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600">Publicaciones</button>
        <button id="categories-tab" class="px-4 py-2 font-medium text-slate-500 hover:text-blue-600">Categorías</button>
        <button id="comments-tab" class="px-4 py-2 font-medium text-slate-500 hover:text-blue-600">Comentarios</button>
      </div>

      <!-- Módulo de Blog (Publicaciones) -->
      <div id="blog-module" class="module-content fade-in">
        <!-- Header del módulo -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-slate-900 mb-2">Gestión de Publicaciones</h2>
              <p class="text-slate-600">Crea, edita y administra las publicaciones del blog académico</p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0">
              <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" placeholder="Buscar publicaciones..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              </div>
              <button onclick="openPostModal()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nueva Publicación
              </button>
            </div>
          </div>

          <!-- Filtros -->
          <div class="flex flex-wrap gap-3 mb-6">
            <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Todas las categorías</option>
              <option>Investigación</option>
              <option>Eventos</option>
              <option>Convocatorias</option>
              <option>Noticias</option>
            </select>
            <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Todos los estados</option>
              <option>Publicado</option>
              <option>Borrador</option>
              <option>Archivado</option>
            </select>
            <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Ordenar por</option>
              <option>Más reciente</option>
              <option>Más antiguo</option>
              <option>Más popular</option>
            </select>
          </div>
        </div>

        <!-- Listado de publicaciones -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <!-- Tarjeta de publicación 1 -->
          <div class="glassmorphism rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 card-hover">
            <div class="h-48 bg-gradient-to-r from-blue-400 to-purple-500 relative">
              <img src="https://source.unsplash.com/random/600x400/?research" alt="Imagen de publicación" class="w-full h-full object-cover">
              <div class="absolute top-4 right-4 flex gap-2">
                <span class="px-3 py-1 bg-white/90 text-blue-800 rounded-full text-xs font-semibold">Investigación</span>
                <span class="px-3 py-1 bg-white/90 text-green-800 rounded-full text-xs font-semibold">Publicado</span>
              </div>
            </div>
            <div class="p-6">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                  <span class="text-white text-xs font-semibold">AD</span>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Administrador</p>
                  <p class="text-xs text-slate-500">15 Mar 2024</p>
                </div>
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Nuevos avances en inteligencia artificial aplicada</h3>
              <p class="text-sm text-slate-600 mb-4 line-clamp-2">Descubre cómo los últimos desarrollos en IA están transformando la investigación académica en múltiples disciplinas científicas.</p>
              <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                <div class="flex items-center gap-2">
                  <i data-lucide="eye" class="w-4 h-4"></i>
                  <span>1,245 vistas</span>
                </div>
                <div class="flex items-center gap-2">
                  <i data-lucide="message-circle" class="w-4 h-4"></i>
                  <span>23 comentarios</span>
                </div>
              </div>
              <div class="flex gap-2">
                <button onclick="editPost('post1')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="edit" class="w-3 h-3"></i>
                  Editar
                </button>
                <button onclick="viewPost('post1')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="eye" class="w-3 h-3"></i>
                  Ver
                </button>
                <button onclick="deletePost('post1')" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all text-sm text-red-600">
                  <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Tarjeta de publicación 2 (Borrador) -->
          <div class="glassmorphism rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 card-hover">
            <div class="h-48 bg-gradient-to-r from-amber-400 to-orange-500 relative">
              <div class="w-full h-full flex items-center justify-center bg-white/20">
                <i data-lucide="edit-3" class="w-12 h-12 text-white/80"></i>
              </div>
              <div class="absolute top-4 right-4 flex gap-2">
                <span class="px-3 py-1 bg-white/90 text-purple-800 rounded-full text-xs font-semibold">Eventos</span>
                <span class="px-3 py-1 bg-white/90 text-yellow-800 rounded-full text-xs font-semibold">Borrador</span>
              </div>
            </div>
            <div class="p-6">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                  <span class="text-white text-xs font-semibold">CR</span>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Carlos Rodríguez</p>
                  <p class="text-xs text-slate-500">12 Mar 2024</p>
                </div>
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Conferencia internacional de ciencia de datos 2024</h3>
              <p class="text-sm text-slate-600 mb-4 line-clamp-2">Resumen de los temas principales que se tratarán en la próxima conferencia internacional que se realizará en nuestro campus.</p>
              <div class="flex gap-2">
                <button onclick="editPost('post2')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="edit" class="w-3 h-3"></i>
                  Editar
                </button>
                <button onclick="previewPost('post2')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="monitor" class="w-3 h-3"></i>
                  Previsualizar
                </button>
                <button onclick="deletePost('post2')" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all text-sm text-red-600">
                  <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Tarjeta de publicación 3 (Archivado) -->
          <div class="glassmorphism rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 card-hover">
            <div class="h-48 bg-gradient-to-r from-gray-400 to-slate-500 relative">
              <img src="https://source.unsplash.com/random/600x400/?university" alt="Imagen de publicación" class="w-full h-full object-cover">
              <div class="absolute top-4 right-4 flex gap-2">
                <span class="px-3 py-1 bg-white/90 text-blue-800 rounded-full text-xs font-semibold">Convocatorias</span>
                <span class="px-3 py-1 bg-white/90 text-gray-800 rounded-full text-xs font-semibold">Archivado</span>
              </div>
            </div>
            <div class="p-6">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                  <span class="text-white text-xs font-semibold">AM</span>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Ana Martínez</p>
                  <p class="text-xs text-slate-500">10 Ene 2024</p>
                </div>
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Becas para investigación en robótica 2023-2024</h3>
              <p class="text-sm text-slate-600 mb-4 line-clamp-2">Conoce los requisitos y beneficios de las becas disponibles para proyectos de investigación en robótica avanzada.</p>
              <div class="flex gap-2">
                <button onclick="editPost('post3')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="edit" class="w-3 h-3"></i>
                  Editar
                </button>
                <button onclick="restorePost('post3')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                  <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                  Restaurar
                </button>
                <button onclick="deletePost('post3')" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all text-sm text-red-600">
                  <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paginación -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mt-6">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-slate-600 mb-4 md:mb-0">Mostrando 1-3 de 12 publicaciones</p>
            <div class="flex gap-2">
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all disabled:opacity-50" disabled>
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
              </button>
              <button class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg">1</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">2</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">3</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">4</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Módulo de Categorías (oculto inicialmente) -->
      <div id="categories-module" class="module-content hidden">
        <!-- Header del módulo -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-slate-900 mb-2">Gestión de Categorías</h2>
              <p class="text-slate-600">Administra las categorías para organizar las publicaciones del blog</p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0">
              <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" placeholder="Buscar categorías..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              </div>
              <button onclick="openCategoryModal()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nueva Categoría
              </button>
            </div>
          </div>
        </div>

        <!-- Listado de categorías -->
        <div class="glassmorphism rounded-2xl shadow-xl overflow-hidden">
          <table class="min-w-full divide-y divide-white/20">
            <thead class="bg-white/60">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nombre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">URL</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Publicaciones</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/20">
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-slate-900">Investigación</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">/investigacion</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  42
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="editCategory('cat1')" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                  </button>
                  <button onclick="toggleCategoryStatus('cat1')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteCategory('cat1')" class="text-red-600 hover:text-red-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-slate-900">Eventos</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">/eventos</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  28
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="editCategory('cat2')" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                  </button>
                  <button onclick="toggleCategoryStatus('cat2')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteCategory('cat2')" class="text-red-600 hover:text-red-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-slate-900">Convocatorias</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">/convocatorias</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  15
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="editCategory('cat3')" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                  </button>
                  <button onclick="toggleCategoryStatus('cat3')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteCategory('cat3')" class="text-red-600 hover:text-red-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm font-medium text-slate-900">Noticias</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">/noticias</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactiva</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  0
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="editCategory('cat4')" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                  </button>
                  <button onclick="toggleCategoryStatus('cat4')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                    <i data-lucide="toggle-right" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteCategory('cat4')" class="text-red-600 hover:text-red-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mt-6">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-slate-600 mb-4 md:mb-0">Mostrando 1-4 de 4 categorías</p>
            <div class="flex gap-2">
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all disabled:opacity-50" disabled>
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
              </button>
              <button class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg">1</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Módulo de Comentarios (oculto inicialmente) -->
      <div id="comments-module" class="module-content hidden">
        <!-- Header del módulo -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-slate-900 mb-2">Gestión de Comentarios</h2>
              <p class="text-slate-600">Modera los comentarios realizados en las publicaciones del blog</p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0">
              <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" placeholder="Buscar comentarios..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              </div>
              <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option>Todos los estados</option>
                <option>Aprobados</option>
                <option>Pendientes</option>
                <option>Rechazados</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Listado de comentarios -->
        <div class="glassmorphism rounded-2xl shadow-xl overflow-hidden">
          <table class="min-w-full divide-y divide-white/20">
            <thead class="bg-white/60">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Comentario</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Publicación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Usuario</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Fecha</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/20">
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="text-sm text-slate-900 line-clamp-2">Excelente artículo, muy informativo sobre los últimos avances en IA aplicada a la investigación médica.</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">Nuevos avances en IA...</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">Juan Pérez</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">16 Mar 2024</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="rejectComment('comment1')" class="text-red-600 hover:text-red-900 mr-3">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteComment('comment1')" class="text-gray-600 hover:text-gray-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="text-sm text-slate-900 line-clamp-2">¿Habrá transmisión en vivo de la conferencia para quienes no podemos asistir presencialmente?</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">Conferencia ciencia...</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">María Gómez</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">14 Mar 2024</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="approveComment('comment2')" class="text-green-600 hover:text-green-900 mr-3">
                    <i data-lucide="check" class="w-4 h-4"></i>
                  </button>
                  <button onclick="rejectComment('comment2')" class="text-red-600 hover:text-red-900 mr-3">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteComment('comment2')" class="text-gray-600 hover:text-gray-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-white/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="text-sm text-slate-900 line-clamp-2">Este contenido es irrelevante y no aporta nada nuevo al tema. Muy decepcionante.</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">Becas investigación...</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">Anónimo</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-600">12 Mar 2024</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="approveComment('comment3')" class="text-green-600 hover:text-green-900 mr-3">
                    <i data-lucide="check" class="w-4 h-4"></i>
                  </button>
                  <button onclick="deleteComment('comment3')" class="text-gray-600 hover:text-gray-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mt-6">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-slate-600 mb-4 md:mb-0">Mostrando 1-3 de 15 comentarios</p>
            <div class="flex gap-2">
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all disabled:opacity-50" disabled>
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
              </button>
              <button class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg">1</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">2</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">3</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">4</button>
              <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

  <!-- Modal para crear/editar publicación -->
  <div id="post-modal" class="modal">
    <div class="modal-content w-full max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900" id="modal-post-title">Nueva Publicación</h2>
        <button onclick="closeModal('post-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Título *</label>
            <input type="text" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Título de la publicación" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">URL amigable *</label>
            <div class="flex">
              <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-white/20 bg-slate-50 text-slate-500 text-sm">
                https://blog.university.edu/
              </span>
              <input type="text" class="flex-1 min-w-0 block w-full px-3 py-3 rounded-r-xl border border-white/20 focus:ring-blue-500 focus:border-blue-500" placeholder="url-amigable" required>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Imagen destacada</label>
          <div class="flex items-center gap-4">
            <div class="w-32 h-32 bg-slate-100 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center">
              <i data-lucide="image" class="w-8 h-8 text-slate-400"></i>
            </div>
            <div class="flex gap-2">
              <button type="button" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all text-sm">
                <i data-lucide="upload" class="w-4 h-4 inline mr-1"></i>
                Subir imagen
              </button>
              <button type="button" class="px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all text-sm">
                <i data-lucide="image" class="w-4 h-4 inline mr-1"></i>
                Biblioteca
              </button>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Contenido *</label>
          <div class="glassmorphism rounded-xl p-4 shadow-inner min-h-[300px]">
            <!-- Editor de texto enriquecido simulado -->
            <div class="flex gap-1 mb-4 border-b border-white/20 pb-2">
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="bold" class="w-4 h-4"></i></button>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="italic" class="w-4 h-4"></i></button>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="underline" class="w-4 h-4"></i></button>
              <div class="w-px h-6 bg-white/20 mx-1"></div>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="list" class="w-4 h-4"></i></button>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="list-ordered" class="w-4 h-4"></i></button>
              <div class="w-px h-6 bg-white/20 mx-1"></div>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="link" class="w-4 h-4"></i></button>
              <button class="p-2 hover:bg-white/50 rounded-lg"><i data-lucide="image" class="w-4 h-4"></i></button>
            </div>
            <textarea rows="10" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Escribe el contenido de la publicación aquí..." required></textarea>
          </div>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Categorías *</label>
            <select multiple class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all h-[52px] overflow-y-auto">
              <option>Investigación</option>
              <option>Eventos</option>
              <option>Convocatorias</option>
              <option>Noticias</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Estado *</label>
            <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option value="draft">Borrador</option>
              <option value="active">Publicado</option>
              <option value="archived">Archivado</option>
            </select>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Etiquetas</label>
          <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
              investigación
              <button type="button" class="ml-1 text-blue-600 hover:text-blue-800">
                <i data-lucide="x" class="w-3 h-3"></i>
              </button>
            </span>
            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
              ciencia de datos
              <button type="button" class="ml-1 text-green-600 hover:text-green-800">
                <i data-lucide="x" class="w-3 h-3"></i>
              </button>
            </span>
            <input type="text" class="flex-1 min-w-[100px] px-3 py-1 bg-white/60 border border-white/20 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm" placeholder="Añadir etiqueta...">
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('post-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cancelar
          </button>
          <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg">
            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
            Guardar Publicación
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para crear/editar categoría -->
  <div id="category-modal" class="modal">
    <div class="modal-content w-full max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900" id="modal-category-title">Nueva Categoría</h2>
        <button onclick="closeModal('category-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
          <input type="text" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Nombre de la categoría" required>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">URL amigable *</label>
          <div class="flex">
            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-white/20 bg-slate-50 text-slate-500 text-sm">
              https://blog.university.edu/
            </span>
            <input type="text" class="flex-1 min-w-0 block w-full px-3 py-3 rounded-r-xl border border-white/20 focus:ring-blue-500 focus:border-blue-500" placeholder="url-amigable" required>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Estado *</label>
          <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            <option value="active">Activa</option>
            <option value="inactive">Inactiva</option>
          </select>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('category-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cancelar
          </button>
          <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg">
            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
            Guardar Categoría
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para confirmar eliminación -->
  <div id="confirm-modal" class="modal">
    <div class="modal-content w-full max-w-md">
      <div class="text-center">
        <i data-lucide="alert-triangle" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
        <h2 class="text-xl font-bold text-slate-900 mb-2" id="confirm-title">¿Estás seguro?</h2>
        <p class="text-slate-600 mb-6" id="confirm-message">Esta acción no se puede deshacer.</p>
        <div class="flex gap-4">
          <button onclick="closeModal('confirm-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cancelar
          </button>
          <button onclick="confirmAction()" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl hover:from-red-600 hover:to-rose-700 transition-all font-medium shadow-lg">
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Inicializar iconos
    lucide.createIcons();

    // Variables para manejar acciones
    let currentAction = '';
    let currentItemId = '';

    // Mostrar módulo según tab seleccionado
    document.getElementById('posts-tab').addEventListener('click', function() {
      document.getElementById('blog-module').classList.remove('hidden');
      document.getElementById('categories-module').classList.add('hidden');
      document.getElementById('comments-module').classList.add('hidden');
      this.classList.add('text-blue-600', 'border-blue-600');
      this.classList.remove('text-slate-500', 'border-transparent');
      document.getElementById('categories-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('categories-tab').classList.add('text-slate-500', 'border-transparent');
      document.getElementById('comments-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('comments-tab').classList.add('text-slate-500', 'border-transparent');
    });

    document.getElementById('categories-tab').addEventListener('click', function() {
      document.getElementById('blog-module').classList.add('hidden');
      document.getElementById('categories-module').classList.remove('hidden');
      document.getElementById('comments-module').classList.add('hidden');
      this.classList.add('text-blue-600', 'border-blue-600');
      this.classList.remove('text-slate-500', 'border-transparent');
      document.getElementById('posts-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('posts-tab').classList.add('text-slate-500', 'border-transparent');
      document.getElementById('comments-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('comments-tab').classList.add('text-slate-500', 'border-transparent');
    });

    document.getElementById('comments-tab').addEventListener('click', function() {
      document.getElementById('blog-module').classList.add('hidden');
      document.getElementById('categories-module').classList.add('hidden');
      document.getElementById('comments-module').classList.remove('hidden');
      this.classList.add('text-blue-600', 'border-blue-600');
      this.classList.remove('text-slate-500', 'border-transparent');
      document.getElementById('posts-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('posts-tab').classList.add('text-slate-500', 'border-transparent');
      document.getElementById('categories-tab').classList.remove('text-blue-600', 'border-blue-600');
      document.getElementById('categories-tab').classList.add('text-slate-500', 'border-transparent');
    });

    // Funciones para publicaciones
    function openPostModal() {
      document.getElementById('modal-post-title').textContent = 'Nueva Publicación';
      document.getElementById('post-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    function editPost(postId) {
      document.getElementById('modal-post-title').textContent = 'Editar Publicación';
      document.getElementById('post-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
      currentItemId = postId;
      // Aquí iría la lógica para cargar los datos de la publicación
    }
    
    function viewPost(postId) {
      console.log('Viendo publicación:', postId);
      // Redirigir o mostrar la publicación completa
    }
    
    function deletePost(postId) {
      currentAction = 'deletePost';
      currentItemId = postId;
      document.getElementById('confirm-title').textContent = 'Eliminar Publicación';
      document.getElementById('confirm-message').textContent = '¿Estás seguro de que deseas eliminar esta publicación? Esta acción no se puede deshacer.';
      document.getElementById('confirm-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    function previewPost(postId) {
      console.log('Previsualizando publicación:', postId);
      // Mostrar vista previa de la publicación
    }
    
    function restorePost(postId) {
      currentAction = 'restorePost';
      currentItemId = postId;
      document.getElementById('confirm-title').textContent = 'Restaurar Publicación';
      document.getElementById('confirm-message').textContent = '¿Estás seguro de que deseas restaurar esta publicación? Volverá a estar disponible en el blog.';
      document.getElementById('confirm-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Funciones para categorías
    function openCategoryModal() {
      document.getElementById('modal-category-title').textContent = 'Nueva Categoría';
      document.getElementById('category-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    function editCategory(categoryId) {
      document.getElementById('modal-category-title').textContent = 'Editar Categoría';
      document.getElementById('category-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
      currentItemId = categoryId;
      // Aquí iría la lógica para cargar los datos de la categoría
    }
    
    function toggleCategoryStatus(categoryId) {
      currentAction = 'toggleCategoryStatus';
      currentItemId = categoryId;
      // Aquí iría la lógica para cambiar el estado de la categoría
      console.log('Cambiando estado de categoría:', categoryId);
    }
    
    function deleteCategory(categoryId) {
      currentAction = 'deleteCategory';
      currentItemId = categoryId;
      document.getElementById('confirm-title').textContent = 'Eliminar Categoría';
      document.getElementById('confirm-message').textContent = '¿Estás seguro de que deseas eliminar esta categoría? Las publicaciones asociadas no se eliminarán, pero perderán esta categorización.';
      document.getElementById('confirm-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Funciones para comentarios
    function approveComment(commentId) {
      console.log('Aprobando comentario:', commentId);
      // Aquí iría la lógica para aprobar el comentario
    }
    
    function rejectComment(commentId) {
      currentAction = 'rejectComment';
      currentItemId = commentId;
      document.getElementById('confirm-title').textContent = 'Rechazar Comentario';
      document.getElementById('confirm-message').textContent = '¿Estás seguro de que deseas rechazar este comentario? El usuario no podrá verlo publicado.';
      document.getElementById('confirm-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    function deleteComment(commentId) {
      currentAction = 'deleteComment';
      currentItemId = commentId;
      document.getElementById('confirm-title').textContent = 'Eliminar Comentario';
      document.getElementById('confirm-message').textContent = '¿Estás seguro de que deseas eliminar este comentario? Esta acción no se puede deshacer.';
      document.getElementById('confirm-modal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Función para confirmar acciones
    function confirmAction() {
      switch(currentAction) {
        case 'deletePost':
          console.log('Eliminando publicación:', currentItemId);
          // Lógica para eliminar publicación
          break;
        case 'restorePost':
          console.log('Restaurando publicación:', currentItemId);
          // Lógica para restaurar publicación
          break;
        case 'deleteCategory':
          console.log('Eliminando categoría:', currentItemId);
          // Lógica para eliminar categoría
          break;
        case 'rejectComment':
          console.log('Rechazando comentario:', currentItemId);
          // Lógica para rechazar comentario
          break;
        case 'deleteComment':
          console.log('Eliminando comentario:', currentItemId);
          // Lógica para eliminar comentario
          break;
      }
      closeModal('confirm-modal');
    }

    // Función para cerrar modales
    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('show');
      document.body.style.overflow = 'auto';
    }
  </script>