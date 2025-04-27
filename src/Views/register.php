<?php
/**
 * Registration page view
 * A simple, clean registration page that uses the auth-container layout
 */

// Use the base layout
$this->setLayout('base');
?>
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg auth-container">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?php echo htmlspecialchars($title ?? 'Create your account'); ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account?
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>

        <?php if (isset($form)): ?>
            <?php
            // First render any form-level errors
            echo $form->renderErrors();
            // Then render the complete form
            echo $form->render();
            ?>
        <?php else: ?>
            <div class="text-red-600">Error: Registration form not available</div>
        <?php endif; ?>
    </div>
</div>
