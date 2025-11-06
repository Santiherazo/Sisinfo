<?php
$permissions = [];
$roles = [];
$users = [];
$uniqueModules = [];
$uniqueActions = [];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_permission':
                if (!empty($_POST['module']) && !empty($_POST['action_name']) && !empty($_POST['description'])) {
                    $module = trim($_POST['module']);
                    $actionName = trim($_POST['action_name']);
                    $description = trim($_POST['description']);
                    
                    $result = $permissionManager->createPermission($module, $actionName, $description);
                    
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Todos los campos son obligatorios"]);
                    exit;
                }
                break;
                
            case 'toggle_permission_status':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                $estado = filter_var($_POST['estado'] ?? 0, FILTER_VALIDATE_INT);
                
                if ($id && $estado !== false) {
                    if ($estado) {
                        $result = $permissionManager->activatePermission($id);
                    } else {
                        $result = $permissionManager->deactivatePermission($id);
                    }
                    
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Datos inválidos para cambiar estado"]);
                    exit;
                }
                break;
                
            case 'delete_permission':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                if ($id) {
                    $result = $permissionManager->deletePermission($id);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "ID de permiso inválido"]);
                    exit;
                }
                break;
                
            case 'assign_permission_to_role':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                $permissionId = intval($_POST['permission_id'] ?? 0);
                if ($id && $permissionId) {
                    $result = $permissionManager->assignPermissionToRole($id, $permissionId);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Datos inválidos para asignar permiso"]);
                    exit;
                }
                break;
                
            case 'remove_permission_from_role':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                $permissionId = intval($_POST['permission_id'] ?? 0);
                if ($id && $permissionId) {
                    $result = $permissionManager->removePermissionFromRole($id, $permissionId);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Datos inválidos para remover permiso"]);
                    exit;
                }
                break;
                
            case 'remove_all_permissions_from_role':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                if ($id) {
                    $result = $permissionManager->removeAllPermissionsFromRole($id);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "ID de rol inválido"]);
                    exit;
                }
                break;
                
            case 'assign_permission_to_user':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                $permissionId = intval($_POST['permission_id'] ?? 0);
                if ($id && $permissionId) {
                    $result = $permissionManager->assignPermissionToUser($id, $permissionId);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Datos inválidos para asignar permiso"]);
                    exit;
                }
                break;
                
            case 'remove_permission_from_user':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                $permissionId = intval($_POST['permission_id'] ?? 0);
                if ($id && $permissionId) {
                    $result = $permissionManager->removePermissionFromUser($id, $permissionId);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "Datos inválidos para remover permiso"]);
                    exit;
                }
                break;
                
            case 'remove_all_permissions_from_user':
                $id = isset($_POST['id']) ? intval($_POST['id']) : null;
                if ($id) {
                    $result = $permissionManager->removeAllPermissionsFromUser($id);
                    if ($result['success']) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $result['message']]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $result['message']]);
                        exit;
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => "ID de usuario inválido"]);
                    exit;
                }
                break;
        }
    }
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
}

$permissions = $permissionManager->getAllPermissions();
$roles = $permissionManager->getAllRolesWithPermissions();
$users = $permissionManager->getAllUsersWithPermissions();

$uniqueModules = [];
$uniqueActions = [];

foreach ($permissions as $permission) {
    $module = $permission['module'] ?? '';
    $action = $permission['action'] ?? '';
    
    if ($module && !in_array($module, $uniqueModules)) {
        $uniqueModules[] = $module;
    }
    
    if ($action && !in_array($action, $uniqueActions)) {
        $uniqueActions[] = $action;
    }
}

$permissions = array_map(function($perm) {
    return [
        'id' => $perm['id'],
        'module' => $perm['module'],
        'action' => $perm['action'],
        'description' => $perm['description'] ?? '',
        'status' => $perm['estado'] ?? $perm['status'] ?? 0
    ];
}, $permissions);

$roles = array_map(function($role) {
    return [
        'id' => $role['role_id'] ?? $role['id'] ?? null,
        'name' => $role['role_name'] ?? $role['name'] ?? '',
        'permissions' => $role['permissions'] ?? []
    ];
}, array_values($roles));

