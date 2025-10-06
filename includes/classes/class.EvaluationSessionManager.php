<?php

class EvaluationSessionManager {
    private PDO $pdo;
    private ErrorLogger $logger;

    public function __construct(PDO $pdo, ErrorLogger $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function createSession(int $projectId, string $token, DateTime|string $inicio, int $duracion, int $userId, bool $cierreManual = false): int {
        try {
            if (is_string($inicio)) {
                $inicio = new DateTime($inicio);
            }

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO " . EVALUATION_SESSIONS_TABLE . " 
                (" . EVALUATION_SESSION_FIELD_PROJECT_ID . ", " . EVALUATION_SESSION_FIELD_CREADO_POR . ", " . 
                EVALUATION_SESSION_FIELD_TOKEN . ", " . EVALUATION_SESSION_FIELD_START . ", " . 
                EVALUATION_SESSION_FIELD_DURATION . ", " . EVALUATION_SESSION_FIELD_STATE . ", " . 
                EVALUATION_SESSION_FIELD_MANUAL_CLOSE . ") 
                VALUES (:project_id, :creado_por, :token_acceso, :inicio, :duracion, 'activa', :cierre_manual)
            ");
            
            $stmt->execute([
                'project_id' => $projectId,
                'creado_por' => $userId,
                'token_acceso' => $token,
                'inicio' => $inicio->format('Y-m-d H:i:s'),
                'duracion' => $duracion,
                'cierre_manual' => $cierreManual ? 1 : 0
            ]);
            
            $sessionId = (int)$this->pdo->lastInsertId();
            $this->addUserToSession($userId, $sessionId);

            $this->pdo->commit();
            $this->logger->log("Sesión creada exitosamente: ID $sessionId para proyecto $projectId", __FILE__, __LINE__);
            
            return $sessionId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->logger->logDatabaseError("Error al crear sesión: " . $e->getMessage(), __FILE__, __LINE__);
            throw new Exception("No se pudo crear la sesión de evaluación: " . $e->getMessage());
        }
    }

    public function getByToken(string $token): ?array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_TOKEN . " = :token
            ");
            
            $stmt->execute(['token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener sesión por token: " . $e->getMessage(), __FILE__, __LINE__);
            return null;
        }
    }

    public function getById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_ID . " = :id
            ");
            
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener sesión por ID: " . $e->getMessage(), __FILE__, __LINE__);
            return null;
        }
    }

