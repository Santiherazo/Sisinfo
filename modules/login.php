<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = check_value($_POST['username']);
  $password = check_value($_POST['password']);

  $Login = new Login();
  $Login->validateLogin($username, $password);
}
?>
<div class="container">
  <h2>Iniciar Sesión</h2>
  <div id="message"></div>
  <form id="loginForm" method="POST">
    <div class="form-group">
      <label for="username">Usuario:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Iniciar Sesión</button>
  </form>
</div>