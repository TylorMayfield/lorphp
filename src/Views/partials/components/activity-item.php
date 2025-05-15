<?php
/**
 * Activity item component for dashboard
 */
?>
<li class="relative pb-8">
    <div class="relative flex items-center space-x-3">
        <div>
            <span class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 flex items-center justify-center ring-8 ring-[#18181b] shadow-md">
                <!-- Avatar or icon -->
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm text-[#a1a1aa]">
                <?php if (isset($link)): ?>
                    <a href="<?php echo $link; ?>" class="font-medium text-[#fafafa] hover:text-indigo-400 transition-colors"><?php echo $title; ?></a>
                <?php else: ?>
                    <span class="font-medium text-[#fafafa]"><?php echo $title; ?></span>
                <?php endif; ?>
                <span class="text-[#71717a]"><?php echo $description; ?></span>
            </p>
            <p class="text-sm text-[#52525b] mt-1"><?php echo $time; ?></p>
        </div>
    </div>
</li>
