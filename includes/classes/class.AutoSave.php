<?php
class AutoSave {
    private static $cache;
    
    public static function configure($cacheInstance) {
        self::$cache = $cacheInstance;
    }
    
    public static function save($userId, $token, $data) {
        try {
            $cacheKey = md5($userId . '_' . $token);
            $data['saved_at'] = time();
            return self::$cache->set($cacheKey, $data, 86400);
        } catch (Exception $e) {
            error_log("AutoSave save error: " . $e->getMessage());
            return false;
        }
    }

    public static function load($userId, $token) {
        try {
            $cacheKey = md5($userId . '_' . $token);
            return self::$cache->get($cacheKey);
        } catch (Exception $e) {
            error_log("AutoSave load error: " . $e->getMessage());
            return null;
        }
    }

    public static function delete($userId, $token) {
        try {
            $cacheKey = md5($userId . '_' . $token);
            return self::$cache->delete($cacheKey);
        } catch (Exception $e) {
            error_log("AutoSave delete error: " . $e->getMessage());
            return false;
        }
    }
}
?>