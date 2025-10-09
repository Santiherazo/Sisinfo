<?php

class EvaluationManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ==================== MÉTODOS DE INICIALIZACIÓN Y CONFIGURACIÓN ====================

    /**
     * Verifica la conexión a la base de datos
     */
    public function checkConnection(): bool {
        try {
            $this->pdo->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            error_log("Error de conexión en EvaluationManager: " . $e->getMessage());
            return false;
        }
    }

    // ==================== MÉTODOS CRUD BÁSICOS PARA RATINGS ====================
    
    public function addRating(array $formData): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO " . TABLE_RATINGS . " (
                " . RATINGS_SESSION_TOKEN . ",
                " . RATINGS_PROJECT_ID . ",
                " . RATINGS_EVALUADOR_UID . ",
                " . RATINGS_CRITERIO_NOMBRE . ",
                " . RATINGS_CRITERIO_VALOR . ",
                " . RATINGS_CALIFICACION . ",
                " . RATINGS_OBSERVACION_PERSONAL . ",
                " . RATINGS_ESTADO . "
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'evaluado')
            ON DUPLICATE KEY UPDATE 
                " . RATINGS_CRITERIO_VALOR . " = VALUES(" . RATINGS_CRITERIO_VALOR . "),
                " . RATINGS_CALIFICACION . " = VALUES(" . RATINGS_CALIFICACION . "),
                " . RATINGS_OBSERVACION_PERSONAL . " = VALUES(" . RATINGS_OBSERVACION_PERSONAL . "),
                " . RATINGS_ESTADO . " = 'evaluado',
                " . RATINGS_UPDATED_AT . " = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            $formData['session_token'] ?? '',
            $formData['project_id'],
            $formData['evaluador_uid'],
            $formData['criterio_nombre'],
            $formData['criterio_valor'] ?? '',
            $formData['calificacion'],
            $formData['observacion_personal'] ?? ''
        ]);
    }

    public function getRating(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_ID . " = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function updateRating(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE " . TABLE_RATINGS . " 
            SET " . RATINGS_CALIFICACION . " = ?,
                " . RATINGS_OBSERVACION_PERSONAL . " = ?,
                " . RATINGS_ESTADO . " = ?,
                " . RATINGS_UPDATED_AT . " = CURRENT_TIMESTAMP
            WHERE " . RATINGS_ID . " = ?
        ");
        return $stmt->execute([
            $data['calificacion'],
            $data['observacion_personal'] ?? '',
            $data['estado'] ?? 'evaluado',
            $id
        ]);
    }

    public function deleteRating(int $id): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_ID . " = ?
        ");
        return $stmt->execute([$id]);
    }

    // ==================== MÉTODOS DE CONSULTA PARA RATINGS ====================

    public function getRatingsByUser(int $projectId, int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY " . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$projectId, $evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsByProject(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY " . RATINGS_EVALUADOR_UID . ", " . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsBySession(string $sessionToken): array {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_SESSION_TOKEN . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY " . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$sessionToken]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsByCriterio(string $criterioNombre): array {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_CRITERIO_NOMBRE . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY " . RATINGS_PROJECT_ID . ", " . RATINGS_EVALUADOR_UID . " ASC
        ");
        $stmt->execute([$criterioNombre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsByEvaluador(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT r.*, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            WHERE r." . RATINGS_EVALUADOR_UID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY r." . RATINGS_PROJECT_ID . ", r." . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS DE CONSULTA COMBINADOS ====================

    public function getAllRatingsWithSummaries($filters = []): array {
        if (is_int($filters)) {
            $filters = ['limit' => $filters];
        }
        
        $defaultFilters = [
            'limit' => 50,
            'project_id' => null,
            'evaluador_uid' => null,
            'order_by' => 'r.' . RATINGS_UPDATED_AT . ' DESC'
        ];
        
        $filters = array_merge($defaultFilters, $filters);
        
        $sql = "
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                uc." . _CORE_UNOM_ . " as evaluador_username,
                rs." . RATING_SUMMARY_COMENTARIO_GENERAL . " as comentario_general,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as calificacion_total,
                rs." . RATING_SUMMARY_ESTADO_EVALUACION . " as estado_evaluacion
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON r." . RATINGS_PROJECT_ID . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
        ";
        
        $params = [];
        
        if ($filters['project_id'] !== null) {
            $sql .= " AND r." . RATINGS_PROJECT_ID . " = ?";
            $params[] = $filters['project_id'];
        }
        
        if ($filters['evaluador_uid'] !== null) {
            $sql .= " AND r." . RATINGS_EVALUADOR_UID . " = ?";
            $params[] = $filters['evaluador_uid'];
        }
        
        $sql .= " ORDER BY " . $filters['order_by'];
        
        if ($filters['limit'] > 0) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRatingsWithSummariesSimple(int $limit = 50): array {
        return $this->getAllRatingsWithSummaries(['limit' => $limit]);
    }

    public function getRatingsWithSummariesByProject(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                uc." . _CORE_UNOM_ . " as evaluador_username,
                rs." . RATING_SUMMARY_COMENTARIO_GENERAL . " as comentario_general,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as calificacion_total,
                rs." . RATING_SUMMARY_ESTADO_EVALUACION . " as estado_evaluacion
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON r." . RATINGS_PROJECT_ID . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_PROJECT_ID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY uc." . _CORE_UNOM_ . " ASC, r." . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsWithSummariesByEvaluador(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                rs." . RATING_SUMMARY_COMENTARIO_GENERAL . " as comentario_general,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as calificacion_total,
                rs." . RATING_SUMMARY_ESTADO_EVALUACION . " as estado_evaluacion
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON r." . RATINGS_PROJECT_ID . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_EVALUADOR_UID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " ASC, r." . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompleteEvaluationData(int $projectId, int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                p." . _CLMN_WEBENGINE_PROJECT_DESCRIPCION_ . " as project_description,
                uc." . _CORE_UNOM_ . " as evaluador_username,
                rs." . RATING_SUMMARY_COMENTARIO_GENERAL . " as comentario_general,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as calificacion_total,
                rs." . RATING_SUMMARY_ESTADO_EVALUACION . " as estado_evaluacion,
                rs." . RATING_SUMMARY_TIEMPO_TOTAL . " as tiempo_total,
                rs." . RATING_SUMMARY_FECHA_INICIO . " as fecha_inicio,
                rs." . RATING_SUMMARY_FECHA_FIN . " as fecha_fin
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON r." . RATINGS_PROJECT_ID . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_PROJECT_ID . " = ? 
            AND r." . RATINGS_EVALUADOR_UID . " = ?
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY r." . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$projectId, $evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS PARA RATING SUMMARY ====================

    public function addRatingSummary(array $formData): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO " . TABLE_RATING_SUMMARY . " (
                " . RATING_SUMMARY_SESSION_TOKEN . ",
                " . RATING_SUMMARY_PROJECT_ID . ",
                " . RATING_SUMMARY_EVALUADOR_UID . ",
                " . RATING_SUMMARY_COMENTARIO_GENERAL . ",
                " . RATING_SUMMARY_CALIFICACION_TOTAL . ",
                " . RATING_SUMMARY_ESTADO_EVALUACION . ",
                " . RATING_SUMMARY_TIEMPO_TOTAL . ",
                " . RATING_SUMMARY_FECHA_INICIO . ",
                " . RATING_SUMMARY_FECHA_FIN . ",
                " . RATING_SUMMARY_CREATED_AT . ",
                " . RATING_SUMMARY_UPDATED_AT . "
            ) VALUES (?, ?, ?, ?, ?, 'completa', ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE 
                " . RATING_SUMMARY_COMENTARIO_GENERAL . " = VALUES(" . RATING_SUMMARY_COMENTARIO_GENERAL . "),
                " . RATING_SUMMARY_CALIFICACION_TOTAL . " = VALUES(" . RATING_SUMMARY_CALIFICACION_TOTAL . "),
                " . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa',
                " . RATING_SUMMARY_TIEMPO_TOTAL . " = VALUES(" . RATING_SUMMARY_TIEMPO_TOTAL . "),
                " . RATING_SUMMARY_FECHA_INICIO . " = VALUES(" . RATING_SUMMARY_FECHA_INICIO . "),
                " . RATING_SUMMARY_FECHA_FIN . " = VALUES(" . RATING_SUMMARY_FECHA_FIN . "),
                " . RATING_SUMMARY_UPDATED_AT . " = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            $formData['session_token'] ?? '',
            $formData['project_id'],
            $formData['evaluador_uid'],
            $formData['comentario_general'],
            $formData['calificacion_total'],
            $formData['tiempo_duracion'] ?? null,
            $formData['fecha_inicio'] ?? null,
            $formData['fecha_fin'] ?? null
        ]);
    }

    public function getRatingSummary(int $projectId): ?array {
        $stmt = $this->pdo->prepare("
            SELECT rs.*, uc." . _CORE_UNOM_ . " as evaluador_username
            FROM " . TABLE_RATING_SUMMARY . " rs
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON rs." . RATING_SUMMARY_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            WHERE rs." . RATING_SUMMARY_PROJECT_ID . " = ?
            ORDER BY rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " DESC
            LIMIT 1
        ");
        $stmt->execute([$projectId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function getSummaryByUser(int $projectId, int $evaluadorUid): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = ? 
            AND " . RATING_SUMMARY_EVALUADOR_UID . " = ?
        ");
        $stmt->execute([$projectId, $evaluadorUid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function getAllRatingSummariesByUser(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT rs.*, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title
            FROM " . TABLE_RATING_SUMMARY . " rs
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON rs." . RATING_SUMMARY_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            WHERE rs." . RATING_SUMMARY_EVALUADOR_UID . " = ?
            ORDER BY rs." . RATING_SUMMARY_FECHA_FIN . " DESC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingSummariesByProject(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT rs.*, uc." . _CORE_UNOM_ . " as evaluador_username
            FROM " . TABLE_RATING_SUMMARY . " rs
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON rs." . RATING_SUMMARY_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            WHERE rs." . RATING_SUMMARY_PROJECT_ID . " = ?
            ORDER BY rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRatingSummary(int $summaryId, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE " . TABLE_RATING_SUMMARY . " 
            SET " . RATING_SUMMARY_COMENTARIO_GENERAL . " = ?,
                " . RATING_SUMMARY_CALIFICACION_TOTAL . " = ?,
                " . RATING_SUMMARY_ESTADO_EVALUACION . " = ?,
                " . RATING_SUMMARY_TIEMPO_TOTAL . " = ?,
                " . RATING_SUMMARY_FECHA_FIN . " = ?,
                " . RATING_SUMMARY_UPDATED_AT . " = CURRENT_TIMESTAMP
            WHERE " . RATING_SUMMARY_ID . " = ?
        ");
        return $stmt->execute([
            $data['comentario_general'],
            $data['calificacion_total'],
            $data['estado_evaluacion'] ?? 'completa',
            $data['tiempo_total'] ?? null,
            $data['fecha_fin'] ?? null,
            $summaryId
        ]);
    }

    public function deleteRatingSummary(int $summaryId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM " . TABLE_RATING_SUMMARY . " 
            WHERE " . RATING_SUMMARY_ID . " = ?
        ");
        return $stmt->execute([$summaryId]);
    }

    // ==================== MÉTODOS PARA EL DASHBOARD Y ESTADÍSTICAS ====================

    /**
     * Cuenta el total de evaluaciones (rating summaries) con condiciones opcionales
     */
    public function countEvaluations($conditions = []): int {
        $where = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $field => $values) {
                if (is_array($values)) {
                    $placeholders = [];
                    foreach ($values as $value) {
                        $placeholders[] = '?';
                        $params[] = $value;
                    }
                    $whereConditions[] = "$field IN (" . implode(',', $placeholders) . ")";
                } else {
                    $whereConditions[] = "$field = ?";
                    $params[] = $values;
                }
            }
            $where = "WHERE " . implode(' AND ', $whereConditions);
        }
        
        $query = "SELECT COUNT(*) as total FROM " . TABLE_RATING_SUMMARY . " $where";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Cuenta evaluaciones entre fechas específicas
     */
    public function countEvaluationsBetweenDates(
        string $startDate, 
        string $endDate, 
        string $dateField = RATING_SUMMARY_CREATED_AT, 
        $conditions = []
    ): int {
        $whereConditions = ["$dateField BETWEEN ? AND ?"];
        $params = [$startDate, $endDate];
        
        if (!empty($conditions)) {
            foreach ($conditions as $field => $values) {
                if (is_array($values)) {
                    $placeholders = [];
                    foreach ($values as $value) {
                        $placeholders[] = '?';
                        $params[] = $value;
                    }
                    $whereConditions[] = "$field IN (" . implode(',', $placeholders) . ")";
                } else {
                    $whereConditions[] = "$field = ?";
                    $params[] = $values;
                }
            }
        }
        
        $where = "WHERE " . implode(' AND ', $whereConditions);
        $query = "SELECT COUNT(*) as total FROM " . TABLE_RATING_SUMMARY . " $where";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Método alternativo más simple para contar todas las evaluaciones
     */
    public function countAllEvaluations(): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM " . TABLE_RATING_SUMMARY . " 
            WHERE " . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Método alternativo más simple para contar evaluaciones por año
     */
    public function countEvaluationsThisYear(string $year = null): int {
        if ($year === null) {
            $year = date('Y');
        }
        
        $startDate = "$year-01-01 00:00:00";
        $endDate = "$year-12-31 23:59:59";
        
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM " . TABLE_RATING_SUMMARY . " 
            WHERE " . RATING_SUMMARY_CREATED_AT . " BETWEEN ? AND ?
            AND " . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa'
        ");
        $stmt->execute([$startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Obtiene estadísticas mensuales de evaluaciones para gráficos
     */
    public function getMonthlyEvaluationStats(int $year = null): array {
        if ($year === null) {
            $year = date('Y');
        }
        
        $stmt = $this->pdo->prepare("
            SELECT 
                MONTH(" . RATING_SUMMARY_CREATED_AT . ") as month,
                COUNT(*) as total_evaluations,
                AVG(" . RATING_SUMMARY_CALIFICACION_TOTAL . ") as average_score
            FROM " . TABLE_RATING_SUMMARY . "
            WHERE YEAR(" . RATING_SUMMARY_CREATED_AT . ") = ?
            AND " . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa'
            GROUP BY MONTH(" . RATING_SUMMARY_CREATED_AT . ")
            ORDER BY month ASC
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los proyectos más evaluados
     */
    public function getMostEvaluatedProjects(int $limit = 5): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                p." . _CLMN_WEBENGINE_PROJECT_ID_ . " as project_id,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                COUNT(rs." . RATING_SUMMARY_ID . ") as evaluation_count,
                AVG(rs." . RATING_SUMMARY_CALIFICACION_TOTAL . ") as average_score
            FROM " . _TBL_WEBENGINE_PROJECTS_ . " p
            INNER JOIN " . TABLE_RATING_SUMMARY . " rs ON p." . _CLMN_WEBENGINE_PROJECT_ID_ . " = rs." . RATING_SUMMARY_PROJECT_ID . "
            WHERE rs." . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa'
            GROUP BY p." . _CLMN_WEBENGINE_PROJECT_ID_ . ", p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . "
            ORDER BY evaluation_count DESC, average_score DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los evaluadores más activos
     */
    public function getTopEvaluators(int $limit = 5): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                uc." . _CORE_UID_ . " as evaluator_id,
                uc." . _CORE_UNOM_ . " as evaluator_name,
                COUNT(rs." . RATING_SUMMARY_ID . ") as evaluation_count,
                AVG(rs." . RATING_SUMMARY_CALIFICACION_TOTAL . ") as average_score_given
            FROM " . _TBL_WEBENGINE_U_CORE_ . " uc
            INNER JOIN " . TABLE_RATING_SUMMARY . " rs ON uc." . _CORE_UID_ . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE rs." . RATING_SUMMARY_ESTADO_EVALUACION . " = 'completa'
            GROUP BY uc." . _CORE_UID_ . ", uc." . _CORE_UNOM_ . "
            ORDER BY evaluation_count DESC, average_score_given DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS PARA PROYECTOS EVALUADOS ====================

    public function getUserEvaluatedProjects(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT 
                r." . RATINGS_PROJECT_ID . ",
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as titulo,
                p." . _CLMN_WEBENGINE_PROJECT_DESCRIPCION_ . " as descripcion,
                p." . _CLMN_WEBENGINE_PROJECT_ESTADO_ . " as estado,
                p." . _CLMN_WEBENGINE_PROJECT_CREADO_EN_ . " as fecha_creacion,
                rs." . RATING_SUMMARY_FECHA_FIN . " as fecha_evaluacion,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as puntuacion,
                rs." . RATING_SUMMARY_COMENTARIO_GENERAL . " as comentario_general
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON r." . RATINGS_PROJECT_ID . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_EVALUADOR_UID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY rs." . RATING_SUMMARY_FECHA_FIN . " DESC, p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " ASC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserEvaluatedProjectIds(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT " . RATINGS_PROJECT_ID . "
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY " . RATINGS_PROJECT_ID . " ASC
        ");
        $stmt->execute([$evaluadorUid]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $results ?: [];
    }

    public function countUserEvaluatedProjects(int $evaluadorUid): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT " . RATINGS_PROJECT_ID . ")
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$evaluadorUid]);
        return (int) $stmt->fetchColumn();
    }

    public function getUserEvaluatedProjectsWithStatus(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                p." . _CLMN_WEBENGINE_PROJECT_ID_ . " as project_id,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                COUNT(r." . RATINGS_ID . ") as criteria_evaluated,
                rs." . RATING_SUMMARY_ESTADO_EVALUACION . " as evaluation_status,
                rs." . RATING_SUMMARY_CALIFICACION_TOTAL . " as total_score,
                rs." . RATING_SUMMARY_FECHA_FIN . " as completion_date
            FROM " . _TBL_WEBENGINE_PROJECTS_ . " p
            INNER JOIN " . TABLE_RATINGS . " r ON p." . _CLMN_WEBENGINE_PROJECT_ID_ . " = r." . RATINGS_PROJECT_ID . "
            LEFT JOIN " . TABLE_RATING_SUMMARY . " rs ON p." . _CLMN_WEBENGINE_PROJECT_ID_ . " = rs." . RATING_SUMMARY_PROJECT_ID . " 
                AND r." . RATINGS_EVALUADOR_UID . " = rs." . RATING_SUMMARY_EVALUADOR_UID . "
            WHERE r." . RATINGS_EVALUADOR_UID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY p." . _CLMN_WEBENGINE_PROJECT_ID_ . ", p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . ", rs." . RATING_SUMMARY_ESTADO_EVALUACION . ", rs." . RATING_SUMMARY_CALIFICACION_TOTAL . ", rs." . RATING_SUMMARY_FECHA_FIN . "
            ORDER BY rs." . RATING_SUMMARY_FECHA_FIN . " DESC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS DE VERIFICACIÓN Y CONTEO ====================

    public function hasUserEvaluatedProject(int $projectId, int $evaluadorUid): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$projectId, $evaluadorUid]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function countRatingsByProject(int $projectId): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$projectId]);
        return (int) $stmt->fetchColumn();
    }

    public function countRatingsByEvaluador(int $evaluadorUid): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$evaluadorUid]);
        return (int) $stmt->fetchColumn();
    }

    // ==================== MÉTODOS DE ESTADÍSTICAS Y PROMEDIOS ====================

    public function getAverageByCriterio(int $projectId, string $criterioNombre): ?float {
        $stmt = $this->pdo->prepare("
            SELECT AVG(" . RATINGS_CALIFICACION . ") 
            FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_CRITERIO_NOMBRE . " = ?
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$projectId, $criterioNombre]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (float) $result : null;
    }

    public function getGeneralAverageByProject(int $projectId): ?float {
        $stmt = $this->pdo->prepare("
            SELECT AVG(" . RATINGS_CALIFICACION . ") 
            FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$projectId]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (float) $result : null;
    }

    public function getProjectRatingsSummary(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                " . RATINGS_CRITERIO_NOMBRE . ",
                COUNT(*) as total_evaluaciones,
                AVG(" . RATINGS_CALIFICACION . ") as promedio,
                MIN(" . RATINGS_CALIFICACION . ") as minima,
                MAX(" . RATINGS_CALIFICACION . ") as maxima
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY " . RATINGS_CRITERIO_NOMBRE . "
            ORDER BY " . RATINGS_CRITERIO_NOMBRE . " ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluatorRatingsSummary(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                " . RATINGS_PROJECT_ID . ",
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                COUNT(*) as total_criterios,
                AVG(" . RATINGS_CALIFICACION . ") as promedio_general
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            WHERE r." . RATINGS_EVALUADOR_UID . " = ? 
            AND r." . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY r." . RATINGS_PROJECT_ID . ", p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . "
            ORDER BY p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " ASC
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluationProgress(int $projectId, int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_criteria,
                SUM(CASE WHEN " . RATINGS_ESTADO . " = 'evaluado' THEN 1 ELSE 0 END) as evaluated_criteria,
                (SUM(CASE WHEN " . RATINGS_ESTADO . " = 'evaluado' THEN 1 ELSE 0 END) / COUNT(*) * 100) as progress_percentage
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_EVALUADOR_UID . " = ?
        ");
        $stmt->execute([$projectId, $evaluadorUid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProjectEvaluationStats(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT " . RATINGS_EVALUADOR_UID . ") as total_evaluators,
                COUNT(*) as total_ratings,
                AVG(" . RATINGS_CALIFICACION . ") as average_score,
                MIN(" . RATINGS_CALIFICACION . ") as min_score,
                MAX(" . RATINGS_CALIFICACION . ") as max_score
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_PROJECT_ID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEvaluatorStats(int $evaluadorUid): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT " . RATINGS_PROJECT_ID . ") as total_projects_evaluated,
                COUNT(*) as total_ratings_given,
                AVG(" . RATINGS_CALIFICACION . ") as average_rating_given,
                MIN(" . RATINGS_CALIFICACION . ") as min_rating_given,
                MAX(" . RATINGS_CALIFICACION . ") as max_rating_given
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_EVALUADOR_UID . " = ? 
            AND " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute([$evaluadorUid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS DE BÚSQUEDA Y LISTADOS ====================

    public function getRecentRatings(int $limit = 10): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                uc." . _CORE_UNOM_ . " as evaluador_username
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
            ORDER BY r." . RATINGS_UPDATED_AT . " DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingsBetweenDates(string $startDate, string $endDate): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                uc." . _CORE_UNOM_ . " as evaluador_username
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
            AND r." . RATINGS_UPDATED_AT . " BETWEEN ? AND ?
            ORDER BY r." . RATINGS_UPDATED_AT . " DESC
        ");
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopRatedProjects(int $limit = 10): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                p." . _CLMN_WEBENGINE_PROJECT_ID_ . " as project_id,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                AVG(r." . RATINGS_CALIFICACION . ") as average_rating,
                COUNT(r." . RATINGS_ID . ") as total_ratings
            FROM " . _TBL_WEBENGINE_PROJECTS_ . " p
            INNER JOIN " . TABLE_RATINGS . " r ON p." . _CLMN_WEBENGINE_PROJECT_ID_ . " = r." . RATINGS_PROJECT_ID . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY p." . _CLMN_WEBENGINE_PROJECT_ID_ . ", p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . "
            HAVING COUNT(r." . RATINGS_ID . ") > 0
            ORDER BY average_rating DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMostActiveEvaluators(int $limit = 10): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                uc." . _CORE_UID_ . " as evaluador_id,
                uc." . _CORE_UNOM_ . " as evaluador_name,
                COUNT(r." . RATINGS_ID . ") as total_ratings,
                AVG(r." . RATINGS_CALIFICACION . ") as average_rating
            FROM " . _TBL_WEBENGINE_U_CORE_ . " uc
            INNER JOIN " . TABLE_RATINGS . " r ON uc." . _CORE_UID_ . " = r." . RATINGS_EVALUADOR_UID . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY uc." . _CORE_UID_ . ", uc." . _CORE_UNOM_ . "
            ORDER BY total_ratings DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchRatings(string $searchTerm, int $limit = 50): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " as project_title,
                uc." . _CORE_UNOM_ . " as evaluador_username
            FROM " . TABLE_RATINGS . " r
            INNER JOIN " . _TBL_WEBENGINE_PROJECTS_ . " p ON r." . RATINGS_PROJECT_ID . " = p." . _CLMN_WEBENGINE_PROJECT_ID_ . "
            INNER JOIN " . _TBL_WEBENGINE_U_CORE_ . " uc ON r." . RATINGS_EVALUADOR_UID . " = uc." . _CORE_UID_ . "
            WHERE r." . RATINGS_ESTADO . " = 'evaluado'
            AND (p." . _CLMN_WEBENGINE_PROJECT_TITULO_ . " LIKE ? 
                 OR r." . RATINGS_CRITERIO_NOMBRE . " LIKE ? 
                 OR r." . RATINGS_OBSERVACION_PERSONAL . " LIKE ?
                 OR uc." . _CORE_UNOM_ . " LIKE ?)
            ORDER BY r." . RATINGS_UPDATED_AT . " DESC
            LIMIT ?
        ");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern, $searchPattern, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== MÉTODOS DE LIMPIEZA Y MANTENIMIENTO ====================

    public function deleteRatingsByProject(int $projectId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_PROJECT_ID . " = ?
        ");
        return $stmt->execute([$projectId]);
    }

    public function deleteRatingsByEvaluador(int $evaluadorUid): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_EVALUADOR_UID . " = ?
        ");
        return $stmt->execute([$evaluadorUid]);
    }

    public function deleteRatingsBySession(string $sessionToken): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM " . TABLE_RATINGS . " 
            WHERE " . RATINGS_SESSION_TOKEN . " = ?
        ");
        return $stmt->execute([$sessionToken]);
    }

    public function getDuplicateRatings(): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                " . RATINGS_PROJECT_ID . ",
                " . RATINGS_EVALUADOR_UID . ",
                " . RATINGS_CRITERIO_NOMBRE . ",
                COUNT(*) as duplicates
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_ESTADO . " = 'evaluado'
            GROUP BY " . RATINGS_PROJECT_ID . ", " . RATINGS_EVALUADOR_UID . ", " . RATINGS_CRITERIO_NOMBRE . "
            HAVING COUNT(*) > 1
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cleanupDuplicateRatings(): bool {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                DELETE r1 FROM " . TABLE_RATINGS . " r1
                INNER JOIN " . TABLE_RATINGS . " r2 
                WHERE 
                    r1." . RATINGS_ID . " < r2." . RATINGS_ID . " AND
                    r1." . RATINGS_PROJECT_ID . " = r2." . RATINGS_PROJECT_ID . " AND
                    r1." . RATINGS_EVALUADOR_UID . " = r2." . RATINGS_EVALUADOR_UID . " AND
                    r1." . RATINGS_CRITERIO_NOMBRE . " = r2." . RATINGS_CRITERIO_NOMBRE . " AND
                    r1." . RATINGS_ESTADO . " = 'evaluado' AND
                    r2." . RATINGS_ESTADO . " = 'evaluado'
            ");
            
            $stmt->execute();
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error cleaning duplicate ratings: " . $e->getMessage());
            return false;
        }
    }

    // ==================== MÉTODOS DE VALIDACIÓN Y UTILIDAD ====================

    /**
     * Valida los datos de una evaluación antes de insertar
     */
    public function validateRatingData(array $data): array {
        $errors = [];

        if (empty($data['project_id']) || !is_numeric($data['project_id'])) {
            $errors[] = "ID de proyecto inválido";
        }

        if (empty($data['evaluador_uid']) || !is_numeric($data['evaluador_uid'])) {
            $errors[] = "ID de evaluador inválido";
        }

        if (empty($data['criterio_nombre'])) {
            $errors[] = "Nombre del criterio es requerido";
        }

        if (!isset($data['calificacion']) || !is_numeric($data['calificacion'])) {
            $errors[] = "Calificación inválida";
        } elseif ($data['calificacion'] < 0 || $data['calificacion'] > 100) {
            $errors[] = "La calificación debe estar entre 0 y 100";
        }

        return $errors;
    }

    /**
     * Obtiene el resumen de estadísticas generales del sistema
     */
    public function getSystemStats(): array {
        return [
            'total_evaluations' => $this->countAllEvaluations(),
            'total_ratings' => $this->countRatingsByEvaluador(0), // 0 para contar todos
            'active_evaluators' => $this->countActiveEvaluators(),
            'average_rating' => $this->getSystemAverageRating(),
            'recent_activity' => $this->getRecentRatings(5)
        ];
    }

    /**
     * Cuenta evaluadores activos (que han realizado al menos una evaluación)
     */
    private function countActiveEvaluators(): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT " . RATINGS_EVALUADOR_UID . ")
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtiene el promedio general de calificaciones del sistema
     */
    private function getSystemAverageRating(): ?float {
        $stmt = $this->pdo->prepare("
            SELECT AVG(" . RATINGS_CALIFICACION . ")
            FROM " . TABLE_RATINGS . "
            WHERE " . RATINGS_ESTADO . " = 'evaluado'
        ");
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result !== false ? (float) $result : null;
    }
}