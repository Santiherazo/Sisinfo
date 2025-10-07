<section class="min-h-screen bg-[var(--color-bg)]">
    <div class="pt-24 pb-16 bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-secondary)]/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fadeIn">
                <nav class="flex justify-center items-center space-x-2 text-sm text-[var(--color-text-muted)] mb-6">
                    <a href="#" class="hover:text-[var(--color-primary)] transition-colors">Inicio</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-[var(--color-primary)] font-medium">Contacto</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent">
                    Contáctanos
                </h1>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto leading-relaxed">
                    Estamos aquí para ayudarte. Elige el canal de contacto más adecuado para tu consulta 
                    y te responderemos a la brevedad.
                </p>
            </div>
        </div>
    </div>

    <section id="formulario" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="animate-slideUp">
                    <div class="bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-secondary)]/10 rounded-2xl p-8 border border-[var(--color-surface-alt)]">
                        <h2 class="text-3xl font-bold text-[var(--color-text)] mb-6">Información de Contacto</h2>
                        
                        <div class="space-y-6 mb-8">
                            <div class="flex items-start space-x-4 group hover:-translate-y-1 transition-transform duration-300">
                                <div class="w-12 h-12 bg-[var(--color-primary)]/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-[var(--color-primary)]/20 transition-colors">
                                    <i data-lucide="phone" class="w-6 h-6 text-[var(--color-primary)]"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[var(--color-text)] mb-1">Teléfono</h3>
                                    <p class="text-[var(--color-text-muted)] mb-1"><?php config('contact_phone'); ?></p>
                                    <p class="text-sm text-[var(--color-border-muted)]">Lun-Vie: 8:00 AM - 6:00 PM</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 group hover:-translate-y-1 transition-transform duration-300">
                                <div class="w-12 h-12 bg-[var(--color-secondary)]/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-[var(--color-secondary)]/20 transition-colors">
                                    <i data-lucide="mail" class="w-6 h-6 text-[var(--color-secondary)]"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[var(--color-text)] mb-1">Email</h3>
                                    <p class="text-[var(--color-text-muted)] mb-1"><?php config('contact_email'); ?></p>
                                    <p class="text-sm text-[var(--color-border-muted)]">Soporte: <?php config('contact_support_email'); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 group hover:-translate-y-1 transition-transform duration-300">
                                <div class="w-12 h-12 bg-[var(--color-success)]/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-[var(--color-success)]/20 transition-colors">
                                    <i data-lucide="map-pin" class="w-6 h-6 text-[var(--color-success)]"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[var(--color-text)] mb-1">Oficina Principal</h3>
                                    <p class="text-[var(--color-text-muted)] mb-1">Calle 123 #45-67, Barrancabermeja</p>
                                    <p class="text-sm text-[var(--color-border-muted)]">Colombia</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-semibold text-[var(--color-text)] mb-4">Síguenos en</h3>
                            <div class="flex space-x-3">
                                <a href="<?php config('social_link_facebook'); ?>" class="w-10 h-10 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center text-[var(--color-primary)] hover:bg-[var(--color-primary)]/20 transition-colors">
                                    <i data-lucide="facebook" class="w-5 h-5"></i>
                                </a>
                                <a href="<?php config('social_link_instagram'); ?>" class="w-10 h-10 bg-[var(--color-secondary)]/10 rounded-lg flex items-center justify-center text-[var(--color-secondary)] hover:bg-[var(--color-secondary)]/20 transition-colors">
                                    <i data-lucide="instagram" class="w-5 h-5"></i>
                                </a>
                                <a href="<?php config('social_link_twitter'); ?>" class="w-10 h-10 bg-[var(--color-link)]/10 rounded-lg flex items-center justify-center text-[var(--color-link)] hover:bg-[var(--color-link)]/20 transition-colors">
                                    <i data-lucide="twitter" class="w-5 h-5"></i>
                                </a>
                                <a href="<?php config('social_link_linkedin'); ?>" class="w-10 h-10 bg-[var(--color-primary)] rounded-lg flex items-center justify-center text-white hover:bg-[var(--color-navbar-hover)] transition-colors">
                                    <i data-lucide="linkedin" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mt-8">
                        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-surface-alt)] hover:shadow-lg transition-all duration-300 group">
                            <div class="w-12 h-12 bg-[var(--color-primary)]/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-[var(--color-primary)]/20 transition-colors">
                                <i data-lucide="headphones" class="w-6 h-6 text-[var(--color-primary)]"></i>
                            </div>
                            <h4 class="font-semibold text-[var(--color-text)] mb-2">Soporte Técnico</h4>
                            <p class="text-sm text-[var(--color-text-muted)] mb-3">Asistencia inmediata para problemas técnicos</p>
                            <p class="text-[var(--color-primary)] text-sm font-medium"><?php config('contact_support_email'); ?></p>
                        </div>

                        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-surface-alt)] hover:shadow-lg transition-all duration-300 group">
                            <div class="w-12 h-12 bg-[var(--color-success)]/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-[var(--color-success)]/20 transition-colors">
                                <i data-lucide="book-open" class="w-6 h-6 text-[var(--color-success)]"></i>
                            </div>
                            <h4 class="font-semibold text-[var(--color-text)] mb-2">Consultoría Académica</h4>
                            <p class="text-sm text-[var(--color-text-muted)] mb-3">Asesoramiento especializado en evaluación</p>
                            <p class="text-[var(--color-success)] text-sm font-medium"><?php config('contact_consultor'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="animate-slideUp" style="animation-delay: 0.2s;">
                    <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-surface-alt)] sticky top-32">
                        <h2 class="text-3xl font-bold text-[var(--color-text)] mb-2">Envíanos un Mensaje</h2>
                        <p class="text-[var(--color-text-muted)] mb-6">Completa el formulario y te contactaremos en menos de 24 horas</p>
                        
                        <form class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-[var(--color-text)] mb-2">Nombre Completo *</label>
                                    <input type="text" id="nombre" required class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-2">Email *</label>
                                    <input type="email" id="email" required class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="empresa" class="block text-sm font-medium text-[var(--color-text)] mb-2">Institución/Empresa</label>
                                    <input type="text" id="empresa" class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                </div>
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-[var(--color-text)] mb-2">Teléfono</label>
                                    <input type="tel" id="telefono" class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                </div>
                            </div>

                            <div>
                                <label for="asunto" class="block text-sm font-medium text-[var(--color-text)] mb-2">Asunto *</label>
                                <select id="asunto" required class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                    <option value="">Selecciona un asunto</option>
                                    <option value="soporte">Soporte Técnico</option>
                                    <option value="consultoria">Consultoría Académica</option>
                                    <option value="demo">Solicitud de Demo</option>
                                    <option value="general">Consulta General</option>
                                </select>
                            </div>

                            <div>
                                <label for="mensaje" class="block text-sm font-medium text-[var(--color-text)] mb-2">Mensaje *</label>
                                <textarea id="mensaje" rows="6" required class="w-full px-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors bg-[var(--color-input-bg)] text-[var(--color-input-text)] resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-opacity font-semibold">
                                Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="oficinas" class="py-16 lg:py-24 bg-[var(--color-surface-alt)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-primary)]/20 rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Nuestras Oficinas
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-text)] mb-6">Visítanos</h2>
                <p class="text-xl text-[var(--color-text-muted)] max-w-3xl mx-auto">
                    Contamos con oficinas estratégicamente ubicadas para servirte mejor
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-surface-alt)]">
                    <div class="w-12 h-12 bg-[var(--color-primary)]/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="map-pin" class="w-6 h-6 text-[var(--color-primary)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-text)] mb-3">Oficina Principal</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Calle 123 #45-67<br>Bogotá, Colombia</p>
                    <div class="space-y-2 text-sm text-[var(--color-border-muted)]">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span><?php config('contact_phone'); ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>Lun-Vie: 8:00 AM - 6:00 PM</span>
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-surface-alt)]">
                    <div class="w-12 h-12 bg-[var(--color-secondary)]/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="map-pin" class="w-6 h-6 text-[var(--color-secondary)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-text)] mb-3">Oficina Medellín</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Carrera 80 #25-34<br>Medellín, Colombia</p>
                    <div class="space-y-2 text-sm text-[var(--color-border-muted)]">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>+57 (4) 567-8901</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>Lun-Vie: 8:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-6 border border-[var(--color-surface-alt)]">
                    <div class="w-12 h-12 bg-[var(--color-accent)]/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="map-pin" class="w-6 h-6 text-[var(--color-accent)]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-text)] mb-3">Oficina Cali</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Avenida 6N #23-45<br>Cali, Colombia</p>
                    <div class="space-y-2 text-sm text-[var(--color-border-muted)]">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>+57 (2) 345-6789</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>Lun-Vie: 7:30 AM - 5:30 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="preguntas" class="py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 rounded-full text-sm font-medium text-[var(--color-primary)] mb-4">
                    Ayuda
                </span>
                <h2 class="text-4xl font-bold text-[var(--color-text)] mb-6">Preguntas Frecuentes</h2>
            </div>

            <div class="space-y-4">
                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg border border-[var(--color-surface-alt)]">
                    <button class="w-full px-6 py-4 text-left flex items-center justify-between font-semibold text-[var(--color-text)]">
                        <span>¿Cómo puedo solicitar una demo de la plataforma?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                    </button>
                    <div class="px-6 pb-4 text-[var(--color-text-muted)]">
                        Puedes solicitar una demo completando nuestro formulario de contacto o llamando a nuestra línea de atención. Te contactaremos para coordinar una demostración personalizada.
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg border border-[var(--color-surface-alt)]">
                    <button class="w-full px-6 py-4 text-left flex items-center justify-between font-semibold text-[var(--color-text)]">
                        <span>¿Qué tipos de instituciones pueden usar EvalAcademic?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                    </button>
                    <div class="px-6 pb-4 text-[var(--color-text-muted)]">
                        Nuestra plataforma está diseñada para instituciones educativas de todos los niveles: universidades, colegios, institutos técnicos y centros de formación continua.
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg border border-[var(--color-surface-alt)]">
                    <button class="w-full px-6 py-4 text-left flex items-center justify-between font-semibold text-[var(--color-text)]">
                        <span>¿Ofrecen capacitación para el uso de la plataforma?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                    </button>
                    <div class="px-6 pb-4 text-[var(--color-text-muted)]">
                        Sí, ofrecemos sesiones de capacitación personalizadas, documentación completa y soporte continuo para garantizar el éxito en la implementación.
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-2xl shadow-lg border border-[var(--color-surface-alt)]">
                    <button class="w-full px-6 py-4 text-left flex items-center justify-between font-semibold text-[var(--color-text)]">
                        <span>¿Cuál es el tiempo de implementación promedio?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-[var(--color-text-muted)]"></i>
                    </button>
                    <div class="px-6 pb-4 text-[var(--color-text-muted)]">
                        El tiempo varía según el tamaño de la institución, pero normalmente la implementación básica toma entre 2 a 4 semanas.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">¿No encuentras lo que buscas?</h2>
            <p class="text-[var(--color-primary)]/80 mb-8 text-lg">
                Nuestro equipo de soporte está disponible para ayudarte con cualquier consulta
            </p>
            <button class="px-8 py-3 bg-white text-[var(--color-primary)] rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Contactar Soporte 24/7
            </button>
        </div>
    </div>
</section>