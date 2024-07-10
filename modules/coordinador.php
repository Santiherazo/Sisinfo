<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <nav class="bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a class="text-xl font-bold text-white cursor-default">Rueda de Proyectos</a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a id="users-tab" class="text-white hover:text-blue-400 font-medium py-2 px-4 border-b-2 border-transparent hover:border-blue-400 transition duration-300 cursor-pointer">Usuarios</a>
                            <a id="projects-tab" class="text-white hover:text-blue-400 font-medium py-2 px-4 border-b-2 border-transparent hover:border-blue-400 transition duration-300 cursor-pointer">Proyectos</a>
                            <a id="reports-tab" class="text-white hover:text-blue-400 font-medium py-2 px-4 border-b-2 border-transparent hover:border-blue-400 transition duration-300 cursor-pointer">Reportes</a>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <button id="logoutButton" class="btn btn-outline btn-error text-white font-semibold py-2 px-4 rounded cursor-pointer">Cerrar sesión</button>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-blue-400 focus:outline-none transition duration-300 cursor-pointer">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="md:hidden">
            <div id="mobile-menu" class="px-2 pt-2 pb-3 space-y-1 sm:px-3 hidden">
                <a id="users-tab-mobile" class="block text-white hover:text-blue-400 py-2 px-3 rounded-md text-base font-medium cursor-pointer">Usuarios</a>
                <a id="projects-tab-mobile" class="block text-white hover:text-blue-400 py-2 px-3 rounded-md text-base font-medium cursor-pointer">Proyectos</a>
                <a id="reports-tab-mobile" class="block text-white hover:text-blue-400 py-2 px-3 rounded-md text-base font-medium cursor-pointer">Reportes</a>
                <a id="logoutButton-mobile" class="block btn btn-outline btn-error text-white font-semibold py-2 px-3 rounded cursor-pointer">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div id="users-section" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Usuarios</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select id="user-role-filter" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded-md shadow-sm leading-tight focus:outline-none focus:shadow-outline">
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
                        <input id="user-search" type="text" placeholder="Buscar por nombre" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 rounded-md shadow-sm leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button id="clear-search" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Limpiar</button>
                    <button id="add-user-button" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Agregar Usuario</button>
                </div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md overflow-y-auto relative">
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
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mt-4" role="alert">
                    <span class="block sm:inline">¡Error al cargar datos!</span>
                </div>
            </div>
        </div>

        <!-- Sección de Proyectos -->
        <div id="projects-section" class="hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Proyectos</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select id="project-phase-filter" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded-md shadow-sm leading-tight focus:outline-none focus:shadow-outline">
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
                        <input id="project-search" type="text" placeholder="Buscar por título" class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 rounded-md shadow-sm leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button id="clear-search" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Limpiar</button>
                    <button id="add-project-button" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Agregar Proyecto</button>
                </div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Título</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Descripción</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Fecha Inicio</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Fecha Fin</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Fase</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Responsable</th>
                            <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="project-table-body">
                    </tbody>
                </table>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mt-4" role="alert">
                    <span class="block sm:inline">¡Error al cargar datos!</span>
                </div>
            </div>
        </div>

        <!-- Sección de Reportes -->
        <div id="reports-section" class="hidden">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Reportes</h2>
            <div class="flex items-center space-x-4 mb-4">
                <button id="download-report" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Descargar Reporte</button>
            </div>
            <div id="report-content" class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-700">Generación de reportes aquí...</p>
            </div>
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mt-4" role="alert">
                <span class="block sm:inline">¡Error al cargar datos!</span>
            </div>
        </div>
    </div>

    <!-- Popup Crear/Editar Usuario -->
<div id="user-popup" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="user-popup-title">Crear Usuario</h3>
                        <div class="mt-2">
                            <form id="user-form">
                                <input type="hidden" id="user-id">
                                <div class="mb-4">
                                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                    <input type="text" id="nombre_completo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                    <input type="email" id="correo_electronico" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="documento_identidad" class="block text-sm font-medium text-gray-700">Documento de Identidad</label>
                                    <input type="text" id="documento_identidad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="carnet" class="block text-sm font-medium text-gray-700">Carnet</label>
                                    <input type="text" id="carnet" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
                                    <input type="password" id="contrasena" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                                    <select id="rol" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="Estudiante">Estudiante</option>
                                        <option value="Evaluador">Evaluador</option>
                                        <option value="Coordinador">Coordinador</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="institucion" class="block text-sm font-medium text-gray-700">Institución</label>
                                    <input type="text" id="institucion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                                    <input type="text" id="direccion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="ciudad" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                    <input type="text" id="ciudad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="estado_provincia" class="block text-sm font-medium text-gray-700">Estado/Provincia</label>
                                    <input type="text" id="estado_provincia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="pais" class="block text-sm font-medium text-gray-700">País</label>
                                    <input type="text" id="pais" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="save-user-button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Guardar</button>
                <button id="cancel-user-button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancelar</button>
            </div>
        </div>
    </div>
</div>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../assets/js/coordinador.js"></script>
</body>
</html>