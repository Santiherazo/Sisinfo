<?php

class NotificationManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function send(int $userId, string $title, string $message = '', string $type = 'info', ?string $url = null): bool {
        $stmt = $this->db->prepare("
            INSERT INTO " . _TBL_WEBENGINE_NOTIFICATIONS_ . " 
            (" . _CLMN_NOTIFY_UID_ . ", " . _CLMN_NOTIFY_TITLE_ . ", " . _CLMN_NOTIFY_MESSAGE_ . ", " . _CLMN_NOTIFY_TYPE_ . ", " . _CLMN_NOTIFY_URL_ . ") 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $title, $message, $type, $url]);
    }

    public function getUnread(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . _TBL_WEBENGINE_NOTIFICATIONS_ . " 
            WHERE " . _CLMN_NOTIFY_UID_ . " = ? AND " . _CLMN_NOTIFY_SEEN_ . " = 0
            ORDER BY " . _CLMN_NOTIFY_CREATED_ . " DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $notificationId): bool {
        $stmt = $this->db->prepare("
            UPDATE " . _TBL_WEBENGINE_NOTIFICATIONS_ . " 
            SET " . _CLMN_NOTIFY_SEEN_ . " = 1 
            WHERE " . _CLMN_NOTIFY_ID_ . " = ?
        ");
        return $stmt->execute([$notificationId]);
    }
}