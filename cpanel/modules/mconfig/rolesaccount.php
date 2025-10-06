<?php
$RoleManager = new RoleManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($action === 'update' && $id) {
        $name = $_POST['name'] ?? '';
        $desc = $_POST['description'] ?? '';
        // Usamos el método updateRole
        $RoleManager->updateRole($id, $name, $desc);
        exit;
    }
    if ($action === 'delete' && $id) {
        // Usamos el método deleteRole
        $RoleManager->deleteRole($id);
        exit;
    }
    if ($action === 'create') {
        $name = $_POST['name'] ?? '';
        $desc = $_POST['description'] ?? '';
        // Usamos el método createRole
        $RoleManager->createRole($name, $desc);
        exit;
    }
}

$roles = $RoleManager->getAllRolesWithPermissionsAndUsers();
?>

<div class="container mx-auto px-4 py-6">
  <h2 class="text-2xl font-bold mb-6">Listado de Roles</h2>

  <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
    <input type="text" id="newRoleName" placeholder="Nombre del rol"
           class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    <input type="text" id="newRoleDesc" placeholder="Descripción del rol"
           class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
    <button onclick="createRole()"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Crear Rol</button>
  </div>

  <div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID Rol</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Descripción</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Permisos</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Usuarios Asociados</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100" id="rolesTable">
        <?php foreach ($roles as $rol): ?>
          <tr class="hover:bg-gray-50" data-role-id="<?= $rol['id'] ?>">
            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($rol['id']) ?></td>
            <td class="px-4 py-2 text-sm font-medium text-gray-900 role-name"><?= htmlspecialchars($rol['name']) ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 role-desc"><?= htmlspecialchars($rol['description']) ?></td>
            <td class="px-4 py-2 text-sm">
              <?php if (!empty($rol['permissions'])): ?>
                <div class="flex flex-wrap gap-1">
                  <?php foreach ($rol['permissions'] as $perm): ?>
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">
                      <?= htmlspecialchars($perm['module'] . ':' . $perm['action']) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">Sin permisos</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-2 text-sm text-center"><?= count($rol['users']) ?></td>
            <td class="px-4 py-2 flex gap-2">
              <button onclick="enableEdit(this)"
                      class="text-sm px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</button>
              <button onclick="confirmDelete(<?= $rol['id'] ?>)"
                      class="text-sm px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Eliminar</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
function enableEdit(button) {
    const row = button.closest('tr');
    const nameCell = row.querySelector('.role-name');
    const descCell = row.querySelector('.role-desc');
    const name = nameCell.textContent.trim();
    const desc = descCell.textContent.trim();

    nameCell.innerHTML = `<input type="text" value="${name}" class="search-input" style="width:100%;">`;
    descCell.innerHTML = `<input type="text" value="${desc}" class="search-input" style="width:100%;">`;

    button.textContent = 'Guardar';
    button.className = 'save-button';
    button.onclick = () => saveRole(button);
}

function saveRole(button) {
    const row = button.closest('tr');
    const roleId = row.getAttribute('data-role-id');
    const name = row.querySelector('.role-name input').value.trim();
    const desc = row.querySelector('.role-desc input').value.trim();

    if (!name) {
        alert('El nombre del rol no puede estar vacío.');
        return;
    }

    const form = new FormData();
    form.append('action', 'update');
    form.append('id', roleId);
    form.append('name', name);
    form.append('description', desc);

    fetch('', { method: 'POST', body: form })
        .then(() => location.reload());
}

function confirmDelete(roleId) {
    if (!confirm('¿Seguro quieres eliminar este rol?')) return;

    const form = new FormData();
    form.append('action', 'delete');
    form.append('id', roleId);

    fetch('', { method: 'POST', body: form })
        .then(() => location.reload());
}

function createRole() {
    const name = document.getElementById('newRoleName').value.trim();
    const desc = document.getElementById('newRoleDesc').value.trim();

    if (!name) {
        alert('El nombre del rol es obligatorio.');
        return;
    }

    const form = new FormData();
    form.append('action', 'create');
    form.append('name', name);
    form.append('description', desc);

    fetch('', { method: 'POST', body: form })
        .then(() => location.reload());
}
</script>