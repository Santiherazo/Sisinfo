<?php

class ProfileManager {
    private PDO $pdo;
    private dB $db;
    private UserManager $userManager;
    private UserDetailsManager $userDetailsManager;
    private NotificationManager $notificationManager;
    private ErrorLogger $logger;

    public function __construct(PDO $pdo, dB $db, ErrorLogger $logger) {
        $this->pdo = $pdo;
        $this->db = $db;
        $this->logger = $logger;

        $this->userManager = new UserManager($pdo, $logger);
        $this->userDetailsManager = new UserDetailsManager($db, $logger);
        $this->notificationManager = new NotificationManager($pdo);
    }

    public function registerFullUser(string $username, string $email, string $password, array $details, int $roleId): ?int {
            try {
                if (empty($username) || empty($email) || empty($password)) {
                    throw new Exception("Datos de usuario incompletos.");
                }

                if ($this->userManager->userExists($username)) {
                    throw new Exception("El nombre de usuario ya está en uso.");
                }

                if ($this->userManager->emailExists($email)) {
                    throw new Exception("El correo electrónico ya está registrado.");
                }

                $uid = $this->userManager->createUser($username, $email, $password);
                if (!$uid) {
                    throw new Exception("No se pudo crear el usuario.");
                }

                $inserted = $this->userDetailsManager->insertDetails($uid, $details);
                if (!$inserted) {
                    $this->userManager->deleteUser($uid);
                    throw new Exception("No se pudieron insertar los detalles.");
                }

                $roleManager = new RoleManager($this->pdo);
                $roleAssigned = $roleManager->assignRoleToUser($uid, $roleId);
                if (!$roleAssigned) {
                    $this->userDetailsManager->deleteDetails($uid);
                    $this->userManager->deleteUser($uid);
                    throw new Exception("No se pudo asignar el rol.");
                }

                try {
                    $this->notificationManager->send($uid, "Registro exitoso", "Bienvenido al sistema", "success");
                } catch (Exception $e) {
                    $this->logger?->logException($e, 'PROFILE_REGISTER_NOTIFICATION');
                }

                return $uid;

            } catch (Throwable $e) {
                $this->logger?->logException($e, 'PROFILE_REGISTER_FULL_USER');
                return null;
            }
    }

    public function getProfile(int $uid): ?array {
        try {
            $fullName = $this->userDetailsManager->getFullName($uid);
            $email = $this->userManager->getEmailById($uid);
            $avatar = $this->userDetailsManager->getAvatarPath($uid);
            $notifications = $this->notificationManager->getUnread($uid);

            return [
                'full_name' => $fullName,
                'email' => $email,
                'avatar' => $avatar,
                'unread_notifications' => $notifications
            ];
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_GET');
            return null;
        }
    }

    public function getInfo(int $userId): array {
        $details = $this->userDetailsManager->getDetails($userId);
        $email = $this->getEmail($userId);

        return [
            'first_name'          => $details['first_name'] ?? null,
            'middle_name'         => $details['middle_name'] ?? null,
            'last_name'           => $details['last_name'] ?? null,
            'second_last_name'    => $details['second_last_name'] ?? null,
            'birth_date'          => $details['birth_date'] ?? null,
            'gender'              => $details['gender'] ?? null,
            'country'             => $details['country'] ?? null,
            'city'                => $details['city'] ?? null,
            'address'             => $details['address'] ?? null,
            'id_type'             => $details['id_type'] ?? null,
            'id_number'           => $details['id_number'] ?? null,
            'university'          => $details['university'] ?? null,
            'program'             => $details['program'] ?? null,
            'semester'            => $details['semester'] ?? null,
            'institutional_email' => $details['institutional_email'] ?? null,
            'card_code'           => $details['card_code'] ?? null,
            'phone_number'        => $details['phone_number'] ?? null,
            'email'               => $email ?? null
        ];
    }

    public function updateProfileData(int $uid, array $data): bool {
        try {
        
            $allowedFields = [
                'first_name',
                'middle_name',
                'last_name',
                'second_last_name',
                'birth_date',
                'gender',
                'id_type',
                'id_number',
                'country',
                'city',
                'address',
                'phone_number',
                'institutional_email',
                'university',
                'program',
                'semester',
                'card_code'
            ];

            $filteredData = array_filter(
                $data,
                fn($key) => in_array($key, $allowedFields, true),
                ARRAY_FILTER_USE_KEY
            );

            if (empty($filteredData)) {
                return true;
            }

            return $this->userDetailsManager->updateDetails($uid, $filteredData);
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_UPDATE');
            return false;
        }
    }
    
