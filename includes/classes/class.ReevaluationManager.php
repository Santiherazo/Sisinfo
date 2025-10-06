<?php

class ReevaluationManager {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function canRequestReevaluation($projectId, $userId, $lastEvaluationDate = null) {
        try {
            $changeCount = $this->getUserChangeCount($projectId, $userId);
            
            if ($changeCount === 0) {
                return true;
            }
            
            $sql = "SELECT COUNT(*) as count 
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_PROJECT_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_ESTADO_ . " IN (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId, $userId, REEVALUATION_ESTADO_PENDIENTE, REEVALUATION_ESTADO_APROBADA]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $hasActiveRequest = $result['count'] > 0;
            
            if ($lastEvaluationDate && !$this->isWithinReevaluationTime($lastEvaluationDate)) {
                return false;
            }
            
            return !$hasActiveRequest;
        } catch (PDOException $e) {
            error_log("Error checking reevaluation permission: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserChangeCount($projectId, $userId) {
        try {
            $sql = "SELECT COUNT(*) as change_count 
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_PROJECT_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId, $userId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['change_count'];
            
        } catch (PDOException $e) {
            error_log("Error getting user change count: " . $e->getMessage());
            return 0;
        }
    }
    
    public function isWithinReevaluationTime($lastEvaluationDate) {
        $fechaLimite = date('Y-m-d H:i:s', strtotime($lastEvaluationDate . ' +72 hours'));
        return time() <= strtotime($fechaLimite);
    }
    
    public function requestReevaluation($projectId, $userId, $lastEvaluationId, $lastEvaluationDate, $motivo = '') {
        try {
            if (!$this->canRequestReevaluation($projectId, $userId, $lastEvaluationDate)) {
                throw new Exception("User already has a pending or approved reevaluation for this project or time limit has expired");
            }
            
            $this->pdo->beginTransaction();
            
            $changeCount = $this->getUserChangeCount($projectId, $userId);
            $fechaLimite = $changeCount > 0 ? date('Y-m-d H:i:s', strtotime($lastEvaluationDate . ' +72 hours')) : null;
            
            $sql = "INSERT INTO " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    (" . _CLMN_REEVALUATION_PROJECT_ID_ . ", 
                     " . _CLMN_REEVALUATION_EVALUADOR_UID_ . ", 
                     " . _CLMN_REEVALUATION_ULTIMA_EVALUACION_ID_ . ", 
                     " . _CLMN_REEVALUATION_FECHA_ULTIMA_EVALUACION_ . ", 
                     " . _CLMN_REEVALUATION_FECHA_LIMITE_ . ", 
                     " . _CLMN_REEVALUATION_MOTIVO_SOLICITUD_ . ", 
                     " . _CLMN_REEVALUATION_ESTADO_ . ") 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $projectId, 
                $userId, 
                $lastEvaluationId,
                $lastEvaluationDate,
                $fechaLimite,
                $motivo,
                REEVALUATION_ESTADO_PENDIENTE
            ]);
            
            $this->pdo->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error requesting reevaluation: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPendingReevaluations($projectId = null) {
        try {
            $sql = "SELECT r.*, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title, 
                           u." . _CORE_UNOM_ . " as evaluador_username
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " r
                    LEFT JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . _CLMN_REEVALUATION_PROJECT_ID_ . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
                    LEFT JOIN " . _TBL_WEBENGINE_U_CORE_ . " u ON r." . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = u." . _CORE_UID_ . "
                    WHERE r." . _CLMN_REEVALUATION_ESTADO_ . " = ?";
            
            $params = [REEVALUATION_ESTADO_PENDIENTE];
            if ($projectId) {
                $sql .= " AND r." . _CLMN_REEVALUATION_PROJECT_ID_ . " = ?";
                $params[] = $projectId;
            }
            
            $sql .= " ORDER BY r." . _CLMN_REEVALUATION_FECHA_SOLICITUD_ . " DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting pending reevaluations: " . $e->getMessage());
            return [];
        }
    }
    
