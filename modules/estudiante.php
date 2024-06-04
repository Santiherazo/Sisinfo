<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados Académicos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md" id="resultsContainer">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Resultados Académicos</h2>
            <div class="flex space-x-4">
                <button id="downloadPdf" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V5m0 6l3-3m-3 3l-3-3m6 8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Descargar Reporte
                </button>
                <button id="logoutButton" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6-4v16"></path>
                    </svg>
                    Cerrar Sesión
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 p-4 rounded shadow">
                <h3 class="text-lg font-semibold">Promedio General</h3>
                <p class="text-4xl font-bold" id="average"></p>
                <h3 class="text-lg font-semibold mt-4">Progreso</h3>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                        <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 progress" id="progressBar"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700" id="progress"></span>
                </div>
                <h3 class="text-lg font-semibold mt-4">Retroalimentación</h3>
                <p id="feedback"></p>
            </div>
            <div class="bg-gray-50 p-4 rounded shadow">
                <h3 class="text-lg font-semibold">Información del Proyecto</h3>
                <ul id="projectInfo">
                    <!-- Se actualizará dinámicamente -->
                </ul>
            </div>
        </div>
        <h3 class="text-lg font-semibold mb-4">Criterios Evaluados</h3>
        <div class="space-y-4" id="evaluatedCriteria">
            <!-- Se actualizará dinámicamente -->
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        function getResults() {
            $.ajax({
                url: 'includes/app.php?page=results',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    try {
                        let parsedResponse = JSON.parse(response);
                        console.log('Parsed Response:', parsedResponse);

                        let project = parsedResponse["11"];
                        let totalScore = parseFloat(project.calificacion_general);
                        
                        let projectInfoHtml = `
                            <li><strong>Título:</strong> ${project.titulo}</li>
                            <li><strong>Descripción:</strong> ${project.descripcion}</li>
                            <li><strong>Estudiantes:</strong> ${project.estudiantes.join(', ')}</li>
                            <li><strong>Fase:</strong> ${project.Fase}</li>
                            <li><strong>Línea:</strong> ${project.Linea}</li>
                            <li><strong>Docente:</strong> ${project.docente}</li>
                            <li><strong>Evaluador:</strong> ${project.evaluador}</li>
                        `;
                        $('#projectInfo').html(projectInfoHtml);

                        $('#average').text(totalScore.toFixed(2));
                        $('#progress').text(`${(totalScore / 50 * 100).toFixed(2)}%`);
                        $('#progressBar').css('width', `${(totalScore / 50 * 100).toFixed(2)}%`);

                        $('#feedback').text(project.comentarioGeneral);

                        let criteriaHtml = '';
                        Object.keys(project.resultados).forEach(key => {
                            let criterio = project.resultados[key];
                            criteriaHtml += `
                                <div class="bg-gray-50 p-4 rounded shadow">
                                    <h4 class="text-md font-semibold">${key}</h4>
                                    <p>${criterio.retroalimentación}</p>
                                    <span class="text-xl font-bold">${criterio.valor}</span>
                                </div>
                            `;
                        });
                        $('#evaluatedCriteria').html(criteriaHtml);
                    } catch (e) {
                        console.error('Error parsing or processing response:', e);
                        $('#evaluatedCriteria').html('<p>Ocurrió un error al procesar los datos.</p>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching data:', textStatus, errorThrown);
                    $('#evaluatedCriteria').html('<p>Ocurrió un error al obtener los datos.</p>');
                }
            });
        }

        getResults();
        setInterval(getResults, 20 * 1000);

        $('#downloadPdf').click(function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');

            let yOffset = 10;

            doc.setFontSize(18);
            doc.text("Resultados Académicos", 10, yOffset);
            yOffset += 10;

            doc.setFontSize(14);
            doc.text("Promedio General:", 10, yOffset);
            doc.setFontSize(12);
            doc.text($('#average').text(), 60, yOffset);
            yOffset += 10;

            doc.setFontSize(14);
            doc.text("Progreso:", 10, yOffset);
            doc.setFontSize(12);
            doc.text($('#progress').text(), 60, yOffset);
            yOffset += 10;

            doc.setFontSize(14);
            doc.text("Retroalimentación:", 10, yOffset);
            doc.setFontSize(12);
            doc.text($('#feedback').text(), 60, yOffset);
            yOffset += 20;

            doc.setFontSize(14);
            doc.text("Información del Proyecto", 10, yOffset);
            yOffset += 10;
            $('#projectInfo li').each(function() {
                doc.setFontSize(12);
                doc.text($(this).text(), 10, yOffset);
                yOffset += 10;
            });

            yOffset += 10;
            doc.setFontSize(14);
            doc.text("Criterios Evaluados", 10, yOffset);
            yOffset += 10;

            $('#evaluatedCriteria > div').each(function() {
                let title = $(this).find('h4').text();
                let feedback = $(this).find('p').text();
                let score = $(this).find('span').text();

                doc.setFontSize(12);
                doc.text(`Título: ${title}`, 10, yOffset);
                yOffset += 10;
                doc.text(`Retroalimentación: ${feedback}`, 10, yOffset);
                yOffset += 10;
                doc.text(`Puntuación: ${score}`, 10, yOffset);
                yOffset += 20;
            });

            doc.save('reporte.pdf');
        });

        $('#logoutButton').click(function() {
            logout();
        });

        function logout() {
            $.get('index.php?page=logout', function(data) {
                location.reload();
            });
        }
    });
    </script>
</body>
</html>