<?php

class Handler {
    public function loadPage() {
        global $config;
        $handler = $this;
        switch(access) {
            case 'index':
                if(!$this->templateExists($config['website_template'])) {
                    throw new Exception('The chosen template cannot be loaded ('.$config['website_template'].').');
                }
                include(__PATH_TEMPLATES__ . $config['website_template'] . '/index.php');
                break;
            case 'dashboard':
                break;
            case 'app':
                break;
            case 'install':
                break;
            default:
                throw new Exception('Access forbidden.');
        }
    }

    private function templateExists($template) {
		if(file_exists(__PATH_TEMPLATES__ . $template . '/index.php')) return true;
		return false;
	}
    
	private function loadModuleFromUrl() {
        $domain = $_SERVER['HTTP_HOST'];
        $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $urlWithoutDomain = str_replace($domain, '', $currentUrl);
        $urlWithoutDomain = trim($urlWithoutDomain, '/');
        $urlSegments = explode('/', $urlWithoutDomain);
        $page = isset($urlSegments[0]) ? $urlSegments[0] : null;
        $subpage = isset($urlSegments[1]) ? $urlSegments[1] : null;
        $this->loadModule($page, $subpage);
    }
    
    private function loadModule($page, $subpage) {
        global $config;
        try {
            $handler = $this;
            $page = $this->cleanRequest($page);
            $subpage = $this->cleanRequest($subpage);
    
            if (empty($page)) {
                include(__PATH_MODULES__ . 'home.php');
                return;
            }
    
            if (!$subpage) {
                if ($this->moduleExists($page)) {
                    include(__PATH_MODULES__ . $page . '.php');
                } else {
                    echo 'not found';
                }
            } else {
                $path = $page . '/' . $subpage;
                if ($this->moduleExists($path)) {
                    $cnf = $page . '.' . $subpage;
                    @loadModuleConfigs($cnf);
                    include(__PATH_MODULES__ . $path . '.php');
                } else {
                    echo 'Oooops!, algo salió mal';
                }
            }
        } catch (Exception $ex) {
            message('error', $ex->getMessage());
        }
    }
    

    private function moduleExists($page) {
		if(file_exists(__PATH_MODULES__ . $page . '.php')) return true;
		return false;
	}
	
	private function module404() {
		redirect();
	}
	
	private function cleanRequest($string) {
		return preg_replace("/[^a-zA-Z0-9\s\/]/", "", $string);
	}
	
	private function _parseRequest() {
		if(!isset($_GET['request'])) return;
		
		$request = explode("/", $_GET['request']);
		if(is_array($request)) {
			for($i = 0; $i < count($request); $i++) {
				if(check_value($request[$i])) {
					if(check_value($request[$i+1])) {
						$_GET[$request[$i]] = filter_var($request[$i+1], FILTER_SANITIZE_STRING);
					} else {
						$_GET[$request[$i]] = NULL;
					}
				}
				$i++;
			}
		}
	}	
}

function message($type, $message) {
    echo "<div class=\"$type\">$message</div>";
}

?>