    public function approveReevaluation($reevaluationId, $adminComments = '') {
        try {
            $sql = "UPDATE " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    SET " . _CLMN_REEVALUATION_ESTADO_ . " = ?, 
                        " . _CLMN_REEVALUATION_COMENTARIOS_ADMIN_ . " = ?,
                        " . _CLMN_REEVALUATION_UPDATED_AT_ . " = CURRENT_TIMESTAMP
                    WHERE " . _CLMN_REEVALUATION_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_ESTADO_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                REEVALUATION_ESTADO_APROBADA, 
                $adminComments, 
                $reevaluationId, 
                REEVALUATION_ESTADO_PENDIENTE
            ]);
            
        } catch (PDOException $e) {
            error_log("Error approving reevaluation: " . $e->getMessage());
            return false;
        }
    }
    
    public function rejectReevaluation($reevaluationId, $adminComments = '') {
        try {
            $sql = "UPDATE " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    SET " . _CLMN_REEVALUATION_ESTADO_ . " = ?, 
                        " . _CLMN_REEVALUATION_COMENTARIOS_ADMIN_ . " = ?,
                        " . _CLMN_REEVALUATION_UPDATED_AT_ . " = CURRENT_TIMESTAMP
                    WHERE " . _CLMN_REEVALUATION_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_ESTADO_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                REEVALUATION_ESTADO_RECHAZADA, 
                $adminComments, 
                $reevaluationId, 
                REEVALUATION_ESTADO_PENDIENTE
            ]);
            
        } catch (PDOException $e) {
            error_log("Error rejecting reevaluation: " . $e->getMessage());
            return false;
        }
    }
    
    public function markAsCompleted($reevaluationId) {
        try {
            $reevaluation = $this->getReevaluationById($reevaluationId);
            if (!$reevaluation || $reevaluation[_CLMN_REEVALUATION_ESTADO_] !== REEVALUATION_ESTADO_APROBADA) {
                return false;
            }
            
            $sql = "UPDATE " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    SET " . _CLMN_REEVALUATION_ESTADO_ . " = ?, 
                        " . _CLMN_REEVALUATION_UPDATED_AT_ . " = CURRENT_TIMESTAMP
                    WHERE " . _CLMN_REEVALUATION_ID_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([REEVALUATION_ESTADO_COMPLETADA, $reevaluationId]);
            
        } catch (PDOException $e) {
            error_log("Error marking reevaluation as completed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserReevaluations($userId, $projectId = null) {
        try {
            $sql = "SELECT r.*, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " r
                    LEFT JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . _CLMN_REEVALUATION_PROJECT_ID_ . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
                    WHERE r." . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = ?";
            
            $params = [$userId];
            if ($projectId) {
                $sql .= " AND r." . _CLMN_REEVALUATION_PROJECT_ID_ . " = ?";
                $params[] = $projectId;
            }
            
            $sql .= " ORDER BY r." . _CLMN_REEVALUATION_FECHA_SOLICITUD_ . " DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting user reevaluations: " . $e->getMessage());
            return [];
        }
    }
    
    public function canUserReevaluate($projectId, $userId) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_PROJECT_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_ESTADO_ . " = ?
                    AND " . _CLMN_REEVALUATION_FECHA_LIMITE_ . " >= NOW()";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId, $userId, REEVALUATION_ESTADO_APROBADA]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error checking reevaluation access: " . $e->getMessage());
            return false;
        }
    }
    
    public function getReevaluationStatus($projectId, $userId) {
        try {
            $sql = "SELECT * FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_PROJECT_ID_ . " = ? 
                    AND " . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = ? 
                    ORDER BY " . _CLMN_REEVALUATION_FECHA_SOLICITUD_ . " DESC 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId, $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting reevaluation status: " . $e->getMessage());
            return null;
        }
    }
    
    public function getReevaluationById($reevaluationId) {
        try {
            $sql = "SELECT r.*, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                           u." . _CORE_UNOM_ . " as evaluador_username
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " r
                    LEFT JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . _CLMN_REEVALUATION_PROJECT_ID_ . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
                    LEFT JOIN " . _TBL_WEBENGINE_U_CORE_ . " u ON r." . _CLMN_REEVALUATION_EVALUADOR_UID_ . " = u." . _CORE_UID_ . "
                    WHERE r." . _CLMN_REEVALUATION_ID_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$reevaluationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting reevaluation by ID: " . $e->getMessage());
            return null;
        }
    }
    
    public function deleteReevaluation($reevaluationId) {
        try {
            $sql = "DELETE FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_ID_ . " = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$reevaluationId]);
            
        } catch (PDOException $e) {
            error_log("Error deleting reevaluation: " . $e->getMessage());
            return false;
        }
    }
    
    public function getExpiredReevaluations() {
        try {
            $sql = "SELECT * FROM " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                    WHERE " . _CLMN_REEVALUATION_ESTADO_ . " = ? 
                    AND " . _CLMN_REEVALUATION_FECHA_LIMITE_ . " < NOW()";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([REEVALUATION_ESTADO_APROBADA]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting expired reevaluations: " . $e->getMessage());
            return [];
        }
    }
    
    public function autoExpireReevaluations() {
        try {
            $expiredReevaluations = $this->getExpiredReevaluations();
            $expiredCount = 0;
            
            foreach ($expiredReevaluations as $reevaluation) {
                $sql = "UPDATE " . _TBL_WEBENGINE_REEVALUATIONS_ . " 
                        SET " . _CLMN_REEVALUATION_ESTADO_ . " = ?,
                            " . _CLMN_REEVALUATION_COMENTARIOS_ADMIN_ . " = CONCAT(COALESCE(" . _CLMN_REEVALUATION_COMENTARIOS_ADMIN_ . ", ''), ' [EXPIRADO AUTOMÁTICAMENTE]'),
                            " . _CLMN_REEVALUATION_UPDATED_AT_ . " = CURRENT_TIMESTAMP
                        WHERE " . _CLMN_REEVALUATION_ID_ . " = ?";
                
                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute([REEVALUATION_ESTADO_RECHAZADA, $reevaluation[_CLMN_REEVALUATION_ID_]])) {
                    $expiredCount++;
                }
            }
            
            return $expiredCount;
            
        } catch (PDOException $e) {
            error_log("Error auto-expiring reevaluations: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getReevaluationStats($projectId = null) {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN " . _CLMN_REEVALUATION_ESTADO_ . " = ? THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN " . _CLMN_REEVALUATION_ESTADO_ . " = ? THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN " . _CLMN_REEVALUATION_ESTADO_ . " = ? THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN " . _CLMN_REEVALUATION_ESTADO_ . " = ? THEN 1 ELSE 0 END) as completadas
                    FROM " . _TBL_WEBENGINE_REEVALUATIONS_;
            
            $params = [
                REEVALUATION_ESTADO_PENDIENTE,
                REEVALUATION_ESTADO_APROBADA,
                REEVALUATION_ESTADO_RECHAZADA,
                REEVALUATION_ESTADO_COMPLETADA
            ];
            
            if ($projectId) {
                $sql .= " WHERE " . _CLMN_REEVALUATION_PROJECT_ID_ . " = ?";
                $params[] = $projectId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting reevaluation stats: " . $e->getMessage());
            return null;
        }
    }
}