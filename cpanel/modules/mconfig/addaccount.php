<?php
//$roleManager = new RoleManager();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'User', 'action' => 'create']])) {
        die('No tienes permisos para acceder a este módulo.');
    }
?>

<form method="post" enctype="multipart/form-data" class="max-w-7xl mx-auto mt-6 mb-10 space-y-6">
    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Datos personales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    formInput('text', 'first_name', 'Primer Nombre', true);
                    formInput('text', 'middle_name', 'Segundo Nombre');
                    formInput('text', 'last_name', 'Primer Apellido', true);
                    formInput('text', 'second_last_name', 'Segundo Apellido');
                    formInput('date', 'birth_date', 'Fecha de nacimiento');
                    formSelect('gender', 'Género', ['' => '-- Seleccione --', 'M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro']);
                    formSelect('id_type', 'Tipo de documento', ['' => '-- Seleccione --','CC' => 'Cédula de ciudadanía', 'TI' => 'Tarjeta de identidad'], true);
                    formInput('text', 'id_number', 'Número de identificación', true);
                    formInput('text', 'country', 'País');
                    formInput('text', 'city', 'Ciudad');
                    formInput('text', 'address', 'Dirección');
                    ?>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Contraseña</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    formInput('password', 'password', 'Contraseña', false);
                    formInput('password', 'confirm_password', 'Confirmar contraseña', false);
                    ?>
                </div>

                <div class="mt-6">
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Foto de perfil</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none p-2 bg-white"/>

                    <div class="mt-4">
                        <img id="profile_preview" src="" alt="Vista previa" class="w-32 h-32 object-cover rounded-full border border-gray-300 shadow"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Datos de contacto</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    formInput('text', 'email', 'Correo electrónico', true);
                    formInput('text', 'phone_number', 'Celular');
                    formInput('text', 'institutional_email', 'Correo institucional');
                    ?>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Datos académicos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    formInput('text', 'program', 'Programa');
                    formSelect('semester', 'Semestre', [
                        '' => '-- Seleccione --',
                        '1' => 'Primer Semestre', '2' => 'Segundo Semestre', '3' => 'Tercer Semestre',
                        '4' => 'Cuarto Semestre', '5' => 'Quinto Semestre', '6' => 'Sexto Semestre',
                        '7' => 'Séptimo Semestre', '8' => 'Octavo Semestre', '9' => 'Noveno Semestre',
                        '10' => 'Décimo Semestre'
                    ]);
                    formInput('text', 'university', 'Institución');
                    formInput('text', 'card_code', 'Carnet');
                    ?>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Rol del usuario</h2>
                <div class="grid grid-cols-1">
                    <?php formSelect('role', 'Rol', $rolesOptions, true); ?>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" name="webengineRegister_submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-lg">
                    Crear cuenta
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('profile_picture').addEventListener('change', function (event) {
    const input = event.target;
    const preview = document.getElementById('profile_preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "";
    }
});
</script>

<?php
/*} catch (Exception $ex) {
    echo '<div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
    echo '<strong>Error:</strong> ' . htmlspecialchars($ex->getMessage());
    echo '</div>';
}*/
?>