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
          $statusMsg = '<div id="statusMsg" class="text-green-600">Correo actualizado correctamente.</div>';
        } else {
          $statusMsg = '<div id="statusMsg" class="text-red-600">No se pudo actualizar el correo.</div>';
        }
      } else {
        $statusMsg = '<div id="statusMsg" class="text-red-600">Correo inválido.</div>';
      }
    }

    if (isset($_POST['update_password'])) {
      $currentPassword = $_POST['current_password'];
      $newPassword = $_POST['new_password'];
      $confirmPassword = $_POST['confirm_password'];

      if ($newPassword !== $confirmPassword) {
          $statusMsg = '<div id="statusMsg" class="text-red-600">Las nuevas contraseñas no coinciden.</div>';
      } elseif (strlen($newPassword) < 6) {
          $statusMsg = '<div id="statusMsg" class="text-red-600">La contraseña debe tener al menos 6 caracteres.</div>';
      } else {
          $storedUser = $usermanager->getUserById($userID);

          if (password_verify($currentPassword, $storedUser['upwd'])) {
              $newHashed = password_hash($newPassword, PASSWORD_BCRYPT);

              $changed = $usermanager->updatePasswordById($userID, $newHashed);

              $statusMsg = $changed
                  ? '<div id="statusMsg" class="text-green-600">Contraseña actualizada correctamente.</div>'
                  : '<div id="statusMsg" class="text-red-600">No se pudo actualizar la contraseña.</div>';
          } else {
              $statusMsg = '<div id="statusMsg" class="text-red-600">Contraseña actual incorrecta.</div>';
          }
      }
  }
  }

  echo $statusMsg;
?>
<script>
  setTimeout(() => {
    const msg = document.getElementById('statusMsg');
    if (msg) msg.remove();
  }, 4000);
</script>

<div class="mt-8 border-b pb-3 flex flex-wrap gap-4 sm:gap-6 overflow-x-auto sm:overflow-visible">
  <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
    <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Proyectos</a>
  <?php endif; ?>
  <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
    <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Resultados</a>
  <?php endif; ?>
    <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Perfil</a>
    <a href="<?php echo __BASE_URL__.'usercp/myphoto';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Fotografía</a>
    <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" class="text-[var(--color-accent)] font-medium border-b-2 border-[var(--color-accent)] pb-1 whitespace-nowrap">Seguridad</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">
  <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-5 space-y-6 border border-[var(--color-border)]">
    <h3 class="text-lg font-semibold mb-4 text-[var(--color-text)]">Seguridad de la cuenta</h3>

    <form method="POST" class="space-y-4">
      <input type="hidden" name="update_email" value="1">
      <div>
        <label class="block font-medium mb-1 text-[var(--color-text)]">Correo principal</label>
        <input type="email" name="new_email" value="<?= htmlspecialchars($email); ?>" class="w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition">
      </div>
      <button type="submit" class="w-full py-2 px-4 bg-[var(--color-primary)] text-white font-medium rounded-md hover:bg-[var(--color-navbar-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] transition">
        Actualizar correo
      </button>
    </form>

    <form method="POST" class="space-y-4">
      <input type="hidden" name="update_password" value="1">
      <div>
        <label class="block text-sm font-medium mb-1 text-[var(--color-text)]">Contraseña actual</label>
        <input type="password" name="current_password" class="w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1 text-[var(--color-text)]">Nueva contraseña</label>
        <input type="password" name="new_password" class="w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1 text-[var(--color-text)]">Repetir nueva contraseña</label>
        <input type="password" name="confirm_password" class="w-full px-3 py-2 bg-[var(--color-input-bg)] text-[var(--color-input-text)] border border-[var(--color-input-border)] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition">
      </div>
      <button type="submit" class="w-full py-2 px-4 bg-[var(--color-primary)] text-white font-medium rounded-md hover:bg-[var(--color-navbar-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] transition">
        Actualizar contraseña
      </button>
    </form>
  </div>

  <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-5 border border-[var(--color-border)]">
    <h3 class="text-lg font-semibold mb-4 text-[var(--color-text)]">Actividad reciente</h3>

    <div class="max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-[var(--color-scrollbar)] scrollbar-track-[var(--color-bg)]">
      <table class="w-full text-sm text-left border-t border-[var(--color-border)]">
        <thead>
          <tr class="border-b border-[var(--color-border)] text-[var(--color-text-muted)]">
            <th class="py-2 px-2">Fecha y hora</th>
            <th class="py-2 px-2">IP</th>
            <th class="py-2 px-2">Ubicación</th>
            <th class="py-2 px-2">Dispositivo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
              <tr class="border-b border-[var(--color-border)]">
                <td class="py-2 px-2 text-[var(--color-text)]"><?= date('Y-m-d H:i:s', strtotime($log['time'])) ?></td>
                <td class="py-2 px-2 text-[var(--color-text)]"><?= htmlspecialchars($log['ip']) ?></td>
                <td class="py-2 px-2 text-[var(--color-text)]"><a href="https://www.ip-tracker.org/lookup.php?ip=<?= $log['ip'] ?>" target="_blank">Ver ubicación</a></td>
                <td class="py-2 px-2 text-[var(--color-text)]"><?= htmlspecialchars($log['agent']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="py-3 text-center text-[var(--color-text-muted)]">Sin registros recientes.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
</div>