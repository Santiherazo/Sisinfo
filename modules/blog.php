<!-- Página Completa Blog -->
<section id="blog-completo" class="min-h-screen bg-[var(--color-bg)]">
    <!-- Header -->
    <div class="pt-24 pb-16 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fadeIn">
                <nav class="flex justify-center items-center space-x-2 text-sm text-[var(--color-text-muted)] mb-6">
                    <a href="#" class="hover:text-[var(--color-primary)] transition-colors">Inicio</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-[var(--color-primary)] font-medium">Blog</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">
                    Nuestro Blog
                </h1>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                    Descubre artículos, noticias y reflexiones sobre innovación educativa, tecnología 
                    y las últimas tendencias en evaluación académica.
                </p>
            </div>
        </div>
    </div>

    <!-- Navegación Interna -->
    <div class="sticky top-16 z-40 bg-[var(--color-surface)] border-b border-[var(--color-border)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto space-x-8 py-4">
                <a href="#destacados" class="whitespace-nowrap text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors font-medium">Destacados</a>
                <a href="#todos" class="whitespace-nowrap text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors font-medium">Todos los Artículos</a>
                <a href="#categorias" class="whitespace-nowrap text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors font-medium">Categorías</a>
                <a href="#autores" class="whitespace-nowrap text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors font-medium">Autores</a>
                <a href="#newsletter" class="whitespace-nowrap text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors font-medium">Newsletter</a>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Filtros Rápidos -->
                <div class="flex flex-wrap gap-3">
                    <button class="px-6 py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-full font-medium hover:opacity-90 transition-opacity flex items-center">
                        <i data-lucide="grid" class="w-4 h-4 mr-2"></i>
                        Todos los Posts
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-dropdown-hover)] transition-colors flex items-center">
                        <i data-lucide="zap" class="w-4 h-4 mr-2"></i>
                        Innovación
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-dropdown-hover)] transition-colors flex items-center">
                        <i data-lucide="book-open" class="w-4 h-4 mr-2"></i>
                        Educación
                    </button>
                    <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full font-medium hover:bg-[var(--color-dropdown-hover)] transition-colors flex items-center">
                        <i data-lucide="code" class="w-4 h-4 mr-2"></i>
                        Tecnología
                    </button>
                </div>

                <!-- Controles -->
                <div class="flex items-center gap-4">
                    <!-- Ordenar -->
                    <div class="relative">
                        <select class="pl-4 pr-10 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] appearance-none bg-[var(--color-input-bg)] text-[var(--color-input-text)] text-sm">
                            <option>Ordenar por: Más reciente</option>
                            <option>Ordenar por: Más popular</option>
                            <option>Ordenar por: Más comentados</option>
                            <option>Ordenar por: Alfabético</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none"></i>
                    </div>

                    <!-- Buscar -->
                    <div class="relative">
                        <input type="text" placeholder="Buscar artículos..." class="pl-10 pr-4 py-2 border border-[var(--color-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] w-64">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Artículos Destacados -->
    <section id="destacados" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-full text-sm font-medium text-yellow-600 mb-4">
                    Lo Más Leído
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Artículos Destacados</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Los posts más populares y relevantes de nuestra comunidad
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 mb-16">
                <!-- Artículo Destacado Principal -->
                <div class="lg:col-span-2 group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                    <div class="grid lg:grid-cols-2">
                        <div class="relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" 
                                 alt="El Futuro de la Evaluación Académica"
                                 class="w-full h-64 lg:h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-full text-sm font-medium">Destacado</span>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Innovación</span>
                            </div>
                        </div>
                        
                        <div class="p-8 flex flex-col justify-center">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="flex items-center space-x-1 text-sm text-[var(--color-text-muted)]">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                    <span>15 Mar 2024</span>
                                </div>
                                <div class="flex items-center space-x-1 text-sm text-[var(--color-text-muted)]">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>8 min lectura</span>
                                </div>
                            </div>

                            <h3 class="text-2xl lg:text-3xl font-bold text-[var(--color-heading)] mb-4 group-hover:text-[var(--color-primary)] transition-colors">
                                El Futuro de la Evaluación Académica: IA y Personalización
                            </h3>
                            <p class="text-[var(--color-text-muted)] mb-6 leading-relaxed">
                                Exploramos cómo la inteligencia artificial está revolucionando los métodos de evaluación, 
                                permitiendo procesos más personalizados, justos y efectivos para el aprendizaje.
                            </p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-[var(--color-primary)] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        CM
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[var(--color-text)]">Dr. Carlos Mendoza</p>
                                        <p class="text-xs text-[var(--color-text-muted)]">CEO & Fundador</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 text-sm text-[var(--color-text-muted)]">
                                    <div class="flex items-center space-x-1">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        <span>2.4k</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                                        <span>47</span>
                                    </div>
                                </div>
                            </div>

                            <button class="mt-6 w-full py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity font-semibold">
                                Leer Artículo Completo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de Artículos Destacados Secundarios -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Artículo 2 -->
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=200&q=80" 
                             alt="Metodologías Ágiles"
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Educación</span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2">
                            Implementación de Metodologías Ágiles en la Educación Superior
                        </h3>
                        <p class="text-[var(--color-text-muted)] mb-4 text-sm leading-relaxed line-clamp-3">
                            Cómo las metodologías ágiles están transformando la gestión de proyectos académicos 
                            y mejorando la colaboración entre estudiantes y docentes.
                        </p>

                        <div class="flex items-center justify-between text-sm text-[var(--color-text-muted)] mb-4">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>12 Mar 2024</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>1.8k</span>
                            </div>
                        </div>

                        <button class="w-full py-2 border border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg hover:bg-blue-50 transition-colors font-medium text-sm">
                            Leer Más
                        </button>
                    </div>
                </div>

                <!-- Artículo 3 -->
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=200&q=80" 
                             alt="Analítica de Datos"
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">Tecnología</span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2">
                            Analítica de Datos para Mejorar el Rendimiento Estudiantil
                        </h3>
                        <p class="text-[var(--color-text-muted)] mb-4 text-sm leading-relaxed line-clamp-3">
                            Utilizando big data y machine learning para identificar patrones de aprendizaje 
                            y personalizar estrategias educativas.
                        </p>

                        <div class="flex items-center justify-between text-sm text-[var(--color-text-muted)] mb-4">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>08 Mar 2024</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>2.1k</span>
                            </div>
                        </div>

                        <button class="w-full py-2 border border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg hover:bg-blue-50 transition-colors font-medium text-sm">
                            Leer Más
                        </button>
                    </div>
                </div>

                <!-- Artículo 4 -->
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=200&q=80" 
                             alt="Evaluación Continua"
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">Tendencias</span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2">
                            La Evaluación Continua: Del Examen Final al Aprendizaje Constante
                        </h3>
                        <p class="text-[var(--color-text-muted)] mb-4 text-sm leading-relaxed line-clamp-3">
                            Cómo los sistemas de evaluación continua están reemplazando los exámenes finales 
                            tradicionales para un aprendizaje más significativo.
                        </p>

                        <div class="flex items-center justify-between text-sm text-[var(--color-text-muted)] mb-4">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>05 Mar 2024</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>1.5k</span>
                            </div>
                        </div>

                        <button class="w-full py-2 border border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg hover:bg-blue-50 transition-colors font-medium text-sm">
                            Leer Más
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Todos los Artículos -->
    <section id="todos" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-blue-200 rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Archivo Completo
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Todos los Artículos</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Artículo 5 -->
                <div class="group bg-[var(--color-surface)] rounded-2xl shadow-lg overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=200&q=80" 
                             alt="Gamificación"
                             class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Innovación</span>
                            <span class="text-xs text-[var(--color-text-muted)]">28 Feb 2024</span>
                        </div>
                        <h3 class="font-semibold text-[var(--color-heading)] mb-2 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2">
                            Gamificación en la Evaluación: Jugando para Aprender
                        </h3>
                        <p class="text-[var(--color-text-muted)] text-sm mb-4 line-clamp-2">
                            Cómo incorporar elementos de juego en los procesos de evaluación para aumentar 
                            la motivación y el engagement estudiantil.
                        </p>
                        <div class="flex items-center justify-between text-xs text-[var(--color-text-muted)]">
                            <div class="flex items-center space-x-1">
                                <i data-lucide="eye" class="w-3 h-3"></i>
                                <span>1.2k</span>
                            </div>
                            <span>5 min lectura</span>
                        </div>
                    </div>
                </div>

                <!-- Más artículos... -->
            </div>

            <!-- Paginación -->
            <div class="flex justify-center items-center space-x-2 mt-12">
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <button class="w-10 h-10 flex items-center justify-center bg-[var(--color-primary)] text-white rounded-lg font-medium">1</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">2</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">3</button>
                <span class="px-2 text-[var(--color-text-muted)]">...</span>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">8</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Sección Categorías -->
    <section id="categorias" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-blue-50 border border-blue-200 rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Explorar
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Categorías</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Navega por nuestros temas especializados en educación y tecnología
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Categoría 1 -->
                <a href="#" class="group bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 text-center hover:-translate-y-2 hover:shadow-xl transition-all duration-300 border-2 border-transparent hover:border-blue-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i data-lucide="zap" class="w-8 h-8 text-[var(--color-primary)]"></i>
                    </div>
                    <h3 class="font-semibold text-[var(--color-heading)] mb-2">Innovación</h3>
                    <p class="text-sm text-[var(--color-text-muted)] mb-3">Tecnologías disruptivas en educación</p>
                    <div class="text-xs text-[var(--color-primary)] font-medium">24 artículos</div>
                </a>

                <!-- Más categorías... -->
            </div>
        </div>
    </section>

    <!-- Sección Autores Destacados -->
    <section id="autores" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-purple-200 rounded-full text-sm font-medium text-purple-600 mb-4">
                    Nuestros Expertos
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Autores Destacados</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Conoce a los especialistas que comparten su conocimiento en nuestro blog
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Autor 1 -->
                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&h=200&q=80" 
                             alt="Carlos Mendoza" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-semibold text-[var(--color-heading)] mb-1">Dr. Carlos Mendoza</h3>
                    <p class="text-[var(--color-primary)] text-sm mb-3">CEO & Fundador</p>
                    <p class="text-[var(--color-text-muted)] text-xs mb-4">Especialista en tecnología educativa e IA</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </a>
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </a>
                    </div>
                    <div class="mt-3 text-xs text-[var(--color-text-muted)]">12 artículos publicados</div>
                </div>

                <!-- Más autores... -->
            </div>
        </div>
    </section>

    <!-- Sección Newsletter -->
    <section id="newsletter" class="py-16 lg:py-24 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-4xl font-bold mb-4">Mantente Actualizado</h2>
            <p class="text-blue-100 text-xl mb-8 max-w-2xl mx-auto">
                Recibe los últimos artículos, noticias y recursos sobre innovación educativa directamente en tu correo
            </p>
            
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input type="email" placeholder="tu@email.com" class="flex-1 px-4 py-3 bg-white border border-blue-500/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900">
                    <button class="px-6 py-3 bg-white text-[var(--color-primary)] rounded-lg font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                        Suscribirse
                        <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                    </button>
                </div>
                <p class="text-blue-100 text-sm mt-4">
                    No spam. Puedes cancelar tu suscripción en cualquier momento.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 text-center">
                <div>
                    <div class="text-2xl font-bold text-white mb-2">500+</div>
                    <div class="text-blue-100 text-sm">Suscriptores</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white mb-2">2x</div>
                    <div class="text-blue-100 text-sm">Por Semana</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white mb-2">98%</div>
                    <div class="text-blue-100 text-sm">Tasa de Apertura</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white mb-2">0</div>
                    <div class="text-blue-100 text-sm">Spam</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-16 lg:py-24 bg-[var(--color-surface)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">¿Te gustaría escribir para nosotros?</h2>
            <p class="text-xl text-[var(--color-text-muted)] mb-8 max-w-2xl mx-auto">
                Buscamos expertos apasionados por compartir conocimiento sobre educación, tecnología e innovación
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="px-8 py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg font-semibold hover:opacity-90 transition-opacity">
                    Enviar Propuesta
                </button>
                <button class="px-8 py-3 border border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-blue-50 transition-colors font-semibold">
                    Ver Guías de Estilo
                </button>
            </div>
        </div>
    </section>
</section>

<script>
    // Script para funcionalidades del blog
    document.addEventListener('DOMContentLoaded', function() {
        // Sistema de filtrado de artículos
        const filterButtons = document.querySelectorAll('.flex-wrap button');
        const articles = document.querySelectorAll('[data-category]');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remover active de todos los botones
                filterButtons.forEach(btn => {
                    btn.classList.remove('bg-gradient-to-br', 'from-[var(--color-primary)]', 'to-[var(--color-secondary)]', 'text-white');
                    btn.classList.add('bg-[var(--color-surface)]', 'border', 'border-[var(--color-border)]', 'text-[var(--color-text)]');
                });

                // Agregar active al botón clickeado
                this.classList.remove('bg-[var(--color-surface)]', 'border', 'border-[var(--color-border)]', 'text-[var(--color-text)]');
                this.classList.add('bg-gradient-to-br', 'from-[var(--color-primary)]', 'to-[var(--color-secondary)]', 'text-white');

                // Filtrar artículos
                const filter = this.textContent.trim();
                articles.forEach(article => {
                    if (filter === 'Todos los Posts' || article.getAttribute('data-category') === filter) {
                        article.style.display = 'block';
                    } else {
                        article.style.display = 'none';
                    }
                });
            });
        });

        // Sistema de búsqueda
        const searchInput = document.querySelector('input[type="text"]');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            articles.forEach(article => {
                const title = article.querySelector('h3').textContent.toLowerCase();
                const excerpt = article.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
                    article.style.display = 'block';
                } else {
                    article.style.display = 'none';
                }
            });
        });

        // Contador de visitas (simulado)
        document.querySelectorAll('[data-article]').forEach(article => {
            const viewsElement = article.querySelector('[data-views]');
            if (viewsElement) {
                // Simular incremento de vistas
                const currentViews = parseInt(viewsElement.textContent);
                viewsElement.textContent = (currentViews + Math.floor(Math.random() * 10)).toLocaleString();
            }
        });
    });
</script>