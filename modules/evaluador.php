<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de tesis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/ratchet/2.0.2/js/ratchet.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-gray-800 text-white p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Ficha de evaluación</h1>
                <div id="timer" class="text-4xl mx-auto">00:00</div>
                <div class="relative">
                    <button class="bg-gray-700 rounded-full p-2 focus:outline-none focus:ring">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A12.05 12.05 0 0012 21c2.178 0 4.21-.56 5.879-1.532M15 12h.01M12 12h.01M9 12h.01M21 12c0 1.657-1.034 3.064-2.487 3.66M3 12c0 1.657 1.034 3.064 2.487 3.66M12 3c2.178 0 4.21.56 5.879 1.532M15 12a3 3 0 11-6 0 3 3 0 016 0zm4.379-6.328A9 9 0 013.621 6.328M12 3v.01M12 12v.01m0-6v.01M3.879 6.328A9 9 0 003 12a9 9 0 009 9h0" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20 hidden">
                        <a id="" class="block px-4 py-2 text-gray-800 hover:bg-gray-200 cursor-pointer">My Account</a>
                        <a id="logoutButton" class="block px-4 py-2 text-gray-800 hover:bg-gray-200 cursor-pointer">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        <main id="infoTable" class="flex-grow p-4 flex max-h-screen">
            <aside class="w-1/4 bg-white p-4 rounded shadow overflow-y-auto max-h-1/2">
                <h2 class="text-lg font-bold mb-4">Lista de tesis</h2>
                <div class="mb-4">
                    <input type="text" id="searchInput" class="w-full p-2 border border-gray-300 rounded" placeholder="Buscar tesis...">
                </div>
                <div class="mb-4 flex space-x-2">
                    <button class="filter-button bg-black text-white text-sm py-1 px-2 rounded hover:bg-gray-700 focus:outline-none" data-phase="Propuesta">Propuesta</button>
                    <button class="filter-button bg-black text-white text-sm py-1 px-2 rounded hover:bg-gray-700 focus:outline-none" data-phase="Desarrollo">Desarrollo</button>
                    <button class="filter-button bg-black text-white text-sm py-1 px-2 rounded hover:bg-gray-700 focus:outline-none" data-phase="Aplicación">Aplicación</button>
                </div>
                <div id="projectList" class="space-y-4"></div>
            </aside>
            <section class="flex-grow bg-white p-4 ml-4 rounded shadow overflow-auto max-h-1/2">
                <h2 class="text-lg font-bold mb-4">Detalles de la tesis</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Título:</label>
                        <input type="text" id="projectName" class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50 loadProjectDetails">
                    </div>
                    <div>
                        <label class="block text-gray-700">Linea:</label>
                        <input type="text" id="projectLine" class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50 loadProjectDetails">
                    </div>
                    <div>
                        <label class="block text-gray-700">Fase:</label>
                        <input type="text" id="projectPhase" class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-opacity-50 loadProjectDetails">
                    </div>
                    <div>
                        <label class="block text-gray-700">Estudiante(s):</label>
                        <div id="projectParticipants" class="input-group"></div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Docente(s) Orientadores:</label>
                        <div id="projectAdvisors" class="input-group"></div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Evaluador(es):</label>
                        <div id="projectEvaluators" class="input-group"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <button id="startEvaluation" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Iniciar Evaluación</button>
                </div>
            </section>
        </main>
        <div id="evaluationTable" class="container mx-auto bg-white p-6 rounded shadow-md hidden">
            <h2 class="text-xl font-bold mb-4">Evaluación</h2>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Criterio</th>
                            <th class="border border-gray-300 px-4 py-2">Escala de Valoración</th>
                            <th class="border border-gray-300 px-4 py-2">Descripción</th>
                            <th class="border border-gray-300 px-4 py-2">Puntuación (0-5)</th>
                            <th class="border border-gray-300 px-4 py-2">Comentarios Específicos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Título</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>Deficiente (0-2.9).</li>
                                    <li>Bueno (3.0-3.9).</li>
                                    <li>Excelente (4.0-5.0).</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>El título no es claro y descriptivo con lo que quiere hacer en el proyecto, y utiliza más de 20 palabras, y no hay relación con el objetivo general y la pregunta problema.</li>
                                    <li>El título es claro, descriptivo, no utiliza más de 20 palabras, aunque guarda poca relación con el objetivo general y la pregunta problema.</li>
                                    <li>El título es claro, descriptivo, no utiliza más de 20 palabras, y guarda relación con el objetivo general y la pregunta problema.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="titleProject" name="titleProject" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedProject" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Introducción</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>Deficiente (0-2.9).</li>
                                    <li>Bueno (3.0-3.9).</li>
                                    <li>Excelente (4.0-5.0).</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>El estudiante sigue la estructura básica de la introducción, pero puede haber algunos elementos omitidos o desorganización.</li>
                                    <li>El estudiante sigue en su mayoría la estructura de la introducción, incluyendo los elementos necesarios, pero puede haber alguna falta de coherencia.</li>
                                    <li>El estudiante sigue claramente la estructura de la introducción, incluyendo los elementos necesarios y relacionándolos de manera coherente.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="introduction" name="introduction" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedIntroduction" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Planteamiento del Problema</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>La descripción del problema no es clara, ya que no enuncia sus causas, consecuencias y como abordarlo, no cita datos o cifras que respalda la problemática, no formula el problema con una pregunta.</li>
                                    <li>Describe el problema claramente, mostrando sus causas, consecuencias y como abordarlo, pero no cita datos o cifras que respalda la problemática, aunque formula el problema con una pregunta.</li>
                                    <li>Describe el problema claramente, mostrando sus causas, consecuencias y como abordarlo, pero no cita datos o cifras que respalda la problemática, aunque formula el problema con una pregunta.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="problemStatement" name="problemStatement" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="FeedStatement" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Justificación</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>No es clara la forma de justificar el por qué y para qué se quiere estudiar e investigar ese problema, y no tiene en cuenta criterios como relevancia, importancia, viabilidad, e impacto social.</li>
                                    <li>Justifica de manera clara y coherente el por qué y para qué se quiere estudiar e investigar ese problema, teniendo en cuenta algunos criterios como relevancia, importancia, viabilidad, e impacto social.</li>
                                    <li>Justifica de manera clara y coherente el por qué y para qué se quiere estudiar e investigar ese problema, teniendo en cuenta los criterios de relevancia, importancia, viabilidad, e impacto social.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="justify" name="justify" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedJustify" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Objetivos</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>El objetivo general no es medible, guarda poca relación con el título y la pregunta problema, debe ser mejorado, no es claro el qué se debe hacer, para qué, y el cómo hacerlo.  Algunos objetivos específicos no están bien formulados, por lo que no son medibles, y no permiten alcanzar el objetivo general, deben ser mejorados.</li>
                                    <li>El objetivo general es medible, guarda relación con el título y la pregunta problema, y es claro el qué se debe hacer, para qué, y el cómo hacerlo, y algunos objetivos específicos están bien formulados y son medibles, y permiten alcanzar el objetivo general.</li>
                                    <li>El objetivo general es medible, guarda relación con el título y la pregunta problema, y es claro el qué se debe hacer, para qué, y el cómo hacerlo, y todos los objetivos específicos están bien formulados y son medibles, y permiten alcanzar el objetivo general.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="targets" name="targets" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedTargets" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Marco Teorico</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>No demuestra una comprensión profunda de los conceptos y teorías relevantes que debe tener su proyecto para poder implementarlos en su desarrollo.</li>
                                    <li>Integra adecuadamente las ideas y teorías en la construcción del marco teórico	con base lo sustentado.</li>
                                    <li>Explica claramente la relevancia y pertinencia del marco teórico para el proyecto de investigación que desarrolla.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="theorical" name="theorical" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedTheorical" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Metodología</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>No es clara la descripción del diseño metodológico del proyecto, ya que no se identifican elementos como el tipo de investigación, la población y la muestra, las técnicas de recolección de datos, las fases de ejecución del proyecto. Hay que mejorar en su totalidad la congruencia entre el diseño metodológico, el planteamiento del problema, y los conceptos a desarrollar.</li>
                                    <li>Hay una clara y buena descripción del tipo de investigación, la población y la muestra, se presentan las técnicas de recolección de datos, se indican las fases de ejecución del proyecto. Hay que mejorar un poco la congruencia entre el diseño metodológico, el planteamiento del problema, y los conceptos a desarrollar.</li>
                                    <li>Hay una clara y excelente descripción del tipo de investigación, la población y la muestra, se presentan las técnicas de recolección de datos, se indican las fases de ejecución del proyecto. Hay congruencia entre dicho diseño, el planteamiento del problema, y los conceptos a utilizar.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="methodology" name="methodology" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedMethodology" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Resultados</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>Los resultados iniciales no son claros y no están alineados con los objetivos del proyecto.</li>
                                    <li>Los resultados iniciales son claros y están alineados con los objetivos del proyecto, aunque falta más detalle en la explicación.</li>
                                    <li>Los resultados iniciales son muy claros, detallados y están perfectamente alineados con los objetivos del proyecto.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="mainResults" name="mainResults" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedMainresults" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Sustentación</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div>Deficiente (0-2.9)</div>
                                <div>Bueno (3.0-3.9)</div>
                                <div>Excelente (4.0-5.0)</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <ul class="list-disc ml-4">
                                    <li>Se realiza una presentación con el recurso digital utilizado, aunque el recurso no contiene los lineamientos establecidos, y se evidencia poco dominio temático, y no se responden de forma adecuada las preguntas formuladas por los jurados.</li>
                                    <li>Se realiza una buena presentación con el recurso digital utilizado, el cual contiene los lineamientos establecidos, se evidencia un buen dominio temático, aunque no se responden de forma adecuada en su totalidad las preguntas formuladas por los jurados.</li>
                                    <li>Se realiza una excelente presentación con el recurso digital utilizado, el cual contiene los lineamientos establecidos, evidenciándose un excelente dominio temático, respondiendo de forma adecuada las preguntas formuladas por los jurados.</li>
                                </ul>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><input type="number" id="support" name="support" min="0" max="5" step="0.1" class="puntaje w-full border border-gray-300 rounded puntuacion"></td>
                            <td class="border border-gray-300 px-4 py-2"><textarea id="feedSupport" class="w-full border border-gray-300 rounded h-24"></textarea></td>
                        </tr>
                        <tr class="bg-gray-200">
                            <td colspan="5" class="font-bold text-right px-4 py-2">Total Puntuación: <span id="rating">0</span></td>
                            <td class="font-bold text-center border-t border-gray-300"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4">
                    <label for="generalComments" class="block text-gray-700 font-bold mb-2">Comentarios Generales</label>
                    <textarea id="generalComments" class="w-full border border-gray-300 rounded p-2 h-32"></textarea>
                </div>
                <div class="mt-4">
                    <button id="saveRubric" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Finalizar Evaluación</button>
                </div>
        </div>
    </div>
    <div id="successPopup" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="relative bg-white p-6 rounded-lg shadow-lg text-center fade-in">
            <button id="closePopupBtn" class="absolute top-0 right-0 mt-2 mr-2 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex justify-center mb-4">
                <svg class="h-16 w-16 text-green-500 scale-in" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-4">¡Éxito!</h2>
            <p class="mb-4">La respuesta fue enviada con éxito.</p>
        </div>
    </div>
    <script src="../assets/js/evaluador.js"></script>
</body>
</html>
