<?php

class UserManager {
    private PDO $db;
    private ErrorLogger $logger;

    public function __construct(PDO $db, ?ErrorLogger $logger = null) {
        $this->db = $db;
        $this->logger = $logger ?? new ErrorLogger();
    }
    
    public function createUser(string $username, string $email, string $password, int $status = 1): ?int {
        try {
            if ($this->userExists($username) || $this->emailExists($email)) {
                throw new Exception("Usuario o correo ya existen.");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_U_CORE_ . " 
                (" . _CORE_UNOM_ . ", " . _CORE_UEML_ . ", " . _CORE_UPWD_ . ", " . _CORE_UREG_ . ", " . _CORE_USTATUS_ . ") 
                VALUES (?, ?, ?, NOW(), ?)
            ");
            $stmt->execute([$username, $email, $hashedPassword, $status]);

            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_CREATE');
            return null;
        }
    }

    public function userExists(string $username): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UNOM_ . " = ? LIMIT 1");
            $stmt->execute([$username]);
            return $stmt->fetchColumn() !== false;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_CHECK');
            return false;
        }
    }

    public function emailExists(string $email): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UEML_ . " = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() !== false;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'EMAIL_CHECK');
            return false;
        }
    }

    public function getUserById(int $uid): ?array {
        try {
            $query = "SELECT * FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UID_ . " = :uid LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_GET_BY_ID');
            return null;
        }
    }

    public function getUserByEmail(string $email): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UEML_ . " = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_GET_BY_EMAIL');
            return null;
        }
    }

    public function updateUserStatus(int $uid, string $status): bool {
        try {
            $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_U_CORE_ . " SET " . _CORE_USTATUS_ . " = ? WHERE " . _CORE_UID_ . " = ?");
            return $stmt->execute([$status, $uid]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_UPDATE_STATUS');
            return false;
        }
    }

    public function updateLastLogin(int $uid, string $ip): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_U_CORE_ . " 
                SET " . _CORE_LASTLOGIN_ . " = NOW(), " . _CORE_LASTIP_ . " = ? 
                WHERE " . _CORE_UID_ . " = ?
            ");
            return $stmt->execute([$ip, $uid]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'USER_UPDATE_LASTLOGIN');
            return false;
        }
    }

    public function updatePasswordById(int $uid, string $newPassword): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT " . _CORE_UID_ . " 
                FROM " . _TBL_WEBENGINE_U_CORE_ . " 
                WHERE " . _CORE_UID_ . " = ? 
                LIMIT 1
            ");
            $stmt->execute([$uid]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("No se encontró un usuario con este ID.");
            }

            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_U_CORE_ . " 
                SET " . _CORE_UPWD_ . " = ? 
                WHERE " . _CORE_UID_ . " = ?
            ");
            return $update->execute([$hashed, $uid]);

        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_UPDATE_PASSWORD');
            return false;
        }
    }

    public function updatePasswordByEmail(string $email, string $newPassword): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT " . _CORE_UID_ . " 
                FROM " . _TBL_WEBENGINE_U_CORE_ . " 
                WHERE " . _CORE_UEML_ . " = ? 
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("No se encontró un usuario con este correo.");
            }

            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_U_CORE_ . " 
                SET " . _CORE_UPWD_ . " = ? 
                WHERE " . _CORE_UEML_ . " = ?
            ");
            return $update->execute([$hashed, $email]);

        } catch (Throwable $e) {
            $this->logger->logException($e, 'USERMANAGER_PASSWORD_UPDATE');
            return false;
        }
    }

    public function getEmailById(int $uid): ?string {
        try {
            $stmt = $this->db->prepare("SELECT " . _CORE_UEML_ . " FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UID_ . " = ?");
            $stmt->execute([$uid]);
            $email = $stmt->fetchColumn();
            return $email ?: null;
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_GET_EMAIL');
            return null;
        }
    }

    public function updateEmailById(int $uid, string $newEmail): bool {
        try {
            $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_U_CORE_ . " SET " . _CORE_UEML_ . " = ? WHERE " . _CORE_UID_ . " = ?");
            return $stmt->execute([$newEmail, $uid]);
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_UPDATE_EMAIL');
            return false;
        }
    }

    public function deleteUser(int $uid): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_UID_ . " = ?");
            return $stmt->execute([$uid]);
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'USER_DELETE');
            return false;
        }
    }

}