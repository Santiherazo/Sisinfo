<?php
define('__APP_SECRET__', 'tu_clave_secreta_aqui');

if (!file_exists(__PATH_UPLOADS__)) {
    mkdir(__PATH_UPLOADS__, 0777, true);
}

function getDiskSpace() {
    $totalSpace = disk_total_space(__PATH_UPLOADS__);
    $freeSpace = disk_free_space(__PATH_UPLOADS__);
    $usedSpace = $totalSpace - $freeSpace;
    
    return [
        'total' => $totalSpace,
        'free' => $freeSpace,
        'used' => $usedSpace
    ];
}

function getFileIconInfo($extension) {
    $ext = strtolower($extension);
    $icon = 'file';
    $color = 'text-gray-600';
    $bgColor = 'bg-gray-100';
    
    if (in_array($ext, ['pdf'])) {
        $icon = 'file-text';
        $color = 'text-red-600';
        $bgColor = 'bg-red-100';
    } else if (in_array($ext, ['doc', 'docx', 'txt', 'rtf'])) {
        $icon = 'file-text';
        $color = 'text-blue-600';
        $bgColor = 'bg-blue-100';
    } else if (in_array($ext, ['xls', 'xlsx', 'csv'])) {
        $icon = 'file-spreadsheet';
        $color = 'text-green-600';
        $bgColor = 'bg-green-100';
    } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])) {
        $icon = 'file-image';
        $color = 'text-blue-600';
        $bgColor = 'bg-blue-100';
    } else if (in_array($ext, ['mp4', 'mov', 'avi', 'wmv', 'mkv', 'flv'])) {
        $icon = 'file-video';
        $color = 'text-orange-600';
        $bgColor = 'bg-orange-100';
    } else if (in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])) {
        $icon = 'file-audio';
        $color = 'text-purple-600';
        $bgColor = 'bg-purple-100';
    } else if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
        $icon = 'archive';
        $color = 'text-purple-600';
        $bgColor = 'bg-purple-100';
    }
    
    return ['icon' => $icon, 'color' => $color, 'bgColor' => $bgColor];
}

$uploadManager = new UploadManager();
$currentDir = isset($_GET['path']) ? $_GET['path'] : __PATH_UPLOADS__;
if (!is_dir($currentDir)) {
    $currentDir = __PATH_UPLOADS__;
}

$directories = [];
$files = [];

if (is_dir($currentDir)) {
    $allItems = array_diff(scandir($currentDir), ['.', '..', '.DS_Store']);
    foreach ($allItems as $item) {
        $itemPath = $currentDir . '/' . $item;
        if (is_dir($itemPath)) {
            $itemCount = count(UploadManager::listAllContents($itemPath));
            $modifiedTime = filemtime($itemPath);
            $formattedDate = date('d M Y', $modifiedTime);
            $directories[] = [
                'name' => $item,
                'item_count' => $itemCount,
                'owner' => 'Sistema',
                'modified' => $formattedDate
            ];
        } else {
            $fileSize = filesize($itemPath);
            $fileExt = pathinfo($item, PATHINFO_EXTENSION);
            $modifiedTime = filemtime($itemPath);
            $formattedDate = date('d M Y', $modifiedTime);
            $formattedSize = $fileSize > 1024 * 1024 
                ? round($fileSize / (1024 * 1024), 1) . ' MB' 
                : round($fileSize / 1024, 1) . ' KB';
            $files[] = [
                'name' => $item,
                'size' => $formattedSize,
                'extension' => $fileExt,
                'owner' => 'Sistema',
                'modified' => $formattedDate,
                'full_path' => $itemPath
            ];
        }
    }
}

$folderCount = count($directories);
$fileCount = count($files);
$usedSpace = UploadManager::getDirectorySize($currentDir);
$usedSpaceFormatted = round($usedSpace / (1024 * 1024 * 1024), 1) . ' GB';

$recentFiles = 0;
foreach ($files as $file) {
    $filePath = $currentDir . '/' . $file['name'];
    if (filemtime($filePath) > (time() - 86400)) {
        $recentFiles++;
    }
}

