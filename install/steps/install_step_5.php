<?php
if (!defined('access') || access !== 'install') exit;
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

echo "<h3 class='text-3xl font-bold text-center text-gray-800 mb-6'>Configuración del Sitio Web</h3>";

function sanitizeText($text) {
    if (!is_string($text)) {
        logPhpError("Texto inválido en sanitizeText.");
        handleCriticalError();
    }
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

function isValidUrl($url) {
    return is_string($url) && filter_var($url, FILTER_VALIDATE_URL);
}

function validateSessionVars(array $vars): bool {
    foreach ($vars as $var) {
        if (!isset($_SESSION[$var])) {
            logPhpError("Falta variable de sesión: $var");
            return false;
        }
        if ($var !== 'install_sql_pass' && empty($_SESSION[$var])) {
            logPhpError("Variable de sesión vacía: $var");
            return false;
        }
    }
    return true;
}

function handleFrontendError(string $message) {
    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'><p>$message</p></div>";
}

if (isset($_POST['install_step_5_submit'])) {
    try {
        validateSessionVars([
            'install_sql_host',
            'install_sql_port',
            'install_sql_user',
            'install_sql_pass',
            'install_sql_db1'
        ]);

        $websiteTitle = sanitizeText($_POST['website_title'] ?? '');
        $websiteName = sanitizeText($_POST['website_name'] ?? '');
        $websiteDescription = trim($_POST['website_description'] ?? '');
        $websiteKeywords = sanitizeText($_POST['website_keywords'] ?? '');
        $websiteForum = trim($_POST['website_forum'] ?? '');
        $facebook = trim($_POST['facebook'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $discord = trim($_POST['discord'] ?? '');

        if (!$websiteTitle) return handleFrontendError('El título del sitio es obligatorio.');
        if (!$websiteDescription) return handleFrontendError('La descripción del sitio es obligatoria.');
        if (!$websiteKeywords) return handleFrontendError('Las palabras clave del sitio son obligatorias.');

        $urls = [
            'Enlace al foro' => $websiteForum,
            'Facebook' => $facebook,
            'Instagram' => $instagram,
            'Discord' => $discord
        ];

        foreach ($urls as $label => $url) {
            if (!empty($url) && !isValidUrl($url)) return handleFrontendError("El campo '$label' contiene una URL inválida.");
        }

        $configPath = __PATH_CONFIGS__ . 'webengine.json';
        $existing = [];

        if (file_exists($configPath)) {
            $jsonContent = file_get_contents($configPath);
            if ($jsonContent === false) {
                logPhpError("No se pudo leer el archivo: $configPath");
                handleCriticalError();
            }

            $decoded = json_decode($jsonContent, true);
            if (!is_array($decoded)) {
                logPhpError("Archivo JSON mal formado: $configPath");
                handleCriticalError();
            }

            $existing = $decoded;
        }

        $siteConfig = [
            'website_title' => $websiteTitle,
            'website_name' => $websiteName,
            'website_meta_description' => $websiteDescription,
            'website_meta_keywords' => $websiteKeywords,
            'website_forum_link' => $websiteForum,
            'social_link_facebook' => $facebook,
            'social_link_instagram' => $instagram,
            'social_link_discord' => $discord,
            'webengine_cms_installed' => true
        ];

        $dbVars = [
            'SQL_DB_HOST' => $_SESSION['install_sql_host'],
            'SQL_DB_NAME' => $_SESSION['install_sql_db1'],
            'SQL_DB_USER' => $_SESSION['install_sql_user'],
            'SQL_DB_PASS' => $_SESSION['install_sql_pass'],
            'SQL_DB_PORT' => $_SESSION['install_sql_port']
        ];

        $configData = array_merge($existing, $siteConfig, $dbVars);

        $jsonData = json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($jsonData === false) {
            logPhpError("Error al codificar JSON");
            handleCriticalError();
        }

        if (!file_put_contents($configPath, $jsonData)) {
            logPhpError("Error al guardar archivo: $configPath");
            handleCriticalError();
        }

        @chmod($configPath, 0640);

        $_SESSION = [];
        session_destroy();

        header('Location: ' . __BASE_URL__);
        exit;
    } catch (Throwable $e) {
        logPhpError("Excepción atrapada: " . $e->getMessage());
        handleCriticalError();
    }
}
?>

<form method="post" class="space-y-6 max-w-2xl mx-auto">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre del sitio</label>
        <input type="text" name="website_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Título del sitio</label>
        <input type="text" name="website_title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Descripción del sitio</label>
        <textarea name="website_description" required rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Palabras clave (SEO)</label>
        <input type="text" name="website_keywords" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Enlace al foro (opcional)</label>
        <input type="url" name="website_forum" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Facebook (opcional)</label>
        <input type="url" name="facebook" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Instagram (opcional)</label>
        <input type="url" name="instagram" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Discord (opcional)</label>
        <input type="url" name="discord" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div class="pt-4">
        <button type="submit" name="install_step_5_submit" value="1" class="w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow hover:bg-green-700 transition">
            Finalizar Instalación
        </button>
    </div>
</form>
