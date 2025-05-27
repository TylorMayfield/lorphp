<?php
/**
 * Packages Component
 * Displays a table of available packages
 */
?>
<div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-4 sm:p-6 lg:p-8 relative">
    <!-- Gradient background effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 rounded-xl"></div>
    
    <div class="relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-[#fafafa]">Packages</h3>
            <a href="/packages/create" 
               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                Add Package
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <?php 
            ob_start();
            foreach ($packages ?? [] as $package): ?>
                <tr>
                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-[#fafafa]"><?php echo htmlspecialchars($package->name); ?></div>
                        <div class="text-sm text-[#a1a1aa]">$<?php echo number_format($package->price, 2); ?></div>
                    </td>
                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">
                        <a href="/packages/<?php echo $package->id; ?>" class="text-indigo-400 hover:text-indigo-300 transition-colors">View</a>
                    </td>
                </tr>
            <?php endforeach;
            $packageTableContent = ob_get_clean();
            
            echo $this->ui()->table(['headers' => ['Package Details', 'Actions']])
                ->withSlot('default', $packageTableContent); ?>
        </div>
    </div>
</div>
