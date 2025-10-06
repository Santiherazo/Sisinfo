<?php

class UserDetailsManager {
    private PDO $pdo;
    private ErrorLogger $logger;

    public function __construct(dB $db, ?ErrorLogger $logger = null) {
        $this->pdo = $db->getConnection(); // Asume que el método getConnection() retorna un PDO
        $this->logger = $logger ?? new ErrorLogger();
    }

    public function getDetails(int $uid): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = ?");
            $stmt->execute([$uid]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_DETAILS_GET');
            return null;
        }
    }

    public function insertDetails(int $uid, array $data): bool {
        try {
            $data[_DETAIL_UID_] = $uid;

            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $values = array_values($data);

            $stmt = $this->pdo->prepare("INSERT INTO " . _TBL_WEBENGINE_USER_DETAILS_ . " ($columns) VALUES ($placeholders)");
            return $stmt->execute($values);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_DETAILS_INSERT');
            return false;
        }
    }

    public function updateDetails(int $uid, array $data): bool {
        try {
            $setPart = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
            $values = array_values($data);
            $values[] = $uid;

            $stmt = $this->pdo->prepare("UPDATE " . _TBL_WEBENGINE_USER_DETAILS_ . " SET $setPart WHERE " . _DETAIL_UID_ . " = ?");
            return $stmt->execute($values);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_DETAILS_UPDATE');
            return false;
        }
    }

    public function deleteDetails(int $uid): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = ?");
            return $stmt->execute([$uid]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_DETAILS_DELETE');
            return false;
        }
    }

    public function getFullName(int $uid): ?string {
        $details = $this->getDetails($uid);
        if (!$details) return null;

        $names = [];
        if (!empty($details[_DETAIL_FIRSTNAME_])) $names[] = $details[_DETAIL_FIRSTNAME_];
        if (!empty($details[_DETAIL_LASTNAME_])) $names[] = $details[_DETAIL_LASTNAME_];
        if (!empty($details[_DETAIL_LASTNAME2_])) $names[] = $details[_DETAIL_LASTNAME2_];

        return implode(' ', $names);
    }

    public function getInstitutionalEmail(int $uid): ?string {
        try {
            $stmt = $this->pdo->prepare("SELECT " . _DETAIL_INST_EMAIL_ . " FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = ?");
            $stmt->execute([$uid]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result[_DETAIL_INST_EMAIL_] ?? null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_DETAILS_INST_EMAIL');
            return null;
        }
    }

    public function getAvatarPath(int $uid): ?string {
        try {
            $stmt = $this->pdo->prepare("SELECT " . _DETAIL_PROFILE_IMG_ . " FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = ?");
            $stmt->execute([$uid]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result[_DETAIL_PROFILE_IMG_] ?? null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_AVATAR_GET');
            return null;
        }
    }
}