$(document).ready(function() {
    $('#users-section').show();
    loadUserTable();
    $('#users-tab').addClass('text-blue-400 border-blue-400 transition duration-300');

    $('#add-user-button').click(function() {
        $('#user-popup-title').text('Crear Usuario');
        $('#user-form')[0].reset();
        $('#user-id').val('');
        $('#pais').val('Colombia');
        $('#save-user-button').attr('id', 'save-new-user-button');
        $('#user-popup').removeClass('hidden');
    });

    $('#add-project-button').click(function() {
        $('#project-popup-title').text('Crear Proyecto');
        $('#project-form')[0].reset();
        $('#project-id').val('');
        $('#project-popup').removeClass('hidden');
    });

    $('#cancel-user-button').click(function() {
        $(this).closest('#user-popup').addClass('hidden');
    });

    $('#cancel-project-button').click(function() {
        $(this).closest('#project-popup').addClass('hidden');
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

            switch(sectionId){
                case 'users-section':
                    loadUserTable();
                    $('#users-tab').addClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#projects-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#reports-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    break;
                case 'projects-section':
                    loadProjectTable();
                    $('#users-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#projects-tab').addClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#reports-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    break;
                case 'reports-section':
                    generateReport();
                    $('#users-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#projects-tab').removeClass('text-blue-400 border-blue-400 transition duration-300');
                    $('#reports-tab').addClass('text-blue-400 border-blue-400 transition duration-300');
                    break;
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
            row.append(createCell(user.id));
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
            $('#save-user-button').attr('id', 'save-edit-user-button');
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
        var endpoint = type === 'user' ? 'endpoint/getUser' : 'endpoint/getProject';
        var formPrefix = type === 'user' ? '#user-' : '#project-';
        var popupSelector = type === 'user' ? '#user-popup' : '#project-popup';
        $.ajax({
            url: endpoint,
            type: 'GET',
            data: { id: id },
            success: function(response) {
                if (response) {
                    for (var key in response) {
                        $(formPrefix + key).val(response[key]);
                    }
                    $(popupSelector).removeClass('hidden');
                } else {
                    showError(type === 'user' ? 'user' : 'project');
                }
            },
            error: function() {
                showError(type === 'user' ? 'user' : 'project');
            }
        });
    }

    $('#save-new-user-button').click(function() {
        saveUser('create');
    });

    $('#save-edit-user-button').click(function() {
        saveUser('update');
    });

    function saveUser(action) {
        var user = {
            id: $('#user-id').val(),
            nombre_completo: $('#nombre_completo').val(),
            correo_electronico: $('#correo_electronico').val(),
            documento_identidad: $('#documento_identidad').val(),
            carnet: $('#carnet').val(),
            contrasena: $('#contrasena').val(),
            rol: $('#rol').val(),
            institucion: $('#institucion').val(),
            ciudad: $('#ciudad').val(),
            pais: $('#pais').val()
        };
        var endpoint = action === 'create' ? 'endpoint/createUser' : 'endpoint/updateUser';
        $.ajax({
            url: endpoint,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(user),
            success: function(response) {
                if (response.success) {
                    displayMessage(action === 'create' ? 'Usuario creado correctamente.' : 'Usuario actualizado correctamente.', 'success');
                    $('#user-popup').addClass('hidden');
                    loadUserTable();
                } else {
                    displayMessage(response.error || (action === 'create' ? 'Error al crear el usuario.' : 'Error al actualizar el usuario.'), 'error');
                }
            },
            error: function() {
                displayMessage(action === 'create' ? 'Error al crear el usuario.' : 'Error al actualizar el usuario.', 'error');
            }
        });
    }

    function filterAndSearchProjects() {
        selectedPhase = $('#project-phase-filter').val();
        searchQuery = $('#project-search').val().toLowerCase();
        loadProjectTable();
    }

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
        var matchesSearch = !searchQuery || project.nombre_proyecto.toLowerCase().includes(searchQuery);
        return matchesPhase && matchesSearch;
    }

    $('#project-phase-filter').change(filterAndSearchProjects);
    $('#project-search').on('input', filterAndSearchProjects);
    $('#clear-project-filter').click(function() {
        $('#project-phase-filter').val('');
        filterAndSearchProjects();
    });
    $('#clear-project-search').click(function() {
        $('#project-search').val('');
        filterAndSearchProjects();
    });

    function displayProjects(projects) {
        var tableBody = $('#project-table-body');
        tableBody.empty();
        projects.forEach(function(project) {
            var row = $('<tr></tr>');
            row.append(createCell(project.id));
            row.append(createCell(project.nombre_proyecto));
            row.append(createCell(project.nombre_corto));
            row.append(createCell(project.fase));
            row.append(createCell(project.tipo_proyecto));
            row.append(createCell(project.linea_investigacion));
            row.append(createCell(project.fecha_inicio));
            row.append(createCell(project.fecha_fin));
            var commandsTd = $('<td></td>').addClass('px-6 py-4 text-sm text-center text-gray-900');
            commandsTd.append(createButton('text-blue-500 edit-project', project.id, 'edit', '<i class="fas fa-edit"></i>'));
            commandsTd.append(createButton('text-red-500 delete-project', project.id, 'delete', '<i class="fas fa-trash"></i>'));
            row.append(commandsTd);
            tableBody.append(row);
        });

        $('.edit-project').click(function() {
            openEditPopup($(this).data('id'), 'project');
            $('#save-project-button').attr('id', 'save-edit-project-button');
        });

        $('.delete-project').click(function() {
            openDeletePopup($(this).data('id'), 'project');
        });
    }

    $('#save-new-project-button').click(function() {
        saveProject('create');
    });

    $('#save-edit-project-button').click(function() {
        saveProject('update');
    });

    function saveProject(action) {
        var project = {
            id: $('#project-id').val(),
            nombre_proyecto: $('#nombre_proyecto').val(),
            nombre_corto: $('#nombre_corto').val(),
            fase: $('#fase').val(),
            tipo_proyecto: $('#tipo_proyecto').val(),
            linea_investigacion: $('#linea_investigacion').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val()
        };
        var endpoint = action === 'create' ? 'endpoint/createProject' : 'endpoint/updateProject';
        $.ajax({
            url: endpoint,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(project),
            success: function(response) {
                if (response.success) {
                    displayMessage(action === 'create' ? 'Proyecto creado correctamente.' : 'Proyecto actualizado correctamente.', 'success');
                    $('#project-popup').addClass('hidden');
                    loadProjectTable();
                } else {
                    displayMessage(response.error || (action === 'create' ? 'Error al crear el proyecto.' : 'Error al actualizar el proyecto.'), 'error');
                }
            },
            error: function() {
                displayMessage(action === 'create' ? 'Error al crear el proyecto.' : 'Error al actualizar el proyecto.', 'error');
            }
        });
    }

    function displayMessage(message, type) {
        var messageDiv = $('<div></div>').addClass('fixed bottom-4 right-4 p-4 rounded shadow-lg max-w-xs z-50').hide();
        var bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        messageDiv.addClass(bgColor).text(message);
        $('body').append(messageDiv);
        messageDiv.fadeIn().delay(3000).fadeOut(function() {
            $(this).remove();
        });
    }

    function showError(section) {
        displayMessage(section === 'users' ? 'Error al cargar los usuarios.' : 'Error al cargar los proyectos.', 'error');
    }
});