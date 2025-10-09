<?php
if (!defined('access') || access !== 'install') exit;

if (isset($_GET['action']) && $_GET['action'] === 'install') {
    $_SESSION['install_cstep']++;
    header('Location: install.php');
    exit;
}
?>

<div class="max-w-5xl mx-auto space-y-12 text-gray-800 p-6 sm:p-10">
    
    <div class="bg-gradient-to-r from-blue-50 to-white p-8 rounded-xl shadow-md border border-blue-200">
        <h1 class="text-4xl font-extrabold text-blue-700 mb-4">Bienvenido a Sisinfo CMS</h1>
        <p class="text-gray-700 text-lg leading-relaxed">
            Gracias por confiar en <strong>Sisinfo CMS</strong>. Este asistente verificará los requisitos esenciales del sistema
            y te guiará paso a paso para completar la instalación de manera segura y rápida.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-3">Soporte Técnico</h2>
            <p class="text-gray-600 mb-3">¿Necesitas ayuda durante el proceso de instalación?</p>
            <ul class="space-y-2 text-gray-700 text-sm">
                <li><span class="font-medium">Email:</span> <a href="mailto:herazopsantiago@gmail.com" class="text-blue-600 hover:underline">herazopsantiago@gmail.com</a></li>
                <li><span class="font-medium">WhatsApp:</span> <a href="https://wa.me/573007639973" target="_blank" class="text-blue-600 hover:underline">+57 300 763 9973</a></li>
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-3">Versión del Sistema</h2>
            <p class="text-gray-600 mb-4">
                Asegúrate de tener la última versión descargada desde nuestro repositorio oficial.
            </p>
            <a href="https://github.com/Santiherazo/Sisinfov1" target="_blank"
               class="inline-block px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow">
               Ir al Repositorio
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Licencia de Uso</h2>
        <div class="bg-gray-100 border border-gray-300 p-5 rounded text-sm max-h-72 overflow-y-auto leading-relaxed text-gray-700">
            <p><strong>Nombre del software:</strong> Sisinfo</p>
            <p><strong>Versión:</strong> 1.0</p>
            <p><strong>Titular:</strong> Santiago Herazo</p>

            <p class="mt-4"><strong>DERECHOS DE AUTOR</strong><br>© Santiago Herazo. Todos los derechos reservados.</p>

            <p class="mt-4"><strong>OBJETO DE LA LICENCIA</strong><br>Uso limitado, no exclusivo y no comercial en un solo dispositivo.</p>

            <p class="mt-4"><strong>CONDICIONES DE USO</strong><br>
            - Licencia permanente tras compra legal.<br>
            - Uso personal y no comercial.</p>

            <p class="mt-4"><strong>RESTRICCIONES</strong><br>
            - No redistribuir, modificar ni revender.<br>
            - Prohibido aplicar ingeniería inversa.</p>

            <p class="mt-4"><strong>SOPORTE TÉCNICO</strong><br>Incluye soporte básico durante la instalación.</p>

            <p class="mt-4"><strong>ACTUALIZACIONES</strong><br>No incluye actualizaciones automáticas ni gratuitas.</p>

            <p class="mt-4"><strong>TERMINACIÓN</strong><br>El incumplimiento revoca la licencia inmediatamente.</p>

            <p class="mt-4"><strong>NOTA FINAL</strong><br>Al instalar Sisinfo, aceptas todos los términos aquí descritos.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Agradecimientos</h2>
        <p class="text-gray-700">
            Este sistema fue desarrollado con profesionalismo y dedicación para facilitar la gestión de tus procesos.
            Gracias por tu confianza. Estamos siempre abiertos a sugerencias.
        </p>
    </div>

    <div class="text-center">
        <p class="text-gray-700 text-lg mb-5">Cuando estés listo, inicia la instalación:</p>
        <a href="?action=install"
           class="relative inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-emerald-700 transition duration-300 group overflow-hidden">
            <span class="absolute left-0 w-1 h-full bg-white opacity-10 group-hover:opacity-20 transition"></span>
            <svg class="w-5 h-5 mr-3 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M6 4l8 6-8 6V4z"/>
            </svg>
            Iniciar Instalación
        </a>
    </div>
</div>