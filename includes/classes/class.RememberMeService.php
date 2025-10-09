<?php

class RememberMeService {
    private PDO $db;
    private ErrorLogger $logger;
    private string $cookieName = 'remember_token';

    public function __construct(PDO $db, ?ErrorLogger $logger = null) {
        $this->db = $db;
        $this->logger = $logger ?? new ErrorLogger();
    }

    /**
     * Crea un token de acceso persistente (recordarme)
     */
    public function createToken(int $uid, string $ip, string $userAgent): string {
        try {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_TOKENS_ . " 
                (" . _TOKEN_ID_ . ", " . _TOKEN_UID_ . ", " . _TOKEN_IP_ . ", " . _TOKEN_AGENT_ . ", " . _TOKEN_EXPIRES_ . ") 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$token, $uid, $ip, $userAgent, $expiresAt]);

            setcookie($this->cookieName, $token, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            return $token;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_CREATE');
            return '';
        }
    }

    /**
     * Valida el token actual desde la cookie
     */
    public function validateTokenFromCookie(string $ip, string $userAgent): ?array {
        if (!isset($_COOKIE[$this->cookieName])) return null;

        return $this->validateToken($_COOKIE[$this->cookieName], $ip, $userAgent);
    }

    /**
     * Valida un token directamente
     */
    public function validateToken(string $token, string $ip, string $userAgent): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                WHERE " . _TOKEN_ID_ . " = ? 
                AND " . _TOKEN_EXPIRES_ . " > NOW()
            ");
            $stmt->execute([$token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row[_TOKEN_IP_] === $ip && $row[_TOKEN_AGENT_] === $userAgent) {
                $this->updateLastUsed($token);
                return $row;
            }

            return null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_VALIDATE');
            return null;
        }
    }

    /**
     * Marca la última vez que se usó el token
     */
    private function updateLastUsed(string $token): void {
        try {
            $stmt = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_TOKENS_ . " 
                SET " . _TOKEN_LAST_USED_ . " = NOW() 
                WHERE " . _TOKEN_ID_ . " = ?
            ");
            $stmt->execute([$token]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_LAST_USED');
        }
    }

    /**
     * Elimina el token actual de la cookie y de la base de datos
     */
    public function clearToken(): void {
        if (isset($_COOKIE[$this->cookieName])) {
            $token = $_COOKIE[$this->cookieName];

            try {
                $stmt = $this->db->prepare("
                    DELETE FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                    WHERE " . _TOKEN_ID_ . " = ?
                ");
                $stmt->execute([$token]);
            } catch (Throwable $e) {
                $this->logger->logException($e, 'REMEMBERME_CLEAR');
            }

            setcookie($this->cookieName, '', time() - 3600, '/', '', true, true);
        }
    }

    /**
     * Elimina todos los tokens expirados
     */
    public function purgeExpiredTokens(): int {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                WHERE " . _TOKEN_EXPIRES_ . " < NOW()
            ");
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_PURGE');
            return 0;
        }
    }

    /**
     * Elimina todos los tokens de un usuario
     */
    public function deleteTokensByUser(int $uid): void {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                WHERE " . _TOKEN_UID_ . " = ?
            ");
            $stmt->execute([$uid]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_DELETE_BY_USER');
        }
    }

    /**
     * Retorna todos los tokens activos de un usuario
     */
    public function getActiveTokensByUser(int $uid): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                WHERE " . _TOKEN_UID_ . " = ? 
                AND " . _TOKEN_EXPIRES_ . " > NOW()
                ORDER BY " . _TOKEN_CREATED_ . " DESC
            ");
            $stmt->execute([$uid]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_GET_ACTIVE');
            return [];
        }
    }

    /**
     * Verifica si un usuario tiene al menos un token válido
     */
    public function hasValidToken(int $uid): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT 1 FROM " . _TBL_WEBENGINE_TOKENS_ . " 
                WHERE " . _TOKEN_UID_ . " = ? 
                AND " . _TOKEN_EXPIRES_ . " > NOW()
                LIMIT 1
            ");
            $stmt->execute([$uid]);
            return (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            $this->logger->logException($e, 'REMEMBERME_HAS_VALID');
            return false;
        }
    }
}