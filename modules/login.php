<div class="login-container">
    <h2>Iniciar sesión</h2>
    <form id="loginForm" action="login.php" method="post">
        <div class="form-group">
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username">
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <input type="submit" value="Iniciar sesión">
        </div>
        <p id="response" class="response"></p>
    </form>
</div>


<script>
$(document).ready(function(){
    $('#loginForm').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: 'class.login.php',
            data: formData,
            success: function(response){
                if(response.trim() === 'Inicio de sesión exitoso'){
                    $('#response').text(response).css('color', 'green');
                    window.location.href = 'dashboard.php';
                } else {
                    $('#response').text(response).css('color', 'red');
                }
            },
            error: function(xhr, status, error){
                console.error(xhr.responseText);
                $('#response').text('Error en el servidor, por favor intente de nuevo más tarde').css('color', 'red');
            }
        });
    });
});

</script>