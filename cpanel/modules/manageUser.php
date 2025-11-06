<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpuser', 'action' => 'manage']])) {
    die('No tienes permisos para acceder a este módulo.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    
    if (empty($action) || empty($id)) {
        http_response_code(400);
        exit;
    }

    if ($action === 'enable' && $id) {
        $profileManager->enableUser($id);
        exit;
    } elseif ($action === 'disable' && $id) {
        $profileManager->disableUser($id);
        exit;
    } elseif ($action === 'permaban' && $id) {
        $profileManager->permabanUser($id);
        exit;
    } elseif ($action === 'delete' && $id) {
        $profileManager->deleteUserCompletely($id);
        exit;
    } elseif ($action === 'update' && $id) {
        $userData = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'second_last_name' => trim($_POST['second_last_name'] ?? ''),
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'gender' => $_POST['gender'] ?? null,
            'country' => trim($_POST['country'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'id_type' => $_POST['id_type'] ?? null,
            'id_number' => trim($_POST['id_number'] ?? ''),
            'university' => trim($_POST['university'] ?? ''),
            'program' => trim($_POST['program'] ?? ''),
            'semester' => !empty($_POST['semester']) ? intval($_POST['semester']) : null,
            'phone_number' => trim($_POST['phone_number'] ?? ''),
            'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'institutional_email' => filter_var(trim($_POST['institutional_email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'card_code' => trim($_POST['card_code'] ?? ''),
            'status' => isset($_POST['status']) ? intval($_POST['status']) : 1,
            'role_id' => !empty($_POST['role_id']) ? intval($_POST['role_id']) : null
        ];
        
        if (empty($userData['first_name']) || empty($userData['last_name']) || empty($userData['email']) || empty($userData['id_type']) || empty($userData['id_number'])) {
            http_response_code(400);
            echo "Faltan campos obligatorios";
            exit;
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo "Email inválido";
            exit;
        }
        
        $profileManager->updateProfileData($id, $userData);
        exit;
    } elseif ($action === 'update_password' && $id) {
        $newPassword = $_POST['new_password'] ?? '';
        
        if (strlen($newPassword) < 6) {
            http_response_code(400);
            echo "La contraseña debe tener al menos 6 caracteres";
            exit;
        }
        
        $userManager->updatePasswordById($id, $newPassword);
        exit;
    }
    
    http_response_code(400);
    exit;
}

$allUsers = $profileManager->getAllUsers();
$groupedUsers = [];
foreach ($allUsers as $user) {
    $userId = $user['id'];
    if (!isset($groupedUsers[$userId])) {
        $groupedUsers[$userId] = [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'middle_name' => $user['middle_name'],
            'last_name' => $user['last_name'],
            'second_last_name' => $user['second_last_name'],
            'birth_date' => $user['birth_date'],
            'gender' => $user['gender'],
            'country' => $user['country'],
            'city' => $user['city'],
            'address' => $user['address'],
            'id_type' => $user['id_type'],
            'id_number' => $user['id_number'],
            'university' => $user['university'],
            'program' => $user['program'],
            'semester' => $user['semester'],
            'phone_number' => $user['phone_number'],
            'email' => $user['email'],
            'institutional_email' => $user['institutional_email'],
            'card_code' => $user['card_code'],
            'status' => $user['status'],
            'created_at' => $user['created_at'],
            'role_names' => [],
            'roles' => []
        ];
    }
    if (!empty($user['role_name'])) {
        $groupedUsers[$userId]['role_names'][] = $user['role_name'];
        $groupedUsers[$userId]['roles'][] = [
            'name' => $user['role_name'],
            'id' => $user['role_id']
        ];
    }
}

$usersJson = json_encode(array_values($groupedUsers));
$rolesJson = json_encode($roleManager->getAllRoles());
?>

<div id="users-module" class="module-content fade-in">
    <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Gestión de Usuarios</h2>
                <p class="text-sm text-slate-600">Administra los usuarios del sistema</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="<?php echo admincp_base(); ?>?module=addUser" class="flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg hover:from-blue-700 hover:to-blue-600 transition-all shadow-md whitespace-nowrap text-sm font-medium">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Nuevo Usuario</span>
                </a>
            </div>
        </div>

        <div class="mb-6">
            <div class="relative">
                <input type="text" id="user-search" placeholder="Buscar usuarios..." class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all pl-10">
                <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
            </div>
        </div>

        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" id="users-grid">
            <?php foreach ($groupedUsers as $user): ?>
            <div class="bg-white rounded-lg p-4 border border-slate-100 hover:shadow-md transition-all user-card min-h-[220px] flex flex-col" data-id="<?= $user['id'] ?>" 
                 data-name="<?= htmlspecialchars(strtolower($user['first_name'] . ' ' . $user['last_name'])) ?>" 
                 data-email="<?= htmlspecialchars(strtolower($user['email'])) ?>" 
                 data-role="<?= htmlspecialchars(strtolower(implode(' ', $user['role_names']))) ?>" 
                 data-status="<?= $user['status'] ?>">
                <div class="flex justify-between items-start mb-4 px-1">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow">
                                <span class="text-white font-bold text-sm">
                                    <?= substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1) ?>
                                </span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 <?= $user['status'] == 1 ? 'bg-green-500' : ($user['status'] == 2 || $user['status'] == 3 ? 'bg-red-500' : 'bg-gray-400') ?> rounded-full border-2 border-white"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-slate-800 text-sm truncate"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                            <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pl-2 shrink-0">
                        <button onclick="toggleUserStatus(<?= $user['id'] ?>, <?= $user['status'] == 1 ? 0 : 1 ?>)" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="<?= $user['status'] == 1 ? 'Desactivar' : 'Activar' ?>">
                            <i data-lucide="<?= $user['status'] == 1 ? 'toggle-right' : 'toggle-left' ?>" class="w-4 h-4 <?= $user['status'] == 1 ? 'text-green-500' : 'text-gray-400' ?>"></i>
                        </button>

                        <button onclick="permabanUser(<?= $user['id'] ?>)" class="p-1.5 bg-white/60 border border-slate-100 rounded-lg hover:bg-slate-100 transition-all" title="Bloqueo permanente">
                            <i data-lucide="lock" class="w-4 h-4 <?= $user['status'] == 3 || $user['status'] == 2 ? 'text-red-500' : 'text-gray-400' ?>"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2 mb-4 flex-1">
                    <div class="flex flex-wrap gap-1">
                        <?php foreach ($user['role_names'] as $roleName): ?>
                            <span class="px-2 py-1 <?= $roleName === 'Admin' ? 'bg-purple-100 text-purple-800' : ($roleName === 'Docente' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?> rounded-md text-xs font-medium truncate max-w-[120px]">
                                <?= htmlspecialchars($roleName) ?>
                            </span>
                        <?php endforeach; ?>
                        <span class="px-2 py-1 <?= $user['status'] == 1 ? 'bg-green-100 text-green-800' : ($user['status'] == 2 || $user['status'] == 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') ?> rounded-md text-xs font-medium">
                            <?= $user['status'] == 1 ? 'Activo' : ($user['status'] == 2 || $user['status'] == 3 ? 'Bloqueado' : 'Inactivo') ?>
                        </span>
                    </div>
                    <?php if (!empty($user['program'])): ?>
                    <p class="text-xs text-slate-600 truncate"><?= htmlspecialchars($user['program']) ?><?= !empty($user['semester']) ? ' - Semestre ' . $user['semester'] : '' ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-slate-500">Registrado: <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                </div>

                <div class="flex gap-2">
                    <button onclick="showUserDetails(<?= $user['id'] ?>)" class="flex-1 flex items-center justify-center gap-1 px-2 py-1 bg-slate-50 border border-slate-100 rounded-md hover:bg-slate-100 transition-all text-xs">
                        <i data-lucide="eye" class="w-3 h-3"></i>
                        Ver
                    </button>
                    <button onclick="editUser(<?= $user['id'] ?>)" class="flex-1 flex items-center justify-center gap-1 px-2 py-1 bg-slate-50 border border-slate-100 rounded-md hover:bg-slate-100 transition-all text-xs">
                        <i data-lucide="edit" class="w-3 h-3"></i>
                        Editar
                    </button>
                    <button onclick="showPasswordModal(<?= $user['id'] ?>)" class="flex items-center justify-center gap-1 px-2 py-1 bg-amber-50 border border-amber-100 rounded-md hover:bg-amber-100 transition-all text-xs text-amber-600">
                        <i data-lucide="key" class="w-3 h-3"></i>
                    </button>
                    <button onclick="confirmDeleteUser(<?= $user['id'] ?>)" class="flex items-center justify-center gap-1 px-2 py-1 bg-red-50 border border-red-100 rounded-md hover:bg-red-100 transition-all text-xs text-red-600">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-between items-center mt-6">
            <p class="text-sm text-slate-600">Mostrando <span id="users-count"><?= count($groupedUsers) ?></span> usuarios</p>
        </div>
    </div>
