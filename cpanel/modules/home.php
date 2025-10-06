<?php if(file_exists(__ROOT_DIR__ . 'install/')): ?>
<div id="securityWarningContainer">
  <div class="glassmorphism rounded-xl p-4 shadow-lg mb-4 border-l-4 border-yellow-500 bg-yellow-50/60 block sm:hidden">
    <div class="flex flex-col items-center text-center relative">
      <button onclick="hideSecurityWarning()" class="absolute top-2 right-2 p-1 text-yellow-600 hover:text-yellow-800">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
      <div class="p-2 bg-yellow-100 rounded-lg mb-3 mt-2">
        <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-600"></i>
      </div>
      <h3 class="font-bold text-yellow-900 mb-2 text-lg">ADVERTENCIA DE SEGURIDAD</h3>
      <p class="text-sm text-yellow-800 mb-4">
        El directorio <strong class="font-semibold">install/</strong> de WebEngine CMS todavía existe. 
        Por seguridad, se recomienda encarecidamente renombrarlo o eliminarlo.
      </p>
      <div class="w-full flex flex-col gap-3">
        <button onclick="hideSecurityWarning()" class="w-full px-4 py-3 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium">
          Ocultar advertencia
        </button>
        <a href="https://github.com/Santiherazo/Sisinfov1" target="_blank" class="w-full px-4 py-3 bg-white/80 border border-white/20 text-slate-800 rounded-lg hover:bg-white transition-colors text-sm font-medium">
          Ver documentación
        </a>
      </div>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 border-l-4 border-yellow-500 bg-yellow-50/60 hidden sm:block">
    <div class="flex items-start gap-4">
      <div class="p-2 bg-yellow-100 rounded-lg">
        <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
      </div>
      <div class="flex-1">
        <h3 class="font-bold text-yellow-900 mb-1">ADVERTENCIA DE SEGURIDAD</h3>
        <p class="text-sm text-yellow-800">
          El directorio <strong class="font-semibold">install/</strong> de WebEngine CMS todavía existe. 
          Por seguridad, se recomienda encarecidamente renombrarlo o eliminarlo.
        </p>
        <div class="mt-3 flex gap-3">
          <button onclick="hideSecurityWarning()" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium">
            Ocultar advertencia
          </button>
          <a href="https://github.com/Santiherazo/Sisinfov1" target="_blank" class="px-4 py-2 bg-white/80 border border-white/20 text-slate-800 rounded-lg hover:bg-white transition-colors text-sm font-medium">
            Ver documentación
          </a>
        </div>
      </div>
      <button onclick="hideSecurityWarning()" class="p-1 text-yellow-600 hover:text-yellow-800">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
  </div>
</div>
<?php endif; ?>

<?php 
$currentYear = date('Y');
$now = new DateTime();
$format = 'Y-m-d H:i:s';
$startOfYear = new DateTime("$currentYear-01-01 00:00:00");
$endOfYear = new DateTime("$currentYear-12-31 23:59:59");

$yearProgressPercent = 0;
if ($endOfYear->getTimestamp() > $startOfYear->getTimestamp()) {
    $yearProgressPercent = round((($now->getTimestamp() - $startOfYear->getTimestamp()) / 
                                ($endOfYear->getTimestamp() - $startOfYear->getTimestamp())) * 100, 2);
}
$yearProgressPercent = min(100, max(0, $yearProgressPercent));

$totalUsers = $profileManager->countActiveUsers(array('start_date' => $startOfYear->format('Y-m-d H:i:s'), 'end_date' => $endOfYear->format('Y-m-d H:i:s')));
$userYearCount = $profileManager->countActiveUsersBetweenDates($startOfYear->format($format), $now->format($format));
$userGrowthPercent = ($yearProgressPercent > 0 && $userYearCount > 0) ? 
                    min(100, round(($userYearCount / ($totalUsers ?: 1)) * 100, 2)) : 0;

$totalProjects = $projectmanager->countProjects();
$projectYearCount = $projectmanager->countProjectsBetweenDates($startOfYear->format($format), $now->format($format));
$projectGrowthPercent = ($yearProgressPercent > 0 && $projectYearCount > 0) ? 
                       min(100, round(($projectYearCount / ($totalProjects ?: 1)) * 100, 2)) : 0;

$conditions = array(RATING_SUMMARY_ESTADO_EVALUACION => array('completa'));
$totalEvaluations = $evaluationManager->countEvaluations($conditions);
$evaluationYearCount = $evaluationManager->countEvaluationsBetweenDates(
    $startOfYear->format($format), 
    $now->format($format), 
    RATING_SUMMARY_CREATED_AT,
    $conditions
);
$evaluationGrowthPercent = ($yearProgressPercent > 0 && $evaluationYearCount > 0) ? 
                          min(100, round(($evaluationYearCount / ($totalEvaluations ?: 1)) * 100, 2)) : 0;

