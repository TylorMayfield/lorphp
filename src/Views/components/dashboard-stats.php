<?php
/**
 * Dashboard Stats Component
 * Displays a grid of stats cards with customizable icons and colors
 */
?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
    <?php foreach ($stats as $stat): ?>
        <div class="relative overflow-hidden rounded-xl <?php echo $stat['color']; ?> p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80"><?php echo htmlspecialchars($stat['label']); ?></p>
                    <p class="mt-2 text-2xl font-bold text-white"><?php echo htmlspecialchars($stat['value']); ?></p>
                </div>
                <div class="rounded-lg bg-white/10 p-2.5">
                    <?php echo $stat['icon']; ?>
                </div>
            </div>
            <!-- Decorative background pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="h-48 w-48 -translate-y-6 transform text-white/10" fill="currentColor" viewBox="0 0 100 100">
                    <path d="M100 34.2c-.4-2.6-3.3-4-5.3-5.3-3.6-2.4-7.1-4.7-10.7-7.1-3.6-2.4-7.5-4.4-12.2-4.4-4.7 0-8.6 2-12.2 4.4-3.6 2.4-7.1 4.7-10.7 7.1-2 1.3-4.9 2.7-5.3 5.3-.4 2.6 2 4.7 3.3 7.1 2.4 3.6 4.7 7.1 7.1 10.7 2.4 3.6 4.4 7.5 4.4 12.2 0 4.7-2 8.6-4.4 12.2-2.4 3.6-4.7 7.1-7.1 10.7-1.3 2-3.7 4.1-3.3 6.7.4 2.6 3.3 4 5.3 5.3 3.6 2.4 7.1 4.7 10.7 7.1 3.6 2.4 7.5 4.4 12.2 4.4 4.7 0 8.6-2 12.2-4.4 3.6-2.4 7.1-4.7 10.7-7.1 2-1.3 4.9-2.7 5.3-5.3.4-2.6-2-4.7-3.3-7.1-2.4-3.6-4.7-7.1-7.1-10.7-2.4-3.6-4.4-7.5-4.4-12.2 0-4.7 2-8.6 4.4-12.2 2.4-3.6 4.7-7.1 7.1-10.7 1.3-2 3.7-4.1 3.3-6.7z" />
                </svg>
            </div>
        </div>
    <?php endforeach; ?>
</div>
