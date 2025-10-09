<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpuser', 'action' => 'create']])) {
    die('No tienes permisos para acceder a este módulo.');
}

$logger = new ErrorLogger();
$roles = $roleManager->getAllRoles();

$registeredEmail = '';
$registeredUsername = '';
$registeredPassword = '';

try {
    if (isset($_POST['webengineRegister_submit'])) {
        try {
            $required_fields = [
                'first_name' => 'Primer Nombre',
                'last_name' => 'Primer Apellido',
                'id_number' => 'Documento de identidad',
                'email' => 'Correo electrónico',
                'id_type' => 'Tipo de documento',
                'role' => 'Rol'
            ];

            foreach ($required_fields as $field => $label) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo '$label' es obligatorio.");
                }
            }

            $formData = [
                'first_name' => trim($_POST['first_name']),
                'middle_name' => trim($_POST['middle_name'] ?? ''),
                'last_name' => trim($_POST['last_name']),
                'second_last_name' => trim($_POST['second_last_name'] ?? ''),
                'birth_date' => $_POST['birth_date'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'country' => trim($_POST['country'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'id_type' => $_POST['id_type'],
                'id_number' => trim($_POST['id_number']),
                'university' => trim($_POST['university'] ?? ''),
                'program' => trim($_POST['program'] ?? ''),
                'semester' => $_POST['semester'] ?? null,
                'phone_number' => trim($_POST['phone_number'] ?? ''),
                'institutional_email' => trim($_POST['institutional_email'] ?? ''),
                'card_code' => trim($_POST['card_code'] ?? '')
            ];

            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = trim($_POST['email']);
            $roleId = intval($_POST['role']);

            if (empty($password) || empty($confirm_password)) {
                $password = $confirm_password = $profileManager->generateRandomPassword(12);
            } elseif ($password !== $confirm_password) {
                throw new Exception("Las contraseñas no coinciden.");
            }

            $username = strtolower(
                substr($formData['first_name'], 0, 3) . 
                substr($formData['last_name'], 0, 3) . 
                substr($formData['id_number'], -3)
            );

            $ucode = strtolower(
                substr($formData['first_name'], 0, 3) . 
                substr($formData['last_name'], -3) . 
                substr($formData['id_number'], 1, 1) . 
                substr($formData['id_number'], -2)
            );

            $userId = $profileManager->registerFullUser(
                $username,
                $email,
                $password,
                $formData,
                $roleId
            );

            if (!$userId) {
                throw new Exception("No se pudo registrar el usuario.");
            }
            
            $registeredEmail = $email;
            $registeredUsername = $username;
            $registeredPassword = $password;
            ?>
            <div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-green-500 bg-gradient-to-r from-green-50/60 to-white/20 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-green-100 rounded-xl shadow-inner">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-green-900 mb-3">¡Usuario creado exitosamente!</h3>
                        
                        <div class="grid md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white/70 p-4 rounded-lg">
                                <p class="text-sm font-medium text-slate-700 mb-2">Correo electrónico</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-900 font-mono" id="emailValue"><?= htmlspecialchars($email) ?></p>
                                    <button onclick="copyToClipboard('emailValue', 'Correo')" class="p-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                        <i data-lucide="copy" class="w-4 h-4 text-slate-500 hover:text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-white/70 p-4 rounded-lg">
                                <p class="text-sm font-medium text-slate-700 mb-2">Nombre de usuario</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-900 font-mono" id="usernameValue"><?= htmlspecialchars($username) ?></p>
                                    <button onclick="copyToClipboard('usernameValue', 'Usuario')" class="p-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                        <i data-lucide="copy" class="w-4 h-4 text-slate-500 hover:text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-white/70 p-4 rounded-lg">
                                <p class="text-sm font-medium text-slate-700 mb-2">Contraseña temporal</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-900 font-mono" id="passwordValue"><?= htmlspecialchars($password) ?></p>
                                    <button onclick="copyToClipboard('passwordValue', 'Contraseña')" class="p-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                        <i data-lucide="copy" class="w-4 h-4 text-slate-500 hover:text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-3">
                            <button onclick="copyAllCredentials()" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md text-sm font-medium">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                Copiar todos los datos
                            </button>
                            <button onclick="printCredentials()" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md text-sm font-medium">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                                Imprimir credenciales
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } catch (Exception $ex) {
            ?>
            <div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-red-500 bg-gradient-to-r from-red-50/60 to-white/20 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 rounded-xl shadow-inner">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-red-900 mb-3">Error</h3>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($ex->getMessage()) ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
    }
} catch (Exception $e) {
    ?>
    <div class="glassmorphism rounded-2xl p-6 mb-8 border-l-4 border-red-500 bg-gradient-to-r from-red-50/60 to-white/20 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-red-100 rounded-xl shadow-inner">
                <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-red-900 mb-3">Error</h3>
                <p class="text-sm text-red-700"><?= htmlspecialchars($e->getMessage()) ?></p>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="glassmorphism rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Registro de Nuevo Usuario</h2>
                <p class="text-sm text-blue-100">Complete todos los campos obligatorios (*)</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <form id="createUserForm" method="POST" enctype="multipart/form-data" class="space-y-8">
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
                        <input type="text" name="first_name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: María" required>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Segundo Nombre</label>
                        <input type="text" name="middle_name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: Fernanda">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Primer Apellido*</label>
                        <input type="text" name="last_name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: González" required>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Segundo Apellido</label>
                        <input type="text" name="second_last_name" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: Pérez">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Fecha de Nacimiento</label>
                        <input type="date" name="birth_date" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Género*</label>
                        <select name="gender" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
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
                        <select name="id_type" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                            <option value="">Seleccionar...</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="PA">Pasaporte</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Número de Documento*</label>
                        <input type="text" name="id_number" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: 123456789" required>
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
                        <label class="block text-sm font-medium text-slate-700">Teléfono/Celular</label>
                        <input type="tel" name="phone_number" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: 3001234567">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Correo Electrónico*</label>
                        <input type="email" name="email" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: usuario@dominio.com" required>
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
                        <input type="text" name="university" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: Universidad Nacional">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Programa Académico</label>
                        <input type="text" name="program" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: Ingeniería de Sistemas">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Semestre</label>
                        <select name="semester" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            <option value="">Seleccionar...</option>
                            <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>">Semestre <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Correo Institucional</label>
                        <input type="email" name="institutional_email" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: estudiante@universidad.edu">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Número de Carnet</label>
                        <input type="text" name="card_code" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Ej: 202312345">
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i data-lucide="key" class="w-4 h-4 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Credenciales de Acceso</h3>
                </div>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password" id="passwordField" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Dejar vacío para generar automática">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-blue-600">
                                <i data-lucide="eye" class="w-4 h-4" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Mínimo 8 caracteres</p>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" placeholder="Repita la contraseña">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Rol*</label>
                        <select name="role" class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all" required>
                            <option value="">Seleccionar rol...</option>
                            <?php foreach($roles as $role): ?>   
                                <option value="<?= htmlspecialchars($role[_CLMN_ROLE_ID_]) ?>">
                                    <?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-slate-100">
                <button type="button" onclick="history.back()" class="order-2 sm:order-1 px-6 py-3 bg-white/80 border border-slate-200 text-slate-700 rounded-lg hover:bg-white transition-all font-medium flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Cancelar
                </button>
                
                <button type="submit" name="webengineRegister_submit" class="order-1 sm:order-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all font-medium flex items-center justify-center gap-2 shadow-lg">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Registrar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordField = document.getElementById('passwordField');
    const icon = document.getElementById('passwordToggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        passwordField.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}

function copyToClipboard(elementId, label) {
    const element = document.getElementById(elementId);
    if (!element) {
        showNotification('Error: Elemento no encontrado', 'error');
        return;
    }
    
    const text = element.textContent || element.innerText;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text.trim()).then(() => {
            showNotification(`${label} copiado al portapapeles`, 'success');
        }).catch(err => {
            fallbackCopyTextToClipboard(text, label);
        });
    } else {
        fallbackCopyTextToClipboard(text, label);
    }
}

function fallbackCopyTextToClipboard(text, label) {
    const textArea = document.createElement("textarea");
    textArea.value = text.trim();
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        if (successful) {
            showNotification(`${label} copiado al portapapeles`, 'success');
        } else {
            showNotification(`Error al copiar ${label}. Por favor, copie manualmente.`, 'error');
        }
    } catch (err) {
        document.body.removeChild(textArea);
        showNotification(`Error al copiar ${label}. Por favor, copie manualmente.`, 'error');
    }
}

function copyAllCredentials() {
    const email = document.getElementById('emailValue')?.textContent?.trim() || '';
    const username = document.getElementById('usernameValue')?.textContent?.trim() || '';
    const password = document.getElementById('passwordValue')?.textContent?.trim() || '';
    
    if (!email || !username || !password) {
        showNotification('Error: No se encontraron credenciales para copiar', 'error');
        return;
    }
    
    const credentials = `Datos de acceso:\n\nCorreo: ${email}\nUsuario: ${username}\nContraseña: ${password}`;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(credentials).then(() => {
            showNotification("Todos los datos copiados al portapapeles", 'success');
        }).catch(err => {
            fallbackCopyTextToClipboard(credentials, "Todos los datos");
        });
    } else {
        fallbackCopyTextToClipboard(credentials, "Todos los datos");
    }
}

