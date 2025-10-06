<?php
class Cache {
    private $memoryStorage = [];
    private $memoryExpiration = [];
    private $storageDirs = [];
    private $defaultStorageDir;
    private $mode = 'memory';
    private $defaultTtl = 3600;
    
    private static $cacheStructure = [
        'metadata' => [
            'key' => '',
            'created' => '',
            'expires' => 0,
            'ttl' => 0,
            'size' => 0
        ],
        'data' => null
    ];

    public function __construct($mode = 'memory', $storageDir = null) {
        $this->mode = $mode;
        if ($mode === 'file' && $storageDir) {
            $this->defaultStorageDir = $this->normalizePath($storageDir);
            $this->ensureDirectory($this->defaultStorageDir);
        }
    }

    public function configure($mode, $storageDir = null) {
        $this->mode = $mode;
        if ($mode === 'file' && $storageDir) {
            $this->defaultStorageDir = $this->normalizePath($storageDir);
            $this->ensureDirectory($this->defaultStorageDir);
        }
        return $this;
    }

    public function setCachePath($storageDir, $key = 'default') {
        $this->storageDirs[$key] = $this->normalizePath($storageDir);
        $this->ensureDirectory($this->storageDirs[$key]);
        return $this;
    }

    public function setDefaultTtl($ttl) {
        $this->defaultTtl = (int)$ttl;
        return $this;
    }

    public function get($key, $default = null, $storageKey = 'default') {
        if ($this->mode === 'memory') {
            $data = $this->getFromMemory($key, $default);
        } else {
            $data = $this->getFromFile($key, $default, $storageKey);
        }
        return (is_array($data) && isset($data['data'])) ? $data['data'] : $default;
    }

    public function set($key, $value, $duration = null, $storageKey = 'default') {
        $duration = $duration ?? $this->defaultTtl;
        $structuredData = $this->createStructure($key, $value, $duration);
        
        if ($this->mode === 'memory') {
            return $this->setInMemory($key, $structuredData, $duration);
        } else {
            return $this->setInFile($key, $structuredData, $duration, $storageKey);
        }
    }

    public function delete($key, $storageKey = 'default') {
        if ($this->mode === 'memory') {
            return $this->deleteFromMemory($key);
        } else {
            return $this->deleteFromFile($key, $storageKey);
        }
    }

    public function clear($storageKey = 'default') {
        if ($this->mode === 'memory') {
            return $this->clearMemory();
        } else {
            return $this->clearFiles($storageKey);
        }
    }

    public function storeNavbarProfile($userId, $profileData, $duration = 300) {
        $key = $this->normalizeKey("navbar_profile_{$userId}");
        $enhancedData = [
            'navbar' => $profileData,
            'metadata' => [
                'user_id' => $userId,
                'stored_at' => date('Y-m-d H:i:s'),
                'notifications_count' => $profileData['unreadCount'] ?? 0
            ]
        ];
        return $this->set($key, $enhancedData, $duration, 'navbar');
    }

    public function getNavbarProfile($userId, $default = null) {
        $key = $this->normalizeKey("navbar_profile_{$userId}");
        $data = $this->get($key, $default, 'navbar');
        return (is_array($data) && isset($data['navbar'])) ? $data['navbar'] : $default;
    }

    public function invalidateNavbarCache($userId) {
        $key = $this->normalizeKey("navbar_profile_{$userId}");
        $notificationsKey = $this->normalizeKey("notifications_{$userId}");
        
        $this->delete($key, 'navbar');
        $this->delete($notificationsKey, 'navbar');
        return true;
    }

    public function storeUserProfile($userId, $profileData, $duration = 300) {
        $key = $this->normalizeKey("user_profile_{$userId}");
        $enhancedData = [
            'profile' => $profileData,
            'metadata' => [
                'user_id' => $userId,
                'stored_at' => date('Y-m-d H:i:s'),
                'access_levels' => [
                    'admin' => $profileData['hasAdminAccess'] ?? false,
                    'evaluator' => $profileData['hasEvaluatorAccess'] ?? false
                ]
            ]
        ];
        return $this->set($key, $enhancedData, $duration, 'profiles');
    }

    public function getUserProfile($userId, $default = null) {
        $key = $this->normalizeKey("user_profile_{$userId}");
        $data = $this->get($key, $default, 'profiles');
        return (is_array($data) && isset($data['profile'])) ? $data['profile'] : $default;
    }

