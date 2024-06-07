<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <div class="flex justify-center mb-4">
            <img src="../assets/img/logo.png" alt="Logo" class="h-20 w-20">
            <img src="../assets/img/logo2.png" alt="Logo" class="h-20 w-20">
        </div>
        <h2 class="text-2xl font-bold text-center mb-4">Bienvenido</h2>
        <p class="text-center mb-6">por favor, inicia sesión</p>
        <?php if (!empty($error_message)): ?>
            <p class="text-center mb-4 text-red-500"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="index.php?page=login" method="POST">
            <div class="mb-4">
                <label for="documento_identidad" class="block text-sm font-medium text-gray-700">Documento de Identidad</label>
                <input type="text" id="documento_identidad" name="documento_identidad" class="input-field mt-1" placeholder="Enter your document ID">
            </div>
            <div class="mb-4">
                <label for="contrasena" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="contrasena" name="contrasena" class="input-field mt-1" placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="w-full btn-primary">Sign in</button>
        </form>
    </div>
</body>
</html>