$pendingConditions = array(_CLMN_WEBENGINE_PROJECT_ESTADO_ => array('nuevo'));
$pendingProjects = $projectmanager->countProjects($pendingConditions);
$pendingYearCount = $projectmanager->countProjectsBetweenDates($startOfYear->format($format), $now->format($format), 'creado', $pendingConditions);
$pendingGrowthPercent = ($yearProgressPercent > 0 && $pendingYearCount > 0) ? 
                       min(100, round(($pendingYearCount / ($pendingProjects ?: 1)) * 100, 2)) : 0;

$latestUsers = $profileManager->getLatestRegisteredUsers(5);

function getServerUptime() {
    if (file_exists('/proc/uptime')) {
        $uptime = @file_get_contents("/proc/uptime");
        if ($uptime !== false) {
            $seconds = (int)explode(' ', $uptime)[0];
            $hours = floor($seconds / 3600);
            $days = floor($hours / 24);
            return "$days días, " . ($hours % 24) . " horas";
        }
    }
    return "No disponible";
}

function getServerLoad() {
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        return round($load[0] ?? 0, 2) . '%';
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return "N/A (Windows)";
    }
    if (is_readable('/proc/loadavg')) {
        $load = file_get_contents('/proc/loadavg');
        $load = explode(' ', $load);
        return round(floatval($load[0]), 2) . '%';
    }
    return "N/A";
}

$serverUptime = getServerUptime();
$cpuLoad = getServerLoad();
$memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
$memoryPeakUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';

function tiempoRelativo(string $fecha): string {
    if (empty($fecha)) return 'Fecha no disponible';
    
    $timestamp = strtotime($fecha);
    if ($timestamp === false) return 'Fecha inválida';
    
    $diferencia = time() - $timestamp;

    if ($diferencia < 60) return 'Hace unos segundos';
    if ($diferencia < 3600) return 'Hace ' . floor($diferencia / 60) . ' minutos';
    if ($diferencia < 86400) return 'Hace ' . floor($diferencia / 3600) . ' horas';
    if ($diferencia < 604800) return 'Hace ' . floor($diferencia / 86400) . ' días';

    return date('d/m/Y', $timestamp);
}
?>

