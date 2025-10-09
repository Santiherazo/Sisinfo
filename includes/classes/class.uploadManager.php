<?php

class uploadManager
{
    private string $baseDir;
    private int $maxFileSize;
    private array $allowedExtensions = [];
    private array $errors = [];
    private ?string $uploadedFileName = null;
    private string $encryptionKey;

    private array $mimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'text/plain' => 'txt',
        'text/csv' => 'csv',
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'application/x-gzip' => 'gz',
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    const CATEGORY_IMG = 'img';
    const CATEGORY_DOCS = 'docs';
    const CATEGORY_OTHERS = 'others';

    public function __construct(string $baseDir = __PATH_UPLOADS__, int $maxSizeMB = 10) {
        $this->baseDir = rtrim($baseDir, '/');
        $this->maxFileSize = $maxSizeMB * 1024 * 1024;
        $this->encryptionKey = hash('sha256', defined('__APP_SECRET__') ? __APP_SECRET__ : 'default_secret_key');
        $this->ensureDirectoryExists($this->baseDir);
        $this->ensureDirectoryExists($this->baseDir.'/'.self::CATEGORY_IMG);
        $this->ensureDirectoryExists($this->baseDir.'/'.self::CATEGORY_DOCS);
        $this->ensureDirectoryExists($this->baseDir.'/'.self::CATEGORY_OTHERS);
    }

