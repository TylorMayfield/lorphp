<?php
/**
 * Auth form container partial
 */
?>
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg auth-container">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900"><?php echo $title; ?></h2>
            <?php if (isset($subtitle)): ?>
                <p class="mt-2 text-center text-sm text-gray-600">
                    <?php echo $subtitle; ?>
                    <?php if (isset($linkText) && isset($linkUrl)): ?>
                        <a href="<?php echo $linkUrl; ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                            <?php echo $linkText; ?>
                        </a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <?php 
        // Error display is now handled by the form itself
        echo $content; 
        ?>
    </div>
</div>
