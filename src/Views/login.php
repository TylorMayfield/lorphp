<?php
/**
 * Login page view
 * A simple, clean login page that uses the auth-container layout
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
                <?php echo htmlspecialchars($title ?? 'Welcome back'); ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                New to LorPHP? 
                <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                    Create an account
                </a>
            </p>
        </div>

        <?php if (isset($form)): ?>
            <div class="mt-8 bg-white/50 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-white/20">
                <?php 
                $formErrors = $form->getErrors();
                if (!empty($formErrors)): 
                    $error = $formErrors['form'] ?? reset($formErrors);
                ?>
                    <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-400">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php echo $form->render(); ?>
            </div>
            <div class="text-center text-sm text-gray-600">
                By signing in, you agree to our
                <a href="/terms" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Terms of Service</a>
                and
                <a href="/privacy" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Privacy Policy</a>
            </div>
        <?php else: ?>
            <div class="rounded-md bg-red-50 p-4 mb-4 border border-red-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-red-700">
                            <p>Error: Login form not available</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


