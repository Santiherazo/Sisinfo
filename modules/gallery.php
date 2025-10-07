<section id="galeria-completa" class="min-h-screen bg-[var(--color-bg)]">
    <div class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fadeIn">
                <nav class="flex justify-center items-center space-x-2 text-sm text-[var(--color-text-muted)] mb-6">
                    <a href="#" class="hover:text-[var(--color-primary)] transition-colors">Inicio</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-[var(--color-primary)] font-medium">Galería</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">
                    Galería
                </h1>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                    Explora todos los momentos, eventos y logros que han marcado nuestra trayectoria 
                    en la transformación de la evaluación académica.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)] animate-slideUp">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex flex-wrap gap-3">
                    <button class="px-6 py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-full font-medium hover:opacity-90 transition-opacity flex items-center">
                        <i data-lucide="grid" class="w-4 h-4 mr-2"></i>
                        Todos los Items
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                        Eventos
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center">
                        <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                        Premios
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center">
                        <i data-lucide="microscope" class="w-4 h-4 mr-2"></i>
                        Investigación
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative">
                        <select class="pl-4 pr-10 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] appearance-none bg-[var(--color-surface)] text-[var(--color-text)]">
                            <option>Ordenar por: Más reciente</option>
                            <option>Ordenar por: Más antiguo</option>
                            <option>Ordenar por: Más popular</option>
                            <option>Ordenar por: Alfabético</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none"></i>
                    </div>

                    <div class="flex bg-[var(--color-surface-alt)] rounded-lg p-1">
                        <button class="p-2 rounded-md bg-[var(--color-surface)] shadow-sm">
                            <i data-lucide="grid" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </button>
                        <button class="p-2 rounded-md hover:bg-[var(--color-surface)]/50 transition-colors">
                            <i data-lucide="list" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-[var(--color-border)]">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Año</label>
                        <select class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)]">
                            <option>Todos los años</option>
                            <option>2024</option>
                            <option>2023</option>
                            <option>2022</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Categoría</label>
                        <select class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)]">
                            <option>Todas las categorías</option>
                            <option>Conferencias</option>
                            <option>Talleres</option>
                            <option>Ferias</option>
                            <option>Premiaciones</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Ubicación</label>
                        <select class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)]">
                            <option>Todas las ubicaciones</option>
                            <option>Auditorio Principal</option>
                            <option>Laboratorios</option>
                            <option>Plaza Central</option>
                            <option>Sedes Externas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Buscar</label>
                        <div class="relative">
                            <input type="text" placeholder="Buscar en galería..." class="w-full pl-10 pr-4 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)]">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div id="gallery-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        </div>

        <div class="flex justify-center items-center space-x-2 mt-12">
            <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <button class="w-10 h-10 flex items-center justify-center bg-[var(--color-primary)] text-white rounded-lg font-medium">1</button>
            <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">2</button>
            <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">3</button>
            <span class="px-2 text-[var(--color-text-muted)]">...</span>
            <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">8</button>
            <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <div class="bg-[var(--color-surface)] border-t border-[var(--color-border)] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-[var(--color-heading)] mb-4">Nuestra Galería en Números</h2>
                <p class="text-[var(--color-text-muted)] max-w-2xl mx-auto">
                    Un vistazo a la riqueza visual de nuestra trayectoria académica y los momentos que han marcado nuestra historia.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-[var(--color-primary)] mb-2">500+</div>
                    <div class="text-[var(--color-text-muted)]">Imágenes</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-[var(--color-secondary)] mb-2">120</div>
                    <div class="text-[var(--color-text-muted)]">Eventos Registrados</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-[var(--color-success)] mb-2">25</div>
                    <div class="text-[var(--color-text-muted)]">Premios y Reconocimientos</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-[var(--color-accent)] mb-2">15K+</div>
                    <div class="text-[var(--color-text-muted)]">Visualizaciones Totales</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">¿Tienes contenido para nuestra galería?</h2>
            <p class="text-blue-100 mb-8 text-lg">
                Comparte tus fotos y eventos académicos para enriquecer nuestra comunidad visual.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="px-8 py-3 bg-white text-[var(--color-primary)] rounded-lg font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                    <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                    Subir Contenido
                </button>
                <button class="px-8 py-3 bg-white/20 text-white rounded-lg font-semibold hover:bg-white/30 transition-colors flex items-center justify-center backdrop-blur-sm">
                    <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                    Contactar al Administrador
                </button>
            </div>
        </div>
    </div>
