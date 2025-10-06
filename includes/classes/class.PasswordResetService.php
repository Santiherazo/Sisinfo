<?php

class PasswordResetService {
    private PDO $db;
    private ErrorLogger $logger;

    public function __construct(PDO $db, ?ErrorLogger $logger = null) {
        $this->db = $db;
        $this->logger = $logger ?? new ErrorLogger();
    }

    public function createRequest(string $email): ?string {
        try {
            $token = bin2hex(random_bytes(64));

            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_PASSWORD_RESETS_ . " (" . _RESET_EMAIL_ . ", " . _RESET_TOKEN_ . ", " . _RESET_CREATED_ . ") 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$email, $token]);

            return $token;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'PASSWORD_RESET_CREATE');
            return null;
        }
    }

    public function validateToken(string $token, int $validMinutes = 60): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_PASSWORD_RESETS_ . " 
                WHERE " . _RESET_TOKEN_ . " = ? AND " . _RESET_CREATED_ . " >= NOW() - INTERVAL ? MINUTE
                ORDER BY " . _RESET_CREATED_ . " DESC
                LIMIT 1
            ");
            $stmt->execute([$token, $validMinutes]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'PASSWORD_RESET_VALIDATE');
            return null;
        }
    }

    public function deleteToken(string $token): void {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_PASSWORD_RESETS_ . " 
                WHERE " . _RESET_TOKEN_ . " = ?
            ");
            $stmt->execute([$token]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'PASSWORD_RESET_DELETE');
        }
    }

    public function purgeExpired(int $olderThanMinutes = 1440): int {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_PASSWORD_RESETS_ . " 
                WHERE " . _RESET_CREATED_ . " < NOW() - INTERVAL ? MINUTE
            ");
            $stmt->execute([$olderThanMinutes]);
            return $stmt->rowCount();
        } catch (Throwable $e) {
            $this->logger->logException($e, 'PASSWORD_RESET_PURGE');
            return 0;
        }
    }
}