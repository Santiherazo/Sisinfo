<?php

class LoginPolicyValidator {
    private array $config;
    private IpBlockManager $ipBlockManager;

    public function __construct(array $config, IpBlockManager $ipBlockManager) {
        $this->config = $config;
        $this->ipBlockManager = $ipBlockManager;
    }

    /**
     * Verifica si el acceso está dentro del horario permitido.
     */
    public function isWithinAllowedHours(): bool {
        if (empty($this->config['enable_login_hours']) || (int)$this->config['enable_login_hours'] !== 1) {
            return true;
        }

        if (empty($this->config['allowed_login_hours'])) {
            return true;
        }

        [$start, $end] = explode('-', $this->config['allowed_login_hours']);
        $now = date('H:i');

        return $now >= $start && $now <= $end;
    }

    /**
     * Determina si la seguridad de intentos fallidos está habilitada.
     */
    public function isLoginSecurityEnabled(): bool {
        return !empty($this->config['enable_failed_login_security']) && (int)$this->config['enable_failed_login_security'] === 1;
    }

    /**
     * Procesa un intento fallido, actualizando contadores y bloqueos.
     */
    public function handleFailedLogin(string $ip, ?int $uid = null): void {
        if (!$this->isLoginSecurityEnabled()) return;

        $maxAttempts     = (int)($this->config['max_login_attempts'] ?? 5);
        $timeout         = (int)($this->config['failed_login_timeout'] ?? 15); // No acumulativo
        $lockoutDuration = (int)($this->config['lockout_duration'] ?? 30);     // Base multiplicador

        $this->ipBlockManager->trackFailure(
            $ip,
            $uid,
            $maxAttempts,
            $timeout,
            $lockoutDuration,
            $this->config
        );
    }

    /**
     * Limpia los intentos fallidos si el inicio fue exitoso.
     */
    public function clearFailedAttempts(string $ip): void {
        if ($this->isLoginSecurityEnabled()) {
            $this->ipBlockManager->clear($ip);
        }
    }

    /**
     * Verifica si una IP está actualmente bloqueada temporalmente.
     */
    public function isIpTemporarilyBlocked(string $ip): bool {
        if (!$this->isLoginSecurityEnabled()) return false;
        return $this->ipBlockManager->isBlocked($ip);
    }

    /**
     * Verifica si el usuario está marcado como permanentemente bloqueado (estado).
     */
    public function isAccountPermanentlyBlocked(array $user): bool {
        $permStatus = (int)($this->config['user_status_perm_block'] ?? 3);
        return (int)($user[_CORE_USTATUS_] ?? 0) === $permStatus;
    }

    /**
     * Verifica si el usuario está marcado como temporalmente bloqueado (estado).
     */
    public function isAccountTemporarilyBlocked(array $user): bool {
        $tempStatus = (int)($this->config['user_status_temp_block'] ?? 2);
        return (int)($user[_CORE_USTATUS_] ?? 0) === $tempStatus;
    }

    /**
     * Verifica si el estado del usuario es activo y tiene permiso.
     */
    public function isAccountActive(array $user): bool {
        $activeStatus = (int)($this->config['user_status_active'] ?? 1);
        return (int)($user[_CORE_USTATUS_] ?? 0) === $activeStatus;
    }

    /**
     * Devuelve todos los estados disponibles como arreglo para administración.
     */
    public function getAccountStatusOptions(): array {
        return [
            'active'      => (int)($this->config['user_status_active'] ?? 1),
            'temp_block'  => (int)($this->config['user_status_temp_block'] ?? 2),
            'perm_block'  => (int)($this->config['user_status_perm_block'] ?? 3)
        ];
    }
}