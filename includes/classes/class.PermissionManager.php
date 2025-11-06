<?php
class PermissionManager {
    private PDO $db;
    private ErrorLogger $errorLogger;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->errorLogger = new ErrorLogger();
    }

    private function getLastError(): string {
        $errorInfo = $this->db->errorInfo();
        return $errorInfo[2] ?? 'Error desconocido';
    }

    public function getAllPermissions(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function createPermission(string $module, string $action, string $description = null, int $estado = 1): array {
        try {
            if (empty($module) || empty($action)) {
                $this->errorLogger->log("Intento de crear permiso con módulo o acción vacíos", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'Módulo y acción son requeridos'];
            }

            if ($this->permissionExists($module, $action)) {
                $this->errorLogger->log("Permiso ya existe: $module.$action", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso ya existe'];
            }

            $stmt = $this->db->prepare("INSERT INTO " . _TBL_WEBENGINE_PERMISSIONS_ . " 
                (" . _CLMN_PERM_MODULE_ . ", " . _CLMN_PERM_ACTION_ . ", " . _CLMN_PERM_DESCRIPTION_ . ", " . _CLMN_PERM_ESTADO_ . ") 
                VALUES (?, ?, ?, ?)");
            
            $success = $stmt->execute([$module, $action, $description, $estado]);
            
            if ($success && $stmt->rowCount() > 0) {
                $permissionId = $this->db->lastInsertId();
                $this->errorLogger->log("Permiso creado exitosamente: $module.$action (ID: $permissionId)", __FILE__, __LINE__);
                return [
                    'success' => true, 
                    'message' => 'Permiso creado exitosamente',
                    'permission_id' => $permissionId
                ];
            } else {
                $errorMsg = 'No se pudo crear el permiso: ' . $this->getLastError();
                $this->errorLogger->logDatabaseError($errorMsg, __FILE__, __LINE__);
                return ['success' => false, 'message' => $errorMsg];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function updatePermission(int $permissionId, string $module, string $action, string $description = null): array {
        try {
            if (empty($module) || empty($action)) {
                $this->errorLogger->log("Intento de actualizar permiso con módulo o acción vacíos", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'Módulo y acción son requeridos'];
            }

            $existingPermission = $this->getPermissionById($permissionId);
            if (!$existingPermission) {
                $this->errorLogger->log("Intento de actualizar permiso inexistente: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso no existe'];
            }

            if ($this->permissionExists($module, $action, $permissionId)) {
                $this->errorLogger->log("Ya existe otro permiso con el mismo módulo y acción: $module.$action", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'Ya existe otro permiso con el mismo módulo y acción'];
            }

            $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_PERMISSIONS_ . " 
                SET " . _CLMN_PERM_MODULE_ . " = ?, " . _CLMN_PERM_ACTION_ . " = ?, " . _CLMN_PERM_DESCRIPTION_ . " = ? 
                WHERE " . _CLMN_PERM_ID_ . " = ?");
            
            $success = $stmt->execute([$module, $action, $description, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->errorLogger->log("Permiso actualizado exitosamente: ID $permissionId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso actualizado exitosamente'];
            } else {
                $this->errorLogger->log("No se realizaron cambios al actualizar permiso: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se realizaron cambios o el permiso no existe'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function deletePermission(int $permissionId): array {
        try {
            $existingPermission = $this->getPermissionById($permissionId);
            if (!$existingPermission) {
                $this->errorLogger->log("Intento de eliminar permiso inexistente: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso no existe'];
            }

            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " 
                WHERE " . _CLMN_ROLE_PERM_PERMID_ . " = ?");
            $stmt1->execute([$permissionId]);

            $stmt2 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " 
                WHERE " . _CLMN_USER_PERM_PERMID_ . " = ?");
            $stmt2->execute([$permissionId]);

            $stmt3 = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " 
                WHERE " . _CLMN_PERM_ID_ . " = ?");
            $success = $stmt3->execute([$permissionId]);
            
            if ($success && $stmt3->rowCount() > 0) {
                $this->db->commit();
                $this->errorLogger->log("Permiso eliminado exitosamente: ID $permissionId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso eliminado exitosamente'];
            } else {
                $this->db->rollBack();
                $this->errorLogger->logDatabaseError("No se pudo eliminar el permiso: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se pudo eliminar el permiso'];
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->errorLogger->logException($e, 'DATABASE');
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function activatePermission(int $permissionId): array {
        return $this->updatePermissionStatus($permissionId, 1);
    }

    public function deactivatePermission(int $permissionId): array {
        return $this->updatePermissionStatus($permissionId, 0);
    }

    private function updatePermissionStatus(int $permissionId, int $estado): array {
        try {
            $existingPermission = $this->getPermissionById($permissionId);
            if (!$existingPermission) {
                $this->errorLogger->log("Intento de cambiar estado de permiso inexistente: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso no existe'];
            }

            $stmt = $this->db->prepare("UPDATE " . _TBL_WEBENGINE_PERMISSIONS_ . " 
                SET " . _CLMN_PERM_ESTADO_ . " = ? 
                WHERE " . _CLMN_PERM_ID_ . " = ?");
            
            $success = $stmt->execute([$estado, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $statusText = $estado == 1 ? 'activado' : 'desactivado';
                $this->errorLogger->log("Permiso $statusText: ID $permissionId", __FILE__, __LINE__);
                return ['success' => true, 'message' => "Permiso {$statusText} exitosamente"];
            } else {
                $this->errorLogger->log("No se realizaron cambios al cambiar estado del permiso: ID $permissionId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se realizaron cambios'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function assignPermissionToRole(int $roleId, int $permissionId): array {
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " 
                (" . _CLMN_ROLE_PERM_ROLEID_ . ", " . _CLMN_ROLE_PERM_PERMID_ . ") VALUES (?, ?)");
            $success = $stmt->execute([$roleId, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->errorLogger->log("Permiso asignado al rol: Permiso ID $permissionId -> Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso asignado al rol exitosamente'];
            } else {
                $this->errorLogger->log("El permiso ya estaba asignado al rol: Permiso ID $permissionId -> Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso ya estaba asignado al rol'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function removePermissionFromRole(int $roleId, int $permissionId): array {
        try {
            $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " 
                WHERE " . _CLMN_ROLE_PERM_ROLEID_ . " = ? AND " . _CLMN_ROLE_PERM_PERMID_ . " = ?");
            $success = $stmt->execute([$roleId, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->errorLogger->log("Permiso removido del rol: Permiso ID $permissionId <- Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso removido del rol exitosamente'];
            } else {
                $this->errorLogger->log("No se encontró la asignación para remover: Permiso ID $permissionId <- Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se encontró la asignación para remover'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function removeAllPermissionsFromRole(int $roleId): array {
        try {
            $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " 
                WHERE " . _CLMN_ROLE_PERM_ROLEID_ . " = ?");
            $success = $stmt->execute([$roleId]);
            
            if ($success) {
                $this->errorLogger->log("Todos los permisos removidos del rol: Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Todos los permisos removidos del rol exitosamente'];
            } else {
                $this->errorLogger->logDatabaseError("No se pudieron remover los permisos del rol: Rol ID $roleId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se pudieron remover los permisos'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function assignPermissionToUser(int $userId, int $permissionId): array {
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " 
                (" . _CLMN_USER_PERM_UID_ . ", " . _CLMN_USER_PERM_PERMID_ . ") VALUES (?, ?)");
            $success = $stmt->execute([$userId, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->errorLogger->log("Permiso asignado al usuario: Permiso ID $permissionId -> Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso asignado al usuario exitosamente'];
            } else {
                $this->errorLogger->log("El permiso ya estaba asignado al usuario: Permiso ID $permissionId -> Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'El permiso ya estaba asignado al usuario'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function removePermissionFromUser(int $userId, int $permissionId): array {
        try {
            $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " 
                WHERE " . _CLMN_USER_PERM_UID_ . " = ? AND " . _CLMN_USER_PERM_PERMID_ . " = ?");
            $success = $stmt->execute([$userId, $permissionId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->errorLogger->log("Permiso removido del usuario: Permiso ID $permissionId <- Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Permiso removido del usuario exitosamente'];
            } else {
                $this->errorLogger->log("No se encontró la asignación para remover: Permiso ID $permissionId <- Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se encontró la asignación para remover'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function removeAllPermissionsFromUser(int $userId): array {
        try {
            $stmt = $this->db->prepare("DELETE FROM " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " 
                WHERE " . _CLMN_USER_PERM_UID_ . " = ?");
            $success = $stmt->execute([$userId]);
            
            if ($success) {
                $this->errorLogger->log("Todos los permisos removidos del usuario: Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => true, 'message' => 'Todos los permisos removidos del usuario exitosamente'];
            } else {
                $this->errorLogger->logDatabaseError("No se pudieron remover los permisos del usuario: Usuario ID $userId", __FILE__, __LINE__);
                return ['success' => false, 'message' => 'No se pudieron remover los permisos'];
            }
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function getPermissionsByRole(int $roleId): array {
        try {
            $stmt = $this->db->prepare("SELECT p.* FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p 
                JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON p." . _CLMN_PERM_ID_ . " = rp." . _CLMN_ROLE_PERM_PERMID_ . " 
                WHERE rp." . _CLMN_ROLE_PERM_ROLEID_ . " = ? AND p." . _CLMN_PERM_ESTADO_ . " = 1");
            $stmt->execute([$roleId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getPermissionsByUser(int $userId): array {
        try {
            $stmt = $this->db->prepare("SELECT p.* FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " p 
                JOIN " . _TBL_WEBENGINE_USER_PERMISSIONS_ . " up ON p." . _CLMN_PERM_ID_ . " = up." . _CLMN_USER_PERM_PERMID_ . " 
                WHERE up." . _CLMN_USER_PERM_UID_ . " = ? AND p." . _CLMN_PERM_ESTADO_ . " = 1");
            $stmt->execute([$userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getRolePermissionsWithDetails(int $roleId): array {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getUserPermissionsWithDetails(int $userId): array {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getActivePermissions(): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_ESTADO_ . " = 1");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function userHasPermission(int $userId, string $module, string $action): bool {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getPermissionById(int $permissionId): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_ID_ . " = ?");
            $stmt->execute([$permissionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return null;
        }
    }

    public function permissionExists(string $module, string $action, ?int $excludePermissionId = null): bool {
        try {
            $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_MODULE_ . " = ? AND " . _CLMN_PERM_ACTION_ . " = ?";
            $params = [$module, $action];
            
            if ($excludePermissionId !== null) {
                $sql .= " AND " . _CLMN_PERM_ID_ . " != ?";
                $params[] = $excludePermissionId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getPermissionsByModule(string $module): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM " . _TBL_WEBENGINE_PERMISSIONS_ . " WHERE " . _CLMN_PERM_MODULE_ . " = ? AND " . _CLMN_PERM_ESTADO_ . " = 1");
            $stmt->execute([$module]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getAllRolesWithPermissions(): array {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getAllUsersWithPermissions(): array {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getUsersByPermission(int $permissionId): array {
        try {
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
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getRolesByPermission(int $permissionId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT r." . _CLMN_ROLE_ID_ . ", r." . _CLMN_ROLE_NAME_ . ", r." . _CLMN_ROLE_DESC_ . ", r." . _CLMN_ROLE_ESTADO_ . "
                FROM " . _TBL_WEBENGINE_ROLES_ . " r
                JOIN " . _TBL_WEBENGINE_ROLE_PERMISSIONS_ . " rp ON r." . _CLMN_ROLE_ID_ . " = rp." . _CLMN_ROLE_PERM_ROLEID_ . "
                WHERE rp." . _CLMN_ROLE_PERM_PERMID_ . " = ?
                ORDER BY r." . _CLMN_ROLE_NAME_ . "
            ");
            $stmt->execute([$permissionId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getUserCompletePermissions(int $userId): array {
        try {
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
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getPermissionUsageStats(int $permissionId): array {
        try {
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
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return ['user_count' => 0, 'role_count' => 0, 'total_assignments' => 0];
        }
    }

    public function checkDatabaseConnection(): bool {
        try {
            $this->db->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            $this->errorLogger->logDatabaseError($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function verifyTableStructure(): array {
        $results = [];
        
        try {
            $stmt = $this->db->query("DESCRIBE " . _TBL_WEBENGINE_PERMISSIONS_);
            $results['permissions_table'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errorLogger->logException($e, 'DATABASE');
            $results['permissions_table_error'] = $e->getMessage();
        }

        return $results;
    }

    public function getErrorLogs(string $type = 'DATABASE', int $limit = 50): array {
        return $this->errorLogger->getLogLines($type, $limit);
    }

    public function getErrorSummary(): array {
        return $this->errorLogger->getLogSummary();
    }
}