<?php
$permissions = [];
$roles = [];
$users = [];
$uniqueModules = [];
$uniqueActions = [];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

if ($formAction === 'create_permission') {
    $module = trim($_POST['module'] ?? '');
    $actionName = trim($_POST['action_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($module) || empty($actionName)) {
        http_response_code(400);
        exit;
    }
    
    if ($permissionManager->permissionExists($module, $actionName)) {
        http_response_code(409);
        exit;
    }

    $result = $permissionManager->createPermission($module, $actionName, $description);
    
    if ($result) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }
    exit;
}
    
    elseif ($formAction === 'update_permission' && $id) {
        $module = trim($_POST['module'] ?? '');
        $actionName = trim($_POST['action_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($module) || empty($actionName)) {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Faltan campos obligatorios'];
        } elseif ($permissionManager->permissionExists($module, $actionName, $id)) {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Este permiso ya existe'];
        } else {
            $result = $permissionManager->updatePermission($id, $module, $actionName, $description);
            if ($result) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso actualizado correctamente'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al actualizar el permiso'];
            }
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'delete_permission' && $id) {
        $result = $permissionManager->deletePermission($id);
        if ($result) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso eliminado correctamente'];
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al eliminar el permiso'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'toggle_permission_status' && $id) {
        $estado = intval($_POST['estado'] ?? 0);
        
        if ($estado) {
            $result = $permissionManager->activatePermission($id);
        } else {
            $result = $permissionManager->deactivatePermission($id);
        }
        
        if ($result) {
            $statusText = $estado ? 'activado' : 'desactivado';
            $_SESSION['notification'] = ['type' => 'success', 'message' => "Permiso $statusText correctamente"];
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al cambiar el estado del permiso'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'assign_permission_to_role' && $id) {
        $permissionId = intval($_POST['permission_id'] ?? 0);
        if ($permissionId) {
            $result = $permissionManager->assignPermissionToRole($id, $permissionId);
            if ($result) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso asignado al rol correctamente'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al asignar el permiso'];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'ID de permiso no válido'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'remove_permission_from_role' && $id) {
        $permissionId = intval($_POST['permission_id'] ?? 0);
        if ($permissionId) {
            $result = $permissionManager->removePermissionFromRole($id, $permissionId);
            if ($result) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso removido del rol correctamente'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al remover el permiso'];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'ID de permiso no válido'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'remove_all_permissions_from_role' && $id) {
        $result = $permissionManager->removeAllPermissionsFromRole($id);
        if ($result) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'Todos los permisos han sido removidos del rol'];
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al remover los permisos'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'assign_permission_to_user' && $id) {
        $permissionId = intval($_POST['permission_id'] ?? 0);
        if ($permissionId) {
            $result = $permissionManager->assignPermissionToUser($id, $permissionId);
            if ($result) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso asignado al usuario correctamente'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al asignar el permiso'];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'ID de permiso no válido'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    elseif ($formAction === 'remove_permission_from_user' && $id) {
        $permissionId = intval($_POST['permission_id'] ?? 0);
        if ($permissionId) {
            $result = $permissionManager->removePermissionFromUser($id, $permissionId);
            if ($result) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Permiso removido del usuario correctamente'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error al remover el permiso'];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'ID de permiso no válido'];
        }
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } 
    
    else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Acción no válida'];
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$resultadoFinal = [
    'permissions' => $permissionManager->getAllPermissions(),
    'roles' => $permissionManager->getAllRolesWithPermissions(),
    'users' => $permissionManager->getAllUsersWithPermissions()
];

?>

