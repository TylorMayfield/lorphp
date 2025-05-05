<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Core\Database;
use LorPHP\Models\User;

/**
 * Controller for handling user registration functionality
 */
class RegisterController extends Controller 
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
     * Display registration page or process registration form submission
     */
    public function index() 
    {
        // Redirect if user is already logged in
        if ($this->app->getState('user')) {
            return $this->redirectTo('/');
        }

        // Load the auth config
        $config = $this->config;
        
        // Initialize form with validation rules from config
        $form = \LorPHP\Core\FormBuilder::createRegistrationForm();
        $form->setSubmitText('Create Account');
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->debugLog("Processing registration form submission");
            
            if ($form->validate()) {
                $data = $form->getData();
                
                try {
                    // Create organization first
                    $organization = new \LorPHP\Models\Organization();
                    $organization->name = $data['name'] . "'s Organization";
                    if (!$organization->save()) {
                        throw new \Exception("Failed to create organization");
                    }
                    
                    // Create user and link to organization
                    $user = new User();
                    $user->organization_id = $organization->id;
                    $user->name = $data['name'];
                    $user->email = $data['email'];
                    $user->setPassword($data['password']);
                    
                    if ($user->save()) {
                        $this->debugLog("User registered successfully: " . $data['email']);
                        $this->app->setState('user', $user);
                        return $this->redirectTo($config['routes']['register_redirect'] ?? '/dashboard');
                    }
                    
                    $form->addError('form', 'Registration failed. Please try again.');
                    
                } catch (\PDOException $e) {
                    // Handle database-specific errors with more detail
                    if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                        $form->addTypedError('email', 'unique');
                        $form->addTypedError('form', 'database', [
                            'It looks like you already have an account. Please try logging in instead.'
                        ]);
                    } else {
                        $form->addTypedError('form', 'database');
                    }
                    $this->debugLog("Database error during registration: " . $e->getMessage());
                    
                } catch (\Exception $e) {
                    $form->addTypedError('form', 'invalid', [
                        'An unexpected error occurred. Our team has been notified.'
                    ]);
                    $this->debugLog("Unexpected error during registration: " . $e->getMessage());
                }
            } else {
                $this->debugLog("Form validation failed");
            }
        }
        
        render:
        // Render the registration page
        return $this->view('register', [
            'title' => 'Create your account - ' . ($config['app_name'] ?? 'LorPHP'),
            'form' => $form
        ]);
    }

    /**
     * Process registration form submission directly from POST
     * Alternative method for form processing without FormBuilder
     */
    private function handleRegistration() 
    {
        // Add debugging information
        error_log("Registration form submitted with data: " . print_r($_POST, true));
        
        // Extract form data
        $formData = [
            'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
            'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
            'password' => isset($_POST['password']) ? $_POST['password'] : '',
            'password_confirm' => isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '',
        ];
        
        // Validate using config rules
        $validationRules = $this->config['validation']['registration'];
        $errors = $this->validateInput($formData, $validationRules);
        
        // If there are errors, return to form with errors and old input
        if (!empty($errors)) {
            error_log("Validation errors: " . implode(', ', $errors));
            $viewName = $this->config['views']['register'];
            $appName = $this->config['app_name'];
            
            return $this->view($viewName, [
                'title' => "Register - {$appName}",
                'error' => implode(', ', $errors),
                'value' => [
                    'name' => $formData['name'],
                    'email' => $formData['email']
                ]
            ]);
        }

        // Check if user with this email already exists
        $db = \LorPHP\Core\Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$formData['email']]);
        $existingUser = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            error_log("User with email {$formData['email']} already exists");
            $viewName = $this->config['views']['register'];
            $appName = $this->config['app_name'];
            
            return $this->view($viewName, [
                'title' => "Register - {$appName}",
                'error' => "A user with this email already exists.",
                'value' => [
                    'name' => $formData['name'],
                    'email' => $formData['email']
                ]
            ]);
        }

        // Create user
        $user = new User();
        $user->name = $formData['name'];
        $user->email = $formData['email'];
        $user->setPassword($formData['password']);

        try {
            if ($user->save()) {
                error_log("User successfully registered: {$formData['email']}");
                $this->app->setState('user', $user);
                return $this->redirectTo($this->config['routes']['register_redirect']);
            } else {
                error_log("Failed to save user: {$formData['email']}");
            }
        } catch (\Exception $e) {
            error_log("Exception during user registration: " . $e->getMessage());
        }

        $viewName = $this->config['views']['register'];
        $appName = $this->config['app_name'];
        $errorMessage = $this->config['messages']['register']['registration_failed'];
        
        return $this->view($viewName, [
            'title' => "Register - {$appName}",
            'error' => $errorMessage,
            'value' => [
                'name' => $formData['name'],
                'email' => $formData['email']
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
