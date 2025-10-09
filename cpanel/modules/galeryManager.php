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
    
    .fade-in {
      animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .image-container {
      position: relative;
      overflow: hidden;
      border-radius: 0.75rem;
    }
    
    .image-container img {
      transition: transform 0.3s ease;
    }
    
    .image-container:hover img {
      transform: scale(1.05);
    }
    
    .status-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      padding: 4px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
  </style>

  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
            Galería de Fotos
          </h1>
          <p class="text-slate-600">Administra las imágenes de tu galería</p>
        </div>
        <button onclick="openUploadModal()" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
          <i data-lucide="plus" class="w-4 h-4"></i>
          Subir Nueva Foto
        </button>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-8">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
          <input type="text" placeholder="Buscar fotos..." 
                 class="w-full pl-10 pr-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
        </div>
        <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
          <option>Todas las categorías</option>
          <option>Eventos</option>
          <option>Productos</option>
          <option>Equipo</option>
        </select>
        <select class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
          <option>Todos los estados</option>
          <option>Activas</option>
          <option>Inactivas</option>
        </select>
      </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- Photo Card 1 -->
      <div class="glassmorphism rounded-2xl overflow-hidden shadow-lg card-hover">
        <div class="image-container aspect-square">
          <img src="https://source.unsplash.com/random/600x600/?nature" 
               alt="Imagen de muestra" 
               class="w-full h-full object-cover">
          <span class="status-badge bg-green-100 text-green-800">Activa</span>
        </div>
        <div class="p-4">
          <h3 class="font-bold text-slate-900 mb-1">Paisaje Natural</h3>
          <p class="text-sm text-slate-600 mb-3">Hermoso paisaje al atardecer en las montañas.</p>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-500">15 Mar 2024</span>
            <div class="flex gap-2">
              <button onclick="togglePhotoStatus(1)" 
                      class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
              </button>
              <button onclick="openEditModal(1)" 
                      class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </button>
              <button onclick="confirmDelete(1)" 
                      class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Photo Card 2 -->
      <div class="glassmorphism rounded-2xl overflow-hidden shadow-lg card-hover">
        <div class="image-container aspect-square">
          <img src="https://source.unsplash.com/random/600x600/?city" 
               alt="Imagen de muestra" 
               class="w-full h-full object-cover">
          <span class="status-badge bg-red-100 text-red-800">Inactiva</span>
        </div>
        <div class="p-4">
          <h3 class="font-bold text-slate-900 mb-1">Ciudad Moderna</h3>
          <p class="text-sm text-slate-600 mb-3">Vista aérea del centro financiero.</p>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-500">10 Mar 2024</span>
            <div class="flex gap-2">
              <button onclick="togglePhotoStatus(2)" 
                      class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
              </button>
              <button onclick="openEditModal(2)" 
                      class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </button>
              <button onclick="confirmDelete(2)" 
                      class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Photo Card 3 -->
      <div class="glassmorphism rounded-2xl overflow-hidden shadow-lg card-hover">
        <div class="image-container aspect-square">
          <img src="https://source.unsplash.com/random/600x600/?tech" 
               alt="Imagen de muestra" 
               class="w-full h-full object-cover">
          <span class="status-badge bg-green-100 text-green-800">Activa</span>
        </div>
        <div class="p-4">
          <h3 class="font-bold text-slate-900 mb-1">Tecnología</h3>
          <p class="text-sm text-slate-600 mb-3">Dispositivos electrónicos modernos.</p>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-500">05 Mar 2024</span>
            <div class="flex gap-2">
              <button onclick="togglePhotoStatus(3)" 
                      class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
              </button>
              <button onclick="openEditModal(3)" 
                      class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </button>
              <button onclick="confirmDelete(3)" 
                      class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Photo Card 4 -->
      <div class="glassmorphism rounded-2xl overflow-hidden shadow-lg card-hover">
        <div class="image-container aspect-square">
          <img src="https://source.unsplash.com/random/600x600/?food" 
               alt="Imagen de muestra" 
               class="w-full h-full object-cover">
          <span class="status-badge bg-green-100 text-green-800">Activa</span>
        </div>
        <div class="p-4">
          <h3 class="font-bold text-slate-900 mb-1">Gastronomía</h3>
          <p class="text-sm text-slate-600 mb-3">Plato gourmet preparado por chef profesional.</p>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-500">28 Feb 2024</span>
            <div class="flex gap-2">
              <button onclick="togglePhotoStatus(4)" 
                      class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
              </button>
              <button onclick="openEditModal(4)" 
                      class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </button>
              <button onclick="confirmDelete(4)" 
                      class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="glassmorphism rounded-2xl p-4 shadow-xl mt-8">
      <div class="flex justify-between items-center">
        <span class="text-sm text-slate-600">Mostrando 4 de 24 fotos</span>
        <div class="flex gap-2">
          <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
          </button>
          <button class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg">1</button>
          <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors">2</button>
          <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors">3</button>
          <button class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Modal -->
  <div id="upload-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('upload-modal')"></div>
    <div class="glassmorphism rounded-2xl p-6 w-full max-w-2xl mx-4 relative fade-in">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-900">Subir Nueva Foto</h2>
        <button onclick="closeModal('upload-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form class="space-y-6">
        <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center">
          <div class="flex flex-col items-center justify-center gap-3">
            <i data-lucide="upload-cloud" class="w-12 h-12 text-blue-500"></i>
            <p class="font-medium text-slate-900">Arrastra tus fotos aquí</p>
            <p class="text-sm text-slate-600">o haz clic para seleccionar archivos</p>
            <input type="file" id="file-upload" class="hidden" accept="image/*">
            <button type="button" onclick="document.getElementById('file-upload').click()" 
                    class="mt-4 px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
              Seleccionar Archivos
            </button>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
          <input type="text" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Título descriptivo">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
          <textarea rows="3" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Descripción detallada de la foto"></textarea>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Categoría</label>
            <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Seleccionar categoría</option>
              <option>Eventos</option>
              <option>Productos</option>
              <option>Equipo</option>
              <option>Otros</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
            <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Activa</option>
              <option>Inactiva</option>
            </select>
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('upload-modal')" 
                  class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-colors font-medium">
            Cancelar
          </button>
          <button type="submit" 
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg">
            Subir Foto
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('edit-modal')"></div>
    <div class="glassmorphism rounded-2xl p-6 w-full max-w-2xl mx-4 relative fade-in">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-900">Editar Foto</h2>
        <button onclick="closeModal('edit-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form class="space-y-6">
        <div class="flex flex-col items-center gap-4">
          <div class="image-container w-full h-64 rounded-xl overflow-hidden">
            <img src="https://source.unsplash.com/random/600x600/?nature" 
                 alt="Imagen a editar" 
                 class="w-full h-full object-cover">
          </div>
          <button type="button" 
                  class="px-4 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-colors text-sm">
            <i data-lucide="image" class="w-4 h-4 inline mr-2"></i>
            Cambiar Imagen
          </button>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
          <input type="text" value="Paisaje Natural" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
          <textarea rows="3" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">Hermoso paisaje al atardecer en las montañas.</textarea>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Categoría</label>
            <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option>Eventos</option>
              <option selected>Naturaleza</option>
              <option>Productos</option>
              <option>Equipo</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
            <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
              <option selected>Activa</option>
              <option>Inactiva</option>
            </select>
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeModal('edit-modal')" 
                  class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-colors font-medium">
            Cancelar
          </button>
          <button type="submit" 
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('delete-modal')"></div>
    <div class="glassmorphism rounded-2xl p-6 w-full max-w-md mx-4 relative fade-in">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
        </div>
        <h3 class="text-lg font-medium text-slate-900 mt-4">¿Eliminar esta foto?</h3>
        <div class="mt-2">
          <p class="text-sm text-slate-600">
            Esta acción no se puede deshacer. La foto será eliminada permanentemente del sistema.
          </p>
        </div>
      </div>
      <div class="mt-6 flex gap-4">
        <button type="button" onclick="closeModal('delete-modal')" 
                class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-colors font-medium">
          Cancelar
        </button>
        <button type="button" onclick="deletePhoto()" 
                class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all font-medium shadow-lg">
          Eliminar
        </button>
      </div>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Global variables
    let currentPhotoId = null;
    
    // Modal functions
    function openUploadModal() {
      document.getElementById('upload-modal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(photoId) {
      currentPhotoId = photoId;
      document.getElementById('edit-modal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
    
    function confirmDelete(photoId) {
      currentPhotoId = photoId;
      document.getElementById('delete-modal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
    
    // Gallery functions
    function togglePhotoStatus(photoId) {
      console.log(`Cambiando estado de la foto ${photoId}`);
      // Aquí iría la lógica para cambiar el estado en la base de datos
      alert(`Estado de la foto ${photoId} cambiado`);
    }
    
    function deletePhoto() {
      console.log(`Eliminando foto ${currentPhotoId}`);
      // Aquí iría la lógica para eliminar de la base de datos
      alert(`Foto ${currentPhotoId} eliminada`);
      closeModal('delete-modal');
    }
    
    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
      const modals = ['upload-modal', 'edit-modal', 'delete-modal'];
      modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
          closeModal(modalId);
        }
      });
    });
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Sistema de Galería inicializado');
    });
  </script>