    public function generateRandomPassword(int $length = 12): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
        $password = '';
        $charsLength = strlen($chars) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charsLength)];
        }
        
        return $password;
    }

    public function getAvatarUrl(int $uid): ?string {
        $path = $this->userDetailsManager->getAvatarPath($uid);
        if (!$path) return null;
        return '/uploads/avatars/' . ltrim($path, '/');
    }

    public function notifyUser(int $uid, string $title, string $message = '', string $type = 'info'): bool {
        return $this->notificationManager->send($uid, $title, $message, $type);
    }

    public function markNotificationAsRead(int $notificationId): bool {
        return $this->notificationManager->markAsRead($notificationId);
    }

    public function getFullName(int $uid): ?string {
        return $this->userDetailsManager->getFullName($uid);
    }

    public function getEmail(int $uid): ?string {
        return $this->userManager->getEmailById($uid);
    }

    public function getInstitutionalEmail(int $uid): ?string {
        return $this->userDetailsManager->getInstitutionalEmail($uid);
    }

    public function getUnreadNotifications(int $uid): array {
        return $this->notificationManager->getUnread($uid);
    }

    public function getAllUsers(): array {
        try {
            $query = "
                SELECT 
                    uc." . _CORE_UID_ . " AS id,
                    uc." . _CORE_UNOM_ . " AS username,
                    uc." . _CORE_UEML_ . " AS email,
                    uc." . _CORE_USTATUS_ . " AS status,
                    uc." . _CORE_UREG_ . " AS created_at,

                    ur." . _CLMN_USER_ROLE_RID_ . " AS role_id,
                    r.name AS role_name,

                    ud." . _DETAIL_FIRSTNAME_ . ",
                    ud." . _DETAIL_MIDDLENAME_ . ",
                    ud." . _DETAIL_LASTNAME_ . ",
                    ud." . _DETAIL_LASTNAME2_ . ",
                    ud." . _DETAIL_BIRTHDATE_ . ",
                    ud." . _DETAIL_GENDER_ . ",
                    ud." . _DETAIL_IDTYPE_ . ",
                    ud." . _DETAIL_IDNUM_ . ",
                    ud." . _DETAIL_COUNTRY_ . ",
                    ud." . _DETAIL_CITY_ . ",
                    ud." . _DETAIL_ADDRESS_ . ",
                    ud." . _DETAIL_PHONE_ . ",
                    ud." . _DETAIL_INST_EMAIL_ . ",
                    ud." . _DETAIL_UNIVERSITY_ . ",
                    ud." . _DETAIL_PROGRAM_ . ",
                    ud." . _DETAIL_SEMESTER_ . ",
                    ud." . _DETAIL_CARD_CODE_ . ",
                    ud." . _DETAIL_PROFILE_IMG_ . "
                FROM " . _TBL_WEBENGINE_U_CORE_ . " uc
                LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " ud ON uc." . _CORE_UID_ . " = ud." . _DETAIL_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur ON uc." . _CORE_UID_ . " = ur." . _CLMN_USER_ROLE_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_ROLES_ . " r ON ur." . _CLMN_USER_ROLE_RID_ . " = r.id
                ORDER BY uc." . _CORE_UREG_ . " DESC
            ";

            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (Throwable $e) {
            $this->logger?->logException($e, 'GET_ALL_USERS');
            return [];
        }
    }

    public function countActiveUsers(array $filters = []): int {
        try {
            $query = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_U_CORE_ . " WHERE " . _CORE_USTATUS_ . " = 1";
            $params = [];
            
            // Filtro por fecha de creación (rango)
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $query .= " AND " . _CORE_UREG_ . " BETWEEN :start_date AND :end_date";
                $params['start_date'] = $filters['start_date'];
                $params['end_date'] = $filters['end_date'];
            }
            
            // Filtro por fecha mínima de creación
            elseif (!empty($filters['start_date'])) {
                $query .= " AND " . _CORE_UREG_ . " >= :start_date";
                $params['start_date'] = $filters['start_date'];
            }
            
            // Filtro por fecha máxima de creación
            elseif (!empty($filters['end_date'])) {
                $query .= " AND " . _CORE_UREG_ . " <= :end_date";
                $params['end_date'] = $filters['end_date'];
            }
            
            // Filtro por rol (si existe la tabla de roles)
            if (!empty($filters['role_id'])) {
                $query .= " AND uc." . _CORE_UID_ . " IN (
                    SELECT " . _CLMN_USER_ROLE_UID_ . " 
                    FROM " . _TBL_WEBENGINE_USER_ROLES_ . " 
                    WHERE " . _CLMN_USER_ROLE_RID_ . " = :role_id
                )";
                $params['role_id'] = $filters['role_id'];
            }
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'COUNT_ACTIVE_USERS');
            return 0;
        }
    }

    public function countActiveUsersBetweenDates(string $startDate, string $endDate): int {
        try {
            $query = "
                SELECT COUNT(*) FROM " . _TBL_WEBENGINE_U_CORE_ . "
                WHERE " . _CORE_UREG_ . " BETWEEN :start AND :end
                AND " . _CORE_USTATUS_ . " = 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'start' => $startDate,
                'end'   => $endDate,
            ]);
            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'COUNT_ACTIVE_USERS_BETWEEN_DATES');
            return 0;
        }
    }
    
    public function getLatestRegisteredUsers(int $limit = 10): array {
        try {
            $query = "
                SELECT 
                    uc." . _CORE_UID_ . " AS id,
                    uc." . _CORE_UNOM_ . " AS username,
                    uc." . _CORE_UEML_ . " AS email,
                    uc." . _CORE_USTATUS_ . " AS status,
                    uc." . _CORE_UREG_ . " AS created_at,

                    ur." . _CLMN_USER_ROLE_RID_ . " AS role_id,
                    r.name AS role_name,

                    ud." . _DETAIL_FIRSTNAME_ . ",
                    ud." . _DETAIL_MIDDLENAME_ . ",
                    ud." . _DETAIL_LASTNAME_ . ",
                    ud." . _DETAIL_LASTNAME2_ . ",
                    ud." . _DETAIL_BIRTHDATE_ . ",
                    ud." . _DETAIL_GENDER_ . ",
                    ud." . _DETAIL_IDTYPE_ . ",
                    ud." . _DETAIL_IDNUM_ . ",
                    ud." . _DETAIL_COUNTRY_ . ",
                    ud." . _DETAIL_CITY_ . ",
                    ud." . _DETAIL_ADDRESS_ . ",
                    ud." . _DETAIL_PHONE_ . ",
                    ud." . _DETAIL_INST_EMAIL_ . ",
                    ud." . _DETAIL_UNIVERSITY_ . ",
                    ud." . _DETAIL_PROGRAM_ . ",
                    ud." . _DETAIL_SEMESTER_ . ",
                    ud." . _DETAIL_CARD_CODE_ . ",
                    ud." . _DETAIL_PROFILE_IMG_ . "
                FROM " . _TBL_WEBENGINE_U_CORE_ . " uc
                LEFT JOIN " . _TBL_WEBENGINE_USER_DETAILS_ . " ud ON uc." . _CORE_UID_ . " = ud." . _DETAIL_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_USER_ROLES_ . " ur ON uc." . _CORE_UID_ . " = ur." . _CLMN_USER_ROLE_UID_ . "
                LEFT JOIN " . _TBL_WEBENGINE_ROLES_ . " r ON ur." . _CLMN_USER_ROLE_RID_ . " = r.id
                ORDER BY uc." . _CORE_UREG_ . " DESC
                LIMIT :limit
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (Throwable $e) {
            $this->logger?->logException($e, 'GET_LATEST_REGISTERED_USERS');
            return [];
        }
    }

    public function enableUser(int $uid): bool {
        try {
            return $this->userManager->updateUserStatus($uid, '1');
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_ENABLE_USER');
            return false;
        }
    }

    public function disableUser(int $uid): bool {
        try {
            return $this->userManager->updateUserStatus($uid, '0');
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_DISABLE_USER');
            return false;
        }
    }

    public function permabanUser(int $uid): bool {
        try {
            return $this->userManager->updateUserStatus($uid, '3');
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_PERMABAN_USER');
            return false;
        }
    }

    public function deleteUserCompletely(int $uid): bool {
        try {
            $detailsDeleted = $this->userDetailsManager->deleteDetails($uid);
            $userDeleted = $this->userManager->deleteUser($uid);

            if (!$detailsDeleted && !$userDeleted) {
                throw new Exception("No se pudo eliminar ningún dato del usuario.");
            }

            return $userDeleted;
        } catch (Throwable $e) {
            $this->logger?->logException($e, 'PROFILE_DELETE_USER_COMPLETELY');
            return false;
        }
    }
}