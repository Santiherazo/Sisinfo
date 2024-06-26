document.querySelector('button').addEventListener('click', () => {
    const dropdown = document.querySelector('div.absolute');
    dropdown.classList.toggle('hidden');
});

$(document).ready(function() {
    let currentProjectId = null;
    let timerStarted = false;
    let timerInterval;
    let timerDuration = 20 * 60;

    function fetchProjects() {
        const searchQuery = $('#searchInput').val().toLowerCase();
        const selectedPhase = $('.filter-button.active').data('phase');

        $.get('endpoint/projects', function(data) {
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

            const filteredProjects = data.filter(project => {
                const titleMatches = project.titulo.toLowerCase().includes(searchQuery);
                const phaseMatches = selectedPhase ? project.fase === selectedPhase : true;
                return titleMatches && phaseMatches;
            });

            displayProjects(filteredProjects);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching projects:', textStatus, errorThrown);
        });
    }

    function displayProjects(projects) {
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
    }

    fetchProjects();
    setInterval(fetchProjects, 20 * 1000);
    $('#searchInput').on('input', fetchProjects);

    $('.filter-button').click(function() {
        $('.filter-button').removeClass('active');
        $(this).addClass('active');
        fetchProjects();
    });

    function loadProjectDetails(project) {
        if (timerStarted) return;

        currentProjectId = project.id;
        $('#projectName').val(project.titulo);
        $('#projectLine').val(project.linea);
        $('#projectPhase').val(project.fase);

        const participantsContainer = $('#projectParticipants');
        participantsContainer.empty();
        project.investigadores.forEach(participante => {
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
        project.docentes.split(', ').forEach(docente => {
            advisorsContainer.append(
                $('<input>')
                    .attr('type', 'text')
                    .addClass('mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50')
                    .val(docente)
                    .prop('readonly', true)
            );
        });

        const evaluatorsContainer = $('#projectEvaluators');
        evaluatorsContainer.empty();
        project.evaluadores.forEach(evaluador => {
            evaluatorsContainer.append(
                $('<input>')
                    .attr('type', 'text')
                    .addClass('mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50')
                    .val(evaluador)
                    .prop('readonly', true)
            );
        });

        timerDuration = project.timer * 60;
        $('#timer').text(formatTime(timerDuration));
    }

    function clearProjectDetails() {
        $('#projectName').val('');
        $('#projectLine').val('');
        $('#projectPhase').val('');
        $('#projectParticipants').empty();
        $('#projectAdvisors').empty();
        $('#projectEvaluators').empty();
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
        $('#rating').text((total / 9).toFixed(1));
    }

    function saveRubric() {
        const data = {
            proyecto_id: currentProjectId,
            titleProject: $('[name="titleProject"]').val(),
            feedProject: $('#feedProject').val(),
            introduction: $('[name="introduction"]').val(),
            feedIntroduction: $('#feedIntroduction').val(),
            problemStatement: $('[name="problemStatement"]').val(),
            feedStatement: $('#FeedStatement').val(),
            justify: $('[name="justify"]').val(),
            feedJustify: $('#feedJustify').val(),
            targets: $('[name="targets"]').val(),
            feedTargets: $('#feedTargets').val(),
            theorical: $('[name="theorical"]').val(),
            feedTheorical: $('#feedTheorical').val(),
            methodology: $('[name="methodology"]').val(),
            feedMethodology: $('#feedMethodology').val(),
            mainResults: $('[name="mainResults"]').val(),
            feedMainresults: $('#feedMainresults').val(),
            support: $('[name="support"]').val(),
            feedSupport: $('#feedSupport').val(),
            rating: $('#rating').text(),
            generalComments: $('#generalComments').val()
        };

        console.log('Rubric data:', data);

        $.ajax({
            url: 'endpoint/rubric',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                console.log('Response:', response);
                try {
                    const res = JSON.parse(response);
                    if (res.success) {
                        clearFormAndHideTable();
                        removeProjectFromList(currentProjectId);
                        $('#timer').text('00:00');
                        document.title = 'Evaluación de tesis';
                        showSuccessPopup();
                        clearInterval(timerInterval);
                        timerStarted = false;
                        $('#startEvaluation').show();
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

        setTimeout(function() {
            popup.addClass('hidden');
        }, 10000);
    }

    function startTimer(duration, display) {
        let timer = duration;
        timerInterval = setInterval(() => {
            let minutes = parseInt(timer / 60, 10);
            let seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);
            document.title = `${minutes}:${seconds}`;

            if (timer === 300) {
                alert("Quedan 5 minutos");
            }

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
        $('[name="titleProject"]').val('');
        $('#feedProject').val('');
        $('[name="introduction"]').val('');
        $('#feedIntroduction').val('');
        $('[name="problemStatement"]').val('');
        $('#FeedStatement').val('');
        $('[name="justify"]').val('');
        $('#feedJustify').val('');
        $('[name="targets"]').val('');
        $('#feedTargets').val('');
        $('[name="theorical"]').val('');
        $('#feedTheorical').val('');
        $('[name="methodology"]').val('');
        $('#feedMethodology').val('');
        $('[name="mainResults"]').val('');
        $('#feedMainresults').val('');
        $('[name="support"]').val('');
        $('#feedSupport').val('');
        $('#rating').text('');
        $('#generalComments').val('');
        setTimeout(() => {
            $('#evaluationTable').hide();
        }, 5000);
    }

    function removeProjectFromList(projectId) {
        fetchProjects();
        clearProjectDetails();
    }

    $('#logoutButton').click(function() {
        logout();
    });

    function logout() {
        $.get('/logout', function(data) {
            location.reload();
        });
    }

    $('#closePopupBtn').click(function() {
        $('#successPopup').addClass('hidden');
    });

    $('#cancelButton').click(function() {
        $('#evaluationTable').hide();
        $('#startEvaluation').show();
        clearFormAndHideTable();
    });
});