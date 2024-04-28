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
    

	public function loadModule($page = 'news',$subpage = 'home') {
		global $config,$lang,$custom,$mconfig,$tSettings;
		try {
			$handler = $this;
			$page = $this->cleanRequest($page);
			$subpage = $this->cleanRequest($subpage);
			
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
			
			if(!check_value($page)) { $page = 'news'; }
			
			if(!check_value($subpage)) {
				if($this->moduleExists($page)) {
					@loadModuleConfigs($page);
					include(__PATH_MODULES__ . $page . '.php');
				} else {
					
				}
			} else {
				// HANDLE PAGE AS DIRECTORY (PATH)
				switch($page) {
					case 'news':
						if($this->moduleExists($page)) {
							@loadModuleConfigs($page);
							include('./templates/gebgames/inc/modules/header.php');
							include(__PATH_MODULES__. $page . '.php');
						} else {
							
						}
					break;
					default:
						$path = $page.'/'.$subpage;
						if($this->moduleExists($path)) {
							$cnf = $page.'.'.$subpage;
							@loadModuleConfigs($cnf);
							include(__PATH_MODULES__ . $path . '.php');
						} else {
							
						}
					break;
				}
			}
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	private function moduleExists($page) {
		if(file_exists(__PATH_MODULES__ . $page . '.php')) return true;
		return false;
	}
	
	private function module404() {
		$this->redirect(); // Cambio aquí para llamar a la función redirect()
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
	
	// Other private methods
	
	// Función para redireccionar
	private function redirect($url = '/') {
		header("Location: $url");
		exit();
	}
}

?>
