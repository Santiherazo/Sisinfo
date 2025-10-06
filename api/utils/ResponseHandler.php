<?php
class ResponseHandler {
    public static function sendSuccess($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        exit();
    }
    
    public static function sendError($message, $statusCode = 500) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit();
    }
}
?>