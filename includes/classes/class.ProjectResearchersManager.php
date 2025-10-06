<?php

class ProjectResearchersManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Agregar investigador a un proyecto
    public function addResearcher(int $projectId, int $userId, string $role = 'colaborador'): bool {
        $sql = "INSERT INTO " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                (" . _CLMN_PROJRES_PROJID_ . ", " . _CLMN_PROJRES_UID_ . ", " . _CLMN_PROJRES_ROLE_ . ") 
                VALUES (:project_id, :user_id, :role)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':user_id' => $userId,
            ':role' => $role
        ]);
    }

    // Remover investigador (cambia estado a 'removido')
    public function removeResearcher(int $projectId, int $userId): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                SET " . _CLMN_PROJRES_STATE_ . " = 'removido'
                WHERE " . _CLMN_PROJRES_PROJID_ . " = :project_id 
                AND " . _CLMN_PROJRES_UID_ . " = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':user_id' => $userId
        ]);
    }

    // Reintegrar investigador (estado a 'activo')
    public function reactivateResearcher(int $projectId, int $userId): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                SET " . _CLMN_PROJRES_STATE_ . " = 'activo'
                WHERE " . _CLMN_PROJRES_PROJID_ . " = :project_id 
                AND " . _CLMN_PROJRES_UID_ . " = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':user_id' => $userId
        ]);
    }

    // Obtener todos los investigadores activos de un proyecto
    public function getActiveResearchers(int $projectId): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                WHERE " . _CLMN_PROJRES_PROJID_ . " = :project_id 
                AND " . _CLMN_PROJRES_STATE_ . " = 'activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si un usuario pertenece activamente a un proyecto
    public function isActiveResearcher(int $projectId, int $userId): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                WHERE " . _CLMN_PROJRES_PROJID_ . " = :project_id 
                AND " . _CLMN_PROJRES_UID_ . " = :user_id 
                AND " . _CLMN_PROJRES_STATE_ . " = 'activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':project_id' => $projectId,
            ':user_id' => $userId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    // Obtener todos los proyectos en los que participa un usuario
    public function getProjectsByResearcher(int $userId): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                WHERE " . _CLMN_PROJRES_UID_ . " = :user_id 
                AND " . _CLMN_PROJRES_STATE_ . " = 'activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar el rol de un investigador
    public function updateResearcherRole(int $projectId, int $userId, string $role): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_PROJECT_RESEARCHERS_ . " 
                SET " . _CLMN_PROJRES_ROLE_ . " = :role
                WHERE " . _CLMN_PROJRES_PROJID_ . " = :project_id 
                AND " . _CLMN_PROJRES_UID_ . " = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':role' => $role,
            ':project_id' => $projectId,
            ':user_id' => $userId
        ]);
    }
}
?>