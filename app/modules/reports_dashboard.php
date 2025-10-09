<?php
$userId = $_SESSION['userid'] ?? 0;

if (!isLoggedIn()) redirect();

$projectManager = new ProjectManager($pdo);
$evaluationManager = new EvaluationManager($pdo);
$researchManager = new ResearchLineManager($pdo);

$allProjects = $projectManager->getAllProjects();
$userEvaluatedProjects = $evaluationManager->getUserEvaluatedProjects($userId);

$evaluatorProjects = [];
foreach ($allProjects as $project) {
    $isEvaluator = false;
    if (!empty($project['reviewers'])) {
        foreach ($project['reviewers'] as $reviewer) {
            if ($reviewer['id'] == $userId) {
                $isEvaluator = true;
                break;
            }
        }
    }
    
    if ($isEvaluator) {
        $evaluatorProjects[] = $project;
    }
}

$processedProjects = [];
foreach ($evaluatorProjects as $project) {
    $hasUserEvaluated = $evaluationManager->hasUserEvaluatedProject($project['id'], $userId);
    $ratingsSummary = $evaluationManager->getRatingSummary($project['id']);
    $userRatings = $evaluationManager->getRatingsByUser($project['id'], $userId);
    $lineaNombre = $researchManager->getLineaNombreById($project['linea_investigacion_id']);
    
    $processedProjects[] = [
        'id' => $project['id'],
        'titulo' => $project['titulo'],
        'linea_investigacion_id' => $project['linea_investigacion_id'],
        'linea_nombre' => $lineaNombre,
        'fase' => $project['fase'],
        'version' => $project['version'],
        'fecha_presentacion' => $project['fecha_presentacion'] ?? null,
        'calificado' => $hasUserEvaluated ? 1 : 0,
        'puntuacion' => $ratingsSummary['calificacion_total'] ?? null,
        'user_ratings' => $userRatings,
        'ratings_summary' => $ratingsSummary
    ];
}

$stats = [
    'total_projects' => count($processedProjects),
    'completed_evaluations' => 0,
    'pending_evaluations' => 0,
    'average_score' => 0,
    'scores_by_linea' => [],
    'scores_by_phase' => []
];

$totalScore = 0;
foreach ($processedProjects as $project) {
    if ($project['calificado']) {
        $stats['completed_evaluations']++;
        $totalScore += $project['puntuacion'] ?? 0;
        
        $linea = $project['linea_nombre'];
        if (!isset($stats['scores_by_linea'][$linea])) {
            $stats['scores_by_linea'][$linea] = ['total' => 0, 'count' => 0];
        }
        $stats['scores_by_linea'][$linea]['total'] += $project['puntuacion'];
        $stats['scores_by_linea'][$linea]['count']++;
        
        $phase = 'Fase ' . $project['fase'];
        if (!isset($stats['scores_by_phase'][$phase])) {
            $stats['scores_by_phase'][$phase] = ['total' => 0, 'count' => 0];
        }
        $stats['scores_by_phase'][$phase]['total'] += $project['puntuacion'];
        $stats['scores_by_phase'][$phase]['count']++;
    } else {
        $stats['pending_evaluations']++;
    }
}

$stats['average_score'] = $stats['completed_evaluations'] > 0 ? $totalScore / $stats['completed_evaluations'] : 0;

foreach ($stats['scores_by_linea'] as &$lineaData) {
    $lineaData['average'] = $lineaData['count'] > 0 ? $lineaData['total'] / $lineaData['count'] : 0;
}

foreach ($stats['scores_by_phase'] as &$phaseData) {
    $phaseData['average'] = $phaseData['count'] > 0 ? $phaseData['total'] / $phaseData['count'] : 0;
}
?>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Dashboard de Evaluaciones</h2>
        <div class="text-sm text-gray-600">
            Generado el <?php echo date('d/m/Y H:i'); ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <span class="text-blue-600">📋</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Proyectos</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_projects']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <span class="text-green-600">✅</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Evaluaciones Completadas</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed_evaluations']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <span class="text-orange-600">⏳</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Evaluaciones Pendientes</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_evaluations']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <span class="text-purple-600">⭐</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Promedio General</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['average_score'], 1); ?>/10</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Puntuación por Línea de Investigación</h3>
            <div class="space-y-4">
                <?php foreach ($stats['scores_by_linea'] as $linea => $data): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700"><?php echo $linea; ?></span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600"><?php echo number_format($data['average'], 1); ?>/10</span>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $data['average'] * 10; ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Puntuación por Fase</h3>
            <div class="space-y-4">
                <?php foreach ($stats['scores_by_phase'] as $phase => $data): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700"><?php echo $phase; ?></span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600"><?php echo number_format($data['average'], 1); ?>/10</span>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $data['average'] * 10; ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalle Completo de Evaluaciones</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Proyecto</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Línea</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Fase</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Fecha Presentación</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Estado</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Puntuación</th>
                        <th class="pb-3 text-left text-sm font-semibold text-gray-600">Última Evaluación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($processedProjects as $project): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-4">
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($project['titulo']); ?></p>
                            <p class="text-sm text-gray-500">v<?php echo $project['version']; ?></p>
                        </td>
                        <td class="py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                <?php echo htmlspecialchars($project['linea_nombre']); ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
                                Fase <?php echo $project['fase']; ?>
                            </span>
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            <?php echo $project['fecha_presentacion'] ? date('d/m/Y', strtotime($project['fecha_presentacion'])) : 'N/A'; ?>
                        </td>
                        <td class="py-4">
                            <span class="text-xs font-medium px-2 py-1 rounded <?php echo $project['calificado'] ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'; ?>">
                                <?php echo $project['calificado'] ? 'Completada' : 'Pendiente'; ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <?php if ($project['calificado']): ?>
                            <span class="text-sm font-medium text-green-600">
                                <?php echo number_format($project['puntuacion'], 1); ?>/10
                            </span>
                            <?php else: ?>
                            <span class="text-sm text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            <?php 
                            if ($project['calificado'] && !empty($project['user_ratings'])) {
                                $lastRating = end($project['user_ratings']);
                                echo date('d/m/Y H:i', strtotime($lastRating['updated_at'] ?? $lastRating['created_at']));
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>