$users = array_map(function($user) {
    return [
        'id' => $user['user_id'] ?? $user['id'] ?? null,
        'full_name' => $user['full_name'] ?? '',
        'status' => $user['user_status'] ?? $user['status'] ?? 1,
        'role_id' => $user['role_id'] ?? null,
        'role_name' => $user['role_name'] ?? '',
        'permissions' => $user['permissions'] ?? []
    ];
}, $users);
?>

<div id="permissions-module" class="bg-white rounded-lg shadow-sm p-6">
  <?php if ($message): ?>
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Gestión de Permisos</h2>
    <p class="text-gray-500">Administra permisos del sistema, asignaciones a roles y usuarios</p>
  </div>

  <div class="mb-6 border-b border-gray-200">
    <ul class="flex flex-wrap -mb-px" id="permissions-tabs">
      <li class="mr-2">
        <button onclick="showTab('permissions-tab')" class="inline-block p-4 border-b-2 border-blue-500 rounded-t-lg text-blue-600">Permisos</button>
      </li>
      <li class="mr-2">
        <button onclick="showTab('roles-tab')" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Roles</button>
      </li>
      <li class="mr-2">
        <button onclick="showTab('users-tab')" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">Usuarios</button>
      </li>
    </ul>
  </div>

  <div id="permissions-tab" class="tab-content">
    <div class="bg-gray-50 rounded-lg p-5 mb-8 border border-gray-100">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Nuevo Permiso</h3>
      <form id="permissionForm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Módulo *</label>
            <input type="text" id="module" name="module" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Acción *</label>
            <input type="text" id="action_name" name="action_name" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Descripción *</label>
            <input type="text" id="description" name="description" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
        </div>
        <div class="flex justify-end mt-4">
          <button type="button" id="createPermission" class="bg-blue-500 hover:bg-blue-600 text-white py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Crear Permiso
          </button>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-100 mb-4">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100" id="permissions-list">
          <?php foreach ($permissions as $permission): ?>
          <tr class="hover:bg-gray-50" data-id="<?= $permission['id'] ?>">
            <td class="px-6 py-4 font-medium text-gray-800">
              <div class="flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-blue-500"></i>
                <span class="permission-module"><?= htmlspecialchars($permission['module']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 font-medium text-gray-800">
              <span class="permission-action"><?= htmlspecialchars($permission['action']) ?></span>
            </td>
            <td class="px-6 py-4 text-gray-600">
              <span class="permission-description"><?= htmlspecialchars($permission['description']) ?></span>
            </td>
            <td class="px-6 py-4">
              <button type="button" onclick="togglePermissionStatus(<?= $permission['id'] ?>, <?= $permission['status'] == 1 ? 0 : 1 ?>)" class="toggle-status-btn relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out <?= $permission['status'] == 1 ? 'bg-green-600' : 'bg-gray-300' ?>">
                <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out <?= $permission['status'] == 1 ? 'translate-x-6' : 'translate-x-1' ?>"></span>
              </button>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end space-x-3">
                <button type="button" onclick="editPermission(<?= $permission['id'] ?>)" class="edit-btn text-gray-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all">
                  <i data-lucide="edit" class="w-5 h-5"></i>
                </button>
                <button type="button" onclick="deletePermission(<?= $permission['id'] ?>)" class="delete-btn text-gray-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
                  <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="roles-tab" class="tab-content hidden">
    <div class="bg-gray-50 rounded-lg p-5 mb-8 border border-gray-100">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Seleccionar Rol</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-600 mb-1">Rol</label>
          <select id="role-selector" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex items-end">
          <button type="button" id="loadRolePermissions" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg w-full flex items-center justify-center gap-2">
            <i data-lucide="key" class="w-4 h-4"></i>
            Cargar Permisos
          </button>
        </div>
      </div>
    </div>

    <div id="permissions-panel" class="hidden">
      <div class="flex items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
        <div>
          <h3 class="text-xl font-bold text-gray-800" id="selected-role-name"></h3>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
            <span id="assigned-count">0</span> permisos asignados
          </span>
          <button type="button" id="removeAllRolePermissions" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium hover:bg-red-200">
            Remover todos
          </button>
        </div>
      </div>

      <div id="permissions-list-container">
        <?php
        $permissionsByModule = [];
        foreach ($permissions as $perm) {
            if (!isset($permissionsByModule[$perm['module']])) {
                $permissionsByModule[$perm['module']] = [];
            }
            $permissionsByModule[$perm['module']][] = $perm;
        }
        ?>
        
        <?php foreach ($permissionsByModule as $module => $modulePermissions): ?>
        <div class="permission-module-group border border-gray-100 rounded-lg overflow-hidden mb-4">
          <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <h4 class="font-semibold text-gray-700 flex items-center gap-2">
              <i data-lucide="shield" class="w-4 h-4 text-blue-500"></i>
              <?= htmlspecialchars($module) ?>
            </h4>
          </div>
          <div class="divide-y divide-gray-100">
            <?php foreach ($modulePermissions as $perm): ?>
            <div class="px-4 py-3 hover:bg-gray-50 flex items-center justify-between" data-permission-id="<?= $perm['id'] ?>">
              <div>
                <p class="font-medium text-gray-800"><?= htmlspecialchars($perm['action']) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($perm['description']) ?></p>
              </div>
              <button type="button" onclick="toggleRolePermission(<?= $perm['id'] ?>)" class="role-permission-btn relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out bg-gray-300" data-permission-id="<?= $perm['id'] ?>">
                <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out translate-x-1"></span>
              </button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div id="users-tab" class="tab-content hidden">
    <div class="overflow-x-auto rounded-lg border border-gray-100 mb-6">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100" id="users-list">
          <?php foreach ($users as $user): ?>
          <tr class="hover:bg-gray-50" data-id="<?= $user['id'] ?>">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white text-xs font-bold">
                  <?= substr($user['full_name'], 0, 2) ?>
                </div>
                <div>
                  <div class="font-medium text-gray-800"><?= htmlspecialchars($user['full_name']) ?></div>
                  <div class="text-sm text-gray-500"><?= $user['status'] == 1 ? 'Activo' : 'Inactivo' ?></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <?= htmlspecialchars($user['role_name']) ?>
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end space-x-3">
                <button type="button" onclick="manageUserPermissions(<?= $user['id'] ?>)" class="user-permissions-btn text-gray-500 hover:text-purple-600 p-1.5 rounded-lg hover:bg-purple-50 transition-all" title="Gestionar permisos">
                  <i data-lucide="key" class="w-5 h-5"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="user-permissions-modal" class="fixed z-50 inset-0 overflow-y-auto hidden">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="user-permissions-modal-title">Gestionar Permisos</h3>
        <div class="mt-4">
          <div class="mb-6">
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
              <div id="user-avatar" class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-bold"></div>
              <div>
                <p class="font-bold text-gray-900" id="user-full-name"></p>
                <p class="text-sm text-gray-600" id="user-role-name"></p>
              </div>
              <button type="button" id="removeAllUserPermissions" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium hover:bg-red-200">
                Remover todos
              </button>
            </div>
          </div>
          
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-2">Permisos Disponibles</h4>
            <div class="overflow-y-auto max-h-64">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permiso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="user-additional-permissions-list"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" onclick="closeModal('user-permissions-modal')" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let allPermissions = <?= json_encode($permissions) ?>;
let allRoles = <?= json_encode($roles) ?>;
let allUsers = <?= json_encode($users) ?>;

let currentRoleId = null;
let currentUserId = null;

// INICIALIZAR EVENTOS CUANDO EL DOCUMENTO ESTÉ LISTO
document.addEventListener('DOMContentLoaded', function() {
    // Evento para crear permiso
    document.getElementById('createPermission').addEventListener('click', createPermission);
    
    // Evento para cargar permisos de rol
    document.getElementById('loadRolePermissions').addEventListener('click', loadRolePermissions);
    
    // Evento para remover todos los permisos de rol
    document.getElementById('removeAllRolePermissions').addEventListener('click', removeAllPermissionsFromRole);
    
    // Evento para remover todos los permisos de usuario
    document.getElementById('removeAllUserPermissions').addEventListener('click', removeAllPermissionsFromUser);
    
    // Inicializar íconos
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// FUNCIÓN PARA CREAR PERMISO
function createPermission() {
    const module = document.getElementById('module').value.trim();
    const actionName = document.getElementById('action_name').value.trim();
    const description = document.getElementById('description').value.trim();
    
    if (!module || !actionName || !description) {
        alert('Todos los campos son obligatorios');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'create_permission');
    formData.append('module', module);
    formData.append('action_name', actionName);
    formData.append('description', description);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Limpiar formulario
            document.getElementById('module').value = '';
            document.getElementById('action_name').value = '';
            document.getElementById('description').value = '';
            // Recargar la página para ver los cambios
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear permiso');
    });
}

// FUNCIÓN PARA EDITAR PERMISO
function editPermission(permissionId) {
    const permission = allPermissions.find(p => p.id == permissionId);
    if (permission) {
        const newModule = prompt('Nuevo módulo:', permission.module);
        const newAction = prompt('Nueva acción:', permission.action);
        const newDescription = prompt('Nueva descripción:', permission.description);
        
        if (newModule && newAction && newDescription) {
            alert('Funcionalidad de edición completa en desarrollo para permiso: ' + permissionId);
            // Aquí iría la llamada a la API para editar el permiso
        }
    }
}

// FUNCIÓN PARA ELIMINAR PERMISO
function deletePermission(permissionId) {
    if (confirm('¿Estás seguro de eliminar este permiso?')) {
        const formData = new FormData();
        formData.append('action', 'delete_permission');
        formData.append('id', permissionId);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar permiso');
        });
    }
}

