<?php
if (isLoggedIn()) redirect();
$logger = new ErrorLogger();
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<section class="min-h-screen bg-[var(--color-bg)] pt-24 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fadeIn">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-[var(--color-heading)] mb-2">Iniciar Sesión</h2>
            <p class="text-[var(--color-text-muted)]">Accede a tu cuenta para gestionar tus proyectos académicos</p>
        </div>

        <form class="bg-[var(--color-surface)] rounded-2xl shadow-lg p-8 border border-[var(--color-border)] space-y-6" method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            
            <div>
                <label for="webengineLogin_user" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Usuario o Correo Electrónico
                </label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--color-text-muted)]"></i>
                    <input type="text" 
                           id="webengineLogin_user" 
                           name="webengineLogin_user" 
                           required 
                           class="w-full pl-10 pr-4 py-3 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors"
                           placeholder="usuario@ejemplo.com"
                           value="<?php echo isset($_POST['webengineLogin_user']) ? htmlspecialchars($_POST['webengineLogin_user']) : ''; ?>">
                </div>
            </div>

            <div>
                <label for="webengineLogin_pwd" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Contraseña
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--color-text-muted)]"></i>
                    <input type="password" 
                           id="webengineLogin_pwd" 
                           name="webengineLogin_pwd" 
                           required 
                           class="w-full pl-10 pr-10 py-3 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-colors"
                           placeholder="••••••••">
                    <button type="button" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-text)] transition-colors"
                            onclick="togglePassword('webengineLogin_pwd')">
                        <i data-lucide="eye" class="w-5 h-5" id="webengineLogin_pwd-eye"></i>
                    </button>
                </div>
                <p class="text-sm text-[var(--color-link)] mt-1">
                    <a href="<?php echo __BASE_URL__; ?>forgotpassword/" class="hover:underline">¿Olvidaste tu contraseña?</a>
                </p>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="remember_me" 
                           name="remember_me" 
                           class="w-4 h-4 text-[var(--color-primary)] border-[var(--color-border)] rounded focus:ring-[var(--color-primary)]"
                           <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>>
                    <label for="remember_me" class="ml-2 block text-sm text-[var(--color-text)]">
                        Recuérdame por 30 días
                    </label>
                </div>
            </div>

            <button type="submit" 
                    name="webengineLogin_submit"
                    value="submit"
                    class="w-full py-3 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg font-semibold hover:opacity-90 transition-opacity flex items-center justify-center space-x-2">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                <span>Iniciar Sesión</span>
            </button>

            <div class="text-center">
                <p class="text-sm text-[var(--color-text-muted)]">
                    ¿No tienes una cuenta? 
                    <a href="#register" class="text-[var(--color-link)] hover:text-[var(--color-primary)] font-medium transition-colors">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </form>
    </div>
</section>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId + '-eye');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordField.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>

<?php
try {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $savedToken = $_SESSION['csrf_token'] ?? '';

    if (
        isset($_POST['webengineLogin_submit']) &&
        check_value($_POST['webengineLogin_submit']) &&
        !empty($submittedToken) &&
        hash_equals($savedToken, $submittedToken)
    ) {
        try {
            $remember = isset($_POST['remember_me']);
            $db = Connection::Database('sisinfo');
            $pdo = $db->getConnection();

            $config = loadConfigurations('login');
            if (!is_array($config)) throw new Exception("Faltan configuraciones de inicio de sesión.");

            $ipBlockManager = new IpBlockManager($pdo, $logger);
            $loginPolicy = new LoginPolicyValidator($config, $ipBlockManager);

            $login = new LoginManager(
                $config,
                new UserCredentialsValidator($pdo),
                new AuthLogger($pdo),
                new SessionManager($pdo),
                new RememberMeService($pdo, $logger),
                $ipBlockManager,
                $loginPolicy
            );

            [$success, $response] = $login->login(
                $_POST['webengineLogin_user'],
                $_POST['webengineLogin_pwd'],
                $remember
            );

            unset($_SESSION['csrf_token']);

            if (!$success) {
                message('error', $response);
            } else {
                $notificationManager = new NotificationManager($pdo);
                $userId = $_SESSION['userid'] ?? null;

                if ($userId) {
                    $notificationManager->send(
                        $userId,
                        'Inicio de sesión exitoso',
                        'Has iniciado sesión correctamente en tu cuenta.',
                        'success',
                        'usercp/'
                    );
                }

                redirect(1, 'usercp/');
            }
        } catch (PDOException $e) {
            $logger->logException($e, 'DATABASE');
            message('error', 'Error de base de datos. Intente más tarde.');
        } catch (Throwable $ex) {
            $logger->logException($ex, 'PHP');
            message('error', 'Ocurrió un error inesperado.');
        }

        echo '<script>
            const msgBox = document.querySelector(".alert");
            if (msgBox) {
                setTimeout(() => {
                    msgBox.style.transition = "opacity 0.5s ease-out";
                    msgBox.style.opacity = "0";
                    setTimeout(() => msgBox.remove(), 500);
                }, 4000);
            }
        </script>';
    }
} catch (Throwable $ex) {
    $logger->logException($ex, 'PHP');
    message('error', 'No se pudo cargar el formulario de inicio de sesión.');

    echo '<script>
        const msgBox = document.querySelector(".alert");
        if (msgBox) {
            setTimeout(() => {
                msgBox.style.transition = "opacity 0.5s ease-out";
                msgBox.style.opacity = "0";
                setTimeout(() => msgBox.remove(), 500);
            }, 4000);
        }
    </script>';
}
?>