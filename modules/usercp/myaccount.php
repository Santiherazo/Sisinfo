<?php 
  if (!isLoggedIn()) { redirect(); }
  if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');

  include(__PATH_MODULES__.'/header.php');

  $logger = new ErrorLogger();
  $userId = $_SESSION['userid'];

  $userPermissions = [
      'canAccessProjects' => accessManager()->canAccess($userId, ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']]),
      'canAccessResults' => accessManager()->canAccess($userId, ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']]),
      'isStudent' => accessManager()->canAccess($userId, ['estudiante'], [['module' => 'Form', 'action' => 'ver']])
  ];

  $successMessage = '';
  $errorMessage = '';

  try {
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
              $successMessage = "Cambios guardados correctamente.";
              if (class_exists('Cache')) {
                  Cache::delete("user_info_{$userId}");
              }
          } else {
              $errorMessage = "Ocurrió un error al guardar los cambios.";
          }
      }

      $cacheKey = "user_info_{$userId}";
      $info = [];
      
      if (class_exists('Cache')) {
          $cachedInfo = Cache::get($cacheKey);
          if ($cachedInfo) {
              $info = $cachedInfo;
          }
      }
      
      if (empty($info)) {
          $info = $profileManager->getInfo($userId);
          if (class_exists('Cache') && !empty($info)) {
              Cache::set($cacheKey, $info, 300);
          }
      }

  } catch (Throwable $e) {
      error_log('[Perfil Error] ' . $e->getMessage());
      $info = [];
  }

  function val(array $arr, string $key) {
      return isset($arr[$key]) ? htmlspecialchars($arr[$key]) : '';
  }
?>

<div class="mt-8 border-b pb-3 flex flex-wrap gap-4 sm:gap-6 overflow-x-auto sm:overflow-visible">
  <?php if ($userPermissions['canAccessProjects']): ?>
    <a href="<?php echo __BASE_URL__.'usercp/myprojects';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Proyectos</a>
  <?php endif; ?>
  <?php if ($userPermissions['canAccessResults']): ?>
    <a href="<?php echo __BASE_URL__.'usercp/myresults';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Resultados</a>
  <?php endif; ?>
  <a href="<?php echo __BASE_URL__.'usercp/myaccount';?>" class="text-[var(--color-accent)] font-medium border-b-2 border-[var(--color-accent)] pb-1 whitespace-nowrap">Perfil</a>
  <a href="<?php echo __BASE_URL__.'usercp/myphoto';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Fotografía</a>
  <a href="<?php echo __BASE_URL__.'usercp/mysecurity';?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Seguridad</a>
</div>

<?php if (!empty($successMessage)): ?>
  <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php elseif (!empty($errorMessage)): ?>
  <div class="alert alert-error"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<form method="POST">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">
    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-5">
      <h3 class="text-lg font-semibold mb-4 text-[var(--color-text)]">Datos Básicos</h3>
      <div class="grid grid-cols-1 gap-4">
        <input type="text" placeholder="Primer nombre" name="<?php echo _DETAIL_FIRSTNAME_; ?>" class="input-field" value="<?php echo val($info, 'first_name'); ?>">
        <input type="text" placeholder="Segundo nombre" name="<?php echo _DETAIL_MIDDLENAME_; ?>" class="input-field" value="<?php echo val($info, 'middle_name'); ?>">
        <input type="text" placeholder="Primer apellido" name="<?php echo _DETAIL_LASTNAME_; ?>" class="input-field" value="<?php echo val($info, 'last_name'); ?>">
        <input type="text" placeholder="Segundo apellido" name="<?php echo _DETAIL_LASTNAME2_; ?>" class="input-field" value="<?php echo val($info, 'second_last_name'); ?>">
        <input type="date" placeholder="Fecha de nacimiento" name="<?php echo _DETAIL_BIRTHDATE_; ?>" class="input-field" value="<?php echo val($info, 'birth_date'); ?>">
        <select name="<?php echo _DETAIL_GENDER_; ?>" class="input-field">
          <option value="" <?php echo val($info, 'gender') === '' ? 'selected' : ''; ?>>Género</option>
          <option value="M" <?php echo val($info, 'gender') === 'M' ? 'selected' : ''; ?>>Masculino</option>
          <option value="F" <?php echo val($info, 'gender') === 'F' ? 'selected' : ''; ?>>Femenino</option>
          <option value="O" <?php echo val($info, 'gender') === 'O' ? 'selected' : ''; ?>>Otro</option>
        </select>
        <input type="text" placeholder="Tipo de documento" name="<?php echo _DETAIL_IDTYPE_; ?>" class="input-field" value="<?php echo val($info, 'id_type'); ?>">
        <input type="text" placeholder="Número de documento" name="<?php echo _DETAIL_IDNUM_; ?>" class="input-field" value="<?php echo val($info, 'id_number'); ?>">
      </div>
    </div>

    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-5">
      <h3 class="text-lg font-semibold mb-4 text-[var(--color-text)]">Datos de Contacto</h3>
      <div class="grid grid-cols-1 gap-4">
        <input type="text" placeholder="País" name="<?php echo _DETAIL_COUNTRY_; ?>" class="input-field" value="<?php echo val($info, 'country'); ?>">
        <input type="text" placeholder="Ciudad" name="<?php echo _DETAIL_CITY_; ?>" class="input-field" value="<?php echo val($info, 'city'); ?>">
        <input type="text" placeholder="Dirección" name="<?php echo _DETAIL_ADDRESS_; ?>" class="input-field" value="<?php echo val($info, 'address'); ?>">
        <input type="text" placeholder="Número de contacto" name="<?php echo _DETAIL_PHONE_; ?>" class="input-field" value="<?php echo val($info, 'phone_number'); ?>">
        <input type="email" placeholder="Correo institucional" name="<?php echo _DETAIL_INST_EMAIL_; ?>" class="input-field" value="<?php echo val($info, 'institutional_email'); ?>">
      </div>
    </div>

    <div class="bg-[var(--color-surface)] rounded-xl shadow-md p-5 col-span-1 lg:col-span-2">
      <h3 class="text-lg font-semibold mb-4 text-[var(--color-text)]">Datos Académicos</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" placeholder="Universidad" name="<?php echo _DETAIL_UNIVERSITY_; ?>" class="input-field" value="<?php echo val($info, 'university'); ?>">
        <input type="text" placeholder="Programa académico" name="<?php echo _DETAIL_PROGRAM_; ?>" class="input-field" value="<?php echo val($info, 'program'); ?>">
        <?php if ($userPermissions['isStudent']): ?>
          <input type="number" placeholder="Semestre" name="<?php echo _DETAIL_SEMESTER_; ?>" min="1" class="input-field" value="<?php echo val($info, 'semester'); ?>">
        <?php endif; ?>
        <input type="text" placeholder="Carnet" name="<?php echo _DETAIL_CARD_CODE_; ?>" class="input-field" value="<?php echo val($info, 'card_code'); ?>">
      </div>
    </div>
  </div>

  <div class="flex justify-end mt-6">
    <button type="submit" name="submit" class="btn-save">Guardar cambios</button>
  </div>
</form>