<script>
window.dashboardData = {
  totalUsers: <?php echo (int)$totalUsers; ?>,
  userGrowthPercent: <?php echo (float)$userGrowthPercent; ?>,
  totalProjects: <?php echo (int)$totalProjects; ?>,
  projectGrowthPercent: <?php echo (float)$projectGrowthPercent; ?>,
  totalEvaluations: <?php echo (int)$totalEvaluations; ?>,
  evaluationGrowthPercent: <?php echo (float)$evaluationGrowthPercent; ?>,
  pendingProjects: <?php echo (int)$pendingProjects; ?>,
  pendingGrowthPercent: <?php echo (float)$pendingGrowthPercent; ?>,
  yearProgressPercent: <?php echo (float)$yearProgressPercent; ?>,
  latestUsers: <?php echo json_encode($latestUsers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
  serverUptime: "<?php echo addslashes($serverUptime); ?>",
  cpuLoad: "<?php echo addslashes($cpuLoad); ?>",
  memoryUsage: "<?php echo addslashes($memoryUsage); ?>",
  memoryPeakUsage: "<?php echo addslashes($memoryPeakUsage); ?>",
  phpVersion: "<?php echo addslashes(phpversion()); ?>",
  osInfo: "<?php echo addslashes(PHP_OS); ?>",
  cmsVersion: "<?php echo addslashes(__WEBENGINE_VERSION__); ?>"
};
</script>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
  <div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">
    <div class="flex items-center justify-between mb-4">
      <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl shadow-lg">
        <i data-lucide="users" class="w-6 h-6 text-white"></i>
      </div>
      <span id="userGrowthPercent" class="text-sm font-semibold <?= $userGrowthPercent >= 100 ? 'text-green-600' : ($userGrowthPercent >= 80 ? 'text-yellow-600' : 'text-red-600') ?>">
        <?= $userGrowthPercent; ?>%
      </span>
    </div>
    <div>
      <p class="text-sm font-medium text-slate-600 mb-1">Total Usuarios</p>
      <p id="totalUsers" class="text-3xl font-bold text-slate-900"><?php echo $totalUsers; ?></p>
      <p class="text-xs text-slate-500">Registrados este año: <?= $userYearCount ?></p>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">
    <div class="flex items-center justify-between mb-4">
      <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl shadow-lg">
        <i data-lucide="folder-open" class="w-6 h-6 text-white"></i>
      </div>
      <span id="projectGrowthPercent" class="text-sm font-semibold <?= $projectGrowthPercent >= 100 ? 'text-green-600' : ($projectGrowthPercent >= 80 ? 'text-yellow-600' : 'text-red-600') ?>">
        <?= $projectGrowthPercent; ?>%
      </span>
    </div>
    <div>
      <p class="text-sm font-medium text-slate-600 mb-1">Proyectos Activos</p>
      <p id="totalProjects" class="text-3xl font-bold text-slate-900"><?php echo $totalProjects; ?></p>
      <p class="text-xs text-slate-500">Creados este año: <?= $projectYearCount ?></p>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">
    <div class="flex items-center justify-between mb-4">
      <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl shadow-lg">
        <i data-lucide="clipboard-check" class="w-6 h-6 text-white"></i>
      </div>
      <span class="text-sm font-semibold <?= $evaluationGrowthPercent >= 100 ? 'text-green-600' : ($evaluationGrowthPercent >= 80 ? 'text-yellow-600' : 'text-red-600') ?>">
        <?= $evaluationGrowthPercent; ?>%
      </span>
    </div>
    <div>
      <p class="text-sm font-medium text-slate-600 mb-1">Evaluaciones</p>
      <p class="text-3xl font-bold text-slate-900"><?php echo $totalEvaluations; ?></p>
      <p class="text-xs text-slate-500">Este año: <?= $evaluationYearCount ?></p>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">
    <div class="flex items-center justify-between mb-4">
      <div class="p-3 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl shadow-lg">
        <i data-lucide="clock" class="w-6 h-6 text-white"></i>
      </div>
      <span class="text-sm font-semibold <?= $pendingGrowthPercent >= 100 ? 'text-green-600' : ($pendingGrowthPercent >= 80 ? 'text-yellow-600' : 'text-red-600') ?>">
        <?= $pendingGrowthPercent; ?>%
      </span>
    </div>
    <div>
      <p class="text-sm font-medium text-slate-600 mb-1">Pendientes</p>
      <p class="text-3xl font-bold text-slate-900"><?php echo $pendingProjects; ?></p>
      <p class="text-xs text-slate-500">Este año: <?= $pendingYearCount ?></p>
    </div>
  </div>
</div>

<div class="grid gap-8 lg:grid-cols-3 mb-8">
  <div class="glassmorphism rounded-2xl p-6 shadow-xl">
    <div class="flex items-center gap-2 mb-6">
      <i data-lucide="server" class="w-5 h-5 text-blue-600"></i>
      <h3 class="text-lg font-semibold text-slate-900">Información del Sistema</h3>
    </div>
    <div class="space-y-4">
      <div class="flex items-center justify-between text-sm p-2 bg-white/60 rounded-lg">
        <span class="font-medium">WebEngine CMS</span>
        <span id="cmsVersion" class="text-slate-600"><?php echo htmlspecialchars(__WEBENGINE_VERSION__); ?></span>
      </div>
      <div class="flex items-center justify-between text-sm p-2 bg-white/60 rounded-lg">
        <span class="font-medium">Versión PHP</span>
        <span id="phpVersion" class="text-slate-600"><?php echo htmlspecialchars(phpversion()); ?></span>
      </div>
      <div class="flex items-center justify-between text-sm p-2 bg-white/60 rounded-lg">
        <span class="font-medium">Sistema Operativo</span>
        <span id="osInfo" class="text-slate-600"><?php echo htmlspecialchars(PHP_OS); ?></span>
      </div>
      <div class="flex items-center justify-between text-sm p-2 bg-white/60 rounded-lg">
        <span class="font-medium">Hora del Servidor</span>
        <span id="serverTime" class="text-slate-600"><?php echo date("Y-m-d H:i:s"); ?></span>
      </div>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl">
    <div class="flex items-center gap-2 mb-6">
      <i data-lucide="activity" class="w-5 h-5 text-orange-600"></i>
      <h3 class="text-lg font-semibold text-slate-900">Actividad Reciente</h3>
    </div>
    <div id="recentActivity" class="space-y-4">
      <?php if (!empty($latestUsers)): ?>
        <?php foreach ($latestUsers as $usuario): 
          $nombre = trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? ''));
          $iniciales = strtoupper(substr($usuario['first_name'] ?? 'U', 0, 1) . substr($usuario['last_name'] ?? 'N', 0, 1));
          $tiempo = tiempoRelativo($usuario['created_at'] ?? '');
        ?>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
              <span class="text-white text-xs font-semibold"><?= $iniciales ?></span>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-slate-900">Nuevo usuario registrado</p>
              <p class="text-xs text-slate-500"><?= htmlspecialchars($nombre) ?> • <?= $tiempo ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-sm text-slate-500 text-center py-4">No hay usuarios recientes</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="glassmorphism rounded-2xl p-6 shadow-xl">
    <div class="flex items-center gap-2 mb-6">
      <i data-lucide="zap" class="w-5 h-5 text-yellow-600"></i>
      <h3 class="text-lg font-semibold text-slate-900">Acciones Rápidas</h3>
    </div>
    <div class="grid gap-4">
      <a href="<?php echo admincp_base(); ?>?module=addUser" class="flex items-center gap-4 p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg">
        <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl shadow-lg">
          <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
        </div>
        <div class="text-left">
          <p class="font-semibold text-slate-900">Crear Usuario</p>
          <p class="text-xs text-slate-600">Registrar nuevo usuario</p>
        </div>
      </a>
      <a href="<?php echo admincp_base(); ?>?module=addProject" class="flex items-center gap-4 p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg">
        <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl shadow-lg">
          <i data-lucide="folder-plus" class="w-5 h-5 text-white"></i>
        </div>
        <div class="text-left">
          <p class="font-semibold text-slate-900">Nuevo Proyecto</p>
          <p class="text-xs text-slate-600">Crear proyecto de investigación</p>
        </div>
      </a>
      <a href="<?php echo admincp_base(); ?>?module=system" class="flex items-center gap-4 p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg">
        <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl shadow-lg">
          <i data-lucide="settings" class="w-5 h-5 text-white"></i>
        </div>
        <div class="text-left">
          <p class="font-semibold text-slate-900">Configuración</p>
          <p class="text-xs text-slate-600">Ajustes del sistema</p>
        </div>
      </a>
    </div>
  </div>
