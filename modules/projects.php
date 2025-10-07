<section class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)]">
    <div class="pt-24 pb-16 bg-gradient-to-br from-[var(--color-surface-alt)] to-[var(--color-dropdown-hover)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fadeIn">
                <nav class="flex justify-center items-center space-x-2 text-sm text-[var(--color-text-muted)] mb-6">
                    <a href="#" class="hover:text-[var(--color-primary)] transition-colors">Inicio</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-[var(--color-primary)] font-medium">Proyectos</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">
                    Nuestros Proyectos
                </h1>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                    Explora nuestra cartera de proyectos de investigación e innovación que están transformando 
                    la educación superior mediante tecnología de vanguardia.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex flex-wrap gap-3 justify-center" id="filters-container">
                    <button class="px-6 py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-full font-medium hover:opacity-90 transition-opacity flex items-center active-filter" data-filter="all">
                        <i data-lucide="grid" class="w-4 h-4 mr-2"></i>
                        Todos los Proyectos
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center" data-filter="active">
                        <i data-lucide="zap" class="w-4 h-4 mr-2"></i>
                        Activos
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center" data-filter="completed">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                        Completados
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)] rounded-full font-medium hover:bg-[var(--color-surface-alt)] transition-colors flex items-center" data-filter="collaborative">
                        <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                        Colaborativos
                    </button>
                </div>

                <div class="flex items-center gap-4 justify-center">
                    <div class="relative">
                        <select id="sort-select" class="pl-4 pr-10 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] appearance-none bg-[var(--color-surface)] text-[var(--color-text)] text-sm">
                            <option value="recent">Ordenar por: Más reciente</option>
                            <option value="funding">Ordenar por: Financiamiento</option>
                            <option value="duration">Ordenar por: Duración</option>
                            <option value="popularity">Ordenar por: Popularidad</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none"></i>
                    </div>

                    <div class="flex bg-[var(--color-surface-alt)] rounded-lg p-1">
                        <button id="grid-view" class="p-2 rounded-md bg-[var(--color-surface)] shadow-sm">
                            <i data-lucide="grid" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </button>
                        <button id="list-view" class="p-2 rounded-md hover:bg-[var(--color-surface)] transition-colors">
                            <i data-lucide="list" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-[var(--color-border)]">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-heading)] mb-2">Área de Investigación</label>
                        <select id="area-filter" class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm">
                            <option value="all">Todas las áreas</option>
                            <option value="ia">Inteligencia Artificial</option>
                            <option value="biotecnologia">Biotecnología</option>
                            <option value="energia">Energías Renovables</option>
                            <option value="educacion">Educación</option>
                            <option value="salud">Salud</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-heading)] mb-2">Estado</label>
                        <select id="status-filter" class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm">
                            <option value="all">Todos los estados</option>
                            <option value="progress">En progreso</option>
                            <option value="completed">Completado</option>
                            <option value="initial">Fase inicial</option>
                            <option value="paused">En pausa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-heading)] mb-2">Financiamiento</label>
                        <select id="funding-filter" class="w-full px-3 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm">
                            <option value="all">Todos los tipos</option>
                            <option value="public">Público</option>
                            <option value="private">Privado</option>
                            <option value="mixed">Mixto</option>
                            <option value="international">Internacional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-heading)] mb-2">Buscar Proyecto</label>
                        <div class="relative">
                            <input id="search-input" type="text" placeholder="Nombre del proyecto..." class="w-full pl-10 pr-4 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="activos" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    En Desarrollo
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Proyectos Activos</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Investigaciones en curso que están marcando la diferencia en la educación superior
                </p>
            </div>

            <div id="projects-container" class="grid lg:grid-cols-2 xl:grid-cols-3 gap-8">
            </div>

            <div class="text-center mt-12">
                <button id="load-more" class="px-8 py-3 border-2 border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors font-semibold">
                    Cargar Más Proyectos
                </button>
            </div>
        </div>
    </section>
</section>

