<?php 
if (!isLoggedIn()) { redirect(); }
if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');

include(__PATH_MODULES__.'/header.php');

$logger = new ErrorLogger();

try {
    $db = Connection::Database('sisinfo');
    $pdo = $db->getConnection();

    $profileManager = new ProfileManager($pdo, $db, $logger);
    $userId = $_SESSION['userid'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $allowedFields = [
            _DETAIL_FIRSTNAME_, _DETAIL_MIDDLENAME_, _DETAIL_LASTNAME_, _DETAIL_LASTNAME2_,
            _DETAIL_BIRTHDATE_, _DETAIL_GENDER_, _DETAIL_IDTYPE_, _DETAIL_IDNUM_,
            _DETAIL_COUNTRY_, _DETAIL_CITY_, _DETAIL_ADDRESS_, _DETAIL_PHONE_,
            _DETAIL_INST_EMAIL_, _DETAIL_UNIVERSITY_, _DETAIL_PROGRAM_,
            _DETAIL_SEMESTER_, _DETAIL_CARD_CODE_
        ];

        $formData = array_intersect_key($_POST, array_flip($allowedFields));

        if ($profileManager->updateProfileData($userId, $formData)) {
            $successMessage = "¡Perfil actualizado exitosamente!";
        } else {
            $errorMessage = "Error al guardar los cambios. Intenta nuevamente.";
        }

        $info = $profileManager->getInfo($userId);
    } else {
        $info = $profileManager->getInfo($userId);
    }

} catch (Throwable $e) {
    error_log('[Perfil Error] ' . $e->getMessage());
    $info = [];
}

function val(array $arr, string $key) {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key]) : '';
}
?>

