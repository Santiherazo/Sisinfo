<?php

class Handler {
	
	private $_disableWebEngineFooterVersion = false;
	private $_disableWebEngineFooterCredits = false;

    public function loadPage() {
        global $config,$lang,$custom,$tSettings;
        
        $handler = $this;
        
        switch(access) {
            case 'index':
                if(!$this->templateExists($config['website_template'])) {
                    throw new Exception('The chosen template cannot be loaded ('.$config['website_template'].').');
                }
                include(__PATH_TEMPLATES__ . $config['website_template'] . '/index.php');
                break;
            case 'admin':
                // Handle admin page loading
                break;
            case 'install':
                // Handle installation page loading
                break;
            // Other cases
            default:
                throw new Exception('Access forbidden.');
        }
    }

    private function templateExists($template) {
		if(file_exists(__PATH_TEMPLATES__ . $template . '/index.php')) return true;
		return false;
	}
    

	public function loadModule($page = 'news', $subpage = 'home') {
		global $config,$lang,$custom,$mconfig,$tSettings;
		
		$handler = $this;
		$page = $this->cleanRequest($page);
		$subpage = $this->cleanRequest($subpage);
		
		$this->_parseRequest();
		
		if(!check_value($page)) { $page = 'news'; }
		
		if(!check_value($subpage)) {
			if($this->moduleExists($page)) {
				@loadModuleConfigs($page);
				include(__PATH_MODULES__ . $page . '.php');
			} else {
				$this->module404();
			}
		} else {
			// Handle subpages
		}
	}
	
	// Other methods
	
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
	
	// Other private methods
	
}
?>
