<?php
namespace LorPHP\Core;

use LorPHP\Models\User;

class AuthMiddleware {
    private const ADMIN_ROLE_ID = '5c6eec33-1150-4e0b-bd2d-a8c0f4a8fd8f';

    /**
     * Ensure admin account exists
     */
    private static function ensureAdminExists(): void {
        $app = Application::getInstance();
        $adminConfig = $app->getConfig('admin.admin_account');
        
        if (!$adminConfig) {
            return;
        }

        $db = Database::getInstance();
        $admin = $db->findOne('users', ['email' => $adminConfig['email']]);
        
        if (!$admin) {
            // Create admin organization
            $org = new \LorPHP\Models\Organization();
            $org->name = "System Administration";
            $org->save();

            // Create admin user
            $user = new \LorPHP\Models\User();
            $user->name = $adminConfig['name'];
            $user->email = $adminConfig['email'];
            $user->setPassword($adminConfig['password']);
            $user->role_id = self::ADMIN_ROLE_ID;
            $user->organization_id = $org->id;
            $user->active = 1;
            $user->save();
        } else {
            // Ensure existing admin has the correct role
            if (!isset($admin['role_id']) || $admin['role_id'] !== self::ADMIN_ROLE_ID) {
                $db->update('users', 
                    ['role_id' => self::ADMIN_ROLE_ID, 'active' => 1],
                    ['id' => $admin['id']]
                );
            }
        }
    }

    /**
     * Handle authentication check
     *
     * @return bool
     */
    public static function handle(): bool {
        self::ensureAdminExists();
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
