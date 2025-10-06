<?php
$RoleManager = new RoleManager();
$msg = null;

try {
    // Crear Permiso
    if (isset($_POST['create_permission'])) {
        $module = trim($_POST['module'] ?? '');
        $action = trim($_POST['action'] ?? '');
        if (!$module || !$action) throw new Exception("Debe completar todos los campos.");
        if ($RoleManager->createPermission($module, $action)) {
            $msg = '<div style="color:green;">✅ Permiso creado correctamente.</div>';
        }
    }

    // Actualizar Permiso
    if (isset($_POST['update_permission'])) {
        $id = $_POST['id'];
        $module = trim($_POST['module']);
        $action = trim($_POST['action']);
        $RoleManager->db->query("UPDATE webengine_permissions SET module = ?, action = ? WHERE id = ?", [$module, $action, $id]);
        $msg = '<div style="color:green;">✅ Permiso actualizado correctamente.</div>';
    }

    // Eliminar Permiso
    if (isset($_POST['delete_permission'])) {
        $id = $_POST['id'];
        $RoleManager->db->query("DELETE FROM webengine_permissions WHERE id = ?", [$id]);
        $msg = '<div style="color:green;">✅ Permiso eliminado correctamente.</div>';
    }

    // Asignar/Eliminar permisos a roles
    if (isset($_POST['assign_permission_to_role'])) {
        $RoleManager->assignPermissionToRole($_POST['role_id'], $_POST['permission_id']);
        $msg = '<div style="color:green;">✅ Permiso asignado al rol.</div>';
    }
    if (isset($_POST['remove_permission_from_role'])) {
        $RoleManager->removePermissionFromRole($_POST['role_id'], $_POST['permission_id']);
        $msg = '<div style="color:green;">✅ Permiso eliminado del rol.</div>';
    }

    // Asignar/Eliminar permisos a usuarios
    if (isset($_POST['assign_permission_to_user'])) {
        $RoleManager->assignPermissionToUser($_POST['user_id'], $_POST['permission_id']);
        $msg = '<div style="color:green;">✅ Permiso asignado al usuario.</div>';
    }
    if (isset($_POST['remove_permission_from_user'])) {
        $RoleManager->removePermissionFromUser($_POST['user_id'], $_POST['permission_id']);
        $msg = '<div style="color:green;">✅ Permiso eliminado del usuario.</div>';
    }

    // Asignar/Eliminar rol a usuario
    if (isset($_POST['assign_role_to_user'])) {
        $RoleManager->assignRole($_POST['user_id'], $_POST['role_id']);
        $msg = '<div style="color:green;">✅ Rol asignado al usuario.</div>';
    }
    if (isset($_POST['remove_role_from_user'])) {
        $RoleManager->removeRole($_POST['user_id'], $_POST['role_id']);
        $msg = '<div style="color:green;">✅ Rol eliminado del usuario.</div>';
    }

    $permissions = $RoleManager->getAllPermissions();
    $roles = $RoleManager->getAllRoles();
    $users = $RoleManager->getAllUsers();

    if (!is_array($users)) {
        throw new Exception("Error al obtener la lista de usuarios.");
    }

} catch (Exception $e) {
    $msg = '<div style="color:red;">❌ ' . $e->getMessage() . '</div>';
}
?>
<div class="container mx-auto px-6 py-8">
  <h2 class="text-2xl font-bold mb-4">🛡️ Gestión de Permisos y Roles</h2>
  <?= $msg ?>

  <!-- Crear Permiso -->
  <form method="post" class="mb-6 bg-white p-6 rounded shadow-md">
    <h3 class="text-lg font-semibold mb-4">➕ Crear Permiso</h3>
    <div class="flex flex-col md:flex-row gap-4">
      <input type="text" name="module" placeholder="Módulo" required
             class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      <input type="text" name="action" placeholder="Acción" required
             class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      <button type="submit" name="create_permission"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Crear</button>
    </div>
  </form>

  <!-- Lista de Permisos -->
  <h3 class="text-lg font-semibold mb-2">📋 Lista de Permisos</h3>
  <div class="overflow-x-auto mb-8">
    <table class="min-w-full bg-white border border-gray-200 rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium">ID</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Módulo</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Acción</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Editar</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Eliminar</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($permissions as $p): ?>
        <tr class="border-t">
          <form method="post">
            <td class="px-4 py-2"><?= $p['id'] ?></td>
            <td class="px-4 py-2">
              <input type="text" name="module" value="<?= htmlspecialchars($p['module']) ?>"
                     class="w-full px-2 py-1 border rounded">
            </td>
            <td class="px-4 py-2">
              <input type="text" name="action" value="<?= htmlspecialchars($p['action']) ?>"
                     class="w-full px-2 py-1 border rounded">
            </td>
            <td class="px-4 py-2">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" name="update_permission"
                      class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Guardar</button>
            </td>
          </form>
          <form method="post">
            <td class="px-4 py-2">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" name="delete_permission" onclick="return confirm('¿Eliminar este permiso?')"
                      class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">❌</button>
            </td>
          </form>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Asignar/Eliminar Permisos a Roles -->
  <h3 class="text-lg font-semibold mb-4">🔗 Permisos ↔ Roles</h3>
  <form method="post" class="flex flex-col md:flex-row items-center gap-4 mb-10">
    <select name="role_id" required
            class="w-full md:w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      <option value="">-- Rol --</option>
      <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="permission_id" required
            class="w-full md:w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      <option value="">-- Permiso --</option>
      <?php foreach ($permissions as $p): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['module'] . ':' . $p['action']) ?></option>
      <?php endforeach; ?>
    </select>
    <div class="flex gap-2">
      <button type="submit" name="assign_permission_to_role"
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Asignar</button>
      <button type="submit" name="remove_permission_from_role"
              class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
    </div>
  </form>

  <!-- Buscar usuario y gestionar permisos/roles -->
  <h3 class="text-lg font-semibold mb-2">🔍 Buscar Usuario y Gestionar Permisos / Roles</h3>
  <input type="text" id="user-search" placeholder="Buscar por nombre, usuario, correo o cédula"
         class="w-full mb-4 px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium">Usuario</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Correo</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Permiso</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Acciones Permiso</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Rol</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Acciones Rol</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr class="border-t">
          <form method="post">
            <td class="px-4 py-2"><?= htmlspecialchars($u['unom']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($u['ueml']) ?></td>
            <td class="px-4 py-2">
              <select name="permission_id"
                      class="w-full px-2 py-1 border rounded">
                <option value="">-- Permiso --</option>
                <?php foreach ($permissions as $p): ?>
                  <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['module'] . ':' . $p['action']) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="px-4 py-2">
              <input type="hidden" name="user_id" value="<?= $u['uid'] ?>">
              <div class="flex gap-2">
                <button type="submit" name="assign_permission_to_user"
                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Asignar</button>
                <button type="submit" name="remove_permission_from_user"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Eliminar</button>
              </div>
            </td>
          </form>
          <form method="post">
            <td class="px-4 py-2">
              <select name="role_id"
                      class="w-full px-2 py-1 border rounded">
                <option value="">-- Rol --</option>
                <?php foreach ($roles as $r): ?>
                  <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="px-4 py-2">
              <input type="hidden" name="user_id" value="<?= $u['uid'] ?>">
              <div class="flex gap-2">
                <button type="submit" name="assign_role_to_user"
                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Asignar</button>
                <button type="submit" name="remove_role_from_user"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Eliminar</button>
              </div>
            </td>
          </form>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
    const input = document.getElementById('user-search');
    const rows = document.querySelectorAll('#user-table tbody tr');

    input.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
</script>