<?php

class Register{
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        echo "Todos los campos son requeridos";
    } else {
        try {
            $sql = "INSERT INTO `usuarios`(`nombre_usuario`, `correo_electronico`, `contrasena`) VALUES (:username, :email, :pass)";
            $consulta = $conexion->prepare($sql);
            $consulta->bindParam(':username', $username);
            $consulta->bindParam(':email', $email);
            $consulta->bindParam(':pass', $password);
            $consulta->execute();

            if ($consulta->rowCount() > 0) {
                echo "Registro exitoso.";
            } else {
                echo "No se pudo insertar el registro";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Acceso denegado";
}
}
?>
