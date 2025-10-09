  <style>
    .glassmorphism {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .card-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .tab-active {
      background: rgba(59, 130, 246, 0.1);
      border-bottom: 2px solid #3b82f6;
      color: #3b82f6;
    }
    
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
    }
    
    .modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 1rem;
      padding: 2rem;
      max-width: 90vw;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
  </style>
    <header class="glassmorphism shadow-xl border-b border-white/20 sticky top-0 z-30">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
              Criterios de Evaluación
            </h1>
            <p class="text-slate-600 text-sm">
              Gestión de criterios y rangos de calificación
            </p>
          </div>
        </div>
      </div>
    </header>

      <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
          <div class="flex flex-1 gap-3">
            <div class="relative flex-1 max-w-md">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
              <input type="text" placeholder="Buscar criterios..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>
            <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Todos los niveles</option>
              <option>Básico</option>
              <option>Avanzado</option>
              <option>Todos</option>
            </select>
          </div>
          <button onclick="openCriteriaModal()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg mt-4 md:mt-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Criterio
          </button>
        </div>

        <!-- Criteria Table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-sm text-slate-600 border-b border-white/20">
                <th class="pb-4 pl-4">#</th>
                <th class="pb-4">Nombre</th>
                <th class="pb-4">Nivel</th>
                <th class="pb-4">Orden</th>
                <th class="pb-4 pr-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <!-- Sample Data Row 1 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4 pl-4">1</td>
                <td class="py-4 font-medium text-slate-900">Calidad del Código</td>
                <td class="py-4">
                  <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Todos</span>
                </td>
                <td class="py-4">1</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="viewCriteriaDetails(1)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="eye" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="editCriteria(1)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteCriteria(1)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Sample Data Row 2 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4 pl-4">2</td>
                <td class="py-4 font-medium text-slate-900">Documentación</td>
                <td class="py-4">
                  <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Avanzado</span>
                </td>
                <td class="py-4">3</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="viewCriteriaDetails(2)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="eye" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="editCriteria(2)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteCriteria(2)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Sample Data Row 3 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4 pl-4">3</td>
                <td class="py-4 font-medium text-slate-900">Funcionalidad Básica</td>
                <td class="py-4">
                  <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">Básico</span>
                </td>
                <td class="py-4">2</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="viewCriteriaDetails(3)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="eye" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="editCriteria(3)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteCriteria(3)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6">
          <div class="text-sm text-slate-600">
            Mostrando 1 al 3 de 3 registros
          </div>
          <div class="flex gap-2">
            <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all disabled:opacity-50" disabled>
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <button class="px-4 py-2 bg-blue-100 text-blue-800 border border-blue-200 rounded-lg font-medium">
              1
            </button>
            <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all disabled:opacity-50" disabled>
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      </div>

  <!-- Criteria Modal -->
  <div id="criteria-modal" class="modal">
    <div class="modal-content w-full max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900" id="criteria-modal-title">Nuevo Criterio</h2>
        <button onclick="closeModal('criteria-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="criteria-form" class="space-y-6">
        <input type="hidden" id="criteria-id">
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Nombre del Criterio</label>
          <input type="text" id="criteria-name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ej: Calidad del Código" required>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nivel que Aplica</label>
            <select id="criteria-level" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option value="todos">Todos</option>
              <option value="basico">Básico</option>
              <option value="avanzado">Avanzado</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
            <input type="number" id="criteria-order" min="1" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ej: 1" required>
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('criteria-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cancelar
          </button>
          <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
            Guardar Criterio
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Criteria Details Modal -->
  <div id="criteria-details-modal" class="modal">
    <div class="modal-content w-full max-w-3xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Detalles del Criterio</h2>
        <button onclick="closeModal('criteria-details-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <div class="space-y-6">
        <!-- Criteria Info -->
        <div class="glassmorphism rounded-xl p-6">
          <div class="grid gap-6 md:grid-cols-3">
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Nombre</p>
              <p class="text-lg font-bold text-slate-900" id="detail-name">Calidad del Código</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Nivel</p>
              <p class="text-lg font-bold text-slate-900" id="detail-level">Todos</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Orden</p>
              <p class="text-lg font-bold text-slate-900" id="detail-order">1</p>
            </div>
          </div>
        </div>
        
        <!-- Reasons Section -->
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Rangos y Razones</h3>
          <button onclick="openReasonModal()" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Razón
          </button>
        </div>
        
        <!-- Reasons Table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-sm text-slate-600 border-b border-white/20">
                <th class="pb-4">Rango Mínimo</th>
                <th class="pb-4">Rango Máximo</th>
                <th class="pb-4">Razón</th>
                <th class="pb-4 pr-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <!-- Sample Reason 1 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4">1.0</td>
                <td class="py-4">2.5</td>
                <td class="py-4">El código tiene múltiples problemas de estructura y estilo</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="editReason(1)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteReason(1)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Sample Reason 2 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4">2.6</td>
                <td class="py-4">3.9</td>
                <td class="py-4">El código cumple con lo básico pero necesita mejoras</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="editReason(2)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteReason(2)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Sample Reason 3 -->
              <tr class="border-b border-white/20 hover:bg-white/50 transition-colors">
                <td class="py-4">4.0</td>
                <td class="py-4">5.0</td>
                <td class="py-4">El código es limpio, bien estructurado y sigue buenas prácticas</td>
                <td class="py-4 pr-4">
                  <div class="flex justify-end gap-2">
                    <button onclick="editReason(3)" class="p-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">
                      <i data-lucide="edit" class="w-4 h-4 text-slate-700"></i>
                    </button>
                    <button onclick="deleteReason(3)" class="p-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                      <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button onclick="closeModal('criteria-details-modal')" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Reason Modal -->
  <div id="reason-modal" class="modal">
    <div class="modal-content w-full max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900" id="reason-modal-title">Nueva Razón</h2>
        <button onclick="closeModal('reason-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="reason-form" class="space-y-6">
        <input type="hidden" id="reason-id">
        <input type="hidden" id="reason-criteria-id">
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Rango Mínimo</label>
            <input type="number" id="reason-min" min="0" max="5" step="0.1" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0.0" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Rango Máximo</label>
            <input type="number" id="reason-max" min="0" max="5" step="0.1" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="5.0" required>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Razón</label>
          <textarea rows="4" id="reason-text" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Descripción detallada de la razón..." required></textarea>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('reason-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
            Cancelar
          </button>
          <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg">
            Guardar Razón
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Modal functions
    function openCriteriaModal(criteria = null) {
      const modal = document.getElementById('criteria-modal');
      const form = document.getElementById('criteria-form');
      const title = document.getElementById('criteria-modal-title');
      
      if (criteria) {
        title.textContent = 'Editar Criterio';
        document.getElementById('criteria-id').value = criteria.id;
        document.getElementById('criteria-name').value = criteria.nombre;
        document.getElementById('criteria-level').value = criteria.nivel_aplica;
        document.getElementById('criteria-order').value = criteria.orden;
      } else {
        title.textContent = 'Nuevo Criterio';
        form.reset();
      }
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function openReasonModal(reason = null) {
      const modal = document.getElementById('reason-modal');
      const form = document.getElementById('reason-form');
      const title = document.getElementById('reason-modal-title');
      
      if (reason) {
        title.textContent = 'Editar Razón';
        document.getElementById('reason-id').value = reason.id;
        document.getElementById('reason-criteria-id').value = reason.criterio_id;
        document.getElementById('reason-min').value = reason.rango_min;
        document.getElementById('reason-max').value = reason.rango_max;
        document.getElementById('reason-text').value = reason.razon;
      } else {
        title.textContent = 'Nueva Razón';
        form.reset();
        // Set the current criteria ID (you would get this from the details view)
        document.getElementById('reason-criteria-id').value = 1;
      }
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // View functions
    function viewCriteriaDetails(id) {
      // In a real app, you would fetch this data from your API
      const criteria = {
        id: id,
        nombre: id === 1 ? 'Calidad del Código' : id === 2 ? 'Documentación' : 'Funcionalidad Básica',
        nivel_aplica: id === 1 ? 'todos' : id === 2 ? 'avanzado' : 'basico',
        orden: id === 1 ? 1 : id === 2 ? 3 : 2
      };
      
      document.getElementById('detail-name').textContent = criteria.nombre;
      document.getElementById('detail-level').textContent = 
        criteria.nivel_aplica === 'todos' ? 'Todos' : 
        criteria.nivel_aplica === 'basico' ? 'Básico' : 'Avanzado';
      document.getElementById('detail-order').textContent = criteria.orden;
      
      // Set the criteria ID for adding new reasons
      document.getElementById('reason-criteria-id').value = id;
      
      closeModal('criteria-modal');
      openModal('criteria-details-modal');
    }

    function openModal(modalId) {
      document.getElementById(modalId).classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // CRUD functions
    function editCriteria(id) {
      // In a real app, you would fetch this data from your API
      const criteria = {
        id: id,
        nombre: id === 1 ? 'Calidad del Código' : id === 2 ? 'Documentación' : 'Funcionalidad Básica',
        nivel_aplica: id === 1 ? 'todos' : id === 2 ? 'avanzado' : 'basico',
        orden: id === 1 ? 1 : id === 2 ? 3 : 2
      };
      openCriteriaModal(criteria);
    }

    function deleteCriteria(id) {
      if (confirm('¿Está seguro de que desea eliminar este criterio? También se eliminarán todas sus razones asociadas.')) {
        console.log('Deleting criteria:', id);
        // Here you would implement the delete functionality
      }
    }

    function editReason(id) {
      // In a real app, you would fetch this data from your API
      const reason = {
        id: id,
        criterio_id: 1,
        rango_min: id === 1 ? 1.0 : id === 2 ? 2.6 : 4.0,
        rango_max: id === 1 ? 2.5 : id === 2 ? 3.9 : 5.0,
        razon: id === 1 ? 'El código tiene múltiples problemas de estructura y estilo' : 
              id === 2 ? 'El código cumple con lo básico pero necesita mejoras' : 
              'El código es limpio, bien estructurado y sigue buenas prácticas'
      };
      openReasonModal(reason);
    }

    function deleteReason(id) {
      if (confirm('¿Está seguro de que desea eliminar esta razón?')) {
        console.log('Deleting reason:', id);
        // Here you would implement the delete functionality
      }
    }

    // Form submissions
    document.getElementById('criteria-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = {
        id: document.getElementById('criteria-id').value,
        nombre: document.getElementById('criteria-name').value,
        nivel_aplica: document.getElementById('criteria-level').value,
        orden: document.getElementById('criteria-order').value
      };
      
      console.log('Saving criteria:', formData);
      closeModal('criteria-modal');
      // Here you would implement the save functionality
    });

    document.getElementById('reason-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = {
        id: document.getElementById('reason-id').value,
        criterio_id: document.getElementById('reason-criteria-id').value,
        rango_min: document.getElementById('reason-min').value,
        rango_max: document.getElementById('reason-max').value,
        razon: document.getElementById('reason-text').value
      };
      
      console.log('Saving reason:', formData);
      closeModal('reason-modal');
      // Here you would implement the save functionality
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
      const modals = document.querySelectorAll('.modal.show');
      modals.forEach(modal => {
        if (event.target === modal) {
          closeModal(modal.id);
        }
      });
    });
  </script>