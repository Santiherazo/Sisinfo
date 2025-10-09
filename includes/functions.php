<?php
function check_value($value) {
	if((@count((array)$value)>0 and !@empty($value) and @isset($value)) || $value=='0') {
		return true;
	}
}

function redirect($type = 1, $location = null, $delay = 0) {
	if(!check_value($location)) {
		$to = __BASE_URL__;
	} else {
		$to = __BASE_URL__ . $location;
		
		if($location == 'login') {
			$_SESSION['login_last_location'] = $_REQUEST['page'].'/';
			if(isset($_REQUEST['subpage'])) {
				$_SESSION['login_last_location'] .= $_REQUEST['subpage'].'/';
			}
		}
	}

	switch($type) {
		default:
			header('Location: '.$to.'');
			die();
		break;
		case 1:
			header('Location: '.$to.'');
			die();
		break;
		case 2:
			echo '<meta http-equiv="REFRESH" content="'.$delay.';url='.$to.'">';
		break;
		case 3:
			header('Location: '.$location.'');
			die();
		break;
	}
}

function isLoggedIn() {
	if(!isset($_SESSION['valid'])) return;
	if(!isset($_SESSION['userid'])) return;
	if(!isset($_SESSION['username'])) return;
	if(!isset($_SESSION['timeout'])) return;
	
	$loginConfigs = loadConfigurations('login');
	if(is_array($loginConfigs)) {
		if($loginConfigs['enable_session_timeout']) {
			if(time()-$_SESSION['timeout'] > $loginConfigs['session_timeout']) {
				logOutUser();
			}
		}
	}
	
	$_SESSION['timeout'] = time();
	return true;
}

function logOutUser() {
    try {
        $db = Connection::Database('sisinfo');
        $pdo = $db->getConnection();

        $uid = $_SESSION['userid'] ?? null;
        $username = $_SESSION['username'] ?? null;
        $sessionId = $_SESSION['sid'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        if ($uid && $username) {
            $userValidator = new UserCredentialsValidator($pdo);
            $userValidator->markOffline($username);

            if ($sessionId) {
                $sessionManager = new SessionManager($pdo);
                $sessionManager->closeSession($sessionId, 'logout');
            }

            $authLogger = new AuthLogger($pdo);
            $authLogger->logAttempt(
                $uid,
                $ip,
                $agent,
                true,
                'Cierre de sesión',
                'logout',
                200,
                $sessionId
            );
        }

        // Elimina token de "Recordarme" si existe
        if (class_exists('RememberMeService')) {
            $remember = new RememberMeService($pdo);
            $remember->clearToken();
        }

        // Destruye sesión y cookies
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }

    } catch (Throwable $e) {
        error_log('[Logout Error] ' . $e->getMessage());
    }

    redirect();
}

function message($type='info', $message="", $title="") {
	switch($type) {
		case 'error':
			$class = ' alert-danger';
		break;
		case 'success':
			$class = ' alert-success';
		break;
		case 'warning':
			$class = ' alert-warning';
		break;
		default:
			$class = ' alert-info';
		break;
	}
	
	if(check_value($title)) {
		echo '<div class="alert'.$class.'" role="alert"><strong>'.$title.'</strong><br />'.$message.'</div>';
	} else {
		echo '<div class="alert'.$class.'" role="alert">'.$message.'</div>';
	}
}

function debug($value) {
	echo '<pre>';
		print_r($value);
	echo '</pre>';
}

function accessManager(): AccessControlManager {
    try {
        $db = Connection::Database('sisinfo');
        $pdo = $db->getConnection();
        $logger = new ErrorLogger();

        $roleManager = new RoleManager($pdo, $logger);
        $permissionManager = new PermissionManager($pdo, $logger);
        $userManager = new UserManager($pdo, $logger);

        return new AccessControlManager($roleManager, $permissionManager, $userManager);
    } catch (Throwable $e) {
        error_log('[AccessManager Error] ' . $e->getMessage());
        throw $e;
    }
}

function BuildCacheData($data_array) {
	$result = null;
	if(is_array($data_array)) {
		foreach($data_array as $row) {
			$count = count($row);
			$i = 1;
			foreach($row as $data) {
				$result .= $data;
				if($i < $count) {
					$result .= '¦';
				}
				$i++;
			}
			$result .= "\n";
		}
		return $result;
	} else {
		return null;
	}
}

function UpdateCache($file_name, $data) {
	$file = __PATH_CACHE__.$file_name;
	if(!file_exists($file)) return;
	if(!is_writable($file)) return;
	
	$fp = fopen($file, 'w');
	fwrite($fp, time()."\n");
	fwrite($fp, $data);
	fclose($fp);
	return true;
}

