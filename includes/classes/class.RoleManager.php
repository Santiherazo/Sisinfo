<?php

class RoleManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createRole(string $name, string $description = '', bool $estado = true): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO " . _TBL_WEBENGINE_ROLES_ . " 
            (" . _CLMN_ROLE_NAME_ . ", " . _CLMN_ROLE_DESC_ . ", " . _CLMN_ROLE_ESTADO_ . ") 
            VALUES (?, ?, ?)"
        );
        return $stmt->execute([$name, $description, $estado]);
    }

    public function getAllRoles(bool $soloActivos = false): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_ROLES_;
        if ($soloActivos) {
            $sql .= " WHERE " . _CLMN_ROLE_ESTADO_ . " = TRUE";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRoles(int $userId, bool $soloActivos = true): array {
        $sql = "SELECT r.* FROM " . _TBL_WEBENGINE_ROLES_ . " r
                JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur 
                ON r." . _CLMN_ROLE_ID_ . " = ur." . _CLMN_USER_ROLE_RID_ . "
                WHERE ur." . _CLMN_USER_ROLE_UID_ . " = ?";
        
        if ($soloActivos) {
            $sql .= " AND r." . _CLMN_ROLE_ESTADO_ . " = TRUE";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignRoleToUser(int $userId, int $roleId): bool {
        $stmtCheck = $this->db->prepare(
            "SELECT 1 FROM " . _TBL_WEBENGINE_ROLES_ . " 
             WHERE " . _CLMN_ROLE_ID_ . " = ? AND " . _CLMN_ROLE_ESTADO_ . " = TRUE"
        );
        $stmtCheck->execute([$roleId]);
        
        if (!$stmtCheck->fetchColumn()) {
            return false;
        }
        
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO " . _TBL_WEBENGINE_USER_ROLES_ . 
            " (" . _CLMN_USER_ROLE_UID_ . ", " . _CLMN_USER_ROLE_RID_ . ") VALUES (?, ?)"
        );
        $stmt->execute([$userId, $roleId]);
        return $stmt->rowCount() > 0;
    }

    public function removeRoleFromUser(int $userId, int $roleId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM " . _TBL_WEBENGINE_USER_ROLES_ . 
            " WHERE " . _CLMN_USER_ROLE_UID_ . " = ? AND " . _CLMN_USER_ROLE_RID_ . " = ?"
        );
        return $stmt->execute([$userId, $roleId]);
    }

    public function userHasRole(int $userId, string $roleName, bool $soloActivos = true): bool {
        $sql = "SELECT 1 FROM " . _TBL_WEBENGINE_ROLES_ . " r
                JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur 
                ON r." . _CLMN_ROLE_ID_ . " = ur." . _CLMN_USER_ROLE_RID_ . "
                WHERE ur." . _CLMN_USER_ROLE_UID_ . " = ? 
                AND r." . _CLMN_ROLE_NAME_ . " = ?";
        
        if ($soloActivos) {
            $sql .= " AND r." . _CLMN_ROLE_ESTADO_ . " = TRUE";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $roleName]);
        return (bool) $stmt->fetchColumn();
    }

    public function getUsersByRoleType(string $roleName, bool $soloActivos = true): array {
        $sql = "SELECT u.* FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " u
                INNER JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur 
                ON u." . _DETAIL_UID_ . " = ur." . _CLMN_USER_ROLE_UID_ . "
                INNER JOIN " . _TBL_WEBENGINE_ROLES_ . " r 
                ON ur." . _CLMN_USER_ROLE_RID_ . " = r." . _CLMN_ROLE_ID_ . "
                WHERE r." . _CLMN_ROLE_NAME_ . " = ?";
        
        if ($soloActivos) {
            $sql .= " AND r." . _CLMN_ROLE_ESTADO_ . " = TRUE";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllStudents(bool $soloActivos = true): array {
        return $this->getUsersByRoleType('estudiante', $soloActivos);
    }

    public function getAllEvaluators(bool $soloActivos = true): array {
        return $this->getUsersByRoleType('evaluador', $soloActivos);
    }

    public function getAllTeachers(bool $soloActivos = true): array {
        return $this->getUsersByRoleType('docente', $soloActivos);
    }

    public function deleteRole(int $roleId): bool {
        $this->db->beginTransaction();
        
        try {
            $stmtDeleteRelations = $this->db->prepare(
                "DELETE FROM " . _TBL_WEBENGINE_USER_ROLES_ . " 
                WHERE " . _CLMN_USER_ROLE_RID_ . " = ?"
            );
            $stmtDeleteRelations->execute([$roleId]);
            
            $stmtDeleteRole = $this->db->prepare(
                "DELETE FROM " . _TBL_WEBENGINE_ROLES_ . " 
                WHERE " . _CLMN_ROLE_ID_ . " = ?"
            );
            $stmtDeleteRole->execute([$roleId]);
            
            $this->db->commit();
            
            return $stmtDeleteRole->rowCount() > 0;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar rol: " . $e->getMessage());
            return false;
        }
    }

    public function updateRole(int $roleId, string $name, string $description = '', ?bool $estado = null): bool {
        if ($estado !== null) {
            $stmt = $this->db->prepare(
                "UPDATE " . _TBL_WEBENGINE_ROLES_ . "
                SET " . _CLMN_ROLE_NAME_ . " = ?, " . _CLMN_ROLE_DESC_ . " = ?, " . _CLMN_ROLE_ESTADO_ . " = ?
                WHERE " . _CLMN_ROLE_ID_ . " = ?"
            );
            return $stmt->execute([$name, $description, $estado, $roleId]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE " . _TBL_WEBENGINE_ROLES_ . "
                SET " . _CLMN_ROLE_NAME_ . " = ?, " . _CLMN_ROLE_DESC_ . " = ?
                WHERE " . _CLMN_ROLE_ID_ . " = ?"
            );
            return $stmt->execute([$name, $description, $roleId]);
        }
    }

    public function setRoleEstado(int $roleId, bool $estado): bool {
        $stmt = $this->db->prepare(
            "UPDATE " . _TBL_WEBENGINE_ROLES_ . "
            SET " . _CLMN_ROLE_ESTADO_ . " = ?
            WHERE " . _CLMN_ROLE_ID_ . " = ?"
        );
        return $stmt->execute([$estado, $roleId]);
    }

    public function getRoleEstado(int $roleId): ?bool {
        $stmt = $this->db->prepare(
            "SELECT " . _CLMN_ROLE_ESTADO_ . " 
            FROM " . _TBL_WEBENGINE_ROLES_ . " 
            WHERE " . _CLMN_ROLE_ID_ . " = ?"
        );
        $stmt->execute([$roleId]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (bool)$result : null;
    }

    public function activateRole(int $roleId): bool {
        return $this->setRoleEstado($roleId, true);
    }

    public function deactivateRole(int $roleId): bool {
        return $this->setRoleEstado($roleId, false);
    }
}