<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados Académicos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Resultados de <span id="projectTitle"></span></h2>
            <div class="flex space-x-4">
                <button id="downloadPdf" class="text-gray-500 hover:text-gray-700">Descargar Reporte</button>
                <button id="logoutButton" class="text-gray-500 hover:text-gray-700">Cerrar Sesión</button>
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
    function renderProject(project) {
        $('#projectTitle').text(project.titulo || 'Título no encontrado');
        $('#average').text(parseFloat(project.calificacion_general).toFixed(2));
        const progress = (project.calificacion_general / 5 * 100).toFixed(2);
        $('#progress').text(`${progress}%`);
        $('#progressBar').css('width', `${progress}%`);
        $('#feedback').text(project.comentarioGeneral || 'No hay comentarios.');

        let estudiantes = project.estudiantes ? project.estudiantes.join(', ') : 'N/A';
        let projectInfoHtml = `
            <li><strong>Título:</strong> ${project.titulo}</li>
            <li><strong>Descripción:</strong> ${project.descripcion}</li>
            <li><strong>Estudiantes:</strong> ${estudiantes}</li>
            <li><strong>Fase:</strong> ${project.Fase}</li>
            <li><strong>Línea:</strong> ${project.Linea}</li>
            <li><strong>Docente:</strong> ${project.docente}</li>
            <li><strong>Evaluador:</strong> ${project.evaluador}</li>
        `;
        $('#projectInfo').html(projectInfoHtml);

        let criteriaHtml = '';
        if (project.resultados) {
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
        }
        $('#evaluatedCriteria').html(criteriaHtml);
    }

    function getResults() {
        $.ajax({
            url: 'includes/app.php?page=results',
            type: 'GET',
            success: function(response) {
                try {
                    let parsedResponse = JSON.parse(response);

                    if (!Array.isArray(parsedResponse)) {
                        parsedResponse = [parsedResponse];
                    }

                    if (parsedResponse.length > 0 && !parsedResponse[0].error) {
                        renderProject(parsedResponse[0]);
                    } else {
                        $('#evaluatedCriteria').html(`<p>${parsedResponse[0].error || 'No se encontraron proyectos.'}</p>`);
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    $('#evaluatedCriteria').html('<p>Ocurrió un error al procesar los datos. Asegúrese de que el servidor está devolviendo JSON válido.</p>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching data:', textStatus, errorThrown);
                $('#evaluatedCriteria').html('<p>Ocurrió un error al obtener los datos. Verifique la consola para más detalles.</p>');
            }
        });
    }

    getResults();
    setInterval(getResults, 20 * 1000);

    $('#logoutButton').click(function() {
        $.get('index.php?page=logout', function() {
            location.reload();
        });
    });

    $('#downloadPdf').click(function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'legal');

        $('#downloadPdf').hide();
        $('#logoutButton').hide();

        html2canvas(document.body, {
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 216;
            const pageHeight = 356;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            doc.save('reporte.pdf');

            $('#downloadPdf').show();
            $('#logoutButton').show();
        });
    });
});
    </script>
</body>
</html>