function LoadCacheData($file_name) {
	$file = __PATH_CACHE__.$file_name;
	if(!file_exists($file)) return;
	if(!is_readable($file)) return;
	
	$cache_file = file_get_contents($file);
	if(empty($cache_file)) return;
	$file_lanes = explode("\n",$cache_file);
	$nlines = count($file_lanes);
	for($i=0; $i<$nlines; $i++) {
		if(check_value($file_lanes[$i])) {
			$line_data[$i] = explode("¦",$file_lanes[$i]);
		}
	}
	return $line_data;
}

function sec_to_hms($input_seconds=0) {
	$result = sec_to_dhms($input_seconds);
	if(!is_array($result)) return array(0,0,0);
	return array((($result[0]*24)+$result[1]), $result[2], $result[3]);
}

function sec_to_dhms($input_seconds=0) {
	if($input_seconds < 1) return array(0,0,0,0);
	$days_module = $input_seconds % 86400;
	$days = ($input_seconds-$days_module)/86400;
	$hours_module = $days_module % 3600;
	$hours = ($days_module-$hours_module)/3600;
	$minutes_module = $hours_module % 60;
	$minutes = ($hours_module-$minutes_module)/60;
	$seconds = $minutes_module;
	return array($days,$hours,$minutes,$seconds);
}

function cs_CalculateTimeLeft() {
	return 0;
}

function updateCronLastRun($file) {
	$database = Connection::Database('Me_MuOnline');
	$update = $database->query("UPDATE ".WEBENGINE_CRON." SET cron_last_run = ? WHERE cron_file_run = ?", array(time(), $file));
	if(!$update) return;
	return true;
}

function webengineConfigs() {
	if(!file_exists(__PATH_CONFIGS__ . 'webengine.json')) throw new Exception('WebEngine\'s configuration file doesn\'t exist, please reupload the website files.');
	
	$webengineConfigs = file_get_contents(__PATH_CONFIGS__ . 'webengine.json');
	if(!check_value($webengineConfigs)) throw new Exception('WebEngine\'s configuration file is empty, please run the installation script.');
	
	return json_decode($webengineConfigs, true);
}

function config($config_name, $return = false) {
	$config = webengineConfigs();
	if(!array_key_exists($config_name, $config)) return;
	if($return) {
		return $config[$config_name];
	} else {
		echo $config[$config_name];
	}
}

function convertXML($object) {
	return json_decode(json_encode($object), true);
}

function getFilesInDirectory(string $directory, bool $includeHidden = false): array {
	$dirs = [];
	$realPath = realpath($directory);

	if ($realPath === false || !is_dir($realPath) || !is_readable($realPath)) {
		return [];
	}

	if ($handle = opendir($realPath)) {
		while (($entry = readdir($handle)) !== false) {
			if ($entry === '.' || $entry === '..') continue;
			if (!$includeHidden && $entry[0] === '.') continue;

			$fullPath = $realPath . DIRECTORY_SEPARATOR . $entry;

			if (is_dir($fullPath)) {
				$dirs[] = $entry;
			}
		}
		closedir($handle);
	}

	return $dirs;
}

function loadModuleConfigs($module) {
	global $mconfig;
	if(moduleConfigExists($module)) {
		$xml = simplexml_load_file(__PATH_MODULE_CONFIGS__.$module.'.xml');
		$mconfig = array();
		if($xml) {
			$moduleCONFIGS = convertXML($xml->children());
			$mconfig = $moduleCONFIGS;
		}
	}
}

function moduleConfigExists($module) {
	if(file_exists(__PATH_MODULE_CONFIGS__.$module.'.xml')) {
		return true;
	}
}

function globalConfigExists($config_file) {
	if(file_exists(__PATH_CONFIGS__.$config_file.'.xml')) {
		return true;
	}
}

function mconfig($configuration) {
	global $mconfig;
	if(!is_array($mconfig)) return null;
	if(array_key_exists($configuration, $mconfig)) {
		return $mconfig[$configuration];
	} else {
		return null;
	}
}

function gconfig($config_file,$return=true) {
	global $gconfig;
	if(globalConfigExists($config_file)) {
		$xml = simplexml_load_file(__PATH_CONFIGS__.$config_file.'.xml');
		$gconfig = array();
		if($xml) {
			$globalCONFIGS = convertXML($xml->children());
			if($return) {
				return $globalCONFIGS;
			} else {
				$gconfig = $globalCONFIGS;
			}
		}
	}
}

function loadConfigurations($file) {
	if(!check_value($file)) return;
	if(!moduleConfigExists($file)) return;
	$xml = simplexml_load_file(__PATH_MODULE_CONFIGS__ . $file . '.xml');
	if($xml) return convertXML($xml->children());
	return;
}

