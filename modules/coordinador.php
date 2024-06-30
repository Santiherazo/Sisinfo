<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-black focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex-shrink-0">
                        <a href="#" class="text-xl font-bold text-black">Dashboard</a>
                    </div>
                    <div class="hidden sm:block sm:ml-6">
                        <div class="flex space-x-4">
                            <a id="users-tab" class="text-gray-600 hover:text-black font-medium py-2 px-4 border-b-2 border-transparent">Usuarios</a>
                            <a id="projects-tab" class="text-gray-600 hover:text-black font-medium py-2 px-4 border-b-2 border-transparent">Proyectos</a>
                            <a id="reports-tab" class="text-gray-600 hover:text-black font-medium py-2 px-4 border-b-2 border-transparent">Reportes</a>
                        </div>
                    </div>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <button id="logoutButton" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cerrar sesión</button>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
        <div id="users-section" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-black">Usuarios</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select id="user-role-filter" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Todos los roles</option>
                            <option value="Estudiante">Estudiante</option>
                            <option value="Evaluador">Evaluador</option>
                            <option value="Coordinador">Coordinador</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-600">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l-4.95 4.95a1 1 0 0 1-1.414-1.414l4.95-4.95a1 1 0 0 1 1.414 1.414z"/></svg>
                        </div>
                    </div>
                    <div class="relative">
                        <input id="user-search" type="text" placeholder="Buscar por nombre" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button id="clear-search" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Limpiar</button>
                </div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Nombre</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Correo</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Documento</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Carnet</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Rol</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Institución</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Ciudad</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">País</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Fecha Registro</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                    </tbody>
                </table>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline">¡Error al cargar datos!</span>
                </div>
            </div>
        </div>
        <div id="projects-section" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-black">Proyectos</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select id="project-phase-filter" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Todas las fases</option>
                            <option value="Propuesta">Propuesta</option>
                            <option value="Desarrollo">Desarrollo</option>
                            <option value="Aplicación">Aplicación</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-600">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l-4.95 4.95a1 1 0 0 1-1.414-1.414l4.95-4.95a1 1 0 0 1 1.414 1.414z"/></svg>
                        </div>
                    </div>
                    <div class="relative">
                        <input id="project-search" type="text" placeholder="Buscar por título" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button id="clear-search" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Limpiar</button>
                </div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Investigadores</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Docentes</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Linea</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Evaluador</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Fase</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Titulo</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Tiempo</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Controles</th>
                        </tr>
                    </thead>
                    <tbody id="project-table-body">
                    </tbody>
                </table>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline">¡Error al cargar datos!</span>
                </div>
            </div>
        </div>
        <div id="reports-section" class="hidden">
            <h2 class="text-2xl font-bold text-black">Reportes</h2>
            <p class="mt-4">Aquí irían los reportes si tuvieras alguno para mostrar.</p>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../assets/js/coordinador.js"></script>
</body>
</html>