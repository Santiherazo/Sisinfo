<?php
if (!defined('access') || access !== 'install') exit;

if (session_status() === PHP_SESSION_NONE) session_start();

$logger = new ErrorLogger();

try {
    $db = new dB(
        $_SESSION['install_sql_host'] ?? null,
        $_SESSION['install_sql_port'] ?? null,
        $_SESSION['install_sql_db1'] ?? null,
        $_SESSION['install_sql_user'] ?? null,
        $_SESSION['install_sql_pass'] ?? null
    );
    if ($db->dead) {
        $logger->logDatabaseError('Error al conectarse a la base de datos.');
        handleCriticalError();
    }
} catch (Throwable $e) {
    $logger->logPhpError("Excepción en conexión a la base de datos: " . $e->getMessage());
    handleCriticalError();
}

if (isset($_POST['install_step_3_submit'])) {
    if (!isset($_POST['install_step_3_error'])) {
        $_SESSION['install_cstep']++;
        session_regenerate_id(true);
        header('Location: install.php');
        exit;
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-6 rounded text-sm">
                No se puede continuar. Se detectaron errores en la creación de tablas.
              </div>';
    }
}

if (empty($install['sql_list']) || !is_array($install['sql_list'])) {
    $logger->logPhpError("Lista de tablas SQL no definida o inválida.");
    handleCriticalError();
}

echo '<h3 class="text-2xl font-bold mb-6 text-gray-800">Creación de Tablas</h3>';
echo '<div class="space-y-3 mb-8">';

$db->query("SET FOREIGN_KEY_CHECKS = 0");
$error = false;

// ELIMINAR TODAS LAS TABLAS EXISTENTES ANTES DE CREARLAS NUEVAS
if ($_GET['force'] ?? null === '1') {
    echo '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 mb-4 rounded text-sm">
            <strong>Modo forzado:</strong> Eliminando todas las tablas existentes...
          </div>';
    
    // Obtener todas las tablas del sistema
    $tables = $db->query_fetch("SHOW TABLES");
    $droppedTables = 0;
    
    foreach ($tables as $table) {
        $tableName = current($table); // Obtener el nombre de la tabla
        if ($db->query("DROP TABLE IF EXISTS `$tableName`")) {
            $droppedTables++;
            echo panel('info', $tableName, 'Tabla eliminada');
        } else {
            echo panel('error', $tableName, 'Error al eliminar tabla');
            $error = true;
        }
    }
    
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded text-sm">
            <strong>Limpieza completada:</strong> ' . $droppedTables . ' tablas eliminadas.
          </div>';
}

foreach ($install['sql_list'] as $sqlFileName => $sqlTableName) {
    $filename = basename($sqlFileName) . '.txt';
    $sqlFilePath = realpath(__DIR__ . '/../sql/' . $filename);
    $displayName = htmlspecialchars($sqlTableName);

    if (!$sqlFilePath || !file_exists($sqlFilePath)) {
        echo panel('error', $displayName, 'Archivo requerido no encontrado.');
        $logger->logPhpError("Archivo faltante: " . ($sqlFilePath ?: 'Ruta inválida'));
        $error = true;
        continue;
    }

    if (!is_readable($sqlFilePath)) {
        echo panel('error', $displayName, 'No se puede leer el archivo.');
        $logger->logPhpError("Archivo no legible: $sqlFilePath");
        $error = true;
        continue;
    }

    $sqlContent = file_get_contents($sqlFilePath);
    if (!$sqlContent) {
        echo panel('warning', $displayName, 'El archivo está vacío.');
        $logger->logPhpError("Archivo vacío: $sqlFilePath");
        continue;
    }

    $query = str_replace('{TABLE_NAME}', $sqlTableName, $sqlContent);

    // Verificar si la tabla existe (en caso de no usar modo forzado)
    $tableExists = $db->query_fetch_single(
        "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
        [$sqlTableName]
    );

    if (!$tableExists || $tableExists['count'] == 0) {
        $queries = array_filter(array_map('trim', explode(';', $query)));
        $success = true;
        foreach ($queries as $q) {
            if (!empty($q) && !$db->query($q)) {
                echo panel('error', $displayName, 'Error al crear la tabla.');
                $logger->logDatabaseError("Error ejecutando consulta para '$sqlTableName'.");
                $success = false;
                $error = true;
                break;
            }
        }
        if ($success) echo panel('success', $displayName, 'Tabla creada correctamente');
    } else {
        echo panel('info', $displayName, 'La tabla ya existe');
    }
}

$db->query("SET FOREIGN_KEY_CHECKS = 1");
echo '</div>';

echo '<form method="post" class="space-y-6">';
if ($error) echo '<input type="hidden" name="install_step_3_error" value="1"/>';

echo '<div class="flex flex-wrap items-center justify-between gap-4">';
echo '<a href="' . htmlspecialchars(__INSTALL_URL__) . 'install.php" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded font-medium transition">← Revisar de nuevo</a>';
echo '<button type="submit" name="install_step_3_submit" value="continue" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold transition">Continuar</button>';
echo '<a href="' . htmlspecialchars(__INSTALL_URL__) . 'install.php?force=1" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-medium transition">Eliminar y Crear de Nuevo</a>';
echo '</div>';
echo '</form>';

function panel($type, $title, $msg) {
    $colors = [
        'success' => ['bg-green-100', 'text-green-700'],
        'error'   => ['bg-red-100', 'text-red-700'],
        'warning' => ['bg-yellow-100', 'text-yellow-800'],
        'info'    => ['bg-gray-100', 'text-gray-700'],
    ];
    [$bg, $text] = $colors[$type] ?? $colors['info'];
    return "<div class=\"flex items-center justify-between px-4 py-3 $bg $text rounded shadow\">
                <span>$title</span><span class=\"font-medium text-sm\">$msg</span></div>";
}