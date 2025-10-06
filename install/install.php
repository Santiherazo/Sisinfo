<?php
ob_start();

$nonce = base64_encode(random_bytes(32));

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com 'nonce-$nonce'; style-src 'self' 'unsafe-inline';");
header('Referrer-Policy: no-referrer');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');

define('access', 'install');
define('__PATH_LOGS__', realpath(__DIR__ . '/../includes/logs') . '/');

require_once __DIR__ . '/loader.php';
require_once __DIR__ . '/../includes/classes/class.Errorlogger.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    $logger = new ErrorLogger();
} catch (Throwable $e) {
    echo '<pre>Error al instanciar ErrorLogger: ' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalador SISINFO CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script nonce="<?php echo $nonce; ?>">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d4ed8',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 min-h-screen flex flex-col">

<header class="bg-white shadow-sm py-6 px-8">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-800">Asistente de Instalación</h1>
            <p class="text-sm text-gray-500 mt-1">Versión <?php echo htmlspecialchars(INSTALLER_VERSION); ?></p>
        </div>
        <a href="#" class="mt-4 md:mt-0 text-sm text-blue-600 hover:text-blue-800 transition">
            Sitio oficial &rarr;
        </a>
    </div>
</header>

<main class="flex-grow py-10 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
        <section class="lg:col-span-3 bg-white p-8 rounded-2xl shadow-lg border border-gray-300 transition">
            <?php
            try {
                if (!isset($_SESSION['install_cstep'])) {
                    throw new Exception('La sesión no contiene install_cstep');
                }

                $stepIndex = (int) $_SESSION['install_cstep'];

                if (!isset($install) || !isset($install['step_list'])) {
                    throw new Exception('La variable $install o step_list no está definida');
                }

                if (!array_key_exists($stepIndex, $install['step_list'])) {
                    throw new Exception('El índice del paso no existe en step_list');
                }

                $fileName = basename($install['step_list'][$stepIndex][0]);

                $allowedFiles = [
                    'install_intro.php',
                    'install_step_1.php',
                    'install_step_2.php',
                    'install_step_3.php',
                    'install_step_4.php',
                    'install_step_5.php'
                ];

                $stepPath = __DIR__ . '/steps/' . $fileName;

                if (!in_array($fileName, $allowedFiles, true) || !file_exists($stepPath) || !is_readable($stepPath)) {
                    throw new Exception("Archivo inválido o no accesible: $fileName");
                }

                include_once $stepPath;
            } catch (Throwable $ex) {
                $logger->logException($ex, 'INSTALL');
                http_response_code(500);
                echo '<div class="bg-red-50 border border-red-400 text-red-700 px-5 py-4 rounded-lg shadow-sm" role="alert">';
                echo '<strong class="font-bold">Error:</strong> ' . htmlspecialchars($ex->getMessage());
                echo '</div>';
                exit;
            }
            ?>
        </section>

        <aside class="bg-white p-6 rounded-2xl shadow-md border border-gray-300 h-fit sticky top-6 transition">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Progreso del Instalador
            </h2>
            <div class="space-y-2 text-sm">
                <?php stepListSidebar(); ?>
            </div>
        </aside>
    </div>
</main>

<footer class="bg-white border-t border-gray-300 text-center py-6 mt-12 text-sm text-gray-500">
    &copy; <?php echo date("Y"); ?> SISINFO CMS. Todos los derechos reservados.
</footer>

</body>
</html>