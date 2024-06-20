<?php
class Auth {
    private $db;
    private $error;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function student($identifier) {
        if (is_null($identifier)) {
            $this->error = "Please enter your student ID or email";
            return false;
        }
    
        $stmt = $this->db->prepare('SELECT id,rol,nombre_completo FROM usuarios WHERE documento_identidad = :identifier AND rol = :role');
        $stmt->execute(['identifier' => $identifier, 'role' => 'Estudiante']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = [];
    
        if($user['rol'] === 'Estudiante'){ 
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['user'] = $user['nombre_completo'];

            ini_set('session.gc_maxlifetime', 86400);
            session_set_cookie_params(86400);

            $data['success']='true';
            $data['message']='Inicio de sesión exitoso. Serás redirigido en unos momentos.';
            $data['id']=$user['id'];
            $data['rol']=$user['rol'];
            $data['nombre_completo']=$user['nombre_completo'];
            return $data;
        }else{
            $data['success']='false';
            $data['message']= $this->error = 'El usuario ingresado no es estudiante';
            return $data;    
        }
    }    

    public function admin($identifier, $password) {
        if (is_null($contrasena)) {
            $stmt = $this->db->prepare('SELECT id,rol,nombre_completo FROM usuarios WHERE documento_identidad = :identifier AND rol = "Evaluador" OR rol = "Coordinador" OR rol = "Administrador"');
            $stmt->execute(['identifier' => $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user['rol'] === 'Evaluador' || $user['rol'] === 'Coordinador' || $user['rol'] === 'Administrador'){
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user'] = $user['nombre_completo'];

                ini_set('session.gc_maxlifetime', 86400);
                session_set_cookie_params(86400);

                $data['success']='true';
                $data['message']='Inicio de sesión exitoso. Serás redirigido en unos momentos.';
                $data['id']=$user['id'];
                $data['rol']=$user['rol'];
                $data['nombre_completo']=$user['nombre_completo'];
                return $data;
            }else{
                $data['success']='false';
                $data['message']= $this->error = 'El usuario ingresado no es administrador';
                return $data; 
            }
    }}

    private function validatePassword($inputPassword, $storedPassword) {
        return password_verify($inputPassword, $storedPassword);
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserRole() {
        return $_SESSION['user_role'];
    }

    public function getError() {
        return $this->error;
    }
}