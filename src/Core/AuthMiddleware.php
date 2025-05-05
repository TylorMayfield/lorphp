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
            error_log("[Auth Debug] No JWT token found in cookies");
            return false;
        }
        
        error_log("[Auth Debug] Found JWT token, validating...");
        $payload = User::validateJWT($token);
        if (!$payload) {
            error_log("[Auth Debug] JWT validation failed");
            self::clearToken();
            return false;
        }
        
        error_log("[Auth Debug] JWT validated successfully, checking user in database...");
        error_log("[Auth Debug] Looking for user with ID: " . $payload['sub']);
        
        // Fetch fresh user data from database
        $db = Database::getInstance();
        // First try to find the user without the active check to see if they exist at all
        $inactiveUser = $db->findOne('users', ['id' => $payload['sub']]);
        if (!$inactiveUser) {
            error_log("[Auth Debug] No user found with ID: " . $payload['sub']);
            self::clearToken();
            return false;
        }
        
        error_log("[Auth Debug] Found user in database, checking active status...");
        // Now check with active status
        $userData = $db->findOne('users', ['id' => $payload['sub'], 'active' => 1]);
        
        if (!$userData) {
            if ($inactiveUser) {
                error_log("[Auth Debug] User found but not active. Active status: " . ($inactiveUser['active'] ?? 'null'));
            }
            self::clearToken();
            return false;
        }

        // Create and populate user model
        $user = new User();
        foreach ($userData as $key => $value) {
            error_log("[Auth Debug] Setting user property '$key': " . print_r($value, true));
            $user->__set($key, $value);
        }
        
        error_log("[Auth Debug] Setting user in application state");
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