function printCredentials() {
    const email = document.getElementById('emailValue')?.textContent?.trim() || '';
    const username = document.getElementById('usernameValue')?.textContent?.trim() || '';
    const password = document.getElementById('passwordValue')?.textContent?.trim() || '';
    
    if (!email || !username || !password) {
        showNotification('Error: No se encontraron credenciales para imprimir', 'error');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Credenciales de Usuario</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .credentials { border: 2px solid #333; padding: 20px; margin: 20px 0; }
                    .field { margin: 10px 0; }
                    .label { font-weight: bold; }
                    .warning { color: red; font-style: italic; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Credenciales de Usuario</h1>
                    <p>Generado el: ${new Date().toLocaleString()}</p>
                </div>
                <div class="credentials">
                    <div class="field"><span class="label">Correo electrónico:</span> ${email}</div>
                    <div class="field"><span class="label">Nombre de usuario:</span> ${username}</div>
                    <div class="field"><span class="label">Contraseña temporal:</span> ${password}</div>
                </div>
                <p class="warning">⚠️ Guarde este documento en un lugar seguro y elimínelo después de su uso.</p>
                <script>
                    window.onload = function() { window.print(); setTimeout(() => window.close(), 500); }
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

function showNotification(message, type = 'success') {
    const notification = document.createElement("div");
    notification.className = `fixed bottom-4 right-4 px-4 py-2 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    
    const icon = type === 'success' ? 'check' : 'alert-circle';
    notification.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4"></i> ${message}`;
    
    document.body.appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => {
        notification.classList.remove("animate-fade-in");
        notification.classList.add("animate-fade-out");
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

const style = document.createElement("style");
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(10px); }
    }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-fade-out { animation: fadeOut 0.3s ease-out forwards; }
`;
document.head.appendChild(style);
</script>