    public function storeSession($sessionKey, $sessionData, $duration = 3600) {
        $key = $this->normalizeKey($sessionKey);
        $enhancedData = [
            'session' => $sessionData,
            'statistics' => [
                'evaluators_total' => count($sessionData['evaluadores'] ?? []),
                'evaluators_completed' => 0,
                'last_activity' => date('Y-m-d H:i:s')
            ]
        ];
        
        if (isset($sessionData['evaluadores']) && is_array($sessionData['evaluadores'])) {
            foreach ($sessionData['evaluadores'] as $evaluador) {
                if (is_array($evaluador) && ($evaluador['estado_evaluacion'] ?? '') === 'completada') {
                    $enhancedData['statistics']['evaluators_completed']++;
                }
            }
        }
        
        return $this->set($key, $enhancedData, $duration, 'sessions');
    }

    public function getSession($sessionKey, $default = null) {
        $key = $this->normalizeKey($sessionKey);
        $data = $this->get($key, $default, 'sessions');
        return (is_array($data) && isset($data['session'])) ? $data['session'] : $default;
    }

    public function deleteSession($sessionKey) {
        $key = $this->normalizeKey($sessionKey);
        $success = $this->delete($key, 'sessions');
        $this->cleanEmptyDirectories('sessions');
        return $success;
    }

    public function storeAutoSave($cacheKey, $autoSaveData, $duration = 86400, $storageKey = 'autosave') {
        $key = $this->normalizeKey($cacheKey);
        return $this->set($key, $autoSaveData, $duration, $storageKey);
    }

    public function getAutoSave($cacheKey, $default = null, $storageKey = 'autosave') {
        $key = $this->normalizeKey($cacheKey);
        return $this->get($key, $default, $storageKey);
    }

    public function deleteAutoSave($cacheKey, $storageKey = 'autosave') {
        $key = $this->normalizeKey($cacheKey);
        $success = $this->delete($key, $storageKey);
        $this->delete($this->normalizeKey("session_stats_{$cacheKey}"), 'sessions');
        $this->cleanEmptyDirectories($storageKey);
        return $success;
    }

    public function exists($key, $storageKey = 'default') {
        $normalizedKey = $this->normalizeKey($key);
        if ($this->mode === 'memory') {
            return isset($this->memoryStorage[$normalizedKey]) && 
                   isset($this->memoryExpiration[$normalizedKey]) && 
                   time() < $this->memoryExpiration[$normalizedKey];
        } else {
            $storageDir = $this->getStorageDir($storageKey);
            $filePath = $this->getFilePath($normalizedKey, $storageDir);
            return file_exists($filePath);
        }
    }

