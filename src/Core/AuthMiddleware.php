<?php
namespace LorPHP\Core;

use LorPHP\Models\User;

class AuthMiddleware {
    /**
     * Handle authentication check
     *
     * @return bool
     */
    public static function handle(): bool {
        $token = $_COOKIE['jwt'] ?? null;
        
        if (!$token) {
            return false;
        }
        
        $payload = User::validateJWT($token);
        if (!$payload) {
            // Clear invalid token
            setcookie('jwt', '', [
                'expires' => 1,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
                'secure' => isset($_SERVER['HTTPS'])
            ]);
            return false;
        }
        
        return true;
    }
}
