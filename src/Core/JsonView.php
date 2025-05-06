<?php
namespace LorPHP\Core;

class JsonView {
    public static function render($data, $statusCode = 200) {
        // Clear any previous output or buffers
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Set appropriate headers
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        // Return JSON encoded data
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}