// FUNCIÓN PARA ACTUALIZAR ESTADO DEL PERMISO
function togglePermissionStatus(permissionId, newStatus) {
    const formData = new FormData();
    formData.append('action', 'toggle_permission_status');
    formData.append('id', permissionId);
    formData.append('estado', newStatus);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar estado');
    });
}

// FUNCIÓN PARA CARGAR PERMISOS DE ROL
function loadRolePermissions() {
    const roleSelector = document.getElementById('role-selector');
    const roleId = roleSelector.value;
    
    if (!roleId) {
        alert('Por favor seleccione un rol');
        return;
    }
    
    currentRoleId = roleId;
    const role = allRoles.find(r => r.id == roleId);
    
    if (role) {
        document.getElementById('selected-role-name').textContent = role.name;
        updateRolePermissionsUI(role);
        
        const permissionsPanel = document.getElementById('permissions-panel');
        if (permissionsPanel) {
            permissionsPanel.classList.remove('hidden');
        }
    }
}

// FUNCIÓN PARA ASIGNAR/REMOVER PERMISO DE ROL
function toggleRolePermission(permissionId) {
    if (!currentRoleId) {
        alert('Primero seleccione un rol');
        return;
    }
    
    const button = document.querySelector(`.role-permission-btn[data-permission-id="${permissionId}"]`);
    const isAssigned = button.classList.contains('bg-green-600');
    const action = isAssigned ? 'remove_permission_from_role' : 'assign_permission_to_role';
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', currentRoleId);
    formData.append('permission_id', permissionId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isAssigned) {
                button.classList.remove('bg-green-600');
                button.classList.add('bg-gray-300');
                button.querySelector('span').classList.remove('translate-x-6');
                button.querySelector('span').classList.add('translate-x-1');
            } else {
                button.classList.remove('bg-gray-300');
                button.classList.add('bg-green-600');
                button.querySelector('span').classList.remove('translate-x-1');
                button.querySelector('span').classList.add('translate-x-6');
            }
            updateAssignedCount();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al modificar permiso del rol');
    });
}

