<?php
namespace LorPHP\Core;

class Form {
    private $data = [];
    private $errors = [];
    private $fields = [];
    private $rules = [];
    private $view;
    private $formId;
    private $action = '';
    private $method = 'POST';
    private $enctype = '';
    private $cssClass = 'mt-8 space-y-6';
    private $submitClass = 'w-full flex justify-center py-3 px-4 border border-[#27272a] rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-500 via-purple-500 to-blue-500 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 transform transition-all duration-300 hover:scale-[1.02]';
    private $submitText = 'Submit'; // Added property with default value
    
    private $errorTypes = [
        'required' => 'This field is required',
        'email' => 'Please enter a valid email address',
        'min' => 'This field must be at least %s characters',
        'max' => 'This field must not exceed %s characters',
        'match' => 'This field must match %s',
        'unique' => 'This value is already taken',
        'database' => 'A database error occurred',
        'invalid' => 'Invalid value provided'
    ];
    
    /**
     * Create a new Form instance
     * 
     * @param string $formId Unique identifier for the form
     * @param array $data Initial data for the form
     */
    public function __construct($formId = 'form', $data = []) {
        $this->formId = $formId;
        $this->data = $data;
        $this->view = new View();
        
        // Merge in POST data if this is a POST request for this form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data = array_merge($this->data, $_POST);
        }
    }
    
    /**
     * Set form action URL
     * 
     * @param string $action The URL to submit the form to
     * @return Form
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }
    
    /**
     * Set form method
     * 
     * @param string $method The HTTP method (POST, GET)
     * @return Form
     */
    public function setMethod($method) {
        $this->method = strtoupper($method);
        return $this;
    }
    
    /**
     * Set form CSS class
     * 
     * @param string $class CSS class string
     * @return Form
     */
    public function setCssClass($class) {
        $this->cssClass = $class;
        return $this;
    }
    
    /**
     * Set form encoding type
     * 
     * @param string $enctype The encoding type (e.g., 'multipart/form-data')
     * @return Form
     */
    public function setEnctype($enctype) {
        $this->enctype = $enctype;
        return $this;
    }
    
    /**
     * Add a field to the form
     * 
     * @param string $name Field name
     * @param array $options Field options
     * @return Form
     */
    public function addField($name, array $options = []) {
        $defaults = [
            'id' => $name,
            'type' => 'text',
            'required' => false,
            'class' => '',
            'label' => ucfirst($name),
            'placeholder' => '',
            'value' => $this->getValue($name)
        ];
        
        $this->fields[$name] = array_merge($defaults, $options);
        return $this;
    }
    
    /**
     * Add text input field
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param bool $required Whether field is required
     * @param array $options Additional options
     * @return Form
     */
    public function addText($name, $label, $required = false, array $options = []) {
        return $this->addField($name, array_merge([
            'type' => 'text',
            'label' => $label,
            'required' => $required
        ], $options));
    }
    
    /**
     * Add email input field
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param bool $required Whether field is required
     * @param array $options Additional options
     * @return Form
     */
    public function addEmail($name, $label, $required = false, array $options = []) {
        return $this->addField($name, array_merge([
            'type' => 'email',
            'label' => $label,
            'required' => $required
        ], $options));
    }
    
    /**
     * Add password input field
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param bool $required Whether field is required
     * @param array $options Additional options
     * @return Form
     */
    public function addPassword($name, $label, $required = false, array $options = []) {
        return $this->addField($name, array_merge([
            'type' => 'password',
            'label' => $label,
            'required' => $required
        ], $options));
    }
    
    /**
     * Add textarea field
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param bool $required Whether field is required
     * @param array $options Additional options
     * @return Form
     */
    public function addTextarea($name, $label, $required = false, array $options = []) {
        return $this->addField($name, array_merge([
            'type' => 'textarea',
            'label' => $label,
            'required' => $required,
            'rows' => $options['rows'] ?? 3
        ], $options));
    }

    /**
     * Add validation rule for a field
     * 
     * @param string $field Field name
     * @param string $rule Rule type
     * @param mixed $param Rule parameter
     * @param string $message Error message
     * @return Form
     */
    public function addRule($field, $rule, $param = null, $message = null) {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        
        $this->rules[$field][] = [
            'type' => $rule,
            'param' => $param,
            'message' => $message
        ];
        
        return $this;
    }

    /**
     * Add an error message for a specific field or the form itself.
     *
     * @param string $field The field name or 'form' for a general error.
     * @param string $message The error message.
     * @return Form
     */
    public function addError($field, $message) {
        $this->errors[$field] = $message;
        return $this;
    }

    /**
     * Get the value for a specific field.
     *
     * @param string $name The name of the field.
     * @param mixed $default The default value to return if the field is not set.
     * @return mixed The value of the field or the default value.
     */
    public function getValue($name, $default = null) {
        return $this->data[$name] ?? $default;
    }

    /**
     * Get form data
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Check if form was submitted
     * 
     * @return bool
     */
    public function isSubmitted() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Validate form data against rules
     * 
     * @return bool
     */
    public function validate() {
        $this->errors = [];
        
        // Validate required fields
        foreach ($this->fields as $name => $field) {
            if ($field['required'] && empty($this->data[$name])) {
                $label = $field['label'];
                $this->errors[$name] = "{$label} is required";
            }
        }
        
        // Apply validation rules
        foreach ($this->rules as $field => $rules) {
            if (isset($this->errors[$field])) {
                continue; // Already has an error
            }
            
            $value = $this->getValue($field);
            
            foreach ($rules as $rule) {
                $valid = true;
                
                switch ($rule['type']) {
                    case 'required':
                        $valid = !empty($value);
                        break;
                    case 'email':
                        $valid = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                        break;
                    case 'min_length':
                        $valid = strlen($value) >= $rule['param'];
                        break;
                    case 'max_length':
                        $valid = strlen($value) <= $rule['param'];
                        break;
                    case 'match':
                        $matchField = $rule['param'];
                        $valid = $value === $this->getValue($matchField);
                        break;
                    case 'pattern':
                        $valid = preg_match($rule['param'], $value) === 1;
                        break;
                }
                
                if (!$valid) {
                    $this->errors[$field] = $rule['message'] ?? "Invalid {$field}";
                    break;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * 
     * @return string|null
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Render form opening tag
     * 
     * @return string
     */
    public function open() {
        $html = '<form';
        $html .= ' id="' . htmlspecialchars($this->formId) . '"';
        $html .= ' action="' . htmlspecialchars($this->action) . '"';
        $html .= ' method="' . htmlspecialchars($this->method) . '"';
        
        if ($this->cssClass) {
            $html .= ' class="' . htmlspecialchars($this->cssClass) . '"';
        }
        
        if ($this->enctype) {
            $html .= ' enctype="' . htmlspecialchars($this->enctype) . '"';
        }
        
        $html .= '>';
        return $html;
    }
    
    /**
     * Render form closing tag
     * 
     * @return string
     */
    public function close() {
        return '</form>';
    }
    
    /**
     * Set the submit button CSS class
     * 
     * @param string $class CSS class string
     * @return Form
     */
    public function setSubmitClass($class) {
        $this->submitClass = $class;
        return $this;
    }

    /**
     * Set the submit button text
     * 
     * @param string $text Button text
     * @return Form
     */
    public function setSubmitText($text) {
        $this->submitText = $text;
        return $this;
    }

    private $buttonsContainerClass = 'flex justify-end space-x-3';

    /**
     * Set the class for the buttons container
     * 
     * @param string $class CSS class string
     * @return Form
     */
    public function setButtonsContainerClass($class) {
        $this->buttonsContainerClass = $class;
        return $this;
    }
    
    /**
     * Render a submit button
     * 
     * @param string $text Button text
     * @param string $class CSS class
     * @return string
     */
    public function submit($text = 'Submit', $class = '') {
        $defaultClass = 'group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
        $class = $class ?: $defaultClass;
        
        return sprintf(
            '<button type="submit" class="%s">%s</button>',
            htmlspecialchars($class),
            htmlspecialchars($text)
        );
    }
    
    /**
     * Render a form field
     * 
     * @param string $name Field name
     * @return string
     */
    public function renderField($name) {
        if (!isset($this->fields[$name])) {
            return '';
        }
        
        $field = $this->fields[$name];
        $hasError = isset($this->errors[$name]);
        
        $html = '<div class="mb-6 relative group">';
        if (!empty($field['label'])) {
            $html .= sprintf(
                '<label for="%s" class="block text-sm font-medium text-[#fafafa] mb-2">%s%s</label>',
                htmlspecialchars($field['id']),
                htmlspecialchars($field['label']),
                $field['required'] ? '<span class="text-red-400 ml-1">*</span>' : ''
            );
        }
        
        $html .= '<div class="relative">';
        
        // Render different field types
        if ($field['type'] === 'textarea') {
            $html .= sprintf(
                '<textarea name="%s" id="%s" rows="%d" class="w-full px-4 py-3 rounded-xl %s bg-[#27272a]/50 border %s text-[#fafafa] placeholder-[#71717a] focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-[#6366f1] transition-all duration-300 %s" %s placeholder="%s">%s</textarea>',
                htmlspecialchars($name),
                htmlspecialchars($field['id']),
                intval($field['rows'] ?? 3),
                $hasError ? 'border-red-500/50 focus:border-red-500' : 'border-[#3f3f46]',
                $hasError ? 'ring-2 ring-red-500/10' : '',
                htmlspecialchars($field['class']),
                $field['required'] ? 'required' : '',
                htmlspecialchars($field['placeholder'] ?? ''),
                htmlspecialchars($field['value'] ?? '')
            );
        } else {
            $html .= sprintf(
                '<input type="%s" name="%s" id="%s" class="w-full px-4 py-3 rounded-xl %s bg-[#27272a]/50 border %s text-[#fafafa] placeholder-[#71717a] focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-[#6366f1] transition-all duration-300 %s" %s value="%s" placeholder="%s">',
                htmlspecialchars($field['type']),
                htmlspecialchars($name),
                htmlspecialchars($field['id']),
                $hasError ? 'border-red-500/50 focus:border-red-500' : 'border-[#3f3f46]',
                $hasError ? 'ring-2 ring-red-500/10' : '',
                htmlspecialchars($field['class']),
                $field['required'] ? 'required' : '',
                htmlspecialchars($field['value'] ?? ''),
                htmlspecialchars($field['placeholder'] ?? '')
            );
        }

        // Add gradient hover effect overlay
        $html .= '<div class="absolute inset-0 rounded-xl bg-gradient-to-r from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>';
        
        $html .= '</div>'; // Close relative div

        $html .= '</div>'; // Close form group div
        return $html;
    }
    
    /**
     * Render form errors in a consistent way
     * 
     * @return string HTML for form errors
     */
    public function renderErrors() {
        if (empty($this->errors)) {
            return '';
        }
        
        $output = '';
        
        // Get all errors and combine them into a single message
        $errorMessages = [];
        foreach ($this->errors as $field => $error) {
            $message = is_array($error) ? $error['message'] : $error;
            $errorMessages[] = $message;
        }

        $output .= '<div class="rounded-xl bg-red-500/10 border border-red-500/20 p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-400">' . htmlspecialchars(implode(", ", $errorMessages)) . '</p>
                </div>
            </div>
        </div>';
        
        return $output;
    }

    /**
     * Render the complete form
     * 
     * @return string
     */
    public function render() {
        $html = $this->open();
        
        // Render all errors first
        if (!empty($this->errors)) {
            $html .= $this->renderErrors();
        }

        // Render all fields
        foreach ($this->fields as $name => $_) {
            $html .= $this->renderField($name);
        }
        
        // Render buttons container
        $html .= sprintf(
            '<div class="%s">',
            htmlspecialchars($this->buttonsContainerClass)
        );

        // Add cancel link - only show if we have a non-empty action
        if (!empty($this->action) && $this->action !== '/dashboard') {
            $cancelUrl = dirname($this->action);
            $html .= sprintf(
                '<a href="%s" class="bg-[#27272a]/50 text-[#a1a1aa] px-4 py-2 rounded-xl text-sm border border-[#3f3f46] hover:bg-[#3f3f46]/50 transition-colors duration-200">Cancel</a>',
                htmlspecialchars($cancelUrl)
            );
        }

        // Add submit button
        $html .= sprintf(
            '<button type="submit" class="%s">%s</button>',
            htmlspecialchars($this->submitClass),
            htmlspecialchars($this->submitText)
        );

        $html .= '</div>'; // Close buttons container
        
        $html .= $this->close();
        return $html;
    }
    
    /**
     * Debug logging
     * 
     * @param string $message Log message
     * @param array $context Contextual data
     */
    private function debugLog($message, $context = []) {
        if (defined('DEBUG') && DEBUG) {
            $contextStr = !empty($context) ? " Context: " . print_r($context, true) : "";
            error_log("[Form Debug] " . $message . $contextStr);
            echo "\n<!-- Form Debug: " . htmlspecialchars($message . $contextStr) . " -->\n";
        }
    }

    /**
     * Add an error with specific type and details
     * 
     * @param string $field Field name
     * @param string $type Error type (required, email, min, max, match, unique, database)
     * @param array $context Additional context for the error
     * @return Form
     */
    public function addTypedError($field, $type, $context = []) {
        $message = $this->errorTypes[$type] ?? 'An error occurred';
        if (!empty($context)) {
            $message = vsprintf($message, $context);
        }
        $this->errors[$field] = [
            'type' => $type,
            'message' => $message,
            'context' => $context
        ];
        return $this;
    }
}
