<?php 
  if(!isLoggedIn()) { redirect(); }
  if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');
  include(__PATH_MODULES__.'/header.php');

  $userID = $_SESSION['userid'];
  $authLogger = new AuthLogger($pdo);
  $usermanager = new UserManager($pdo);
  $email = $usermanager->getEmailById($userID);
  $logs = $authLogger->getLogsByUserId($userID);

  $statusMsg = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_email'])) {
      $newEmail = trim($_POST['new_email']);
      if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $updated = $usermanager->updateEmailById($userID, $newEmail);
        if ($updated) {
          $email = $newEmail;
          $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-success)]/10 border border-[var(--color-success)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
            <i data-lucide="check-circle" class="w-5 h-5 text-[var(--color-success)] flex-shrink-0"></i>
            <p class="text-[var(--color-success)] font-medium text-sm">Correo actualizado correctamente.</p>
          </div>';
        } else {
          $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
            <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
            <p class="text-[var(--color-danger)] font-medium text-sm">No se pudo actualizar el correo.</p>
          </div>';
        }
      } else {
        $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
          <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
          <p class="text-[var(--color-danger)] font-medium text-sm">Correo inválido.</p>
        </div>';
      }
    }

    if (isset($_POST['update_password'])) {
      $currentPassword = $_POST['current_password'];
      $newPassword = $_POST['new_password'];
      $confirmPassword = $_POST['confirm_password'];

      if ($newPassword !== $confirmPassword) {
          $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
            <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
            <p class="text-[var(--color-danger)] font-medium text-sm">Las nuevas contraseñas no coinciden.</p>
          </div>';
      } elseif (strlen($newPassword) < 6) {
          $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
            <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
            <p class="text-[var(--color-danger)] font-medium text-sm">La contraseña debe tener al menos 6 caracteres.</p>
          </div>';
      } else {
          $storedUser = $usermanager->getUserById($userID);

          if (password_verify($currentPassword, $storedUser['upwd'])) {
              $newHashed = password_hash($newPassword, PASSWORD_BCRYPT);

              $changed = $usermanager->updatePasswordById($userID, $newHashed);

              $statusMsg = $changed
                  ? '<div class="mb-6 p-4 bg-[var(--color-success)]/10 border border-[var(--color-success)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
                      <i data-lucide="check-circle" class="w-5 h-5 text-[var(--color-success)] flex-shrink-0"></i>
                      <p class="text-[var(--color-success)] font-medium text-sm">Contraseña actualizada correctamente.</p>
                    </div>'
                  : '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
                      <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
                      <p class="text-[var(--color-danger)] font-medium text-sm">No se pudo actualizar la contraseña.</p>
                    </div>';
          } else {
              $statusMsg = '<div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
                <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
                <p class="text-[var(--color-danger)] font-medium text-sm">Contraseña actual incorrecta.</p>
              </div>';
          }
      }
  }
  }

  echo $statusMsg;
?>

<div class="max-w-[1400px] mx-auto mb-12">
    <!-- Navegación Mejorada -->
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Proyectos</span>
            </a>
            <?php endif; ?>
            
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-secondary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-secondary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-secondary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Resultados</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-success)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-success)]/10 rounded flex items-center justify-center">
                    <i data-lucide="user" class="w-3 h-3 text-[var(--color-success)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Perfil</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-primary)] text-[var(--color-navbar-text)] border border-[var(--color-primary)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="shield" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Seguridad</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sección Seguridad -->
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 space-y-6 border border-[var(--color-border)] animate-slideUp">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                    <i data-lucide="shield" class="w-5 h-5 text-[var(--color-primary)]"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-heading)]">Seguridad de la cuenta</h3>
                    <p class="text-[var(--color-text-muted)] text-sm">Gestiona tu correo y contraseña</p>
                </div>
            </div>

            <!-- Formulario Correo -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="update_email" value="1">
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-text)]">Correo principal</label>
                    <input type="email" name="new_email" value="<?= htmlspecialchars($email); ?>" 
                           class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                </div>
                <button type="submit" 
                        class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary)]/90 text-[var(--color-navbar-text)] font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>Actualizar correo</span>
                </button>
            </form>

            <!-- Formulario Contraseña -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="update_password" value="1">
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-text)]">Contraseña actual</label>
                    <input type="password" name="current_password" 
                           class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-text)]">Nueva contraseña</label>
                    <input type="password" name="new_password" 
                           class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-text)]">Repetir nueva contraseña</label>
                    <input type="password" name="confirm_password" 
                           class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                </div>
                <button type="submit" 
                        class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary)]/90 text-[var(--color-navbar-text)] font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Actualizar contraseña</span>
                </button>
            </form>
        </div>

        <!-- Sección Actividad -->
        <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-6 border border-[var(--color-border)] animate-slideUp" style="animation-delay: 0.1s">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-[var(--color-secondary)]/10 rounded-lg flex items-center justify-center">
                    <i data-lucide="activity" class="w-5 h-5 text-[var(--color-secondary)]"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-heading)]">Actividad reciente</h3>
                    <p class="text-[var(--color-text-muted)] text-sm">Registro de inicios de sesión</p>
                </div>
            </div>

            <div class="max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-[var(--color-scrollbar)] scrollbar-track-[var(--color-bg)]">
                <table class="w-full text-sm text-left border-t border-[var(--color-border)]">
                    <thead>
                        <tr class="border-b border-[var(--color-border)] text-[var(--color-text-muted)]">
                            <th class="py-3 px-3 font-medium">Fecha y hora</th>
                            <th class="py-3 px-3 font-medium">IP</th>
                            <th class="py-3 px-3 font-medium">Ubicación</th>
                            <th class="py-3 px-3 font-medium">Dispositivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="border-b border-[var(--color-border)] hover:bg-[var(--color-dropdown-hover)] transition-colors">
                                    <td class="py-3 px-3 text-[var(--color-text)] text-sm"><?= date('Y-m-d H:i:s', strtotime($log['time'])) ?></td>
                                    <td class="py-3 px-3 text-[var(--color-text)] text-sm font-mono"><?= htmlspecialchars($log['ip']) ?></td>
                                    <td class="py-3 px-3 text-[var(--color-text)] text-sm">
                                        <a href="https://www.ip-tracker.org/lookup.php?ip=<?= $log['ip'] ?>" target="_blank" 
                                           class="text-[var(--color-link)] hover:text-[var(--color-primary)] transition-colors">
                                            Ver ubicación
                                        </a>
                                    </td>
                                    <td class="py-3 px-3 text-[var(--color-text)] text-sm truncate max-w-[200px]" title="<?= htmlspecialchars($log['agent']) ?>">
                                        <?= htmlspecialchars($log['agent']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-[var(--color-text-muted)]">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>Sin registros recientes</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    setTimeout(() => {
        const msg = document.querySelector('.bg-\\[var\\(--color-success\\)\\]\\/10, .bg-\\[var\\(--color-danger\\)\\]\\/10');
        if (msg) msg.remove();
    }, 4000);
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>