// FUNCIÓN PARA REMOVER TODOS LOS PERMISOS DEL ROL
function removeAllPermissionsFromRole() {
    if (!currentRoleId) {
        alert('Primero seleccione un rol');
        return;
    }
    
    if (confirm('¿Estás seguro de remover todos los permisos de este rol?')) {
        const formData = new FormData();
        formData.append('action', 'remove_all_permissions_from_role');
        formData.append('id', currentRoleId);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al remover permisos');
        });
    }
}

// FUNCIÓN PARA REMOVER TODOS LOS PERMISOS DEL USUARIO
function removeAllPermissionsFromUser() {
    if (!currentUserId) {
        alert('No hay usuario seleccionado');
        return;
    }
    
    if (confirm('¿Estás seguro de remover todos los permisos de este usuario?')) {
        const formData = new FormData();
        formData.append('action', 'remove_all_permissions_from_user');
        formData.append('id', currentUserId);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al remover permisos');
        });
    }
}

// FUNCIÓN PARA ASIGNAR/REMOVER PERMISO DE USUARIO
function toggleUserPermission(permissionId, isAssigned) {
    if (!currentUserId) return;
    
    const action = isAssigned ? 'remove_permission_from_user' : 'assign_permission_to_user';
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', currentUserId);
    formData.append('permission_id', permissionId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al modificar permiso del usuario');
    });
}

