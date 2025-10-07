<footer id="contacto" class="bg-[var(--color-surface)] border-t border-[var(--color-border)]">
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-8 text-center md:text-left">
                <div class="lg:col-span-2 mx-auto md:mx-0">
                    <a href="#" class="flex items-center space-x-3 mb-6 justify-center md:justify-start">
                        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] rounded-xl flex items-center justify-center">
                            <i data-lucide="award" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] bg-clip-text text-transparent"><?php echo config('website_name'); ?></span>
                            <p class="text-sm text-[var(--color-text-muted)]"><?php echo config('website_slogan'); ?></p>
                        </div>
                    </a>

                    <p class="text-[var(--color-text-muted)] mb-6 leading-relaxed max-w-md mx-auto md:mx-0"><?php echo config('about_us'); ?></p>

                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <i data-lucide="mail" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm"><?php echo config('contact_email'); ?></span>
                        </div>
                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <i data-lucide="phone" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm"><?php echo config('contact_phone'); ?></span>
                        </div>
                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <i data-lucide="map-pin" class="w-4 h-4 text-[var(--color-text-muted)]"></i>
                            <span class="text-sm"><?php echo config('contact_address'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="mx-auto md:mx-0">
                    <h4 class="font-semibold mb-4 text-[var(--color-heading)]">Plataforma</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Características</a></li>
                        <li><a href="#proyectos" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Proyectos</a></li>
                        <li><a href="#evaluaciones" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Evaluaciones</a></li>
                        <li><a href="#reportes" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Reportes</a></li>
                    </ul>
                </div>

                <div class="mx-auto md:mx-0">
                    <h4 class="font-semibold mb-4 text-[var(--color-heading)]">Recursos</h4>
                    <ul class="space-y-2">
                        <li><a href="/docs" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Documentación</a></li>
                        <li><a href="/tutorials" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Tutoriales</a></li>
                        <li><a href="/api" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">API</a></li>
                        <li><a href="/support" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Soporte</a></li>
                    </ul>
                </div>

                <div class="mx-auto md:mx-0">
                    <h4 class="font-semibold mb-4 text-[var(--color-heading)]">Empresa</h4>
                    <ul class="space-y-2">
                        <li><a href="#about" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Sobre Nosotros</a></li>
                        <li><a href="/team" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Equipo</a></li>
                        <li><a href="/careers" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Carreras</a></li>
                        <li><a href="#noticias" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Noticias</a></li>
                    </ul>
                </div>

                <div class="mx-auto md:mx-0">
                    <h4 class="font-semibold mb-4 text-[var(--color-heading)]">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo config('legal_terms_conditions'); ?>" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Términos de Uso</a></li>
                        <li><a href="<?php echo config('legal_privacy_policy'); ?>" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Política de Privacidad</a></li>
                        <li><a href="<?php echo config('legal_cookies_policy'); ?>" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Cookies</a></li>
                        <li><a href="/gdpr" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">GDPR</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-[var(--color-border)]"></div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 text-center">
                <div class="flex items-center space-x-2 text-sm text-[var(--color-text-muted)] mx-auto md:mx-0">
                    <span><?php echo config('website_copyright'); ?></span>
                </div>

                <div class="flex items-center space-x-4 mx-auto md:mx-0">
                    <a href="<?php echo config('social_link_facebook'); ?>" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors" target="_blank" rel="noopener noreferrer">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="<?php echo config('social_link_twitter'); ?>" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors" target="_blank" rel="noopener noreferrer">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </a>
                    <a href="<?php echo config('social_link_linkedin'); ?>" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors" target="_blank" rel="noopener noreferrer">
                        <i data-lucide="linkedin" class="w-5 h-5"></i>
                    </a>
                    <a href="<?php echo config('social_link_instagram'); ?>" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors" target="_blank" rel="noopener noreferrer">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>