    public function isSessionActive(string $token): bool {
        try {
            $session = $this->getByToken($token);
            
            if (!$session || $session[EVALUATION_SESSION_FIELD_STATE] !== 'activa') {
                return false;
            }

            $now = new DateTime();
            $inicio = new DateTime($session[EVALUATION_SESSION_FIELD_START]);
            $fin = new DateTime($session[EVALUATION_SESSION_FIELD_END]);

            return $now >= $inicio && $now <= $fin;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al verificar estado de sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getRemainingTime(string $token): int {
        try {
            $session = $this->getByToken($token);
            
            if (!$session || $session[EVALUATION_SESSION_FIELD_STATE] !== 'activa') {
                return 0;
            }

            $fin = new DateTime($session[EVALUATION_SESSION_FIELD_END]);
            $now = new DateTime();
            
            $remaining = $fin->getTimestamp() - $now->getTimestamp();
            return max(0, $remaining);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener tiempo restante: " . $e->getMessage(), __FILE__, __LINE__);
            return 0;
        }
    }

    public function closeSession(int $id, string $motivo = 'manual', bool $manual = true): bool {
        try {
            $data = [
                EVALUATION_SESSION_FIELD_STATE => 'cerrada',
                EVALUATION_SESSION_FIELD_CLOSE_REASON => $motivo,
                EVALUATION_SESSION_FIELD_MANUAL_CLOSE => $manual ? 1 : 0,
                EVALUATION_SESSION_FIELD_UPDATED => (new DateTime())->format('Y-m-d H:i:s')
            ];
            
            return $this->updateSession($id, $data);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al cerrar sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function syncSessionStatus(): int {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . EVALUATION_SESSIONS_TABLE . " 
                SET " . EVALUATION_SESSION_FIELD_STATE . " = 'cerrada', 
                    " . EVALUATION_SESSION_FIELD_CLOSE_REASON . " = 'expirada', 
                    " . EVALUATION_SESSION_FIELD_MANUAL_CLOSE . " = 0,
                    " . EVALUATION_SESSION_FIELD_UPDATED . " = NOW()
                WHERE " . EVALUATION_SESSION_FIELD_STATE . " = 'activa' 
                AND " . EVALUATION_SESSION_FIELD_END . " < NOW()
            ");
            
            $stmt->execute();
            $count = $stmt->rowCount();
            
            if ($count > 0) {
                $this->logger->log("Sincronización: $count sesiones expiradas cerradas automáticamente", __FILE__, __LINE__);
            }
            
            return $count;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error en sincronización de sesiones: " . $e->getMessage(), __FILE__, __LINE__);
            return 0;
        }
    }

    public function extendSession(int $sessionId, int $additionalSeconds): bool {
        try {
            $session = $this->getById($sessionId);
            if (!$session || $session[EVALUATION_SESSION_FIELD_STATE] !== 'activa') {
                return false;
            }

            $nuevaDuracion = $session[EVALUATION_SESSION_FIELD_DURATION] + $additionalSeconds;

            $data = [
                EVALUATION_SESSION_FIELD_DURATION => $nuevaDuracion,
                EVALUATION_SESSION_FIELD_UPDATED => (new DateTime())->format('Y-m-d H:i:s')
            ];

            return $this->updateSession($sessionId, $data);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al extender sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getActiveSessionsCount(): int {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_STATE . " = 'activa' 
                AND NOW() BETWEEN " . EVALUATION_SESSION_FIELD_START . " AND " . EVALUATION_SESSION_FIELD_END . "
            ");
            
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al contar sesiones activas: " . $e->getMessage(), __FILE__, __LINE__);
            return 0;
        }
    }

    private function updateSession(int $id, array $data): bool {
        try {
            if (empty($data)) {
                return false;
            }

            $data[EVALUATION_SESSION_FIELD_UPDATED] = (new DateTime())->format('Y-m-d H:i:s');

            $fields = [];
            $params = ['id' => $id];

            foreach ($data as $key => $value) {
                $allowedFields = [
                    EVALUATION_SESSION_FIELD_STATE, 
                    EVALUATION_SESSION_FIELD_CLOSE_REASON, 
                    EVALUATION_SESSION_FIELD_MANUAL_CLOSE, 
                    EVALUATION_SESSION_FIELD_END, 
                    EVALUATION_SESSION_FIELD_DURATION, 
                    EVALUATION_SESSION_FIELD_UPDATED
                ];
                if (in_array($key, $allowedFields)) {
                    $fields[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }

            if (empty($fields)) {
                return false;
            }

            $query = "UPDATE " . EVALUATION_SESSIONS_TABLE . " SET " . implode(', ', $fields) . " 
                      WHERE " . EVALUATION_SESSION_FIELD_ID . " = :id";
            $stmt = $this->pdo->prepare($query);
            
            return $stmt->execute($params);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al actualizar sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function addUserToSession(int $userId, int $sessionId): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " 
                (" . _PARTICIPANT_USER_ID_ . ", " . _PARTICIPANT_SESSION_ID_ . ", " . _PARTICIPANT_FECHA_REGISTRO_ . ")
                VALUES (:user_id, :session_id, NOW())
            ");
            
            $result = $stmt->execute([
                'user_id' => $userId, 
                'session_id' => $sessionId
            ]);
            
            if ($result) {
                $this->logger->log("Usuario $userId añadido a sesión $sessionId", __FILE__, __LINE__);
            }
            
            return $result;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al añadir usuario a sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function hasActiveSessionForProject(int $projectId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_PROJECT_ID . " = :project_id 
                AND " . EVALUATION_SESSION_FIELD_STATE . " = 'activa' 
                AND NOW() BETWEEN " . EVALUATION_SESSION_FIELD_START . " AND " . EVALUATION_SESSION_FIELD_END . "
            ");
            
            $stmt->execute(['project_id' => $projectId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al verificar sesión activa: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function isUserInAnyActiveSession(int $userId, int $projectId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " p
                JOIN " . EVALUATION_SESSIONS_TABLE . " s ON p." . _PARTICIPANT_SESSION_ID_ . " = s." . EVALUATION_SESSION_FIELD_ID . "
                WHERE p." . _PARTICIPANT_USER_ID_ . " = :user_id 
                AND s." . EVALUATION_SESSION_FIELD_PROJECT_ID . " = :project_id
                AND s." . EVALUATION_SESSION_FIELD_STATE . " = 'activa'
                AND NOW() BETWEEN s." . EVALUATION_SESSION_FIELD_START . " AND s." . EVALUATION_SESSION_FIELD_END . "
            ");
            
            $stmt->execute(['user_id' => $userId, 'project_id' => $projectId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al verificar usuario en sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getActiveSessionsByProject(int $projectId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_PROJECT_ID . " = :project_id 
                AND " . EVALUATION_SESSION_FIELD_STATE . " = 'activa' 
                AND NOW() BETWEEN " . EVALUATION_SESSION_FIELD_START . " AND " . EVALUATION_SESSION_FIELD_END . "
                ORDER BY " . EVALUATION_SESSION_FIELD_CREATED . " DESC
            ");
            
            $stmt->execute(['project_id' => $projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener sesiones activas: " . $e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function isUserInSession(int $userId, int $sessionId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " 
                WHERE " . _PARTICIPANT_USER_ID_ . " = :user_id 
                AND " . _PARTICIPANT_SESSION_ID_ . " = :session_id
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al verificar usuario en sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getSessionParticipants(int $sessionId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u." . _CORE_UID_ . " as id, d." . _DETAIL_FIRSTNAME_ . " as firstname, 
                       d." . _DETAIL_LASTNAME_ . " as lastname, u." . _CORE_UNOM_ . " as username, 
                       u." . _CORE_UEML_ . " as email,
                       p." . _PARTICIPANT_COMPLETED_ . " as completed,
                       p." . _PARTICIPANT_COMPLETED_AT_ . " as completed_at
                FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " p
                JOIN " . _TBL_WEBENGINE_U_CORE_ . " u ON p." . _PARTICIPANT_USER_ID_ . " = u." . _CORE_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " d ON u." . _CORE_UID_ . " = d." . _DETAIL_UID_ . "
                WHERE p." . _PARTICIPANT_SESSION_ID_ . " = :session_id
            ");
            
            $stmt->execute(['session_id' => $sessionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener participantes: " . $e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function markEvaluationCompleted(int $sessionId, int $userId): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " 
                SET " . _PARTICIPANT_COMPLETED_ . " = 1,
                    " . _PARTICIPANT_COMPLETED_AT_ . " = NOW()
                WHERE " . _PARTICIPANT_USER_ID_ . " = :user_id 
                AND " . _PARTICIPANT_SESSION_ID_ . " = :session_id
            ");
            
            $result = $stmt->execute([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            
            if ($result) {
                $this->logger->log("Evaluación marcada como completada - Usuario $userId, Sesión $sessionId", __FILE__, __LINE__);
            }
            
            return $result;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al marcar evaluación completada: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function hasUserCompletedEvaluation(int $sessionId, int $userId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT " . _PARTICIPANT_COMPLETED_ . " 
                FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " 
                WHERE " . _PARTICIPANT_USER_ID_ . " = :user_id 
                AND " . _PARTICIPANT_SESSION_ID_ . " = :session_id
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            
            $result = $stmt->fetchColumn();
            return $result !== false && (bool)$result;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al verificar evaluación completada: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getAllCompletedEvaluations(int $sessionId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u." . _CORE_UID_ . " as id, d." . _DETAIL_FIRSTNAME_ . " as firstname, 
                       d." . _DETAIL_LASTNAME_ . " as lastname, u." . _CORE_UNOM_ . " as username, 
                       p." . _PARTICIPANT_COMPLETED_AT_ . " as completed_at
                FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " p
                JOIN " . _TBL_WEBENGINE_U_CORE_ . " u ON p." . _PARTICIPANT_USER_ID_ . " = u." . _CORE_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " d ON u." . _CORE_UID_ . " = d." . _DETAIL_UID_ . "
                WHERE p." . _PARTICIPANT_SESSION_ID_ . " = :session_id 
                AND p." . _PARTICIPANT_COMPLETED_ . " = 1
                ORDER BY p." . _PARTICIPANT_COMPLETED_AT_ . " DESC
            ");
            
            $stmt->execute(['session_id' => $sessionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener evaluaciones completadas: " . $e->getMessage(), __FILE__, __LINE__);
            return [];
        }
    }

    public function getSessionProgress(int $sessionId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_participants,
                    SUM(" . _PARTICIPANT_COMPLETED_ . ") as completed_count,
                    CASE 
                        WHEN COUNT(*) > 0 THEN ROUND((SUM(" . _PARTICIPANT_COMPLETED_ . ") / COUNT(*)) * 100, 2)
                        ELSE 0 
                    END as completion_percentage
                FROM " . _TBL_WEBENGINE_EVALUATION_PARTICIPANTS_ . " 
                WHERE " . _PARTICIPANT_SESSION_ID_ . " = :session_id
            ");
            
            $stmt->execute(['session_id' => $sessionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_participants' => 0,
                'completed_count' => 0,
                'completion_percentage' => 0
            ];
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener progreso de sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return [
                'total_participants' => 0,
                'completed_count' => 0,
                'completion_percentage' => 0
            ];
        }
    }

    public function getSessionToken(int $sessionId): ?string {
        try {
            $stmt = $this->pdo->prepare("
                SELECT " . EVALUATION_SESSION_FIELD_TOKEN . " 
                FROM " . EVALUATION_SESSIONS_TABLE . " 
                WHERE " . EVALUATION_SESSION_FIELD_ID . " = :session_id
            ");
            
            $stmt->execute(['session_id' => $sessionId]);
            return $stmt->fetchColumn() ?: null;
        } catch (Exception $e) {
            $this->logger->logDatabaseError("Error al obtener token de sesión: " . $e->getMessage(), __FILE__, __LINE__);
            return null;
        }
    }
}