<div id="permissions-module" class="bg-white rounded-lg shadow-sm p-6">
  <?php if (isset($_SESSION['notification'])): ?>
    <div class="mb-4 px-4 py-3 rounded-lg <?= $_SESSION['notification']['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
      <?= $_SESSION['notification']['message'] ?>
    </div>
    <?php unset($_SESSION['notification']); ?>
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
      <form method="POST" id="permissionForm" onsubmit="return validatePermissionForm()">
        <input type="hidden" name="action" value="create_permission">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Módulo <span class="text-red-500">*</span></label>
            <input type="text" name="module" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Acción <span class="text-red-500">*</span></label>
            <input type="text" name="action_name" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Descripción</label>
            <input type="text" name="description" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
        </div>
        <div class="flex justify-end mt-4">
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Crear Permiso
          </button>
        </div>
      </form>
    </div>

    <div class="mb-4 flex gap-4">
      <div class="relative flex-1">
        <select id="module-filter" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          <option value="">Todos los módulos</option>
          <?php foreach ($uniqueModules as $module): ?>
            <option value="<?= htmlspecialchars($module) ?>"><?= htmlspecialchars($module) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="relative flex-1">
        <select id="action-filter" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          <option value="">Todas las acciones</option>
          <?php foreach ($uniqueActions as $action): ?>
            <option value="<?= htmlspecialchars($action) ?>"><?= htmlspecialchars($action) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
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
          <?php foreach ($permissions as $permission): 
            $status = $permission['estado'] ?? $permission['status'] ?? 0;
          ?>
          <tr class="hover:bg-gray-50" data-id="<?= $permission['id'] ?>" data-module="<?= htmlspecialchars($permission['module']) ?>" data-action="<?= htmlspecialchars($permission['action']) ?>">
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
              <span class="permission-description"><?= htmlspecialchars($permission['description'] ?? '') ?></span>
            </td>
            <td class="px-6 py-4">
              <form method="POST" class="toggle-permission-form">
                <input type="hidden" name="action" value="toggle_permission_status">
                <input type="hidden" name="id" value="<?= $permission['id'] ?>">
                <input type="hidden" name="estado" value="<?= $status == 1 ? 0 : 1 ?>">
                <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out <?= $status == 1 ? 'bg-green-600' : 'bg-gray-300' ?>">
                  <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out <?= $status == 1 ? 'translate-x-6' : 'translate-x-1' ?>"></span>
                </button>
              </form>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end space-x-3">
                <button onclick="editPermission(<?= $permission['id'] ?>)" class="text-gray-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all">
                  <i data-lucide="edit" class="w-5 h-5"></i>
                </button>
                <form method="POST" class="inline-block">
                  <input type="hidden" name="action" value="delete_permission">
                  <input type="hidden" name="id" value="<?= $permission['id'] ?>">
                  <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este permiso?')" class="text-gray-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div id="pagination-controls" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
      <div class="flex flex-1 justify-between sm:hidden">
        <button id="prev-page-mobile" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</button>
        <button id="next-page-mobile" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</button>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Mostrando <span class="font-medium" id="current-range">1-<?= count($permissions) > 15 ? 15 : count($permissions) ?></span> de <span class="font-medium" id="total-permissions"><?= count($permissions) ?></span> resultados
          </p>
        </div>
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <button id="prev-page" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
              <span class="sr-only">Anterior</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
              </svg>
            </button>
            <div id="page-numbers"></div>
            <button id="next-page" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
              <span class="sr-only">Siguiente</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
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
          <button onclick="loadRolePermissions()" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg w-full flex items-center justify-center gap-2">
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
          <p class="text-gray-600" id="selected-role-desc"></p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
            <span id="assigned-count">0</span> permisos asignados
          </span>
          <form method="POST" id="remove-all-form" class="inline-block">
            <input type="hidden" name="action" value="remove_all_permissions_from_role">
            <input type="hidden" name="id" id="remove-all-role-id">
            <button type="submit" onclick="return confirm('¿Estás seguro de que deseas remover todos los permisos de este rol?')" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium hover:bg-red-200">
              Remover todos
            </button>
          </form>
        </div>
      </div>

      <div class="mb-6">
        <div class="relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
          <input type="text" id="permission-search" placeholder="Buscar permisos..." 
                class="w-full pl-10 pr-4 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
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
                <p class="text-sm text-gray-500"><?= htmlspecialchars($perm['description'] ?? '') ?></p>
              </div>
              <form method="POST" class="toggle-role-permission-form">
                <input type="hidden" name="action" value="assign_permission_to_role">
                <input type="hidden" name="id" id="role-id-input-<?= $perm['id'] ?>">
                <input type="hidden" name="permission_id" value="<?= $perm['id'] ?>">
                <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out bg-gray-300">
                  <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out translate-x-1"></span>
                </button>
              </form>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div id="users-tab" class="tab-content hidden">
    <div class="bg-gray-50 rounded-lg p-5 mb-8 border border-gray-100">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Filtrar Usuarios</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Buscar</label>
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" id="user-search" placeholder="Nombre o email" class="w-full pl-10 pr-4 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Rol</label>
          <select id="role-filter" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            <option value="">Todos</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-100 mb-6">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100" id="users-list">
          <?php foreach ($users as $user): 
            $userId = $user['user_id'] ?? $user['id'] ?? '';
            $userName = $user['name'] ?? $user['full_name'] ?? '';
            $userEmail = $user['email'] ?? '';
            $userRoleId = $user['role_id'] ?? '';
            $userRoleName = $user['role_name'] ?? '';
          ?>
          <tr class="hover:bg-gray-50" data-id="<?= $userId ?>" data-role-id="<?= $userRoleId ?>">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white text-xs font-bold">
                  <?= substr($userName, 0, 2) ?>
                </div>
                <div>
                  <div class="font-medium text-gray-800"><?= htmlspecialchars($userName) ?></div>
                  <div class="text-sm text-gray-500"><?= htmlspecialchars($userEmail) ?></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($userEmail) ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <?= htmlspecialchars($userRoleName) ?>
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end space-x-3">
                <button onclick="manageUserPermissions(<?= $userId ?>)" class="text-gray-500 hover:text-purple-600 p-1.5 rounded-lg hover:bg-purple-50 transition-all" title="Gestionar permisos">
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

