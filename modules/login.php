<div class="container">
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
        $('#loginForm').on('submit', function(e){
            e.preventDefault();
            
            // Saneamiento de entradas
            let username = $('#username').val().trim();
            let password = $('#password').val().trim();
            
            if (username === "" || password === "") {
                $('#response').text('Por favor, introduzca nombre de usuario y contraseña').css('color', 'red');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: 'class.login.php',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        $('#response').text('Inicio de sesión exitoso').css('color', 'green');
                        setTimeout(() => {
                            //window.location.href = 'dashboard';
                        }, 1000);
                    } else {
                        $('#response').text('Nombre de usuario o contraseña incorrectos').css('color', 'red');
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
    </script>