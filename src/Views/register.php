<?php
/**
 * Registration page view
 * A simple, clean registration page that uses the auth-container layout
 */

// Use the base layout
$this->setLayout('base');
?>
<div class="min-h-screen flex items-center justify-center py-8">
    <div class="max-w-md w-full space-y-6 p-8 bg-white/80 backdrop-blur-md rounded-2xl shadow-xl">
        <div>
            <a href="/" class="flex justify-center mb-6">
                <span class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">LorPHP</span>
            </a>
            <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
                <?php echo htmlspecialchars($title ?? 'Create your account'); ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account? 
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                    Sign in
                </a>
            </p>
        </div>

        <?php if (isset($form)): ?>
            <div class="mt-8 bg-white/50 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-white/20">
                <?php
                // Display any form errors using our new error component
                echo $form->renderErrors();
                
                // Render the complete form
                echo $form->render();
                ?>
            </div>
            <div class="text-center text-sm text-gray-600">
                By creating an account, you agree to our
                <a href="/terms" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Terms of Service</a>
                and
                <a href="/privacy" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Privacy Policy</a>
            </div>
        <?php else: ?>
            <?php echo $this->renderPartial('partials/components/error-alert', [
                'message' => 'Error: Registration form not available',
                'type' => 'error'
            ]); ?>
        <?php endif; ?>
    </div>
</div>
