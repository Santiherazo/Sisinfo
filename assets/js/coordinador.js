$(document).ready(function() {
    var selectedRole = '';
    var searchQuery = '';
    var usersData = [];

    $('#logoutButton').click(logout);

    function logout() {
        $.get('/logout', function() {
            location.reload();
        });
    }

    function filterAndSearchUsers() {
        selectedRole = $('#user-role-filter').val();
        searchQuery = $('#user-search').val().toLowerCase();
        loadUserTable();
    }

    function loadUserTable() {
        $.ajax({
            url: 'endpoint/users',
            type: 'GET',
            success: function(response) {
                try {
                    usersData = JSON.parse(response);
                    if (Array.isArray(usersData)) {
                        displayUsers(usersData.filter(filterUsers));
                    } else {
                        showError();
                    }
                } catch (error) {
                    showError();
                }
            },
            error: showError
        });
    }

    function filterUsers(user) {
        var matchesRole = !selectedRole || user.rol === selectedRole;
        var matchesSearch = !searchQuery || user.nombre_completo.toLowerCase().includes(searchQuery) || user.correo_electronico.toLowerCase().includes(searchQuery);
        return matchesRole && matchesSearch;
    }

    function showError() {
        displayMessage('Error al cargar los datos de usuarios.', 'error');
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
            row.append(createCell(user.nombre_completo));
            row.append(createCell(user.correo_electronico));
            row.append(createCell(user.documento_identidad));
            row.append(createCell(user.carnet));
            row.append(createCell(user.rol));
            row.append(createCell(user.institucion));
            row.append(createCell(user.ciudad));
            row.append(createCell(user.pais));
            row.append(createCell(user.fecha_registro));
            var commandsTd = $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900');
            commandsTd.append(createButton('text-blue-500 edit-user', user.id, 'edit', '<i class="fas fa-edit"></i>'));
            commandsTd.append(createButton('text-red-500 delete-user', user.id, 'delete', '<i class="fas fa-trash"></i>'));
            row.append(commandsTd);
            tableBody.append(row);
        });

        $('.edit-user').click(function() {
            openEditPopup($(this).data('id'));
        });

        $('.delete-user').click(function() {
            openDeletePopup($(this).data('id'));
        });
    }

    function createCell(content) {
        return $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(content || 'N/A');
    }

    function createButton(className, id, action, icon) {
        return $('<button></button>').addClass(className).attr('data-id', id).attr('data-action', action).html(icon);
    }

    function openDeletePopup(userId) {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            $.ajax({
                url: 'endpoint/deleteUser',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: userId }),
                success: function(response) {
                    if (response.success) {
                        displayMessage('Usuario eliminado correctamente.', 'success');
                        loadUserTable();
                    } else {
                        displayMessage(response.error || 'Error al eliminar el usuario.', 'error');
                    }
                },
                error: function() {
                    displayMessage('Error al eliminar el usuario.', 'error');
                }
            });
        }
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
            displayMessage('Usuario no encontrado.', 'error');
        }
    }

    $('#close-popup').click(function() {
        $('#edit-user-popup').addClass('hidden');
    });

    $('#edit-user-form').submit(function(e) {
        e.preventDefault();
        var userData = {
            id: $('#edit-user-id').val(),
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
        $.ajax({
            url: 'endpoint/editUser',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function(response) {
                if (response.success) {
                    displayMessage('Usuario actualizado correctamente.', 'success');
                    $('#edit-user-popup').addClass('hidden');
                    loadUserTable();
                } else {
                    displayMessage(response.error || 'Error al actualizar el usuario.', 'error');
                }
            },
            error: function() {
                displayMessage('Error al actualizar el usuario.', 'error');
            }
        });
    });

    loadUserTable();

    function displayMessage(message, type) {
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
    }
});