$diskSpace = getDiskSpace();
$diskTotalFormatted = round($diskSpace['total'] / (1024 * 1024 * 1024), 1) . ' GB';
$diskUsedFormatted = round($diskSpace['used'] / (1024 * 1024 * 1024), 1) . ' GB';
$diskFreeFormatted = round($diskSpace['free'] / (1024 * 1024 * 1024), 1) . ' GB';
$diskUsagePercentage = round(($diskSpace['used'] / $diskSpace['total']) * 100, 1);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'create_folder':
                if (!empty($_POST['folder_name']) && !empty($_POST['path'])) {
                    $newPath = $_POST['path'] . '/' . $_POST['folder_name'];
                    if (UploadManager::createDirectory($newPath)) {
                        $message = 'Carpeta creada correctamente';
                    } else {
                        $error = 'Error al crear la carpeta';
                    }
                }
                break;
                
            case 'upload_files':
                if (!empty($_FILES['files']) && !empty($_POST['path'])) {
                    $uploadedCount = 0;
                    $errors = [];
                    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) {
                            $errors[] = "Error al subir " . $_FILES['files']['name'][$key];
                            continue;
                        }
                        
                        $file = [
                            'name' => $_FILES['files']['name'][$key],
                            'type' => $_FILES['files']['type'][$key],
                            'tmp_name' => $tmp_name,
                            'error' => $_FILES['files']['error'][$key],
                            'size' => $_FILES['files']['size'][$key]
                        ];
                        
                        if ($uploadManager->upload($file, 'user_id', str_replace(__PATH_UPLOADS__, '', $_POST['path']))) {
                            $uploadedCount++;
                        } else {
                            $errors[] = "Error al subir " . $_FILES['files']['name'][$key];
                        }
                    }
                    
                    if ($uploadedCount > 0) {
                        $message = "Se subieron $uploadedCount archivos correctamente";
                        if (!empty($errors)) {
                            $error = implode(", ", $errors);
                        }
                    } else {
                        $error = "Error al subir archivos: " . implode(", ", $errors);
                    }
                }
                break;
                
            case 'rename_item':
                if (!empty($_POST['old_name']) && !empty($_POST['new_name']) && !empty($_POST['path']) && !empty($_POST['item_type'])) {
                    $oldPath = $_POST['path'] . '/' . $_POST['old_name'];
                    $newPath = $_POST['path'] . '/' . $_POST['new_name'];
                    
                    if ($_POST['item_type'] === 'folder') {
                        if (UploadManager::renameDirectory($oldPath, $newPath)) {
                            $message = 'Carpeta renombrada correctamente';
                        } else {
                            $error = 'Error al renombrar la carpeta';
                        }
                    } else {
                        if (file_exists($oldPath)) {
                            if (rename($oldPath, $newPath)) {
                                $message = 'Archivo renombrado correctamente';
                            } else {
                                $error = 'Error al renombrar el archivo';
                            }
                        } else {
                            $error = 'El archivo no existe';
                        }
                    }
                }
                break;
                
            case 'delete_item':
                if (!empty($_POST['item_name']) && !empty($_POST['path']) && !empty($_POST['item_type'])) {
                    $path = $_POST['path'] . '/' . $_POST['item_name'];
                    
                    if ($_POST['item_type'] === 'folder') {
                        if (UploadManager::deleteDirectory($path)) {
                            $message = 'Carpeta eliminada correctamente';
                        } else {
                            $error = 'Error al eliminar la carpeta';
                        }
                    } else {
                        if ($uploadManager->delete($path)) {
                            $message = 'Archivo eliminado correctamente';
                        } else {
                            $error = 'Error al eliminar el archivo';
                        }
                    }
                }
                break;
                
            case 'move_item':
                if (!empty($_POST['item_name']) && !empty($_POST['path']) && !empty($_POST['item_type']) && !empty($_POST['target_path'])) {
                    $source = $_POST['path'] . '/' . $_POST['item_name'];
                    $destination = $_POST['target_path'] . '/' . $_POST['item_name'];
                    
                    if ($_POST['item_type'] === 'folder') {
                        if (UploadManager::moveDirectory($source, $destination)) {
                            $message = 'Carpeta movida correctamente';
                        } else {
                            $error = 'Error al mover la carpeta';
                        }
                    } else {
                        if (file_exists($source)) {
                            if (rename($source, $destination)) {
                                $message = 'Archivo movido correctamente';
                            } else {
                                $error = 'Error al mover el archivo';
                            }
                        } else {
                            $error = 'El archivo no existe';
                        }
                    }
                }
                break;
                
            case 'get_directories':
                $excludePath = isset($_POST['exclude']) ? $_POST['exclude'] : '';
                $allDirectories = [];
                $rootDirs = UploadManager::listDirectories(__PATH_UPLOADS__);
                
                foreach ($rootDirs as $dir) {
                    $dirPath = __PATH_UPLOADS__ . '/' . $dir;
                    if ($dirPath !== $excludePath) {
                        $allDirectories[] = ['name' => $dir, 'path' => $dirPath];
                        
                        $subDirs = UploadManager::listDirectories($dirPath);
                        foreach ($subDirs as $subDir) {
                            $subDirPath = $dirPath . '/' . $subDir;
                            if ($subDirPath !== $excludePath) {
                                $allDirectories[] = ['name' => $dir . '/' . $subDir, 'path' => $subDirPath];
                            }
                        }
                    }
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'directories' => $allDirectories]);
                exit;
                
            case 'get_content':
                $path = isset($_POST['path']) ? $_POST['path'] : __PATH_UPLOADS__;
                if (!is_dir($path)) {
                    $path = __PATH_UPLOADS__;
                }
                
                $directories = [];
                $files = [];
                
                if (is_dir($path)) {
                    $allItems = array_diff(scandir($path), ['.', '..', '.DS_Store']);
                    foreach ($allItems as $item) {
                        $itemPath = $path . '/' . $item;
                        if (is_dir($itemPath)) {
                            $itemCount = count(UploadManager::listAllContents($itemPath));
                            $modifiedTime = filemtime($itemPath);
                            $formattedDate = date('d M Y', $modifiedTime);
                            $directories[] = [
                                'name' => $item,
                                'item_count' => $itemCount,
                                'owner' => 'Sistema',
                                'modified' => $formattedDate
                            ];
                        } else {
                            $fileSize = filesize($itemPath);
                            $fileExt = pathinfo($item, PATHINFO_EXTENSION);
                            $modifiedTime = filemtime($itemPath);
                            $formattedDate = date('d M Y', $modifiedTime);
                            $formattedSize = $fileSize > 1024 * 1024 
                                ? round($fileSize / (1024 * 1024), 1) . ' MB' 
                                : round($fileSize / 1024, 1) . ' KB';
                            $files[] = [
                                'name' => $item,
                                'size' => $formattedSize,
                                'extension' => $fileExt,
                                'owner' => 'Sistema',
                                'modified' => $formattedDate,
                                'full_path' => $itemPath
                            ];
                        }
                    }
                }
                
                $folderCount = count($directories);
                $fileCount = count($files);
                $usedSpace = UploadManager::getDirectorySize($path);
                $usedSpaceFormatted = round($usedSpace / (1024 * 1024 * 1024), 1) . ' GB';
                
                $recentFiles = 0;
                foreach ($files as $file) {
                    $filePath = $path . '/' . $file['name'];
                    if (filemtime($filePath) > (time() - 86400)) {
                        $recentFiles++;
                    }
                }
                
                $diskSpace = getDiskSpace();
                $diskTotalFormatted = round($diskSpace['total'] / (1024 * 1024 * 1024), 1) . ' GB';
                $diskUsedFormatted = round($diskSpace['used'] / (1024 * 1024 * 1024), 1) . ' GB';
                $diskFreeFormatted = round($diskSpace['free'] / (1024 * 1024 * 1024), 1) . ' GB';
                $diskUsagePercentage = round(($diskSpace['used'] / $diskSpace['total']) * 100, 1);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'directories' => $directories,
                    'files' => $files,
                    'folder_count' => $folderCount,
                    'file_count' => $fileCount,
                    'used_space' => $usedSpaceFormatted,
                    'recent_files' => $recentFiles,
                    'disk_used' => $diskUsedFormatted,
                    'disk_total' => $diskTotalFormatted,
                    'disk_free' => $diskFreeFormatted,
                    'disk_usage_percentage' => $diskUsagePercentage,
                    'current_path' => $path
                ]);
                exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => empty($error), 'message' => $message, 'error' => $error]);
        exit;
    } else {
        header('Location: ?path=' . urlencode($currentDir));
        exit;
    }
}
?>
<div id="files-module" class="module-content">
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
        <div id="message-container" class="hidden mb-4">
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden"></div>
            <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden"></div>
        </div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Gestor de Archivos</h2>
                <div class="flex items-center gap-2 text-sm text-slate-600" id="breadcrumb">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Inicio</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="showCreateFolderModal()" class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg font-medium">
                    <i data-lucide="folder-plus" class="w-5 h-5"></i>
                    Nueva Carpeta
                </button>
                <button onclick="showUploadModal()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg font-medium">
                    <i data-lucide="upload" class="w-5 h-5"></i>
                    Subir Archivos
                </button>
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-4 mb-8">
            <div class="glassmorphism rounded-xl p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="folder" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Carpetas</p>
                        <p id="folder-count" class="text-xl font-bold text-slate-900"><?php echo $folderCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="glassmorphism rounded-xl p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="file" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Archivos</p>
                        <p id="file-count" class="text-xl font-bold text-slate-900"><?php echo $fileCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="glassmorphism rounded-xl p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="hard-drive" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Espacio Usado</p>
                        <p id="used-space" class="text-xl font-bold text-slate-900"><?php echo $diskUsedFormatted; ?></p>
                        <p class="text-xs text-slate-500 mt-1"><?php echo $diskUsagePercentage; ?>% del disco (<?php echo $diskTotalFormatted; ?> total)</p>
                    </div>
                </div>
            </div>
            <div class="glassmorphism rounded-xl p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Recientes</p>
                        <p id="recent-files" class="text-xl font-bold text-slate-900"><?php echo $recentFiles; ?></p>
                        <p class="text-xs text-slate-500 mt-1">Últimas 24 horas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div class="flex flex-1 gap-3">
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" id="searchInput" placeholder="Buscar archivos y carpetas..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <select id="filterSelect" class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="all">Todos los tipos</option>
                    <option value="document">Documentos</option>
                    <option value="image">Imágenes</option>
                    <option value="video">Videos</option>
                    <option value="pdf">PDFs</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button id="gridViewBtn" class="flex items-center gap-2 px-4 py-3 bg-blue-500 text-white border border-white/20 rounded-xl hover:bg-blue-600 transition-all">
                    <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                </button>
                <button id="listViewBtn" class="flex items-center gap-2 px-4 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div id="file-grid" class="file-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php if (empty($directories) && empty($files)): ?>
                <div class="col-span-full text-center py-12">
                    <i data-lucide="folder-open" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay archivos o carpetas</p>
                </div>
            <?php else: ?>
                <?php if ($currentDir !== __PATH_UPLOADS__): ?>
                    <div class="folder-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer" onclick="navigateToParentDirectory()">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-gray-100 rounded-lg">
                                <i data-lucide="arrow-left" class="w-6 h-6 text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-slate-900">Volver</h4>
                                <p class="text-xs text-slate-500">Directorio anterior</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php foreach ($directories as $dir): ?>
                    <div class="folder-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer" data-name="<?php echo $dir['name']; ?>" data-type="folder" ondblclick="openFolder('<?php echo $dir['name']; ?>')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i data-lucide="folder" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-slate-900"><?php echo $dir['name']; ?></h4>
                                <p class="text-xs text-slate-500"><?php echo $dir['item_count']; ?> elementos</p>
                            </div>
                            <div class="dropdown relative">
                                <button onclick="event.stopPropagation(); toggleDropdown(this)" class="p-1 rounded hover:bg-gray-100">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                                <div class="dropdown-menu absolute right-0 z-10 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openFolder('<?php echo $dir['name']; ?>')">Abrir</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="renameItem('<?php echo $dir['name']; ?>', 'folder')">Renombrar</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="moveItem('<?php echo $dir['name']; ?>', 'folder')">Mover</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="deleteItem('<?php echo $dir['name']; ?>', 'folder')">Eliminar</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span><?php echo $dir['owner']; ?></span>
                            <span><?php echo $dir['modified']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach ($files as $file): ?>
                    <?php
                    $iconInfo = getFileIconInfo($file['extension']);
                    ?>
                    <div class="file-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer" data-name="<?php echo $file['name']; ?>" data-type="file" data-extension="<?php echo $file['extension']; ?>" ondblclick="previewFile('<?php echo $file['name']; ?>', '<?php echo $file['extension']; ?>', '<?php echo $file['full_path']; ?>')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 <?php echo $iconInfo['bgColor']; ?> rounded-lg">
                                <i data-lucide="<?php echo $iconInfo['icon']; ?>" class="w-6 h-6 <?php echo $iconInfo['color']; ?>"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-slate-900"><?php echo $file['name']; ?></h4>
                                <p class="text-xs text-slate-500"><?php echo $file['size']; ?></p>
                            </div>
                            <div class="dropdown relative">
                                <button onclick="event.stopPropagation(); toggleDropdown(this)" class="p-1 rounded hover:bg-gray-100">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                                <div class="dropdown-menu absolute right-0 z-10 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="previewFile('<?php echo $file['name']; ?>', '<?php echo $file['extension']; ?>', '<?php echo $file['full_path']; ?>')">Vista Previa</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="downloadFile('<?php echo $file['full_path']; ?>', '<?php echo $file['name']; ?>')">Descargar</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="renameItem('<?php echo $file['name']; ?>', 'file')">Renombrar</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="moveItem('<?php echo $file['name']; ?>', 'file')">Mover</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="deleteItem('<?php echo $file['name']; ?>', 'file')">Eliminar</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span><?php echo $file['owner']; ?></span>
                            <span><?php echo $file['modified']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div id="file-list" class="file-list hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Nombre</th>
                        <th class="text-left py-3">Tamaño</th>
                        <th class="text-left py-3">Modificado</th>
                        <th class="text-left py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody id="list-view-content">
                    <?php if ($currentDir !== __PATH_UPLOADS__): ?>
                        <tr class="folder-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer" onclick="navigateToParentDirectory()">
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-gray-100 rounded-lg">
                                        <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
                                    </div>
                                    <span class="font-medium">Volver</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-500">-</td>
                            <td class="py-3 text-sm text-gray-500">-</td>
                            <td class="py-3 text-sm text-gray-500">-</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($directories as $dir): ?>
                        <tr class="folder-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer" data-name="<?php echo $dir['name']; ?>" data-type="folder" ondblclick="openFolder('<?php echo $dir['name']; ?>')">
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <i data-lucide="folder" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <span class="font-medium"><?php echo $dir['name']; ?></span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-500">-</td>
                            <td class="py-3 text-sm text-gray-500"><?php echo $dir['modified']; ?></td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <button onclick="openFolder('<?php echo $dir['name']; ?>')" class="p-1 text-blue-600 hover:bg-blue-100 rounded">
                                        <i data-lucide="folder-open" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="renameItem('<?php echo $dir['name']; ?>', 'folder')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="moveItem('<?php echo $dir['name']; ?>', 'folder')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                                        <i data-lucide="move" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteItem('<?php echo $dir['name']; ?>', 'folder')" class="p-1 text-red-600 hover:bg-red-100 rounded">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php foreach ($files as $file): ?>
                        <?php
                        $iconInfo = getFileIconInfo($file['extension']);
                        ?>
                        <tr class="file-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer" data-name="<?php echo $file['name']; ?>" data-type="file" data-extension="<?php echo $file['extension']; ?>" ondblclick="previewFile('<?php echo $file['name']; ?>', '<?php echo $file['extension']; ?>', '<?php echo $file['full_path']; ?>')">
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 <?php echo $iconInfo['bgColor']; ?> rounded-lg">
                                        <i data-lucide="<?php echo $iconInfo['icon']; ?>" class="w-4 h-4 <?php echo $iconInfo['color']; ?>"></i>
                                    </div>
                                    <span class="font-medium"><?php echo $file['name']; ?></span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-500"><?php echo $file['size']; ?></td>
                            <td class="py-3 text-sm text-gray-500"><?php echo $file['modified']; ?></td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <button onclick="previewFile('<?php echo $file['name']; ?>', '<?php echo $file['extension']; ?>', '<?php echo $file['full_path']; ?>')" class="p-1 text-blue-600 hover:bg-blue-100 rounded">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="downloadFile('<?php echo $file['full_path']; ?>', '<?php echo $file['name']; ?>')" class="p-1 text-green-600 hover:bg-green-100 rounded">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="renameItem('<?php echo $file['name']; ?>', 'file')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="moveItem('<?php echo $file['name']; ?>', 'file')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                                        <i data-lucide="move" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteItem('<?php echo $file['name']; ?>', 'file')" class="p-1 text-red-600 hover:bg-red-100 rounded">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="createFolderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Crear Nueva Carpeta</h3>
        <form id="createFolderForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre de la carpeta</label>
                <input type="text" name="folder_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideModal('createFolderModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Crear</button>
            </div>
        </form>
    </div>
