<?php

class ProjectTeacherManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Agregar profesor al proyecto
    public function addTeacher(int $projectId, int $usuarioUid, string $rol): bool {
        $sql = "INSERT INTO " . TABLE_PROJECT_TEACHERS . " 
                (" . COL_PROJECT_TEACHER_PROJECT_ID . ", " . COL_PROJECT_TEACHER_USUARIO_UID . ", " . COL_PROJECT_TEACHER_ROL . ")
                VALUES (:project_id, :usuario_uid, :rol)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':project_id' => $projectId,
            ':usuario_uid' => $usuarioUid,
            ':rol' => $rol
        ]);
    }

    // Cambiar estado del profesor (activo o removido)
    public function updateEstado(int $id, string $estado): bool {
        $sql = "UPDATE " . TABLE_PROJECT_TEACHERS . " 
                SET " . COL_PROJECT_TEACHER_ESTADO . " = :estado 
                WHERE " . COL_PROJECT_TEACHER_ID . " = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    // Obtener profesores por proyecto
    public function getByProject(int $projectId): array {
        $sql = "SELECT * FROM " . TABLE_PROJECT_TEACHERS . " 
                WHERE " . COL_PROJECT_TEACHER_PROJECT_ID . " = :project_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si un usuario ya está asignado a un proyecto
    public function exists(int $projectId, int $usuarioUid): bool {
        $sql = "SELECT COUNT(*) FROM " . TABLE_PROJECT_TEACHERS . " 
                WHERE " . COL_PROJECT_TEACHER_PROJECT_ID . " = :project_id 
                AND " . COL_PROJECT_TEACHER_USUARIO_UID . " = :usuario_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':project_id' => $projectId,
            ':usuario_uid' => $usuarioUid
        ]);
        return $stmt->fetchColumn() > 0;
    }

    // Eliminar profesor del proyecto
    public function removeById(int $id): bool {
        $sql = "DELETE FROM " . TABLE_PROJECT_TEACHERS . " 
                WHERE " . COL_PROJECT_TEACHER_ID . " = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}