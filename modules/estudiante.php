<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Resultado <span id="presentationTitle"></span></h2>
            <div>
                <button class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                    Descargar Reporte
                </button>
                <button id="logoutButton" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-700">
                    Salir
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold">Promedio General</h3>
                <p class="text-4xl font-bold" id="average"></p>
                <h3 class="text-lg font-semibold mt-4">Progreso</h3>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                        <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 progress" id="progressBar" style="width: 85%;"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700" id="progress">85%</span>
                </div>
                <h3 class="text-lg font-semibold mt-4">Retroalimentación</h3>
                <p id="feedback">Excelente trabajo, sigue así. Recuerda enfocarte en mejorar tus habilidades de análisis.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">Criterios</h3>
                <ul id="criteria">
                    <!-- Se actualizará dinámicamente -->
                </ul>
            </div>
        </div>

        <h3 class="text-lg font-semibold mb-4">Ítems Evaluados</h3>
        <div class="space-y-4" id="evaluatedItems">
            <!-- Se actualizará dinámicamente -->
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
   $(document).ready(function() {
    function getResults() {
        $.ajax({
            url: 'includes/app.php?page=results',
            type: 'GET',
            dataType: 'text',
            success: function(resultData) {
                console.log('Raw response:', resultData); // Ver la respuesta cruda

                // Eliminar posibles caracteres no imprimibles o espacios en blanco
                resultData = resultData.trim();

                // Verificar si la respuesta contiene JSON válido
                if (resultData.startsWith('{') || resultData.startsWith('[')) {
                    try {
                        resultData = JSON.parse(resultData);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        return;
                    }

                    if (!resultData.items || !Array.isArray(resultData.items)) {
                        console.error('Expected an array of items but got:', resultData.items);
                        return;
                    }

                    // Limpiar elementos anteriores
                    $('#evaluatedItems').empty();
                    $('#criteria').empty();

                    let totalScore = 0;
                    let totalProjects = resultData.items.length;
                    let feedbackComments = [];

                    resultData.items.forEach(item => {
                        totalScore += item.calificacion;
                        feedbackComments.push(...item.resto_de_calificaciones.map(cal => cal.comentario));

                        let evaluatedItem = `
                            <div class="bg-gray-50 p-4 rounded shadow">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-md font-semibold">${item.titulo}</h4>
                                    <span class="text-xl font-bold">${item.calificacion.toFixed(2)}</span>
                                </div>
                                <p>${item.resto_de_calificaciones.map(cal => cal.comentario).join('. ')}</p>
                            </div>
                        `;
                        $('#evaluatedItems').append(evaluatedItem);
                    });

                    let averageScore = (totalScore / totalProjects).toFixed(2);
                    $('#average').text(averageScore);
                    $('#progress').text(`${averageScore * 10}%`);
                    $('#progressBar').css('width', `${averageScore * 10}%`);

                    if (resultData.items.length > 0) {
                        let criteriaList = resultData.items[0].resto_de_calificaciones.map(cal => `
                            <li class="flex justify-between">
                                <span>${cal.criterio}</span><span>${cal.calificacion}</span>
                            </li>
                        `).join('');
                        $('#criteria').html(criteriaList);
                    }

                    $('#feedback').text(feedbackComments.join('. '));

                    if (resultData.items.length > 0) {
                        $('#presentationTitle').text(resultData.items[0].titulo);
                    }

                    console.log(resultData);
                } else {
                    console.error('Invalid JSON format:', resultData);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching data:', textStatus, errorThrown);
            }
        });
    }

    getResults();
    setInterval(getResults, 20 * 1000);
});

$('#logoutButton').click(function() {
            logout();
        });

        function logout() {
            $.get('index.php?page=logout', function(data) {
                location.reload();
            });
        }

    </script>
</body>
</html>
