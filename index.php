<?php
define('access', $_REQUEST['access'] ?? 'index');

header('Content-Type: text/html; charset=UTF-8');

$path = __DIR__ . '/includes/webengine.php';
$errorTpl = __DIR__ . '/includes/error.html';
$logPath = __DIR__ . '/includes/logs/php_errors.log';

if (!file_exists($logPath)) {
    file_put_contents($logPath, '');
}

try {
    if (!is_readable($path)) {
        throw new Exception('Archivo requerido no disponible o ilegible.');
    }

    require_once $path;

} catch (Exception $e) {
    if (ob_get_length()) ob_clean();

    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $html = is_readable($errorTpl) 
        ? str_replace('{ERROR_MESSAGE}', $msg, file_get_contents($errorTpl)) 
        : "<h1>Error del sistema</h1><p>$msg</p>";

    echo $html;

    error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, $logPath);
    exit;
}