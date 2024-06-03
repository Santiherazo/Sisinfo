$(document).ready(function() {
    fetchProjects();
    setInterval(fetchProjects, 20 * 1000);

    let currentProjectId = null;
    let timerStarted = false;
    let timerInterval;
    let timerDuration = 20 * 60;

    function fetchProjects() {
        $.get('includes/app.php?page=projects', function(data) {
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    return;
                }
            }

            if (!Array.isArray(data)) {
                console.error('Expected an array but got:', data);
                return;
            }

            const projects = data;
            const projectList = $('#projectList');
            projectList.empty();

            projects.forEach(project => {
                const listItem = $('<div>')
                    .addClass('p-4 bg-gray-100 rounded shadow project-list-item')
                    .on('click', () => loadProjectDetails(project));

                const phaseTag = $('<span>')
                    .addClass('bg-blue-500 text-white px-2 py-1 rounded')
                    .text(`Fase: ${project.fase}`);

                const title = $('<h3>')
                    .addClass('font-semibold mt-2')
                    .text(project.titulo);

                const line = $('<p>').text(`Linea: ${project.linea}`);
                const time = $('<p>').text(`Tiempo: ${project.timer} minutos`);

                listItem.append(phaseTag, title, line, time);
                projectList.append(listItem);
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching projects:', textStatus, errorThrown);
        });
    }

    function loadProjectDetails(project) {
        if (timerStarted) return;

        currentProjectId = project.id;
        $('#projectName').val(project.titulo);
        $('#projectLine').val(project.linea);
        $('#projectPhase').val(project.fase);
        $('#evaluatorName').val(project.evaluador);

        const participantsContainer = $('#projectParticipants');
        participantsContainer.empty();
        const investigadoresArray = project.investigadores.split(', ');
        investigadoresArray.forEach(participante => {
            participantsContainer.append(
                $('<input>')
                .attr('type', 'text')
                .addClass('mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50')
                .val(participante)
                .prop('readonly', true)
            );
        });

        const advisorsContainer = $('#projectAdvisors');
        advisorsContainer.empty();
        const docentesArray = project.docentes.split(', ');
        docentesArray.forEach(docente => {
            advisorsContainer.append(
                $('<input>')
                .attr('type', 'text')
                .addClass('mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50')
                .val(docente)
                .prop('readonly', true)
            );
        });

        timerDuration = project.timer * 60;
        $('#timer').text(formatTime(timerDuration));
    }

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes < 10 ? '0' : ''}${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    function calculateTotal() {
        let total = 0;
        $('.puntaje').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalPuntaje').text(total.toFixed(1));
    }

    function saveRubric() {
        const data = {
            proyecto_id: currentProjectId,
            assessor: $('#evaluatorName').val(),
            titleProject: $('[name="titleProject"]').val(),
            feedProject: $('#feedProject').val(),
            problemStatement: $('[name="problemStatement"]').val(),
            FeedStatement: $('#FeedStatement').val(),
            justify: $('[name="justify"]').val(),
            feedJustify: $('#feedJustify').val(),
            methodology: $('[name="methodology"]').val(),
            feedMethodology: $('#feedMethodology').val(),
            mainResults: $('[name="mainResults"]').val(),
            feedMainresults: $('#feedMainresults').val(),
            support: $('[name="support"]').val(),
            rating: $('#rating').val(),
            generalComments: $('#generalComments').val(),
        };

        console.log('Datos a enviar:', data);

        $.ajax({
            url: 'includes/app.php?page=send',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success) {
                        showSuccessPopup(); // Mostrar el popup de éxito
                        clearFormAndHideTable();
                        removeProjectFromList(currentProjectId);
                        $('#timer').val('00:00');
                        document.title = 'Evaluación de tesis'; // Cambia el título de vuelta
                    } else {
                        alert('Error al guardar la rúbrica: ' + res.error);
                    }
                } catch (e) {
                    console.error('Error al analizar la respuesta:', e);
                    alert('Error inesperado. Consulta la consola para más detalles.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', status, error);
                alert('Error en la solicitud. Consulta la consola para más detalles.');
            }
        });
    }

    function showSuccessPopup() {
        const popup = $('#successPopup');
        popup.removeClass('hidden');

        // Cerrar automáticamente el popup después de 10-20 segundos
        setTimeout(function() {
            popup.addClass('hidden');
        }, 10000); // Aquí se puede ajustar el tiempo en milisegundos (10000 ms = 10 segundos)
    }

    function startTimer(duration, display) {
        let timer = duration,
            minutes, seconds;
        timerInterval = setInterval(() => {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);
            document.title = `${minutes}:${seconds}`;

            if (--timer < 0) {
                clearInterval(timerInterval);
                alert("El tiempo ha finalizado");
            }
        }, 1000);
    }

    $('#startEvaluation').click(() => {
        if (currentProjectId) {
            if (!timerStarted) {
                timerStarted = true;
                startTimer(timerDuration, $('#timer'));
            }
            $('#evaluationTable').show();
            $('#startEvaluation').hide();
        } else {
            alert('Por favor, selecciona un proyecto antes de comenzar la evaluación.');
        }
    });

    $('#evaluationTable input').on('input', calculateTotal);

    $('#saveRubric').click(saveRubric);

    function clearFormAndHideTable() {
        $('#evaluatorName').val('');
        $('[name="titulo_proyecto"]').val('');
        $('[name="planteamiento_problema"]').val('');
        $('[name="justificacion"]').val('');
        $('[name="objetivos"]').val('');
        $('[name="metodologia"]').val('');
        $('[name="resultados_iniciales"]').val('');
        $('[name="sustentacion"]').val('');
        $('#comentarios').val('');
        $('#totalPuntaje').text('0');
        $('#rubricTable').hide();
    }

    function removeProjectFromList(projectId) {
        fetchProjects();
        clearFormAndHideTable();
    }

    $('#logoutButton').click(function() {
        logout();
    });

    function logout() {
        $.get('index.php?page=logout', function(data) {
            location.reload();
        });
    }

    // Cerrar el popup cuando se haga clic en el botón de cierre
    $('#closePopupBtn').click(function() {
        $('#successPopup').addClass('hidden');
    });
});