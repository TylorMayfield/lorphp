<?php
/**
 * Register form partial
 */

error_log("=== Register Form Start ===");

// Handle view instance
if (!isset($this) || !($this instanceof \LorPHP\Core\View)) {
    throw new \Exception('Register form partial must be rendered through View class');
}

// Initialize values
$values = $values ?? [];
error_log("Form values: " . print_r($values, true));
?>
<form class="mt-8 space-y-6" action="/register" method="POST">
    <div class="space-y-4">
        <?php
        error_log("Rendering form fields");
        // Name field
        echo $this->renderPartialToString('forms/input', [
            'id' => 'name',
            'type' => 'text',
            'label' => 'Full Name',
            'required' => true,
            'class' => 'rounded-t-md',
            'value' => $values['name'] ?? ''
        ]);

        // Email field
        echo $this->renderPartialToString('forms/input', [
            'id' => 'email',
            'type' => 'email',
            'label' => 'Email address',
            'required' => true,
            'value' => $values['email'] ?? ''
        ]);

        // Password field
        echo $this->renderPartialToString('forms/input', [
            'id' => 'password',
            'type' => 'password',
            'label' => 'Password',
            'required' => true
        ]);

        // Password confirmation field
        echo $this->renderPartialToString('forms/input', [
            'id' => 'password_confirm',
            'type' => 'password',
            'label' => 'Confirm Password',
            'required' => true,
            'class' => 'rounded-b-md'
        ]);
        ?>
    </div>

    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Create Account
    </button>
</form>
