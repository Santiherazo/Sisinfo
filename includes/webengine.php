<?php

if (!defined('ACCESS_MODE') || ACCESS_MODE !== 'cron') {
	if (session_status() === PHP_SESSION_NONE) {
		ob_start();
		session_start();
	}
}

define('__WEBENGINE_VERSION__', '1.2');
ini_set('default_charset', 'utf-8');

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
	$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
	$_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';
}

define('HTTP_HOST', filter_var($_SERVER['HTTP_HOST'] ?? 'CLI', FILTER_SANITIZE_STRING));
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://');
define('__ROOT_DIR__', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');
define('__RELATIVE_ROOT__', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__) : '/');
define('__BASE_URL__', SERVER_PROTOCOL . HTTP_HOST . __RELATIVE_ROOT__);

define('__PATH_INCLUDES__', __ROOT_DIR__ . 'includes/');
define('__PATH_UPLOADS__', __ROOT_DIR__ . 'uploads/');
define('__PATH_TEMPLATES__', __ROOT_DIR__ . 'templates/');
define('__PATH_CLASSES__', __PATH_INCLUDES__ . 'classes/');
define('__PATH_FUNCTIONS__', __PATH_INCLUDES__ . 'functions/');
define('__PATH_MODULES__', __ROOT_DIR__ . 'modules/');
define('__PATH_MODULES_USERCP__', __PATH_MODULES__ . 'usercp/');
define('__PATH_CACHE__', __PATH_INCLUDES__ . 'cache/');
define('__PATH_ADMINCP__', __ROOT_DIR__ . 'cpanel/');
define('__PATH_ADMINCP_INC__', __ROOT_DIR__ . 'cpanel/inc/');
define('__PATH_ADMINCP_MODULES__', __ROOT_DIR__ . 'cpanel/modules/');
define('__PATH_EPANEL__', __ROOT_DIR__ . 'app/');
define('__PATH_EPANEL_INC__', __ROOT_DIR__ . 'app/inc/');
define('__PATH_EPANEL_MODULES__', __ROOT_DIR__ . 'app/modules/');
define('__PATH_CONFIGS__', __PATH_INCLUDES__ . 'config/');
define('__PATH_MODULE_CONFIGS__', __PATH_CONFIGS__ . 'modules/');
define('__PATH_LOGS__', __PATH_INCLUDES__ . 'logs/');

define('__PATH_ADMINCP_HOME__', __BASE_URL__ . 'cpanel/');
define('__PATH_EPANEL_HOME__', __BASE_URL__ . 'app/');
define('__PATH_IMG__', __BASE_URL__ . 'img/');
define('AUTOSAVE_DIR', __PATH_CACHE__ . 'autosave/');
define('SESSION_DATA_DIR', __PATH_CACHE__ . 'session_data/');

define('WEBENGINE_DATABASE_ERRORLOG', __PATH_LOGS__ . 'database_errors.log');
define('WEBENGINE_WRITABLE_PATHS', __PATH_CONFIGS__ . 'writable.paths.json');
define('WEBENGINE_PHP_ERRORLOG', __PATH_LOGS__ . 'php_errors.log');

ini_set('log_errors', 1);
ini_set('error_log', WEBENGINE_PHP_ERRORLOG);

