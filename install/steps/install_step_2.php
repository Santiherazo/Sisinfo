<?php
if (!defined('access') || !access || access !== 'install') {
	http_response_code(403);
	exit('Acceso denegado.');
}

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$logger = new ErrorLogger();

function get_input($key, $default = '') {
	return htmlspecialchars(trim($_POST[$key] ?? $default), ENT_QUOTES, 'UTF-8');
}

function is_valid_hostname_or_ip($host) {
	return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || filter_var($host, FILTER_VALIDATE_IP);
}

function increment_failed_attempts() {
	if (!isset($_SESSION['failed_attempts'])) {
		$_SESSION['failed_attempts'] = 0;
	}
	$_SESSION['failed_attempts']++;
	if ($_SESSION['failed_attempts'] > 3) {
		sleep(3);
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_step_2_submit'])) {
	try {
		if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
			throw new Exception('Token CSRF inválido.');
		}

		$host = get_input('install_step_2_1');
		$port = get_input('install_step_2_7');
		$user = get_input('install_step_2_2');
		$pass = $_POST['install_step_2_3'] ?? '';
		$db   = get_input('install_step_2_4');
		$dev  = isset($_POST['install_step_2_dev']) && $_POST['install_step_2_dev'] === 'on';

		if (empty($host) || empty($port) || empty($user) || empty($db)) {
			throw new Exception('Todos los campos obligatorios deben completarse.');
		}

		if (!is_valid_hostname_or_ip($host)) {
			throw new Exception('Host inválido.');
		}

		if (!preg_match('/^\d{2,5}$/', $port)) {
			throw new Exception('Puerto inválido.');
		}

		if (mb_strlen($host) > 100 || mb_strlen($user) > 100 || mb_strlen($db) > 100) {
			throw new Exception('Uno o más campos exceden la longitud permitida.');
		}

		if (!$dev && (empty($pass) || strlen($pass) < 8)) {
			throw new Exception('Contraseña no válida.');
		}

		$_SESSION['install_sql_host'] = $host;
		$_SESSION['install_sql_port'] = $port;
		$_SESSION['install_sql_user'] = $user;
		$_SESSION['install_sql_pass'] = $pass;
		$_SESSION['install_sql_db1']  = $db;
		$_SESSION['install_sql_dev']  = $dev;

		$db1 = new dB($host, $port, $db, $user, $pass);
		if ($db1->dead) {
			$logger->logDatabaseError('Error de conexión con la base de datos.', __FILE__, __LINE__);
			increment_failed_attempts();
			handleCriticalError('Conexión fallida. Verifica los datos ingresados o consulta con el administrador.');
		}

		$_SESSION['install_cstep']++;
		session_regenerate_id(true);
		header('Location: install.php');
		exit();
	} catch (Exception $ex) {
		increment_failed_attempts();

		$msg = strtolower($ex->getMessage());
		if (str_contains($msg, 'sql') || str_contains($msg, 'base de datos') || str_contains($msg, 'conexión')) {
			$logger->logDatabaseError($ex->getMessage(), $ex->getFile(), $ex->getLine());
		} else {
			$logger->logPhpError('Error del sistema: ' . $ex->getMessage(), $ex->getFile(), $ex->getLine());
		}

		//handleCriticalError('Conexión fallida. Verifica los datos ingresados o consulta con el administrador.');
	}
}
?>

<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 space-y-8 text-gray-800">
	<h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
		<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v9a2 2 0 002 2z" />
		</svg>
		Conexión a la Base de Datos
	</h2>

	<form method="post" class="space-y-6" autocomplete="off">
		<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

		<div>
			<label for="input_1" class="block text-sm font-medium text-gray-700">Host <span class="text-red-500">*</span></label>
			<input type="text" id="input_1" name="install_step_2_1" required maxlength="100"
				class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
				value="<?= htmlspecialchars($_SESSION['install_sql_host'] ?? 'localhost') ?>">
		</div>

		<div>
			<label for="input_7" class="block text-sm font-medium text-gray-700">Puerto <span class="text-red-500">*</span></label>
			<input type="text" id="input_7" name="install_step_2_7" required pattern="\d{2,5}"
				class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
				value="<?= htmlspecialchars($_SESSION['install_sql_port'] ?? '3306') ?>">
		</div>

		<div>
			<label for="input_2" class="block text-sm font-medium text-gray-700">Usuario <span class="text-red-500">*</span></label>
			<input type="text" id="input_2" name="install_step_2_2" required maxlength="100"
				class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
				value="<?= htmlspecialchars($_SESSION['install_sql_user'] ?? 'root') ?>">
		</div>

		<div>
			<label for="input_3" class="block text-sm font-medium text-gray-700">Contraseña <?= empty($_SESSION['install_sql_dev']) ? '<span class="text-red-500">*</span>' : '' ?></label>
			<input type="password" id="input_3" name="install_step_2_3"
				class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
				value="">
		</div>

		<div>
			<label for="input_4" class="block text-sm font-medium text-gray-700">Base de Datos <span class="text-red-500">*</span></label>
			<input type="text" id="input_4" name="install_step_2_4" required maxlength="100"
				class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
				value="<?= htmlspecialchars($_SESSION['install_sql_db1'] ?? 'sisinfo') ?>">
		</div>

		<div class="flex items-center gap-2 mt-4">
			<input type="checkbox" id="dev_mode" name="install_step_2_dev" <?= !empty($_SESSION['install_sql_dev']) ? 'checked' : '' ?>
				class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
			<label for="dev_mode" class="text-sm text-gray-600">Es un entorno de desarrollo (permite contraseña vacía)</label>
		</div>

		<div>
			<button type="submit" name="install_step_2_submit" value="continue"
				class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-all">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
				</svg>
				Continuar
			</button>
		</div>
	</form>
</div>