</div>

<div id="user-details-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="modal-content w-full max-w-3xl bg-white rounded-2xl p-6 max-h-[90vh] overflow-y-auto mx-4">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Detalles del Usuario</h2>
        </div>
        <div class="space-y-6" id="user-details-content"></div>
    </div>
</div>

<div id="edit-user-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden overflow-y-auto py-8">
    <div class="glassmorphism rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl max-h-[90vh] mx-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Editar Usuario</h2>
                    <p class="text-sm text-blue-100">Actualiza la información del usuario</p>
                </div>
                <button class="p-2 hover:bg-white/10 rounded-lg transition-colors" onclick="closeModal('edit-user-modal')">
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <form id="edit-user-form" method="POST" class="space-y-8">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-user-id">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Información Personal</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Primer Nombre*</label>
                            <input type="text" name="first_name" id="edit-first-name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Segundo Nombre</label>
                            <input type="text" name="middle_name" id="edit-middle-name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Primer Apellido*</label>
                            <input type="text" name="last_name" id="edit-last-name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Segundo Apellido</label>
                            <input type="text" name="second_last_name" id="edit-second-last-name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Fecha de Nacimiento</label>
                            <input type="date" name="birth_date" id="edit-birth-date" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Género</label>
                            <select name="gender" id="edit-gender" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i data-lucide="id-card" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Documentación</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Tipo de Documento*</label>
                            <select name="id_type" id="edit-id-type" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                                <option value="">Seleccionar...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PA">Pasaporte</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Número de Documento*</label>
                            <input type="text" name="id_number" id="edit-id-number" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="phone" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Información de Contacto</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">País</label>
                            <input type="text" name="country" id="edit-country" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Ciudad</label>
                            <input type="text" name="city" id="edit-city" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Dirección</label>
                            <input type="text" name="address" id="edit-address" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Teléfono/Celular</label>
                            <input type="tel" name="phone_number" id="edit-phone-number" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Correo Electrónico*</label>
                            <input type="email" name="email" id="edit-email" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800">Información Académica</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Institución</label>
                            <input type="text" name="university" id="edit-university" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Programa Académico</label>
                            <input type="text" name="program" id="edit-program" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Semestre</label>
                            <select name="semester" id="edit-semester" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                                <option value="">Seleccionar...</option>
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>">Semestre <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Correo Institucional</label>
                            <input type="email" name="institutional_email" id="edit-institutional-email" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700">Número de Carnet</label>
                            <input type="text" name="card_code" id="edit-card-code" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-slate-100">
                    <button type="button" onclick="closeModal('edit-user-modal')" class="order-2 sm:order-1 px-6 py-3 bg-white/80 border border-slate-200 text-slate-700 rounded-lg hover:bg-white transition-all font-medium flex items-center justify-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all font-medium flex items-center justify-center gap-2 shadow-lg">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="password-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i data-lucide="key" class="w-5 h-5 text-blue-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Cambiar Contraseña</h3>
        </div>
        <form id="password-form" method="POST">
            <input type="hidden" name="action" value="update_password">
            <input type="hidden" name="id" id="password-user-id">
            <div class="space-y-4 mb-6">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Nueva Contraseña</label>
                    <input type="password" name="new_password" id="new-password" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required minlength="6">
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Confirmar Contraseña</label>
                    <input type="password" id="confirm-password" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required minlength="6">
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('password-modal')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Confirmar eliminación</h3>
        </div>
        <p class="text-slate-600 mb-6">¿Estás seguro de que deseas eliminar permanentemente este usuario? Esta acción no se puede deshacer.</p>
        <div class="flex justify-end gap-3">
            <button class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all" onclick="closeModal('delete-confirm-modal')">
                Cancelar
            </button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                Eliminar Usuario
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const allUsers = <?= $usersJson ?>;
        const roles = <?= $rolesJson ?>;
        
        const searchInput = document.getElementById('user-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const userCards = document.querySelectorAll('.user-card');
                let visibleCount = 0;
                
                userCards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    const email = card.getAttribute('data-email');
                    const role = card.getAttribute('data-role');
                    const status = card.getAttribute('data-status');
                    
                    const matchesSearch = name.includes(searchTerm) || 
                                        email.includes(searchTerm) || 
                                        role.includes(searchTerm) ||
                                        status.toString().includes(searchTerm);
                    
                    if (matchesSearch || searchTerm === '') {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                document.getElementById('users-count').textContent = visibleCount;
            });
        }
        
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }
        });

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        window.showPasswordModal = function(userId) {
            document.getElementById('password-user-id').value = userId;
            showModal('password-modal');
        };

        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const newPassword = document.getElementById('new-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;
                
                if (newPassword !== confirmPassword) {
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                if (newPassword.length < 6) {
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
                
                const formData = new FormData(this);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        alert('Contraseña actualizada correctamente');
                        closeModal('password-modal');
                        location.reload();
                    } else {
                        response.text().then(text => {
                            alert(text || 'Error al actualizar la contraseña');
                        });
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar la contraseña');
                });
            });
        }

        window.showUserDetails = function(userId) {
            const user = allUsers.find(u => u.id == userId);
            if (!user) {
                alert('Usuario no encontrado');
                return;
            }
            
            const createdAt = new Date(user.created_at);
            const formattedDate = createdAt.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const displayField = (value, fieldName, transformFn = null) => {
                if (!value) {
                    return `
                        <div class="flex items-center gap-3 text-slate-400">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            <span class="text-sm">No hay información</span>
                        </div>
                    `;
                }
                
                const displayValue = transformFn ? transformFn(value) : value;
                return `
                    <div class="flex items-center gap-3">
                        <i data-lucide="${getIconForField(fieldName)}" class="w-4 h-4 text-slate-500"></i>
                        <span class="text-sm text-slate-600">${displayValue}</span>
                    </div>
                `;
            };

            const modalContent = `
                <div class="flex items-center gap-6 p-6 bg-white/60 rounded-xl">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-2xl font-bold">${user.first_name?.charAt(0) || ''}${user.last_name?.charAt(0) || ''}</span>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 ${user.status == 1 ? 'bg-green-500' : (user.status == 2 || user.status == 3 ? 'bg-red-500' : 'bg-gray-400')} rounded-full border-4 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-2xl font-bold text-slate-900 truncate">${user.first_name || ''} ${user.last_name || ''}</h3>
                        <p class="text-slate-600 mb-2 truncate">${user.email || 'Sin email registrado'}</p>
                        <div class="flex gap-2 flex-wrap">
                            ${user.role_names.map(roleName => `
                                <span class="px-3 py-1 ${roleName === 'Admin' ? 'bg-purple-100 text-purple-800' : (roleName === 'Docente' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')} rounded-full text-sm font-semibold truncate max-w-[120px]">${roleName || 'Sin rol'}</span>
                            `).join('')}
                            <span class="px-3 py-1 ${user.status == 1 ? 'bg-green-100 text-green-800' : (user.status == 2 || user.status == 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')} rounded-full text-sm font-semibold">${user.status == 1 ? 'Activo' : (user.status == 2 || user.status == 3 ? 'Bloqueado' : 'Inactivo')}</span>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-slate-900">Información Personal</h4>
                        <div class="space-y-3">
                            ${displayField(formattedDate, 'created_at')}
                            ${displayField(user.phone_number, 'phone')}
                            ${displayField(user.country ? `${user.city ? user.city + ', ' : ''}${user.country}` : '', 'location')}
                            ${displayField(user.birth_date, 'birth_date')}
                            ${displayField(user.gender, 'gender', (g) => {
                                if (g === 'M') return 'Masculino';
                                if (g === 'F') return 'Femenino';
                                if (g === 'O') return 'Otro';
                                return '';
                            })}
                            ${displayField(user.address, 'address')}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-slate-900">Información Académica</h4>
                        <div class="space-y-3">
                            ${displayField(user.university, 'university')}
                            ${displayField(user.program, 'program')}
                            ${displayField(user.semester, 'semester', (s) => `Semestre ${s}`)}
                            ${displayField(user.institutional_email, 'institutional_email')}
                            ${displayField(user.card_code, 'card_code')}
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="editUser(${user.id})" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
                        <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
                        Editar Usuario
                    </button>
                </div>
            `;
            
            document.getElementById('user-details-content').innerHTML = modalContent;
            lucide.createIcons();
            showModal('user-details-modal');
        };

        function getIconForField(fieldName) {
            const icons = {
                'created_at': 'calendar',
                'phone': 'phone',
                'location': 'map-pin',
                'birth_date': 'cake',
                'gender': 'user',
                'address': 'home',
                'university': 'school',
                'program': 'book-open',
                'semester': 'book',
                'institutional_email': 'mail',
                'card_code': 'credit-card'
            };
            return icons[fieldName] || 'info';
        }

        window.editUser = function(userId) {
            const user = allUsers.find(u => u.id == userId);
            if (!user) {
                alert('Usuario no encontrado');
                return;
            }
            
            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-first-name').value = user.first_name || '';
            document.getElementById('edit-middle-name').value = user.middle_name || '';
            document.getElementById('edit-last-name').value = user.last_name || '';
            document.getElementById('edit-second-last-name').value = user.second_last_name || '';
            document.getElementById('edit-birth-date').value = user.birth_date || '';
            document.getElementById('edit-gender').value = user.gender || '';
            document.getElementById('edit-id-type').value = user.id_type || '';
            document.getElementById('edit-id-number').value = user.id_number || '';
            document.getElementById('edit-country').value = user.country || '';
            document.getElementById('edit-city').value = user.city || '';
            document.getElementById('edit-address').value = user.address || '';
            document.getElementById('edit-phone-number').value = user.phone_number || '';
            document.getElementById('edit-email').value = user.email || '';
            document.getElementById('edit-university').value = user.university || '';
            document.getElementById('edit-program').value = user.program || '';
            document.getElementById('edit-semester').value = user.semester || '';
            document.getElementById('edit-institutional-email').value = user.institutional_email || '';
            document.getElementById('edit-card-code').value = user.card_code || '';
            
            showModal('edit-user-modal');
        };

        const editForm = document.getElementById('edit-user-form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }
                
                const formData = new FormData(this);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        response.text().then(text => {
                            alert(text || 'Error al actualizar el usuario');
                        });
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el usuario');
                });
            });
        }

        window.toggleUserStatus = function(userId, newStatus) {
            const action = newStatus == 1 ? 'enable' : 'disable';
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&id=${userId}`
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al cambiar el estado del usuario');
                }
            }).catch(error => {
                console.error('Error:', error);
                    alert('Error al cambiar el estado del usuario');
            });
        };

        window.permabanUser = function(userId) {
            if (confirm('¿Estás seguro de bloquear permanentemente este usuario?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=permaban&id=${userId}`
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error al bloquear el usuario');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al bloquear el usuario');
                });
            }
        };

        window.confirmDeleteUser = function(userId) {
            const confirmBtn = document.getElementById('confirm-delete-btn');
            
            const oldOnClick = confirmBtn.onclick;
            
            confirmBtn.onclick = function() {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${userId}`
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el usuario');
                        closeModal('delete-confirm-modal');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el usuario');
                    closeModal('delete-confirm-modal');
                }).finally(() => {
                    confirmBtn.onclick = oldOnClick;
                });
            };
            
            showModal('delete-confirm-modal');
        };

        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        };
    });
</script>