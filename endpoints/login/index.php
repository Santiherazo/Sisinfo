<?php 

class Login {
    private $conexion;

    public function __construct($db_connection) {
        $this->conexion = $db_connection;
    }

    public function authenticate($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        // Saneamiento de entradas
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        // Preparamos la consulta
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificamos la contraseña
        if ($user && password_verify($password, $user['contrasena'])) {
            // Iniciar sesión segura
            session_start();
            session_regenerate_id(true); // Prevención de fijación de sesión
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true; // Indicador de sesión iniciada

            return true;
        } else {
            return false;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'conexion.php'; // Asegúrate de que este archivo incluya la variable $conexion con la conexión a la base de datos.

    $login = new Login($conexion);
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAuthenticated = $login->authenticate($username, $password);

    echo json_encode(['success' => $isAuthenticated]);
} else {
    json_encode(['success' => false]);
}
?>