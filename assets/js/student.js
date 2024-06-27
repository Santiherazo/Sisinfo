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
    logout();
});

function logout() {
    $.get('/logout', function(data) {
        location.reload();
    });
}
});