<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rúbrica de Evaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }

        .header {
            background-color: #1f2937;
            color: #ffffff;
        }

        .sidebar {
            background-color: #374151;
            color: #ffffff;
        }

        .sidebar a {
            color: #ffffff;
        }

        .table-header {
            background-color: #4b5563;
            color: #ffffff;
        }

        .table-row:nth-child(even) {
            background-color: #f3f4f6;
        }

        .table-row:nth-child(odd) {
            background-color: #ffffff;
        }

        .input-field {
            background-color: #e5e7eb;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
        }

        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .btn-primary {
            background-color: #1f2937;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #4b5563;
        }

        .total-row {
            background-color: #d1fae5;
        }

        .timer-container {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: red;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            fetchProjects();
        });

        let currentProjectId = null;
        let timerStarted = false;
        let timerInterval;
        let timerDuration = 20 * 60;

        function fetchProjects() {
            $.get('get_projects.php', function (data) {
                const projects = data;
                const projectList = $('#projectList');
                projectList.empty();
                projects.forEach(project => {
                    const listItem = $('<a>')
                        .attr('href', '#')
                        .addClass('block p-4 hover:bg-gray-600')
                        .text(project.titulo)
                        .on('click', () => loadProjectDetails(project));
                    listItem.data('project', project);
                    projectList.append(listItem);
                });
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
                        .addClass('input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50')
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
                        .addClass('input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50')
                        .val(docente)
                        .prop('readonly', true)
                );
            });
        }

        function calculateTotal() {
            let total = 0;
            $('.puntaje').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#totalPuntaje').text(total.toFixed(1));
        }

        function saveRubric() {
            const data = {
                proyecto_id: currentProjectId,
                evaluador: $('#evaluatorName').val(),
                titulo_proyecto: $('[name="titulo_proyecto"]').val(),
                planteamiento_problema: $('[name="planteamiento_problema"]').val(),
                justificacion: $('[name="justificacion"]').val(),
                objetivos: $('[name="objetivos"]').val(),
                metodologia: $('[name="metodologia"]').val(),
                resultados_iniciales: $('[name="resultados_iniciales"]').val(),
                sustentacion: $('[name="sustentacion"]').val(),
                total: $('#totalPuntaje').text()
            };

            console.log('Datos a enviar:', data);

            $.ajax({
                url: 'save_rubric.php',
                type: 'POST',
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.success) {
                        alert('Rúbrica guardada exitosamente');
                        clearFormAndHideTable();
                        removeProjectFromList(currentProjectId);
                    } else {
                        alert('Error al guardar la rúbrica: ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error en la solicitud:', status, error);
                    alert('Error en la solicitud. Consulta la consola para más detalles.');
                }
            });
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

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    sendRubric();
                }
            }, 1000);
        }

        function startEvaluation() {
            const allFieldsFilled = areAllFieldsFilled();
            if (!timerStarted && allFieldsFilled) {
                timerStarted = true;
                $('#evaluationTable').show();
                $('#startButton').hide();
                $('#sendButton').show();
                startTimer(timerDuration, $('#timer'));

                $('#projectName').prop('readonly', true);
                $('#projectLine').prop('readonly', true);
                $('#projectPhase').prop('readonly', true);
                $('#evaluatorName').prop('readonly', true);
            }
        }

        function areAllFieldsFilled() {
            const fieldIds = ['evaluatorName', 'projectLine', 'projectPhase'];
            for (const id of fieldIds) {
                const field = $('#' + id);
                if (!field.val().trim()) {
                    alert('Por favor, complete todos los campos antes de comenzar la evaluación.');
                    return false;
                }
            }
            return true;
        }

        function sendRubric() {
            clearInterval(timerInterval);
            saveRubric();
        }

        function clearFormAndHideTable() {
            $('#rubricForm')[0].reset();
            $('#evaluationTable').hide();
            $('#startButton').show();
            $('#sendButton').hide();
            $('#totalPuntaje').text('0.0');
            timerStarted = false;

            $('#projectName').prop('readonly', false);
            $('#projectLine').prop('readonly', false);
            $('#projectPhase').prop('readonly', false);
            $('#evaluatorName').prop('readonly', false);
        }

        function removeProjectFromList(projectId) {
            $('#projectList a').each(function () {
                const project = $(this).data('project');
                if (project.id === projectId) {
                    $(this).remove();
                }
            });
        }
    </script>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <header class="header p-4">
            <h1 class="text-2xl">Rúbrica de Evaluación</h1>
        </header>
        <div class="flex flex-1">
            <aside class="sidebar w-64 p-4">
                <h2 class="text-lg font-medium mb-4">Proyectos</h2>
                <div id="projectList" class="space-y-2"></div>
            </aside>
            <main class="flex-1 p-8">
                <form id="rubricForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="projectName" class="block text-sm font-medium text-gray-700">Nombre del Proyecto</label>
                            <input type="text" id="projectName" class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="projectLine" class="block text-sm font-medium text-gray-700">Línea del Proyecto</label>
                            <input type="text" id="projectLine" class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="projectPhase" class="block text-sm font-medium text-gray-700">Fase del Proyecto</label>
                            <input type="text" id="projectPhase" class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="evaluatorName" class="block text-sm font-medium text-gray-700">Nombre del Evaluador</label>
                            <input type="text" id="evaluatorName" class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-700">Participantes</h3>
                        <div id="projectParticipants" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-700">Docentes</h3>
                        <div id="projectAdvisors" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="startButton" class="btn-primary w-full" onclick="startEvaluation()">Comenzar Evaluación</button>
                    </div>
                </form>

                <div id="evaluationTable" class="mt-8 hidden">
                    <h3 class="text-lg font-medium text-gray-700">Evaluación</h3>
                    <table class="min-w-full table-auto">
                        <thead class="table-header">
                            <tr>
                                <th class="px-4 py-2">Criterio</th>
                                <th class="px-4 py-2">Descripción</th>
                                <th class="px-4 py-2">Puntaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Título del Proyecto</td>
                                <td class="border px-4 py-2">Claridad y coherencia</td>
                                <td class="border px-4 py-2"><input type="number" name="titulo_proyecto" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Planteamiento del Problema</td>
                                <td class="border px-4 py-2">Descripción detallada</td>
                                <td class="border px-4 py-2"><input type="number" name="planteamiento_problema" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Justificación</td>
                                <td class="border px-4 py-2">Importancia y relevancia</td>
                                <td class="border px-4 py-2"><input type="number" name="justificacion" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Objetivos</td>
                                <td class="border px-4 py-2">Claridad y alcanzabilidad</td>
                                <td class="border px-4 py-2"><input type="number" name="objetivos" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Metodología</td>
                                <td class="border px-4 py-2">Apropiada y bien detallada</td>
                                <td class="border px-4 py-2"><input type="number" name="metodologia" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Resultados Iniciales</td>
                                <td class="border px-4 py-2">Claros y significativos</td>
                                <td class="border px-4 py-2"><input type="number" name="resultados_iniciales" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                            <tr class="table-row">
                                <td class="border px-4 py-2">Sustentación</td>
                                <td class="border px-4 py-2">Clara y bien argumentada</td>
                                <td class="border px-4 py-2"><input type="number" name="sustentacion" class="puntaje input-field" oninput="calculateTotal()" step="0.1"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td class="border px-4 py-2 font-bold" colspan="2">Total</td>
                                <td class="border px-4 py-2 font-bold" id="totalPuntaje">0.0</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mt-4">
                        <button type="button" id="sendButton" class="btn-primary w-full" onclick="sendRubric()">Enviar Evaluación</button>
                    </div>
                </div>
            </main>
        </div>
        <div class="timer-container">
            <div class="timer" id="timer">20:00</div>
        </div>
    </div>
</body>
</html>
