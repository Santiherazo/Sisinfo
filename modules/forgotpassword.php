<?php
if (isLoggedIn()) redirect();

echo '<div class="page-title text-xl font-semibold text-[var(--color-heading)] mb-6"><span>Recuperar Contraseña</span></div>';

try {
    if (!mconfig('active')) {
        throw new Exception('Este módulo no está habilitado actualmente. Intenta más tarde.');
    }

    $db = Connection::Database('sisinfo')->getConnection();
    $logger = new ErrorLogger();
    $userManager = new UserManager($db, $logger);
    $resetService = new PasswordResetService($db, $logger);

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $resetData = $resetService->validateToken($token);

        if (!$resetData) throw new Exception('El enlace de recuperación ha expirado o es inválido.');

        if (isset($_POST['resetPassword_submit'])) {
            $resetData = $resetService->validateToken($token);

            if (!$resetData) throw new Exception('El enlace ha expirado o ya fue usado.');

            $newPassword = $_POST['new_password'] ?? '';
            $repeatPassword = $_POST['repeat_password'] ?? '';

            if (!check_value($newPassword) || strlen($newPassword) < 6) {
                throw new Exception('La nueva contraseña debe tener al menos 6 caracteres.');
            }

            if ($newPassword !== $repeatPassword) {
                throw new Exception('Las contraseñas no coinciden.');
            }

            if (!$userManager->updatePasswordByEmail($resetData['email'], $newPassword)) {
                throw new Exception('Error al actualizar la contraseña.');
            }

            $resetService->deleteToken($token);
            
            message('success', 'Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión.');
                echo '<div class="text-center mt-6"><a href="' . htmlspecialchars(__BASE_URL__ . 'login', ENT_QUOTES, 'UTF-8') . '" class="inline-block px-4 py-2 bg-[var(--color-primary)] text-white rounded hover:bg-[var(--color-navbar-hover)]">Ir al inicio de sesión</a></div>';
            return;
        }

        echo '<div class="max-w-xl mx-auto mt-10 px-4">';
        echo '  <form method="post" class="space-y-6 bg-[var(--color-surface)] p-6 rounded shadow">';
        echo '    <div>';
        echo '      <label class="block text-sm font-medium text-[var(--color-heading)]">Nueva contraseña</label>';
        echo '      <input type="password" name="new_password" required class="mt-1 block w-full rounded-md border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] shadow-sm">';
        echo '    </div>';
        echo '    <div>';
        echo '      <label class="block text-sm font-medium text-[var(--color-heading)]">Repetir contraseña</label>';
        echo '      <input type="password" name="repeat_password" required class="mt-1 block w-full rounded-md border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] shadow-sm">';
        echo '    </div>';
        echo '    <div class="flex justify-between">';
        echo '      <a href="login" class="inline-flex items-center text-sm text-[var(--color-text-muted)] hover:underline">Cancelar</a>';
        echo '      <button type="submit" name="resetPassword_submit" value="submit" class="px-4 py-2 bg-[var(--color-primary)] text-white rounded hover:bg-[var(--color-navbar-hover)]">Cambiar contraseña</button>';
        echo '    </div>';
        echo '  </form>';
        echo '</div>';
        return;
    }

    if (isset($_POST['webengineEmail_submit'])) {
        $email = trim($_POST['webengineEmail_current'] ?? '');

        if (!Validator::Email($email)) {
            throw new Exception('Por favor ingresa un correo electrónico válido.');
        }

        if (!$userManager->emailExists($email)) {
            throw new Exception('No se encontró una cuenta asociada a ese correo.');
        }

        $token = $resetService->createRequest($email);
        if (!$token) throw new Exception('Error al generar el enlace de recuperación.');

        $link = __BASE_URL__ . 'forgotpassword/?token=' . urlencode($token);

        echo '<div class="text-center mt-6">';
        echo '  <a href="' . $link . '" class="inline-block px-4 py-2 bg-[var(--color-primary)] text-white rounded hover:bg-[var(--color-navbar-hover)]">Recuperar contraseña</a>';
        echo '</div>';
        return;
    }

    echo '<div class="max-w-xl mx-auto mt-10 px-4">';
    echo '  <form method="post" class="space-y-6 bg-[var(--color-surface)] p-6 rounded shadow">';
    echo '    <div>';
    echo '      <label for="webengineEmail" class="block text-sm font-medium text-[var(--color-heading)]">Correo electrónico asociado a tu cuenta</label>';
    echo '      <input type="email" id="webengineEmail" name="webengineEmail_current" required autocomplete="email" class="mt-1 block w-full rounded-md border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] shadow-sm">';
    echo '    </div>';
    echo '    <div class="flex justify-end">';
    echo '      <button type="submit" name="webengineEmail_submit" value="submit" class="px-4 py-2 bg-[var(--color-primary)] text-white rounded hover:bg-[var(--color-navbar-hover)]">Enviar enlace de recuperación</button>';
    echo '    </div>';
    echo '  </form>';
    echo '</div>';

} catch (Exception $ex) {
    message('error', $ex->getMessage());
}