<script>
    const projectsData = [
        {
            id: 1,
            title: "Sistema de IA para Diagnóstico Médico",
            description: "Desarrollo de algoritmos de machine learning para el diagnóstico temprano de enfermedades mediante análisis de imágenes médicas.",
            status: "progress",
            area: "ia",
            funding: "public",
            duration: "12 meses",
            budget: "$150,000",
            progress: 75,
            researchers: 8,
            rating: 4.9,
            tags: ["Machine Learning", "Salud", "Python"],
            image: "https://images.unsplash.com/photo-1485827404703-89b55fcc595e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80",
            leader: "Dr. Carlos Mendoza",
            collaborative: false
        },
        {
            id: 2,
            title: "Optimización de Paneles Solares con IA",
            description: "Aplicación de algoritmos de inteligencia artificial para optimizar la eficiencia de paneles solares en diferentes condiciones climáticas.",
            status: "progress",
            area: "energia",
            funding: "private",
            duration: "24 meses",
            budget: "$300,000",
            progress: 60,
            researchers: 10,
            rating: 4.8,
            tags: ["IA", "Energía Solar", "Optimización"],
            image: "https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80",
            leader: "Dr. Roberto Sánchez",
            collaborative: true
        },
        {
            id: 3,
            title: "Bioplásticos a partir de Desechos Agrícolas",
            description: "Desarrollo de materiales biodegradables a partir de residuos de cosecha para reducir la contaminación por plásticos convencionales.",
            status: "completed",
            area: "biotecnologia",
            funding: "mixed",
            duration: "18 meses",
            budget: "$200,000",
            progress: 100,
            researchers: 6,
            rating: 4.7,
            tags: ["Sostenibilidad", "Materiales", "Biodegradable"],
            image: "https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80",
            leader: "Dra. Laura González",
            collaborative: true
        },
        {
            id: 4,
            title: "Plataforma Educativa con Realidad Aumentada",
            description: "Desarrollo de una plataforma educativa interactiva que utiliza realidad aumentada para mejorar el aprendizaje.",
            status: "progress",
            area: "educacion",
            funding: "public",
            duration: "15 meses",
            budget: "$180,000",
            progress: 40,
            researchers: 7,
            rating: 4.6,
            tags: ["Educación", "Realidad Aumentada", "Tecnología"],
            image: "https://plus.unsplash.com/premium_photo-1710118990459-07ad42731385?q=80&w=1234&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
            leader: "Dra. María López",
            collaborative: false
        },
        {
            id: 5,
            title: "Sistema de Monitoreo de Salud Remoto",
            description: "Desarrollo de un sistema IoT para monitoreo remoto de pacientes con enfermedades crónicas.",
            status: "initial",
            area: "salud",
            funding: "private",
            duration: "20 meses",
            budget: "$250,000",
            progress: 20,
            researchers: 9,
            rating: 4.5,
            tags: ["IoT", "Salud", "Tecnología"],
            image: "https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80",
            leader: "Dr. Javier Rodríguez",
            collaborative: true
        },
        {
            id: 6,
            title: "Algoritmos para Diagnóstico de Cáncer",
            description: "Investigación y desarrollo de algoritmos de deep learning para diagnóstico temprano de cáncer.",
            status: "completed",
            area: "ia",
            funding: "international",
            duration: "30 meses",
            budget: "$500,000",
            progress: 100,
            researchers: 12,
            rating: 4.9,
            tags: ["Deep Learning", "Salud", "Investigación"],
            image: "https://images.unsplash.com/photo-1559757175-0eb30cd8c063?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80",
            leader: "Dra. Ana Martínez",
            collaborative: true
        }
    ];

    let currentFilter = 'all';
    let currentSort = 'recent';
    let currentView = 'grid';
    let displayedProjects = 6;

    function initApp() {
        renderProjects();
        setupEventListeners();
        updateViewButtons();
    }

    function setupEventListeners() {
        document.getElementById('sort-select').addEventListener('change', (e) => {
            currentSort = e.target.value;
            renderProjects();
        });

        document.getElementById('area-filter').addEventListener('change', () => {
            renderProjects();
        });

        document.getElementById('status-filter').addEventListener('change', () => {
            renderProjects();
        });

        document.getElementById('funding-filter').addEventListener('change', () => {
            renderProjects();
        });

        document.getElementById('search-input').addEventListener('input', () => {
            renderProjects();
        });

        document.getElementById('load-more').addEventListener('click', () => {
            displayedProjects += 3;
            renderProjects();
        });

        document.getElementById('grid-view').addEventListener('click', () => {
            currentView = 'grid';
            updateViewButtons();
            renderProjects();
        });

        document.getElementById('list-view').addEventListener('click', () => {
            currentView = 'list';
            updateViewButtons();
            renderProjects();
        });

        document.querySelectorAll('#filters-container button').forEach(button => {
            button.addEventListener('click', (e) => {
                currentFilter = e.target.dataset.filter;
                updateFilterButtons();
                renderProjects();
            });
        });
    }

    function updateFilterButtons() {
        document.querySelectorAll('#filters-container button').forEach(button => {
            if (button.dataset.filter === currentFilter) {
                button.classList.add('active-filter');
                button.style.background = 'linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%)';
                button.style.color = 'white';
            } else {
                button.classList.remove('active-filter');
                button.style.background = 'var(--color-surface)';
                button.style.color = 'var(--color-text-muted)';
            }
        });
    }

    function updateViewButtons() {
        const gridBtn = document.getElementById('grid-view');
        const listBtn = document.getElementById('list-view');
        
        if (currentView === 'grid') {
            gridBtn.style.backgroundColor = 'var(--color-surface)';
            gridBtn.querySelector('i').style.color = 'var(--color-primary)';
            listBtn.style.backgroundColor = 'transparent';
            listBtn.querySelector('i').style.color = 'var(--color-text-muted)';
        } else {
            listBtn.style.backgroundColor = 'var(--color-surface)';
            listBtn.querySelector('i').style.color = 'var(--color-primary)';
            gridBtn.style.backgroundColor = 'transparent';
            gridBtn.querySelector('i').style.color = 'var(--color-text-muted)';
        }
    }

    function filterProjects() {
        const areaFilter = document.getElementById('area-filter').value;
        const statusFilter = document.getElementById('status-filter').value;
        const fundingFilter = document.getElementById('funding-filter').value;
        const searchTerm = document.getElementById('search-input').value.toLowerCase();

        return projectsData.filter(project => {
            if (currentFilter !== 'all') {
                if (currentFilter === 'active' && project.status !== 'progress') return false;
                if (currentFilter === 'completed' && project.status !== 'completed') return false;
                if (currentFilter === 'collaborative' && !project.collaborative) return false;
            }

            if (areaFilter !== 'all' && project.area !== areaFilter) return false;
            if (statusFilter !== 'all' && project.status !== statusFilter) return false;
            if (fundingFilter !== 'all' && project.funding !== fundingFilter) return false;
            if (searchTerm && !project.title.toLowerCase().includes(searchTerm) && 
                !project.description.toLowerCase().includes(searchTerm)) return false;

            return true;
        });
    }

    function sortProjects(projects) {
        return projects.sort((a, b) => {
            switch (currentSort) {
                case 'recent':
                    return b.id - a.id;
                case 'funding':
                    return parseFloat(b.budget.replace(/[^0-9.]/g, '')) - parseFloat(a.budget.replace(/[^0-9.]/g, ''));
                case 'duration':
                    return parseInt(b.duration) - parseInt(a.duration);
                case 'popularity':
                    return b.rating - a.rating;
                default:
                    return 0;
            }
        });
    }

    function renderProjects() {
        const container = document.getElementById('projects-container');
        const filteredProjects = filterProjects();
        const sortedProjects = sortProjects(filteredProjects);
        const projectsToShow = sortedProjects.slice(0, displayedProjects);

        if (currentView === 'grid') {
            container.className = 'grid lg:grid-cols-2 xl:grid-cols-3 gap-8';
        } else {
            container.className = 'grid grid-cols-1 gap-6';
        }

        container.innerHTML = projectsToShow.map(project => createProjectCard(project)).join('');

        document.getElementById('load-more').style.display = 
            displayedProjects < filteredProjects.length ? 'block' : 'none';

        lucide.createIcons();
    }

    function createProjectCard(project) {
        const statusColors = {
            progress: { bg: 'var(--color-surface-alt)', text: 'var(--color-primary)' },
            completed: { bg: 'var(--color-surface-alt)', text: 'var(--color-success)' },
            initial: { bg: 'var(--color-surface-alt)', text: 'var(--color-warning)' },
            paused: { bg: 'var(--color-surface-alt)', text: 'var(--color-danger)' }
        };

        const areaColors = {
            ia: { bg: 'var(--color-surface-alt)', text: 'var(--color-secondary)' },
            biotecnologia: { bg: 'var(--color-surface-alt)', text: 'var(--color-success)' },
            energia: { bg: 'var(--color-surface-alt)', text: 'var(--color-success)' },
            educacion: { bg: 'var(--color-surface-alt)', text: 'var(--color-primary)' },
            salud: { bg: 'var(--color-surface-alt)', text: 'var(--color-danger)' }
        };

        const statusText = {
            progress: 'En progreso',
            completed: 'Completado',
            initial: 'Fase inicial',
            paused: 'En pausa'
        };

        const areaText = {
            ia: 'IA',
            biotecnologia: 'Biotecnología',
            energia: 'Energía',
            educacion: 'Educación',
            salud: 'Salud'
        };

        if (currentView === 'grid') {
            return `
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                    <div class="relative overflow-hidden">
                        <img src="${project.image}" alt="${project.title}" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-3 py-1 bg-white/90 text-[var(--color-text)] rounded-full text-sm font-medium">
                                <i data-lucide="star" class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400"></i>
                                ${project.rating}
                            </span>
                        </div>
                        <div class="absolute bottom-4 left-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: ${statusColors[project.status].bg}; color: ${statusColors[project.status].text};">
                                ${statusText[project.status]}
                            </span>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: ${areaColors[project.area].bg}; color: ${areaColors[project.area].text};">
                                ${areaText[project.area]}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3 group-hover:text-[var(--color-primary)] transition-colors">${project.title}</h3>
                        <p class="text-[var(--color-text-muted)] mb-4 leading-relaxed line-clamp-3">${project.description}</p>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-[var(--color-primary)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                ${project.leader.split(' ').map(n => n[0]).join('')}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-[var(--color-heading)]">${project.leader}</p>
                                <p class="text-xs text-[var(--color-text-muted)]">Director del Proyecto</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            ${project.tags.map(tag => `<span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text-muted)] rounded text-xs">${tag}</span>`).join('')}
                        </div>
                        <div class="grid grid-cols-3 gap-4 py-3 border-t border-[var(--color-border)]">
                            <div class="text-center">
                                <div class="text-lg font-bold text-[var(--color-primary)]">${project.researchers}</div>
                                <div class="text-xs text-[var(--color-text-muted)]">Investigadores</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-[var(--color-secondary)]">${project.duration.split(' ')[0]}</div>
                                <div class="text-xs text-[var(--color-text-muted)]">Meses</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-[var(--color-success)]">${project.progress}%</div>
                                <div class="text-xs text-[var(--color-text-muted)]">Completado</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-[var(--color-text-muted)] mb-1">
                                <span>Progreso</span>
                                <span>${project.progress}%</span>
                            </div>
                            <div class="w-full bg-[var(--color-surface-alt)] rounded-full h-2">
                                <div class="bg-[var(--color-success)] h-2 rounded-full" style="width: ${project.progress}%"></div>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-6">
                            <button class="flex-1 py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-medium">
                                Ver Detalles
                            </button>
                            <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                                <i data-lucide="bookmark" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-500 flex">
                    <div class="w-1/3 flex-shrink-0">
                        <img src="${project.image}" alt="${project.title}" class="w-full h-full object-cover">
                    </div>
                    <div class="w-2/3 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-[var(--color-heading)] group-hover:text-[var(--color-primary)] transition-colors">${project.title}</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: ${statusColors[project.status].bg}; color: ${statusColors[project.status].text};">
                                    ${statusText[project.status]}
                                </span>
                            </div>
                            <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">${project.description}</p>
                            <div class="flex flex-wrap gap-2 mb-4">
                                ${project.tags.map(tag => `<span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text-muted)] rounded text-xs">${tag}</span>`).join('')}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2 text-sm text-[var(--color-text-muted)]">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                    <span>${project.researchers} investigadores</span>
                                </div>
                                <div class="flex items-center space-x-2 text-sm text-[var(--color-text-muted)]">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>${project.duration}</span>
                                </div>
                                <div class="flex items-center space-x-2 text-sm text-[var(--color-text-muted)]">
                                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                    <span>${project.budget}</span>
                                </div>
                            </div>
                            <button class="py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-medium">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initApp();
    });
</script>