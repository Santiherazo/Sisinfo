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
</div>

<script>
    $(document).ready(function() {
        var selectedRole = '';
        var searchQuery = '';
        var usersData = [];

        function filterAndSearchUsers() {
            selectedRole = $('#user-role-filter').val();
            searchQuery = $('#user-search').val().toLowerCase();
            loadUserTable();
            setInterval(function() {
                loadUserTable();
            }, 20000);
        }

        function loadUserTable() {
            $.ajax({
                url: 'endpoint/users',
                type: 'GET',
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        if (Array.isArray(data)) {
                            usersData = data;
                            var filteredUsers = data.filter(function(user) {
                                var matchesRole = !selectedRole || user.rol === selectedRole;
                                var matchesSearch = !searchQuery || user.nombre_completo.toLowerCase().includes(searchQuery) || user.correo_electronico.toLowerCase().includes(searchQuery);
                                return matchesRole && matchesSearch;
                            });
                            displayUsers(filteredUsers);
                        } else {
                            $('#error-message').removeClass('hidden');
                        }
                    } catch (error) {
                        $('#error-message').removeClass('hidden');
                    }
                },
                error: function() {
                    $('#error-message').removeClass('hidden');
                }
            });
        }

        $('#user-role-filter').change(filterAndSearchUsers);
        $('#user-search').on('input', filterAndSearchUsers);
        $('#clear-filter').click(function() {
            $('#user-role-filter').val('');
            filterAndSearchUsers();
        });

        $('#clear-search').click(function() {
            $('#user-search').val('');
            filterAndSearchUsers();
        });

        function displayUsers(users) {
            var tableBody = $('#user-table-body');
            tableBody.empty();
            users.forEach(function(user) {
                var row = $('<tr></tr>');
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.nombre_completo || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.correo_electronico || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.documento_identidad || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.carnet || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.rol || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.institucion || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.ciudad || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.pais || 'N/A'));
                row.append($('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(user.fecha_registro || 'N/A'));
                var commandsTd = $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900');
                var editButton = $('<button></button>').addClass('text-blue-500 edit-user').attr('data-id', user.id).html('<i class="fas fa-edit"></i>');
                var deleteButton = $('<button></button>').addClass('text-red-500 delete-user').attr('data-id', user.id).html('<i class="fas fa-trash"></i>');
                commandsTd.append(editButton);
                commandsTd.append(deleteButton);
                row.append(commandsTd);
                tableBody.append(row);
            });

            $('.edit-user').click(function() {
                var userId = $(this).data('id');
                openEditPopup(userId);
            });

            $('.delete-user').click(function() {
                var userId = $(this).data('id');
                openDeletePopup(userId);
            });
        }

        function openDeletePopup(userId){
            var popup = $('#delete-user-popup');
            popup.find('.delete-user-popup-title').text('¿Está seguro de eliminar el usuario?');
            popup.find('.delete-user-popup-body').text('Esta acción no se puede deshacer.');
            popup.find('.delete-user-popup-button').attr('data-id', userId).text('Eliminar');
            popup.modal('show');
        }

        function openEditPopup(userId) {
            var user = usersData.find(function(u) { return u.id === userId; });
            if (user) {
                $('#edit-user-id').val(user.id);
                $('#edit-user-nombre_completo').val(user.nombre_completo);
                $('#edit-user-correo_electronico').val(user.correo_electronico);
                $('#edit-user-documento_identidad').val(user.documento_identidad);
                $('#edit-user-carnet').val(user.carnet);
                $('#edit-user-rol').val(user.rol);
                $('#edit-user-institucion').val(user.institucion);
                $('#edit-user-ciudad').val(user.ciudad);
                $('#edit-user-pais').val(user.pais);
                $('#edit-user-popup').removeClass('hidden');
            } else {
                alert('Usuario no encontrado.');
            }
        }

        $('#close-popup').click(function() {
            $('#edit-user-popup').addClass('hidden');
        });

        $('#edit-user-form').submit(function(e) {
            e.preventDefault();
            var userId = $('#edit-user-id').val();
            var userData = {
                userID: userId,
                nombre_completo: $('#edit-user-nombre_completo').val(),
                correo_electronico: $('#edit-user-correo_electronico').val(),
                documento_identidad: $('#edit-user-documento_identidad').val(),
                contrasena: $('#edit-user-password').val(),
                carnet: $('#edit-user-carnet').val(),
                rol: $('#edit-user-rol').val(),
                institucion: $('#edit-user-institucion').val(),
                ciudad: $('#edit-user-ciudad').val(),
                pais: $('#edit-user-pais').val()
            };
            console.log(userData)
            $.ajax({
                url: 'endpoint/editUser',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(userData),
                success: function() {
                    alert('Usuario actualizado correctamente.');
                    $('#edit-user-popup').addClass('hidden');
                    loadUserTable();
                },
                error: function() {
                    alert('Error al actualizar el usuario.');
                }
            });
        });

        loadUserTable();
    });

    $('#logoutButton').click(function() {
        logout();
    });

    function logout() {
        $.get('/logout', function(data) {
            location.reload();
        });
    }

    $(".edit-user").on("click", function() {
                    var userId = $(this).data("user-id");
                    var userName = $(this).data("user-name");
                    var userEmail = $(this).data("user-email");
                    var userDocument = $(this).data("user-document");
                    var userCarnet = $(this).data("user-carnet");
                    var userRole = $(this).data("user-role");
                    var userInstitution = $(this).data("user-institution");
                    var userCity = $(this).data("user-city");
                    var userCountry = $(this).data("user-country");

                    $("#edit-user-id").val(userId);
                    $("#edit-user-nombre_completo").val(userName);
                    $("#edit-user-correo_electronico").val(userEmail);
                    $("#edit-user-documento_identidad").val(userDocument);
                    $("#edit-user-carnet").val(userCarnet);
                    $("#edit-user-rol").val(userRole);
                    $("#edit-user-institucion").val(userInstitution);
                    $("#edit-user-ciudad").val(userCity);
                    $("#edit-user-pais").val(userCountry);

                    $("#edit-user-popup").removeClass("hidden");
                });

                $("#cancel-edit").on("click", function() {
                    $("#edit-user-popup").addClass("hidden");
                });

                $("#edit-user-form").on("submit", function(event) {
                    event.preventDefault();

                    var userId = $("#edit-user-id").val();
                    var userName = $("#edit-user-nombre_completo").val();
                    var userEmail = $("#edit-user-correo_electronico").val();
                    var userDocument = $("#edit-user-documento_identidad").val();
                    var userCarnet = $("#edit-user-carnet").val();
                    var userRole = $("#edit-user-rol").val();
                    var userInstitution = $("#edit-user-institucion").val();
                    var userCity = $("#edit-user-ciudad").val();
                    var userCountry = $("#edit-user-pais").val();

                    $("#edit-user-popup").addClass("hidden");

                    $("#user-row-" + userId).find(".user-nombre_completo").text(userName);
                    $("#user-row-" + userId).find(".user-correo_electronico").text(userEmail);
                    $("#user-row-" + userId).find(".user-documento_identidad").text(userDocument);
                    $("#user-row-" + userId).find(".user-carnet").text(userCarnet);
                    $("#user-row-" + userId).find(".user-rol").text(userRole);
                    $("#user-row-" + userId).find(".user-institucion").text(userInstitution);
                    $("#user-row-" + userId).find(".user-ciudad").text(userCity);
                    $("#user-row-" + userId).find(".user-pais").text(userCountry);
                });
</script>
</body>
</html>