function loadConfig($name="webengine") {
	if(!check_value($name)) return;
	if(!file_exists(__PATH_CONFIGS__ . $name . '.json')) return;
	$cfg = file_get_contents(__PATH_CONFIGS__ . $name . '.json');
	if(!check_value($cfg)) return;
	return json_decode($cfg, true);
}

function guildProfile($guildName, $returnLinkOnly=false) {
	if(!config('guild_profiles',true)) return $guildName;
	
	$profileConfig = loadConfigurations('profiles');
	if(is_array($profileConfig) && array_key_exists('encode', $profileConfig) && $profileConfig['encode'] == 1) {
		if($returnLinkOnly) {
			return __BASE_URL__.'profile/guild/req/'.base64url_encode($guildName);
		}
		return '<a href="'.__BASE_URL__.'profile/guild/req/'.base64url_encode($guildName).'/" target="_blank">'.$guildName.'</a>';
	}
	if($returnLinkOnly) {
		return __BASE_URL__.'profile/guild/req/'.urlencode($guildName);
	}
	return '<a href="'.__BASE_URL__.'profile/guild/req/'.urlencode($guildName).'/" target="_blank">'.$guildName.'</a>';
}

function encodeCache($data, $pretty=false) {
	if($pretty) return json_encode($data, JSON_PRETTY_PRINT);
	return json_encode($data);
}

function decodeCache($data) {
	return json_decode($data, true);
}

function updateCacheFile($fileName, $data) {
	$file = __PATH_CACHE__ . $fileName;
	if(!file_exists($file)) return;
	if(!is_writable($file)) return;
	
	$fp = fopen($file, 'w');
	fwrite($fp, $data);
	fclose($fp);
	return true;
}

function loadCache($fileName) {
	$file = __PATH_CACHE__ . $fileName;
	if(!file_exists($file)) return;
	if(!is_readable($file)) return;
	
	$cacheDataRaw = file_get_contents($file);
	if(!check_value($cacheDataRaw)) return;
	
	$cacheData = decodeCache($cacheDataRaw);
	if(!is_array($cacheData)) return;
	
	return $cacheData;
}

function checkBlockedIp() {
	if(in_array(access, array('cron'))) return;
	if(!isset($_SERVER['REMOTE_ADDR'])) return true;
	if(!Validator::Ip($_SERVER['REMOTE_ADDR'])) return true;
	$blockedIpCache = loadCache('blocked_ip.cache');
	if(!is_array($blockedIpCache)) return;
	if(in_array($_SERVER['REMOTE_ADDR'], $blockedIpCache)) return true;
}

function getCronList() {
	$db = Connection::Database('Me_MuOnline');
	$result = $db->query_fetch("SELECT * FROM ".WEBENGINE_CRON." ORDER BY cron_id ASC");
	if(!is_array($result)) return;
	return $result;
}

function loadJsonFile($filePath) {
	if(!file_exists($filePath)) return;
	if(!is_readable($filePath)) return;
	$jsonData = file_get_contents($filePath);
	if($jsonData == false) return;
	$result = json_decode($jsonData, true);
	if(!is_array($result)) return;	
	return $result;
}

function getCountryCodeFromIp($ip) {
	$api = 'http://ip-api.com/json/'.$ip.'?fields=status,countryCode';
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $api);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($handle);
    curl_close($handle);
    if(!check_value($json)) return;
    $result = json_decode($json, true);
	if(!is_array($result)) return;
	if($result['status'] == 'fail') return;
	if(!check_value($result['countryCode'])) return;
	return $result['countryCode'];
}

function getCountryFlag($countryCode='default') {
	if(!check_value($countryCode)) $countryCode = 'default';
	return __PATH_COUNTRY_FLAGS__ . strtolower($countryCode) . '.gif';
}

function getDirectoryListFromPath($path) {
	if(!file_exists($path)) return;
	$files = scandir($path);
	foreach($files as $row) {
		if(in_array($row, array('.','..'))) continue;
		if(!is_dir($path.$row)) continue;
		$result[] = $row;
	}
	if(!is_array($result)) return;
	return $result;
}

function custom($index) {
	global $custom;
	if(!is_array($custom)) return;
	if(!array_key_exists($index, $custom)) return;
	return $custom[$index];
}

function base64url_encode($data) {
	$b64 = base64_encode($data . '!we');
	if ($b64 === false) return false;
	$url = strtr($b64, '+/', '-_');
	return rtrim($url, '=');
}

function base64url_decode($data, $strict=false) {
	$b64 = strtr($data, '-_', '+/');
	$decoded = base64_decode($b64, $strict);
	$end = substr($decoded, -3);
	if($end !== '!we') return;
	return substr($decoded, 0, -3);
}