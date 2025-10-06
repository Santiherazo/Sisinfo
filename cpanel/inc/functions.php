<?php
function admincp_base($module="") {
	if(check_value($module)) return __PATH_ADMINCP_HOME__ . "?module=" . $module;
	return __PATH_ADMINCP_HOME__;
}


function userName($userId, $initialsOnly = false) {
    $db = Connection::Database(config('SQL_DB_NAME', true));
    $pdo = $db->getConnection();

    $logger = new ErrorLogger();
    $profileManager = new ProfileManager($pdo, $db, $logger);

    $fullName = $profileManager->getFullName($userId);

    if ($initialsOnly) {
        $words = explode(' ', $fullName);
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
        }
        return $initials;
    }

    return $fullName;
}