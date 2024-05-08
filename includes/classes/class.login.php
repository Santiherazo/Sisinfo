<?php

class login {
	
	public function validateLogin($username, $password) {
		//if(!check_value($username)) throw new Exception('Error en la cuenta de usuario');
		//if(!check_value($password)) throw new Exception('Error en la contraseña');
	    header("Location: /dashboard");
		//if(!$this->canLogin($_SERVER['REMOTE_ADDR'])) throw new Exception('oppps!');
		/*if($this->common->userExists($username)){
			# login success
			$this->removeFailedLogins($_SERVER['REMOTE_ADDR']);
			session_regenerate_id();
			$_SESSION['valid'] = true;
			$_SESSION['timeout'] = time();
			$_SESSION['userid'] = $userId;
			$_SESSION['username'] = $accountData[_CLMN_USERNM_];
			
			# redirect to usercp
			
			
		} else {
			# failed login
			//$this->addFailedLogin($username,$_SERVER['REMOTE_ADDR']);
			message('error', 'The password you provided it\'s not correct, please try again.');
			message('warning', 'The username length must be 4 to 10 characters.');
		}*/
	}
	
	public function logout() {
		$_SESSION = array();
		session_destroy();
		redirect();
	}

}