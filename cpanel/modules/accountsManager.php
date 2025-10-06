<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'create']])) {
    die('No tienes permisos para acceder a este módulo.');
}

$db = Connection::Database('sisinfo');
$pdo = $db->getConnection();
$roleManager = new RoleManager($pdo);
$roles = $roleManager->getAllRoles();
?>

<div id="global-alert" class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 border-l-4 hidden sm:block">
</div>

<div class="glassmorphism rounded-2xl p-4 sm:p-6 shadow-xl mb-6">
    <!--Editar el sistema de roles para previsualiacion de usuario y el buscador-->
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative w-full">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" placeholder="Buscar usuarios..." class="pl-10 pr-4 py-2 sm:py-3 w-full bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm sm:text-base">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <div class="flex gap-3">
                    <button class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-3 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all text-sm sm:text-base">
                        <i data-lucide="download" class="w-3 h-3 sm:w-4 sm:h-4"></i>
                        <span class="hidden sm:inline">Exportar</span>
                    </button>
                    <button class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-3 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all text-sm sm:text-base">
                        <i data-lucide="upload" class="w-3 h-3 sm:w-4 sm:h-4"></i>
                        <span class="hidden sm:inline">Importar</span>
                    </button>
                </div>
                <button onclick="openUserModal()" class="hidden md:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg text-sm whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Crear Usuario</span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6">
        
    </div>
</div>

<!--User Modal-->
<div id="user-modal" class="modal">
    <div class="modal-content w-full max-w-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Crear Nuevo Usuario</h2>
            <button onclick="closeModal('user-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
          
        <form id="createUserForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Sección de información personal -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Primer Nombre*</label>
                    <input type="text" name="first_name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Jhon" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Segundo Nombre</label>
                    <input type="text" name="middle_name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Primer Apellido*</label>
                    <input type="text" name="last_name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Doe" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Segundo Apellido</label>
                    <input type="text" name="second_last_name" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Género*</label>
                    <select name="gender" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Seleccionar Género</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                        <option value="O">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de documento*</label>
                    <select name="id_type" class="w-full px-4 pya-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                        <option value="">Seleccionar Tipo</option>
                        <option value="TI">Tarjeta de identidad</option>
                        <option value="CC">Cédula de ciudadanía</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Número de identificación*</label>
                    <input type="text" name="id_number" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Ciudad</label>
                    <input type="text" name="city" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">País</label>
                    <input type="text" name="country" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dirección</label>
                    <input type="text" name="address" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Celular</label>
                    <input type="text" name="phone_number" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Sección de credenciales -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Correo electrónico*</label>
                    <input type="email" name="email" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contraseña</label>
                    <input type="password" name="password" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Verificar Contraseña</label>
                    <input type="password" name="confirm_password" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rol*</label>
                    <select name="role" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                        <?php foreach($roles as $role): ?>   
                            <option value="<?= htmlspecialchars($role[_CLMN_ROLE_ID_]) ?>">
                                <?= htmlspecialchars($role[_CLMN_ROLE_NAME_]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Sección de información académica -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Programa Académico*</label>
                    <input type="text" name="program" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Semestre*</label>
                    <select name="semester" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Seleccionar semestre</option>
                        <option value="1">Primer Semestre</option>
                        <option value="2">Segundo Semestre</option>
                        <option value="3">Tercer Semestre</option>
                        <option value="4">Cuarto Semestre</option>
                        <option value="5">Quinto Semestre</option>
                        <option value="6">Sexto Semestre</option>
                        <option value="7">Séptimo Semestre</option>
                        <option value="8">Octavo Semestre</option>
                        <option value="9">Noveno Semestre</option>
                        <option value="10">Décimo Semestre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Institución*</label>
                    <input type="text" name="university" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Correo Institucional</label>
                    <input type="email" name="institutional_email" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Carnet</label>
                    <input type="text" name="card_code" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal('user-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                    Cancelar
                </button>
                <button type="submit" name="webengineRegister_submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
  let baseUrl = "<?php echo rtrim(admincp_base(), '/'); ?>";

    document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("createUserForm");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("action", "create");
        const email = formData.get("email");
        const username = formData.get("username");
        const password = formData.get("password");

        try {
            const response = await fetch(`${baseUrl}?module=accountsManager.api`, {
                method: "POST",
                body: formData,
            });

            let result;
            const contentType = response.headers.get("Content-Type");

            if (contentType && contentType.includes("application/json")) {
                result = await response.json();
            } else {
                const text = await response.text();
                throw new SyntaxError("Respuesta no JSON:\n" + text);
            }

            if (result?.success) {
                form.reset();
                closeModal("user-modal");

                showGlobalAlert({
                    type: 'success',
                    title: 'REGISTRO EXITOSO',
                    htmlContent: `
                        <p class="font-medium mb-2 text-green-900">Tus datos de acceso:</p>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <p><strong class="w-32">Correo electrónico:</strong> <code>${email}</code></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p><strong class="w-32">Nombre de usuario:</strong> <code>${username}</code></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p><strong class="w-32">Contraseña:</strong> <code>${password}</code></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 my-3">
                            <button onclick="copyAllCredentials()" class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-800 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                Copiar todos los datos
                            </button>
                        </div>
                        <p class="text-xs text-green-600">Los datos copiados se guardarán temporalmente en tu portapapeles.</p>
                    `
                });
            } else {
                alert("❌ Error al crear usuario:\n" + (result.message || "Error desconocido."));
            }
        } catch (error) {
            console.error("Error capturado:", error);
            alert("❌ Error inesperado:\n" + error.message);
        }
    });
    });
</script>