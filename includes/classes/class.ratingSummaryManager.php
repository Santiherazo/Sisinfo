<?php 
class ratingSummaryManager {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            ORDER BY " . RATING_SUMMARY_CREATED_AT . " DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_ID . " = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllByProject(int $projectId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
            ORDER BY " . RATING_SUMMARY_CREATED_AT . " DESC
        ");
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByEvaluator(int $uid): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_EVALUADOR_UID . " = :uid
            ORDER BY " . RATING_SUMMARY_CREATED_AT . " DESC
        ");
        $stmt->execute([':uid' => $uid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProjectAndUser(int $projectId, int $uid): array|false {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
              AND " . RATING_SUMMARY_EVALUADOR_UID . " = :uid
            LIMIT 1
        ");
        $stmt->execute([
            ':project_id' => $projectId,
            ':uid'        => $uid
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO " . TABLE_RATING_SUMMARY . " (
                " . RATING_SUMMARY_PROJECT_ID . ",
                " . RATING_SUMMARY_EVALUADOR_UID . ",
                " . RATING_SUMMARY_CALIFICACION_TOTAL . ",
                " . RATING_SUMMARY_COMENTARIO . ",
                " . RATING_SUMMARY_CREATED_AT . "
            ) VALUES (
                :project_id, :uid, :calificacion_total, :comentario, NOW()
            )
        ");
        return $stmt->execute([
            ':project_id'        => $data[RATING_SUMMARY_PROJECT_ID],
            ':uid'               => $data[RATING_SUMMARY_EVALUADOR_UID],
            ':calificacion_total'=> $data[RATING_SUMMARY_CALIFICACION_TOTAL],
            ':comentario'        => $data[RATING_SUMMARY_COMENTARIO]
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE " . TABLE_RATING_SUMMARY . " SET
                " . RATING_SUMMARY_CALIFICACION_TOTAL . " = :calificacion_total,
                " . RATING_SUMMARY_COMENTARIO . " = :comentario
            WHERE " . RATING_SUMMARY_ID . " = :id
        ");
        return $stmt->execute([
            ':id'                => $id,
            ':calificacion_total'=> $data[RATING_SUMMARY_CALIFICACION_TOTAL],
            ':comentario'        => $data[RATING_SUMMARY_COMENTARIO]
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("
            DELETE FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_ID . " = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    public function deleteByProject(int $projectId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
        ");
        return $stmt->execute([':project_id' => $projectId]);
    }

    public function deleteByEvaluator(int $uid): bool {
        $stmt = $this->db->prepare("
            DELETE FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_EVALUADOR_UID . " = :uid
        ");
        return $stmt->execute([':uid' => $uid]);
    }

    public function existsResumen(int $projectId, int $uid): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
              AND " . RATING_SUMMARY_EVALUADOR_UID . " = :uid
        ");
        $stmt->execute([
            ':project_id' => $projectId,
            ':uid'        => $uid
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0) > 0;
    }

    public function getAverageByProject(int $projectId): ?float {
        $stmt = $this->db->prepare("
            SELECT AVG(" . RATING_SUMMARY_CALIFICACION_TOTAL . ") as promedio 
            FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
        ");
        $stmt->execute([':project_id' => $projectId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['promedio'] !== null ? (float) $result['promedio'] : null;
    }

    public function getResumenCountByProject(int $projectId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_PROJECT_ID . " = :project_id
        ");
        $stmt->execute([':project_id' => $projectId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function searchComentarios(string $keyword): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            WHERE " . RATING_SUMMARY_COMENTARIO . " LIKE :keyword
            ORDER BY " . RATING_SUMMARY_CREATED_AT . " DESC
        ");
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecent(int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_SUMMARY . "
            ORDER BY " . RATING_SUMMARY_CREATED_AT . " DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
