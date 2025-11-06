<?php
if (!mconfig('active')) throw new Exception('Este módulo se encuentra deshabilitado.');
?>

<!-- Hero Section con Slider -->
<section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[var(--color-bg)]">
    <div class="hero-slider absolute inset-0">
        <!-- Slide 1 -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-100 transition-opacity duration-1000" style="background-image: url('<?php echo __PATH_IMG__ . 'hero/hero_1.jpg'; ?>');">
            <div class="absolute inset-0 bg-[var(--color-navbar-bg)]/60"></div>
        </div>
        
        <!-- Slide 2 -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-0 transition-opacity duration-1000" style="background-image: url('<?php echo __PATH_IMG__ . 'hero/hero_2.jpg'; ?>');">
            <div class="absolute inset-0 bg-[var(--color-navbar-bg)]/60"></div>
        </div>
        
        <!-- Slide 3 -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-0 transition-opacity duration-1000" style="background-image: url('<?php echo __PATH_IMG__ . 'hero/hero_3.jpg'; ?>');">
            <div class="absolute inset-0 bg-[var(--color-navbar-bg)]/60"></div>
        </div>
    </div>

    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-20 h-20 bg-[var(--color-primary)]/20 rounded-full blur-xl animate-float"></div>
        <div class="absolute top-40 right-20 w-32 h-32 bg-[var(--color-secondary)]/20 rounded-full blur-xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-[var(--color-accent)]/20 rounded-full blur-xl animate-float" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative z-10 text-center text-white max-w-6xl mx-auto px-4">
        <div class="animate-fadeIn">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                    Semillero de investigación en Soluciones Informáticas
                </span>
            </h1>

            <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-4xl mx-auto leading-relaxed">
                Brindar soluciones informáticas a problemáticas de la región y el país.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                <a href="/us" class="text-lg px-8 py-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-all hover:scale-105 transform flex items-center">
                    Nosotros
                </a>
                <a href="/projects" class="text-lg px-8 py-4 bg-white/10 backdrop-blur-md border border-white/30 hover:bg-white/20 rounded-lg flex items-center">
                    Ver Proyectos
                </a>
            </div>
        </div>
    </div>

    <!-- Controles del Slider -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
        <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-colors" data-slide="0"></button>
        <button class="slider-dot w-3 h-3 rounded-full bg-white/30 hover:bg-white transition-colors" data-slide="1"></button>
        <button class="slider-dot w-3 h-3 rounded-full bg-white/30 hover:bg-white transition-colors" data-slide="2"></button>
    </div>

    <div class="absolute bottom-8 right-8 transform animate-bounce z-20">
        <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
            <div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>
</section>

