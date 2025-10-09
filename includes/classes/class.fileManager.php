<?php

class fileManager
{
    public static function listFiles(string $directory): array {
        if (!is_dir($directory)) return [];
        return array_values(array_filter(scandir($directory), function($item) use ($directory) {
            return $item !== '.' && $item !== '..' && is_file($directory . '/' . $item);
        }));
    }

    public static function deleteFile(string $filePath): bool {
        return is_file($filePath) ? unlink($filePath) : false;
    }

    public static function renameFile(string $oldPath, string $newPath): bool {
        return is_file($oldPath) && !file_exists($newPath) ? rename($oldPath, $newPath) : false;
    }

    public static function moveFile(string $sourcePath, string $destinationPath): bool {
        if (!is_file($sourcePath)) return false;
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) mkdir($destinationDir, 0755, true);
        return rename($sourcePath, $destinationPath);
    }

    public static function readFileContents(string $file): ?string {
        return is_file($file) ? file_get_contents($file) : null;
    }

    public static function updateFileContents(string $file, string $content): bool {
        return is_file($file) ? file_put_contents($file, $content) !== false : false;
    }

    public static function copyFile(string $sourcePath, string $destinationPath): bool {
        if (!is_file($sourcePath)) return false;
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) mkdir($destinationDir, 0755, true);
        return copy($sourcePath, $destinationPath);
    }

    public static function fileExists(string $filePath): bool {
        return is_file($filePath);
    }

    public static function getFileSize(string $filePath): int|false {
        return is_file($filePath) ? filesize($filePath) : false;
    }

    public static function getFileExtension(string $filePath): string|null {
        return is_file($filePath) ? pathinfo($filePath, PATHINFO_EXTENSION) : null;
    }

    public static function getFileName(string $filePath): string|null {
        return is_file($filePath) ? basename($filePath) : null;
    }

    public static function getFileMimeType(string $filePath): string|false {
        return is_file($filePath) ? mime_content_type($filePath) : false;
    }

    public static function touchFile(string $filePath): bool {
        $dir = dirname($filePath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return touch($filePath);
    }

}

