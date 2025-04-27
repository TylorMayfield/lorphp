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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }
        
        $viewName = $this->config['views']['login'];
        $appName = $this->config['app_name'];
        
        $result = $this->view($viewName, [
            'title' => "Login - {$appName}",
            'debug' => $this->app->getConfig('debug', false)
        ]);
        
        return $result;
    }

    /**
     * Process login form submission
     */
    private function handleLogin() 
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $validationRules = $this->config['validation']['login'];
        $errors = $this->validateInput($_POST, $validationRules);
        
        if (!empty($errors)) {
            $viewName = $this->config['views']['login'];
            $appName = $this->config['app_name'];
            
            return $this->view($viewName, [
                'title' => "Login - {$appName}",
                'error' => implode(', ', $errors),
                'value' => [
                    'email' => $email
                ]
            ]);
        }

        $user = new User();
        $token = $user->authenticate($email, $password);
        if ($token) {
            // Set JWT token in HTTP-only cookie
            setcookie('jwt', $token, [
                'expires' => time() + (60 * 60 * 24), // 24 hours
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
                'secure' => isset($_SERVER['HTTPS'])
            ]);
            return $this->redirectTo($this->config['routes']['login_redirect']);
        }

        $viewName = $this->config['views']['login'];
        $appName = $this->config['app_name'];
        $errorMessage = $this->config['messages']['login']['invalid_credentials'];
        
        return $this->view($viewName, [
            'title' => "Login - {$appName}",
            'error' => $errorMessage,
            'value' => [
                'email' => $email
            ]
        ]);
    }
    
    /**
     * Validate input data against rules
     * 
     * @param array $data Input data to validate
     * @param array $rules Validation rules from config
     * @return array Array of error messages if validation fails
     */
    private function validateInput(array $data, array $rules): array 
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            // Check required fields
            if (isset($fieldRules['required']) && $fieldRules['required'] && empty($data[$field])) {
                $errors[] = $fieldRules['message'] ?? "$field is required";
                continue;
            }
            
            // Skip further validation if field is empty and not required
            if (empty($data[$field])) {
                continue;
            }
            
            // Validate email format
            if (isset($fieldRules['validate_email']) && $fieldRules['validate_email']) {
                if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = $fieldRules['message'] ?? "Invalid email format";
                }
            }
            
            // Validate minimum length
            if (isset($fieldRules['min_length']) && strlen($data[$field]) < $fieldRules['min_length']) {
                $errors[] = $fieldRules['message'] ?? 
                            "$field must be at least {$fieldRules['min_length']} characters";
            }
            
            // Validate that fields match (for password confirmation)
            if (isset($fieldRules['match']) && $data[$field] !== $data[$fieldRules['match']]) {
                $errors[] = $fieldRules['message'] ?? "$field does not match {$fieldRules['match']}";
            }
        }
        
        return $errors;
    }
}
