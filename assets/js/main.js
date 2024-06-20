import { loadAdminInterface } from './adminInterface.js';
import { loadStudentInterface } from './studentInterface.js';

$(document).ready(() => {
    loadSelectionInterface();
});

export function loadSelectionInterface() {
    $('#content').html(`
        <div id="role-selection" class="bg-white p-8 rounded-lg shadow-md fade-in">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-4">¿Quién eres?</h1>
                <p class="text-gray-600 mb-6">Selecciona tu rol para continuar.</p>
                <div class="flex justify-center space-x-4">
                    <button id="admin-btn" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-300">Soy Administrativo</button>
                    <button id="student-btn" class="px-6 py-2 bg-white text-black border rounded-lg hover:bg-gray-200 transition duration-300">Soy Estudiante</button>
                </div>
            </div>
        </div>
        <div class="text-center mt-20">
            <p class="text-gray-500 text-sm mb-2">Powered by</p>
            <div class="flex justify-center space-x-4">
                <img src="../assets/img/logo.png" alt="Logo 1" class="h-20">
                <img src="../assets/img/logo2.png" alt="Logo 2" class="h-20">
            </div>
        </div>
    `);

    $('#admin-btn').click(() => {
        loadAdminInterface();
    });

    $('#student-btn').click(() => {
        loadStudentInterface();
    });
}

export function handleLogin(url, userType) {
    const idUser = $('#idUser').val();
    const password = userType === 'admin' ? $('#password').val() : null;

    if (!idUser) {
        displayMessage('El campo ID/carné es obligatorio.', 'error');
        return;
    }

    const data = { idUser };
    if (userType === 'admin') data.password = password;
    
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: (response) => {
            console.log(response);
            if (response.success) {
                displayMessage(response.message, 'success', response.redirect);
            } else {
                displayMessage(response.message, 'error');
            }
        },
        error: () => {
            displayMessage('Ha ocurrido un error al intentar iniciar sesión. Por favor, inténtalo de nuevo.', 'error');
        }
    });
}

function displayMessage(message, type, redirect = null) {
    const messageTypes = {
        success: {
            bgColor: 'bg-green-100',
            textColor: 'text-green-600',
            icon: `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-11.707a1 1 0 00-1.414-1.414L9 8.586 7.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            `
        },
        error: {
            bgColor: 'bg-red-100',
            textColor: 'text-red-600',
            icon: `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            `
        }
    };

    const { bgColor, textColor, icon } = messageTypes[type];
    const messageContainer = $(`
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-auto shadow-lg">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center ${bgColor} rounded-full mr-4">
                        ${icon}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold ${textColor}">${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
                        <p class="text-gray-600">${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('body').append(messageContainer);

    setTimeout(() => {
        messageContainer.fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);

    if (redirect) {
        setTimeout(() => {
            window.location.href = redirect;
        }, 7000);
    }
}