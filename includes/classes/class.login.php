<?php
session_start();

$response = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Aquí deberías realizar una consulta a tu base de datos para verificar las credenciales
        // Por ahora, solo compararemos con valores fijos
        if ($username === 'usuario' && $password === 'contraseña') {
            $_SESSION['username'] = $username;
            $response = "Inicio de sesión exitoso";
        } else {
            $response = "Nombre de usuario o contraseña incorrectos";
        }
    } else {
        $response = "Por favor, introduzca nombre de usuario y contraseña";
    }
} else {
    $response = "Acceso denegado";
}

echo $response;
?>
