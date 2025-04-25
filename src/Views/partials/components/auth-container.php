<?php
/**
 * Auth form container partial
 */
error_log("Auth container rendering with content length: " . strlen($content ?? ''));
?>
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg">
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

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>
    </div>
</div>
