<?php
/**
 * Activity item component for dashboard
 */
?>
<li class="relative pb-8">
    <div class="relative flex items-center space-x-3">
        <div>
            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                <!-- Avatar or icon -->
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm text-gray-500">
                <?php if (isset($link)): ?>
                    <a href="<?php echo $link; ?>" class="font-medium text-gray-900"><?php echo $title; ?></a>
                <?php else: ?>
                    <span class="font-medium text-gray-900"><?php echo $title; ?></span>
                <?php endif; ?>
                <?php echo $description; ?>
            </p>
            <p class="text-sm text-gray-500"><?php echo $time; ?></p>
        </div>
    </div>
</li>