<!-- Sección Nosotros -->
<section id="about" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn">
            <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm mb-4">Sobre Nosotros</span>
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Transformando la Educación Superior</h2>
            <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                Plataforma integral para la evaluación y gestión de proyectos académicos universitarios. Nuestra plataforma conecta investigadores, evaluadores y administradores en un ecosistema digital diseñado para la excelencia académica.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-8 text-center animate-slideUp hover:-translate-y-2 hover:shadow-xl transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-blue-100 flex items-center justify-center text-[var(--color-primary)]">
                    <i data-lucide="target" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-[var(--color-heading)]">Misión</h3>
                <p class="text-[var(--color-text-muted)] leading-relaxed">“SISINFO¨ (Semillero de investigación en Soluciones Informáticas) es un semillero que busca brindar soluciones informáticas a problemáticas de la región y el país teniendo en cuenta relaciones de Ciencia Tecnología- Sociedad –Ambiente (CTSA), donde el estudiante adquiera competencias científicas, tecnológicas y ciudadanas que favorecen su participación en las organizaciones de ámbito educativo y empresarial.</p>
            </div>
            <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-8 text-center animate-slideUp hover:-translate-y-2 hover:shadow-xl transition-all duration-300" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-purple-100 flex items-center justify-center text-[var(--color-secondary)]">
                    <i data-lucide="eye" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-[var(--color-heading)]">Visión</h3>
                <p class="text-[var(--color-text-muted)] leading-relaxed">Para el año 2030 el semillero de investigación SISINFO será reconocido en la región del Magdalena Medio por ser pionero en ofrecer soluciones informáticas a problemas del contexto, brindando a sus miembros conocimientos y destrezas generados por la aplicación de la ciencia, la tecnología y la ingeniería en la solución de problemas.</p>
            </div>
            <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-8 text-center animate-slideUp hover:-translate-y-2 hover:shadow-xl transition-all duration-300" style="animation-delay: 0.4s;">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-red-100 flex items-center justify-center text-[var(--color-accent)]">
                    <i data-lucide="heart" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-[var(--color-heading)]">Objetivo</h3>
                <p class="text-[var(--color-text-muted)] leading-relaxed">Brindar soluciones informáticas a problemáticas de la región y el país teniendo en cuenta relaciones CTSA, mediante el uso de estrategias, métodos, y metodologías propias de la ingeniería informática</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="flex items-center space-x-4 p-6 bg-[var(--color-surface)] rounded-xl shadow-sm hover:-translate-y-2 hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-1 text-[var(--color-heading)]">Eficiencia</h4>
                    <p class="text-sm text-[var(--color-text-muted)]">Procesos automatizados y optimizados</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-6 bg-[var(--color-surface)] rounded-xl shadow-sm hover:-translate-y-2 hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-1 text-[var(--color-heading)]">Seguridad</h4>
                    <p class="text-sm text-[var(--color-text-muted)]">Datos protegidos con estándares internacionales</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-6 bg-[var(--color-surface)] rounded-xl shadow-sm hover:-translate-y-2 hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="globe" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-1 text-[var(--color-heading)]">Accesibilidad</h4>
                    <p class="text-sm text-[var(--color-text-muted)]">Plataforma disponible 24/7 desde cualquier lugar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Proyectos -->
<section id="proyectos" class="py-16 lg:py-24 bg-[var(--color-bg)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn">
            <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm mb-4">Proyectos de Investigación</span>
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Proyectos Destacados</h2>
            <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                Investigaciones multidisciplinarias trabajando en proyectos de vanguardia que transforman el conocimiento en soluciones reales.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Inteligencia Artificial" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2 py-1 bg-white/90 text-black rounded text-sm">
                            <i data-lucide="star" class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400"></i>
                            4.9
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-blue-100 text-[var(--color-primary)] rounded text-sm">En progreso</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors text-[var(--color-heading)]">Sistema de IA para Diagnóstico Médico</h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Desarrollo de algoritmos de machine learning para el diagnóstico temprano de enfermedades</p>

                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-[var(--color-primary)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                            CM
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--color-heading)]">Dr. Carlos Mendoza</p>
                            <p class="text-xs text-[var(--color-text-muted)]">Director del Proyecto</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1 mb-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Machine Learning</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Salud</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Python</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 mb-4">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">8 investigadores</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">12 meses</span>
                        </div>
                    </div>

                    <button class="w-full py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                        Ver detalles
                        <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                    </button>
                </div>
            </div>

            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.2s;">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Biotecnología" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2 py-1 bg-white/90 text-black rounded text-sm">
                            <i data-lucide="star" class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400"></i>
                            4.8
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-green-100 text-[var(--color-success)] rounded text-sm">Completado</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors text-[var(--color-heading)]">Bioplásticos a partir de Desechos Agrícolas</h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Desarrollo de materiales biodegradables a partir de residuos de cosecha para reducir la contaminación</p>

                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-[var(--color-success)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                            AR
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--color-heading)]">Dra. Ana Rodríguez</p>
                            <p class="text-xs text-[var(--color-text-muted)]">Directora del Proyecto</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1 mb-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Sostenibilidad</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Materiales</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Química</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 mb-4">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">6 investigadores</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">18 meses</span>
                        </div>
                    </div>

                    <button class="w-full py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                        Ver detalles
                        <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                    </button>
                </div>
            </div>

            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.4s;">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Energías Renovables" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2 py-1 bg-white/90 text-black rounded text-sm">
                            <i data-lucide="star" class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400"></i>
                            4.7
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-yellow-100 text-[var(--color-warning)] rounded text-sm">Fase inicial</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors text-[var(--color-heading)]">Optimización de Paneles Solares con Nanotecnología</h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Investigación en materiales nanoestructurados para mejorar la eficiencia de captación solar</p>

                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-[var(--color-warning)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                            PS
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--color-heading)]">Ing. Pedro Silva</p>
                            <p class="text-xs text-[var(--color-text-muted)]">Director del Proyecto</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1 mb-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Energía</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Nanotecnología</span>
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-xs">Física</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 mb-4">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">5 investigadores</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm text-[var(--color-text)]">24 meses</span>
                        </div>
                    </div>

                    <button class="w-full py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                        Ver detalles
                        <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <button class="px-6 py-3 border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-primary)]/10 transition-colors flex items-center mx-auto">
                Ver todos los proyectos
                <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
            </button>
        </div>
    </div>
