$(document).ready(function() {
    $('#users-section').show();
    loadUserTable();

    $('#add-user-button').click(function() {
        $('#user-popup-title').text('Crear Usuario');
        $('#user-form')[0].reset();
        $('#user-id').val('');
        $('#user-popup').removeClass('hidden');
    });

    $('#mobile-menu-button').click(function() {
        $('#mobile-menu').toggleClass('hidden');
    });

    function showSection(sectionToShow) {
        $('#users-section, #projects-section, #reports-section').hide();
        $(`#${sectionToShow}`).show();
    }

    function handleTabClick(tabId, sectionId) {
        $(`#${tabId}`).on('click', function () {
            showSection(sectionId);
            $(this).addClass('text-black border-black').removeClass('text-gray-600');
            $('#users-tab, #projects-tab, #reports-tab').removeClass('text-black border-black').addClass('text-gray-600');
            if (sectionId === 'projects-section') {
                loadProjectTable();
            } else if (sectionId === 'users-section') {
                loadUserTable();
            }
        });
    }

    handleTabClick('users-tab', 'users-section');
    handleTabClick('projects-tab', 'projects-section');
    handleTabClick('reports-tab', 'reports-section');

    var selectedRole = '';
    var searchQuery = '';
    var selectedPhase = '';
    var usersData = [];
    var projectsData = [];

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
                        showError('users');
                    }
                } catch (error) {
                    showError('users');
                }
            },
            error: function() {
                showError('users');
            }
        });
    }

    function filterUsers(user) {
        var matchesRole = !selectedRole || user.rol === selectedRole;
        var matchesSearch = !searchQuery || user.nombre_completo.toLowerCase().includes(searchQuery) || user.documento_identidad.toString().includes(searchQuery.toString());
        return matchesRole && matchesSearch;
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
            openEditPopup($(this).data('id'), 'user');
        });

        $('.delete-user').click(function() {
            openDeletePopup($(this).data('id'), 'user');
        });
    }

    function createCell(content) {
        return $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900').text(content || 'N/A');
    }

    function createButton(className, id, action, icon) {
        return $('<button></button>').addClass(className).attr('data-id', id).attr('data-action', action).html(icon);
    }

    function openDeletePopup(id, type) {
        var confirmMessage = type === 'user' ? '¿Estás seguro de que deseas eliminar este usuario?' : '¿Estás seguro de que deseas eliminar este proyecto?';
        if (confirm(confirmMessage)) {
            var endpoint = type === 'user' ? 'endpoint/deleteUser' : 'endpoint/deleteProject';
            $.ajax({
                url: endpoint,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: id }),
                success: function(response) {
                    if (response.success) {
                        displayMessage(type === 'user' ? 'Usuario eliminado correctamente.' : 'Proyecto eliminado correctamente.', 'success');
                        if (type === 'user') {
                            loadUserTable();
                        } else {
                            loadProjectTable();
                        }
                    } else {
                        displayMessage(response.error || (type === 'user' ? 'Error al eliminar el usuario.' : 'Error al eliminar el proyecto.'), 'error');
                    }
                },
                error: function() {
                    displayMessage(type === 'user' ? 'Error al eliminar el usuario.' : 'Error al eliminar el proyecto.', 'error');
                }
            });
        }
    }

    function openEditPopup(id, type) {
        var data = type === 'user' ? usersData.find(function(u) { return u.id === id; }) : projectsData.find(function(p) { return p.id === id; });
        if (data) {
            if (type === 'user') {
                $('#user-popup-title').text('Editar Usuario');
                $('#user-id').val(data.id);
                $('#nombre_completo').val(data.nombre_completo);
                $('#correo_electronico').val(data.correo_electronico);
                $('#documento_identidad').val(data.documento_identidad);
                $('#carnet').val(data.carnet);
                $('#contrasena').val('');
                $('#rol').val(data.rol);
                $('#institucion').val(data.institucion);
                $('#direccion').val(data.direccion);
                $('#ciudad').val(data.ciudad);
                $('#estado_provincia').val(data.estado_provincia);
                $('#pais').val(data.pais);
                $('#user-popup').removeClass('hidden');
            } else {
                $('#edit-project-id').val(data.id);
                $('#edit-project-nombre').val(data.nombre);
                $('#edit-project-descripcion').val(data.descripcion);
                $('#edit-project-fecha_inicio').val(data.fecha_inicio);
                $('#edit-project-fecha_fin').val(data.fecha_fin);
                $('#edit-project-fase').val(data.fase);
                $('#edit-project-estado').val(data.estado);
                $('#project-popup').removeClass('hidden');
            }
        } else {
            displayMessage(type === 'user' ? 'Usuario no encontrado.' : 'Proyecto no encontrado.', 'error');
        }
    }

    $('#cancel-user-button').click(function() {
        $('#user-popup').addClass('hidden');
    });

    $('#cancel-project-button').click(function() {
        $('#project-popup').addClass('hidden');
    });

    $('#edit-user-form').submit(function(e) {
        e.preventDefault();
        var userData = {
            id: $('#user-id').val(),
            nombre_completo: $('#nombre_completo').val(),
            correo_electronico: $('#correo_electronico').val(),
            documento_identidad: $('#documento_identidad').val(),
            contrasena: $('#contrasena').val(),
            carnet: $('#carnet').val(),
            rol: $('#rol').val(),
            institucion: $('#institucion').val(),
            direccion: $('#direccion').val(),
            ciudad: $('#ciudad').val(),
            estado_provincia: $('#estado_provincia').val(),
            pais: $('#pais').val()
        };
        $.ajax({
            url: 'endpoint/editUser',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function(response) {
                if (response.success) {
                    displayMessage('Usuario actualizado correctamente.', 'success');
                    $('#user-popup').addClass('hidden');
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

    $('#edit-project-form').submit(function(e) {
        e.preventDefault();
        var projectData = {
            id: $('#edit-project-id').val(),
            nombre: $('#edit-project-nombre').val(),
            descripcion: $('#edit-project-descripcion').val(),
            fecha_inicio: $('#edit-project-fecha_inicio').val(),
            fecha_fin: $('#edit-project-fecha_fin').val(),
            fase: $('#edit-project-fase').val(),
            estado: $('#edit-project-estado').val()
        };
        $.ajax({
            url: 'endpoint/editProject',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(projectData),
            success: function(response) {
                if (response.success) {
                    displayMessage('Proyecto actualizado correctamente.', 'success');
                    $('#project-popup').addClass('hidden');
                    loadProjectTable();
                } else {
                    displayMessage(response.error || 'Error al actualizar el proyecto.', 'error');
                }
            },
            error: function() {
                displayMessage('Error al actualizar el proyecto.', 'error');
            }
        });
    });

    function displayMessage(message, type) {
        var messageBox = $('#message-box');
        messageBox.removeClass('hidden').text(message);
        messageBox.removeClass('bg-green-500 bg-red-500').addClass(type === 'success' ? 'bg-green-500' : 'bg-red-500');
        setTimeout(function() {
            messageBox.addClass('hidden');
        }, 3000);
    }

    $('#add-project-button').click(function() {
        $('#project-popup-title').text('Crear Proyecto');
        $('#project-form')[0].reset();
        $('#project-id').val('');
        $('#project-popup').removeClass('hidden');
    });

    function loadProjectTable() {
        $.ajax({
            url: 'endpoint/projects',
            type: 'GET',
            success: function(response) {
                try {
                    projectsData = JSON.parse(response);
                    if (Array.isArray(projectsData)) {
                        displayProjects(projectsData.filter(filterProjects));
                    } else {
                        showError('projects');
                    }
                } catch (error) {
                    showError('projects');
                }
            },
            error: function() {
                showError('projects');
            }
        });
    }

    function filterProjects(project) {
        var matchesPhase = !selectedPhase || project.fase === selectedPhase;
        var matchesSearch = !searchQuery || project.nombre.toLowerCase().includes(searchQuery) || project.descripcion.toLowerCase().includes(searchQuery);
        return matchesPhase && matchesSearch;
    }

    $('#project-phase-filter').change(function() {
        selectedPhase = $(this).val();
        loadProjectTable();
    });
    $('#project-search').on('input', function() {
        searchQuery = $(this).val().toLowerCase();
        loadProjectTable();
    });

    function displayProjects(projects) {
        var tableBody = $('#project-table-body');
        tableBody.empty();
        projects.forEach(function(project) {
            var row = $('<tr></tr>');
            row.append(createCell(project.nombre));
            row.append(createCell(project.descripcion));
            row.append(createCell(project.fecha_inicio));
            row.append(createCell(project.fecha_fin));
            row.append(createCell(project.fase));
            row.append(createCell(project.estado));
            var commandsTd = $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900');
            commandsTd.append(createButton('text-blue-500 edit-project', project.id, 'edit', '<i class="fas fa-edit"></i>'));
            commandsTd.append(createButton('text-red-500 delete-project', project.id, 'delete', '<i class="fas fa-trash"></i>'));
            row.append(commandsTd);
            tableBody.append(row);
        });

        $('.edit-project').click(function() {
            openEditPopup($(this).data('id'), 'project');
        });

        $('.delete-project').click(function() {
            openDeletePopup($(this).data('id'), 'project');
        });
    }

    function showError(type) {
        var message = type === 'users' ? 'Error al cargar los usuarios.' : 'Error al cargar los proyectos.';
        displayMessage(message, 'error');
    }
});