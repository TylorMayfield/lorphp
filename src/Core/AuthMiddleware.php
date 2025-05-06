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
            self::clearToken();
            return false;
        }
        
        // Fetch fresh user data from database
        $db = Database::getInstance();
        // First try to find the user without the active check to see if they exist at all
        $inactiveUser = $db->findOne('users', ['id' => $payload['sub']]);
        if (!$inactiveUser) {
            self::clearToken();
            return false;
        }
        // Now check with active status
        $userData = $db->findOne('users', ['id' => $payload['sub'], 'active' => 1]);
        
        if (!$userData) {
            self::clearToken();
            return false;
        }

        // Create and populate user model
        $user = new User();
        foreach ($userData as $key => $value) {
            $user->__set($key, $value);
        }
        
        // Set the user in the application state
        $app = Application::getInstance();
        $app->setState('user', $user);
        
        return true;
    }
    
    /**
     * Clear the JWT token cookie
     */
    private static function clearToken(): void {
        setcookie('jwt', '', [
            'expires' => 1,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => isset($_SERVER['HTTPS'])
        ]);
    }
}