</section>

<!-- Nueva Sección de Equipos -->
<section id="equipos" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn">
            <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm mb-4">Nuestros Equipos</span>
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Conoce Nuestros Equipos</h2>
            <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                Profesionales multidisciplinarios comprometidos con la excelencia académica y la innovación en investigación.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center bg-[var(--color-surface)] rounded-xl shadow-md p-6 hover:-translate-y-2 hover:shadow-xl transition-all duration-300 animate-slideUp">
                <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center overflow-hidden">
                    <img src="<?php echo __PATH_IMG__ . "Chaparro.jpg"?>" alt="Carlos Mendoza" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-[var(--color-heading)]">Daniel Chaparro Martinez</h3>
                <p class="text-[var(--color-primary)] mb-2">Estudiante Principal</p>
                <p class="text-[var(--color-text-muted)] text-sm mb-4">Descripción</p>
                <div class="flex justify-center space-x-3">
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="linkedin" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>

            <div class="text-center bg-[var(--color-surface)] rounded-xl shadow-md p-6 hover:-translate-y-2 hover:shadow-xl transition-all duration-300 animate-slideUp" style="animation-delay: 0.2s;">
                <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-100 to-teal-100 flex items-center justify-center overflow-hidden">
                    <img src="<?php echo __PATH_IMG__ . "lopez.jpg"?>" alt="Cipriano López Vides" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold mb-2 text-[var(--color-heading)]">Cipriano López Vides</h3>
                <p class="text-[var(--color-primary)] mb-2">Docente Acompañante</p>
                <p class="text-[var(--color-text-muted)] text-sm mb-4">Descripción</p>
                <div class="flex justify-center space-x-3">
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="linkedin" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <button class="px-6 py-3 border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-primary)]/10 transition-colors flex items-center mx-auto">
                Conoce todo el equipo
                <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
            </button>
        </div>
    </div>
</section>

