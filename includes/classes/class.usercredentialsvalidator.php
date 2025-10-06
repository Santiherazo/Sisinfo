<?php

define('_USER_STATUS_ACTIVE_', 1);         // Activo
define('_USER_STATUS_TEMP_BLOCK_', 2);     // Bloqueado temporal
define('_USER_STATUS_PERM_BLOCK_', 3);     // Bloqueado permanente

class UserCredentialsValidator {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Valida las credenciales de usuario.
     */
    public function validate(string $usernameOrEmail, string $password, string $ip, string $agent): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_U_CORE_ . "
                WHERE " . _CORE_UNOM_ . " = :username
                   OR " . _CORE_UEML_ . " = :email
                LIMIT 1
            ");
            $stmt->execute([
                ':username' => $usernameOrEmail,
                ':email'    => $usernameOrEmail
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [false, "Usuario no encontrado.", null];
            }

            if (!password_verify($password, $user[_CORE_UPWD_])) {
                return [false, "Contraseña incorrecta.", $user[_CORE_UID_] ?? null];
            }

            $status = (int)($user[_CORE_USTATUS_] ?? 0);

            switch ($status) {
                case _USER_STATUS_ACTIVE_:
                    return [true, $user, $user[_CORE_UID_]];
                case _USER_STATUS_TEMP_BLOCK_:
                    return [false, "Tu cuenta está bloqueada temporalmente. Intenta más tarde o contacta al administrador.", $user[_CORE_UID_]];
                case _USER_STATUS_PERM_BLOCK_:
                    return [false, "Tu cuenta ha sido bloqueada permanentemente. Contacta al soporte.", $user[_CORE_UID_]];
                default:
                    return [false, "La cuenta está inactiva o en estado desconocido.", $user[_CORE_UID_]];
            }

        } catch (Throwable $e) {
            (new ErrorLogger())->logException($e, 'USER_VALIDATION');
            return [false, "Error inesperado durante la validación.", null];
        }
    }

    /**
     * Actualiza la información de último inicio de sesión.
     */
    public function updateLastLogin(int $uid, string $ip): void {
        $stmt = $this->db->prepare("
            UPDATE " . _TBL_WEBENGINE_U_CORE_ . "
            SET last_login = NOW(), last_ip = ?, " . _CORE_UONLINE_ . " = 1
            WHERE " . _CORE_UID_ . " = ?
        ");
        $stmt->execute([$ip, $uid]);
    }

    /**
     * Marca al usuario como desconectado.
     */
    public function markOffline(string $username): void {
        $stmt = $this->db->prepare("
            UPDATE " . _TBL_WEBENGINE_U_CORE_ . "
            SET " . _CORE_UONLINE_ . " = 0
            WHERE " . _CORE_UNOM_ . " = ?
        ");
        $stmt->execute([$username]);
    }
}