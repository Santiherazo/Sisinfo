import { handleLogin, loadSelectionInterface } from './main.js';

export function loadStudentInterface() {
    $('#content').html(`
        <form id="login-form" class="relative w-full max-w-md fade-in">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <button type="button" id="back-btn" class="absolute top-3 left-4 text-blue-500 hover:underline flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Atrás
                </button>
                <h2 class="text-2xl font-bold mt-4 mb-4">¡Nos alegra verte!</h2>
                <p class="mb-4">Ingresa tu ID para ver los resultados</p>
                <input class="w-full p-3 mb-4 border border-gray-300 rounded-lg" type="text" id="idUser" name="idUser" placeholder="Ingresa tu ID">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-500">Ver resultados</button>
                <div class="text-center mb-4 mt-8">
                    <div class="flex justify-center space-x-4">
                        <img src="../assets/img/logo.png" alt="Logo 1" class="h-20">
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
        handleLogin('endpoint/studentAuth', 'student');
    });
}
