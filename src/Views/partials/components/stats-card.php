<?php
/**
 * Stats card component for dashboard
 */
?>
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="<?php echo $bgColor ?? 'bg-indigo-500'; ?> rounded-md p-3">
                    <!-- Icon placeholder -->
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate"><?php echo $label; ?></dt>
                    <dd class="text-3xl font-semibold text-gray-900"><?php echo $value; ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
