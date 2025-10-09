<?php

class Connection {
    public static function Database(): dB {
        $host = self::_config('SQL_DB_HOST');
        $port = self::_config('SQL_DB_PORT');
        $dbname = self::_config('SQL_DB_NAME');
        $user = self::_config('SQL_DB_USER');
        $pass = self::_config('SQL_DB_PASS');

        $logger = new ErrorLogger();

        if (!self::validateParams($host, $port, $dbname, $user)) {
            $logger->logPhpError('Invalid DB configuration parameters.');
            throw new RuntimeException('Invalid database configuration.');
        }

        try {
            $db = new dB($host, $port, $dbname, $user, $pass);

            if ($db->dead) {
                $errorMessage = $db->error ?: "Connection to database failed ({$dbname})";
                $logger->logDatabaseError($errorMessage);
                if (self::_config('error_reporting')) {
                    throw new RuntimeException($errorMessage);
                }
                throw new RuntimeException('Connection to database failed.');
            }

            return $db;
        } catch (Throwable $e) {
            $logger->logException($e, 'DATABASE');
            throw new RuntimeException('Connection error: ' . $e->getMessage(), 0, $e);
        }
    }

    private static function _config(string $config): mixed {
        $webengineConfig = webengineConfigs();
        return (is_array($webengineConfig) && array_key_exists($config, $webengineConfig)) ? $webengineConfig[$config] : null;
    }

    private static function validateParams(...$params): bool {
        foreach ($params as $param) {
            if (!is_string($param) || trim($param) === '') return false;
        }
        return true;
    }
}