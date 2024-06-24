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
        <div id="btn_content" class="flex justify-end items-center mb-4">
            <div class="flex space-x-4 text-right text-sm">
                <button id="downloadPdf" class="text-gray-500 hover:text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Descargar Reporte
                </button>
                <button id="logoutButton" class="text-gray-500 hover:text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6-4v8"></path>
                    </svg>
                    Cerrar Sesión
                </button>
            </div>
        </div>
        <h2 class="text-2xl font-semibold text-center w-full mb-6">Resultados de <span id="projectTitle"></span></h2>
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
        <div id="evaluatedCriteria"></div>
    </div>

    <script>
    $(document).ready(function() {
        function renderProject(project) {
            $('#projectTitle').text(project.titulo || 'Título no encontrado');
            let calificaciones = project.calificaciones || [];
            let evaluadores = project.evaluadores || [];
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
            let evaluadoresHtml = evaluadores.length > 0 ? evaluadores.join(', ') : 'N/A';
            let projectInfoHtml = `
                <li><strong>Título:</strong> ${project.titulo}</li>
                <li><strong>Descripción:</strong> ${project.descripcion || 'Descripción no disponible'}</li>
                <li><strong>Estudiantes:</strong> ${estudiantes}</li>
                <li><strong>Fase:</strong> ${project.fase}</li>
                <li><strong>Línea:</strong> ${project.linea}</li>
                <li><strong>Docentes:</strong> ${docentes}</li>
                <li><strong>Evaluadores:</strong> ${evaluadoresHtml}</li>
            `;
            $('#projectInfo').html(projectInfoHtml);

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

            evaluadores.forEach((evaluador, index) => {
                let calificacion = calificaciones[index];
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

            $('#evaluatedCriteria').html(criteriaHtml);
        }

        function getResults() {
            $.ajax({
                url: 'endpoint/results',
                type: 'GET',
                success: function(response) {
                    let projects = JSON.parse(response);
                    if (Array.isArray(projects) && projects.length > 0) {
                        renderProject(projects[0]);
                    } else {
                        $('#contentToExport').html('<p>No hay información que cargar</p>');
                    }
                },
                error: function(error) {
                    console.error('Error fetching results:', error);
                }
            });
        }

        getResults();

        $('#downloadPdf').click(function() {
        $('#btn_content').hide();

        const { jsPDF } = window.jspdf;
        const content = document.getElementById('contentToExport');

        html2canvas(content, { scale: 6 }).then(canvas => {
            const imgData = canvas.toDataURL('image/jpeg');
            const pdf = new jsPDF();
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = 300;
            pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
            
            const projectTitle = $('#projectTitle').text();
            const maxChars = 60;
            const trimmedTitle = projectTitle.slice(0, maxChars);

            pdf.save(`${trimmedTitle}... .pdf`);
        });

        $('#btn_content').show();
    });


        $('#logoutButton').click(function() {
            window.location.href = '/logout';
        });
    });
    </script>
</body>
</html>