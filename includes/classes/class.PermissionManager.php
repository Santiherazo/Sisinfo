<?php
class PermissionManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllPermissions(): array {
        $stmt = $this->db->query("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPermission(string $module, string $action, string $description = null, int $estado = 1): bool {
        $stmt = $this->db->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_PERMISSIONS_ . " (" . _CLMN_PERM_MODULE_ . ", " . _CLMN_PERM_ACTION_ . ", " . _CLMN_PERM_DESCRIPTION_ . ", " . _CLMN_PERM_ESTADO_ . ") VALUES (?, ?, ?, ?)");
        return $stmt->execute([$module, $action, $description, $estado]);
    }

    public function updatePermission(int $permissionId, string $module, string $action, string $description = null): bool {
        $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_PERMISSIONS_ . " SET " . _CLMN_PERM_MODULE_ . " = ?, " . _CLMN_PERM_ACTION_ . " = ?, " . _CLMN_PERM_DESCRIPTION_ . " = ? WHERE " . _CLMN_PERM_ID_ . " = ?");
        return $stmt->execute([$module, $action, $description, $permissionId]);
    }

    public function deletePermission(int $permissionId): bool {
        $this->db->beginTransaction();
        try {
            // Eliminar de role_permissions
            $stmt1 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " WHERE " . _CLMN_ROLE_PERM_PERMID_ . " = ?");
            $stmt1->execute([$permissionId]);
            
            // Eliminar de user_permissions
            $stmt2 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " WHERE " . _CLMN_USER_PERM_PERMID_ . " = ?");
            $stmt2->execute([$permissionId]);
            
            // Eliminar el permiso
            $stmt3 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_ID_ . " = ?");
            $result = $stmt3->execute([$permissionId]);
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function activatePermission(int $permissionId): bool {
        return $this->updatePermissionStatus($permissionId, 1);
    }

    public function deactivatePermission(int $permissionId): bool {
        return $this->updatePermissionStatus($permissionId, 0);
    }

    private function updatePermissionStatus(int $permissionId, int $estado): bool {
        $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_PERMISSIONS_ . " SET " . _CLMN_PERM_ESTADO_ . " = ? WHERE " . _CLMN_PERM_ID_ . " = ?");
        return $stmt->execute([$estado, $permissionId]);
    }

    public function assignPermissionToRole(int $roleId, int $permissionId): bool {
        $stmt = $this->db->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " (" . _CLMN_ROLE_PERM_ROLEID_ . ", " . _CLMN_ROLE_PERM_PERMID_ . ") VALUES (?, ?)");
        return $stmt->execute([$roleId, $permissionId]);
    }

    public function removePermissionFromRole(int $roleId, int $permissionId): bool {
        $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " WHERE " . _CLMN_ROLE_PERM_ROLEID_ . " = ? AND " . _CLMN_ROLE_PERM_PERMID_ . " = ?");
        return $stmt->execute([$roleId, $permissionId]);
    }

    public function removeAllPermissionsFromRole(int $roleId): bool {
        $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " WHERE " . _CLMN_ROLE_PERM_ROLEID_ . " = ?");
        return $stmt->execute([$roleId]);
    }

    public function assignPermissionToUser(int $userId, int $permissionId): bool {
        $stmt = $this->db->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " (" . _CLMN_USER_PERM_UID_ . ", " . _CLMN_USER_PERM_PERMID_ . ") VALUES (?, ?)");
        return $stmt->execute([$userId, $permissionId]);
    }

    public function removePermissionFromUser(int $userId, int $permissionId): bool {
        $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " WHERE " . _CLMN_USER_PERM_UID_ . " = ? AND " . _CLMN_USER_PERM_PERMID_ . " = ?");
        return $stmt->execute([$userId, $permissionId]);
    }

    public function removeAllPermissionsFromUser(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " WHERE " . _CLMN_USER_PERM_UID_ . " = ?");
        return $stmt->execute([$userId]);
    }

    public function getPermissionsByRole(int $roleId): array {
        $stmt = $this->db->prepare("SELECT p.* FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON p." . _CLMN_PERM_ID_ . " = rp." . _CLMN_ROLE_PERM_PERMID_ . " WHERE rp." . _CLMN_ROLE_PERM_ROLEID_ . " = ? AND p." . _CLMN_PERM_ESTADO_ . " = 1");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionsByUser(int $userId): array {
        $stmt = $this->db->prepare("SELECT p.* FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON p." . _CLMN_PERM_ID_ . " = up." . _CLMN_USER_PERM_PERMID_ . " WHERE up." . _CLMN_USER_PERM_UID_ . " = ? AND p." . _CLMN_PERM_ESTADO_ . " = 1");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRolePermissionsWithDetails(int $roleId): array {
        $stmt = $this->db->prepare("
            SELECT r." . _CLMN_ROLE_ID_ . ", r." . _CLMN_ROLE_NAME_ . ", 
                    p." . _CLMN_PERM_ID_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . ", p." . _CLMN_PERM_DESCRIPTION_ . "
            FROM " . _TBL_WEBENGINE_ROLES_ . " r
            LEFT JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON r." . _CLMN_ROLE_ID_ . " = rp." . _CLMN_ROLE_PERM_ROLEID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_PERMISSIONS_ . " p ON rp." . _CLMN_ROLE_PERM_PERMID_ . " = p." . _CLMN_PERM_ID_ . " AND p." . _CLMN_PERM_ESTADO_ . " = 1
            WHERE r." . _CLMN_ROLE_ID_ . " = ?
        ");
        $stmt->execute([$roleId]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) return [];
        
        $roleData = [
            'role_id' => $result[0][_CLMN_ROLE_ID_],
            'role_name' => $result[0][_CLMN_ROLE_NAME_],
            'permissions' => []
        ];
        
        foreach ($result as $row) {
            if ($row[_CLMN_PERM_ID_] !== null) {
                $roleData['permissions'][] = [
                    'permission_id' => $row[_CLMN_PERM_ID_],
                    'module' => $row[_CLMN_PERM_MODULE_],
                    'action' => $row[_CLMN_PERM_ACTION_],
                    'description' => $row[_CLMN_PERM_DESCRIPTION_]
                ];
            }
        }
        
        return $roleData;
    }

    public function getUserPermissionsWithDetails(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT u." . _CORE_UID_ . ", u." . _CORE_UNOM_ . ", u." . _CORE_UEML_ . ", 
                    ud." . _DETAIL_IDNUM_ . ",
                    p." . _CLMN_PERM_ID_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . ", p." . _CLMN_PERM_DESCRIPTION_ . "
            FROM " . _TBL_WEBENGINE_U_CORE_ . " u
            LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " ud ON u." . _CORE_UID_ . " = ud." . _DETAIL_UID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON u." . _CORE_UID_ . " = up." . _CLMN_USER_PERM_UID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_PERMISSIONS_ . " p ON up." . _CLMN_USER_PERM_PERMID_ . " = p." . _CLMN_PERM_ID_ . " AND p." . _CLMN_PERM_ESTADO_ . " = 1
            WHERE u." . _CORE_UID_ . " = ?
        ");
        $stmt->execute([$userId]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) return [];
        
        $userData = [
            'user_id' => $result[0][_CORE_UID_],
            'username' => $result[0][_CORE_UNOM_],
            'email' => $result[0][_CORE_UEML_],
            'document_number' => $result[0][_DETAIL_IDNUM_],
            'permissions' => []
        ];
        
        foreach ($result as $row) {
            if ($row[_CLMN_PERM_ID_] !== null) {
                $userData['permissions'][] = [
                    'permission_id' => $row[_CLMN_PERM_ID_],
                    'module' => $row[_CLMN_PERM_MODULE_],
                    'action' => $row[_CLMN_PERM_ACTION_],
                    'description' => $row[_CLMN_PERM_DESCRIPTION_]
                ];
            }
        }
        
        return $userData;
    }

    public function getActivePermissions(): array {
        $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_ESTADO_ . " = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function userHasPermission(int $userId, string $module, string $action): bool {
        $stmt = $this->db->prepare("
            SELECT 1 FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p 
            LEFT JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON p." . _CLMN_PERM_ID_ . " = up." . _CLMN_USER_PERM_PERMID_ . " AND up." . _CLMN_USER_PERM_UID_ . " = ? 
            LEFT JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON p." . _CLMN_PERM_ID_ . " = rp." . _CLMN_ROLE_PERM_PERMID_ . " 
            LEFT JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur ON rp." . _CLMN_ROLE_PERM_ROLEID_ . " = ur." . _CLMN_USER_ROLE_RID_ . " AND ur." . _CLMN_USER_ROLE_UID_ . " = ? 
            WHERE p." . _CLMN_PERM_MODULE_ . " = ? AND p." . _CLMN_PERM_ACTION_ . " = ? AND p." . _CLMN_PERM_ESTADO_ . " = 1 
            AND (up." . _CLMN_USER_PERM_UID_ . " IS NOT NULL OR ur." . _CLMN_USER_ROLE_UID_ . " IS NOT NULL)
        ");
        $stmt->execute([$userId, $userId, $module, $action]);
        return (bool) $stmt->fetchColumn();
    }

    public function getPermissionById(int $permissionId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_ID_ . " = ?");
        $stmt->execute([$permissionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function permissionExists(string $module, string $action, ?int $excludePermissionId = null): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_MODULE_ . " = ? AND " . _CLMN_PERM_ACTION_ . " = ?";
        $params = [$module, $action];
        
        if ($excludePermissionId !== null) {
            $sql .= " AND " . _CLMN_PERM_ID_ . " != ?";
            $params[] = $excludePermissionId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function getPermissionsByModule(string $module): array {
        $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_MODULE_ . " = ? AND " . _CLMN_PERM_ESTADO_ . " = 1");
        $stmt->execute([$module]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRolesWithPermissions(): array {
        $stmt = $this->db->prepare("
            SELECT r." . _CLMN_ROLE_ID_ . ", r." . _CLMN_ROLE_NAME_ . ", r." . _CLMN_ROLE_DESC_ . ", r." . _CLMN_ROLE_ESTADO_ . ",
                p." . _CLMN_PERM_ID_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . ", 
                p." . _CLMN_PERM_DESCRIPTION_ . ", p." . _CLMN_PERM_ESTADO_ . " as permission_status
            FROM " . _TBL_WEBENGINE_ROLES_ . " r
            LEFT JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON r." . _CLMN_ROLE_ID_ . " = rp." . _CLMN_ROLE_PERM_ROLEID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_PERMISSIONS_ . " p ON rp." . _CLMN_ROLE_PERM_PERMID_ . " = p." . _CLMN_PERM_ID_ . " AND p." . _CLMN_PERM_ESTADO_ . " = 1
            ORDER BY r." . _CLMN_ROLE_NAME_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . "
        ");
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) return [];
        
        $roles = [];
        
        foreach ($result as $row) {
            $roleName = strtolower($row[_CLMN_ROLE_NAME_]);
            
            if (!isset($roles[$roleName])) {
                $roles[$roleName] = [
                    'role_id' => $row[_CLMN_ROLE_ID_],
                    'role_name' => $row[_CLMN_ROLE_NAME_],
                    'role_description' => $row[_CLMN_ROLE_DESC_],
                    'role_status' => $row[_CLMN_ROLE_ESTADO_],
                    'permissions' => []
                ];
            }
            
            if ($row[_CLMN_PERM_ID_] !== null) {
                $roles[$roleName]['permissions'][] = [
                    'permission_id' => $row[_CLMN_PERM_ID_],
                    'module' => $row[_CLMN_PERM_MODULE_],
                    'action' => $row[_CLMN_PERM_ACTION_],
                    'description' => $row[_CLMN_PERM_DESCRIPTION_],
                    'status' => $row['permission_status']
                ];
            }
        }
        
        return $roles;
    }

    public function getAllUsersWithPermissions(): array {
        $stmt = $this->db->prepare("
            SELECT u." . _CORE_UID_ . ", u." . _CORE_UNOM_ . ", u." . _CORE_UEML_ . ", u." . _CORE_USTATUS_ . ",
                ud." . _DETAIL_IDNUM_ . ", ud." . _DETAIL_FIRSTNAME_ . ", ud." . _DETAIL_LASTNAME_ . ",
                p." . _CLMN_PERM_ID_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . ", 
                p." . _CLMN_PERM_DESCRIPTION_ . ", p." . _CLMN_PERM_ESTADO_ . " as permission_status
            FROM " . _TBL_WEBENGINE_U_CORE_ . " u
            LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " ud ON u." . _CORE_UID_ . " = ud." . _DETAIL_UID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON u." . _CORE_UID_ . " = up." . _CLMN_USER_PERM_UID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_PERMISSIONS_ . " p ON up." . _CLMN_USER_PERM_PERMID_ . " = p." . _CLMN_PERM_ID_ . " AND p." . _CLMN_PERM_ESTADO_ . " = 1
            ORDER BY ud." . _DETAIL_LASTNAME_ . ", ud." . _DETAIL_FIRSTNAME_ . ", u." . _CORE_UNOM_ . ", p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . "
        ");
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) return [];
        
        $users = [];
        
        foreach ($result as $row) {
            $userId = $row[_CORE_UID_];
            
            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'user_id' => $userId,
                    'username' => $row[_CORE_UNOM_],
                    'email' => $row[_CORE_UEML_],
                    'user_status' => $row[_CORE_USTATUS_],
                    'document_number' => $row[_DETAIL_IDNUM_],
                    'first_name' => $row[_DETAIL_FIRSTNAME_],
                    'last_name' => $row[_DETAIL_LASTNAME_],
                    'full_name' => trim($row[_DETAIL_FIRSTNAME_] . ' ' . $row[_DETAIL_LASTNAME_]),
                    'permissions' => []
                ];
            }
            
            if ($row[_CLMN_PERM_ID_] !== null) {
                $users[$userId]['permissions'][] = [
                    'permission_id' => $row[_CLMN_PERM_ID_],
                    'module' => $row[_CLMN_PERM_MODULE_],
                    'action' => $row[_CLMN_PERM_ACTION_],
                    'description' => $row[_CLMN_PERM_DESCRIPTION_],
                    'status' => $row['permission_status']
                ];
            }
        }
        
        return array_values($users);
    }

    // Nuevos métodos adicionales

    public function getUsersByPermission(int $permissionId): array {
        $stmt = $this->db->prepare("
            SELECT u." . _CORE_UID_ . ", u." . _CORE_UNOM_ . ", u." . _CORE_UEML_ . ", u." . _CORE_USTATUS_ . ",
                   ud." . _DETAIL_FIRSTNAME_ . ", ud." . _DETAIL_LASTNAME_ . ", ud." . _DETAIL_IDNUM_ . "
            FROM " . _TBL_WEBENGINE_U_CORE_ . " u
            JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON u." . _CORE_UID_ . " = up." . _CLMN_USER_PERM_UID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " ud ON u." . _CORE_UID_ . " = ud." . _DETAIL_UID_ . "
            WHERE up." . _CLMN_USER_PERM_PERMID_ . " = ?
            ORDER BY ud." . _DETAIL_LASTNAME_ . ", ud." . _DETAIL_FIRSTNAME_ . "
        ");
        $stmt->execute([$permissionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRolesByPermission(int $permissionId): array {
        $stmt = $this->db->prepare("
            SELECT r." . _CLMN_ROLE_ID_ . ", r." . _CLMN_ROLE_NAME_ . ", r." . _CLMN_ROLE_DESC_ . ", r." . _CLMN_ROLE_ESTADO_ . "
            FROM " . _TBL_WEBENGINE_ROLES_ . " r
            JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON r." . _CLMN_ROLE_ID_ . " = rp." . _CLMN_ROLE_PERM_ROLEID_ . "
            WHERE rp." . _CLMN_ROLE_PERM_PERMID_ . " = ?
            ORDER BY r." . _CLMN_ROLE_NAME_ . "
        ");
        $stmt->execute([$permissionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserCompletePermissions(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.* 
            FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p
            LEFT JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON p." . _CLMN_PERM_ID_ . " = up." . _CLMN_USER_PERM_PERMID_ . " AND up." . _CLMN_USER_PERM_UID_ . " = ?
            LEFT JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON p." . _CLMN_PERM_ID_ . " = rp." . _CLMN_ROLE_PERM_PERMID_ . "
            LEFT JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur ON rp." . _CLMN_ROLE_PERM_ROLEID_ . " = ur." . _CLMN_USER_ROLE_RID_ . " AND ur." . _CLMN_USER_ROLE_UID_ . " = ?
            WHERE p." . _CLMN_PERM_ESTADO_ . " = 1 
            AND (up." . _CLMN_USER_PERM_UID_ . " IS NOT NULL OR ur." . _CLMN_USER_ROLE_UID_ . " IS NOT NULL)
            ORDER BY p." . _CLMN_PERM_MODULE_ . ", p." . _CLMN_PERM_ACTION_ . "
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionUsageStats(int $permissionId): array {
        $userCountStmt = $this->db->prepare("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " WHERE " . _CLMN_USER_PERM_PERMID_ . " = ?");
        $userCountStmt->execute([$permissionId]);
        $userCount = $userCountStmt->fetchColumn();

        $roleCountStmt = $this->db->prepare("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " WHERE " . _CLMN_ROLE_PERM_PERMID_ . " = ?");
        $roleCountStmt->execute([$permissionId]);
        $roleCount = $roleCountStmt->fetchColumn();

        return [
            'user_count' => (int)$userCount,
            'role_count' => (int)$roleCount,
            'total_assignments' => (int)$userCount + (int)$roleCount
        ];
    }
}