</div>
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Subir Archivos</h3>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Seleccionar archivos</label>
                <input type="file" name="files[]" multiple class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideModal('uploadModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Subir</button>
            </div>
        </form>
    </div>
</div>
<div id="renameModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-96">
        <h3 class="text-xl font-bold mb-4" id="renameTitle">Renombrar</h3>
        <form id="renameForm">
            <input type="hidden" name="item_type" id="renameItemType">
            <input type="hidden" name="old_name" id="renameOldName">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nuevo nombre</label>
                <input type="text" name="new_name" id="renameNewName" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideModal('renameModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Renombrar</button>
            </div>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Confirmar eliminación</h3>
        <p class="mb-4" id="deleteMessage">¿Estás seguro de que quieres eliminar este elemento?</p>
        <form id="deleteForm">
            <input type="hidden" name="item_type" id="deleteItemType">
            <input type="hidden" name="item_name" id="deleteItemName">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideModal('deleteModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Eliminar</button>
            </div>
        </form>
    </div>
</div>
<div id="moveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Mover elemento</h3>
        <form id="moveForm">
            <input type="hidden" name="item_type" id="moveItemType">
            <input type="hidden" name="item_name" id="moveItemName">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ruta de destino</label>
                <select name="target_path" id="moveTargetPath" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Seleccionar destino</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideModal('moveModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Mover</button>
            </div>
        </form>
    </div>
</div>
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 w-11/12 max-w-4xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold" id="previewTitle">Vista Previa</h3>
            <button onclick="hideModal('previewModal')" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="preview-content max-h-96 overflow-auto" id="previewContent"></div>
    </div>
</div>
<script>
let __PATH_UPLOADS__ = '<?php echo __PATH_UPLOADS__; ?>';
let currentPath = '<?php echo $currentDir; ?>';
let currentView = 'grid';
let pathHistory = ['<?php echo __PATH_UPLOADS__; ?>'];
let currentHistoryIndex = 0;

function getFileIconInfo(extension) {
    const ext = extension.toLowerCase();
    let icon = 'file';
    let color = 'text-gray-600';
    let bgColor = 'bg-gray-100';
    
    if (['pdf'].includes(ext)) {
        icon = 'file-text';
        color = 'text-red-600';
        bgColor = 'bg-red-100';
    } else if (['doc', 'docx', 'txt', 'rtf'].includes(ext)) {
        icon = 'file-text';
        color = 'text-blue-600';
        bgColor = 'bg-blue-100';
    } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
        icon = 'file-spreadsheet';
        color = 'text-green-600';
        bgColor = 'bg-green-100';
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
        icon = 'file-image';
        color = 'text-blue-600';
        bgColor = 'bg-blue-100';
    } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv', 'flv'].includes(ext)) {
        icon = 'file-video';
        color = 'text-orange-600';
        bgColor = 'bg-orange-100';
    } else if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) {
        icon = 'file-audio';
        color = 'text-purple-600';
        bgColor = 'bg-purple-100';
    } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
        icon = 'archive';
        color = 'text-purple-600';
        bgColor = 'bg-purple-100';
    }
    
    return { icon, color, bgColor };
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    updateBreadcrumb();
    
    document.getElementById('createFolderForm').addEventListener('submit', handleCreateFolder);
    document.getElementById('uploadForm').addEventListener('submit', handleUploadFiles);
    document.getElementById('renameForm').addEventListener('submit', handleRenameItem);
    document.getElementById('deleteForm').addEventListener('submit', handleDeleteItem);
    document.getElementById('moveForm').addEventListener('submit', handleMoveItem);
    
    document.getElementById('searchInput').addEventListener('input', handleSearch);
    document.getElementById('filterSelect').addEventListener('change', handleFilter);
    
    document.getElementById('gridViewBtn').addEventListener('click', () => switchView('grid'));
    document.getElementById('listViewBtn').addEventListener('click', () => switchView('list'));
});