    public function encryptId(string $id): string {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($id, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decryptId(string $encryptedId): string|false {
        $data = base64_decode($encryptedId);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
    }

    public function generateTempDirName(string $prefix = 'temp_'): string {
        return $prefix . bin2hex(random_bytes(16));
    }

    public function setAllowedExtensions(array $extensions): void {
        $this->allowedExtensions = array_map('strtolower', $extensions);
    }

    public function setMaxFileSize(int $bytes): void {
        $this->maxFileSize = $bytes;
    }

    public function setUploadDirectory(string $dir): void {
        $this->baseDir = rtrim($dir, '/');
    }

    public function getUploadedFileName(): ?string {
        return $this->uploadedFileName;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function upload(array $file, string $userId, string $subFolder = ''): string|false {
        $this->errors = [];
        $this->uploadedFileName = null;

        if (!isset($file['error'])) {
            $this->errors[] = 'Parámetros de archivo inválidos.';
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($file['error']);
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = sprintf('El archivo excede el tamaño máximo permitido (%s MB).', round($this->maxFileSize / 1024 / 1024, 2));
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset($this->mimeTypes[$mime])) {
            $this->errors[] = "Tipo de archivo no permitido: $mime";
            return false;
        }

        $ext = $this->mimeTypes[$mime];

        if (!empty($this->allowedExtensions) && !in_array($ext, $this->allowedExtensions)) {
            $this->errors[] = "Extensión no permitida: .$ext";
            return false;
        }

        if (str_starts_with($mime, 'image/') && !in_array($mime, ['image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])) {
            if (!@getimagesize($file['tmp_name'])) {
                $this->errors[] = 'El archivo no es una imagen válida.';
                return false;
            }
        }

        $category = $this->determineCategory($ext);
        $safeName = $this->sanitizeFileName(pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = $safeName . '_' . uniqid() . '.' . $ext;
        $this->uploadedFileName = $filename;

        $safeUser = $this->sanitizeFileName($userId);
        $basePath = $this->baseDir . '/' . $category;
        
        if (!empty($subFolder)) {
            $safeSub = $this->sanitizeFileName($subFolder);
            $basePath .= '/' . $safeSub;
        }
        
        $userFolder = str_starts_with($userId, 'temp_') ? $this->encryptId($userId) : $safeUser;
        $basePath .= '/' . $userFolder;

        if (!is_dir($basePath)) {
            if (!mkdir($basePath, 0755, true)) {
                $this->errors[] = 'No se pudo crear el directorio de destino.';
                return false;
            }
        }

        $destPath = $basePath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->errors[] = 'No se pudo mover el archivo subido.';
            return false;
        }

        return $destPath;
    }

    public function delete(string $path): bool {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    public function getPublicUrl(string $filePath): ?string {
        if (!str_starts_with($filePath, $this->baseDir)) {
            return null;
        }
        
        $relativePath = str_replace($this->baseDir, '', $filePath);
        
        if (str_starts_with($relativePath, '/'.self::CATEGORY_IMG.'/')) {
            return __BASE_URL__ . 'uploads' . $relativePath;
        }
        
        return null;
    }

    private function sanitizeFileName(string $name): string {
        $name = str_replace([' ', '%20'], '_', $name);
        return preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
    }

    private function getUploadError(int $code): string {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido.',
            UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente.',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo.',
            default => 'Error desconocido al subir el archivo.',
        };
    }

    private function determineCategory(string $ext): string {
        $images = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico'];
        $docs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];
        return in_array($ext, $images) ? self::CATEGORY_IMG : (in_array($ext, $docs) ? self::CATEGORY_DOCS : self::CATEGORY_OTHERS);
    }

    private function ensureDirectoryExists(string $path): void {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    public static function createDirectory(string $path): bool {
        return !is_dir($path) ? mkdir($path, 0755, true) : false;
    }

    public static function renameDirectory(string $oldPath, string $newPath): bool {
        return is_dir($oldPath) && !file_exists($newPath) ? rename($oldPath, $newPath) : false;
    }

    public static function deleteDirectory(string $path): bool {
        if (!is_dir($path)) return false;
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;
            $itemPath = $path . '/' . $item;
            is_dir($itemPath) ? self::deleteDirectory($itemPath) : unlink($itemPath);
        }
        return rmdir($path);
    }

    public static function moveDirectory(string $source, string $destination): bool {
        return is_dir($source) && !file_exists($destination) ? rename($source, $destination) : false;
    }

    public static function listDirectories(string $path): array {
        if (!is_dir($path)) return [];
        $items = array_diff(scandir($path), ['.', '..']);
        $directories = [];
        foreach ($items as $item) {
            $itemPath = $path . '/' . $item;
            if (is_dir($itemPath)) {
                $directories[] = $item;
            }
        }
        return $directories;
    }

    public static function listAllContents(string $path): array {
        if (!is_dir($path)) return [];
        return array_diff(scandir($path), ['.', '..']);
    }

    public static function getDirectorySize(string $path): int {
        $totalSize = 0;
        if (!is_dir($path)) return $totalSize;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }
        return $totalSize;
    }

    public static function directoryExists(string $path): bool {
        return is_dir($path);
    }

    public static function copyDirectory(string $source, string $destination): bool {
        if (!is_dir($source)) return false;
        if (!is_dir($destination)) mkdir($destination, 0755, true);
        foreach (scandir($source) as $item) {
            if ($item === '.' || $item === '..') continue;
            $src = $source . '/' . $item;
            $dst = $destination . '/' . $item;
            is_dir($src) ? self::copyDirectory($src, $dst) : copy($src, $dst);
        }
        return true;
    }

    public function cleanOldFiles(string $directory, int $daysOld): int {
        $deleted = 0;
        if (!is_dir($directory)) return 0;
        foreach (scandir($directory) as $item) {
            if ($item === '.' || $item === '..') continue;
            $itemPath = $directory . '/' . $item;
            if (is_dir($itemPath)) {
                $deleted += $this->cleanOldFiles($itemPath, $daysOld);
                if (count(scandir($itemPath)) === 2) {
                    rmdir($itemPath);
                }
            } elseif (filemtime($itemPath) < (time() - ($daysOld * 86400))) {
                if (unlink($itemPath)) $deleted++;
            }
        }
        return $deleted;
    }

    public function generateUniqueFilename(string $directory, string $originalName): string {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = $this->sanitizeFileName($basename);
        $counter = 1;
        $filename = "$safeName.$extension";
        $filepath = "$directory/$filename";
        while (file_exists($filepath)) {
            $filename = "$safeName($counter).$extension";
            $filepath = "$directory/$filename";
            $counter++;
        }
        return $filename;
    }

    public function replaceFile(array $newFile, string $existingFilePath, string $userId, string $subFolder = ''): string|false {
        $this->errors = [];
        $this->uploadedFileName = null;
        if (!file_exists($existingFilePath)) {
            $this->errors[] = 'El archivo a reemplazar no existe.';
            return false;
        }
        $existingDir = dirname($existingFilePath);
        $existingFileName = basename($existingFilePath);
        $newFilePath = $this->upload($newFile, $userId, $subFolder);
        if ($newFilePath === false) {
            return false;
        }
        if (!$this->delete($existingFilePath)) {
            $this->delete($newFilePath);
            $this->errors[] = 'No se pudo eliminar el archivo antiguo.';
            return false;
        }
        $renamedPath = $existingDir . '/' . $existingFileName;
        if (rename($newFilePath, $renamedPath)) {
            $this->uploadedFileName = $existingFileName;
            return $renamedPath;
        } else {
            $this->uploadedFileName = basename($newFilePath);
            return $newFilePath;
        }
    }

    public function replaceFileKeepName(array $newFile, string $existingFilePath, string $userId, string $subFolder = ''): string|false {
        $this->errors = [];
        $this->uploadedFileName = null;
        if (!file_exists($existingFilePath)) {
            $this->errors[] = 'El archivo a reemplazar no existe.';
            return false;
        }
        $existingDir = dirname($existingFilePath);
        $existingFileName = basename($existingFilePath);
        $existingFileInfo = pathinfo($existingFilePath);
        if ($newFile['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($newFile['error']);
            return false;
        }
        if ($newFile['size'] > $this->maxFileSize) {
            $this->errors[] = sprintf('El archivo excede el tamaño máximo permitido (%s MB).', round($this->maxFileSize / 1024 / 1024, 2));
            return false;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($newFile['tmp_name']);
        if (!isset($this->mimeTypes[$mime])) {
            $this->errors[] = "Tipo de archivo no permitido: $mime";
            return false;
        }
        $newExt = $this->mimeTypes[$mime];
        $existingExt = $existingFileInfo['extension'] ?? '';
        if (!empty($this->allowedExtensions) && !in_array($newExt, $this->allowedExtensions)) {
            $this->errors[] = "Extensión no permitida: .$newExt";
            return false;
        }
        if (str_starts_with($mime, 'image/') && !in_array($mime, ['image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])) {
            if (!@getimagesize($newFile['tmp_name'])) {
                $this->errors[] = 'El archivo no es una imagen válida.';
                return false;
            }
        }
        $tempPath = $existingDir . '/temp_' . uniqid() . '.' . $newExt;
        if (!move_uploaded_file($newFile['tmp_name'], $tempPath)) {
            $this->errors[] = 'No se pudo mover el archivo subido.';
            return false;
        }
        if (!$this->delete($existingFilePath)) {
            $this->delete($tempPath);
            $this->errors[] = 'No se pudo eliminar el archivo antiguo.';
            return false;
        }
        $finalFileName = $existingFileInfo['filename'] . '.' . $newExt;
        $finalPath = $existingDir . '/' . $finalFileName;
        if (rename($tempPath, $finalPath)) {
            $this->uploadedFileName = $finalFileName;
            return $finalPath;
        } else {
            $this->uploadedFileName = basename($tempPath);
            return $tempPath;
        }
    }
}