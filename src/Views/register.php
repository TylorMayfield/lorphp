<?php
/**
 * Registration page view
 * A simple, clean registration page that uses the auth-container layout
 */

// Use the base layout
$this->setLayout('base');
?>
<div class="min-h-screen flex items-center justify-center pt-24 pb-8">
    <div class="max-w-md w-full space-y-6 p-8 bg-[#18181b]/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-[#27272a] relative overflow-hidden">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-blue-500/10 opacity-50"></div>
        
        <div class="relative">
            <a href="/" class="flex justify-center mb-8 group">
                <span class="text-4xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 bg-clip-text text-transparent animate-gradient-x relative">
                    LorPHP
                    <span class="absolute inset-0 bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 opacity-20 blur-sm transition-opacity duration-300 group-hover:opacity-40"></span>
                </span>
            </a>
            <h2 class="text-center text-3xl font-bold tracking-tight text-[#fafafa] mb-3">
                <?php echo htmlspecialchars($title ?? 'Create your account'); ?>
            </h2>
            <p class="text-center text-sm text-[#a1a1aa]">
                Already have an account? 
                <a href="/login" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                    Sign in
                </a>
            </p>
        </div>

        <?php if (isset($form)): ?>
            <div class="mt-8 relative">
                <?php
                // Display any form errors using our new error component
                $formErrors = $form->getErrors();
                if (!empty($formErrors)): 
                    $error = $formErrors['form'] ?? reset($formErrors);
                ?>
                    <div class="rounded-xl bg-red-500/10 border border-red-500/20 p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-400"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php echo $form->render(); ?>
            </div>
            <div class="text-center text-sm text-[#71717a] mt-6">
                By creating an account, you agree to our
                <a href="/terms" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Terms of Service</a>
                and
                <a href="/privacy" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Privacy Policy</a>
            </div>
        <?php else: ?>
            <div class="rounded-xl bg-red-500/10 border border-red-500/20 p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-red-400">
                            <p>Error: Registration form not available</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
