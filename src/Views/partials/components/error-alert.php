<?php
/**
 * Error Alert Component
 * @param string $message The error message to display
 * @param string $type The type of error (error, warning, info)
 */
$type = $type ?? 'error';
$bgColor = $type === 'error' ? 'bg-red-50' : ($type === 'warning' ? 'bg-yellow-50' : 'bg-blue-50');
$textColor = $type === 'error' ? 'text-red-700' : ($type === 'warning' ? 'text-yellow-700' : 'text-blue-700');
$borderColor = $type === 'error' ? 'border-red-400' : ($type === 'warning' ? 'border-yellow-400' : 'border-blue-400');
$iconColor = $type === 'error' ? 'text-red-400' : ($type === 'warning' ? 'text-yellow-400' : 'text-blue-400');
?>

<div class="rounded-md <?php echo $bgColor; ?> p-4 mb-4 border <?php echo $borderColor; ?>">
    <div class="flex">
        <div class="flex-shrink-0">
            <!-- Alert Icon -->
            <svg class="h-5 w-5 <?php echo $iconColor; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <?php if ($type === 'error'): ?>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                <?php elseif ($type === 'warning'): ?>
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                <?php else: ?>
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                <?php endif; ?>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium <?php echo $textColor; ?>">
                <?php echo htmlspecialchars($message); ?>
            </h3>
            <?php if (isset($details) && !empty($details)): ?>
            <div class="mt-2 text-sm <?php echo $textColor; ?> opacity-90">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($details as $detail): ?>
                    <li><?php echo htmlspecialchars($detail); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
