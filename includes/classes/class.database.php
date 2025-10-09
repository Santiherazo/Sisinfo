<?php

class dB {
    public string $error = '';
    public bool $dead = false;
    protected PDO $db;

    private bool $enableErrorLogs;
    private ErrorLogger $logger;

    public function __construct(
        string $host,
        string $port,
        string $dbname,
        string $user,
        string $password,
        bool $enableErrorLogs = true,
        ?ErrorLogger $logger = null
    ) {
        $this->enableErrorLogs = $enableErrorLogs;
        $this->logger = $logger ?? new ErrorLogger();

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            $this->handleError("PDO Error: " . $e->getMessage(), $e);
            throw new RuntimeException("Database connection failed.");
        } catch (Throwable $e) {
            $this->handleError("General Error: " . $e->getMessage(), $e);
            throw new RuntimeException("Unexpected database error.");
        }
    }

    public function getConnection(): PDO {
        return $this->db;
    }

    public function query(string $sql, array $params = []): bool {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->dead = true;
            $this->handleError("Query failed: {$e->getMessage()} | SQL: $sql", $e);
            return false;
        }
    }

    public function query_fetch(string $sql, array $params = []): array|false {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->dead = true;
            $this->handleError("Fetch failed: {$e->getMessage()} | SQL: $sql", $e);
            return false;
        }
    }

    public function query_fetch_single(string $sql, array $params = []): ?array {
        $result = $this->query_fetch($sql, $params);
        return (!empty($result)) ? $result[0] : null;
    }

    public function lastInsertId(?string $name = null): string {
        try {
            return $this->db->lastInsertId($name);
        } catch (PDOException $e) {
            $this->handleError("lastInsertId failed: {$e->getMessage()}", $e);
            return '0';
        }
    }

    public function beginTransaction(): bool {
        try {
            return $this->db->beginTransaction();
        } catch (PDOException $e) {
            $this->handleError("beginTransaction failed: {$e->getMessage()}", $e);
            return false;
        }
    }

    public function commit(): bool {
        try {
            return $this->db->commit();
        } catch (PDOException $e) {
            $this->handleError("commit failed: {$e->getMessage()}", $e);
            return false;
        }
    }

    public function rollBack(): bool {
        try {
            return $this->db->rollBack();
        } catch (PDOException $e) {
            $this->handleError("rollBack failed: {$e->getMessage()}", $e);
            return false;
        }
    }

    public function inTransaction(): bool {
        try {
            return $this->db->inTransaction();
        } catch (PDOException $e) {
            $this->handleError("inTransaction check failed: {$e->getMessage()}", $e);
            return false;
        }
    }

    public function endTransaction(bool $success): bool {
        return $success ? $this->commit() : ($this->inTransaction() ? $this->rollBack() : false);
    }

    private function handleError(string $message, ?Throwable $e = null): void {
        $this->dead = true;
        $this->error = $message;

        if ($this->enableErrorLogs && $e !== null) {
            $this->logger->logException($e, 'DATABASE');
        } elseif ($this->enableErrorLogs) {
            $this->logger->logMessage($message, 'DATABASE');
        }
    }
}