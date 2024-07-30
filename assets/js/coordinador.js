$(document).ready(function() {
    $('#users-section').show();
    loadUserTable();
    $('#users-tab').addClass('text-blue-400 border-blue-400 transition duration-300');

    $('#add-user-button').click(function() {
        $('#user-popup-title').text('Crear Usuario');
        $('#user-form')[0].reset();
        $('#user-id').val('');
        $('#pais').val('Colombia');
        $('#institucion').val('Unipaz');
        $('#ciudad').val('Barrancabermeja');
        $('#carnet').val('1');
        $('#save-user-button').attr('id', 'save-new-user-button');
        $('#user-popup').removeClass('hidden');
    });

    $('#add-project-button').click(function() {
        $('#project-popup-title').text('Crear Proyecto');
        $('#project-form')[0].reset();
        $('#project-id').val('');
        $('#project-popup').removeClass('hidden');
        clearSelections();
    });

    $('#cancel-user-button').click(function() {
        $(this).closest('#user-popup').addClass('hidden');
        clearSelections();
    });

    function clearSelections() {
        selectedStudents = [];
        selectedEvaluators = [];
        updateProjectPopup();
    }

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
            var endpoint = type === 'user' ? 'endpoint/deleteUser' : 'endpoint/deleteProjects';
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
                $('#save-new-user-button').attr('id', 'save-edit-user-button');
                $('#user-popup').removeClass('hidden');
            }
        } else {
            console.error(`No se encontró ${type} con id:`, id);
        }
    }

    $(document).on('click', '#save-new-user-button', function() {
        var userData = {
            nombre_completo: $('#nombre_completo').val(),
            correo_electronico: $('#correo_electronico').val(),
            documento_identidad: $('#documento_identidad').val(),
            carnet: $('#carnet').val(),
            contrasena: $('#contrasena').val(),
            rol: $('#rol').val(),
            institucion: $('#institucion').val(),
            direccion: $('#direccion').val(),
            ciudad: $('#ciudad').val(),
            estado_provincia: $('#estado_provincia').val(),
            pais: $('#pais').val()
        };

        var endpoint = 'endpoint/addUser';

        console.log(userData);

        $.ajax({
            url: endpoint,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function(response) {
                if (response.success) {
                    displayMessage('Usuario creado correctamente.', 'success');
                    loadUserTable();
                    $('#user-popup').addClass('hidden');
                } else {
                    displayMessage(response.error || 'Error al crear el usuario.', 'error');
                }
            },
            error: function() {
                displayMessage('Error al crear el usuario.', 'error');
            }
        });
    });

    $(document).on('click', '#save-edit-user-button', function() {
        var userData = {
            id: $('#user-id').val(),
            nombre_completo: $('#nombre_completo').val(),
            correo_electronico: $('#correo_electronico').val(),
            documento_identidad: $('#documento_identidad').val(),
            carnet: $('#carnet').val(),
            contrasena: $('#contrasena').val(),
            rol: $('#rol').val(),
            institucion: $('#institucion').val(),
            direccion: $('#direccion').val(),
            ciudad: $('#ciudad').val(),
            estado_provincia: $('#estado_provincia').val(),
            pais: $('#pais').val()
        };
    
        if (userData.contrasena === '') {
            delete userData.contrasena;
        }
    
        var endpoint = 'endpoint/editUser';
    
        $.ajax({
            url: endpoint,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function(response) {
                if (response.success) {
                    displayMessage('Usuario actualizado correctamente.', 'success');
                    loadUserTable();
                    $('#user-popup').addClass('hidden');
                } else {
                    displayMessage(response.error || 'Error al actualizar el usuario.', 'error');
                }
            },
            error: function() {
                displayMessage('Error al actualizar el usuario.', 'error');
            }
        });
    });    

    function filterAndSearchProjects() {
        selectedPhase = $('#project-phase-filter').val();
        searchQuery = $('#project-search').val().toLowerCase();
        loadProjectTable();
    }

    function loadProjectTable() {
        $.ajax({
            url: 'endpoint/adminProjects',
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
        var matchesSearch = !searchQuery || project.titulo.toLowerCase().includes(searchQuery);
        return matchesPhase && matchesSearch;
    }

    $('#project-phase-filter').change(filterAndSearchProjects);
    $('#project-search').on('input', filterAndSearchProjects);
    $('#clear-phase-filter').click(function() {
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
            row.append(createCell(project.titulo));
            row.append(createCell(project.estudiantes));
            row.append(createCell(project.docentes));
            row.append(createCell(project.evaluadores));
            row.append(createCell(project.linea));
            row.append(createCell(project.fase));
            row.append(createCell(project.timer));
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

    function displayMessage(message, type) {
        var alertBox = $('#alert-box');
        alertBox.removeClass('hidden').text(message);
        if (type === 'success') {
            alertBox.addClass('bg-green-200 text-green-800').removeClass('bg-red-200 text-red-800');
        } else if (type === 'error') {
            alertBox.addClass('bg-red-200 text-red-800').removeClass('bg-green-200 text-green-800');
        }
        setTimeout(function() {
            alertBox.addClass('hidden');
        }, 3000);
    }

   var projectsData = [];
var selectedStudents = [];
var selectedEvaluators = [];

const createPopupHtml = (title, listId) => 
    `<div id="${listId}-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h2 class="text-xl font-semibold">${title}</h2>
                <button class="close-popup text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div class="mb-4">
                <input type="text" class="search-input w-full p-2 border rounded" placeholder="Buscar...">
            </div>
            <ul id="${listId}" class="max-h-60 overflow-y-auto"></ul>
            <button class="add-selected mt-4 w-full bg-blue-500 text-white py-2 rounded" id="add-selected-${listId}">Agregar</button>
        </div>
    </div>`;

$('body').append(createPopupHtml('Agregar Estudiantes', 'student-list'));
$('body').append(createPopupHtml('Agregar Evaluadores', 'evaluator-list'));

$('#add-student-button').click(function() {
    displayUsersByRole('student-list', 'Estudiante');
    $('#student-list-popup').removeClass('hidden');
});

$('#add-evaluator-button').click(function() {
    displayUsersByRole('evaluator-list', 'Evaluador');
    $('#evaluator-list-popup').removeClass('hidden');
});

$(document).on('click', '.close-popup', function() {
    $(this).closest('.fixed').addClass('hidden');
});

function displayUsersByRole(listId, role) {
    const users = usersData.filter(user => user.rol === role && !isUserSelected(user.id));
    const list = $(`#${listId}`);
    list.empty();
    users.forEach(function(user) {
        const item = $(`<li data-id="${user.id}" class="p-2 border-b cursor-pointer hover:bg-gray-100">${user.nombre_completo}</li>`);
        item.click(function() {
            $(this).toggleClass('bg-gray-200');
            $(this).data('selected', !$(this).data('selected'));
        });
        list.append(item);
    });
}

function isUserSelected(userId) {
    return selectedStudents.includes(userId) || selectedEvaluators.includes(userId);
}

$(document).on('input', '.search-input', function() {
    const searchTerm = $(this).val().toLowerCase();
    const listId = $(this).closest('.bg-white').find('ul').attr('id');
    const allItems = $(`#${listId} li`);

    allItems.each(function() {
        const itemText = $(this).text().toLowerCase();
        $(this).toggle(itemText.includes(searchTerm));
    });
});

$(document).on('click', '#add-selected-student-list', function() {
    $('#student-list li').each(function() {
        if ($(this).data('selected')) {
            const userId = $(this).data('id');
            if (!selectedStudents.includes(userId)) {
                selectedStudents.push(userId);
            }
        }
    });
    updateProjectPopup();
    $('#student-list-popup').addClass('hidden');
});

$(document).on('click', '#add-selected-evaluator-list', function() {
    $('#evaluator-list li').each(function() {
        if ($(this).data('selected')) {
            const userId = $(this).data('id');
            if (!selectedEvaluators.includes(userId)) {
                selectedEvaluators.push(userId);
            }
        }
    });
    updateProjectPopup();
    $('#evaluator-list-popup').addClass('hidden');
});

function updateProjectPopup() {
    const studentListContainer = $('#students-list');
    const evaluatorListContainer = $('#evaluators-list');

    studentListContainer.empty();
    evaluatorListContainer.empty();

    selectedStudents.forEach(id => {
        const user = getUserById(id);
        if (user) {
            const userDiv = $(`<div class="selected-user" data-id="${user.id}">${user.nombre_completo} <button class="remove-user" data-id="${user.id}"><i class="fas fa-times text-gray-400 cursor-pointer hover:text-gray-600"></i></button></div>`);
            studentListContainer.append(userDiv);
        } else {
            console.warn(`Usuario con ID ${id} no encontrado`);
        }
    });

    selectedEvaluators.forEach(id => {
        const user = getUserById(id);
        if (user) {
            const userDiv = $(`<div class="selected-user" data-id="${user.id}">${user.nombre_completo} <button class="remove-user" data-id="${user.id}"><i class="fas fa-times text-gray-400 cursor-pointer hover:text-gray-600"></i></button></div>`);
            evaluatorListContainer.append(userDiv);
        } else {
            console.warn(`Evaluador con ID ${id} no encontrado`);
        }
    });

    $('.remove-user').off('click').on('click', function() {
        const userId = $(this).data('id');
        $(this).parent().remove();

        selectedStudents = selectedStudents.filter(id => id !== userId);
        selectedEvaluators = selectedEvaluators.filter(id => id !== userId);
    });
}

function getUserById(userId) {
    return usersData.find(user => user.id === userId);
}

function getUserByName(userName) {
    return usersData.find(user => user.nombre_completo === userName);
}

function showError(entity) {
    console.error(`Error al cargar los ${entity}. Por favor, inténtelo de nuevo más tarde.`);
}

$('#add-project-button').click(function() {
    $('#project-popup-title').text('Crear Proyecto');
    $('#project-form')[0].reset();
    $('#project-id').val('');
    selectedStudents = [];
    selectedEvaluators = [];
    updateProjectPopup();
    $('#project-popup').removeClass('hidden');

    $('#save-project-button').off('click').on('click', createProject);
});

function createProject() {
    const projectData = {
        titulo: $('#titulo').val(),
        docentes: $('#docentes').val(),
        linea: $('#linea').val(),
        fase: $('#fase').val(),
        timer: $('#duracion').val(),
        estudiantes: selectedStudents.join(','),
        evaluadores: selectedEvaluators.join(',')
    };

    $.ajax({
        url: 'endpoint/addProjects',
        type: 'POST',
        data: JSON.stringify(projectData),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                displayMessage('Proyecto guardado correctamente.', 'success');
                loadProjectTable();
            } else {
                displayMessage('Error al guardar el proyecto.', 'error');
            }
        },
        error: function() {
            showError('project');
        }
    });

    $('#project-popup').addClass('hidden');
    selectedStudents = [];
    selectedEvaluators = [];
    updateProjectPopup();
}

$(document).on('click', '.edit-project', function() {
    const projectId = $(this).data('id');
    const project = projectsData.find(p => p.id === projectId);

    if (project) {
        $('#project-id').val(project.id);
        $('#titulo').val(project.titulo);
        $('#docentes').val(project.docentes);
        $('#linea').val(project.linea);
        $('#fase').val(project.fase);
        $('#duracion').val(project.timer);

        if (typeof project.estudiantes === 'string') {
            selectedStudents = project.estudiantes.split(',').map(name => getUserByName(name).id);
        } else {
            selectedStudents = Array.isArray(project.estudiantes) ? project.estudiantes.map(name => getUserByName(name).id) : [];
        }

        if (typeof project.evaluadores === 'string') {
            selectedEvaluators = project.evaluadores.split(',').map(name => getUserByName(name).id);
        } else {
            selectedEvaluators = Array.isArray(project.evaluadores) ? project.evaluadores.map(name => getUserByName(name).id) : [];
        }

        updateProjectPopup();
        $('#project-popup').removeClass('hidden');

        $('#save-project-button').off('click').on('click', editProject);
    } else {
        console.error('Proyecto no encontrado con id:', projectId);
        showError('project');
    }
});

function editProject() {
    const projectId = $('#project-id').val();
    const projectData = {
        id: projectId,
        titulo: $('#titulo').val(),
        docentes: $('#docentes').val(),
        linea: $('#linea').val(),
        fase: $('#fase').val(),
        timer: $('#duracion').val(),
        investigadores: selectedStudents.join(','),
        evaluador: selectedEvaluators.join(',')
    };

    console.log(projectData);

    $.ajax({
        url: 'endpoint/editProjects',
        type: 'POST',
        data: JSON.stringify(projectData),
        contentType: 'application/json',
        success: function(response) {
            console.log(response);
            if (response.success) {
                displayMessage('Proyecto editado correctamente.', 'success');
                loadProjectTable();
            } else {
                displayMessage('Error al editar el proyecto.', 'error');
            }
        },
        error: function() {
            showError('project');
        }
    });

    $('#project-popup').addClass('hidden');
    selectedStudents = [];
    selectedEvaluators = [];
    updateProjectPopup();
}

$('#cancel-project-button').click(function() {
    $('#project-popup').addClass('hidden');
    selectedStudents = [];
    selectedEvaluators = [];
    updateProjectPopup();
});

$('#cancel-project-button').click(function() {
    $('#project-popup').addClass('hidden');
    selectedStudents = [];
    selectedEvaluators = [];
    updateProjectPopup();
});

function renderProject(project, container) {
    const projectContainer = $('<div class="project mb-10"></div>');

    const title = $('<h1 class="text-2xl font-bold mb-4"></h1>').text(project.titulo || 'Título no encontrado');
    const average = $('<p><strong>Calificación General:</strong> </p>').append($('<span></span>').text(project.calificacion_general.toFixed(2)));
    const progressText = $('<p><strong>Progreso:</strong> </p>').append($('<span></span>').text(`${project.progress}%`));
    const progressBar = $('<div class="progress w-full bg-gray-300 h-6 rounded"></div>').append(
        $('<div class="progress-bar h-full rounded"></div>').css('width', `${project.progress}%`).addClass(project.calificacion_general >= 3 ? 'bg-green-500' : 'bg-red-500')
    );

    const feedback = $('<div id="feedback" class="my-4"></div>').html(
        project.calificaciones.map(criterio => `<p>${criterio.generalComments || "No hubo comentario adicional"}</p>`).join("")
    );

    const estudiantes = project.investigadores ? project.investigadores.join(', ') : 'N/A';
    const docentes = project.docentes ? project.docentes : 'N/A';
    const evaluadoresHtml = project.evaluadores.length > 0 ? project.evaluadores.join(', ') : 'N/A';
    const projectInfoHtml = `
        <li><strong>Título:</strong> ${project.titulo}</li>
        <li><strong>Estudiantes:</strong> ${estudiantes}</li>
        <li><strong>Fase:</strong> ${project.fase}</li>
        <li><strong>Línea:</strong> ${project.linea}</li>
        <li><strong>Docentes:</strong> ${docentes}</li>
        <li><strong>Evaluadores:</strong> ${evaluadoresHtml}</li>
    `;
    const projectInfo = $('<ul class="list-disc pl-5 mb-4"></ul>').html(projectInfoHtml);

    let criteriaHtml = '';
    const properties = [
        { name: "titleProject", label: "Título del Proyecto", feed: "feedProject" },
        { name: "introduction", label: "Introducción", feed: "feedIntroduction" },
        { name: "problemStatement", label: "Planteamiento del Problema", feed: "FeedStatement" },
        { name: "justify", label: "Justificación", feed: "feedJustify" },
        { name: "targets", label: "Objetivos", feed: "feedTargets" },
        { name: "theorical", label: "Marco Teórico", feed: "feedTheorical" },
        { name: "methodology", label: "Metodología", feed: "feedMethodology" },
        { name: "mainResults", label: "Resultados", feed: "feedMainresults" },
        { name: "support", label: "Sustentación", feed: "feedSupport" }
    ];

    project.evaluadores.forEach((evaluador, index) => {
        let calificacion = project.calificaciones[index];
        criteriaHtml += `
        <div class="mb-6">
            <h4 class="text-xl font-semibold mb-2">${evaluador}</h4>
            <table class="table-auto w-full bg-gray-50 shadow rounded mb-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Criterios</th>
                        <th class="px-4 py-2">Comentarios</th>
                        <th class="px-4 py-2">Resultado</th>
                    </tr>
                </thead>
                <tbody>`;
        properties.forEach(property => {
            let commentKey = property.feed;
            let comment = calificacion[commentKey] || "No hay comentarios";
            let value = calificacion[property.name] || "No hay comentarios";
            criteriaHtml += `
                    <tr>
                        <td class="border px-4 py-2 font-semibold">${property.label}</td>
                        <td class="border px-4 py-2">${comment}</td>
                        <td class="border px-4 py-2">${value}</td>
                    </tr>`;
        });
        let generalComment = calificacion.generalComments || "No hay comentarios";
        let finalResult = calificacion.rating || "No hay una calificación";
        criteriaHtml += `
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Resultado Final</td>
                        <td class="border px-4 py-2"></td>
                        <td class="border px-4 py-2">${finalResult}</td>
                    </tr>
                </tbody>
            </table>
        </div>`;
    });

    const evaluatedCriteria = $('<div id="evaluatedCriteria"></div>').html(criteriaHtml);

    projectContainer.append(title, average, progressText, progressBar, feedback, projectInfo, evaluatedCriteria);
    container.append(projectContainer);
}

function generateReport() {
    $.ajax({
        url: 'endpoint/report',
        type: 'GET',
        success: function(response) {
            let projects = JSON.parse(response);
            if (Array.isArray(projects) && projects.length > 0) {
                const container = $('#contentToExport');
                container.empty();
                projects.forEach(project => {
                    let calificaciones = project.calificaciones || [];
                    let calificacion_general = calificaciones.length > 0 
                        ? (calificaciones.reduce((acc, curr) => acc + parseFloat(curr.rating), 0) / calificaciones.length)
                        : 0;
                    project.calificacion_general = calificacion_general;
                    project.progress = (calificacion_general / 5 * 100).toFixed(2);

                    renderProject(project, container);
                });
            } else {
                $('#contentToExport').html('<p>No hay información que cargar</p>');
            }
        },
        error: function(error) {
            console.error('Error fetching results:', error);
        }
    });
}
})