<?php

class projectReviewerManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function addReviewer(int $projectId, int $usuarioUid): bool {
        $sql = "INSERT INTO " . DB_PROJECT_REVIEWERS . " 
                (" . DB_REVIEWER_PROJECT_ID . ", " . DB_REVIEWER_USUARIO_UID . ") 
                VALUES (:project_id, :usuario_uid)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':usuario_uid' => $usuarioUid
        ]);
    }

    public function removeReviewer(int $projectId, int $usuarioUid): bool {
        $sql = "UPDATE " . DB_PROJECT_REVIEWERS . " 
                SET " . DB_REVIEWER_ESTADO . " = 'removido' 
                WHERE " . DB_REVIEWER_PROJECT_ID . " = :project_id 
                  AND " . DB_REVIEWER_USUARIO_UID . " = :usuario_uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':usuario_uid' => $usuarioUid
        ]);
    }

    public function getReviewersByProject(int $projectId): array {
        $sql = "SELECT * FROM " . DB_PROJECT_REVIEWERS . " 
                WHERE " . DB_REVIEWER_PROJECT_ID . " = :project_id 
                  AND " . DB_REVIEWER_ESTADO . " = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isReviewer(int $projectId, int $usuarioUid): bool {
        $sql = "SELECT 1 FROM " . DB_PROJECT_REVIEWERS . " 
                WHERE " . DB_REVIEWER_PROJECT_ID . " = :project_id 
                  AND " . DB_REVIEWER_USUARIO_UID . " = :usuario_uid 
                  AND " . DB_REVIEWER_ESTADO . " = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':project_id' => $projectId,
            ':usuario_uid' => $usuarioUid
        ]);
        return $stmt->fetchColumn() !== false;
    }
}
