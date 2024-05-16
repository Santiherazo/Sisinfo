<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :username AND contrasena = :password");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                session_start();
                $_SESSION['username'] = 'login';
                $response['success'] = true;
                $response['message'] = "Inicio de sesión exitoso";
                header('location: dashboard');
            } else {
                $response['success'] = false;
                $response['message'] = "Nombre de usuario o contraseña incorrectos";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Por favor, introduzca nombre de usuario y contraseña";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Acceso denegado";
    }
    
    echo json_encode($response);
?>
