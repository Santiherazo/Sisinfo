<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <button id="users-tab" class="text-black font-bold border-b-2 border-black focus:outline-none">Users</button>
                <button id="projects-tab" class="text-gray-600 hover:text-black focus:outline-none">Projects</button>
                <button id="reports-tab" class="text-gray-600 hover:text-black focus:outline-none">Reports</button>
            </div>
            <button id="logout-button" class="bg-blue-500 text-white px-4 py-2 rounded-md">Logout</button>
        </div>
    </nav>
    <div class="container mx-auto mt-6 relative">
        <div id="users-section">
            <!-- Search and Filters -->
            <div class="flex justify-between mb-4">
                <input type="text" id="user-search" class="w-1/3 p-2 border rounded-md" placeholder="Search users...">
                <select id="user-role-filter" class="w-1/4 p-2 border rounded-md">
                    <option value="">Filter by role</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <!-- User Cards -->
            <div class="grid grid-cols-3 gap-6" id="user-cards"></div>
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
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
        <button id="add-button" class="fixed bottom-6 right-6 bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg">Agregar</button>
    </div>

    <!-- User Form Modal -->
    <div id="user-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-md w-1/2">
            <h2 class="text-xl font-bold mb-4" id="user-modal-title">Create User</h2>
            <form id="user-form" class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="fullName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="institution" class="block text-sm font-medium text-gray-700">Institution</label>
                    <input type="text" id="institution" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="identification" class="block text-sm font-medium text-gray-700">Identification</label>
                    <input type="text" id="identification" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" id="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="idCard" class="block text-sm font-medium text-gray-700">ID Card</label>
                    <input type="text" id="idCard" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="stateProvince" class="block text-sm font-medium text-gray-700">State/Province</label>
                    <input type="text" id="stateProvince" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                    <select id="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select country</option>
                        <!-- Add more country options as needed -->
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select role</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>
            </form>
            <div class="flex justify-end mt-4">
                <button id="close-modal" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2">Close</button>
                <button id="save-user" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#users-tab').click(function () {
                $('#users-section').show();
                $('#projects-section, #reports-section').hide();
                $(this).addClass('text-black font-bold border-b-2 border-black');
                $('#projects-tab, #reports-tab').removeClass('text-black font-bold border-b-2 border-black').addClass('text-gray-600');
            });

            $('#projects-tab').click(function () {
                $('#projects-section').show();
                $('#users-section, #reports-section').hide();
                $(this).addClass('text-black font-bold border-b-2 border-black');
                $('#users-tab, #reports-tab').removeClass('text-black font-bold border-b-2 border-black').addClass('text-gray-600');
            });

            $('#reports-tab').click(function () {
                $('#reports-section').show();
                $('#users-section, #projects-section').hide();
                $(this).addClass('text-black font-bold border-b-2 border-black');
                $('#users-tab, #projects-tab').removeClass('text-black font-bold border-b-2 border-black').addClass('text-gray-600');
            });

            $('#add-button').click(function () {
                $('#user-modal').removeClass('hidden');
            });

            $('#close-modal').click(function () {
                $('#user-modal').addClass('hidden');
            });

            $('#save-user').click(function () {
                // Save user logic here
                $('#user-modal').addClass('hidden');
            });

            $('#logout-button').click(function () {
                // Logout logic here
            });

            // Fetch and display users
            $.ajax({
                url: 'endpoint/users',  // Replace with your API endpoint
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    console.log(data);  // Añadir más detalles de depuración
                    if (Array.isArray(data)) {
                        var userCards = '';
                        data.forEach(function (user) {
                            userCards += `
                                <div class="bg-white p-4 rounded-lg shadow-md">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-lg font-bold">${user.nombre_completo}</div>
                                            <div class="text-gray-600">${user.rol}</div>
                                            <div class="text-gray-600">${user.correo_electronico}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#user-cards').html(userCards);
                    } else {
                        $('#error-message').removeClass('hidden');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("Error: ", textStatus, errorThrown);  // Añadir más detalles de depuración en caso de error
                    $('#error-message').removeClass('hidden');
                }
            });
        });
    </script>
</body>
</html>