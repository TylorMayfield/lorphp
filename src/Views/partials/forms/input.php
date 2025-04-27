<?php
/**
 * Form input field partial
 * 
 * Parameters:
 * - id: Field ID and name
 * - type: Input type (text, email, password, etc.)
 * - label: Field label text
 * - value: Current value
 * - required: Whether the field is required
 * - class: Additional CSS classes (string or array)
 * - placeholder: Placeholder text (optional)
 * - error: Error message if validation failed
 */

// Extract and sanitize variables with proper type handling
$id = isset($id) ? (string)$id : '';
$type = isset($type) ? (string)$type : 'text';
$label = isset($label) ? (string)$label : '';
$value = isset($value) && !is_array($value) ? (string)$value : '';
$required = isset($required) ? (bool)$required : false;
$placeholder = isset($placeholder) ? (string)$placeholder : '';

// Handle class attribute safely
$classString = '';
if (isset($class)) {
    if (is_array($class)) {
        $classArray = array_filter(array_map(function($item) {
            return is_scalar($item) ? (string)$item : '';
        }, $class));
        $classString = implode(' ', $classArray);
    } else {
        $classString = is_scalar($class) ? (string)$class : '';
    }
}

// Default CSS classes
$defaultClasses = "appearance-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm";
$borderClass = isset($error) ? 'border-red-300' : 'border-gray-300';

// Basic validation
if (empty($id)) {
    error_log("Warning: Input field missing ID");
}
?>
<div class="mb-4">
    <?php if (!empty($label)): ?>
        <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" class="block text-sm font-medium text-gray-700 mb-1">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            <?php if ($required): ?>
                <span class="text-red-500">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <div class="relative">
        <input 
            type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
            id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
            name="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
            value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"
            <?= $required ? 'required' : '' ?>
            <?= !empty($placeholder) ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            class="<?= htmlspecialchars($defaultClasses . ' ' . $borderClass . ' ' . $classString, ENT_QUOTES, 'UTF-8') ?>"
        >
        <?php if (isset($error)): ?>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        <?php endif; ?>
    </div>
    <?php if (isset($error)): ?>
        <p class="mt-2 text-sm text-red-600">
            <?= htmlspecialchars(is_scalar($error) ? (string)$error : '', ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>
</div>
