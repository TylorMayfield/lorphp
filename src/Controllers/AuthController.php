<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Models\User;

class AuthController extends Controller 
{
    public function __construct() 
    {
        parent::__construct();
    }    public function login() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }
        $result = $this->view('login', [
            'title' => 'Login - LorPHP',
            'debug' => true
        ]);
        var_dump($result);
        return $result;
    }

    public function register() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleRegistration();
        }
        return $this->view('register', [
            'title' => 'Register - LorPHP'
        ]);
    }

    public function logout() 
    {
        $this->app->setState('user', null);
        return $this->redirectTo('/');
    }

    private function handleLogin() 
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return $this->view('login', [
                'title' => 'Login - LorPHP',
                'error' => 'Please fill in all fields'
            ]);
        }

        $user = new User();
        if ($user->authenticate($email, $password)) {
            $this->app->setState('user', $user);
            return $this->redirectTo('/dashboard');
        }

        return $this->view('login', [
            'title' => 'Login - LorPHP',
            'error' => 'Invalid credentials'
        ]);
    }    private function handleRegistration() 
    {
        // Debug the incoming data
        error_log("Raw POST data: " . print_r($_POST, true));
        
        // Get data from form, now using the new nested structure
        $name = isset($_POST['field']['name']) ? trim($_POST['field']['name']) : '';
        $email = isset($_POST['field']['email']) ? trim($_POST['field']['email']) : '';
        $password = isset($_POST['field']['password']) ? $_POST['field']['password'] : '';
        $password_confirm = isset($_POST['field']['password_confirm']) ? $_POST['field']['password_confirm'] : '';
        
        error_log("Processed form data:");
        error_log("name: " . $name);
        error_log("email: " . $email);
        error_log("password length: " . strlen($password));
        error_log("password_confirm length: " . strlen($password_confirm));
        
        $errors = [];
        
        // Validate each field
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        if (empty($password_confirm)) {
            $errors[] = 'Password confirmation is required';
        }
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
        
        // If there are errors, return to form with errors and old input
        if (!empty($errors)) {
            error_log("Validation errors: " . implode(', ', $errors));
            return $this->view('register', [
                'title' => 'Register - LorPHP',
                'error' => implode(', ', $errors),
                'value' => [
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }

        // Create user
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->setPassword($password);

        if ($user->save()) {
            $this->app->setState('user', $user);
            return $this->redirectTo('/dashboard');
        }

        return $this->view('register', [
            'title' => 'Register - LorPHP',
            'error' => 'Registration failed. Please try again.',
            'value' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
    }
}
