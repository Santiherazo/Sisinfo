// Inicializar iconos de Lucide
lucide.createIcons();

// Cerrar banners
document.getElementById('close-top-banner').addEventListener('click', function() {
    document.getElementById('top-banner').style.display = 'none';
    document.getElementById('main-nav').style.top = '0';
    document.getElementById('hero').style.marginTop = '64px';
});

document.getElementById('close-bottom-banner').addEventListener('click', function() {
    this.closest('.bg-gradient-to-r').style.display = 'none';
});

// Navegación suave
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Cambiar navbar en scroll
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('main-nav');
    if (window.scrollY > 50) {
        navbar.classList.add('backdrop-blur-md', 'bg-white/80', 'shadow-md');
    } else {
        navbar.classList.remove('backdrop-blur-md', 'bg-white/80', 'shadow-md');
    }
});

// Slider del Hero
const slides = document.querySelectorAll('.hero-slider > div');
const dots = document.querySelectorAll('.slider-dot');
let currentSlide = 0;

function showSlide(n) {
    slides.forEach(slide => slide.style.opacity = '0');
    dots.forEach(dot => dot.classList.replace('bg-white/50', 'bg-white/30'));
    
    slides[n].style.opacity = '100';
    dots[n].classList.replace('bg-white/30', 'bg-white/50');
    currentSlide = n;
}

dots.forEach(dot => {
    dot.addEventListener('click', function() {
        showSlide(parseInt(this.getAttribute('data-slide')));
    });
});

// Cambio automático de slides
setInterval(() => {
    showSlide((currentSlide + 1) % slides.length);
}, 5000);

// Animación de elementos al hacer scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fadeIn');
        }
    });
}, observerOptions);

document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
});

// Alternar tema claro/oscuro
const themeToggle = document.querySelector('[data-lucide="sun"]').closest('button');
let isDark = false;

themeToggle.addEventListener('click', function() {
    isDark = !isDark;
    document.documentElement.classList.toggle('dark', isDark);
    
    const sunIcon = this.querySelector('[data-lucide="sun"]');
    const moonIcon = this.querySelector('[data-lucide="moon"]');
    
    if (isDark) {
        sunIcon.style.display = 'none';
        if (!moonIcon) {
            const moon = document.createElement('i');
            moon.setAttribute('data-lucide', 'moon');
            this.appendChild(moon);
            lucide.createIcons();
        }
    } else {
        sunIcon.style.display = 'block';
        if (moonIcon) moonIcon.remove();
    }
});

// Suscripción a newsletter
const subscribeBtn = document.querySelector('input[type="email"]').closest('div').querySelector('button');

subscribeBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const email = this.closest('div').querySelector('input[type="email"]').value;
    
    if (email) {
        this.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Suscrito';
        this.classList.add('bg-green-500');
        lucide.createIcons();
        
        setTimeout(() => {
            this.innerHTML = 'Suscribirse <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>';
            this.classList.remove('bg-green-500');
            lucide.createIcons();
        }, 3000);
    }
});

// Animación de contadores
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start).toLocaleString();
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    }
    
    updateCounter();
}

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.text-3xl');
            counters.forEach(counter => {
                const text = counter.textContent;
                const number = parseInt(text.replace(/[^\d]/g, ''));
                if (number) {
                    animateCounter(counter, number);
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const heroStats = document.querySelector('#hero .grid');
if (heroStats) {
    statsObserver.observe(heroStats);
}