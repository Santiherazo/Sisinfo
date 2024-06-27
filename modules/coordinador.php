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
            <button id="logout-button" class="bg-red-500 text-white px-4 py-2 rounded-md">Logout</button>
        </div>
    </nav>
    <div class="container mx-auto mt-6 relative">
        <div id="users-section">
            <div class="flex justify-between mb-4">
                <input type="text" id="user-search" class="w-1/3 p-2 border rounded-md" placeholder="Search users...">
                <select id="user-role-filter" class="w-1/4 p-2 border rounded-md">
                    <option value="">Filter by role</option>
                    <option value="Evaluador">Evaluador</option>
                    <option value="Estudiante">Estudiante</option>
                </select>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
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
                    <tbody id="user-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Aquí se cargarán dinámicamente los datos de los usuarios -->
                    </tbody>
                </table>
            </div>
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">Hay problemas para cargar la información.</span>
            </div>
        </div>
        <div id="projects-section" class="grid grid-cols-3 gap-6 hidden">
            <!-- Project Cards -->
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
            <!-- Add more project cards as needed -->
        </div>
        <div id="reports-section" class="grid grid-cols-2 gap-6 hidden">
            <!-- Report Cards (Placeholders) -->
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

    <script>
      $(document).ready(function() {
    // Cargar la tabla de usuarios cuando la página cargue
    loadUserTable();
    
    function loadUserTable() {
        $.ajax({
            url: 'endpoint/users',
            type: 'GET',
            contentType: 'application/json',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    console.log("Datos recibidos:", data);
                    
                    if (Array.isArray(data)) {
                        displayUsers(data);
                    } else {
                        console.error("Los datos no son un arreglo:", data);
                        $('#error-message').removeClass('hidden');
                    }
                } catch (error) {
                    console.error("Error al parsear JSON:", error);
                    $('#error-message').removeClass('hidden');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX: ", textStatus, errorThrown);
                $('#error-message').removeClass('hidden');
            }
        });
    }

    // Navegación por pestañas
    $('#users-tab').click(function() {
        $('#users-section').show();
        $('#projects-section').hide();
        $('#reports-section').hide();
        $('#users-tab').addClass('border-black text-black').removeClass('text-gray-600');
        $('#projects-tab, #reports-tab').removeClass('border-black text-black').addClass('text-gray-600');
    });

    // Abrir modal
    $('#add-button').click(function() {
        $('#user-modal').removeClass('hidden');
    });

    // Cerrar modal
    $('#close-modal').click(function() {
        $('#user-modal').addClass('hidden');
    });

    // Guardar usuario
    $('#save-user').click(function() {
        var userData = {
            nombre_completo: $('#fullName').val(),
            institucion: $('#institution').val(),
            correo_electronico: $('#email').val(),
            direccion: $('#address').val(),
            identificacion: $('#identification').val(),
            ciudad: $('#city').val(),
            cedula: $('#idCard').val(),
            provincia: $('#stateProvince').val(),
            contraseña: $('#password').val(),
            pais: $('#country').val(),
            rol: $('#role').val()
        };

        $.ajax({
            url: 'endpoint/users',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function() {
                $('#user-modal').addClass('hidden');
                loadUserTable(); // Recargar la tabla de usuarios después de añadir un nuevo usuario
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al guardar usuario: ", textStatus, errorThrown);
            }
        });
    });

    // Filtrar usuarios por rol
    $('#user-role-filter').change(function() {
        var selectedRole = $(this).val();
        filterUsers(selectedRole);
    });

    // Buscar usuarios por nombre
    $('#user-search').on('keyup', function() {
        var searchQuery = $(this).val().toLowerCase();
        filterUsersBySearch(searchQuery);
    });

    // Función para filtrar y mostrar usuarios
    function filterUsers(role) {
        $.ajax({
            url: 'endpoint/users',
            type: 'GET',
            contentType: 'application/json',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    console.log("Datos recibidos:", data);
                    
                    if (Array.isArray(data)) {
                        var filteredUsers = role ? data.filter(user => user.rol === role) : data;
                        displayUsers(filteredUsers);
                    } else {
                        console.error("Los datos no son un arreglo:", data);
                        $('#error-message').removeClass('hidden');
                    }
                } catch (error) {
                    console.error("Error al parsear JSON:", error);
                    $('#error-message').removeClass('hidden');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX: ", textStatus, errorThrown);
                $('#error-message').removeClass('hidden');
            }
        });
    }

    // Función para filtrar y mostrar usuarios por búsqueda
    function filterUsersBySearch(query) {
        $.ajax({
            url: 'endpoint/users',
            type: 'GET',
            contentType: 'application/json',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    console.log("Datos recibidos:", data);
                    
                    if (Array.isArray(data)) {
                        var filteredUsers = data.filter(user => {
                            return user.nombre_completo.toLowerCase().includes(query);
                        });
                        displayUsers(filteredUsers);
                    } else {
                        console.error("Los datos no son un arreglo:", data);
                        $('#error-message').removeClass('hidden');
                    }
                } catch (error) {
                    console.error("Error al parsear JSON:", error);
                    $('#error-message').removeClass('hidden');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX: ", textStatus, errorThrown);
                $('#error-message').removeClass('hidden');
            }
        });
    }

    // Función para mostrar usuarios en la tabla
    function displayUsers(users) {
        var tableBody = '';
        if (users.length > 0) {
            users.forEach(function(user) {
                tableBody += `
                    <tr>
                        <td>${user.nombre_completo}</td>
                        <td>${user.correo_electronico}</td>
                        <td>${user.documento_identidad}</td>
                        <td>${user.carnet}</td>
                        <td>${user.rol}</td>
                        <td>${user.institucion}</td>
                        <td>${user.ciudad}</td>
                        <td>${user.pais}</td>
                        <td>${user.fecha_registro}</td>
                        <td>delete</td>
                    </tr>
                `;
            });
        } else {
            tableBody = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-gray-500">No se encontraron usuarios</td>
                </tr>
            `;
        }
        $('#user-table-body').html(tableBody);
    }
});
    </script>
</body>
</html>