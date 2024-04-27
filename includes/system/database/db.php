<?php
class ConexionDB {
    private $host = "localhost";
    private $usuario = "tu_usuario";
    private $contrasena = "tu_contraseña";
    private $nombre_bd = "nombre_de_tu_base_de_datos";
    private $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->nombre_bd};charset=utf8mb4";
            $opciones = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            );
            $this->conexion = new PDO($dsn, $this->usuario, $this->contrasena, $opciones);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            die();
        }
    }

    public function obtenerConexion() {
        return $this->conexion;
    }
}

// Ejemplo de uso:
$conexionDB = new ConexionDB();
$conexion = $conexionDB->obtenerConexion();

// Ahora puedes utilizar $conexion para ejecutar consultas SQL de forma segura.
?>
