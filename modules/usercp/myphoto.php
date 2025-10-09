<?php
	if (!mconfig('active')) throw new Exception('El módulo de inicio de sesión está deshabilitado.');

	include(__PATH_MODULES__ . '/header.php');

	$logger = new ErrorLogger();
	$uid = $_SESSION['userid'];

	$photoManager = new UserPhotoManager($pdo, __PATH_AVATAR__, __PATH_UPLOADS__);

	try {
		handlePostRequest($uid, $photoManager);
		$currentPhoto = $photoManager->getCurrentAvatar($uid);
		$defaultAvatars = $photoManager->getDefaultAvatars();
	} catch (Throwable $e) {
		$logger->logException($e);
		echo "<h2>Error al procesar tu solicitud</h2>";
		exit;
	}

	renderNav($uid);
	renderPhotoSection($currentPhoto, $defaultAvatars);

	function handlePostRequest(int $uid, UserPhotoManager $photoManager): void {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

		if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
			$success = $photoManager->uploadAvatarFile($_FILES['photo'], $uid);
			if (!$success) {
				throw new Exception('No se pudo subir la imagen: ' . $photoManager->getLastUploadError());
			}
		} elseif (!empty($_POST['default'])) {
			$photoManager->useDefaultAvatar($uid, $_POST['default']);
		} elseif (!empty($_POST['delete'])) {
			$photoManager->deleteCustomAvatar($uid);
		}
	}

	function renderNav(int $uid): void {
		?>
		<div class="mt-8 border-b pb-3 flex flex-wrap gap-4 sm:gap-6 overflow-x-auto sm:overflow-visible">
			<?php if (accessManager()->canAccess($uid, ['estudiante','docente'], [['module' => 'usercp', 'action' => 'projects']])): ?>
				<a href="<?= __BASE_URL__.'usercp/myprojects'; ?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Proyectos</a>
			<?php endif; ?>	
			<?php if (accessManager()->canAccess($_SESSION['userid'], ['estudiante','docente'], [['module' => 'usercp', 'action' => 'results']])): ?>	
				<a href="<?= __BASE_URL__.'usercp/myresults'; ?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Resultados</a>
			<?php endif; ?>
			<a href="<?= __BASE_URL__.'usercp/myaccount'; ?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Perfil</a>
			<a href="<?= __BASE_URL__.'usercp/myphoto'; ?>" class="text-[var(--color-accent)] font-medium border-b-2 border-[var(--color-accent)] pb-1 whitespace-nowrap">Fotografía</a>
			<a href="<?= __BASE_URL__.'usercp/mysecurity'; ?>" class="text-[var(--color-secondary)] hover:text-[var(--color-text)] whitespace-nowrap">Seguridad</a>
		</div>
		<?php
	}

	function renderPhotoSection(?string $currentPhoto, array $defaultAvatars): void {
		?>
		<div class="max-w-6xl mx-auto px-4">
			<div class="grid grid-cols-1 md:grid-cols-12 gap-10 mt-10">

				<!-- Vista previa -->
				<div class="md:col-span-5 grid place-items-center">
					<div class="flex flex-col items-center text-center gap-5">
						<h2 class="text-xl font-semibold">Vista previa</h2>
						<div class="w-64 h-64 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100">
							<?php if ($currentPhoto): ?>
								<img id="preview-avatar" src="<?= htmlspecialchars(__BASE_URL__ . 'uploads/' . $currentPhoto) ?>" alt="Vista previa" class="w-full h-full object-cover">
							<?php else: ?>
								<img id="preview-avatar" src="<?= __PATH_TEMPLATE__ . 'avatars/default.png' ?>" alt="Vista previa" class="w-full h-full object-cover opacity-50">
							<?php endif; ?>
						</div>

						<form method="POST">
							<input type="hidden" name="delete" value="1">
							<button type="submit" class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600">Eliminar</button>
						</form>
					</div>
				</div>

				<div class="md:col-span-7 grid place-items-center">
					<div class="flex flex-col items-center text-center gap-6">

						<form method="POST" enctype="multipart/form-data" class="flex gap-3 items-center w-full max-w-sm justify-center">
							<input type="file" name="photo" accept="image/*" class="border p-2 rounded w-full" onchange="this.form.submit()">
							<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Subir</button>
						</form>
						<div class="w-full max-w-md">
							<h3 class="font-semibold mb-2">Fotos por defecto</h3>
							<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 justify-center" id="avatar-list">
								<?php foreach ($defaultAvatars as $avatar): ?>
									<button type="button" onclick="selectAvatar(this, '<?= htmlspecialchars($avatar) ?>')" class="avatar-btn focus:outline-none">
										<img
											src="<?= __URL_AVATAR__ .'avatars/'.htmlspecialchars($avatar) ?>"
											class="avatar-img w-20 h-20 border rounded transition-all duration-150 hover:scale-105">
									</button>
								<?php endforeach; ?>
							</div>
						</div>
						<form method="POST" id="save-avatar-form" class="mt-4 hidden">
							<input type="hidden" name="default" id="default-avatar-value">
							<button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Guardar avatar seleccionado</button>
						</form>

					</div>
				</div>

			</div>
		</div>

		<script>
		function selectAvatar(button, avatarName) {
			const preview = document.getElementById('preview-avatar');
			const path = "<?= __URL_AVATAR__.'avatars/' ?>";
			preview.src = path + avatarName;

			document.getElementById('default-avatar-value').value = avatarName;
			document.getElementById('save-avatar-form').classList.remove('hidden');

			document.querySelectorAll('.avatar-btn').forEach(btn => {
				btn.classList.remove('ring-4', 'ring-blue-400');
			});
			button.classList.add('ring-4', 'ring-blue-400');
		}

		function reloadAvatar() {
			const avatar = document.getElementById('avatarPreview');
			const src = avatar.src.split('?')[0];
			avatar.src = src + '?t=' + new Date().getTime();
		}
		</script>
		<?php
	}
?>