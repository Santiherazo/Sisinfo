<section id="nosotros-completo" class="min-h-screen bg-[var(--color-bg)]">
    <div class="pt-24 pb-16 bg-gradient-to-br from-[var(--color-surface-alt)] to-[var(--color-surface)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fadeIn">
                <nav class="flex justify-center items-center space-x-2 text-sm text-[var(--color-text-muted)] mb-6">
                    <a href="#" class="hover:text-[var(--color-primary)] transition-colors">Inicio</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-[var(--color-primary)] font-medium">Nosotros</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">
                    Sobre Nosotros
                </h1>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                    Conoce nuestra historia, valores y el equipo que está transformando la evaluación académica 
                    en Latinoamérica mediante tecnología innovadora.
                </p>
            </div>
        </div>
    </div>

    <section id="historia" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-slideUp">
                    <span class="inline-block px-4 py-2 bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                        Nuestra Trayectoria
                    </span>
                    <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">De Idea a Realidad</h2>
                    <p class="text-lg text-[var(--color-text-muted)] mb-6 leading-relaxed">
                        <?php config('history'); ?>
                    </p>
                    <p class="text-lg text-[var(--color-text-muted)] mb-8 leading-relaxed">
                        <?php config('about_us'); ?>
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-6 bg-[var(--color-surface-alt)] rounded-xl">
                            <div class="text-3xl font-bold text-[var(--color-primary)] mb-2">12+</div>
                            <div class="text-sm text-[var(--color-text-muted)]">Años de Experiencia</div>
                        </div>
                        <div class="text-center p-6 bg-[var(--color-surface-alt)] rounded-xl">
                            <div class="text-3xl font-bold text-[var(--color-accent)] mb-2">50+</div>
                            <div class="text-sm text-[var(--color-text-muted)]">Instituciones</div>
                        </div>
                    </div>
                </div>

                <div class="relative animate-slideUp" style="animation-delay: 0.2s;">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-accent)] rounded-2xl p-1 shadow-xl">
                                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=200&q=80" 
                                     alt="Fundación" class="w-full h-48 object-cover rounded-2xl">
                            </div>
                            <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-4 border border-[var(--color-border)]">
                                <div class="text-2xl font-bold text-[var(--color-primary)] mb-1">2012</div>
                                <p class="text-sm text-[var(--color-text-muted)]">Fundación como proyecto universitario</p>
                            </div>
                        </div>
                        <div class="space-y-4 mt-8">
                            <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-4 border border-[var(--color-border)]">
                                <div class="text-2xl font-bold text-[var(--color-accent)] mb-1">2018</div>
                                <p class="text-sm text-[var(--color-text-muted)]">Expansión a nivel nacional</p>
                            </div>
                            <div class="bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-primary)] rounded-2xl p-1 shadow-xl">
                                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=200&q=80" 
                                     alt="Crecimiento" class="w-full h-48 object-cover rounded-2xl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="mision-vision" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Nuestro Propósito
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Misión & Visión</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-12">
                <div class="animate-slideUp">
                    <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-border)] h-full">
                        <div class="w-16 h-16 bg-[var(--color-surface-alt)] rounded-2xl flex items-center justify-center mb-6">
                            <i data-lucide="target" class="w-8 h-8 text-[var(--color-primary)]"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-[var(--color-heading)] mb-4">Nuestra Misión</h3>
                        <p class="text-[var(--color-text-muted)] leading-relaxed mb-6">
                            <?php config('mission'); ?>
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="w-5 h-5 text-[var(--color-success)]"></i>
                                <span class="text-[var(--color-text-muted)]">Automatización de procesos evaluativos</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="w-5 h-5 text-[var(--color-success)]"></i>
                                <span class="text-[var(--color-text-muted)]">Transparencia en los resultados</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="w-5 h-5 text-[var(--color-success)]"></i>
                                <span class="text-[var(--color-text-muted)]">Soporte continuo a instituciones</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="animate-slideUp" style="animation-delay: 0.2s;">
                    <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-border)] h-full">
                        <div class="w-16 h-16 bg-[var(--color-surface-alt)] rounded-2xl flex items-center justify-center mb-6">
                            <i data-lucide="eye" class="w-8 h-8 text-[var(--color-accent)]"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-[var(--color-heading)] mb-4">Nuestra Visión</h3>
                        <p class="text-[var(--color-text-muted)] leading-relaxed mb-6">
                            <?php config('vision'); ?>
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-3">
                                <i data-lucide="star" class="w-5 h-5 text-[var(--color-warning)]"></i>
                                <span class="text-[var(--color-text-muted)]">Liderazgo regional en 5 años</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="star" class="w-5 h-5 text-[var(--color-warning)]"></i>
                                <span class="text-[var(--color-text-muted)]">Expansión a 10 países</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="star" class="w-5 h-5 text-[var(--color-warning)]"></i>
                                <span class="text-[var(--color-text-muted)]">+200 instituciones asociadas</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="valores" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Nuestros Principios
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Valores Corporativos</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Los principios que guían cada decisión y acción en EvalAcademic Pro
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-6 bg-[var(--color-primary)]/10 rounded-2xl flex items-center justify-center group-hover:bg-[var(--color-primary)]/20 transition-colors">
                        <i data-lucide="shield" class="w-10 h-10 text-[var(--color-primary)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3">Transparencia</h3>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Procesos claros, resultados verificables y comunicación abierta en cada evaluación
                    </p>
                </div>

                <div class="text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-6 bg-[var(--color-accent)]/10 rounded-2xl flex items-center justify-center group-hover:bg-[var(--color-accent)]/20 transition-colors">
                        <i data-lucide="award" class="w-10 h-10 text-[var(--color-accent)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3">Excelencia</h3>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Comprometidos con los más altos estándares de calidad académica y tecnológica
                    </p>
                </div>

                <div class="text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-6 bg-[var(--color-success)]/10 rounded-2xl flex items-center justify-center group-hover:bg-[var(--color-success)]/20 transition-colors">
                        <i data-lucide="zap" class="w-10 h-10 text-[var(--color-success)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3">Innovación</h3>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Constantemente evolucionando con las últimas tecnologías y metodologías educativas
                    </p>
                </div>

                <div class="text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-6 bg-[var(--color-warning)]/10 rounded-2xl flex items-center justify-center group-hover:bg-[var(--color-warning)]/20 transition-colors">
                        <i data-lucide="users" class="w-10 h-10 text-[var(--color-warning)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-3">Colaboración</h3>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Trabajo en equipo y alianzas estratégicas para alcanzar objetivos comunes
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="equipo" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Nuestro Equipo
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Equipo Directivo</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Conoce a los líderes que guían nuestra visión y estrategia hacia la excelencia académica
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-[var(--color-surface-alt)] to-[var(--color-surface-alt)] overflow-hidden">
                        <img src="<?php echo __PATH_IMG__ . "chaparro.jpg"?>" 
                             alt="Daniel Chaparro Martinez" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Daniel Chaparro Martinez</h3>
                    <p class="text-[var(--color-primary)] font-medium mb-3">Estudiante Principal</p>
                    <p class="text-[var(--color-text-muted)] text-sm mb-4">
                        Descripción
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="linkedin" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-[var(--color-surface-alt)] to-[var(--color-surface-alt)] overflow-hidden">
                        <img src="<?php echo __PATH_IMG__ . "lopez.jpg"?>" 
                             alt="Cipriano López Vides" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Cipriano López Vides</h3>
                    <p class="text-[var(--color-secondary)] font-medium mb-3">Docente Acompañante</p>
                    <p class="text-[var(--color-text-muted)] text-sm mb-4">
                        Descripción
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="linkedin" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="team" class="px-8 py-3 border-2 border-[var(--color-primary)] text-[var(--color-primary)] bg-transparent rounded-lg hover:bg-[var(--color-surface-alt)] transition-colors font-semibold">
                    Ver Equipo Completo
                </a>
            </div>
        </div>
    </section>

    <section id="logros" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Reconocimientos
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-heading)] mb-6">Logros y Premios</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-warning)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="award" class="w-8 h-8 text-[var(--color-warning)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Premio a la Innovación 2023</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Reconocimiento nacional por nuestra plataforma de evaluación</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-warning)]/10 text-[var(--color-warning)] rounded-full text-sm">1er Lugar</span>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-success)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="trophy" class="w-8 h-8 text-[var(--color-success)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Mejor Startup Educativa 2022</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Reconocimiento en el Latin American EdTech Awards</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-success)]/10 text-[var(--color-success)] rounded-full text-sm">Ganador</span>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-primary)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="star" class="w-8 h-8 text-[var(--color-primary)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Sello de Calidad Educativa</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Certificación de calidad en procesos de evaluación</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-full text-sm">Certificado</span>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-accent)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="users" class="w-8 h-8 text-[var(--color-accent)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">+50 Instituciones</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Más de 50 instituciones educativas confían en nuestra plataforma</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-accent)]/10 text-[var(--color-accent)] rounded-full text-sm">Métrica</span>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-secondary)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="globe" class="w-8 h-8 text-[var(--color-secondary)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Expansión Internacional</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Presencia en 5 países de Latinoamérica</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-secondary)]/10 text-[var(--color-secondary)] rounded-full text-sm">Crecimiento</span>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-border)]">
                    <div class="w-16 h-16 bg-[var(--color-warning)]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="trending-up" class="w-8 h-8 text-[var(--color-warning)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-heading)] mb-2">Crecimiento 300%</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Crecimiento anual en número de usuarios activos</p>
                    <span class="inline-block px-3 py-1 bg-[var(--color-warning)]/10 text-[var(--color-warning)] rounded-full text-sm">Éxito</span>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-accent)] py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">¿Listo para transformar tu institución?</h2>
            <p class="text-blue-100 mb-8 text-lg">
                Únete a las más de 50 instituciones que ya confían en <?php config('website_name'); ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="px-8 py-3 bg-white text-[var(--color-primary)] rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Solicitar Demo
                </button>
                <button class="px-8 py-3 bg-white/20 text-white rounded-lg font-semibold hover:bg-white/30 transition-colors backdrop-blur-sm">
                    Contactar Ventas
                </button>
            </div>
        </div>
    </div>
</section>