<?php

# Version
define('__WEBSITE_VERSION__', '1.0.0');

# Set Encoding
@ini_set('default_charset', 'utf-8');

# CloudFlare IP Workaround
if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

# CloudFlare HTTPS Workaround
if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
	$_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'on' : 'off';
}

# Global Paths
define('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
define('__ROOT_DIR__', str_replace('\\','/',dirname(dirname(__FILE__))).'/'); // /home/user/public_html/
define('__RELATIVE_ROOT__', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__) : '/');// /
define('__BASE_URL__', SERVER_PROTOCOL.HTTP_HOST.__RELATIVE_ROOT__); // http(s)://www.mysite.com/

# Private Paths
define('__PATH_INCLUDES__', __ROOT_DIR__.'includes/');
define('__PATH_TEMPLATES__', __ROOT_DIR__.'templates/');
define('__PATH_CLASSES__', __PATH_INCLUDES__.'classes/');
define('__PATH_FUNCTIONS__', __PATH_INCLUDES__.'functions/');
define('__PATH_MODULES__', __ROOT_DIR__.'modules/');
define('__PATH_MODULES_USERCP__', __PATH_MODULES__.'user/');
define('__PATH_ADMINCP__', __ROOT_DIR__.'admincp/');
define('__PATH_CONFIGS__', __PATH_INCLUDES__.'config/');
define('__PATH_MODULE_CONFIGS__', __PATH_CONFIGS__.'modules/');

# Public Paths
define('__PATH_ADMINCP_HOME__', __BASE_URL__.'admincp/');
define('__PATH_IMG__', __BASE_URL__.'img/');
define('__PATH_UPLOAD__PROFILE__',__PATH_IMG__.'uploads/profile');
define('__PATH_UPLOAD__GALLERY__',__PATH_IMG__.'uploads/gallery');
define('__PATH_COUNTRY_FLAGS__', __PATH_IMG__.'flags/');
define('__PATH_API__', __BASE_URL__.'api/');

# Timezone
//if(!@include_once(__PATH_CONFIGS__ . 'timezone.php')) throw new Exception('Could not load timezone.');

# Load Libraries
if(!@include_once(__PATH_CLASSES__ . 'class.database.php')) throw new Exception('Ooops!, algo salió mal');
if(!@include_once(__PATH_CLASSES__ . 'class.common.php')) throw new Exception('Ooops!, algo salió mal');
if(!@include_once(__PATH_CLASSES__ . 'class.handler.php')) throw new Exception('Ooops!, algo salió mal');
//if(!@include_once(__PATH_CLASSES__ . 'class.validator.php')) throw new Exception('Could not load class (validator).');
if(!@include_once(__PATH_CLASSES__ . 'class.login.php')) throw new Exception('Ooops!, algo salió mal');
if(!@include_once(__PATH_CLASSES__ . 'class.requests.php')) throw new Exception('Ooops!, algo salió mal');
//if(!@include_once(__PATH_CLASSES__ . 'class.character.php')) throw new Exception('Could not load class (character).');
//if(!@include_once(__PATH_CLASSES__ . 'phpmailer/autoload.php')) throw new Exception('Could not load class (phpmailer).');
//if(!@include_once(__PATH_CLASSES__ . 'class.rankings.php')) throw new Exception('Could not load class (rankings).');
//if(!@include_once(__PATH_CLASSES__ . 'class.news.php')) throw new Exception('Could not load class (news).');
//if(!@include_once(__PATH_CLASSES__ . 'class.plugins.php')) throw new Exception('Could not load class (plugins).');
//if(!@include_once(__PATH_CLASSES__ . 'class.profiles.php')) throw new Exception('Could not load class (profiles).');
//if(!@include_once(__PATH_CLASSES__ . 'class.credits.php')) throw new Exception('Could not load class (credits).');
//if(!@include_once(__PATH_CLASSES__ . 'class.email.php')) throw new Exception('Could not load class (email).');
//if(!@include_once(__PATH_CLASSES__ . 'class.account.php')) throw new Exception('Could not load class (account).');
if(!@include_once(__PATH_CLASSES__ . 'class.connection.php')) throw new Exception('Ooops!, algo salió mal');
//if(!@include_once(__PATH_CLASSES__ . 'class.castlesiege.php')) throw new Exception('Could not load class (castlesiege).');
//if(!@include_once(__PATH_CLASSES__ . 'class.cron.php')) throw new Exception('Could not load class (cron).');
//if(!@include_once(__PATH_CLASSES__ . 'class.cache.php')) throw new Exception('Could not load class (cache).');
//if(!@include_once(__PATH_CLASSES__ . 'paypal/PaypalIPN.php')) throw new Exception('Could not load class (PayalIPN).');
//if(!@include_once(__PATH_CLASSES__ . 'class.upload.php')) throw new Exception('Could not load class (upload).');

# Load Functions
if(!@include_once(__PATH_INCLUDES__ . 'functions.php')) throw new Exception('Ooops!, algo salió mal');

# Website Configurations
$config = webesiteConfigs();
# Check if the website is installed
/*if($config['webengine_cms_installed'] == false) {
	header('Location: '.__BASE_URL__.'install/');
	die();
}
*/

# Template Paths
define('__PATH_TEMPLATE_ROOT__', __PATH_TEMPLATES__ . $config['website_template'] . '/');
define('__PATH_TEMPLATE__', __BASE_URL__ . 'templates/' . $config['website_template'] . '/');
define('__PATH_TEMPLATE_IMG__', __PATH_TEMPLATE__ . 'img/');
define('__PATH_TEMPLATE_CSS__', __PATH_TEMPLATE__ . 'css/');
define('__PATH_TEMPLATE_JS__', __PATH_TEMPLATE__ . 'js/');
define('__PATH_TEMPLATE_FONTS__', __PATH_TEMPLATE__ . 'fonts/');

# Handler Instance
$handler = new Handler();
$handler->loadPage();