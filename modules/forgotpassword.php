<?php
if (isLoggedIn()) redirect();

try {
    if (!mconfig('active')) {
        echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>Este módulo no está habilitado actualmente. Intenta más tarde.</span></div></div>';
        throw new Exception();
    }

    $db = Connection::Database('sisinfo')->getConnection();
    $logger = new ErrorLogger();
    $userManager = new UserManager($db, $logger);
    $resetService = new PasswordResetService($db, $logger);

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $resetData = $resetService->validateToken($token);

        if (!$resetData) {
            echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>El enlace de recuperación ha expirado o es inválido.</span></div></div>';
            throw new Exception();
        }

        if (isset($_POST['resetPassword_submit'])) {
            $resetData = $resetService->validateToken($token);

            if (!$resetData) {
                echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>El enlace ha expirado o ya fue usado.</span></div></div>';
                throw new Exception();
            }

            $newPassword = $_POST['new_password'] ?? '';
            $repeatPassword = $_POST['repeat_password'] ?? '';

            if (!check_value($newPassword) || strlen($newPassword) < 6) {
                echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>La nueva contraseña debe tener al menos 6 caracteres.</span></div></div>';
                throw new Exception();
            }

            if ($newPassword !== $repeatPassword) {
                echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>Las contraseñas no coinciden.</span></div></div>';
                throw new Exception();
            }

            if (!$userManager->updatePasswordByEmail($resetData['email'], $newPassword)) {
                echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error al actualizar la contraseña.</span></div></div>';
                throw new Exception();
            }

            $resetService->deleteToken($token);
            
            echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="check-circle" class="w-5 h-5"></i><span>Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión.</span></div></div>';
            echo '<div class="text-center my-24"><a href="' . htmlspecialchars(__BASE_URL__ . 'login', ENT_QUOTES, 'UTF-8') . '" class="inline-block px-6 py-3 bg-[var(--color-primary)] text-white rounded-lg font-semibold hover:bg-[var(--color-navbar-hover)] transition-colors">Ir al inicio de sesión</a></div>';
            return;
        }

        echo '<section class="min-h-screen bg-[var(--color-bg)] flex items-center justify-center pt-24 py-12 px-4 sm:px-6 lg:px-8">';
        echo '  <div class="max-w-md w-full space-y-8 animate-fadeIn">';
        echo '    <div class="text-center">';
        echo '      <h2 class="text-3xl font-bold text-[var(--color-heading)] mb-2">Restablecer Contraseña</h2>';
        echo '      <p class="text-[var(--color-text-muted)]">Ingresa tu nueva contraseña</p>';
        echo '    </div>';
        
        echo '    <form method="post" class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-border)] space-y-6">';
        echo '      <div>';
        echo '        <label for="new_password" class="block text-sm font-medium text-[var(--color-heading)] mb-2">Nueva Contraseña</label>';
        echo '        <div class="relative">';
        echo '          <i data-lucide="lock" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--color-text-muted)]"></i>';
        echo '          <input type="password" id="new_password" name="new_password" required class="w-full pl-10 pr-4 py-3 border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors" placeholder="Mínimo 6 caracteres">';
        echo '        </div>';
        echo '      </div>';
        
        echo '      <div>';
        echo '        <label for="repeat_password" class="block text-sm font-medium text-[var(--color-heading)] mb-2">Repetir Contraseña</label>';
        echo '        <div class="relative">';
        echo '          <i data-lucide="lock" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--color-text-muted)]"></i>';
        echo '          <input type="password" id="repeat_password" name="repeat_password" required class="w-full pl-10 pr-4 py-3 border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors" placeholder="Repite tu contraseña">';
        echo '        </div>';
        echo '      </div>';
        
        echo '      <button type="submit" name="resetPassword_submit" value="submit" class="w-full py-3 bg-[var(--color-primary)] text-white rounded-lg font-semibold hover:bg-[var(--color-navbar-hover)] transition-colors flex items-center justify-center space-x-2">';
        echo '        <i data-lucide="key" class="w-5 h-5"></i>';
        echo '        <span>Cambiar Contraseña</span>';
        echo '      </button>';
        
        echo '      <div class="text-center">';
        echo '        <a href="' . htmlspecialchars(__BASE_URL__ . 'login', ENT_QUOTES, 'UTF-8') . '" class="text-[var(--color-link)] hover:text-[var(--color-primary)] font-medium transition-colors flex items-center justify-center space-x-2">';
        echo '          <i data-lucide="arrow-left" class="w-4 h-4"></i>';
        echo '          <span>Volver al Inicio de Sesión</span>';
        echo '        </a>';
        echo '      </div>';
        echo '    </form>';
        echo '  </div>';
        echo '</section>';
        return;
    }

    if (isset($_POST['webengineEmail_submit'])) {
        $email = trim($_POST['webengineEmail_current'] ?? '');

        if (!Validator::Email($email)) {
            echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>Por favor ingresa un correo electrónico válido.</span></div></div>';
            throw new Exception();
        }

        if (!$userManager->emailExists($email)) {
            echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>No se encontró una cuenta asociada a ese correo.</span></div></div>';
            throw new Exception();
        }

        $token = $resetService->createRequest($email);
        if (!$token) {
            echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error al generar el enlace de recuperación.</span></div></div>';
            throw new Exception();
        }

        $link = __BASE_URL__ . 'forgotpassword/?token=' . urlencode($token);

        echo '<div id="popup-message" class="fixed top-4 right-4 z-50 animate-slideIn"><div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2"><i data-lucide="check-circle" class="w-5 h-5"></i><span>Se ha enviado un enlace de recuperación a tu correo electrónico.</span></div></div>';
        echo '<div class="text-center my-24">';
        echo '  <a href="' . $link . '" class="inline-block px-6 py-3 bg-[var(--color-primary)] text-white rounded-lg font-semibold hover:bg-[var(--color-navbar-hover)] transition-colors">Recuperar contraseña</a>';
        echo '</div>';
        return;
    }

} catch (Exception $ex) {
}
?>
<section class="min-h-screen bg-[var(--color-bg)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fadeIn">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-[var(--color-heading)] mb-2">Recuperar Contraseña</h2>
            <p class="text-[var(--color-text-muted)]">Te enviaremos un enlace para restablecer tu contraseña</p>
        </div>

        <form method="post" class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-border)] space-y-6">
            <div>
                <label for="webengineEmail_current" class="block text-sm font-medium text-[var(--color-heading)] mb-2">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--color-text-muted)]"></i>
                    <input type="email" 
                           id="webengineEmail_current" 
                           name="webengineEmail_current" 
                           required 
                           class="w-full pl-10 pr-4 py-3 border border-[var(--color-input-border)] bg-[var(--color-input-bg)] text-[var(--color-input-text)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors"
                           placeholder="usuario@ejemplo.com"
                           autocomplete="email">
                </div>
                <p class="text-xs text-[var(--color-text-muted)] mt-2">
                    Ingresa el correo electrónico asociado a tu cuenta
                </p>
            </div>

            <button type="submit" 
                    name="webengineEmail_submit"
                    value="submit"
                    class="w-full py-3 bg-[var(--color-primary)] text-white rounded-lg font-semibold hover:bg-[var(--color-navbar-hover)] transition-colors flex items-center justify-center space-x-2">
                <i data-lucide="send" class="w-5 h-5"></i>
                <span>Enviar Enlace de Recuperación</span>
            </button>

            <div class="text-center">
                <a href="<?php echo htmlspecialchars(__BASE_URL__ . 'login', ENT_QUOTES, 'UTF-8'); ?>" 
                   class="text-[var(--color-link)] hover:text-[var(--color-primary)] font-medium transition-colors flex items-center justify-center space-x-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Volver al Inicio de Sesión</span>
                </a>
            </div>
        </form>

        <div class="bg-[var(--color-surface-alt)] border border-[var(--color-border)] rounded-2xl p-6">
            <div class="flex items-start space-x-3">
                <i data-lucide="info" class="w-5 h-5 text-[var(--color-primary)] mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="font-semibold text-[var(--color-heading)] mb-2">¿Cómo funciona?</h4>
                    <ul class="text-sm text-[var(--color-text)] space-y-1">
                        <li>• Ingresa tu correo electrónico registrado</li>
                        <li>• Recibirás un enlace de recuperación</li>
                        <li>• El enlace expirará en 1 hora por seguridad</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
setTimeout(function() {
    const popup = document.getElementById('popup-message');
    if (popup) {
        popup.remove();
    }
}, 5000);
</script>