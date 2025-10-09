<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_role':
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $estado = isset($_POST['estado']) && $_POST['estado'] === '1';
                
                if (!empty($name)) {
                    $success = $roleManager->createRole($name, $description, $estado);
                    if ($success) {
                        $message = "Rol creado exitosamente";
                    } else {
                        $error = "Error al crear el rol";
                    }
                }
                break;
                
            case 'update_role':
                $roleId = (int)($_POST['role_id'] ?? 0);
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $estado = isset($_POST['estado']) && $_POST['estado'] === '1';
                
                if ($roleId > 0 && !empty($name)) {
                    $success = $roleManager->updateRole($roleId, $name, $description, $estado);
                    if ($success) {
                        $message = "Rol actualizado exitosamente";
                    } else {
                        $error = "Error: Ya existe un rol con ese nombre";
                    }
                }
                break;
                
            case 'delete_role':
                $roleId = (int)($_POST['role_id'] ?? 0);
                if ($roleId > 0) {
                    $success = $roleManager->deleteRole($roleId);
                    if ($success) {
                        $message = "Rol eliminado exitosamente";
                    } else {
                        $error = "Error al eliminar el rol";
                    }
                }
                break;
                
            case 'toggle_role_status':
                $roleId = (int)($_POST['role_id'] ?? 0);
                $estado = isset($_POST['estado']) && $_POST['estado'] === '1';
                
                if ($roleId > 0) {
                    if ($estado) {
                        $success = $roleManager->activateRole($roleId);
                        $message = $success ? "Rol activado exitosamente" : "Error al activar el rol";
                    } else {
                        $success = $roleManager->deactivateRole($roleId);
                        $message = $success ? "Rol desactivado exitosamente" : "Error al desactivar el rol";
                    }
                    
                    if (!$success) {
                        $error = $message;
                        unset($message);
                    }
                }
                break;
                
            case 'assign_role':
                $userId = (int)($_POST['user_id'] ?? 0);
                $roleIds = $_POST['role_ids'] ?? [];
                
                if ($userId > 0 && !empty($roleIds)) {
                    $currentRoles = $roleManager->getUserRoles($userId, false);
                    foreach ($currentRoles as $role) {
                        $roleManager->removeRoleFromUser($userId, $role[_CLMN_ROLE_ID_]);
                    }
                    
                    $successCount = 0;
                    foreach ($roleIds as $roleId) {
                        $roleId = (int)$roleId;
                        if ($roleId > 0) {
                            $success = $roleManager->assignRoleToUser($userId, $roleId);
                            if ($success) $successCount++;
                        }
                    }
                    
                    if ($successCount > 0) {
                        $message = "Roles asignados exitosamente";
                    } else {
                        $error = "Error al asignar los roles";
                    }
                }
                break;
                
            case 'remove_role':
                $userId = (int)($_POST['user_id'] ?? 0);
                $roleId = (int)($_POST['role_id'] ?? 0);
                if ($userId > 0 && $roleId > 0) {
                    $success = $roleManager->removeRoleFromUser($userId, $roleId);
                    if ($success) {
                        $message = "Rol removido exitosamente";
                    } else {
                        $error = "Error al remover el rol";
                    }
                }
                break;
        }
    }
}

$allRoles = $roleManager->getAllRoles(false);
$allUsers = $profileManager->getAllUsers();

$usersData = [];
foreach ($allUsers as $user) {
    $userId = $user['id'];
    $userRoles = $roleManager->getUserRoles($userId, false);
    
    $formattedRoles = [];
    foreach ($userRoles as $role) {
        $formattedRoles[] = [
            'role_id' => $role[_CLMN_ROLE_ID_],
            'role_name' => $role[_CLMN_ROLE_NAME_],
            'role_estado' => $role[_CLMN_ROLE_ESTADO_]
        ];
    }
    
    $usersData[] = [
        'id' => $userId,
        'username' => $user['username'],
        'email' => $user['email'],
        'first_name' => $user['first_name'] ?? '',
        'last_name' => $user['last_name'] ?? '',
        'roles' => $formattedRoles
    ];
}

$roleUserCounts = [];
foreach ($allRoles as $role) {
    $roleId = $role[_CLMN_ROLE_ID_];
    $roleUserCounts[$roleId] = count($roleManager->getUsersByRoleType($role[_CLMN_ROLE_NAME_], false));
}

