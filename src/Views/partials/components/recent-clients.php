<?php
/**
 * Recent clients component for dashboard
 */
?>
<div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-5 sm:p-6 lg:p-8 relative">
    <!-- Gradient background effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 rounded-xl"></div>
    
    <div class="relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-[#fafafa]">Recent Clients</h3>
            <a href="/clients/create" 
               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                Add Client
            </a>
        </div>
        <div class="overflow-x-auto">
            <?php 
            ob_start();
            foreach ($clients as $client): ?>
                <tr>
                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-[#fafafa]"><?php echo htmlspecialchars($client->name); ?></div>
                        <div class="hidden sm:block text-sm text-[#a1a1aa]"><?php echo htmlspecialchars($client->email); ?></div>
                    </td>
                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                        <?php echo $this->ui()->badge(['type' => $client->status === 'active' ? 'success' : 'default'])
                            ->withSlot('default', ucfirst($client->status)); ?>
                    </td>
                    <td class="hidden sm:table-cell px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-[#71717a]">
                        <?php echo $client->last_contact_date ? date('M j, Y', strtotime($client->last_contact_date)) : 'Never'; ?>
                    </td>
                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">
                        <a href="/clients/<?php echo $client->id; ?>" class="text-indigo-400 hover:text-indigo-300 transition-colors">View</a>
                    </td>
                </tr>
            <?php endforeach;
            $tableContent = ob_get_clean();
            
            echo $this->ui()->table(['headers' => ['Name', 'Status', 'Last Contact', 'Actions']])
                ->withSlot('default', $tableContent); ?>
        </div>
    </div>
</div>