function setupEventListeners() {
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && !e.target.closest('.dropdown-menu')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('hidden');
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
    setTimeout(() => {
        const closeDropdown = (e) => {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.removeEventListener('click', closeDropdown);
            }
        };
        document.addEventListener('click', closeDropdown);
    }, 0);
}

function showCreateFolderModal() {
    document.getElementById('createFolderModal').classList.remove('hidden');
}

function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function showRenameModal() {
    document.getElementById('renameModal').classList.remove('hidden');
}

function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function showMoveModal() {
    loadMoveOptions();
    document.getElementById('moveModal').classList.remove('hidden');
}

function showPreviewModal() {
    document.getElementById('previewModal').classList.remove('hidden');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function loadMoveOptions() {
    const select = document.getElementById('moveTargetPath');
    select.innerHTML = '<option value="">Seleccionar destino</option>';
    
    const rootOption = document.createElement('option');
    rootOption.value = __PATH_UPLOADS__;
    rootOption.textContent = 'Raíz';
    select.appendChild(rootOption);
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_directories&exclude=${encodeURIComponent(currentPath)}`
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al cargar directorios');
        }
    }).then(data => {
        if (data.success) {
            data.directories.forEach(dir => {
                const option = document.createElement('option');
                option.value = dir.path;
                option.textContent = dir.name;
                select.appendChild(option);
            });
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification('Error al cargar directorios', 'error');
    });
}

function renameItem(name, type) {
    document.getElementById('renameItemType').value = type;
    document.getElementById('renameOldName').value = name;
    document.getElementById('renameNewName').value = name;
    document.getElementById('renameTitle').textContent = `Renombrar ${type === 'folder' ? 'carpeta' : 'archivo'}`;
    showRenameModal();
}

function deleteItem(name, type) {
    document.getElementById('deleteItemType').value = type;
    document.getElementById('deleteItemName').value = name;
    document.getElementById('deleteMessage').textContent = `¿Estás seguro de que quieres eliminar este ${type === 'folder' ? 'carpeta' : 'archivo'}?`;
    showDeleteModal();
}

function moveItem(name, type) {
    document.getElementById('moveItemType').value = type;
    document.getElementById('moveItemName').value = name;
    showMoveModal();
}

function openFolder(folderName) {
    const newPath = currentPath + '/' + folderName;
    loadDirectoryContent(newPath);
}

function navigateToParentDirectory() {
    if (currentPath !== __PATH_UPLOADS__) {
        const parentPath = currentPath.substring(0, currentPath.lastIndexOf('/'));
        loadDirectoryContent(parentPath);
    }
}

function loadDirectoryContent(path) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_content&path=${encodeURIComponent(path)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (pathHistory[currentHistoryIndex] !== path) {
                pathHistory = pathHistory.slice(0, currentHistoryIndex + 1);
                pathHistory.push(path);
                currentHistoryIndex = pathHistory.length - 1;
            }
            
            currentPath = data.current_path;
            
            document.getElementById('folder-count').textContent = data.folder_count;
            document.getElementById('file-count').textContent = data.file_count;
            document.getElementById('used-space').textContent = data.disk_used;
            document.getElementById('recent-files').textContent = data.recent_files;
            
            updateBreadcrumb();
            
            renderContent(data.directories, data.files);
            
            showNotification('Directorio cargado correctamente', 'success');
        } else {
            throw new Error(data.error || 'Error al cargar el directorio');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al cargar el directorio', 'error');
    });
}

function renderContent(directories, files) {
    const fileGrid = document.getElementById('file-grid');
    const listViewContent = document.getElementById('list-view-content');
    
    fileGrid.innerHTML = '';
    listViewContent.innerHTML = '';
    
    if (currentPath !== __PATH_UPLOADS__) {
        const backDiv = createBackElement();
        fileGrid.appendChild(backDiv);
        
        const backRow = createBackListElement();
        listViewContent.appendChild(backRow);
    }
    
    directories.forEach(dir => {
        const dirElement = createDirectoryElement(dir);
        fileGrid.appendChild(dirElement);
        
        const dirRow = createDirectoryListElement(dir);
        listViewContent.appendChild(dirRow);
    });
    
    files.forEach(file => {
        const fileElement = createFileElement(file);
        fileGrid.appendChild(fileElement);
        
        const fileRow = createFileListElement(file);
        listViewContent.appendChild(fileRow);
    });
    
    lucide.createIcons();
}

function createBackElement() {
    const backDiv = document.createElement('div');
    backDiv.className = 'folder-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer';
    backDiv.innerHTML = `
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2 bg-gray-100 rounded-lg">
                <i data-lucide="arrow-left" class="w-6 h-6 text-gray-600"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-medium text-slate-900">Volver</h4>
                <p class="text-xs text-slate-500">Directorio anterior</p>
            </div>
        </div>
    `;
    backDiv.addEventListener('click', navigateToParentDirectory);
    return backDiv;
}

function createBackListElement() {
    const row = document.createElement('tr');
    row.className = 'folder-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
    row.innerHTML = `
        <td class="py-3">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </div>
                <span class="font-medium">Volver</span>
            </div>
        </td>
        <td class="py-3 text-sm text-gray-500">-</td>
        <td class="py-3 text-sm text-gray-500">-</td>
        <td class="py-3 text-sm text-gray-500">-</td>
    `;
    row.addEventListener('click', navigateToParentDirectory);
    return row;
}

function createDirectoryElement(dir) {
    const div = document.createElement('div');
    div.className = 'folder-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer';
    div.dataset.name = dir.name;
    div.dataset.type = 'folder';
    div.innerHTML = `
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i data-lucide="folder" class="w-6 h-6 text-blue-600"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-medium text-slate-900">${dir.name}</h4>
                <p class="text-xs text-slate-500">${dir.item_count} elementos</p>
            </div>
            <div class="dropdown relative">
                <button onclick="event.stopPropagation(); toggleDropdown(this)" class="p-1 rounded hover:bg-gray-100">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
                <div class="dropdown-menu absolute right-0 z-10 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openFolder('${dir.name}')">Abrir</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="renameItem('${dir.name}', 'folder')">Renombrar</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="moveItem('${dir.name}', 'folder')">Mover</a>
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="deleteItem('${dir.name}', 'folder')">Eliminar</a>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span>${dir.owner}</span>
            <span>${dir.modified}</span>
        </div>
    `;
    div.addEventListener('dblclick', () => openFolder(dir.name));
    return div;
}

function createDirectoryListElement(dir) {
    const row = document.createElement('tr');
    row.className = 'folder-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
    row.dataset.name = dir.name;
    row.dataset.type = 'folder';
    row.innerHTML = `
        <td class="py-3">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="folder" class="w-4 h-4 text-blue-600"></i>
                </div>
                <span class="font-medium">${dir.name}</span>
            </div>
        </td>
        <td class="py-3 text-sm text-gray-500">-</td>
        <td class="py-3 text-sm text-gray-500">${dir.modified}</td>
        <td class="py-3">
            <div class="flex gap-2">
                <button onclick="openFolder('${dir.name}')" class="p-1 text-blue-600 hover:bg-blue-100 rounded">
                    <i data-lucide="folder-open" class="w-4 h-4"></i>
                </button>
                <button onclick="renameItem('${dir.name}', 'folder')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                </button>
                <button onclick="moveItem('${dir.name}', 'folder')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                    <i data-lucide="move" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteItem('${dir.name}', 'folder')" class="p-1 text-red-600 hover:bg-red-100 rounded">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </td>
    `;
    row.addEventListener('dblclick', () => openFolder(dir.name));
    return row;
}

function createFileElement(file) {
    const iconInfo = getFileIconInfo(file.extension);
    const div = document.createElement('div');
    div.className = 'file-item bg-white/60 rounded-xl p-4 hover:bg-white/80 transition-all duration-200 hover:scale-105 shadow-lg cursor-pointer';
    div.dataset.name = file.name;
    div.dataset.type = 'file';
    div.dataset.extension = file.extension;
    div.innerHTML = `
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2 ${iconInfo.bgColor} rounded-lg">
                <i data-lucide="${iconInfo.icon}" class="w-6 h-6 ${iconInfo.color}"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-medium text-slate-900">${file.name}</h4>
                <p class="text-xs text-slate-500">${file.size}</p>
            </div>
            <div class="dropdown relative">
                <button onclick="event.stopPropagation(); toggleDropdown(this)" class="p-1 rounded hover:bg-gray-100">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
                <div class="dropdown-menu absolute right-0 z-10 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="previewFile('${file.name}', '${file.extension}', '${file.full_path}')">Vista Previa</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="downloadFile('${file.full_path}', '${file.name}')">Descargar</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="renameItem('${file.name}', 'file')">Renombrar</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="moveItem('${file.name}', 'file')">Mover</a>
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="deleteItem('${file.name}', 'file')">Eliminar</a>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span>${file.owner}</span>
            <span>${file.modified}</span>
        </div>
    `;
    div.addEventListener('dblclick', () => previewFile(file.name, file.extension, file.full_path));
    return div;
}

function createFileListElement(file) {
    const iconInfo = getFileIconInfo(file.extension);
    const row = document.createElement('tr');
    row.className = 'file-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
    row.dataset.name = file.name;
    row.dataset.type = 'file';
    row.dataset.extension = file.extension;
    row.innerHTML = `
        <td class="py-3">
            <div class="flex items-center gap-2">
                <div class="p-2 ${iconInfo.bgColor} rounded-lg">
                    <i data-lucide="${iconInfo.icon}" class="w-4 h-4 ${iconInfo.color}"></i>
                </div>
                <span class="font-medium">${file.name}</span>
            </div>
        </td>
        <td class="py-3 text-sm text-gray-500">${file.size}</td>
        <td class="py-3 text-sm text-gray-500">${file.modified}</td>
        <td class="py-3">
            <div class="flex gap-2">
                <button onclick="previewFile('${file.name}', '${file.extension}', '${file.full_path}')" class="p-1 text-blue-600 hover:bg-blue-100 rounded">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
                <button onclick="downloadFile('${file.full_path}', '${file.name}')" class="p-1 text-green-600 hover:bg-green-100 rounded">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </button>
                <button onclick="renameItem('${file.name}', 'file')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                </button>
                <button onclick="moveItem('${file.name}', 'file')" class="p-1 text-gray-600 hover:bg-gray-100 rounded">
                    <i data-lucide="move" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteItem('${file.name}', 'file')" class="p-1 text-red-600 hover:bg-red-100 rounded">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </td>
    `;
    row.addEventListener('dblclick', () => previewFile(file.name, file.extension, file.full_path));
    return row;
}

function updateBreadcrumb() {
    const breadcrumb = document.getElementById('breadcrumb');
    const pathParts = currentPath.replace(__PATH_UPLOADS__, '').split('/').filter(part => part !== '');
    
    let breadcrumbHTML = `
        <i data-lucide="home" class="w-4 h-4"></i>
        <span class="cursor-pointer hover:text-blue-600" onclick="loadDirectoryContent('${__PATH_UPLOADS__}')">Inicio</span>
    `;
    
    let currentPathPart = __PATH_UPLOADS__;
    pathParts.forEach((part, index) => {
        currentPathPart += '/' + part;
        const isLast = index === pathParts.length - 1;
        
        breadcrumbHTML += `
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="${isLast ? 'text-blue-600' : 'cursor-pointer hover:text-blue-600'}" ${isLast ? '' : `onclick="loadDirectoryContent('${currentPathPart}')"`}>
                ${part}
            </span>
        `;
    });
    
    breadcrumb.innerHTML = breadcrumbHTML;
    lucide.createIcons();
}

function previewFile(name, extension, fullPath) {
    const ext = extension.toLowerCase();
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');
    
    previewTitle.textContent = name;
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
        previewContent.innerHTML = `<img src="${fullPath}" alt="${name}" class="max-w-full mx-auto">`;
    } else if (['pdf'].includes(ext)) {
        previewContent.innerHTML = `<iframe src="${fullPath}" class="w-full h-96" frameborder="0"></iframe>`;
    } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv'].includes(ext)) {
        previewContent.innerHTML = `<video src="${fullPath}" controls class="w-full max-h-96"></video>`;
    } else if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) {
        previewContent.innerHTML = `<audio src="${fullPath}" controls class="w-full"></audio>`;
    } else if (['txt', 'html', 'css', 'js', 'php', 'json', 'xml'].includes(ext)) {
        fetch(fullPath)
            .then(response => response.text())
            .then(data => {
                previewContent.innerHTML = `<pre class="bg-gray-100 p-4 rounded overflow-auto max-h-96">${escapeHtml(data)}</pre>`;
            })
            .catch(error => {
                previewContent.innerHTML = `<p class="text-red-600">No se pudo cargar el archivo: ${error.message}</p>`;
            });
    } else {
        previewContent.innerHTML = `<p class="text-gray-600">Vista previa no disponible para archivos .${extension}</p>
                                   <p class="mt-2"><a href="${fullPath}" download="${name}" class="text-blue-600 hover:underline">Descargar archivo</a></p>`;
    }
    
    showPreviewModal();
}

function downloadFile(fullPath, fileName) {
    const a = document.createElement('a');
    a.href = fullPath;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterValue = document.getElementById('filterSelect').value;
    
    document.querySelectorAll('.file-item, .folder-item').forEach(item => {
        const name = item.dataset.name.toLowerCase();
        const type = item.dataset.type;
        const extension = item.dataset.extension ? item.dataset.extension.toLowerCase() : '';
        
        let matchesSearch = name.includes(searchTerm);
        let matchesFilter = true;
        
        if (filterValue !== 'all') {
            if (type === 'folder') {
                matchesFilter = filterValue === 'folder';
            } else {
                if (filterValue === 'document') {
                    matchesFilter = ['doc', 'docx', 'txt', 'rtf', 'pdf'].includes(extension);
                } else if (filterValue === 'image') {
                    matchesFilter = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(extension);
                } else if (filterValue === 'video') {
                    matchesFilter = ['mp4', 'mov', 'avi', 'wmv', 'mkv', 'flv'].includes(extension);
                } else if (filterValue === 'pdf') {
                    matchesFilter = extension === 'pdf';
                }
            }
        }
        
        if (matchesSearch && matchesFilter) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function handleFilter() {
    handleSearch();
}

function switchView(view) {
    currentView = view;
    
    if (view === 'grid') {
        document.getElementById('file-grid').classList.remove('hidden');
        document.getElementById('file-list').classList.add('hidden');
        document.getElementById('gridViewBtn').classList.add('bg-blue-500', 'text-white');
        document.getElementById('gridViewBtn').classList.remove('bg-white/60');
        document.getElementById('listViewBtn').classList.remove('bg-blue-500', 'text-white');
        document.getElementById('listViewBtn').classList.add('bg-white/60');
    } else {
        document.getElementById('file-grid').classList.add('hidden');
        document.getElementById('file-list').classList.remove('hidden');
        document.getElementById('listViewBtn').classList.add('bg-blue-500', 'text-white');
        document.getElementById('listViewBtn').classList.remove('bg-white/60');
        document.getElementById('gridViewBtn').classList.remove('bg-blue-500', 'text-white');
        document.getElementById('gridViewBtn').classList.add('bg-white/60');
    }
}

function handleCreateFolder(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const folderName = formData.get('folder_name');
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=create_folder&folder_name=${encodeURIComponent(folderName)}&path=${encodeURIComponent(currentPath)}`
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al crear carpeta');
        }
    }).then(data => {
        if (data.success) {
            hideModal('createFolderModal');
            showNotification(data.message || 'Carpeta creada correctamente', 'success');
            loadDirectoryContent(currentPath);
        } else {
            throw new Error(data.error || data.message || 'Error al crear la carpeta');
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al crear la carpeta', 'error');
    });
}

