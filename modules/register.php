
<div class="container">
    <h2>Registro</h2>
    <div id="errorMsg"></div>
    <form id="registerForm" method="post">
        <label for="username">Nombre de usuario:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required minlength="8"><br>
        <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
        <input type="submit" value="Registrar">
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#registerForm').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&csrfToken=' + $('#csrfToken').val();
        $.ajax({
            type: 'POST',
            url: 'class.register.php',
            data: formData,
            success: function(response){
                $('#errorMsg').html(response);
            }
        });
    });
});
</script>
