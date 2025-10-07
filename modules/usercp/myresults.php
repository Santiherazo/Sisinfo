<?php
if(!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
if(!accessManager()->canAccess($_SESSION['userid'], ['estudiante', 'docente'], [['module' => 'usercp', 'action' => 'results']])) {
    die('No tienes permisos para acceder a este módulo.');
}

include(__PATH_MODULES__.'/header.php');

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

<div class="max-w-[1400px] mx-auto mb-12">
    <!-- Navegación Mejorada -->
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Proyectos</span>
            </a>
            <?php endif; ?>
            
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-accent)] text-[var(--color-navbar-text)] border border-[var(--color-accent)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Resultados</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-success)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-success)]/10 rounded flex items-center justify-center">
                    <i data-lucide="user" class="w-3 h-3 text-[var(--color-success)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Perfil</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/myphoto';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-warning)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-warning)]/10 rounded flex items-center justify-center">
                    <i data-lucide="camera" class="w-3 h-3 text-[var(--color-warning)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Fotografía</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-danger)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-danger)]/10 rounded flex items-center justify-center">
                    <i data-lucide="shield" class="w-3 h-3 text-[var(--color-danger)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Seguridad</span>
            </a>
        </div>
    </div>

    <!-- Header de la Página -->
    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-[var(--color-accent)]/10 rounded-lg flex items-center justify-center">
                <i data-lucide="award" class="w-5 h-5 text-[var(--color-accent)]"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-heading)]">Buscador de Resultados</h2>
                <p class="text-[var(--color-text-muted)] text-sm">Consulta y revisa los resultados de evaluaciones</p>
            </div>
        </div>

        <!-- Buscador -->
        <form method="GET" action="" class="mb-2">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]"></i>
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($searchTerm); ?>" 
                           placeholder="Buscar por documento, nombre o correo..." 
                           class="w-full pl-10 pr-4 py-3 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                </div>
                <button type="submit" class="px-6 py-3 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/90 text-[var(--color-navbar-text)] font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 whitespace-nowrap">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Buscar</span>
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($searchTerm) && empty($searchResults)): ?>
        <div class="bg-[var(--color-warning)]/10 border border-[var(--color-warning)]/20 text-[var(--color-warning)] p-4 rounded-lg mb-6 flex items-center space-x-3 animate-fadeIn">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <p class="font-medium text-sm">No se encontraron usuarios con el término "<?php echo htmlspecialchars($searchTerm); ?>"</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($searchResults)): ?>
        <!-- Resultados de la búsqueda -->
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp" style="animation-delay: 0.1s">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-[var(--color-primary)]"></i>
                </div>
                <h3 class="text-xl font-semibold text-[var(--color-heading)]">Resultados de la búsqueda</h3>
            </div>
            
            <div class="grid gap-4">
                <?php foreach ($searchResults as $user): ?>
                    <div class="border border-[var(--color-border)] rounded-lg p-4 hover:bg-[var(--color-dropdown-hover)] transition-all duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                                    <i data-lucide="user" class="w-5 h-5 text-[var(--color-primary)]"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-[var(--color-text)]"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h4>
                                    <p class="text-sm text-[var(--color-text-muted)]"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-sm text-[var(--color-text-muted)]">Documento: <?php echo htmlspecialchars($user['document'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <a href="?search=<?php echo urlencode($searchTerm); ?>&user_id=<?php echo $user['id']; ?>" 
                               class="px-4 py-2 bg-[var(--color-accent)] hover:bg-[var(--color-accent)]/90 text-[var(--color-navbar-text)] font-medium rounded-lg text-sm transition-all duration-200 flex items-center space-x-2">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>Ver resultados</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($selectedUser): ?>
        <!-- Detalles del usuario seleccionado -->
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] mb-6 animate-slideUp" style="animation-delay: 0.2s">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-[var(--color-success)]/10 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-[var(--color-success)]"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-[var(--color-heading)]">Resultados de: <?php echo htmlspecialchars($selectedUser['firstname'] . ' ' . $selectedUser['lastname']); ?></h3>
                    <p class="text-[var(--color-text-muted)] text-sm">Información detallada del usuario</p>
                </div>
            </div>
            
            <div class="bg-[var(--color-surface-alt)] rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center md:text-left">
                        <p class="text-sm text-[var(--color-text-muted)] mb-1">Nombre completo</p>
                        <p class="font-medium text-[var(--color-text)]"><?php echo htmlspecialchars($selectedUser['firstname'] . ' ' . $selectedUser['lastname']); ?></p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-sm text-[var(--color-text-muted)] mb-1">Correo electrónico</p>
                        <p class="font-medium text-[var(--color-text)]"><?php echo htmlspecialchars($selectedUser['email']); ?></p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-sm text-[var(--color-text-muted)] mb-1">Documento de identidad</p>
                        <p class="font-medium text-[var(--color-text)]"><?php echo htmlspecialchars($selectedUser['document'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Evaluaciones del usuario -->
            <?php if (!empty($userEvaluations)): ?>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-[var(--color-secondary)]/10 rounded-lg flex items-center justify-center">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-[var(--color-secondary)]"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-[var(--color-heading)]">Evaluaciones realizadas</h4>
                </div>
                
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
                    <div class="border border-[var(--color-border)] rounded-lg p-6 mb-6 last:mb-0">
                        <h5 class="font-semibold text-lg mb-4 text-[var(--color-heading)] flex items-center space-x-2">
                            <i data-lucide="folder" class="w-5 h-5 text-[var(--color-primary)]"></i>
                            <span><?php echo htmlspecialchars($projectData['project_title']); ?></span>
                        </h5>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[var(--color-border)]">
                                <thead class="bg-[var(--color-surface-alt)]">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider">Criterio</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider">Calificación</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[var(--color-surface)] divide-y divide-[var(--color-border)]">
                                    <?php foreach ($projectData['evaluations'] as $evaluation): ?>
                                        <tr class="hover:bg-[var(--color-dropdown-hover)] transition-colors">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                                                <?php echo htmlspecialchars($evaluation[RATINGS_CRITERIO_NOMBRE]); ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                                    <?php echo htmlspecialchars($evaluation[RATINGS_CALIFICACION]); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-[var(--color-text)]">
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
                <div class="bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-lg p-8 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-[var(--color-text-muted)] mx-auto mb-3 opacity-50"></i>
                    <p class="text-[var(--color-text-muted)] font-medium">Este usuario no ha realizado ninguna evaluación.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    lucide.createIcons();
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>