<div class="max-w-[1400px] mx-auto mb-12">
    <div class="mb-8">
        <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide justify-center">
           <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-secondary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-secondary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-3 h-3 text-[var(--color-secondary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Proyectos</span>
            </a>
            <?php endif; ?>
            
            <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>
            <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-primary)]/10 rounded flex items-center justify-center">
                    <i data-lucide="award" class="w-3 h-3 text-[var(--color-primary)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Resultados</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-primary)] text-[var(--color-navbar-text)] border border-[var(--color-primary)] shadow-sm whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-navbar-text)]/20 rounded flex items-center justify-center">
                    <i data-lucide="user" class="w-3 h-3 text-[var(--color-navbar-text)]"></i>
                </div>
                <span class="font-medium text-sm">Perfil</span>
            </a>
            
            <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" 
               class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-[var(--color-surface)] border border-[var(--color-border)] hover:border-[var(--color-danger)] hover:bg-[var(--color-dropdown-hover)] transition-all duration-200 whitespace-nowrap flex-shrink-0">
                <div class="w-5 h-5 bg-[var(--color-danger)]/10 rounded flex items-center justify-center">
                    <i data-lucide="shield" class="w-3 h-3 text-[var(--color-danger)]"></i>
                </div>
                <span class="font-medium text-[var(--color-text)] text-sm">Seguridad</span>
            </a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
    <div class="mb-6 p-4 bg-[var(--color-success)]/10 border border-[var(--color-success)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
        <i data-lucide="check-circle" class="w-5 h-5 text-[var(--color-success)] flex-shrink-0"></i>
        <div>
            <p class="text-[var(--color-success)] font-medium text-sm"><?php echo $successMessage; ?></p>
        </div>
    </div>
    <?php elseif (!empty($errorMessage)): ?>
    <div class="mb-6 p-4 bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/20 rounded-lg flex items-center space-x-3 animate-fadeIn">
        <i data-lucide="alert-circle" class="w-5 h-5 text-[var(--color-danger)] flex-shrink-0"></i>
        <div>
            <p class="text-[var(--color-danger)] font-medium text-sm"><?php echo $errorMessage; ?></p>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Columna Izquierda: Datos Personales + Contacto -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-6 animate-slideUp">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--color-heading)]">Datos Personales</h3>
                            <p class="text-[var(--color-text-muted)] text-sm">Información básica de identificación</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Primer Nombre *</label>
                            <input type="text" name="first_name" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="María"
                                   value="<?php echo val($info, 'first_name'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Segundo Nombre</label>
                            <input type="text" name="middle_name"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Alejandra"
                                   value="<?php echo val($info, 'middle_name'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Primer Apellido *</label>
                            <input type="text" name="last_name" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="González"
                                   value="<?php echo val($info, 'last_name'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Segundo Apellido</label>
                            <input type="text" name="second_last_name"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Rodríguez"
                                   value="<?php echo val($info, 'second_last_name'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Fecha de Nacimiento</label>
                            <input type="date" name="birth_date"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   value="<?php echo val($info, 'birth_date'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Género</label>
                            <select name="gender" class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                <option value="" <?php echo val($info, 'gender') === '' ? 'selected' : ''; ?>>Seleccionar</option>
                                <option value="M" <?php echo val($info, 'gender') === 'M' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo val($info, 'gender') === 'F' ? 'selected' : ''; ?>>Femenino</option>
                                <option value="O" <?php echo val($info, 'gender') === 'O' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CONTACTO EN COLUMNA IZQUIERDA -->
                <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-6 animate-slideUp" style="animation-delay: 0.2s">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-[var(--color-success)]/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="phone" class="w-4 h-4 text-[var(--color-success)]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--color-heading)]">Información de Contacto</h3>
                            <p class="text-[var(--color-text-muted)] text-sm">Datos de ubicación y comunicación</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">País</label>
                            <input type="text" name="country"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-success)] focus:border-[var(--color-success)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Colombia"
                                   value="<?php echo val($info, 'country'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Ciudad</label>
                            <input type="text" name="city"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-success)] focus:border-[var(--color-success)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Bogotá D.C."
                                   value="<?php echo val($info, 'city'); ?>">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Dirección</label>
                            <input type="text" name="address"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-success)] focus:border-[var(--color-success)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Calle 123 #45-67"
                                   value="<?php echo val($info, 'address'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Teléfono</label>
                            <input type="text" name="phone_number"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-success)] focus:border-[var(--color-success)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="+57 300 123 4567"
                                   value="<?php echo val($info, 'phone_number'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Email Institucional *</label>
                            <input type="email" name="institutional_email" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-success)] focus:border-[var(--color-success)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="usuario@universidad.edu"
                                   value="<?php echo val($info, 'institutional_email'); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Documentación + Académica -->
            <div class="space-y-6">
                
                <!-- DOCUMENTACIÓN EN COLUMNA DERECHA -->
                <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-6 animate-slideUp" style="animation-delay: 0.1s">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-[var(--color-primary)]/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="id-card" class="w-4 h-4 text-[var(--color-primary)]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--color-heading)]">Documentación</h3>
                            <p class="text-[var(--color-text-muted)] text-sm">Información de identificación oficial</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Tipo de Documento</label>
                            <select name="id_type" class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]">
                                <option value="" <?php echo val($info, 'id_type') === '' ? 'selected' : ''; ?>>Seleccionar tipo</option>
                                <option value="CC" <?php echo val($info, 'id_type') === 'CC' ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                                <option value="TI" <?php echo val($info, 'id_type') === 'TI' ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
                                <option value="CE" <?php echo val($info, 'id_type') === 'CE' ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                                <option value="PA" <?php echo val($info, 'id_type') === 'PA' ? 'selected' : ''; ?>>Pasaporte</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Número de Documento *</label>
                            <input type="text" name="id_number" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="123456789"
                                   value="<?php echo val($info, 'id_number'); ?>">
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] p-6 animate-slideUp" style="animation-delay: 0.3s">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-[var(--color-secondary)]/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-[var(--color-secondary)]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--color-heading)]">Información Académica</h3>
                            <p class="text-[var(--color-text-muted)] text-sm">Datos de tu formación</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Universidad *</label>
                            <input type="text" name="university" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-secondary)] focus:border-[var(--color-secondary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Universidad Nacional"
                                   value="<?php echo val($info, 'university'); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Programa Académico *</label>
                            <input type="text" name="program" required
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-secondary)] focus:border-[var(--color-secondary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="Ingeniería de Sistemas"
                                   value="<?php echo val($info, 'program'); ?>">
                        </div>

                        <?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante'], [['module' => 'Form', 'action' => 'ver']])): ?>
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Semestre</label>
                            <input type="number" name="semester" min="1" max="20"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-secondary)] focus:border-[var(--color-secondary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="5"
                                   value="<?php echo val($info, 'semester'); ?>">
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Código de Carnet</label>
                            <input type="text" name="card_code"
                                   class="w-full px-3 py-2 border border-[var(--color-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--color-secondary)] focus:border-[var(--color-secondary)] transition-all duration-200 bg-[var(--color-input-bg)] text-[var(--color-input-text)]"
                                   placeholder="202312345"
                                   value="<?php echo val($info, 'card_code'); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit" 
                        class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary)]/90 text-[var(--color-navbar-text)] font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 shadow-sm animate-slideUp" style="animation-delay: 0.4s">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
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