<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Models\User;

/**
 * Main Authentication Controller 
 * 
 * This controller serves as a router to the specific auth-related controllers
 * and handles basic auth operations like logout.
 */
class AuthController extends Controller 
{
    protected $config;
    
    public function __construct() 
    {
        parent::__construct();
        // Use include instead of require_once to avoid returning true on subsequent inclusions
        $configPath = dirname(dirname(__DIR__)) . '/config/auth.php';
        if (file_exists($configPath)) {
            $this->config = include $configPath;
        } else {
            $this->config = [];
            error_log("Auth config file not found: $configPath");
        }
    }
    
    /**
     * Login page route handler - delegates to LoginController
     */
    public function login() 
    {
        $loginController = new LoginController();
        return $loginController->index();
    }

    /**
     * Register page route handler - delegates to RegisterController
     */
    public function register() 
    {
        $registerController = new RegisterController();
        return $registerController->index();
    }    /**
     * Logout action
     */
    public function logout() 
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear session data
        $_SESSION = array();
        session_destroy();

        // Clear JWT token
        setcookie('jwt', '', [
            'expires' => 1,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => isset($_SERVER['HTTPS'])
        ]);

        // Clear application state
        $this->app->setState('user', null);
        
        return $this->redirectTo($this->config['routes']['logout_redirect'] ?? '/');
    }
    
    /**
     * Check if the user is authenticated
     * 
     * @return bool True if the user is authenticated, false otherwise
     */
    public function isAuthenticated(): bool
    {
        return $this->app->getState('user') !== null;
    }
    
    /**
     * Get the currently authenticated user
     * 
     * @return User|null The authenticated user or null if not authenticated
     */
    public function getAuthenticatedUser()
    {
        return $this->app->getState('user');
    }

    /**
     * Hash a password using bcrypt
     * @param string $password The plain text password to hash
     * @return string The hashed password
     */
    public static function hashPassword(string $password): string 
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify if a password matches its hash
     * @param string $password The plain text password to verify
     * @param string $hash The hashed password to check against
     * @return bool True if password matches, false otherwise
     */
    public static function verifyPassword(string $password, string $hash): bool 
    {
        return password_verify($password, $hash);
    }
}