</section>

<div id="image-modal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-6xl max-h-full">
        <button id="close-modal" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <div class="bg-[var(--color-surface)] rounded-2xl overflow-hidden">
            <div class="grid lg:grid-cols-2">
                <div class="aspect-square bg-[var(--color-surface-alt)]">
                    <img id="modal-image" src="" alt="" class="w-full h-full object-cover">
                </div>
                <div class="p-8">
                    <div class="flex items-center space-x-2 mb-4">
                        <span id="modal-category" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"></span>
                        <span id="modal-date" class="text-sm text-[var(--color-text-muted)]"></span>
                    </div>
                    <h3 id="modal-title" class="text-2xl font-bold text-[var(--color-heading)] mb-4"></h3>
                    <p id="modal-description" class="text-[var(--color-text-muted)] mb-6 leading-relaxed"></p>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                            <span id="modal-location" class="text-[var(--color-text)]"></span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i data-lucide="users" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                            <span id="modal-participants" class="text-[var(--color-text)]"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-[var(--color-border)]">
                        <div class="flex items-center space-x-4 text-sm text-[var(--color-text-muted)]">
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span id="modal-views">0</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="heart" class="w-4 h-4"></i>
                                <span id="modal-likes">0</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                                <i data-lucide="download" class="w-5 h-5"></i>
                            </button>
                            <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                                <i data-lucide="share-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const galleryItems = [
        {
            id: 1,
            image: "https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Evento",
            categoryColor: "bg-blue-100 text-blue-800",
            date: "15 Mar 2024",
            title: "Simposio Internacional de Innovación Educativa 2024",
            description: "Conferencia magistral con expertos internacionales en educación superior y transformación digital",
            location: "Auditorio Principal - Campus Central",
            participants: "250+ asistentes",
            views: 1250,
            likes: 89
        },
        {
            id: 2,
            image: "https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Estudiantes",
            categoryColor: "bg-green-100 text-green-800",
            date: "10 Feb 2024",
            title: "Feria Anual de Proyectos Innovadores",
            description: "Exposición de trabajos destacados desarrollados por estudiantes de ingeniería y ciencias",
            location: "Plaza Central Universitaria",
            participants: "500+ visitantes",
            views: 890,
            likes: 67
        },
        {
            id: 3,
            image: "https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Premio",
            categoryColor: "bg-yellow-100 text-yellow-800",
            date: "05 Ene 2024",
            title: "Premio Nacional a la Innovación Educativa",
            description: "Reconocimiento a nuestro sistema de evaluación académica basado en competencias",
            location: "Centro de Convenciones Nacional",
            participants: "300+ invitados",
            views: 2100,
            likes: 156
        },
        {
            id: 4,
            image: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Investigación",
            categoryColor: "bg-purple-100 text-purple-800",
            date: "20 Dic 2023",
            title: "Presentación de Avances en IA Educativa",
            description: "Demostración de algoritmos de machine learning aplicados a la evaluación formativa",
            location: "Laboratorio de Computación Avanzada",
            participants: "45 investigadores",
            views: 780,
            likes: 42
        },
        {
            id: 5,
            image: "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Evento",
            categoryColor: "bg-blue-100 text-blue-800",
            date: "30 Nov 2023",
            title: "Workshop de Metodologías Activas",
            description: "Taller práctico sobre flipped classroom y aprendizaje basado en proyectos",
            location: "Aula Magna - Edificio de Pedagogía",
            participants: "80 docentes",
            views: 650,
            likes: 38
        },
        {
            id: 6,
            image: "https://images.unsplash.com/photo-1551836026-d5c088a9f7b2?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Estudiantes",
            categoryColor: "bg-green-100 text-green-800",
            date: "15 Nov 2023",
            title: "Hackathon de Soluciones Educativas",
            description: "Competencia de 48 horas para desarrollar herramientas tecnológicas educativas",
            location: "Centro de Innovación Tecnológica",
            participants: "120 estudiantes",
            views: 920,
            likes: 71
        },
        {
            id: 7,
            image: "https://images.unsplash.com/photo-1541336032412-2048a678540d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Premio",
            categoryColor: "bg-yellow-100 text-yellow-800",
            date: "08 Oct 2023",
            title: "Reconocimiento a la Excelencia Docente",
            description: "Ceremonia de premiación a los mejores profesores del año académico",
            location: "Teatro Universitario",
            participants: "200+ miembros académicos",
            views: 1100,
            likes: 93
        },
        {
            id: 8,
            image: "https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=450&q=80",
            category: "Investigación",
            categoryColor: "bg-purple-100 text-purple-800",
            date: "25 Sep 2023",
            title: "Lanzamiento de Plataforma Analytics",
            description: "Presentación del sistema de análisis de datos para seguimiento estudiantil",
            location: "Sala de Conferencias TIC",
            participants: "60 especialistas",
            views: 870,
            likes: 55
        }
    ];

    function renderGallery() {
        const grid = document.getElementById('gallery-grid');
        grid.innerHTML = '';

        galleryItems.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 cursor-pointer bg-[var(--color-surface)]';
            itemElement.innerHTML = `
                <div class="aspect-[4/3] overflow-hidden">
                    <img 
                        src="${item.image}" 
                        alt="${item.title}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    >
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 ${item.categoryColor} text-xs rounded-full font-medium">${item.category}</span>
                        <span class="text-xs text-[var(--color-text-muted)]">${item.date}</span>
                    </div>
                    <h3 class="font-semibold text-[var(--color-heading)] mb-2 line-clamp-2">${item.title}</h3>
                    <p class="text-sm text-[var(--color-text-muted)] line-clamp-2">${item.description}</p>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                    <div class="text-white">
                        <div class="flex items-center space-x-4 text-sm mb-2">
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>${item.views}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="heart" class="w-4 h-4"></i>
                                <span>${item.likes}</span>
                            </div>
                        </div>
                        <button class="w-full py-2 bg-white text-[var(--color-primary)] rounded-lg font-medium hover:bg-gray-100 transition-colors view-details" data-id="${item.id}">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(itemElement);
        });

        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const itemId = parseInt(button.getAttribute('data-id'));
                openModal(itemId);
            });
        });

        document.querySelectorAll('.group').forEach(item => {
            item.addEventListener('click', function() {
                const itemId = parseInt(this.querySelector('.view-details').getAttribute('data-id'));
                openModal(itemId);
            });
        });
    }

    function openModal(itemId) {
        const item = galleryItems.find(i => i.id === itemId);
        if (!item) return;

        document.getElementById('modal-image').src = item.image;
        document.getElementById('modal-category').textContent = item.category;
        document.getElementById('modal-category').className = `px-3 py-1 ${item.categoryColor} rounded-full text-sm font-medium`;
        document.getElementById('modal-date').textContent = item.date;
        document.getElementById('modal-title').textContent = item.title;
        document.getElementById('modal-description').textContent = item.description;
        document.getElementById('modal-location').textContent = item.location;
        document.getElementById('modal-participants').textContent = item.participants;
        document.getElementById('modal-views').textContent = item.views;
        document.getElementById('modal-likes').textContent = item.likes;

        const modal = document.getElementById('image-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderGallery();
        
        const modal = document.getElementById('image-modal');
        const closeModal = document.getElementById('close-modal');

        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>