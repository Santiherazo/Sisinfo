<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Autenticación y permisos
$userId = $_SESSION['userid'] ?? null;

if (!$userId || !accessManager()->canAccess($userId,['administrador', 'coordinador'],[['module' => 'Project', 'action' => 'create']])){
    die('No tienes permisos para acceder a este módulo.');
}

?>

<!-- Formulario HTML -->
<form method="post" enctype="multipart/form-data" class="max-w-5xl mx-auto mt-10 space-y-10">

    <!-- Información del proyecto -->
    <section class="bg-white shadow rounded-lg p-6 space-y-5">
        <h2 class="text-xl font-semibold border-b pb-2">Información del Proyecto</h2>
        <?php
            formInput('text', 'titulo', 'Título del proyecto', true);
            formTextarea('descripcion', 'Descripción', '', true);
            formInput('text', 'palabras_clave', 'Palabras clave (separadas por comas)');
        ?>
    </section>

    <!-- Detalles técnicos -->
    <section class="bg-white shadow rounded-lg p-6 space-y-5">
        <h2 class="text-xl font-semibold border-b pb-2">Detalles Técnicos</h2>
        <?php
            formSelect('fase', 'Fase del proyecto', [
                '' => '-- Seleccione --',
                'Propuesta' => 'Propuesta',
                'Desarrollo' => 'Desarrollo',
                'Aplicacion' => 'Aplicación'
            ], true);

            formSelect('linea', 'Línea de investigación', [
                '' => '-- Seleccione --',
                'Ingeniería del Software' => 'Ingeniería del Software',
                'Gestión de la Seguridad Informática' => 'Gestión de la Seguridad Informática',
                'Redes y Telemática' => 'Redes y Telemática',
                'Ingeniería del conocimiento' => 'Ingeniería del conocimiento',
                'Robótica' => 'Robótica'
            ], true);

            formInput('date', 'fecha_inicio', 'Fecha de inicio', true);
            formInput('time', 'hora', 'Hora programada', true);
            formInput('number', 'timer', 'Tiempo de evaluación (min)', true);
        ?>
    </section>

    <!-- Participantes -->
    <section class="bg-white shadow rounded-lg p-6 space-y-5">
        <h2 class="text-xl font-semibold border-b pb-2">Participantes</h2>
        <?php
            foreach ($roleUserOptions as $name => $config) {
                formSelect($name, $config['label'], $config['options'], true, true);
            }
        ?>
    </section>

    <!-- Archivo PDF -->
    <section class="bg-white shadow rounded-lg p-6 space-y-3">
        <label for="archivo_pdf" class="block text-sm font-medium text-gray-700">Archivo PDF</label>
        <input
            type="file"
            name="archivo_pdf"
            id="archivo_pdf"
            accept="application/pdf"
            class="mt-1 block w-full border rounded p-2 border-gray-300 shadow-sm focus:ring focus:ring-blue-300"
        />
    </section>

    <!-- Botón de envío -->
    <div class="text-center">
        <button
            type="submit"
            name="webengineProject_submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-lg transition"
        >
            Registrar Proyecto
        </button>
    </div>
</form>