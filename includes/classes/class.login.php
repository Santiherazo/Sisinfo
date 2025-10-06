<?php

class LoginManager {
	private UserCredentialsValidator $credentialsValidator;
	private AuthLogger $authLogger;
	private SessionManager $sessionManager;
	private RememberMeService $rememberMe;
	private IpBlockManager $ipBlockManager;
	private LoginPolicyValidator $loginPolicy;
	private array $_config;

	public function __construct(
		array $config,
		UserCredentialsValidator $credentialsValidator,
		AuthLogger $authLogger,
		SessionManager $sessionManager,
		RememberMeService $rememberMe, // actualizado
		IpBlockManager $ipBlockManager,
		LoginPolicyValidator $loginPolicy
	) {
		$this->_config = $config;
		$this->credentialsValidator = $credentialsValidator;
		$this->authLogger = $authLogger;
		$this->sessionManager = $sessionManager;
		$this->rememberMe = $rememberMe;
		$this->ipBlockManager = $ipBlockManager;
		$this->loginPolicy = $loginPolicy;
	}

	public function login(string $identifier, string $password, bool $remember = false): array {
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		$agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

		// Restricción por horario
		if (!$this->loginPolicy->isWithinAllowedHours()) {
			return [false, 'No está permitido iniciar sesión en este horario.'];
		}

		// Reintentos fallidos: validación de bloqueo por IP
		if ($this->ipBlockManager->isBlocked($ip)) {
			return [false, 'Demasiados intentos fallidos. Intente más tarde.'];
		}

		// Validaciones básicas
		if (!Validator::Email($identifier) && !Validator::AlphaNumeric($identifier)) {
			return [false, 'Usuario inválido.'];
		}

		if (!check_value($password)) {
			return [false, 'Debe ingresar la contraseña.'];
		}

		if (strlen($password) < (int)($this->_config['password_min_length'] ?? 6)) {
			return [false, 'La contraseña no cumple con la longitud mínima requerida.'];
		}

		// Validar credenciales
		[$success, $userOrMsg, $uid] = $this->credentialsValidator->validate($identifier, $password, $ip, $agent);

		if (!$success) {
			$this->authLogger->logAttempt($uid, $ip, $agent, false, $userOrMsg, 'password', 401);
			$this->loginPolicy->handleFailedLogin($ip);
			return [false, $userOrMsg];
		}

		$user = $userOrMsg;
		$username = $user[_CORE_UNOM_] ?? null;

		if (!$uid || !$username) {
			return [false, 'Error al procesar los datos del usuario.'];
		}

		// Generar sesión
		session_regenerate_id(true);
		$_SESSION['valid'] = true;
		$_SESSION['timeout'] = time();
		$_SESSION['userid'] = $uid;
		$_SESSION['username'] = $username;

		// Registrar sesión
		$sessionId = $this->sessionManager->createSession($uid, $remember ? 'remember' : 'normal', $ip, $agent);
		$_SESSION['sid'] = $sessionId;

		// Recordar sesión
		if ($remember && ($this->_config['remember_me_enabled'] ?? false)) {
			$this->rememberMe->createToken($uid, $ip, $agent);
		}

		// Registro exitoso y limpieza de bloqueos
		$this->credentialsValidator->updateLastLogin($uid, $ip);
		$this->authLogger->logAttempt($uid, $ip, $agent, true, 'Inicio de sesión exitoso', 'password', 200, $sessionId);
		$this->loginPolicy->clearFailedAttempts($ip);

		return [true, $user];
	}

	public function logout(): void {
		$sessionId = $_SESSION['sid'] ?? null;
		$userId = $_SESSION['userid'] ?? null;
		$username = $_SESSION['username'] ?? null;
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		$agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

		if ($sessionId) {
			$this->sessionManager->closeSession($sessionId, 'logout');
		}

		if ($userId) {
			$this->authLogger->logAttempt($userId, $ip, $agent, true, 'Cierre de sesión', 'logout', 200, $sessionId);
		}

		if ($username) {
			$this->credentialsValidator->markOffline($username);
		}

		$this->rememberMe->clearToken();

		// Destruir la sesión completamente
		if (session_status() === PHP_SESSION_ACTIVE) {
			$_SESSION = [];
			session_unset();
			session_destroy();
		}

		redirect();
	}
}