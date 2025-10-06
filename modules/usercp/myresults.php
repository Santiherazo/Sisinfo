<?php
if(!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
if(!accessManager()->canAccess($_SESSION['userid'], ['estudiante', 'docente'], [['module' => 'usercp', 'action' => 'results']])) {
    die('No tienes permisos para acceder a este módulo.');
}

include(__PATH_MODULES__.'/header.php');
?>

<div class="mt-8 border-b pb-3 flex flex-wrap gap-4 sm:gap-6 overflow-x-auto sm:overflow-visible">
    <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
        <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Proyectos</a>
    <?php endif; ?>
    <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
        <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" class="text-[var(--color-accent)] font-medium border-b-2 border-[var(--color-accent)] pb-1 whitespace-nowrap">Resultados</a>
    <?php endif; ?>
    <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Perfil</a>
    <a href="<?php echo __BASE_URL__.'usercp/myphoto';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Fotografía</a>
    <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Seguridad</a>
</div>

<div class="mt-8">
    <h2 class="text-2xl font-bold mb-6">Buscador de Resultados</h2>

    <?php 

$evaluationManager = new EvaluationManager($pdo);

$searchTerm = $_GET['search'] ?? '';
$searchResults = [];
$selectedUser = null;
$userEvaluations = [];

if (!empty($searchTerm)) {
    $searchResults = $userManager->searchUsers($searchTerm);
    $selectedUserId = $_GET['user_id'] ?? null;
    if ($selectedUserId) {
        $selectedUser = $userManager->getUserById($selectedUserId);
        if ($selectedUser) {
            $userEvaluations = $evaluationManager->getUserEvaluations($selectedUserId);
        }
    }
}
    
    ?>
    <form method="GET" action="" class="mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <input type="text" 
                   name="search" 
                   value="<?php echo htmlspecialchars($searchTerm); ?>" 
                   placeholder="Buscar por documento, nombre o correo..." 
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]">
            <button type="submit" class="px-6 py-2 bg-[var(--color-accent)] text-white rounded-md hover:bg-opacity-90 transition-colors">
                Buscar
            </button>
        </div>
    </form>

    <?php if (!empty($searchTerm) && empty($searchResults)): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            No se encontraron usuarios con el término "<?php echo htmlspecialchars($searchTerm); ?>"
        </div>
    <?php endif; ?>

    <?php if (!empty($searchResults)): ?>
        <!-- Resultados de la búsqueda -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Resultados de la búsqueda</h3>
            <div class="grid gap-4">
                <?php foreach ($searchResults as $user): ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h4>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                <p class="text-sm text-gray-600">Documento: <?php echo htmlspecialchars($user['document'] ?? 'N/A'); ?></p>
                            </div>
                            <a href="?search=<?php echo urlencode($searchTerm); ?>&user_id=<?php echo $user['id']; ?>" 
                               class="px-4 py-2 bg-[var(--color-accent)] text-white rounded-md text-sm hover:bg-opacity-90 transition-colors">
                                Ver resultados
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($selectedUser): ?>
        <!-- Detalles del usuario seleccionado -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Resultados de: <?php echo htmlspecialchars($selectedUser['firstname'] . ' ' . $selectedUser['lastname']); ?></h3>
            
            <div class="bg-white border rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Nombre completo</p>
                        <p class="font-medium"><?php echo htmlspecialchars($selectedUser['firstname'] . ' ' . $selectedUser['lastname']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Correo electrónico</p>
                        <p class="font-medium"><?php echo htmlspecialchars($selectedUser['email']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Documento de identidad</p>
                        <p class="font-medium"><?php echo htmlspecialchars($selectedUser['document'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Evaluaciones del usuario -->
            <?php if (!empty($userEvaluations)): ?>
                <h4 class="text-lg font-medium mb-4">Evaluaciones realizadas</h4>
                
                <!-- Agrupar evaluaciones por proyecto -->
                <?php
                $groupedEvaluations = [];
                foreach ($userEvaluations as $evaluation) {
                    $projectId = $evaluation['project_id'];
                    if (!isset($groupedEvaluations[$projectId])) {
                        $groupedEvaluations[$projectId] = [
                            'project_title' => $evaluation['project_title'],
                            'evaluations' => []
                        ];
                    }
                    $groupedEvaluations[$projectId]['evaluations'][] = $evaluation;
                }
                ?>

                <?php foreach ($groupedEvaluations as $projectId => $projectData): ?>
                    <div class="border rounded-lg p-4 mb-6">
                        <h5 class="font-medium text-lg mb-3"><?php echo htmlspecialchars($projectData['project_title']); ?></h5>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criterio</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calificación</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($projectData['evaluations'] as $evaluation): ?>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($evaluation[RATINGS_CRITERIO_NOMBRE]); ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($evaluation[RATINGS_CALIFICACION]); ?>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-900">
                                                <?php echo htmlspecialchars($evaluation[RATINGS_OBSERVACION_PERSONAL] ?? 'Sin observaciones'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-gray-100 border rounded-lg p-6 text-center">
                    <p class="text-gray-600">Este usuario no ha realizado ninguna evaluación.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>