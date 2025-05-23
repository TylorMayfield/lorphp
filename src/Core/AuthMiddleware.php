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
            $user->name = $adminConfig['name'];            $user->email = $adminConfig['email'];
            $user->password = \LorPHP\Controllers\AuthController::hashPassword($adminConfig['password']);
            $user->role_id = self::ADMIN_ROLE_ID;
            $user->organization_id = $org->id;
            $user->is_active = 1;
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
     * Validate JWT token
     * @param string $token
     * @return array|false Decoded token payload or false if invalid
     */
    private static function validateToken(string $token) {
        $app = Application::getInstance();
        $secret = $app->getConfig('auth.jwt.secret') ?? $_ENV['JWT_SECRET'] ?? 'your-256-bit-secret';
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verify signature
        $valid = hash_hmac('sha256', 
            $header . "." . $payload, 
            $secret,
            true
        );
        
        $valid = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($valid));
        
        if (!hash_equals($signature, $valid)) {
            return false;
        }
        
        // Decode payload
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
        
        // Check expiration
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }

    /**
     * Handle authentication check
     * @return void
     */
    public static function handle(): void {
        $app = Application::getInstance();
        
        // Check for session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {            $db = Database::getInstance();
            $user = $db->findOne('users', ['id' => $_SESSION['user_id'], 'is_active' => 1]);
            
            if ($user) {
                // Create User model instance and set it in application state
                $userModel = new User();
                $userModel->fill($user);
                $app->setState('user', $userModel);

                // Validate JWT token if present
                if (isset($_COOKIE['jwt'])) {
                    $token = $_COOKIE['jwt'];
                    $jwt = self::validateToken($token);
                    if (!$jwt || $jwt['sub'] !== $user['id']) {
                        self::clearToken();
                        unset($_SESSION['user_id']);
                        $app->setState('user', null);
                        header('Location: /login');
                        exit;
                    }
                }
            } else {
                // Invalid user ID in session, clear it
                unset($_SESSION['user_id']);
                $app->setState('user', null);
                
                // Redirect to login
                if (!in_array($_SERVER['REQUEST_URI'], ['/login', '/register'])) {
                    header('Location: /login');
                    exit;
                }
            }
        } else {
            $app->setState('user', null);
        }

        // Ensure admin exists
        self::ensureAdminExists();
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