</div>

<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-8">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full">
        <div class="w-2.5 h-2.5 bg-green-500 rounded-full pulse-animation"></div>
        <span class="text-sm font-medium text-slate-800">Sistema Operativo</span>
      </div>
      <span class="text-sm text-slate-600 hidden md:block">Todos los servicios funcionando correctamente</span>
    </div>
    
    <div class="flex gap-2">
      <a href="mailto:herazopsantiago@gmail.com" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded-full text-sm font-medium text-blue-700 transition-colors">
        <i data-lucide="help-circle" class="w-4 h-4"></i>
        <span>Ayuda</span>
      </a>
      <a href="https://wa.me/573007639973" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 rounded-full text-sm font-medium text-purple-700 transition-colors">
        <i data-lucide="message-square" class="w-4 h-4"></i>
        <span>Soporte</span>
      </a>
    </div>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white/60 p-3 rounded-lg">
      <div class="flex items-center gap-2 text-slate-500 mb-1">
        <i data-lucide="activity" class="w-4 h-4"></i>
        <span class="text-xs">Uptime</span>
      </div>
      <p id="serverUptime" class="font-medium text-slate-900"><?php echo htmlspecialchars($serverUptime); ?></p>
    </div>
    
    <div class="bg-white/60 p-3 rounded-lg">
      <div class="flex items-center gap-2 text-slate-500 mb-1">
        <i data-lucide="cpu" class="w-4 h-4"></i>
        <span class="text-xs">Carga CPU</span>
      </div>
      <p id="cpuLoad" class="font-medium text-slate-900"><?php echo htmlspecialchars($cpuLoad); ?></p>
    </div>
    
    <div class="bg-white/60 p-3 rounded-lg">
      <div class="flex items-center gap-2 text-slate-500 mb-1">
        <i data-lucide="database" class="w-4 h-4"></i>
        <span class="text-xs">Memoria Usada</span>
      </div>
      <p id="memoryUsage" class="font-medium text-slate-900"><?php echo htmlspecialchars($memoryUsage); ?></p>
    </div>
    
    <div class="bg-white/60 p-3 rounded-lg">
      <div class="flex items-center gap-2 text-slate-500 mb-1">
        <i data-lucide="bar-chart" class="w-4 h-4"></i>
        <span class="text-xs">Memoria Pico</span>
      </div>
      <p id="memoryPeakUsage" class="font-medium text-slate-900"><?php echo htmlspecialchars($memoryPeakUsage); ?></p>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    initializeDashboard();
    setInterval(updateServerTime, 1000);
});

function hideSecurityWarning() {
    const securityWarningContainer = document.getElementById('securityWarningContainer');
    if (securityWarningContainer) {
        securityWarningContainer.style.display = 'none';
    }
}

function initializeDashboard() {
    updateServerTime();
    setInterval(updateServerTime, 1000);
}

function updateServerTime() {
    const now = new Date();
    const serverTimeOffset = 0;
    
    const serverTime = new Date(now.getTime() + serverTimeOffset);
    const formattedTime = serverTime.toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    const serverTimeElement = document.getElementById('serverTime');
    if (serverTimeElement) {
        serverTimeElement.textContent = formattedTime;
    }
}
</script>