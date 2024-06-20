import { handleLogin, loadSelectionInterface } from './main.js';

export function loadAdminInterface() {
    $('#content').html(`
        <form id="login-form" class="relative w-full max-w-md mb-8 fade-in">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <button type="button" id="back-btn" class="absolute top-3 left-4 text-blue-500 hover:underline flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Atrás
                </button>
                <h2 class="text-2xl font-bold mt-4 mb-4">¡Nos alegra verte!</h2>
                <p class="mb-4">Ingresa tu ID/carné y contraseña para acceder a tu cuenta.</p>
                <input class="w-full p-3 mb-4 border border-gray-300 rounded-lg" type="text" id="idUser" name="documento_identidad" placeholder="Ingresa tu ID/carné" required>
                <input class="w-full p-3 mb-4 border border-gray-300 rounded-lg" type="password" id="password" name="contrasena" placeholder="Ingresa tu contraseña">
                <button type="submit" class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800">Iniciar sesión</button>
                <p class="mt-4"><a href="#" class="text-blue-500 hover:underline">¿Olvidaste tu contraseña?</a></p>
                <div class="text-center mb-4 mt-8">
                    <p class="text-gray-500 text-sm mb-2">Powered by</p>
                    <div class="flex justify-center space-x-4">
                        <img src="../assets/img/logo.png" alt="Logo 1" class="h-20">
                        <img src="../assets/img/logo2.png" alt="Logo 2" class="h-20">
                    </div>
                </div>
            </div>
        </form>
        <div id="message" class="mt-4"></div>
    `);

    $('#back-btn').click(() => {
        loadSelectionInterface();
    });

    $('#login-form').submit((event) => {
        event.preventDefault();
        handleLogin('endpoint/adminAuth', 'admin');
    });
}