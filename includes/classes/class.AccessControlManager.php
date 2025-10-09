<?php

class AccessControlManager {
    private RoleManager $roleManager;
    private PermissionManager $permissionManager;

    public function __construct(RoleManager $roleManager, PermissionManager $permissionManager) {
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
    }

    public function getUserRoles(int $userId): array {
        return $this->roleManager->getUserRoles($userId);
    }

    public function userHasRole(int $userId, string $roleName): bool {
        return in_array($roleName, $this->getUserRoles($userId), true);
    }

    public function getUserPermissions(int $userId): array {
        return $this->permissionManager->getUserPermissions($userId);
    }

    public function userHasPermission(int $userId, string $module, string $action): bool {
        return $this->permissionManager->userHasPermission($userId, $module, $action);
    }

    public function canAccess(int $userId, array $requiredRoles = [], array $requiredPermissions = []): bool {
        // Validar roles
        if (!empty($requiredRoles)) {
            foreach ($requiredRoles as $role) {
                if ($this->userHasRole($userId, $role)) return true;
            }
        }

        // Validar permisos
        if (!empty($requiredPermissions)) {
            foreach ($requiredPermissions as $perm) {
                if ($this->userHasPermission($userId, $perm['module'], $perm['action'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canAccessByRoleOnly(int $userId, array $requiredRoles): bool {
        if (empty($requiredRoles)) {
            return false; // No hay roles requeridos
        }

        foreach ($requiredRoles as $role) {
            if ($this->userHasRole($userId, $role)) {
                return true; // Tiene al menos uno de los roles requeridos
            }
        }

        return false; // No cumple con los roles requeridos
    }

}