    public function garbageCollection($storageKey = 'default') {
        try {
            $storageDir = $this->getStorageDir($storageKey);
            $files = glob($storageDir . '**/*.json', GLOB_BRACE);
            $cleaned = 0;
            $now = time();
            
            if (!is_array($files)) {
                return 0;
            }
            
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                
                if (is_array($data) && isset($data['metadata']['expires'])) {
                    if ($now > $data['metadata']['expires']) {
                        if (unlink($file)) {
                            $cleaned++;
                        }
                    }
                } else {
                    if (file_exists($file)) {
                        unlink($file);
                        $cleaned++;
                    }
                }
            }
            
            $this->cleanEmptyDirectories($storageKey);
            return $cleaned;
        } catch (Exception $e) {
            error_log("Cache GC error: " . $e->getMessage());
            return 0;
        }
    }

    private function createStructure($key, $value, $duration) {
        $structure = self::$cacheStructure;
        $structure['metadata'] = [
            'key' => $key,
            'created' => date('Y-m-d H:i:s'),
            'expires' => time() + $duration,
            'ttl' => $duration,
            'size' => strlen(serialize($value))
        ];
        $structure['data'] = $value;
        return $structure;
    }

    private function normalizePath($path) {
        return rtrim(str_replace('\\', '/', $path), '/') . '/';
    }

    private function normalizeKey($key) {
        if (is_array($key)) {
            return md5(serialize($key));
        }
        if (is_object($key)) {
            return md5(serialize($key));
        }
        return (string)$key;
    }

    private function ensureDirectory($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function getStorageDir($storageKey) {
        return $this->storageDirs[$storageKey] ?? $this->defaultStorageDir;
    }

    private function getFilePath($key, $storageDir) {
        $normalizedKey = $this->normalizeKey($key);
        $hash = md5($normalizedKey);
        $subDir = substr($hash, 0, 2) . '/';
        $fullDir = $storageDir . $subDir;
        $this->ensureDirectory($fullDir);
        return $fullDir . $hash . '.json';
    }

    private function getFromMemory($key, $default) {
        $normalizedKey = $this->normalizeKey($key);
        if (isset($this->memoryStorage[$normalizedKey]) && isset($this->memoryExpiration[$normalizedKey])) {
            if (time() < $this->memoryExpiration[$normalizedKey]) {
                return $this->memoryStorage[$normalizedKey];
            }
            $this->deleteFromMemory($normalizedKey);
        }
        return $default;
    }

    private function setInMemory($key, $value, $duration) {
        $normalizedKey = $this->normalizeKey($key);
        $this->memoryStorage[$normalizedKey] = $value;
        $this->memoryExpiration[$normalizedKey] = time() + $duration;
        return true;
    }

    private function deleteFromMemory($key) {
        $normalizedKey = $this->normalizeKey($key);
        unset($this->memoryStorage[$normalizedKey]);
        unset($this->memoryExpiration[$normalizedKey]);
        return true;
    }

    private function clearMemory() {
        $this->memoryStorage = [];
        $this->memoryExpiration = [];
        return true;
    }

    private function getFromFile($key, $default, $storageKey) {
        try {
            $normalizedKey = $this->normalizeKey($key);
            $storageDir = $this->getStorageDir($storageKey);
            $filePath = $this->getFilePath($normalizedKey, $storageDir);
            
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return $default;
            }
            
            $content = file_get_contents($filePath);
            if ($content === false) {
                return $default;
            }
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || 
                !is_array($data) || 
                !isset($data['metadata'], $data['data'])) {
                $this->deleteFromFile($normalizedKey, $storageKey);
                return $default;
            }
            
            if (time() > $data['metadata']['expires']) {
                $this->deleteFromFile($normalizedKey, $storageKey);
                return $default;
            }
            
            return $data;
        } catch (Exception $e) {
            error_log("Cache get error for key {$key}: " . $e->getMessage());
            return $default;
        }
    }

    private function setInFile($key, $value, $duration, $storageKey) {
        try {
            $normalizedKey = $this->normalizeKey($key);
            $storageDir = $this->getStorageDir($storageKey);
            $filePath = $this->getFilePath($normalizedKey, $storageDir);
            
            $dir = dirname($filePath);
            if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                error_log("Cannot create cache directory: {$dir}");
                return false;
            }
            
            $jsonData = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($jsonData === false) {
                error_log("JSON encode error for key: {$key}");
                return false;
            }
            
            $tempFile = $filePath . '.tmp';
            if (file_put_contents($tempFile, $jsonData, LOCK_EX) === false) {
                return false;
            }
            
            return rename($tempFile, $filePath);
        } catch (Exception $e) {
            error_log("Cache set error for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    private function deleteFromFile($key, $storageKey) {
        try {
            $normalizedKey = $this->normalizeKey($key);
            $storageDir = $this->getStorageDir($storageKey);
            $filePath = $this->getFilePath($normalizedKey, $storageDir);
            
            if (file_exists($filePath)) {
                $success = unlink($filePath);
                if ($success) {
                    $subDir = dirname($filePath);
                    $this->cleanEmptySubdirectories($subDir);
                }
                return $success;
            }
            return true;
        } catch (Exception $e) {
            error_log("Cache delete error: " . $e->getMessage());
            return false;
        }
    }

    private function clearFiles($storageKey) {
        try {
            $storageDir = $this->getStorageDir($storageKey);
            $files = glob($storageDir . '**/*.json', GLOB_BRACE);
            $deleted = 0;
            
            if (!is_array($files)) {
                return false;
            }
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (unlink($file)) {
                        $deleted++;
                    }
                }
            }
            
            $this->cleanEmptySubdirectories($storageDir);
            return $deleted > 0;
        } catch (Exception $e) {
            error_log("Cache clear error: " . $e->getMessage());
            return false;
        }
    }

    private function cleanEmptyDirectories($storageKey) {
        try {
            $storageDir = $this->getStorageDir($storageKey);
            $this->cleanEmptySubdirectories($storageDir);
        } catch (Exception $e) {
            error_log("Clean directories error: " . $e->getMessage());
        }
    }

    private function cleanEmptySubdirectories($dir) {
        if (!is_dir($dir)) return;
        
        $subdirs = glob($dir . '*', GLOB_ONLYDIR);
        if (!is_array($subdirs)) {
            return;
        }
        
        foreach ($subdirs as $subdir) {
            $files = glob($subdir . '/*');
            if (is_array($files) && count($files) === 0) {
                @rmdir($subdir);
            }
        }
    }
}