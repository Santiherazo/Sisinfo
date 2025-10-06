<?php
declare(strict_types=1);
if (!defined('access') || access !== 'install') exit;

$logger = new ErrorLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_step_4_submit'])) {
    try {
        if (
            empty($_SESSION['install_sql_host']) ||
            empty($_SESSION['install_sql_db1']) ||
            empty($_SESSION['install_sql_user']) ||
            !isset($_SESSION['install_sql_pass'])
        ) {
            throw new Exception('Faltan datos de conexión.');
        }

        $sisinfo = new dB(
            $_SESSION['install_sql_host'],
            $_SESSION['install_sql_port'],
            $_SESSION['install_sql_db1'],
            $_SESSION['install_sql_user'],
            $_SESSION['install_sql_pass']
        );

        $id_type = preg_replace('/[^A-Z]/', '', strtoupper(trim($_POST['id_type'] ?? '')));
        $id_number = preg_replace('/[^0-9]/', '', trim($_POST['id_number'] ?? ''));
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $department = htmlspecialchars(trim($_POST['department'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (!$email) throw new Exception('Correo electrónico inválido.');
        if (empty($id_type) || empty($id_number)) throw new Exception('Documento de identidad incompleto.');
        if (empty($password) || empty($confirm_password)) throw new Exception('La contraseña es obligatoria.');
        if ($password !== $confirm_password) throw new Exception('Las contraseñas no coinciden.');

        $first_name = 'Administrador';
        $last_name = 'Usuario';
        $username = strtolower('admin' . substr($id_number, -3));
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sisinfo->beginTransaction();
        $success = true;

        try {
            $insert1 = $sisinfo->query(
                "INSERT INTO webengine_u_core (upwd, unom, ueml) VALUES (?, ?, ?)",
                [$hashedPassword, $username, $email]
            );
            if (!$insert1) throw new Exception;
            $userId = (int) $sisinfo->lastInsertId();
        } catch (Throwable $e) {
            $logger->logDatabaseError($e->getMessage(), $e->getFile(), $e->getLine());
            $success = false;
        }

        try {
            $insert2 = $sisinfo->query(
                "INSERT INTO webengine_user_details (user_id, first_name, last_name, id_type, id_number, city, address, phone_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$userId, $first_name, $last_name, $id_type, $id_number, $department, $address, $phone]
            );
            if (!$insert2) throw new Exception;
        } catch (Throwable $e) {
            $logger->logDatabaseError($e->getMessage(), $e->getFile(), $e->getLine());
            $success = false;
        }

        try {
            $insert3 = $sisinfo->query(
                "INSERT IGNORE INTO webengine_user_roles (user_id, role_id) VALUES (?, ?)",
                [$userId, 1]
            );
            if (!$insert3) throw new Exception;
        } catch (Throwable $e) {
            $logger->logDatabaseError($e->getMessage(), $e->getFile(), $e->getLine());
            $success = false;
        }

        $sisinfo->endTransaction($success);

        if (!$success) {
            $logger->logPhpError("Error al registrar el usuario administrador.", __FILE__, __LINE__);
            throw new Exception("No se pudo completar el registro.");
        }

        $_SESSION['install_cstep']++;
        http_response_code(302);
        header('Location: install.php', true, 302);
        exit;
    } catch (Throwable $ex) {
        $logger->logPhpError($ex->getMessage(), $ex->getFile(), $ex->getLine());
    }
}
?>

<h3 class="text-xl font-semibold mb-6 text-center text-gray-800">Configuración del Usuario Administrador</h3>

<form method="post" class="space-y-6 max-w-2xl mx-auto bg-white p-8 rounded shadow">
    <fieldset class="space-y-4">
        <legend class="text-lg font-medium text-gray-800">General</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="first_name" value="Administrador" readonly class="mt-1 w-full px-3 py-2 border rounded bg-gray-100 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Apellido</label>
                <input type="text" name="last_name" value="Usuario" readonly class="mt-1 w-full px-3 py-2 border rounded bg-gray-100 text-gray-600">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" name="email" required class="mt-1 w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                <select name="id_type" required class="mt-1 w-full px-3 py-2 border rounded">
                    <option value="">--Seleccione--</option>
                    <option value="CC" <?= (($_POST['id_type'] ?? '') === 'CC') ? 'selected' : '' ?>>Cedula de ciudadanía</option>
                    <option value="TI" <?= (($_POST['id_type'] ?? '') === 'TI') ? 'selected' : '' ?>>Tarjeta de identidad</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Número de documento</label>
                <input type="text" name="id_number" required pattern="\d{4,20}" class="mt-1 w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($_POST['id_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4">
        <div class="flex justify-between items-center">
            <legend class="text-lg font-medium text-gray-800">Seguridad</legend>
            <button id="generateBtn" type="button" class="px-4 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200 border">Generar contraseña aleatoria</button>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div class="relative">
                <input id="password" type="password" name="password" minlength="6" required placeholder="Contraseña" class="w-full px-3 py-2 border rounded pr-10" value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="button" class="absolute top-1/2 right-2 -translate-y-1/2 text-sm text-gray-600 toggle-visibility" data-target="password">👁️</button>
            </div>
            <div class="relative">
                <input id="confirm_password" type="password" name="confirm_password" minlength="6" required placeholder="Confirmar contraseña" class="w-full px-3 py-2 border rounded pr-10" value="<?= htmlspecialchars($_POST['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="button" class="absolute top-1/2 right-2 -translate-y-1/2 text-sm text-gray-600 toggle-visibility" data-target="confirm_password">👁️</button>
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4">
        <legend class="text-lg font-medium text-gray-800">Opcional</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-700">Departamento</label>
                <input type="text" name="department" class="mt-1 w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($_POST['department'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label class="block text-sm text-gray-700">Teléfono</label>
                <input type="tel" name="phone" class="mt-1 w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-700">Dirección</label>
                <input type="text" name="address" class="mt-1 w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
    </fieldset>

    <div class="pt-6">
        <button type="submit" name="install_step_4_submit" class="w-full px-5 py-3 bg-green-600 text-white font-semibold rounded hover:bg-green-700">Crear Usuario</button>
    </div>
</form>

<script nonce="<?= $nonce ?>">
document.addEventListener('DOMContentLoaded', function () {
    const generateBtn = document.getElementById('generateBtn');
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');

    generateBtn.addEventListener('click', function () {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%!'; 
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordField.value = password;
        confirmField.value = password;
    });

    document.querySelectorAll('.toggle-visibility').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            input.type = input.type === 'text' ? 'password' : 'text';
            btn.textContent = input.type === 'text' ? '🙈' : '👁️';
        });
    });
});
</script>