<!-- Sección Eventos -->
<section id="eventos" class="py-16 lg:py-24 bg-[var(--color-bg)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn">
            <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm mb-4">Eventos y Convocatorias</span>
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Próximos Eventos Académicos</h2>
            <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                Participa en conferencias, talleres y eventos que enriquecen tu experiencia académica y profesional en el mundo de la investigación.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Simposio IA" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="px-2 py-1 bg-green-100 text-[var(--color-success)] rounded text-sm">Inscripciones Abiertas</span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-sm">Conferencia</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                        Simposio Internacional de IA
                    </h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Conferencia magistral sobre los últimos avances en inteligencia artificial y sus aplicaciones</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">lunes, 15 de abril de 2024</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="clock" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">09:00 AM</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="map-pin" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">Auditorio Principal</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">250 participantes esperados</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[var(--color-border)] mb-4">
                        <p class="text-sm text-[var(--color-text-muted)]">Presentador:</p>
                        <p class="font-medium text-[var(--color-heading)]">Dr. Elena Vásquez</p>
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button class="flex-1 py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                            Inscribirse
                            <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                        </button>
                        <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                            <i data-lucide="bell" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.2s;">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Workshop" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="px-2 py-1 bg-yellow-100 text-[var(--color-warning)] rounded text-sm">Últimos Cupos</span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-sm">Taller</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                        Workshop: Metodologías de Investigación
                    </h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Taller práctico sobre metodologías avanzadas para proyectos de investigación académica</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">lunes, 22 de abril de 2024</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="clock" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">02:00 PM</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="map-pin" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">Laboratorio 301</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">50 participantes esperados</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[var(--color-border)] mb-4">
                        <p class="text-sm text-[var(--color-text-muted)]">Presentador:</p>
                        <p class="font-medium text-[var(--color-heading)]">Dr. Miguel Torres</p>
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button class="flex-1 py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                            Inscribirse
                            <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                        </button>
                        <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                            <i data-lucide="bell" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.4s;">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1559223607-a43c990c692c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=300&q=80" alt="Feria" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span class="px-2 py-1 bg-blue-100 text-[var(--color-primary)] rounded text-sm">Próximamente</span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 bg-[var(--color-surface-alt)] text-[var(--color-text)] rounded text-sm">Feria</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                        Feria de Proyectos Estudiantiles
                    </h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">Exposición de los mejores proyectos desarrollados por estudiantes de pregrado y posgrado</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="calendar" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">viernes, 10 de mayo de 2024</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="clock" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">10:00 AM</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="map-pin" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">Plaza Central</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <i data-lucide="users" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-[var(--color-text)]">500 participantes esperados</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[var(--color-border)] mb-4">
                        <p class="text-sm text-[var(--color-text-muted)]">Presentador:</p>
                        <p class="font-medium text-[var(--color-heading)]">Múltiples Presentadores</p>
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button class="flex-1 py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                            Inscribirse
                            <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                        </button>
                        <button class="p-2 border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors">
                            <i data-lucide="bell" class="h-4 w-4 text-[var(--color-text-muted)]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <button class="px-6 py-3 border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-primary)]/10 transition-colors flex items-center mx-auto">
                Ver calendario completo
                <i data-lucide="calendar" class="ml-2 h-4 w-4"></i>
            </button>
        </div>
    </div>
</section>

