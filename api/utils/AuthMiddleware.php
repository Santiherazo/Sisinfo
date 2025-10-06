<?php
class AuthMiddleware {
    public function validateToken($token) {
        if (empty($token)) {
            return false;
        }
        
        // Aquí puedes agregar lógica adicional de validación de token
        // como verificación de formato, checksum, etc.
        
        return true;
    }
}
?>