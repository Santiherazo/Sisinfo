<?php

class ProjectSystemManager {

    protected $projectManager;
    protected $versionManager;
    protected $researcherManager;
    protected $teacherManager;
    protected $reviewerManager;
    protected $lineManager;
    protected $ratingManager;
    protected $ratingSummaryManager;
    protected $ratingReasonManager;
    protected $criteriaManager;
    protected $sessionManager;
    protected $userManager;
    protected $roleManager;
    protected $detailsManager;

    public function __construct(
        ProjectManager $projectManager,
        ProjectVersionManager $versionManager,
        ProjectResearcherManager $researcherManager,
        ProjectTeacherManager $teacherManager,
        ProjectReviewerManager $reviewerManager,
        ResearchLineManager $lineManager,
        RatingManager $ratingManager,
        RatingSummaryManager $ratingSummaryManager,
        RatingReasonManager $ratingReasonManager,
        RatingCriteriaManager $criteriaManager,
        EvaluationSessionManager $sessionManager,
        UserManager $userManager,
        RoleManager $roleManager,
        UserDetailsManager $detailsManager
    ) {
        $this->projectManager         = $projectManager;
        $this->versionManager         = $versionManager;
        $this->researcherManager      = $researcherManager;
        $this->teacherManager         = $teacherManager;
        $this->reviewerManager        = $reviewerManager;
        $this->lineManager            = $lineManager;
        $this->ratingManager          = $ratingManager;
        $this->ratingSummaryManager   = $ratingSummaryManager;
        $this->ratingReasonManager    = $ratingReasonManager;
        $this->criteriaManager        = $criteriaManager;
        $this->sessionManager         = $sessionManager;
        $this->userManager            = $userManager;
        $this->roleManager            = $roleManager;
        $this->detailsManager         = $detailsManager;
    }

    public function getAllProjects(): array {
        return $this->projectManager->getAll();
    }

    public function createProject(array $data): int {
        $projectId = $this->projectManager->create($data['project']);
        $this->versionManager->createInitialVersion($projectId, $data['version'] ?? []);

        if (!empty($data['researchers'])) {
            $this->researcherManager->assignToProject($projectId, $data['researchers']);
        }

        if (!empty($data['teachers'])) {
            $this->teacherManager->assignToProject($projectId, $data['teachers']);
        }

        if (!empty($data['reviewers'])) {
            $this->reviewerManager->assignToProject($projectId, $data['reviewers']);
        }

        return $projectId;
    }

    public function editProject(int $projectId, array $data): bool {
        $updated = $this->projectManager->update($projectId, $data['project']);

        if (isset($data['researchers'])) {
            $this->researcherManager->updateProjectResearchers($projectId, $data['researchers']);
        }

        if (isset($data['teachers'])) {
            $this->teacherManager->updateProjectTeachers($projectId, $data['teachers']);
        }

        if (isset($data['reviewers'])) {
            $this->reviewerManager->updateProjectReviewers($projectId, $data['reviewers']);
        }

        return $updated;
    }

    public function deleteProject(int $projectId): bool {
        $this->researcherManager->removeFromProject($projectId);
        $this->teacherManager->removeFromProject($projectId);
        $this->reviewerManager->removeFromProject($projectId);
        $this->versionManager->deleteByProject($projectId);
        $this->ratingManager->deleteByProject($projectId);
        $this->ratingSummaryManager->deleteByProject($projectId);
        return $this->projectManager->delete($projectId);
    }

    public function getProjectEvaluators(int $projectId): array {
        return $this->reviewerManager->getReviewersByProject($projectId);
    }

    public function startEvaluationSession(int $projectId, int $userId): string {
        return $this->sessionManager->createSession($projectId, $userId);
    }

    public function evaluateProject(int $projectId, int $reviewerId, array $ratings): bool {
        foreach ($ratings as $criteriaId => $value) {
            $this->ratingManager->saveRating($projectId, $reviewerId, $criteriaId, $value);
        }

        $summary = $this->ratingManager->calculateSummary($projectId, $reviewerId);
        return $this->ratingSummaryManager->saveSummary($projectId, $reviewerId, $summary);
    }

    /**
     * Obtener los usuarios que tienen un rol específico (estudiante, docente, evaluador),
     * incluyendo su información de detalle (nombre, correo, avatar).
     */
    public function getUsersByRole(string $roleName): array {
        $users = [];
        $allUsers = $this->getAllUsers();

        foreach ($allUsers as $user) {
            $uid = (int)$user[_CORE_UID_];

            if ($this->roleManager->userHasRole($uid, $roleName)) {
                $details = $this->detailsManager->getDetails($uid);

                $users[] = [
                    'id' => $uid,
                    'username' => $user[_CORE_UNAME_] ?? null,
                    'nombre_completo' => $this->detailsManager->getFullName($uid),
                    'correo_institucional' => $details[_DETAIL_INST_EMAIL_] ?? null,
                    'avatar' => $details[_DETAIL_PROFILE_IMG_] ?? null
                ];
            }
        }

        return $users;
    }

    /**
     * Obtener todos los usuarios del sistema.
     */
    public function getAllUsers(): array {
        $stmt = $this->userManager->db->query("SELECT * FROM " . _TBL_WEBENGINE_U_CORE_);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener datos necesarios para crear un proyecto (usuarios por rol con detalles).
     */
    public function getCreationFormData(): array {
        return [
            'students'   => $this->getUsersByRole('estudiante'),
            'teachers'   => $this->getUsersByRole('docente'),
            'evaluators' => $this->getUsersByRole('evaluador')
        ];
    }
}