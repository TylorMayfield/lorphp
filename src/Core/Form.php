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
     * Set submit button text
     * 
     * @param string $text The text to display on the submit button
     * @return Form
     */
    public function setSubmitText($text) {
        $this->submitText = $text;
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
            $this->debugLog("Field not found: {$name}");
            return '';
        }
        
        $field = $this->fields[$name];
        $error = $this->errors[$name] ?? null;
        
        // Add error to field data if present
        if ($error) {
            $field['error'] = $error;
        }
        
        try {
            $html = $this->view->renderPartialToString('forms/input', $field);
            $this->debugLog("Field rendered successfully", [
                'name' => $name, 
                'length' => strlen($html)
            ]);
            return $html;
        } catch (\Throwable $e) {
            $this->debugLog("Field render error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Render the complete form
     * 
     * @return string
     */
    public function render() {
        try {
            ob_start();
            
            echo $this->open();
            $this->debugLog("Form opened");
            
            // Render global form errors if any
            if (!empty($this->errors['form'])) {
                echo '<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
                echo '<span class="block sm:inline">' . htmlspecialchars($this->errors['form']) . '</span>';
                echo '</div>';
            }
            
            // Wrap fields in a container
            echo '<div class="space-y-4">';
            
            // Render each field directly
            foreach ($this->fields as $name => $field) {
                $this->renderFieldDirect($name);
                $this->debugLog("Field rendered", ['name' => $name]);
            }
            
            echo '</div>';
            
            // Add submit button using the submitText property
            echo $this->submit($this->submitText);
            
            // Close the form
            echo $this->close();
            
            $html = ob_get_clean();
            $this->debugLog("Form rendered successfully", ['length' => strlen($html)]);
            
            return $html;
        } catch (\Throwable $e) {
            ob_end_clean();
            $this->debugLog("Form render error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Render a field directly to output
     */
    private function renderFieldDirect($name) {
        if (!isset($this->fields[$name])) {
            $this->debugLog("Field not found: {$name}");
            return;
        }
        
        $field = $this->fields[$name];
        $error = $this->errors[$name] ?? null;
        
        echo '<div class="mb-4">';
        if (!empty($field['label'])) {
            echo '<label for="' . htmlspecialchars($field['id']) . '" class="block text-sm font-medium text-gray-700 mb-1">';
            echo htmlspecialchars($field['label']);
            if ($field['required']) {
                echo '<span class="text-red-500">*</span>';
            }
            echo '</label>';
        }
        
        echo '<div class="relative">';
        echo '<input';
        
        $attrs = [
            'type' => $field['type'],
            'id' => $field['id'],
            'name' => $field['id'],
            'value' => $field['value'] ?? '',
            'class' => 'appearance-none relative block w-full px-3 py-2 border ' . 
                      ($error ? 'border-red-300' : 'border-gray-300') . 
                      ' placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 ' .
                      'focus:border-indigo-500 focus:z-10 sm:text-sm ' . ($field['class'] ?? '')
        ];
        
        if ($field['required']) {
            $attrs['required'] = 'required';
        }
        
        if (!empty($field['placeholder'])) {
            $attrs['placeholder'] = $field['placeholder'];
        }
        
        foreach ($attrs as $key => $value) {
            if ($value === 'required') {
                echo " $key";
            } else {
                echo ' ' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        echo '>';
        
        if ($error) {
            echo '<div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">';
            echo '<svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">';
            echo '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />';
            echo '</svg></div>';
            echo '<p class="mt-1 text-sm text-red-600">' . htmlspecialchars($error) . '</p>';
        }
        
        echo '</div></div>';
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
        
        // First, render any form-level errors
        if (isset($this->errors['form'])) {
            $error = $this->errors['form'];
            $output .= $this->view->renderPartial('partials/components/error-alert', [
                'message' => is_array($error) ? $error['message'] : $error,
                'type' => 'error'
            ]);
        }
        
        // Then, render field-specific errors
        $fieldErrors = array_filter($this->errors, function($key) {
            return $key !== 'form';
        }, ARRAY_FILTER_USE_KEY);
        
        if (!empty($fieldErrors)) {
            $details = array_map(function($error, $field) {
                $message = is_array($error) ? $error['message'] : $error;
                return "$message";
            }, $fieldErrors, array_keys($fieldErrors));
            
            $output .= $this->view->renderPartial('partials/components/error-alert', [
                'message' => 'Please correct the following errors:',
                'type' => 'error',
                'details' => $details
            ]);
        }
        
        return $output;
    }
}