<!-- Sección Noticias -->
<section id="noticias" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn">
            <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm mb-4">Noticias y Novedades</span>
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Últimas Noticias</h2>
            <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                Mantente informado sobre los últimos desarrollos, logros y novedades de nuestra comunidad académica y científica.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&h=400&q=80" alt="Nueva metodología" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-2 py-1 bg-blue-100 text-[var(--color-primary)] rounded text-sm">Innovación</span>
                    </div>
                    <div class="absolute top-4 right-4">
                        <span class="px-2 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded text-sm">Destacada</span>
                    </div>
                </div>

                <div class="p-4">
                    <h3 class="text-2xl font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                        Nueva metodología de evaluación implementada con éxito
                    </h3>
                    <p class="text-[var(--color-text-muted)] mb-4 line-clamp-3">
                        La universidad ha implementado un sistema innovador de evaluación que mejora la transparencia y eficiencia en los procesos académicos...
                    </p>

                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-[var(--color-primary)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                            MG
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-[var(--color-heading)]">Dr. María González</p>
                            <div class="flex items-center space-x-2 text-xs text-[var(--color-text-muted)]">
                                <i data-lucide="calendar" class="h-3 w-3"></i>
                                <span>10/03/2024</span>
                                <i data-lucide="clock" class="h-3 w-3 ml-2"></i>
                                <span>5 min</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-[var(--color-text-muted)] mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                <span>1250</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="message-circle" class="h-4 w-4"></i>
                                <span>23</span>
                            </div>
                        </div>
                    </div>

                    <button class="w-full py-2 px-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                        Leer más
                        <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-8">
                <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.2s;">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80" alt="Resultados convocatoria" class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 bg-green-100 text-[var(--color-success)] rounded text-sm">Logros</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                            Resultados destacados en la convocatoria nacional 2024
                        </h3>
                        <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">
                            Nuestros investigadores obtuvieron financiamiento para 15 proyectos en la convocatoria nacional de ciencia y tecnología...
                        </p>

                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-[var(--color-success)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                CR
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-[var(--color-heading)]">Ing. Carlos Ruiz</p>
                                <div class="flex items-center space-x-2 text-xs text-[var(--color-text-muted)]">
                                    <i data-lucide="calendar" class="h-3 w-3"></i>
                                    <span>08/03/2024</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 bg-[var(--color-surface)] rounded-xl shadow-md overflow-hidden animate-slideUp" style="animation-delay: 0.4s;">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80" alt="Alianza internacional" class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 bg-purple-100 text-[var(--color-secondary)] rounded text-sm">Alianzas</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 text-[var(--color-heading)]">
                            Alianza estratégica con universidades internacionales
                        </h3>
                        <p class="text-[var(--color-text-muted)] mb-4 line-clamp-2">
                            Se firmó un convenio de cooperación académica con prestigiosas universidades de Europa y América para intercambio de conocimiento...
                        </p>

                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-[var(--color-secondary)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                AL
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-[var(--color-heading)]">Dra. Ana López</p>
                                <div class="flex items-center space-x-2 text-xs text-[var(--color-text-muted)]">
                                    <i data-lucide="calendar" class="h-3 w-3"></i>
                                    <span>05/03/2024</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <button class="px-6 py-3 border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-primary)]/10 transition-colors flex items-center mx-auto">
                Ver todas las noticias
                <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
            </button>
        </div>
    </div>
</section>

<!-- Banner Inferior -->
<div class="bg-gradient-to-r from-[var(--color-success)] to-[var(--color-secondary)] text-white py-3 px-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 flex-1">
                <i data-lucide="trophy" class="w-5 h-5 flex-shrink-0"></i>
                <p class="text-sm md:text-base font-medium">🏆 Únete a nuestra comunidad de investigadores - Más de 50 proyectos activos</p>
            </div>
            <div class="flex items-center space-x-2 ml-4">
                <button class="hidden sm:inline-flex px-3 py-1 bg-white text-[var(--color-success)] rounded text-sm font-medium">Explorar</button>
                <button id="close-bottom-banner" class="text-white hover:bg-white/20 p-1 rounded">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="py-16 lg:py-24 bg-gradient-to-r from-[var(--color-bg)] to-[var(--color-surface-alt)]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl font-bold mb-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">Mantente Actualizado</h3>
        <p class="text-[var(--color-text-muted)] mb-8 text-lg">
            Recibe las últimas noticias, actualizaciones de la plataforma y oportunidades de investigación directamente en tu correo electrónico.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input type="email" placeholder="tu@email.com" class="flex-1 px-4 py-3 bg-[var(--color-input-bg)] border border-[var(--color-primary)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] text-[var(--color-input-text)]">
            <button class="px-6 py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center">
                Suscribirse
                <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
            </button>
        </div>
        <p class="text-xs text-[var(--color-text-muted)] mt-4">
            No spam. Puedes cancelar tu suscripción en cualquier momento.
        </p>
    </div>
</div>

<script>
// JavaScript para el slider
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slider > div');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(n) {
        // Ocultar todas las slides
        slides.forEach(slide => {
            slide.style.opacity = '0';
        });
        
        // Remover clase activa de todos los dots
        dots.forEach(dot => {
            dot.classList.remove('bg-white/50');
            dot.classList.add('bg-white/30');
        });
        
        // Mostrar slide actual
        slides[n].style.opacity = '1';
        
        // Activar dot correspondiente
        dots[n].classList.remove('bg-white/30');
        dots[n].classList.add('bg-white/50');
        
        currentSlide = n;
    }

    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slides.length) {
            next = 0;
        }
        showSlide(next);
    }

    // Inicializar slider
    showSlide(0);
    
    // Iniciar intervalo automático
    slideInterval = setInterval(nextSlide, 5000);

    // Event listeners para los dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            clearInterval(slideInterval);
            showSlide(index);
            // Reiniciar intervalo
            slideInterval = setInterval(nextSlide, 5000);
        });
    });

    // Cerrar banner inferior
    const closeBannerBtn = document.getElementById('close-bottom-banner');
    if (closeBannerBtn) {
        closeBannerBtn.addEventListener('click', function() {
            this.closest('.bg-gradient-to-r').style.display = 'none';
        });
    }
});
</script>