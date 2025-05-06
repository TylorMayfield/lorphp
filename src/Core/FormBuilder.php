<?php
namespace LorPHP\Core;

/**
 * FormBuilder class for creating common forms
 */
class FormBuilder {
    /**
     * Create a login form
     * 
     * @param array $data Initial form data
     * @param string $action Form action URL
     * @return Form
     */
    public static function createLoginForm($data = [], $action = '/login') {
        $form = new Form('login-form', $data);
        $form->setAction($action);
        
        // Add fields with enhanced styling
        $form->addEmail('email', 'Email address', true, [
            'class' => 'w-full px-4 py-3 rounded-lg bg-white/70 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors mb-3',
            'placeholder' => 'you@example.com'
        ])
        ->addPassword('password', 'Password', true, [
            'class' => 'w-full px-4 py-3 rounded-lg bg-white/70 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors',
            'placeholder' => '••••••••'
        ]);
        
        // Add validation rules
        $form->addRule('email', 'required', null, 'Email is required')
             ->addRule('email', 'email', null, 'Please enter a valid email address')
             ->addRule('password', 'required', null, 'Password is required');
        
        // Set submit button text and styling
        $form->setSubmitText('Sign in to your account');
        
        return $form;
    }
    
    /**
     * Create a login form with standard fields and validation
     * 
     * @param array $data Initial form data
     * @param string $action Form action URL
     * @return Form
     */
    public static function createStandardLoginForm($data = [], $action = '/login') {
        $form = new Form('login-form', $data);
        $form->setAction($action);
        
        // Add fields with Tailwind styling
        $form->addEmail('email', 'Email address', true, ['class' => 'rounded-t-md'])
             ->addPassword('password', 'Password', true, ['class' => 'rounded-b-md']);
        
        // Add validation rules
        $form->addRule('email', 'required', null, 'Email is required')
             ->addRule('email', 'email', null, 'Please enter a valid email address')
             ->addRule('password', 'required', null, 'Password is required');
             
        return $form;
    }
    
    /**
     * Create a registration form
     * 
     * @param array $data Initial form data
     * @param string $action Form action URL
     * @return Form
     */
    public static function createRegistrationForm($data = [], $action = '/register') {
        $form = new Form('register-form', $data);
        $form->setAction($action);
        
        // Common input classes
        $inputClass = 'w-full px-4 py-3 rounded-lg bg-white/70 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors';
        
        // Add fields with enhanced styling
        $form->addText('name', 'Full Name', true, [
            'class' => $inputClass . ' mb-3',
            'placeholder' => 'John Doe'
        ])
        ->addEmail('email', 'Email address', true, [
            'class' => $inputClass . ' mb-3',
            'placeholder' => 'you@example.com'
        ])
        ->addPassword('password', 'Password', true, [
            'class' => $inputClass . ' mb-3',
            'placeholder' => '••••••••'
        ])
        ->addPassword('password_confirm', 'Confirm Password', true, [
            'class' => $inputClass,
            'placeholder' => '••••••••'
        ]);
        
        // Add validation rules
        $form->addRule('name', 'required', null, 'Name is required')
             ->addRule('email', 'required', null, 'Email is required')
             ->addRule('email', 'email', null, 'Please enter a valid email address')
             ->addRule('password', 'required', null, 'Password is required')
             ->addRule('password', 'min_length', 6, 'Password must be at least 6 characters')
             ->addRule('password_confirm', 'required', null, 'Password confirmation is required')
             ->addRule('password_confirm', 'match', 'password', 'Passwords do not match');
        
        // Set submit button text
        $form->setSubmitText('Create your account');
        
        return $form;
    }
    
    /**
     * Load form rules from config
     * 
     * @param Form $form Form to add rules to
     * @param array $config Configuration array
     * @param string $section Config section name
     * @return Form
     */
    public static function applyConfigRules(Form $form, array $config, $section) {
        if (!isset($config['validation'][$section])) {
            return $form;
        }
        
        $rules = $config['validation'][$section];
        
        foreach ($rules as $field => $fieldRules) {
            if (isset($fieldRules['required']) && $fieldRules['required']) {
                $form->addRule($field, 'required', null, $fieldRules['message'] ?? "$field is required");
            }
            
            if (isset($fieldRules['validate_email']) && $fieldRules['validate_email']) {
                $form->addRule($field, 'email', null, $fieldRules['message'] ?? "Invalid email format");
            }
            
            if (isset($fieldRules['min_length'])) {
                $form->addRule($field, 'min_length', $fieldRules['min_length'], 
                    $fieldRules['message'] ?? "$field must be at least {$fieldRules['min_length']} characters");
            }
            
            if (isset($fieldRules['match'])) {
                $form->addRule($field, 'match', $fieldRules['match'], 
                    $fieldRules['message'] ?? "$field does not match {$fieldRules['match']}");
            }
        }
        
        return $form;
    }
}
