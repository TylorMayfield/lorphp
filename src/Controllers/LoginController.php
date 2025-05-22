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
                    
                    if ($user && $user->verifyPassword($data['password'])) {
                        // Generate and set JWT token
                        $token = $user->generateJWT();
                        setcookie('jwt', $token, [
                            'expires' => time() + (60 * 60 * 72), // 72 hours
                            'path' => '/',
                            'httponly' => true,
                            'samesite' => 'Strict',
                            'secure' => isset($_SERVER['HTTPS'])
                        ]);
                        
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
