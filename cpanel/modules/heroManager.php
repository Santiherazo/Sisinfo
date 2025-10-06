  <style>
    .glassmorphism {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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

    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #3b82f6;
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }
    
    .hero-preview {
      transition: all 0.3s ease;
      transform-origin: center;
    }
    
    .hero-preview:hover {
      transform: scale(1.02);
    }
    
    .slide-fade-enter-active {
      transition: all 0.3s ease-out;
    }
    
    .slide-fade-leave-active {
      transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
    }
    
    .slide-fade-enter-from,
    .slide-fade-leave-to {
      transform: translateX(20px);
      opacity: 0;
    }
  </style>
  
      <!-- Hero Management Module -->
      <div id="heros-module" class="module-content fade-in">
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Gestión de Heros</h2>
              <p class="text-slate-600">Crea y gestiona los banners principales de tu sitio web</p>
            </div>
            <button onclick="openHeroModal()" class="mt-4 md:mt-0 flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
              <i data-lucide="plus" class="w-4 h-4"></i>
              Nuevo Hero
            </button>
          </div>

          <!-- Active Heros Section -->
          <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
              <i data-lucide="zap" class="w-5 h-5 text-green-600"></i>
              <h3 class="text-lg font-semibold text-slate-900">Heros Activos</h3>
              <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">3 activos</span>
            </div>
            
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              <!-- Hero Card 1 -->
              <div class="bg-white/60 rounded-xl overflow-hidden shadow-lg card-hover">
                <div class="relative">
                  <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Hero 1" class="w-full h-48 object-cover">
                  <div class="absolute top-3 right-3">
                    <label class="switch">
                      <input type="checkbox" checked onchange="toggleHeroStatus('hero1', this)">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>
                <div class="p-5">
                  <h4 class="font-bold text-slate-900 mb-2">Promoción de Verano</h4>
                  <p class="text-sm text-slate-600 mb-4">Descuentos especiales en todos nuestros cursos</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">15 Mar - 30 Abr 2024</span>
                    <div class="flex gap-2">
                      <button onclick="editHero('hero1')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                      </button>
                      <button onclick="deleteHero('hero1')" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Hero Card 2 -->
              <div class="bg-white/60 rounded-xl overflow-hidden shadow-lg card-hover">
                <div class="relative">
                  <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Hero 2" class="w-full h-48 object-cover">
                  <div class="absolute top-3 right-3">
                    <label class="switch">
                      <input type="checkbox" checked onchange="toggleHeroStatus('hero2', this)">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>
                <div class="p-5">
                  <h4 class="font-bold text-slate-900 mb-2">Nuevos Cursos</h4>
                  <p class="text-sm text-slate-600 mb-4">Descubre nuestra oferta académica 2024</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">1 Ene - 31 Dic 2024</span>
                    <div class="flex gap-2">
                      <button onclick="editHero('hero2')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                      </button>
                      <button onclick="deleteHero('hero2')" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Hero Card 3 -->
              <div class="bg-white/60 rounded-xl overflow-hidden shadow-lg card-hover">
                <div class="relative">
                  <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Hero 3" class="w-full h-48 object-cover">
                  <div class="absolute top-3 right-3">
                    <label class="switch">
                      <input type="checkbox" checked onchange="toggleHeroStatus('hero3', this)">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>
                <div class="p-5">
                  <h4 class="font-bold text-slate-900 mb-2">Evento Internacional</h4>
                  <p class="text-sm text-slate-600 mb-4">Conferencia con expertos mundiales</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">10 May - 15 May 2024</span>
                    <div class="flex gap-2">
                      <button onclick="editHero('hero3')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                      </button>
                      <button onclick="deleteHero('hero3')" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Inactive Heros Section -->
          <div>
            <div class="flex items-center gap-3 mb-4">
              <i data-lucide="moon" class="w-5 h-5 text-slate-600"></i>
              <h3 class="text-lg font-semibold text-slate-900">Heros Inactivos</h3>
              <span class="px-3 py-1 bg-slate-100 text-slate-800 rounded-full text-sm font-medium">2 inactivos</span>
            </div>
            
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              <!-- Hero Card 4 -->
              <div class="bg-white/60 rounded-xl overflow-hidden shadow-lg card-hover opacity-80">
                <div class="relative">
                  <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Hero 4" class="w-full h-48 object-cover">
                  <div class="absolute top-3 right-3">
                    <label class="switch">
                      <input type="checkbox" onchange="toggleHeroStatus('hero4', this)">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>
                <div class="p-5">
                  <h4 class="font-bold text-slate-900 mb-2">Oferta de Invierno</h4>
                  <p class="text-sm text-slate-600 mb-4">Cursos con descuento para la temporada</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">1 Dic 2023 - 15 Ene 2024</span>
                    <div class="flex gap-2">
                      <button onclick="editHero('hero4')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                      </button>
                      <button onclick="deleteHero('hero4')" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Hero Card 5 -->
              <div class="bg-white/60 rounded-xl overflow-hidden shadow-lg card-hover opacity-80">
                <div class="relative">
                  <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Hero 5" class="w-full h-48 object-cover">
                  <div class="absolute top-3 right-3">
                    <label class="switch">
                      <input type="checkbox" onchange="toggleHeroStatus('hero5', this)">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>
                <div class="p-5">
                  <h4 class="font-bold text-slate-900 mb-2">Aniversario</h4>
                  <p class="text-sm text-slate-600 mb-4">Celebra nuestros 10 años contigo</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">1 Oct - 31 Oct 2023</span>
                    <div class="flex gap-2">
                      <button onclick="editHero('hero5')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                      </button>
                      <button onclick="deleteHero('hero5')" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>



  <!-- Hero Modal -->
  <div id="hero-modal" class="modal">
    <div class="modal-content w-full max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900" id="modal-title">Crear Nuevo Hero</h2>
        <button onclick="closeModal('hero-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="hero-form" class="space-y-6">
        <input type="hidden" id="hero-id">
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
            <input type="text" id="hero-title" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Título principal del hero" required>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
            <input type="text" id="hero-subtitle" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Texto secundario (opcional)">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Imagen</label>
          <div class="flex items-center gap-4">
            <div class="relative flex-1">
              <input type="file" id="hero-image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
              <div class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <p class="text-sm text-slate-600" id="file-name">Seleccionar imagen...</p>
              </div>
            </div>
            <button type="button" onclick="document.getElementById('hero-image').click()" class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
              <i data-lucide="upload" class="w-4 h-4"></i>
            </button>
          </div>
          <p class="text-xs text-slate-500 mt-2">Recomendado: 1920x1080 px (relación 16:9)</p>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Inicio</label>
            <input type="date" id="hero-start-date" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Finalización</label>
            <input type="date" id="hero-end-date" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Texto del Botón</label>
          <input type="text" id="hero-button-text" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ej: Ver más">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">URL del Botón</label>
          <input type="url" id="hero-button-url" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="https://ejemplo.com/ruta">
        </div>
        
        <div class="flex items-center justify-between pt-4">
          <div class="flex items-center gap-3">
            <label class="switch">
              <input type="checkbox" id="hero-status" checked>
              <span class="slider"></span>
            </label>
            <span class="text-sm font-medium text-slate-700">Hero Activo</span>
          </div>
          
          <div class="flex gap-4">
            <button type="button" onclick="closeModal('hero-modal')" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
              Cancelar
            </button>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
              Guardar Hero
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Preview Modal -->
  <div id="preview-modal" class="modal">
    <div class="modal-content w-full max-w-5xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Vista Previa del Hero</h2>
        <button onclick="closeModal('preview-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <div class="hero-preview bg-white/60 rounded-xl overflow-hidden shadow-xl">
        <div class="relative h-96 overflow-hidden">
          <img id="preview-image" src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Preview" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-r from-slate-900/40 to-transparent"></div>
          <div class="absolute inset-0 flex items-center">
            <div class="px-12 py-8 max-w-2xl">
              <h3 id="preview-title" class="text-4xl font-bold text-white mb-4">Promoción de Verano</h3>
              <p id="preview-subtitle" class="text-xl text-white/90 mb-6">Descuentos especiales en todos nuestros cursos</p>
              <button id="preview-button" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                Ver Oferta
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="flex justify-end mt-6">
        <button onclick="closeModal('preview-modal')" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
          Cerrar Vista Previa
        </button>
      </div>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Hero data storage (simulating database)
    let heros = [
      {
        id: 'hero1',
        title: 'Promoción de Verano',
        subtitle: 'Descuentos especiales en todos nuestros cursos',
        image: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: '2024-03-15',
        endDate: '2024-04-30',
        buttonText: 'Ver Oferta',
        buttonUrl: '#',
        active: true
      },
      {
        id: 'hero2',
        title: 'Nuevos Cursos',
        subtitle: 'Descubre nuestra oferta académica 2024',
        image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: '2024-01-01',
        endDate: '2024-12-31',
        buttonText: 'Explorar Cursos',
        buttonUrl: '#',
        active: true
      },
      {
        id: 'hero3',
        title: 'Evento Internacional',
        subtitle: 'Conferencia con expertos mundiales',
        image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: '2024-05-10',
        endDate: '2024-05-15',
        buttonText: 'Más Información',
        buttonUrl: '#',
        active: true
      },
      {
        id: 'hero4',
        title: 'Oferta de Invierno',
        subtitle: 'Cursos con descuento para la temporada',
        image: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: '2023-12-01',
        endDate: '2024-01-15',
        buttonText: 'Ver Descuentos',
        buttonUrl: '#',
        active: false
      },
      {
        id: 'hero5',
        title: 'Aniversario',
        subtitle: 'Celebra nuestros 10 años contigo',
        image: 'https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: '2023-10-01',
        endDate: '2023-10-31',
        buttonText: 'Ver Eventos',
        buttonUrl: '#',
        active: false
      }
    ];
    
    // Current editing hero
    let currentHero = null;
    
    // Modal functions
    function openHeroModal(heroId = null) {
      const modal = document.getElementById('hero-modal');
      const form = document.getElementById('hero-form');
      const title = document.getElementById('modal-title');
      
      if (heroId) {
        // Edit mode
        currentHero = heros.find(h => h.id === heroId);
        title.textContent = 'Editar Hero';
        
        // Fill form with hero data
        document.getElementById('hero-id').value = currentHero.id;
        document.getElementById('hero-title').value = currentHero.title;
        document.getElementById('hero-subtitle').value = currentHero.subtitle;
        document.getElementById('hero-start-date').value = currentHero.startDate;
        document.getElementById('hero-end-date').value = currentHero.endDate;
        document.getElementById('hero-button-text').value = currentHero.buttonText;
        document.getElementById('hero-button-url').value = currentHero.buttonUrl;
        document.getElementById('hero-status').checked = currentHero.active;
        document.getElementById('file-name').textContent = 'Imagen actual seleccionada';
      } else {
        // Create mode
        currentHero = null;
        title.textContent = 'Crear Nuevo Hero';
        form.reset();
        document.getElementById('file-name').textContent = 'Seleccionar imagen...';
      }
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('show');
      document.body.style.overflow = 'auto';
    }
    
    function showPreview() {
      const modal = document.getElementById('preview-modal');
      
      // Update preview with form data
      document.getElementById('preview-title').textContent = document.getElementById('hero-title').value || 'Título del Hero';
      document.getElementById('preview-subtitle').textContent = document.getElementById('hero-subtitle').value || 'Subtítulo descriptivo del hero';
      document.getElementById('preview-button').textContent = document.getElementById('hero-button-text').value || 'Ver más';
      
      // Handle image preview
      const fileInput = document.getElementById('hero-image');
      if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('preview-image').src = e.target.result;
        };
        reader.readAsDataURL(fileInput.files[0]);
      }
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    
    // Form submission
    document.getElementById('hero-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = {
        id: document.getElementById('hero-id').value || 'hero' + (heros.length + 1),
        title: document.getElementById('hero-title').value,
        subtitle: document.getElementById('hero-subtitle').value,
        image: currentHero?.image || 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80',
        startDate: document.getElementById('hero-start-date').value,
        endDate: document.getElementById('hero-end-date').value,
        buttonText: document.getElementById('hero-button-text').value,
        buttonUrl: document.getElementById('hero-button-url').value,
        active: document.getElementById('hero-status').checked
      };
      
      // Handle image upload (simulated)
      const fileInput = document.getElementById('hero-image');
      if (fileInput.files && fileInput.files[0]) {
        formData.image = URL.createObjectURL(fileInput.files[0]);
      }
      
      if (currentHero) {
        // Update existing hero
        const index = heros.findIndex(h => h.id === currentHero.id);
        heros[index] = formData;
      } else {
        // Add new hero
        heros.push(formData);
      }
      
      closeModal('hero-modal');
      alert('Hero guardado exitosamente');
      // In a real app, you would refresh the hero list here
    });
    
    // File input display
    document.getElementById('hero-image').addEventListener('change', function() {
      const fileName = this.files[0]?.name || 'Seleccionar imagen...';
      document.getElementById('file-name').textContent = fileName;
    });
    
    // Hero management functions
    function toggleHeroStatus(heroId, checkbox) {
      const hero = heros.find(h => h.id === heroId);
      if (hero) {
        hero.active = checkbox.checked;
        alert(`Hero ${hero.title} ha sido ${hero.active ? 'activado' : 'desactivado'}`);
        // In a real app, you would refresh the hero list here
      }
    }
    
    function editHero(heroId) {
      openHeroModal(heroId);
    }
    
    function deleteHero(heroId) {
      if (confirm('¿Estás seguro de que deseas eliminar este hero? Esta acción no se puede deshacer.')) {
        heros = heros.filter(h => h.id !== heroId);
        alert('Hero eliminado exitosamente');
        // In a real app, you would refresh the hero list here
      }
    }
    
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