<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <button id="users-tab" class="text-black font-bold border-b-2 border-black focus:outline-none">Users</button>
                <button id="projects-tab" class="text-gray-600 hover:text-black focus:outline-none">Projects</button>
                <button id="reports-tab" class="text-gray-600 hover:text-black focus:outline-none">Reports</button>
            </div>
            <button id="logoutButton" class="bg-red-500 text-white text-xs px-2 py-1 rounded-md">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </nav>
    <div class="container mx-auto mt-6 relative">
        <div id="users-section">
            <div class="flex justify-between mb-4">
                <div class="relative w-1/3">
                    <input type="text" id="user-search" class="w-full p-2 border rounded-md" placeholder="Search users...">
                    <button id="clear-search" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="relative w-1/4">
                    <select id="user-role-filter" class="w-full p-2 border rounded-md">
                        <option value="">Filtra por rol</option>
                        <option value="Evaluador">Evaluador</option>
                        <option value="Estudiante">Estudiante</option>
                    </select>
                    <button id="clear-filter" class="absolute right-5 top-2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md mb-6">
                <table id="user-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre completo</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Correo electrónico</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Documento de identidad</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Carnet</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Institución</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">País</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de registro</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Comandos</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">Hay problemas para cargar la información.</span>
            </div>
        </div>
        <div id="projects-section" class="grid grid-cols-3 gap-6 hidden">
            <div class="bg-white p-4 rounded-lg shadow-md relative">
                <div class="flex justify-between">
                    <div>
                        <div class="text-lg font-bold">Project A</div>
                        <div class="text-gray-600">Website Redesign</div>
                    </div>
                    <div class="relative">
                        <button id="dropdownButton4" class="bg-gray-200 p-2 rounded-md">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdownMenu4" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg hidden">
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200 edit-project">Edit</a>
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="text-gray-800 font-semibold">Approved</div>
                <div class="text-gray-500">Created 2 months ago</div>
                <div class="text-gray-500">Assigned to John Doe</div>
            </div>
        </div>
        <div id="reports-section" class="grid grid-cols-2 gap-6 hidden">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="text-lg font-bold">Reporte de usuarios</div>
                <div class="text-gray-600"></div>
                <a class="text-gray-800 font-semibold">Usuarios totales: </a>
                <a class="text-gray-500">Estudiantes:</a>
                <a class="text-gray-500">Evaluadores:</a>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="text-lg font-bold">Reportes de proyectos</div>
                <div class="text-gray-600">Resumen de proyectos</div>
                <div class="text-gray-800 font-semibold">Proyectos totales: </div>
                <div class="text-gray-500">Aprobados:</div>
                <div class="text-gray-500">Rechazados:</div>
            </div>
        </div>
    </div>

    <div id="edit-user-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden overflow-y-auto">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg h-auto max-h-[80vh] mx-auto overflow-y-auto">
        <h2 class="text-2xl font-bold mb-4">Editar Usuario</h2>
        <form id="edit-user-form">
            <input type="hidden" id="edit-user-id">
            <div class="mb-4">
                <label for="edit-user-nombre_completo" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                <input type="text" id="edit-user-nombre_completo" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-correo_electronico" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" id="edit-user-correo_electronico" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-documento_identidad" class="block text-sm font-medium text-gray-700">Documento de identidad</label>
                <input type="text" id="edit-user-documento_identidad" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-password" class="block text-gray-700">Contraseña:</label>
                <input type="password" id="edit-user-password" class="w-full p-2 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <label for="edit-user-carnet" class="block text-sm font-medium text-gray-700">Carnet</label>
                <input type="text" id="edit-user-carnet" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-rol" class="block text-sm font-medium text-gray-700">Rol</label>
                <select id="edit-user-rol" class="w-full p-2 border rounded-md">
                    <option value="Evaluador">Evaluador</option>
                    <option value="Estudiante">Estudiante</option>
                    <option value="Coordinador">Coordinador</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit-user-institucion" class="block text-sm font-medium text-gray-700">Institución</label>
                <input type="text" id="edit-user-institucion" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-ciudad" class="block text-sm font-medium text-gray-700">Ciudad</label>
                <input type="text" id="edit-user-ciudad" class="w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="edit-user-pais" class="block text-sm font-medium text-gray-700">País</label>
                <input type="text" id="edit-user-pais" class="w-full p-2 border rounded-md">
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-popup" class="bg-red-500 text-white px-4 py-2 rounded-md mr-2">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Guardar</button>
            </div>
        </form>
    </div>

    <div id="successPopup" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="relative bg-white p-6 rounded-lg shadow-lg text-center fade-in">
            <button id="closePopupBtn" class="absolute top-0 right-0 mt-2 mr-2 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex justify-center mb-4">
                <svg class="h-16 w-16 text-green-500 scale-in" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-4">¡Éxito!</h2>
            <p class="mb-4">La respuesta fue enviada con éxito.</p>
        </div>
    </div>
</div>
<script src="../assets/js/coordinador.js"></script>
</body>
</html>