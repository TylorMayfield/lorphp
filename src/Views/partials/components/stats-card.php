<?php
/**
 * Stats card component for dashboard
 */
?>
<div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] overflow-hidden rounded-xl shadow-xl relative group">
    <!-- Gradient background effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 transition-opacity duration-300 group-hover:opacity-70"></div>
    
    <div class="p-5 relative">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="<?php echo $bgColor ?? 'bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400'; ?> rounded-xl p-3 shadow-lg">
                    <!-- Icon placeholder -->
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-[#a1a1aa] truncate"><?php echo $label; ?></dt>
                    <dd class="text-3xl font-semibold text-[#fafafa] group-hover:text-white transition-colors"><?php echo $value; ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
