<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Models\User;

/**
 * Controller for handling user login functionality
 */
class LoginController extends Controller 
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
     * Generate a JWT token for a user
     * @param array $userData User data to encode in the token
     * @return string The generated JWT token
     */
    protected function generateJWT(User $user): string 
    {
        $config = $this->config['jwt'] ?? [];
        $secret = $config['secret'] ?? $_ENV['JWT_SECRET'] ?? 'your-256-bit-secret';
        $expiration = $config['expiration'] ?? 72; // Default to 72 hours
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload = json_encode([
            'sub' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'org' => $user->getOrganization_id(),
            'iat' => time(),
            'exp' => time() + ($expiration * 60 * 60)
        ]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', 
            $base64UrlHeader . "." . $base64UrlPayload, 
            $secret,
            true
        );
        
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Verify if the provided password matches the stored hash
     * @param string $hashedPassword The stored password hash
     * @param string $password The password to verify
     * @return bool True if password matches, false otherwise
     */
    protected function verifyPassword(string $hashedPassword, string $password): bool
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Display login page or process login form submission
     */
    public function index() 
    {
        // Redirect if user is already logged in
        if ($this->app->getState('user')) {
            return $this->redirectTo('/');
        }

        $form = \LorPHP\Core\FormBuilder::createLoginForm();
        $form->setSubmitText('Sign in');
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($form->validate()) {
                $data = $form->getData();
                  try {
                    $user = User::findByEmail($data['email']);
                    if ($user && AuthController::verifyPassword($data['password'], $user->getPassword())) {
                        // Generate and set JWT token
                        $token = $this->generateJWT($user);
                        setcookie('jwt', $token, [
                            'expires' => time() + (60 * 60 * 72), // 72 hours
                            'path' => '/',
                            'httponly' => true,
                            'samesite' => 'Strict',
                            'secure' => isset($_SERVER['HTTPS'])
                        ]);
                        
                        // Start session and store user ID
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['user_id'] = $user->getId();
                        
                        $this->app->setState('user', $user);
                        return $this->redirectTo($this->config['routes']['login_redirect'] ?? '/dashboard');
                    }
                    
                    // Use form error instead of general error message
                    $errorMessage = $this->config['messages']['login']['invalid_credentials'] ?? 'Invalid email or password.';
                    $form->addError('form', $errorMessage);
                    
                } catch (\Exception $e) {
                    error_log("Login error: " . $e->getMessage());
                    $form->addError('form', 'An error occurred during login. Please try again.');
                }
            }
        }
        
        // Render the login page
        return $this->view('login', [
            'title' => 'Sign in - ' . ($this->config['app_name'] ?? 'LorPHP'),
            'form' => $form
        ]);
    }
}