<div id="edit-permission-modal" class="fixed z-50 inset-0 overflow-y-auto hidden">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Editar Permiso</h3>
        <form id="edit-permission-form" method="POST" onsubmit="return validateEditPermissionForm()">
          <input type="hidden" name="action" value="update_permission">
          <input type="hidden" name="id" id="edit-permission-id">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Módulo <span class="text-red-500">*</span></label>
              <input type="text" name="module" id="edit-permission-module" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Acción <span class="text-red-500">*</span></label>
              <input type="text" name="action_name" id="edit-permission-action" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Descripción</label>
              <input type="text" name="description" id="edit-permission-description" class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            </div>
          </div>
        </form>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" onclick="document.getElementById('edit-permission-form').submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
          Guardar
        </button>
        <button type="button" onclick="closeModal('edit-permission-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
          Cancelar
        </button>
      </div>
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
              <div id="user-avatar" class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-bold">
              </div>
              <div>
                <p class="font-bold text-gray-900" id="user-full-name"></p>
                <p class="text-sm text-gray-600" id="user-role-name"></p>
              </div>
            </div>
          </div>
          
          <div class="mb-6">
            <h4 class="text-md font-medium text-gray-899 mb-2">Permisos del Rol</h4>
            <div class="flex flex-wrap gap-2" id="user-role-permissions-list">
            </div>
          </div>
          
          <div>
            <h4 class="text-md font-medium text-gray-899 mb-2">Permisos Adicionales</h4>
            <div class="relative mb-4">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
              <input type="text" id="search-user-permissions" placeholder="Buscar permisos..." class="w-full pl-10 pr-4 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            </div>
            <div class="overflow-y-auto max-h-64">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permiso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="user-additional-permissions-list">
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" onclick="closeModal('user-permissions-modal')" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let currentRoleId = null;
let currentUserId = null;
let allPermissions = <?= json_encode($permissions ?? []) ?>;
let allRoles = <?= json_encode($roles ?? []) ?>;
let allUsers = <?= json_encode($users ?? []) ?>;
let filteredPermissions = [...allPermissions];
let filteredUsers = [...allUsers];

let currentPage = 1;
const perPage = 15;
let totalPermissions = allPermissions.length;
let totalPages = Math.ceil(totalPermissions / perPage);

document.addEventListener('DOMContentLoaded', function() {
  initializePermissionsModule();
  setupEventListeners();
  setupPagination();
  
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
});

function initializePermissionsModule() {
  showTab('permissions-tab');
}

