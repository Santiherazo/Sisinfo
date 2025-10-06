<?php
if (isLoggedIn()) redirect();

$logger = new ErrorLogger();

if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo '<div class="max-w-md mx-auto mt-12 p-6 bg-[var(--color-surface)] shadow-lg rounded-xl border border-[var(--color-border)]">';
echo '<h2 class="text-2xl font-semibold text-center text-[var(--color-heading)] mb-6">Iniciar Sesión</h2>';

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
                new RememberMeService($pdo, $logger), // ← aquí el cambio
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

    echo '<form class="space-y-4" method="post" action="" class="bg-[var(--color-surface)] text-[var(--color-text)]">';
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];

    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">';

    echo '<div class="space-y-4">';
        echo '<div>';
            echo '<label for="webengineLogin_user" class="block text-sm font-medium text-[var(--color-text)]">Usuario o Correo</label>';
            echo '<input type="text" name="webengineLogin_user" id="webengineLogin_user" required class="mt-1 w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition">';
        echo '</div>';

        echo '<div>';
            echo '<label for="webengineLogin_pwd" class="block text-sm font-medium text-[var(--color-text)]">Contraseña</label>';
            echo '<input type="password" name="webengineLogin_pwd" id="webengineLogin_pwd" required class="mt-1 w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition">';
            echo '<p class="text-sm text-[var(--color-link)] mt-1"><a href="' . __BASE_URL__ . 'forgotpassword/" class="hover:underline">¿Olvidaste tu contraseña?</a></p>';
        echo '</div>';

        echo '<div class="flex items-center">';
            echo '<input type="checkbox" id="remember_me" name="remember_me" class="h-4 w-4 text-[var(--color-primary)] focus:ring-[var(--color-primary)] border-[var(--color-border)] rounded">';
            echo '<label for="remember_me" class="ml-2 block text-sm text-[var(--color-text)]">Recuérdame por 30 días</label>';
        echo '</div>';

        echo '<div>';
            echo '<button type="submit" name="webengineLogin_submit" value="submit" class="w-full py-2 px-4 bg-[var(--color-primary)] text-white font-semibold rounded-md hover:bg-[var(--color-navbar-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] transition">Iniciar Sesión</button>';
        echo '</div>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
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