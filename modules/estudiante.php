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
    <script src="../assets/js/student.js"></script>
</body>
</html>