function setupEventListeners() {
  document.getElementById('module-filter')?.addEventListener('change', filterPermissions);
  document.getElementById('action-filter')?.addEventListener('change', filterPermissions);
  
  document.getElementById('permission-search')?.addEventListener('input', debounce(searchPermissions, 300));
  
  document.getElementById('search-user-permissions')?.addEventListener('input', debounce(searchUserPermissions, 300));
  
  document.getElementById('user-search')?.addEventListener('input', debounce(filterUsers, 300));
  document.getElementById('role-filter')?.addEventListener('change', filterUsers);
  
  document.getElementById('prev-page')?.addEventListener('click', () => changePage(currentPage - 1));
  document.getElementById('next-page')?.addEventListener('click', () => changePage(currentPage + 1));
  document.getElementById('prev-page-mobile')?.addEventListener('click', () => changePage(currentPage - 1));
  document.getElementById('next-page-mobile')?.addEventListener('click', () => changePage(currentPage + 1));
}

function setupPagination() {
  renderPagination();
  renderPermissionsPage();
}

function renderPagination() {
  const pageNumbersContainer = document.getElementById('page-numbers');
  if (!pageNumbersContainer) return;
  
  pageNumbersContainer.innerHTML = '';
  
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);
  
  if (endPage - startPage < 4) {
    startPage = Math.max(1, endPage - 4);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const pageButton = document.createElement('button');
    pageButton.className = `relative inline-flex items-center px-4 py-2 text-sm font-semibold ${i === currentPage ? 'bg-blue-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'}`;
    pageButton.textContent = i;
    pageButton.addEventListener('click', () => changePage(i));
    pageNumbersContainer.appendChild(pageButton);
  }
  
  document.getElementById('prev-page').disabled = currentPage === 1;
  document.getElementById('prev-page-mobile').disabled = currentPage === 1;
  document.getElementById('next-page').disabled = currentPage === totalPages;
  document.getElementById('next-page-mobile').disabled = currentPage === totalPages;
  
  const startIndex = (currentPage - 1) * perPage + 1;
  const endIndex = Math.min(currentPage * perPage, totalPermissions);
  document.getElementById('current-range').textContent = `${startIndex}-${endIndex}`;
  document.getElementById('total-permissions').textContent = totalPermissions;
}

function changePage(page) {
  if (page < 1 || page > totalPages) return;
  
  currentPage = page;
  renderPagination();
  renderPermissionsPage();
}

function renderPermissionsPage() {
  const permissionsList = document.getElementById('permissions-list');
  if (!permissionsList) return;
  
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = Math.min(startIndex + perPage, totalPermissions);
  const currentPermissions = allPermissions.slice(startIndex, endIndex);
  
  permissionsList.innerHTML = '';
  
  currentPermissions.forEach(permission => {
    const status = permission.estado ?? permission.status ?? 0;
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.dataset.id = permission.id;
    row.dataset.module = permission.module;
    row.dataset.action = permission.action;
    
    row.innerHTML = `
      <td class="px-6 py-4 font-medium text-gray-800">
        <div class="flex items-center gap-2">
          <i data-lucide="shield" class="w-4 h-4 text-blue-500"></i>
          <span class="permission-module">${escapeHtml(permission.module)}</span>
        </div>
      </td>
      <td class="px-6 py-4 font-medium text-gray-800">
        <span class="permission-action">${escapeHtml(permission.action)}</span>
      </td>
      <td class="px-6 py-4 text-gray-600">
        <span class="permission-description">${escapeHtml(permission.description ?? '')}</span>
      </td>
      <td class="px-6 py-4">
        <form method="POST" class="toggle-permission-form">
          <input type="hidden" name="action" value="toggle_permission_status">
          <input type="hidden" name="id" value="${permission.id}">
          <input type="hidden" name="estado" value="${status == 1 ? 0 : 1}">
          <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out ${status == 1 ? 'bg-green-600' : 'bg-gray-300'}">
            <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out ${status == 1 ? 'translate-x-6' : 'translate-x-1'}"></span>
          </button>
        </form>
      </td>
      <td class="px-6 py-4 text-right">
        <div class="flex justify-end space-x-3">
          <button onclick="editPermission(${permission.id})" class="text-gray-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all">
            <i data-lucide="edit" class="w-5 h-5"></i>
          </button>
          <form method="POST" class="inline-block">
            <input type="hidden" name="action" value="delete_permission">
            <input type="hidden" name="id" value="${permission.id}">
            <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este permiso?')" class="text-gray-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
              <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
          </form>
        </div>
      </td>
    `;
    
    permissionsList.appendChild(row);
  });
  
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
}

