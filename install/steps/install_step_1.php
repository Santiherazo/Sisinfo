<?php
if (!defined('access') || access !== 'install') exit;
ob_start();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$logger = new ErrorLogger();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_step_1_submit'])) {
        $_SESSION['install_cstep']++;
        http_response_code(302);
        header('Location: install.php', true, 302);
        exit;
    }
} catch (Throwable $e) {
    $logger->critical($e->getMessage());
    http_response_code(500);
    echo $logger->render();
    exit;
}

function statusLabel(bool $ok, bool $optional = false): string {
    $text = $ok ? 'Correcto' : ($optional ? 'Opcional' : 'Corregir');
    $color = $ok ? 'green' : ($optional ? 'yellow' : 'red');
    return "<span class=\"px-3 py-1 text-sm font-semibold rounded-full bg-{$color}-100 text-{$color}-800 shadow-inner\">{$text}</span>";
}

$checks = [
    ["PHP ≥ 8.1", version_compare(PHP_VERSION, '8.1', '>='), "(PHP " . PHP_VERSION . ")"],
    ["OpenSSL", extension_loaded('openssl')],
    ["cURL", extension_loaded('curl')],
    ["GD", extension_loaded('gd')],
    ["XML", extension_loaded('xml')],
    ["PDO", extension_loaded('pdo')],
    ["JSON", extension_loaded('json')],
];

$pdoExtras = '';
if (extension_loaded('pdo')) {
    $pdoExtras .= '<li class="flex justify-between items-center px-4 py-3 border-b">' .
        '<span class="truncate">PDO dblib (Linux)</span>' .
        statusLabel(extension_loaded('pdo_dblib'), true) . '</li>';
    $pdoExtras .= '<li class="flex justify-between items-center px-4 py-3 border-b">' .
        '<span class="truncate">PDO sqlsrv (Windows)</span>' .
        statusLabel(extension_loaded('pdo_sqlsrv'), true) . '</li>';
}

$htaccessStatus = false;
if (defined('__BASE_URL__') && filter_var(__BASE_URL__, FILTER_VALIDATE_URL)) {
    $headers = @get_headers(__BASE_URL__ . 'includes/config/webengine.json', 1);
    $htaccessStatus = is_array($headers) && strpos($headers[0], '403') !== false;
}

$writablePaths = $writablePaths ?? [];
$permItems = '';
foreach ($writablePaths as $path) {
    $full = __PATH_INCLUDES__ . $path;
    $exists = file_exists($full);
    $writable = $exists && is_writable($full);
    $label = !$exists ? statusLabel(false) : statusLabel($writable);
    $permItems .= '<li class="flex justify-between items-center px-4 py-3 border-b">' .
        '<span class="truncate">' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '</span>' . $label . '</li>';
}

echo <<<HTML
<div class="w-full space-y-12 px-6 py-8 text-gray-800">

    <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 text-lg font-semibold text-gray-700 rounded-t-2xl">
            Requisitos del Servidor Web
        </div>
        <ul class="divide-y divide-gray-100">
HTML;

foreach ($checks as $c) {
    echo '<li class="flex justify-between items-center px-4 py-3">' .
        '<span class="truncate font-medium">' . htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8') . ' ' . ($c[2] ?? '') . '</span>' .
        statusLabel($c[1]) . '</li>';
}

echo $pdoExtras;

echo '<li class="flex justify-between items-center px-4 py-3">' .
     '<span class="truncate">Protección de directorios (.htaccess)</span>' .
     statusLabel($htaccessStatus) . '</li>';

echo <<<HTML
        </ul>
    </div>

    <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 text-lg font-semibold text-gray-700 rounded-t-2xl">
            Permisos de Escritura
        </div>
        <ul class="divide-y divide-gray-100">
            {$permItems}
        </ul>
    </div>

    <div class="text-center pt-8">
        <p class="text-red-600 font-semibold text-base mb-4">
            Por favor corrige los errores críticos antes de continuar.
        </p>
        <form method="post" class="flex flex-wrap justify-center gap-4">
            <button type="button" onclick="location.reload()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold shadow-md transition">
                Revisar de nuevo
            </button>
            <button type="submit" name="install_step_1_submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold shadow-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M6 4l8 6-8 6V4z"/></svg>
                Continuar
            </button>
        </form>
    </div>
</div>
HTML;