<?php

class AuthLogger {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function logAttempt(?int $uid, string $ip, string $agent, bool $success, string $reason, string $method = 'password', int $status = 0): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_U_AUTH_ . "
                (" . _AUTH_UID_ . ", " . _AUTH_TIME_ . ", " . _AUTH_IP_ . ", " . _AUTH_AGENT_ . ", " . _AUTH_METHOD_ . ", " . _AUTH_SUCCESS_ . ", " . _AUTH_REASON_ . ", " . _AUTH_STATUS_ . ")
                VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $uid ?? 0,
                $ip,
                $agent,
                $method,
                $success ? 1 : 0,
                $reason,
                $status
            ]);
        } catch (Throwable $e) {
            (new ErrorLogger())->logException($e, 'AUTH_LOG');
        }
    }

    public function getLogsByUserId(int $uid, ?int $limit = 20): array {
        try {
            $query = "
                SELECT 
                    " . _AUTH_TIME_ . " AS time,
                    " . _AUTH_IP_ . " AS ip,
                    " . _AUTH_AGENT_ . " AS agent,
                    " . _AUTH_METHOD_ . " AS method,
                    " . _AUTH_SUCCESS_ . " AS success,
                    " . _AUTH_REASON_ . " AS reason,
                    " . _AUTH_STATUS_ . " AS status
                FROM " . _TBL_WEBENGINE_U_AUTH_ . "
                WHERE " . _AUTH_UID_ . " = ?
                ORDER BY " . _AUTH_TIME_ . " DESC
            ";

            if ($limit) {
                $query .= " LIMIT ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$uid, $limit]);
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute([$uid]);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            (new ErrorLogger())->logException($e, 'AUTH_LOG_VIEW');
            return [];
        }
    }
}