function validatePermissionForm() {
  const module = document.querySelector('input[name="module"]').value.trim();
  const action = document.querySelector('input[name="action_name"]').value.trim();
  
  if (!module) {
    alert('El campo Módulo es obligatorio');
    return false;
  }
  
  if (!action) {
    alert('El campo Acción es obligatorio');
    return false;
  }
  
  // Realiza la petición AJAX para crear el permiso
  const formData = new FormData(document.getElementById('permissionForm'));
  
  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // Si la respuesta es 200 (éxito)
    if (response.ok) {
      alert('Permiso creado exitosamente');
      window.location.reload(); 
    } else {
      // Manejar errores basados en el código de respuesta HTTP
      response.text().then(text => {
        let errorMessage = 'Error al crear el permiso';
        if (response.status === 400) {
          errorMessage = 'Error: Faltan campos obligatorios.';
        } else if (response.status === 409) {
          errorMessage = 'Error: Este permiso ya existe.';
        } else if (response.status === 500) {
          errorMessage = 'Error interno del servidor al intentar guardar el permiso.';
        }
        alert(errorMessage);
      }).catch(() => {
        alert('Error desconocido al procesar la respuesta del servidor.');
      });
    }
  })
  .catch(error => {
    alert('Error de conexión con el servidor: ' + error.message);
  });
  
  // Detener el envío tradicional del formulario
  return false;
}

