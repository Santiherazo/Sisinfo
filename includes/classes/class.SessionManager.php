<?php

class SessionManager {
    private PDO $db;
    private ErrorLogger $logger;

    public function __construct(PDO $db, ?ErrorLogger $logger = null) {
        $this->db = $db;
        $this->logger = $logger ?? new ErrorLogger();
    }

    public function createSession(int $uid, string $method, string $ip, string $userAgent): string {
        try {
            $sessionId = bin2hex(random_bytes(32));

            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_USER_SESSIONS_ . " 
                (" . _SESSION_ID_ . ", " . _SESSION_UID_ . ", " . _SESSION_METHOD_ . ", " . _SESSION_IP_ . ", " . _SESSION_AGENT_ . ") 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$sessionId, $uid, $method, $ip, $userAgent]);

            return $sessionId;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'SESSION_CREATE');
            return '';
        }
    }

    public function closeSession(string $sessionId, string $reason): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_USER_SESSIONS_ . "
                SET " . _SESSION_ENDED_ . " = NOW(), " . _SESSION_REASON_ . " = ?
                WHERE " . _SESSION_ID_ . " = ? AND " . _SESSION_ENDED_ . " IS NULL
            ");
            $stmt->execute([$reason, $sessionId]);
            return $stmt->rowCount() > 0;
            
        } catch (Throwable $e) {
            $this->logger->logException($e, 'SESSION_CLOSE');
            return false;
        }
    }

    public function getSessionById(string $sessionId): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_USER_SESSIONS_ . " 
                WHERE " . _SESSION_ID_ . " = ?
            ");
            $stmt->execute([$sessionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'SESSION_GET_BY_ID');
            return null;
        }
    }

    public function getSessionsByUser(int $uid): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_USER_SESSIONS_ . " 
                WHERE " . _SESSION_UID_ . " = ? 
                ORDER BY " . _SESSION_STARTED_ . " DESC
            ");
            $stmt->execute([$uid]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'SESSION_GET_BY_USER');
            return [];
        }
    }

    public function getActiveSessions(int $uid): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_USER_SESSIONS_ . " 
                WHERE " . _SESSION_UID_ . " = ? 
                AND " . _SESSION_ENDED_ . " IS NULL 
                ORDER BY " . _SESSION_STARTED_ . " DESC
            ");
            $stmt->execute([$uid]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'SESSION_GET_ACTIVE');
            return [];
        }
    }
}