<div class="container">
<h2>Registro de Usuario</h2>
    <form action="procesar_registro.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="segundo_nombre">Segundo Nombre:</label>
        <input type="text" id="segundo_nombre" name="segundo_nombre"><br>

        <label for="primer_apellido">Primer Apellido:</label>
        <input type="text" id="primer_apellido" name="primer_apellido" required><br>

        <label for="segundo_apellido">Segundo Apellido:</label>
        <input type="text" id="segundo_apellido" name="segundo_apellido"><br>

        <label for="carnet">Carnet:</label>
        <input type="text" id="carnet" name="carnet" required><br>

        <label for="cedula">Cédula:</label>
        <input type="text" id="cedula" name="cedula" required><br>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="celular">Número de Celular:</label>
        <input type="text" id="celular" name="celular" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <input type="submit" value="Registrarse">
    </form>
</div>