// FUNCIONES AUXILIARES
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    document.querySelectorAll('#permissions-tabs button').forEach(btn => {
        if (btn.getAttribute('onclick').includes(tabId)) {
            btn.classList.add('border-blue-500', 'text-blue-600');
            btn.classList.remove('border-transparent', 'text-gray-500');
        } else {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        }
    });
}

function updateRolePermissionsUI(role) {
    document.querySelectorAll('.role-permission-btn').forEach(button => {
        const permissionId = button.getAttribute('data-permission-id');
        const isAssigned = role.permissions.some(p => p.id == permissionId);
        
        if (isAssigned) {
            button.classList.remove('bg-gray-300');
            button.classList.add('bg-green-600');
            button.querySelector('span').classList.remove('translate-x-1');
            button.querySelector('span').classList.add('translate-x-6');
        } else {
            button.classList.remove('bg-green-600');
            button.classList.add('bg-gray-300');
            button.querySelector('span').classList.remove('translate-x-6');
            button.querySelector('span').classList.add('translate-x-1');
        }
    });
    
    updateAssignedCount();
}

function updateAssignedCount() {
    const assignedButtons = document.querySelectorAll('.role-permission-btn.bg-green-600');
    document.getElementById('assigned-count').textContent = assignedButtons.length;
}

function manageUserPermissions(userId) {
    currentUserId = userId;
    const user = allUsers.find(u => u.id == userId);
    
    if (user) {
        document.getElementById('user-permissions-modal-title').textContent = `Permisos para ${user.full_name}`;
        document.getElementById('user-full-name').textContent = user.full_name;
        document.getElementById('user-role-name').textContent = user.role_name || 'Sin rol';
        document.getElementById('user-avatar').textContent = user.full_name.substring(0, 2).toUpperCase();
        
        renderUserPermissions(user);
        document.getElementById('user-permissions-modal').classList.remove('hidden');
    }
}

function renderUserPermissions(user) {
    const container = document.getElementById('user-additional-permissions-list');
    if (!container) return;
    
    container.innerHTML = '';
    
    const role = allRoles.find(r => r.id == user.role_id);
    const rolePermissionIds = role ? role.permissions.map(p => p.id) : [];
    const userPermissionIds = user.permissions.map(p => p.id);
    
    const additionalPermissions = allPermissions.filter(p => !rolePermissionIds.includes(p.id));
    
    additionalPermissions.forEach(permission => {
        const isAssigned = userPermissionIds.includes(permission.id);
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${permission.module}.${permission.action}</div>
                <div class="text-sm text-gray-500">${permission.description}</div>
            </td>
            <td class="px-6 py-4">
                <button type="button" onclick="toggleUserPermission(${permission.id}, ${isAssigned})" class="p-2 rounded-lg ${isAssigned ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200'}">
                    <i data-lucide="${isAssigned ? 'x' : 'check'}" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        container.appendChild(row);
    });
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>