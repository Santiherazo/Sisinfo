<?php

if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];

  $query = "INSERT INTO `usuarios`(`id`, `nombre_completo`, `nombre_usuario`, `correo_electronico`, `contrasena`, `rol`, `institucion`, `direccion`, `ciudad`, `estado_provincia`, `pais`, `codigo_postal`, `telefono`, `foto_perfil_url`, `fecha_nacimiento`, `genero`, `fecha_registro`) VALUES ('','', '$username','$email','$password','','','','','','','','','','','','')";
  $result = query($conexion, $query);

  header("location: login");

} else {
}

?>