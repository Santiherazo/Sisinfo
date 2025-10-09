<?php

class ProjectManager {

    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createProject(array $formData) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO "._TBL_WEBENGINE_PROJECTS_." (
                    "._CLMN_WEBENGINE_PROJECT_TITULO_.",
                    "._CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_.",
                    "._CLMN_WEBENGINE_PROJECT_VISIBILIDAD_.",
                    "._CLMN_WEBENGINE_PROJECT_ACTIVO_.",
                    "._CLMN_WEBENGINE_PROJECT_DIRECTORIO_.",
                    "._CLMN_WEBENGINE_PROJECT_VERSION_.",
                    "._CLMN_WEBENGINE_PROJECT_FASE_.",
                    "._CLMN_WEBENGINE_PROJECT_ESTADO_.",
                    "._CLMN_WEBENGINE_PROJECT_DESCRIPCION_.",
                    "._CLMN_WEBENGINE_PROJECT_PALABRAS_CLAVE_.",
                    "._CLMN_WEBENGINE_PROJECT_CALIFICADO_.",
                    "._CLMN_WEBENGINE_PROJECT_PUNTUACION_.",
                    "._CLMN_WEBENGINE_PROJECT_TIMER_SEGUNDOS_.",
                    "._CLMN_WEBENGINE_PROJECT_HORA_PROGRAMADA_.",
                    "._CLMN_WEBENGINE_PROJECT_FECHA_PRESENTACION_.",
                    "._CLMN_WEBENGINE_PROJECT_CREADO_EN_."
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
            ");
            
            $stmt->execute([
                $formData['titulo'],
                $formData['linea_investigacion_id'],
                $formData['visibilidad'] ?? 'privado',
                1,
                $formData['directorio'] ?? null,
                $formData['version'] ?? '1.0',
                $formData['fase'] ?? 'propuesta',
                'nuevo',
                $formData['descripcion'] ?? null,
                $formData['palabras_clave'] ?? null,
                0,
                null,
                $formData['timer_segundos'] ?? 0,
                $formData['hora_programada'] ?? null,
                $formData['fecha_presentacion'] ?? null
            ]);

            $projectId = $this->pdo->lastInsertId();

            if (!empty($formData['investigadores'])) {
                foreach ($formData['investigadores'] as $researcher) {
                    if (!empty($researcher['usuario_uid'])) {
                        $this->addResearcher(
                            $projectId, 
                            $researcher['usuario_uid'], 
                            $researcher['rol'] ?? 'investigador'
                        );
                    }
                }
            }

            if (!empty($formData['docentes'])) {
                foreach ($formData['docentes'] as $teacher) {
                    if (!empty($teacher['usuario_uid'])) {
                        $this->addTeacher(
                            $projectId, 
                            $teacher['usuario_uid'], 
                            $teacher['rol'] ?? PROJECT_TEACHER_ROL_ASESOR
                        );
                    }
                }
            }

            if (!empty($formData['evaluadores'])) {
                foreach ($formData['evaluadores'] as $reviewerId) {
                    if (!empty($reviewerId)) {
                        $this->addReviewer($projectId, $reviewerId);
                    }
                }
            }

            $this->pdo->commit();
            return (int)$projectId;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error in createProject: ".$e->getMessage());
            error_log("Form data: ".print_r($formData, true));
            return 0;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("General error in createProject: ".$e->getMessage());
            return 0;
        }
    }

    public function updateProject($id, $data) {
        try {
            $this->pdo->beginTransaction();

            $fields = []; 
            $values = [];
            
            $mapping = [
                _CLMN_WEBENGINE_PROJECT_TITULO_ => 'titulo',
                _CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_ => 'linea_investigacion_id',
                _CLMN_WEBENGINE_PROJECT_DIRECTORIO_ => 'directorio',
                _CLMN_WEBENGINE_PROJECT_VERSION_ => 'version',
                _CLMN_WEBENGINE_PROJECT_FASE_ => 'fase',
                _CLMN_WEBENGINE_PROJECT_DESCRIPCION_ => 'descripcion',
                _CLMN_WEBENGINE_PROJECT_PALABRAS_CLAVE_ => 'palabras_clave',
                _CLMN_WEBENGINE_PROJECT_CALIFICADO_ => 'calificado',
                _CLMN_WEBENGINE_PROJECT_PUNTUACION_ => 'puntuacion',
                _CLMN_WEBENGINE_PROJECT_TIMER_SEGUNDOS_ => 'timer_segundos',
                _CLMN_WEBENGINE_PROJECT_HORA_PROGRAMADA_ => 'hora_programada',
                _CLMN_WEBENGINE_PROJECT_FECHA_PRESENTACION_ => 'fecha_presentacion'
            ];

            foreach ($mapping as $dbField => $dataField) {
                if (array_key_exists($dataField, $data)) {
                    $fields[] = "$dbField = ?";
                    $values[] = $data[$dataField];
                }
            }

            if (!empty($fields)) {
                $values[] = $id;
                $sql = "UPDATE "._TBL_WEBENGINE_PROJECTS_." SET ".implode(', ', $fields)." WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($values);
            }

            $currentResearchers = $this->getProjectResearchers($id);
            $currentTeachers = $this->getProjectTeachers($id);
            $currentReviewers = $this->getProjectReviewers($id);

            if (array_key_exists('investigadores', $data)) {
                $newResearchers = $data['investigadores'] ?? [];
                if ($this->hasResearchersChanged($currentResearchers, $newResearchers)) {
                    $this->removeAllResearchers($id);
                    if (!empty($newResearchers)) {
                        foreach ($newResearchers as $investigador) {
                            if (!empty($investigador['usuario_uid'])) {
                                $this->addResearcher(
                                    $id, 
                                    $investigador['usuario_uid'], 
                                    $investigador['rol'] ?? 'investigador'
                                );
                            }
                        }
                    }
                }
            }

            if (array_key_exists('docentes', $data)) {
                $newTeachers = $data['docentes'] ?? [];
                if ($this->hasTeachersChanged($currentTeachers, $newTeachers)) {
                    $this->removeAllTeachers($id);
                    if (!empty($newTeachers)) {
                        foreach ($newTeachers as $docente) {
                            if (!empty($docente['usuario_uid'])) {
                                $this->addTeacher(
                                    $id, 
                                    $docente['usuario_uid'], 
                                    $docente['rol'] ?? PROJECT_TEACHER_ROL_ASESOR
                                );
                            }
                        }
                    }
                }
            }

            if (array_key_exists('evaluadores', $data)) {
                $newReviewers = $data['evaluadores'] ?? [];
                if ($this->hasReviewersChanged($currentReviewers, $newReviewers)) {
                    $this->removeAllReviewers($id);
                    if (!empty($newReviewers)) {
                        foreach ($newReviewers as $evaluadorId) {
                            if (!empty($evaluadorId)) {
                                $this->addReviewer($id, $evaluadorId);
                            }
                        }
                    }
                }
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error in updateProject: ".$e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("General error in updateProject: ".$e->getMessage());
            return false;
        }
    }

    private function hasResearchersChanged($current, $new) {
        if (count($current) !== count($new)) return true;
        
        $currentIds = array_column($current, 'usuario_uid');
        $newIds = array_column($new, 'usuario_uid');
        
        sort($currentIds);
        sort($newIds);
        
        return $currentIds !== $newIds;
    }

    private function hasTeachersChanged($current, $new) {
        if (count($current) !== count($new)) return true;
        
        $currentIds = array_column($current, 'usuario_uid');
        $newIds = array_column($new, 'usuario_uid');
        
        sort($currentIds);
        sort($newIds);
        
        return $currentIds !== $newIds;
    }

    private function hasReviewersChanged($current, $new) {
        if (count($current) !== count($new)) return true;
        
        sort($current);
        sort($new);
        
        return $current !== $new;
    }

    private function getProjectResearchers($projectId) {
        $sql = "SELECT "._CLMN_PROJRES_UID_." as usuario_uid, "._CLMN_PROJRES_ROLE_." as rol FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." WHERE "._CLMN_PROJRES_PROJID_." = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getProjectTeachers($projectId) {
        $sql = "SELECT ".COL_PROJECT_TEACHER_USUARIO_UID." as usuario_uid, ".COL_PROJECT_TEACHER_ROL." as rol FROM ".TABLE_PROJECT_TEACHERS." WHERE ".COL_PROJECT_TEACHER_PROJECT_ID." = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getProjectReviewers($projectId) {
        $sql = "SELECT ".DB_REVIEWER_USUARIO_UID." as evaluador_id FROM ".DB_PROJECT_REVIEWERS." WHERE ".DB_REVIEWER_PROJECT_ID." = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getProject($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllProjects() {
        try {
            $sql = "SELECT * FROM "._TBL_WEBENGINE_PROJECTS_." 
                    ORDER BY "._CLMN_WEBENGINE_PROJECT_CREADO_EN_." DESC";
            $stmt = $this->pdo->query($sql);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if (empty($projects)) {
                return [];
            }

            $projectIds = array_column($projects, _CLMN_WEBENGINE_PROJECT_ID_);
            $placeholders = rtrim(str_repeat('?,', count($projectIds)), ',');

            $researchersSql = "
                SELECT 
                    r."._CLMN_PROJRES_PROJID_." as project_id,
                    r."._CLMN_PROJRES_UID_." as id,
                    uc."._CORE_UNOM_." as username,
                    uc."._CORE_UEML_." as email,
                    ud."._DETAIL_FIRSTNAME_." as firstname,
                    ud."._DETAIL_LASTNAME_." as lastname,
                    r."._CLMN_PROJRES_ROLE_." as role,
                    r."._CLMN_PROJRES_STATE_." as state
                FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." r
                LEFT JOIN "._TBL_WEBENGINE_U_CORE_." uc ON r."._CLMN_PROJRES_UID_." = uc."._CORE_UID_."
                LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON r."._CLMN_PROJRES_UID_." = ud."._DETAIL_UID_."
                WHERE r."._CLMN_PROJRES_PROJID_." IN ($placeholders)
                AND r."._CLMN_PROJRES_STATE_." = 'activo'
            ";
            $researchersStmt = $this->pdo->prepare($researchersSql);
            $researchersStmt->execute($projectIds);
            $allResearchers = $researchersStmt->fetchAll(PDO::FETCH_ASSOC);

            $teachersSql = "
                SELECT 
                    t.".COL_PROJECT_TEACHER_PROJECT_ID." as project_id,
                    t.".COL_PROJECT_TEACHER_USUARIO_UID." as id,
                    uc."._CORE_UNOM_." as username,
                    uc."._CORE_UEML_." as email,
                    ud."._DETAIL_FIRSTNAME_." as firstname,
                    ud."._DETAIL_LASTNAME_." as lastname,
                    t.".COL_PROJECT_TEACHER_ROL." as role,
                    t.".COL_PROJECT_TEACHER_ESTADO." as state
                FROM ".TABLE_PROJECT_TEACHERS." t
                LEFT JOIN "._TBL_WEBENGINE_U_CORE_." uc ON t.".COL_PROJECT_TEACHER_USUARIO_UID." = uc."._CORE_UID_."
                LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON t.".COL_PROJECT_TEACHER_USUARIO_UID." = ud."._DETAIL_UID_."
                WHERE t.".COL_PROJECT_TEACHER_PROJECT_ID." IN ($placeholders)
                AND t.".COL_PROJECT_TEACHER_ESTADO." = '".PROJECT_TEACHER_ESTADO_ACTIVO."'
            ";
            $teachersStmt = $this->pdo->prepare($teachersSql);
            $teachersStmt->execute($projectIds);
            $allTeachers = $teachersStmt->fetchAll(PDO::FETCH_ASSOC);

            $reviewersSql = "
                SELECT 
                    r.".DB_REVIEWER_PROJECT_ID." as project_id,
                    r.".DB_REVIEWER_USUARIO_UID." as id,
                    uc."._CORE_UNOM_." as username,
                    uc."._CORE_UEML_." as email,
                    ud."._DETAIL_FIRSTNAME_." as firstname,
                    ud."._DETAIL_LASTNAME_." as lastname,
                    r.".DB_REVIEWER_ESTADO." as state
                FROM ".DB_PROJECT_REVIEWERS." r
                LEFT JOIN "._TBL_WEBENGINE_U_CORE_." uc ON r.".DB_REVIEWER_USUARIO_UID." = uc."._CORE_UID_."
                LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON r.".DB_REVIEWER_USUARIO_UID." = ud."._DETAIL_UID_."
                WHERE r.".DB_REVIEWER_PROJECT_ID." IN ($placeholders)
                AND r.".DB_REVIEWER_ESTADO." = 'activo'
            ";
            $reviewersStmt = $this->pdo->prepare($reviewersSql);
            $reviewersStmt->execute($projectIds);
            $allReviewers = $reviewersStmt->fetchAll(PDO::FETCH_ASSOC);

            $researchersByProject = [];
            foreach ($allResearchers as $researcher) {
                $researchersByProject[$researcher['project_id']][] = [
                    'id' => $researcher['id'],
                    'username' => $researcher['username'],
                    'email' => $researcher['email'],
                    'firstname' => $researcher['firstname'],
                    'lastname' => $researcher['lastname'],
                    'role' => $researcher['role'],
                    'state' => $researcher['state']
                ];
            }

            $teachersByProject = [];
            foreach ($allTeachers as $teacher) {
                $teachersByProject[$teacher['project_id']][] = [
                    'id' => $teacher['id'],
                    'username' => $teacher['username'],
                    'email' => $teacher['email'],
                    'firstname' => $teacher['firstname'],
                    'lastname' => $teacher['lastname'],
                    'role' => $teacher['role'],
                    'state' => $teacher['state']
                ];
            }

            $reviewersByProject = [];
            foreach ($allReviewers as $reviewer) {
                $reviewersByProject[$reviewer['project_id']][] = [
                    'id' => $reviewer['id'],
                    'username' => $reviewer['username'],
                    'email' => $reviewer['email'],
                    'firstname' => $reviewer['firstname'],
                    'lastname' => $reviewer['lastname'],
                    'state' => $reviewer['state']
                ];
            }

            foreach ($projects as &$project) {
                $projectId = $project[_CLMN_WEBENGINE_PROJECT_ID_];
                $project['researchers'] = $researchersByProject[$projectId] ?? [];
                $project['teachers'] = $teachersByProject[$projectId] ?? [];
                $project['reviewers'] = $reviewersByProject[$projectId] ?? [];
            }

            return $projects;

        } catch (PDOException $e) {
            error_log("Error in getAllProjects: ".$e->getMessage());
            return [];
        }
    }

    public function getProjectDuration($projectId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    "._CLMN_WEBENGINE_PROJECT_TIMER_SEGUNDOS_." as timer_segundos
                FROM "._TBL_WEBENGINE_PROJECTS_." 
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            
            $stmt->execute([$projectId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $project ? (int)$project['timer_segundos'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in getProjectDuration: ".$e->getMessage());
            return 0;
        }
    }

    public function deleteProject($projectId) {
        try {
            $this->pdo->beginTransaction();

            // Delete related evaluation sessions first to avoid FK constraint error
            $stmt = $this->pdo->prepare("DELETE FROM webengine_evaluation_sessions WHERE project_id = ?");
            $stmt->execute([$projectId]);

            $tables = [
                _TBL_WEBENGINE_PROJECT_RESEARCHERS_ => _CLMN_PROJRES_PROJID_,
                TABLE_PROJECT_TEACHERS => COL_PROJECT_TEACHER_PROJECT_ID,
                DB_PROJECT_REVIEWERS => DB_REVIEWER_PROJECT_ID
            ];

            foreach ($tables as $table => $column) {
                $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $column = ?");
                $stmt->execute([$projectId]);
            }

            $stmt = $this->pdo->prepare("DELETE FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
            $result = $stmt->execute([$projectId]);

            $this->pdo->commit();
            return $result;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error in deleteProject: ".$e->getMessage());
            return false;
        }
    }

    public function activateProject($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE "._TBL_WEBENGINE_PROJECTS_." SET "._CLMN_WEBENGINE_PROJECT_ACTIVO_." = 1 WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deactivateProject($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE "._TBL_WEBENGINE_PROJECTS_." SET "._CLMN_WEBENGINE_PROJECT_ACTIVO_." = 0 WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function makeProjectPublic($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE "._TBL_WEBENGINE_PROJECTS_." SET "._CLMN_WEBENGINE_PROJECT_VISIBILIDAD_." = 'publico' WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function makeProjectPrivate($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE "._TBL_WEBENGINE_PROJECTS_." SET "._CLMN_WEBENGINE_PROJECT_VISIBILIDAD_." = 'privado' WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPublicProjects() {
        $stmt = $this->pdo->prepare("SELECT * FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_VISIBILIDAD_." = 'publico'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getPrivateProjects() {
        $stmt = $this->pdo->prepare("SELECT * FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_VISIBILIDAD_." = 'privado'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getProjectsByUser($userId) {
        $sql = "
            SELECT DISTINCT p."._CLMN_WEBENGINE_PROJECT_ID_." as id
            FROM "._TBL_WEBENGINE_PROJECTS_." p
            WHERE p."._CLMN_WEBENGINE_PROJECT_ID_." IN (
                SELECT "._CLMN_PROJRES_PROJID_." 
                FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." 
                WHERE "._CLMN_PROJRES_UID_." = ?
                
                UNION
                
                SELECT ".DB_REVIEWER_PROJECT_ID." 
                FROM ".DB_PROJECT_REVIEWERS." 
                WHERE ".DB_REVIEWER_USUARIO_UID." = ?
                
                UNION
                
                SELECT ".COL_PROJECT_TEACHER_PROJECT_ID." 
                FROM ".TABLE_PROJECT_TEACHERS." 
                WHERE ".COL_PROJECT_TEACHER_USUARIO_UID." = ?
            )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
        $projects = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return array_map(function($id) { return ['id' => $id]; }, $projects);
    }

    public function getResearchers($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, uc."._CORE_UNOM_." as username, ud."._DETAIL_FIRSTNAME_." as firstname, ud."._DETAIL_LASTNAME_." as lastname 
            FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." r
            JOIN "._TBL_WEBENGINE_U_CORE_." uc ON r."._CLMN_PROJRES_UID_." = uc."._CORE_UID_."
            LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON r."._CLMN_PROJRES_UID_." = ud."._DETAIL_UID_."
            WHERE r."._CLMN_PROJRES_PROJID_." = ?
            AND r."._CLMN_PROJRES_STATE_." = 'activo'
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function addResearcher($projectId, $userId, $role) {
        $stmt = $this->pdo->prepare("
            INSERT INTO "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." (
                "._CLMN_PROJRES_PROJID_.",
                "._CLMN_PROJRES_UID_.",
                "._CLMN_PROJRES_ROLE_.",
                "._CLMN_PROJRES_STATE_.",
                "._CLMN_PROJRES_CREATED_.",
                "._CLMN_PROJRES_UPDATED_."
            ) VALUES (?, ?, ?, 'activo', NOW(), NOW())
        ");
        return $stmt->execute([$projectId, $userId, $role]);
    }

    public function updateResearcher($projectId, $userId, $role) {
        $stmt = $this->pdo->prepare("
            UPDATE "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." 
            SET "._CLMN_PROJRES_ROLE_." = ?,
                "._CLMN_PROJRES_UPDATED_." = NOW()
            WHERE "._CLMN_PROJRES_PROJID_." = ?
            AND "._CLMN_PROJRES_UID_." = ?
        ");
        return $stmt->execute([$role, $projectId, $userId]);
    }

    public function removeResearcher($projectId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." 
            WHERE "._CLMN_PROJRES_PROJID_." = ?
            AND "._CLMN_PROJRES_UID_." = ?
        ");
        return $stmt->execute([$projectId, $userId]);
    }

    public function removeAllResearchers($projectId) {
        $stmt = $this->pdo->prepare("DELETE FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." WHERE "._CLMN_PROJRES_PROJID_." = ?");
        return $stmt->execute([$projectId]);
    }

    public function getTeachers($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT t.*, uc."._CORE_UNOM_." as username, ud."._DETAIL_FIRSTNAME_." as firstname, ud."._DETAIL_LASTNAME_." as lastname 
            FROM ".TABLE_PROJECT_TEACHERS." t 
            JOIN "._TBL_WEBENGINE_U_CORE_." uc ON t.".COL_PROJECT_TEACHER_USUARIO_UID." = uc."._CORE_UID_."
            LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON t.".COL_PROJECT_TEACHER_USUARIO_UID." = ud."._DETAIL_UID_."
            WHERE t.".COL_PROJECT_TEACHER_PROJECT_ID." = ? 
            AND t.".COL_PROJECT_TEACHER_ESTADO." = '".PROJECT_TEACHER_ESTADO_ACTIVO."'
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function addTeacher($projectId, $userId, $role) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ".TABLE_PROJECT_TEACHERS." (
                ".COL_PROJECT_TEACHER_PROJECT_ID.", 
                ".COL_PROJECT_TEACHER_USUARIO_UID.", 
                ".COL_PROJECT_TEACHER_ROL.", 
                ".COL_PROJECT_TEACHER_ESTADO.", 
                ".COL_PROJECT_TEACHER_CREATED_AT."
            ) VALUES (?, ?, ?, '".PROJECT_TEACHER_ESTADO_ACTIVO."', NOW())
        ");
        return $stmt->execute([$projectId, $userId, $role]);
    }

    public function updateTeacher($projectId, $userId, $role) {
        $stmt = $this->pdo->prepare("
            UPDATE ".TABLE_PROJECT_TEACHERS." 
            SET ".COL_PROJECT_TEACHER_ROL." = ? 
            WHERE ".COL_PROJECT_TEACHER_PROJECT_ID." = ? 
            AND ".COL_PROJECT_TEACHER_USUARIO_UID." = ?
        ");
        return $stmt->execute([$role, $projectId, $userId]);
    }

    public function removeTeacher($projectId, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE ".TABLE_PROJECT_TEACHERS." 
            SET ".COL_PROJECT_TEACHER_ESTADO." = '".PROJECT_TEACHER_ESTADO_REMOVIDO."' 
            WHERE ".COL_PROJECT_TEACHER_PROJECT_ID." = ? 
            AND ".COL_PROJECT_TEACHER_USUARIO_UID." = ?
        ");
        return $stmt->execute([$projectId, $userId]);
    }

    public function removeAllTeachers($projectId) {
        $stmt = $this->pdo->prepare("DELETE FROM ".TABLE_PROJECT_TEACHERS." WHERE ".COL_PROJECT_TEACHER_PROJECT_ID." = ?");
        return $stmt->execute([$projectId]);
    }
    
    public function getReviewers($projectId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.".DB_REVIEWER_USUARIO_UID." as id,
                    uc."._CORE_UNOM_." as username,
                    uc."._CORE_UEML_." as email,
                    ud."._DETAIL_FIRSTNAME_." as firstname,
                    ud."._DETAIL_LASTNAME_." as lastname,
                    r.".DB_REVIEWER_ESTADO." as status,
                    r.".DB_REVIEWER_CREATED_AT." as created_at
                FROM ".DB_PROJECT_REVIEWERS." r
                LEFT JOIN "._TBL_WEBENGINE_U_CORE_." uc ON r.".DB_REVIEWER_USUARIO_UID." = uc."._CORE_UID_."
                LEFT JOIN "._TBL_WEBENGINE_USER_DETAILS_." ud ON r.".DB_REVIEWER_USUARIO_UID." = ud."._DETAIL_UID_."
                WHERE r.".DB_REVIEWER_PROJECT_ID." = ?
                AND r.".DB_REVIEWER_ESTADO." = 'activo'
                ORDER BY ud."._DETAIL_FIRSTNAME_." ASC, ud."._DETAIL_LASTNAME_." ASC
            ");
            
            $stmt->execute([$projectId]);
            $reviewers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $reviewers ?: [];
            
        } catch (PDOException $e) {
            error_log("Error in getReviewers: ".$e->getMessage());
            return [];
        }
    }

    public function addReviewer($projectId, $userId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ".DB_PROJECT_REVIEWERS." (
                ".DB_REVIEWER_PROJECT_ID.", 
                ".DB_REVIEWER_USUARIO_UID.", 
                ".DB_REVIEWER_ESTADO.", 
                ".DB_REVIEWER_CREATED_AT."
            ) VALUES (?, ?, 'activo', NOW())
        ");
        return $stmt->execute([$projectId, $userId]);
    }

    public function updateReviewer($projectId, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE ".DB_PROJECT_REVIEWERS." 
            SET ".DB_REVIEWER_ESTADO." = 'activo', 
                ".DB_REVIEWER_UPDATED_AT." = NOW() 
            WHERE ".DB_REVIEWER_PROJECT_ID." = ? 
            AND ".DB_REVIEWER_USUARIO_UID." = ?
        ");
        return $stmt->execute([$projectId, $userId]);
    }

    public function removeReviewer($projectId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM ".DB_PROJECT_REVIEWERS." 
            WHERE ".DB_REVIEWER_PROJECT_ID." = ? 
            AND ".DB_REVIEWER_USUARIO_UID." = ?
        ");
        return $stmt->execute([$projectId, $userId]);
    }

    public function removeAllReviewers($projectId) {
        $stmt = $this->pdo->prepare("DELETE FROM ".DB_PROJECT_REVIEWERS." WHERE ".DB_REVIEWER_PROJECT_ID." = ?");
        return $stmt->execute([$projectId]);
    }

    public function updateProjectScore($projectId, $score) {
        try {
            $score = max(0.0, min(10.0, (float)$score));
            
            $stmt = $this->pdo->prepare("
                UPDATE "._TBL_WEBENGINE_PROJECTS_." 
                SET "._CLMN_WEBENGINE_PROJECT_PUNTUACION_." = ?,
                    "._CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_." = NOW()
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            return $stmt->execute([$score, $projectId]);
        } catch (PDOException $e) {
            error_log("Error updating project score: ".$e->getMessage());
            return false;
        }
    }

    public function updateProjectQualification($projectId, $qualified) {
        try {
            $qualifiedValue = $qualified ? 1 : 0;
            
            $stmt = $this->pdo->prepare("
                UPDATE "._TBL_WEBENGINE_PROJECTS_." 
                SET "._CLMN_WEBENGINE_PROJECT_CALIFICADO_." = ?,
                    "._CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_." = NOW()
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            return $stmt->execute([$qualifiedValue, $projectId]);
        } catch (PDOException $e) {
            error_log("Error updating project qualification: ".$e->getMessage());
            return false;
        }
    }

    public function updateProjectEvaluation($projectId, $score, $qualified) {
        try {
            $this->pdo->beginTransaction();
            
            $scoreResult = $this->updateProjectScore($projectId, $score);
            
            $qualificationResult = $this->updateProjectQualification($projectId, $qualified);
            
            if ($scoreResult && $qualificationResult) {
                $this->pdo->commit();
                return true;
            }
            
            $this->pdo->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error updating project evaluation: ".$e->getMessage());
            return false;
        }
    }

    public function updateProjectDocument($projectId, $documentPath) {
        try {
            $project = $this->getProject($projectId);
            if (!$project) {
                error_log("Project with ID $projectId not found");
                return false;
            }
            
            if (empty($documentPath)) {
                error_log("Document path cannot be empty");
                return false;
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE "._TBL_WEBENGINE_PROJECTS_." 
                SET "._CLMN_WEBENGINE_PROJECT_DIRECTORIO_." = ?,
                    "._CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_." = NOW()
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            
            $result = $stmt->execute([$documentPath, $projectId]);
            
            if ($result) {
                return true;
            } else {
                error_log("Failed to update project document for project ID: $projectId");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error in updateProjectDocument: ".$e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("General error in updateProjectDocument: ".$e->getMessage());
            return false;
        }
    }

    public function projectExists($projectId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM "._TBL_WEBENGINE_PROJECTS_." 
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            $stmt->execute([$projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ($result && $result['count'] > 0);
            
        } catch (PDOException $e) {
            error_log("Error in projectExists: ".$e->getMessage());
            return false;
        }
    }

    public function countProjects($conditions = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM "._TBL_WEBENGINE_PROJECTS_." WHERE 1=1";
            $params = [];
            
            if (!empty($conditions)) {
                foreach ($conditions as $field => $value) {
                    $validFields = [
                        _CLMN_WEBENGINE_PROJECT_ACTIVO_,
                        _CLMN_WEBENGINE_PROJECT_VISIBILIDAD_,
                        _CLMN_WEBENGINE_PROJECT_FASE_,
                        _CLMN_WEBENGINE_PROJECT_ESTADO_,
                        _CLMN_WEBENGINE_PROJECT_CALIFICADO_,
                        _CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_
                    ];
                    
                    if (in_array($field, $validFields)) {
                        if ($field === _CLMN_WEBENGINE_PROJECT_ESTADO_) {
                            if (is_array($value)) {
                                $placeholders = implode(',', array_fill(0, count($value), '?'));
                                $sql .= " AND $field IN ($placeholders)";
                                $params = array_merge($params, $value);
                            } else {
                                $sql .= " AND $field = ?";
                                $params[] = $value;
                            }
                        } else {
                            $sql .= " AND $field = ?";
                            $params[] = $value;
                        }
                    }
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error in countProjects: ".$e->getMessage());
            return 0;
        }
    }

    public function countProjectsByUser($userId, $conditions = []) {
        try {
            $sql = "
                SELECT COUNT(DISTINCT p."._CLMN_WEBENGINE_PROJECT_ID_.") as total 
                FROM "._TBL_WEBENGINE_PROJECTS_." p
                WHERE p."._CLMN_WEBENGINE_PROJECT_ID_." IN (
                    SELECT "._CLMN_PROJRES_PROJID_." 
                    FROM "._TBL_WEBENGINE_PROJECT_RESEARCHERS_." 
                    WHERE "._CLMN_PROJRES_UID_." = ?
                    
                    UNION
                    
                    SELECT ".DB_REVIEWER_PROJECT_ID." 
                    FROM ".DB_PROJECT_REVIEWERS." 
                    WHERE ".DB_REVIEWER_USUARIO_UID." = ?
                    
                    UNION
                    
                    SELECT ".COL_PROJECT_TEACHER_PROJECT_ID." 
                    FROM ".TABLE_PROJECT_TEACHERS." 
                    WHERE ".COL_PROJECT_TEACHER_USUARIO_UID." = ?
                )
            ";
            
            $params = [$userId, $userId, $userId];
            
            if (!empty($conditions)) {
                foreach ($conditions as $field => $value) {
                    $validFields = [
                        _CLMN_WEBENGINE_PROJECT_ACTIVO_,
                        _CLMN_WEBENGINE_PROJECT_VISIBILIDAD_,
                        _CLMN_WEBENGINE_PROJECT_FASE_,
                        _CLMN_WEBENGINE_PROJECT_ESTADO_
                    ];
                    
                    if (in_array($field, $validFields)) {
                        $sql .= " AND p.$field = ?";
                        $params[] = $value;
                    }
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error in countProjectsByUser: ".$e->getMessage());
            return 0;
        }
    }

    public function getFase($projectId) {
        try {
            $sql = "SELECT "._CLMN_WEBENGINE_PROJECT_FASE_." FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result[_CLMN_WEBENGINE_PROJECT_FASE_] : null;
        } catch (PDOException $e) {
            error_log("Error in getFase: ".$e->getMessage());
            return null;
        }
    }

    public function getDirectory($projectId) {
        try {
            $sql = "SELECT "._CLMN_WEBENGINE_PROJECT_DIRECTORIO_." FROM "._TBL_WEBENGINE_PROJECTS_." WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result[_CLMN_WEBENGINE_PROJECT_DIRECTORIO_] : null;
        } catch (PDOException $e) {
            error_log("Error in getDirectory: ".$e->getMessage());
            return null;
        }
    }

    public function setStatus($projectId, $status) {
        try {
            $validStatuses = ['nuevo', 'en_evaluacion', 'en_revision', 'aprobado', 'rechazado', 'suspendido', 'completado'];

            if (!in_array($status, $validStatuses)) {
                error_log("Invalid project status: $status");
                return false;
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE "._TBL_WEBENGINE_PROJECTS_." 
                SET "._CLMN_WEBENGINE_PROJECT_ESTADO_." = ?,
                    "._CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_." = NOW()
                WHERE "._CLMN_WEBENGINE_PROJECT_ID_." = ?
            ");
            
            $result = $stmt->execute([$status, $projectId]);
            
            if ($result) {
                return true;
            } else {
                error_log("Failed to update project status for project ID: $projectId");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error in setStatus: ".$e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("General error in setStatus: ".$e->getMessage());
            return false;
        }
    }

    public function countProjectsBetweenDates($startDate, $endDate, $dateField = 'creado', $conditions = []) {
        try {
            $dateFields = [
                'creado' => _CLMN_WEBENGINE_PROJECT_CREADO_EN_,
                'actualizado' => _CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_,
                'presentacion' => _CLMN_WEBENGINE_PROJECT_FECHA_PRESENTACION_
            ];
            
            if (!array_key_exists($dateField, $dateFields)) {
                error_log("Invalid date field: $dateField. Valid options: 'creado', 'actualizado', 'presentacion'");
                return 0;
            }
            
            $selectedDateField = $dateFields[$dateField];
            
            $sql = "
                SELECT COUNT(*) as total 
                FROM "._TBL_WEBENGINE_PROJECTS_." 
                WHERE $selectedDateField BETWEEN ? AND ?
            ";
            
            $params = [$startDate, $endDate];
            
            if (!empty($conditions) && is_array($conditions)) {
                foreach ($conditions as $field => $value) {
                    if (!is_string($field)) {
                        continue;
                    }
                    
                    $validFields = [
                        _CLMN_WEBENGINE_PROJECT_ACTIVO_,
                        _CLMN_WEBENGINE_PROJECT_VISIBILIDAD_,
                        _CLMN_WEBENGINE_PROJECT_FASE_,
                        _CLMN_WEBENGINE_PROJECT_ESTADO_,
                        _CLMN_WEBENGINE_PROJECT_CALIFICADO_,
                        _CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_
                    ];
                    
                    if (in_array($field, $validFields)) {
                        if (is_array($value)) {
                            if (!empty($value)) {
                                $placeholders = implode(',', array_fill(0, count($value), '?'));
                                $sql .= " AND $field IN ($placeholders)";
                                $params = array_merge($params, $value);
                            }
                        } else {
                            $sql .= " AND $field = ?";
                            $params[] = $value;
                        }
                    }
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error in countProjectsBetweenDates: ".$e->getMessage());
            return 0;
        }
    }
}