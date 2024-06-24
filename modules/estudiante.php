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
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md" id="contentToExport">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-center w-full">Resultados de <span id="projectTitle"></span></h2>
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
                        <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center progress" id="progressBar"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700" id="progress"></span>
                </div>
                <h3 class="text-lg font-semibold mt-4">Retroalimentación</h3>
                <div id="feedback" class="space-y-2"></div>
            </div>
            <div class="bg-gray-50 p-4 rounded shadow">
                <h3 class="text-lg font-semibold">Información del Proyecto</h3>
                <ul id="projectInfo">
                </ul>
            </div>
        </div>
        <h3 class="text-lg font-semibold mb-4">Criterios Evaluados</h3>
        <div class="space-y-4" id="evaluatedCriteria"></div>
        <h3 class="text-lg font-semibold mb-4">Retroalimentaciones</h3>
        <div class="space-y-4" id="evaluatorsFeedback"></div>
    </div>

    <script>
    $(document).ready(function() {
        function renderProject(project) {
            $('#projectTitle').text(project.titulo || 'Título no encontrado');
            let calificaciones = project.calificaciones || [];
            let calificacion_general = calificaciones.length > 0 
                ? (calificaciones.reduce((acc, curr) => acc + parseFloat(curr.rating), 0) / calificaciones.length)
                : 0;
            $('#average').text(calificacion_general.toFixed(2));
            const progress = (calificacion_general / 5 * 100).toFixed(2);
            $('#progress').text(`${progress}%`);
            if (calificacion_general >= 3) {
                $('#progressBar').addClass('bg-green-500');
            } else {
                $('#progressBar').addClass('bg-red-500');
            }
            $('#progressBar').css('width', `${progress}%`);
            let feedbacks = calificaciones.map(criterio => `<p>${criterio.generalComments || "No hubo comentario adicional"}</p>`).join("");
            $('#feedback').html(feedbacks);
            let estudiantes = project.investigadores ? project.investigadores.join(', ') : 'N/A';
            let docentes = project.docentes ? project.docentes : 'N/A';
            let evaluadores = project.evaluadores ? project.evaluadores.join(', ') : 'N/A';
            let projectInfoHtml = `
                <li><strong>Título:</strong> ${project.titulo}</li>
                <li><strong>Descripción:</strong> ${project.descripcion || 'Descripción no disponible'}</li>
                <li><strong>Estudiantes:</strong> ${estudiantes}</li>
                <li><strong>Fase:</strong> ${project.fase}</li>
                <li><strong>Línea:</strong> ${project.linea}</li>
                <li><strong>Docentes:</strong> ${docentes}</li>
                <li><strong>Evaluadores:</strong> ${evaluadores}</li>
            `;
            $('#projectInfo').html(projectInfoHtml);
            let criteriaHtml = `
                <table class="table-auto w-full bg-gray-50 shadow rounded mb-4">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Criterios</th>`;
            calificaciones.forEach(criterio => {
                criteriaHtml += `<th class="px-4 py-2">${criterio.assessor}</th>`;
            });
            criteriaHtml += `</tr></thead><tbody>`;
            const properties = [
                { name: "titleProject", label: "Título del Proyecto" },
                { name: "introduction", label: "Introducción" },
                { name: "problemStatement", label: "Planteamiento del Problema" },
                { name: "justify", label: "Justificación" },
                { name: "targets", label: "Objetivos" },
                { name: "theorical", label: "Marco Teórico" },
                { name: "methodology", label: "Metodología" },
                { name: "mainResults", label: "Resultados" },
                { name: "support", label: "Sustentación" }
            ];
            properties.forEach(property => {
                criteriaHtml += `<tr><td class="border px-4 py-2 font-semibold">${property.label}</td>`;
                calificaciones.forEach(criterio => {
                    let value = criterio[property.name] || "No hay comentarios";
                    criteriaHtml += `<td class="border px-4 py-2">${value}</td>`;
                });
                criteriaHtml += `</tr>`;
            });
            criteriaHtml += `<tr><td class="border px-4 py-2 font-semibold">Resultado Final</td>`;
            let totalFinalRating = 0;
            calificaciones.forEach(criterio => {
                let value = criterio.rating || "No hay una calificación";
                totalFinalRating += parseFloat(criterio.rating) || 0;
                criteriaHtml += `<td class="border px-4 py-2">${value}</td>`;
            });
            let finalAverageRating = (totalFinalRating / calificaciones.length).toFixed(2);
            criteriaHtml += `<td class="border px-4 py-2">${finalAverageRating}</td></tr>`;
            criteriaHtml += `</tbody></table>`;
            $('#evaluatedCriteria').html(criteriaHtml);
            let feedbackHtml = '';
            calificaciones.forEach((criterio, index) => {
                feedbackHtml += `
                    <div class="bg-gray-50 p-4 rounded shadow mb-4">
                        <h4 class="text-md font-semibold">Retroalimentación de ${criterio.assessor}</h4>
                        <p><strong>Título:</strong> ${criterio.feedProject || "No hay comentarios"}</p>
                        <p><strong>Introducción:</strong> ${criterio.feedIntroduction || "No hay comentarios"}</p>
                        <p><strong>Planteamiento del problema:</strong> ${criterio.FeedStatement || "No hay comentarios"}</p>
                        <p><strong>Justificación:</strong> ${criterio.feedJustify || "No hay comentarios"}</p>
                        <p><strong>Objetivos:</strong> ${criterio.feedTargets || "No hay comentarios"}</p>
                        <p><strong>Marco teórico:</strong> ${criterio.feedTheorical || "No hay comentarios"}</p>
                        <p><strong>Metodología:</strong> ${criterio.feedMethodology || "No hay comentarios"}</p>
                        <p><strong>Resultados:</strong> ${criterio.feedMainresults || "No hay comentarios"}</p>                        
                        <p><strong>Sustentación:</strong> ${criterio.feedSupport || "No hay comentarios"}</p>
                    </div>`;
            });
            $('#evaluatorsFeedback').html(feedbackHtml);
        }
        function getResults() {
            $.ajax({
                url: 'endpoint/results',
                type: 'GET',
                success: function(response) {
                    let projects = JSON.parse(response);
                    if (Array.isArray(projects) && projects.length > 0) {
                        renderProject(projects[0]);
                    }
                },
                error: function(error) {
                    console.error('Error fetching project data:', error);
                }
            });
        }
        getResults();
        setInterval(getResults, 20 * 1000);
        $('#logoutButton').click(function() {
            $.get('./logout', function() {
                location.reload();
            });
        });
        $('#downloadPdf').click(function() {
            const { jsPDF } = window.jspdf;
            const content = document.getElementById('contentToExport');
            const doc = new jsPDF('p', 'mm', 'a4');
            const options = {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                logging: true,
                width: content.clientWidth,
                height: content.clientHeight,
                windowWidth: content.scrollWidth,
                windowHeight: content.scrollHeight
            };
            $('.flex.space-x-4').hide();
            $('h2').css('text-align', 'center');
            html2canvas(content, options).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 210;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let position = 0;
                while (position < imgHeight) {
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    position += imgHeight;
                    if (position < imgHeight) {
                        doc.addPage();
                    }
                }
                doc.save('reporte.pdf');
                $('.flex.space-x-4').show();
                $('h2').css('text-align', '');
            });
        });
    });
    </script>
</body>
</html>