function handleUploadFiles(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    const uploadFormData = new FormData();
    const files = formData.getAll('files[]');
    
    files.forEach(file => {
        uploadFormData.append('files[]', file);
    });
    uploadFormData.append('action', 'upload_files');
    uploadFormData.append('path', currentPath);
    
    fetch('', {
        method: 'POST',
        body: uploadFormData
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al subir archivos');
        }
    }).then(data => {
        if (data.success) {
            hideModal('uploadModal');
            showNotification(data.message || 'Archivos subidos correctamente', 'success');
            loadDirectoryContent(currentPath);
        } else {
            throw new Error(data.error || data.message || 'Error al subir archivos');
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al subir archivos', 'error');
    });
}

function handleRenameItem(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const oldName = formData.get('old_name');
    const newName = formData.get('new_name');
    const itemType = formData.get('item_type');
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=rename_item&old_name=${encodeURIComponent(oldName)}&new_name=${encodeURIComponent(newName)}&item_type=${itemType}&path=${encodeURIComponent(currentPath)}`
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al renombrar elemento');
        }
    }).then(data => {
        if (data.success) {
            hideModal('renameModal');
            showNotification(data.message || 'Elemento renombrado correctamente', 'success');
            loadDirectoryContent(currentPath);
        } else {
            throw new Error(data.error || data.message || 'Error al renombrar el elemento');
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al renombrar el elemento', 'error');
    });
}

function handleDeleteItem(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const itemName = formData.get('item_name');
    const itemType = formData.get('item_type');
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_item&item_name=${encodeURIComponent(itemName)}&item_type=${itemType}&path=${encodeURIComponent(currentPath)}`
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al eliminar elemento');
        }
    }).then(data => {
        if (data.success) {
            hideModal('deleteModal');
            showNotification(data.message || 'Elemento eliminado correctamente', 'success');
            loadDirectoryContent(currentPath);
        } else {
            throw new Error(data.error || data.message || 'Error al eliminar el elemento');
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al eliminar el elemento', 'error');
    });
}

function handleMoveItem(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const itemName = formData.get('item_name');
    const itemType = formData.get('item_type');
    const targetPath = formData.get('target_path');
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=move_item&item_name=${encodeURIComponent(itemName)}&item_type=${itemType}&path=${encodeURIComponent(currentPath)}&target_path=${encodeURIComponent(targetPath)}`
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al mover elemento');
        }
    }).then(data => {
        if (data.success) {
            hideModal('moveModal');
            showNotification(data.message || 'Elemento movido correctamente', 'success');
            loadDirectoryContent(currentPath);
        } else {
            throw new Error(data.error || data.message || 'Error al mover el elemento');
        }
    }).catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error al mover el elemento', 'error');
    });
}

function showNotification(message, type) {
    const container = document.getElementById('message-container');
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    
    if (type === 'success') {
        successMsg.textContent = message;
        successMsg.classList.remove('hidden');
        errorMsg.classList.add('hidden');
    } else {
        errorMsg.textContent = message;
        errorMsg.classList.remove('hidden');
        successMsg.classList.add('hidden');
    }
    
    container.classList.remove('hidden');
    setTimeout(() => {
        container.classList.add('hidden');
    }, 5000);
}

document.querySelectorAll('.fixed').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>