function validateEditPermissionForm() {
  const module = document.getElementById('edit-permission-module').value.trim();
  const action = document.getElementById('edit-permission-action').value.trim();
  
  if (!module) {
    alert('El campo Módulo es obligatorio');
    return false;
  }
  
  if (!action) {
    alert('El campo Acción es obligatorio');
    return false;
  }
  
  return true;
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function editPermission(id) {
  const permission = allPermissions.find(p => p.id == id);
  if (!permission) return;
  
  document.getElementById('edit-permission-id').value = id;
  document.getElementById('edit-permission-module').value = permission.module;
  document.getElementById('edit-permission-action').value = permission.action;
  document.getElementById('edit-permission-description').value = permission.description || '';
  
  document.getElementById('edit-permission-modal').classList.remove('hidden');
}

function filterPermissions() {
  const moduleFilter = document.getElementById('module-filter').value.toLowerCase();
  const actionFilter = document.getElementById('action-filter').value.toLowerCase();
  
  filteredPermissions = allPermissions.filter(permission => {
    const module = permission.module.toLowerCase();
    const action = permission.action.toLowerCase();
    
    const matchesModule = !moduleFilter || module.includes(moduleFilter);
    const matchesAction = !actionFilter || action.includes(actionFilter);
    
    return matchesModule && matchesAction;
  });
  
  allPermissions = filteredPermissions;
  totalPermissions = allPermissions.length;
  totalPages = Math.ceil(totalPermissions / perPage);
  currentPage = 1;
  
  renderPagination();
  renderPermissionsPage();
}

function loadRolePermissions() {
  const roleId = document.getElementById('role-selector').value;
  if (!roleId) {
    alert('Por favor seleccione un rol');
    return;
  }
  
  currentRoleId = roleId;
  document.getElementById('remove-all-role-id').value = roleId;
  
  const role = allRoles.find(r => r.id == roleId);
  if (role) {
    document.getElementById('selected-role-name').textContent = role.name;
    document.getElementById('selected-role-desc').textContent = role.description || 'Sin descripción';
  }
  
  document.querySelectorAll('.toggle-role-permission-form').forEach(form => {
    const permissionId = form.querySelector('input[name="permission_id"]').value;
    form.querySelector('input[name="id"]').value = roleId;
    
    const isAssigned = role.permissions && role.permissions.some(p => p.id == permissionId);
    const button = form.querySelector('button');
    
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
  
  document.getElementById('assigned-count').textContent = role.permissions ? role.permissions.length : 0;
  document.getElementById('permissions-panel').classList.remove('hidden');
}

function searchPermissions() {
  const searchTerm = document.getElementById('permission-search').value.toLowerCase();
  
  document.querySelectorAll('.permission-module-group').forEach(group => {
    const moduleName = group.querySelector('h4').textContent.toLowerCase();
    let hasMatch = moduleName.includes(searchTerm);
    
    if (!hasMatch) {
      group.querySelectorAll('.font-medium').forEach(el => {
        if (el.textContent.toLowerCase().includes(searchTerm)) {
          hasMatch = true;
        }
      });
    }
    
    group.style.display = hasMatch ? '' : 'none';
  });
}

function filterUsers() {
  const searchTerm = document.getElementById('user-search').value.toLowerCase();
  const roleFilter = document.getElementById('role-filter').value;
  
  document.querySelectorAll('#users-list tr').forEach(row => {
    const name = row.querySelector('.font-medium').textContent.toLowerCase();
    const email = row.querySelector('.text-sm').textContent.toLowerCase();
    const roleId = row.dataset.roleId;
    
    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
    const matchesRole = !roleFilter || roleId == roleFilter;
    
    row.style.display = matchesSearch && matchesRole ? '' : 'none';
  });
}

function manageUserPermissions(userId) {
  currentUserId = userId;
  
  const user = allUsers.find(u => (u.user_id ?? u.id) == userId);
  if (!user) return;
  
  const userName = user.name ?? user.full_name;
  
  document.getElementById('user-permissions-modal-title').textContent = `Permisos para ${userName}`;
  document.getElementById('user-full-name').textContent = userName;
  document.getElementById('user-role-name').textContent = user.role_name;
  document.getElementById('user-avatar').textContent = userName.substring(0, 2);
  
  renderUserPermissions(user.permissions, user.role_id);
  document.getElementById('user-permissions-modal').classList.remove('hidden');
}

function renderUserPermissions(userPermissions, roleId) {
  const role = allRoles.find(r => r.id == roleId);
  const rolePermissions = role && role.permissions ? role.permissions : [];
  
  renderUserRolePermissions(rolePermissions);
  renderUserAdditionalPermissions(userPermissions, rolePermissions);
}

function renderUserRolePermissions(permissions) {
  const container = document.getElementById('user-role-permissions-list');
  container.innerHTML = '';
  
  if (permissions.length === 0) {
    container.innerHTML = '<span class="text-sm text-gray-500">El rol no tiene permisos asignados</span>';
    return;
  }
  
  permissions.forEach(permission => {
    const chip = document.createElement('span');
    chip.className = 'px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium';
    chip.textContent = `${permission.module}.${permission.action}`;
    container.appendChild(chip);
  });
}

function renderUserAdditionalPermissions(userPermissions, rolePermissions) {
  const container = document.getElementById('user-additional-permissions-list');
  container.innerHTML = '';
  
  if (allPermissions.length === 0) {
    container.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No hay permisos disponibles</td></tr>';
    return;
  }
  
  const rolePermissionIds = rolePermissions.map(rp => rp.id);
  const additionalPermissions = allPermissions.filter(p => !rolePermissionIds.includes(p.id));
  
  if (additionalPermissions.length === 0) {
    container.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No hay permisos adicionales disponibles</td></tr>';
    return;
  }
  
  additionalPermissions.forEach(permission => {
    const isAssigned = userPermissions && userPermissions.some(up => up.id == permission.id);
    
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.innerHTML = `
      <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900">${permission.module}.${permission.action}</div>
        <div class="text-sm text-gray-500">${permission.description || ''}</div>
      </td>
      <td class="px-6 py-4">
        <form method="POST" class="toggle-user-permission-form">
          <input type="hidden" name="action" value="${isAssigned ? 'remove_permission_from_user' : 'assign_permission_to_user'}">
          <input type="hidden" name="id" value="${currentUserId}">
          <input type="hidden" name="permission_id" value="${permission.id}">
          <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out ${isAssigned ? 'bg-purple-600' : 'bg-gray-300'}">
            <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out ${isAssigned ? 'translate-x-6' : 'translate-x-1'}"></span>
          </button>
        </form>
      </td>
    `;
    container.appendChild(row);
  });
}

function searchUserPermissions() {
  const searchTerm = document.getElementById('search-user-permissions').value.toLowerCase();
  document.querySelectorAll('#user-additional-permissions-list tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
}

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

function closeModal(modalId) {
  document.getElementById(modalId).classList.add('hidden');
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

if (window.lucide) {
  lucide.createIcons();
}

window.addEventListener('click', function(event) {
  if (event.target.id === 'user-permissions-modal' || event.target.id === 'edit-permission-modal') {
    closeModal(event.target.id);
  }
});
</script>