if (!include_once(__PATH_CONFIGS__ . 'webengine.tables.php')) throw new Exception('Could not load WebEngine CMS table definitions.');
if (!include_once(__PATH_CONFIGS__ . 'timezone.php')) throw new Exception('Could not load timezone.');
if (!include_once(__PATH_CLASSES__ . 'class.ErrorLogger.php')) throw new Exception('Could not load class (ErrorLogger).');
if (!include_once(__PATH_CLASSES__ . 'class.database.php')) throw new Exception('Could not load class (database).');
if (!include_once(__PATH_CLASSES__ . 'class.handler.php')) throw new Exception('Could not load class (handler).');
if (!include_once(__PATH_CLASSES__ . 'class.validator.php')) throw new Exception('Could not load class (validator).');
if (!include_once(__PATH_CLASSES__ . 'class.AccessControlManager.php')) throw new Exception('Could not load class (AccessControlManager).');
if (!include_once(__PATH_CLASSES__ . 'class.NotificationManager.php')) throw new Exception('Could not load class (class.NotificationManager).');
if (!include_once(__PATH_CLASSES__ . 'class.RoleManager.php')) throw new Exception('Could not load class (RoleManager).');
if (!include_once(__PATH_CLASSES__ . 'class.UserManager.php')) throw new Exception('Could not load class (UserManager).');
if (!include_once(__PATH_CLASSES__ . 'class.PasswordResetService.php')) throw new Exception('Could not load class (PasswordResetService).');
if (!include_once(__PATH_CLASSES__ . 'class.RememberMeService.php')) throw new Exception('Could not load class (RememberMeService).');
if (!include_once(__PATH_CLASSES__ . 'class.usercredentialsvalidator.php')) throw new Exception('Could not load class (usercredentialsvalidator).');
if (!include_once(__PATH_CLASSES__ . 'class.authlogger.php')) throw new Exception('Could not load class (authlogger).');
if (!include_once(__PATH_CLASSES__ . 'class.loginpolicy.php')) throw new Exception('Could not load class (loginpolicy).');
if (!include_once(__PATH_CLASSES__ . 'class.AutoSave.php')) throw new Exception('Could not load class (AutoSave).');
if (!include_once(__PATH_CLASSES__ . 'class.IpBlockManager.php')) throw new Exception('Could not load class (IpBlockManager).');
if (!include_once(__PATH_CLASSES__ . 'class.UserPhotoManager.php')) throw new Exception('Could not load class (UserPhotoManager).');
if (!include_once(__PATH_CLASSES__ . 'class.SessionManager.php')) throw new Exception('Could not load class (SessionManager).');
if (!include_once(__PATH_CLASSES__ . 'class.UserDetailsManager.php')) throw new Exception('Could not load class (UserDetailsManager).');
if (!include_once(__PATH_CLASSES__ . 'class.login.php')) throw new Exception('Could not load class (login).');
if (!include_once(__PATH_CLASSES__ . 'class.projectManager.php')) throw new Exception('Could not load class (projectManager).');
if (!include_once(__PATH_CLASSES__ . 'class.Cache.php')) throw new Exception('Could not load class (Cache).');
if (!include_once(__PATH_CLASSES__ . 'class.ProjectResearchersManager.php')) throw new Exception('Could not load class (ProjectResearchersManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.ProjectTeacherManager.php')) throw new Exception('Could not load class (ProjectTeacherManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.researchLineManager.php')) throw new Exception('Could not load class (researchLineManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.ratingSummaryManager.php')) throw new Exception('Could not load class (ratingSummaryManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.RatingReasonManager.php')) throw new Exception('Could not load class (RatingReasonManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.RatingCriteriaManager.php')) throw new Exception('Could not load class (RatingCriteriaManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.EvaluationSessionManager.php')) throw new Exception('Could not load class (EvaluationSessionManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.EvaluationManager.php')) throw new Exception('Could not load class (EvaluationManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.ReevaluationManager.php')) throw new Exception('Could not load class (ReevaluationManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.ProfileManager.php')) throw new Exception('Could not load class (ProfileManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.fileManager.php')) throw new Exception('Could not load class (fileManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.BlogManager.php')) throw new Exception('Could not load class (BlogManager).'); #nuevo
if (!include_once(__PATH_CLASSES__ . 'class.PermissionManager.php')) throw new Exception('Could not load class (PermissionManagers).');
if (!include_once(__PATH_CLASSES__ . 'class.connection.php')) throw new Exception('Could not load class (connection).');
if (!include_once(__PATH_CLASSES__ . 'class.uploadManager.php')) throw new Exception('Could not load class (uploadManager).');
if (!include_once(__PATH_INCLUDES__ . 'functions.php')) throw new Exception('Could not load functions.');

$config = webengineConfigs();
if (!is_array($config)) throw new Exception('La configuración global es inválida.');

if ($config['webengine_cms_installed'] == false) {
	header('Location: ' . __BASE_URL__ . 'install/');
	exit;
}

if (!include_once(__PATH_CONFIGS__ . 'compatibility.php')) throw new Exception('Could not load file compatibility.');
if (!array_key_exists(strtolower($config['server_files']), $webengine['file_compatibility'])) throw new Exception('The server files configuration is not valid.');

if (!file_exists(__PATH_TEMPLATES__ . $config['website_template'])) throw new Exception('The default template doesn\'t exist.');
if (!check_value($config['SQL_DB_HOST'])) throw new Exception('The database host configuration is required to connect to your database.');
if (!check_value($config['SQL_DB_NAME'])) throw new Exception('The database name configuration is required to connect to your database.');
if (!check_value($config['SQL_DB_USER'])) throw new Exception('The database user configuration is required to connect to your database.');
if (!check_value($config['SQL_DB_PORT'])) throw new Exception('The database port configuration is required to connect to your database.');

if (!include_once(__PATH_CONFIGS__ . $webengine['file_compatibility'][strtolower($config['server_files'])]['file'])) throw new Exception('Could not load the table definitions.');

if (!$config['system_active'] && ACCESS_MODE !== 'cron') {
	$username = $_SESSION['username'] ?? '';
	if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username) || !array_key_exists($username, $config['admins'])) {
		header('Location: ' . $config['maintenance_page']);
		exit;
	}
	echo '<div style="text-align:center;border-bottom:1px solid #aa0000;padding:15px;background:#000;color:#ff0000;font-size:12pt;">';
	echo 'OFFLINE MODE';
	echo '</div>';
}

if (!empty($config['ip_block_system_enable']) && checkBlockedIp()) {
	throw new Exception('Your IP address has been blocked.');
}

define('__PATH_TEMPLATE_ROOT__', __PATH_TEMPLATES__ . $config['website_template'] . '/');
define('__PATH_TEMPLATE__', __BASE_URL__ . 'templates/' . $config['website_template'] . '/');
define('__URL_AVATAR__', __PATH_TEMPLATE__. 'img/');
define('__PATH_AVATAR__', __PATH_TEMPLATES__ . $config['website_template']. '/img/avatars/');
define('__PATH_TEMPLATE_IMG__', __PATH_TEMPLATE__ . 'img/');
define('__PATH_TEMPLATE_CSS__', __PATH_TEMPLATE__ . 'css/');
define('__PATH_TEMPLATE_JS__', __PATH_TEMPLATE__ . 'js/');
define('__PATH_TEMPLATE_FONTS__', __PATH_TEMPLATE__ . 'fonts/');

$handler = new Handler();
$handler->loadPage();