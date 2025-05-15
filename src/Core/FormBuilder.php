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
            'class' => 'mb-2',
            'placeholder' => 'you@example.com'
        ])
        ->addPassword('password', 'Password', true, [
            'class' => '',
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
        $inputClass = '';
        
        // Add fields with enhanced styling
        $form->addText('name', 'Full Name', true, [
            'class' => $inputClass . ' mb-2',
            'placeholder' => 'John Doe'
        ])
        ->addEmail('email', 'Email address', true, [
            'class' => $inputClass . ' mb-2',
            'placeholder' => 'you@example.com'
        ])
        ->addPassword('password', 'Password', true, [
            'class' => $inputClass . ' mb-2',
            'placeholder' => '••••••••'
        ])
        ->addPassword('password_confirm', 'Confirm Password', true, [
            'class' => $inputClass . ' mb-2',
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
     * Create a client form
     * 
     * @param array $data Initial form data
     * @param string $action Form action URL
     * @return Form
     */
    public static function createClientForm($data = [], $action = '/clients') {
        $form = new Form('client-form', $data);
        $form->setAction($action);
        
        // Common input classes for dark theme styling
        $inputClass = 'bg-[#27272a]/50 border-[#3f3f46] text-[#fafafa] rounded-xl focus:border-indigo-400 focus:ring-indigo-400 hover:bg-[#27272a]/70 transition-colors duration-200';
        
        // Add fields with enhanced styling
        $form->addText('name', 'Client Name', true, [
            'class' => $inputClass . ' mb-4',
            'placeholder' => 'Acme Corporation'
        ])
        ->addEmail('email', 'Email Address', false, [
            'class' => $inputClass . ' mb-4',
            'placeholder' => 'contact@acme.com'
        ])
        ->addText('phone', 'Phone Number', false, [
            'class' => $inputClass . ' mb-4',
            'placeholder' => '(555) 555-5555'
        ])
        ->addTextarea('notes', 'Notes', false, [
            'class' => $inputClass . ' mb-6',
            'placeholder' => 'Any additional notes about this client...',
            'rows' => 3
        ]);
        
        // Add validation rules
        $form->addRule('name', 'required', null, 'Client name is required')
             ->addRule('name', 'min_length', 2, 'Client name must be at least 2 characters')
             ->addRule('email', 'email', null, 'Please enter a valid email address')
             ->addRule('phone', 'pattern', '/^[\d\s\(\)\-\+]*$/', 'Please enter a valid phone number');
        
        // Set submit button with modern gradient styling
        $form->setSubmitText('Create Client')
             ->setSubmitClass('bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl')
             ->setButtonsContainerClass('flex justify-end space-x-3');
        
        return $form;
    }
    
    /**
     * Create a package form
     * 
     * @param array $data Initial form data
     * @param string $action Form action URL
     * @return Form
     */
    public static function createPackageForm($data = [], $action = '/packages') {
        $form = new Form('package-form', $data);
        $form->setAction($action);
        
        // Common input classes for dark theme styling
        $inputClass = 'bg-[#27272a]/50 border-[#3f3f46] text-[#fafafa] rounded-xl focus:border-indigo-400 focus:ring-indigo-400 hover:bg-[#27272a]/70 transition-colors duration-200';
        
        // Add fields with enhanced styling
        $form->addText('name', 'Package Name', true, [
            'class' => $inputClass . ' mb-4',
            'placeholder' => 'Premium Package'
        ])
        ->addTextarea('description', 'Description', false, [
            'class' => $inputClass . ' mb-4',
            'placeholder' => 'Describe what this package includes...',
            'rows' => 3
        ])
        ->addText('price', 'Price', true, [
            'class' => $inputClass . ' mb-4',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'placeholder' => '0.00'
        ]);
        
        // Add validation rules
        $form->addRule('name', 'required', null, 'Package name is required')
             ->addRule('name', 'min_length', 2, 'Package name must be at least 2 characters')
             ->addRule('price', 'required', null, 'Price is required')
             ->addRule('price', 'min', 0, 'Price must be greater than or equal to 0');
        
        // Set submit button with modern gradient styling
        $form->setSubmitText('Create Package')
             ->setSubmitClass('bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl')
             ->setButtonsContainerClass('flex justify-end space-x-3');
        
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