$jsData = [
    'users' => $usersData,
    'roles' => array_map(function($role) {
        return [
            'id' => $role[_CLMN_ROLE_ID_],
            'name' => $role[_CLMN_ROLE_NAME_],
            'description' => $role[_CLMN_ROLE_DESC_],
            'estado' => (bool)$role[_CLMN_ROLE_ESTADO_]
        ];
    }, $allRoles),
    'roleUserCounts' => $roleUserCounts
];
?>

<?php if (isset($message)): ?>
<div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Gestión de Roles</h2>
            <p class="text-slate-600">Administra los roles y permisos del sistema</p>
        </div>
        <button onclick="openRoleModal('new')" class="mt-4 md:mt-0 flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Rol
        </button>
    </div>
    
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($allRoles as $role): 
            $isActive = (bool)$role[_CLMN_ROLE_ESTADO_];
            $roleId = $role[_CLMN_ROLE_ID_];
            $roleColor = $isActive ? 'from-blue-500 to-purple-500' : 'from-gray-400 to-gray-500';
            $roleIcon = $isActive ? 'shield' : 'shield-off';
        ?>
            <div class="glassmorphism rounded-xl p-6 shadow-lg card-hover <?= $isActive ? '' : 'opacity-70' ?>">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-gradient-to-r <?= $roleColor ?> rounded-xl shadow-lg">
                        <i data-lucide="<?= $roleIcon ?>" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900"><?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?></h3>
                        <p class="text-sm text-slate-600"><?= htmlspecialchars($role[_CLMN_ROLE_DESC_]) ?></p>
                    </div>
                </div>
                <div class="space-y-2 mb-4">
                    <p class="text-sm text-slate-600">• Permisos básicos</p>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-slate-700"><?= $roleUserCounts[$roleId] ?? 0 ?> usuarios</span>
                    <span class="px-2 py-1 <?= $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> rounded-lg text-xs font-semibold">
                        <?= $isActive ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
                <div class="flex gap-2">
                    <button onclick="openRoleModal('<?= $roleId ?>', '<?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?>', '<?= htmlspecialchars($role[_CLMN_ROLE_DESC_]) ?>', <?= $isActive ? 'true' : 'false' ?>)" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all text-sm">
                        <i data-lucide="edit" class="w-3 h-3"></i>
                        Editar
                    </button>
                    <?php if ($isActive): ?>
                        <button onclick="toggleRoleStatus(<?= $roleId ?>, false)" class="flex items-center justify-center gap-1 px-3 py-2 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-all text-sm text-yellow-600">
                            <i data-lucide="toggle-left" class="w-3 h-3"></i>
                        </button>
                    <?php else: ?>
                        <button onclick="toggleRoleStatus(<?= $roleId ?>, true)" class="flex items-center justify-center gap-1 px-3 py-2 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-all text-sm text-green-600">
                            <i data-lucide="toggle-right" class="w-3 h-3"></i>
                        </button>
                    <?php endif; ?>
                    <button onclick="showDeleteConfirm('<?= $roleId ?>', '<?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?>')" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all text-sm text-red-600">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="user-roles-section" class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Asignación de Roles</h2>
            <p class="text-slate-600">Asigna roles a usuarios y gestiona permisos</p>
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
            <input type="text" id="user-search" placeholder="Buscar por nombre, email o usuario..." class="pl-10 pr-4 py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
        </div>
        <select id="role-filter" class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            <option value="">Todos los roles</option>
            <?php foreach ($allRoles as $role): ?>
                <option value="<?= $role[_CLMN_ROLE_ID_] ?>">
                    <?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?> (<?= $roleUserCounts[$role[_CLMN_ROLE_ID_]] ?? 0 ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button onclick="clearFilters()" class="px-4 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all flex items-center">
            <i data-lucide="x" class="w-4 h-4"></i> Limpiar
        </button>
    </div>
    
    <div id="users-container" class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/20 text-left text-sm text-slate-600">
                    <th class="pb-3 pl-2 pr-4">Usuario</th>
                    <th class="pb-3 px-4">Email</th>
                    <th class="pb-3 px-4">Roles actuales</th>
                    <th class="pb-3 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody id="users-list" class="divide-y divide-white/20">
            </tbody>
        </table>
    </div>

    <div id="no-users-message" class="hidden p-8 text-center text-slate-500">
        <i data-lucide="users" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
        <p class="text-lg">No se encontraron usuarios</p>
        <p class="mt-2">Prueba con otros criterios de búsqueda</p>
        <button onclick="clearFilters()" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Limpiar filtros
        </button>
    </div>

    <div id="pagination-container" class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4 hidden">
        <div id="pagination-info" class="text-sm text-slate-600"></div>
        <div id="pagination-controls" class="flex gap-1"></div>
    </div>
</div>

<div id="role-modal" class="modal">
    <div class="modal-content w-full max-w-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-900" id="role-modal-title">Crear Nuevo Rol</h2>
            <button onclick="closeModal('role-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" id="role-action" value="create_role">
            <input type="hidden" id="role-id" name="role_id" value="">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre del Rol</label>
                <input type="text" id="role-name" name="name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ingresa el nombre del rol" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                <textarea rows="3" id="role-description" name="description" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ingresa la descripción del rol"></textarea>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="role-estado" name="estado" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                <label for="role-estado" class="ms-2 text-sm font-medium text-gray-900">Rol activo</label>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal('role-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
                    Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<div id="user-role-modal" class="modal">
    <div class="modal-content w-full max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900">Asignar Roles</h2>
            <button onclick="closeModal('user-role-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form method="POST" id="assign-roles-form">
            <input type="hidden" name="action" value="assign_role">
            <input type="hidden" id="user-id" name="user_id" value="">
            
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg font-semibold" id="user-initials">AD</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900" id="user-name">Usuario</h3>
                        <p class="text-sm text-slate-600" id="user-email">email@ejemplo.com</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Roles Disponibles</label>
                    <div class="space-y-2 max-h-60 overflow-y-auto" id="roles-container">
                        <?php foreach ($allRoles as $role): 
                            $isActive = (bool)$role[_CLMN_ROLE_ESTADO_];
                        ?>
                            <div class="flex items-center justify-between p-3 bg-white/60 rounded-lg <?= $isActive ? '' : 'opacity-60' ?>">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 <?= $isActive ? 'bg-blue-500' : 'bg-gray-400' ?> rounded-full"></div>
                                    <span class="text-sm font-medium"><?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?></span>
                                    <?php if (!$isActive): ?>
                                        <span class="text-xs text-red-500">(Inactivo)</span>
                                    <?php endif; ?>
                                </div>
                                <input type="checkbox" name="role_ids[]" value="<?= $role[_CLMN_ROLE_ID_] ?>" <?= $isActive ? '' : 'disabled' ?>>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal('user-role-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<div id="confirm-modal" class="modal">
    <div class="modal-content w-full max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900" id="confirm-title">Confirmar Acción</h2>
            <button onclick="closeModal('confirm-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <p id="confirm-message">¿Estás seguro de que quieres realizar esta acción?</p>
            <form id="delete-form" method="POST" style="display: none;">
                <input type="hidden" name="action" value="delete_role">
                <input type="hidden" id="delete-role-id" name="role_id" value="">
            </form>
            <form id="toggle-status-form" method="POST" style="display: none;">
                <input type="hidden" name="action" value="toggle_role_status">
                <input type="hidden" id="toggle-role-id" name="role_id" value="">
                <input type="hidden" id="toggle-role-estado" name="estado" value="">
            </form>
        </div>
        
        <div class="flex gap-4 pt-4">
            <button onclick="closeModal('confirm-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                Cancelar
            </button>
            <button onclick="confirmAction()" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all font-medium shadow-lg">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
const appData = <?= json_encode($jsData) ?>;
const usersData = appData.users;
const allRoles = appData.roles;
const roleUserCounts = appData.roleUserCounts;

let filteredUsers = [...usersData];
let currentPage = 1;
const usersPerPage = 5;

function filterUsers() {
    const searchTerm = document.getElementById('user-search').value.toLowerCase();
    const roleFilter = document.getElementById('role-filter').value;
    
    filteredUsers = usersData.filter(user => {
        const matchesSearch = !searchTerm || 
            user.username.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchTerm);
        
        let matchesRole = !roleFilter;
        if (roleFilter) {
            matchesRole = user.roles.some(role => role.role_id == roleFilter);
        }
        
        return matchesSearch && matchesRole;
    });
    
    currentPage = 1;
    renderUsers();
    renderPagination();
}

function renderUsers() {
    const usersList = document.getElementById('users-list');
    const noUsersMessage = document.getElementById('no-users-message');
    const usersContainer = document.getElementById('users-container');
    const paginationContainer = document.getElementById('pagination-container');
    
    if (filteredUsers.length === 0) {
        usersList.innerHTML = '';
        noUsersMessage.classList.remove('hidden');
        usersContainer.classList.add('hidden');
        paginationContainer.classList.add('hidden');
        return;
    }
    
    noUsersMessage.classList.add('hidden');
    usersContainer.classList.remove('hidden');
    paginationContainer.classList.remove('hidden');
    
    const startIndex = (currentPage - 1) * usersPerPage;
    const endIndex = Math.min(startIndex + usersPerPage, filteredUsers.length);
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex);
    
    let usersHTML = '';
    
    paginatedUsers.forEach(user => {
        const fullName = `${user.first_name} ${user.last_name}`.trim() || user.username;
        const initials = fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        
        let rolesHTML = '';
        if (user.roles && user.roles.length > 0) {
            user.roles.forEach(role => {
                const isRoleActive = role.role_estado;
                rolesHTML += `
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full ${isRoleActive ? '' : 'opacity-60'}">
                        ${role.role_name}
                        ${isRoleActive ? `
                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este rol?')">
                                <input type="hidden" name="action" value="remove_role">
                                <input type="hidden" name="user_id" value="${user.id}">
                                <input type="hidden" name="role_id" value="${role.role_id}">
                                <button type="submit" class="text-blue-600 hover:text-blue-800 opacity-70 hover:opacity-100">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </form>
                        ` : `<span class="text-xs text-red-500">(Inactivo)</span>`}
                    </span>
                `;
            });
        } else {
            rolesHTML = '<span class="text-slate-400 text-sm">Sin roles asignados</span>';
        }
        
        usersHTML += `
            <tr class="hover:bg-white/60 transition-colors">
                <td class="py-4 pl-2 pr-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-sm font-semibold">${initials}</span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">${escapeHtml(fullName)}</p>
                            <p class="text-xs text-slate-500">@${escapeHtml(user.username)}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-4">
                    <p class="text-sm text-slate-600">${escapeHtml(user.email)}</p>
                </td>
                <td class="py-4 px-4">
                    <div class="flex flex-wrap gap-2">${rolesHTML}</div>
                </td>
                <td class="py-4 px-4">
                    <button onclick="openUserRoleModal(${user.id}, '${escapeHtml(fullName).replace(/'/g, "\\'")}', '${escapeHtml(user.email).replace(/'/g, "\\'")}')" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg text-sm">
                        <i data-lucide="edit" class="w-3 h-3"></i>
                        Editar Roles
                    </button>
                </td>
            </tr>
        `;
    });
    
    usersList.innerHTML = usersHTML;
    lucide.createIcons();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderPagination() {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');
    
    if (totalPages <= 1) {
        paginationControls.innerHTML = '';
        paginationInfo.textContent = `Mostrando ${filteredUsers.length} usuarios`;
        return;
    }
    
    const startIndex = (currentPage - 1) * usersPerPage + 1;
    const endIndex = Math.min(startIndex + usersPerPage - 1, filteredUsers.length);
    paginationInfo.textContent = `Mostrando ${startIndex} a ${endIndex} de ${filteredUsers.length} usuarios`;
    
    let paginationHTML = '';
    
    if (currentPage > 1) {
        paginationHTML += `
            <button onclick="goToPage(1)" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all" title="Primera página">
                <i data-lucide="chevrons-left" class="w-4 h-4"></i>
            </button>
            <button onclick="goToPage(${currentPage - 1})" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all" title="Página anterior">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
        `;
    }
    
    if (currentPage > 3) {
        paginationHTML += `
            <button onclick="goToPage(1)" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">1</button>
        `;
        if (currentPage > 4) {
            paginationHTML += `<span class="px-3 py-1">...</span>`;
        }
    }
    
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button onclick="goToPage(${i})" class="px-3 py-1 min-w-[2.5rem] text-center ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-white/60 hover:bg-white/80'} border border-white/20 rounded-lg transition-all">${i}</button>
        `;
    }
    
    if (currentPage < totalPages - 2) {
        if (currentPage < totalPages - 3) {
            paginationHTML += `<span class="px-3 py-1">...</span>`;
        }
        paginationHTML += `
            <button onclick="goToPage(${totalPages})" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all">${totalPages}</button>
        `;
    }
    
    if (currentPage < totalPages) {
        paginationHTML += `
            <button onclick="goToPage(${currentPage + 1})" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all" title="Página siguiente">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
            <button onclick="goToPage(${totalPages})" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg hover:bg-white/80 transition-all" title="Última página">
                <i data-lucide="chevrons-right" class="w-4 h-4"></i>
            </button>
        `;
    }
    
    paginationControls.innerHTML = paginationHTML;
    lucide.createIcons();
}

function goToPage(page) {
    currentPage = page;
    renderUsers();
    renderPagination();
    document.getElementById('user-roles-section').scrollIntoView({ behavior: 'smooth' });
}

function clearFilters() {
    document.getElementById('user-search').value = '';
    document.getElementById('role-filter').value = '';
    filterUsers();
}

function openRoleModal(roleId, roleName = '', roleDescription = '', estado = true) {
    const modal = document.getElementById('role-modal');
    const title = document.getElementById('role-modal-title');
    const actionInput = document.getElementById('role-action');
    const nameInput = document.getElementById('role-name');
    const descInput = document.getElementById('role-description');
    const estadoInput = document.getElementById('role-estado');
    const idInput = document.getElementById('role-id');
    
    if (roleId === 'new') {
        title.textContent = 'Crear Nuevo Rol';
        actionInput.value = 'create_role';
        nameInput.value = '';
        descInput.value = '';
        estadoInput.checked = true;
        idInput.value = '';
    } else {
        title.textContent = 'Editar Rol';
        actionInput.value = 'update_role';
        nameInput.value = roleName;
        descInput.value = roleDescription;
        estadoInput.checked = estado;
        idInput.value = roleId;
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function openUserRoleModal(userId, userName, userEmail) {
    const modal = document.getElementById('user-role-modal');
    const nameElement = document.getElementById('user-name');
    const emailElement = document.getElementById('user-email');
    const initialsElement = document.getElementById('user-initials');
    const userIdElement = document.getElementById('user-id');
    
    nameElement.textContent = userName;
    emailElement.textContent = userEmail;
    userIdElement.value = userId;
    
    const names = userName.split(' ');
    let initials = '';
    if (names.length > 0) initials += names[0].charAt(0).toUpperCase();
    if (names.length > 1) initials += names[names.length - 1].charAt(0).toUpperCase();
    initialsElement.textContent = initials || 'US';
    
    const checkboxes = document.querySelectorAll('input[name="role_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    const user = usersData.find(u => u.id == userId);
    if (user) {
        user.roles.forEach(role => {
            const checkbox = document.querySelector(`input[name="role_ids[]"][value="${role.role_id}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function showDeleteConfirm(roleId, roleName) {
    document.getElementById('confirm-title').textContent = 'Eliminar Rol';
    document.getElementById('confirm-message').textContent = `¿Estás seguro de que quieres eliminar el rol "${roleName}"? Esta acción no se puede deshacer.`;
    document.getElementById('delete-role-id').value = roleId;
    
    document.getElementById('delete-form').style.display = 'block';
    document.getElementById('toggle-status-form').style.display = 'none';
    
    document.getElementById('confirm-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function toggleRoleStatus(roleId, activate) {
    document.getElementById('confirm-title').textContent = activate ? 'Activar Rol' : 'Desactivar Rol';
    document.getElementById('confirm-message').textContent = `¿Estás seguro de que quieres ${activate ? 'activar' : 'desactivar'} este rol?`;
    document.getElementById('toggle-role-id').value = roleId;
    document.getElementById('toggle-role-estado').value = activate ? '1' : '0';
    
    document.getElementById('toggle-status-form').style.display = 'block';
    document.getElementById('delete-form').style.display = 'none';
    
    document.getElementById('confirm-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function confirmAction() {
    const deleteForm = document.getElementById('delete-form');
    const toggleForm = document.getElementById('toggle-status-form');
    
    if (deleteForm.style.display !== 'none') {
        deleteForm.submit();
    } else if (toggleForm.style.display !== 'none') {
        toggleForm.submit();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = 'auto';
}

document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
});

document.getElementById('user-search').addEventListener('input', filterUsers);
document.getElementById('role-filter').addEventListener('change', filterUsers);

document.addEventListener('DOMContentLoaded', function() {
    renderUsers();
    renderPagination();
    lucide.createIcons();
});
</script>