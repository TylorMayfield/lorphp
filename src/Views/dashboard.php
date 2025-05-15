<?php
/**
 * Dashboard page view - Auth required
 */
$this->setLayout('base');
?>    
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 dashboard-content">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <?php echo $this->ui()->statsCard()
                ->label('Total Clients')
                ->value($stats['totalClients'])
                ->color('bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400')
                ->icon('<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>'); ?>

            <?php echo $this->ui()->statsCard()
                ->label('Active Clients')
                ->value($stats['activeClients'])
                ->color('bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400')
                ->icon('<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>'); ?>
                
            <?php echo $this->ui()->statsCard()
                ->label('Total Packages')
                ->value($stats['totalPackages'])
                ->color('bg-gradient-to-br from-amber-400 via-orange-400 to-yellow-400')
                ->icon('<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>'); ?>

            <?php echo $this->ui()->statsCard()
                ->label('Recent Contacts')
                ->value($stats['recentContacts'])
                ->color('bg-gradient-to-br from-blue-400 via-cyan-400 to-sky-400')
                ->icon('<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>'); ?>

            <?php 
            $organization = $user->getOrganization();
            echo $this->ui()->statsCard()
                ->label('Organization Users')
                ->value($stats['organizationUsers'])
                ->color('bg-gradient-to-br from-purple-400 via-fuchsia-400 to-pink-400')
                ->icon('<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>'); ?>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Clients -->
            <div class="lg:col-span-2">
                <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-6 relative">
                    <!-- Gradient background effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                    
                    <div class="relative">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-[#fafafa]">Recent Clients</h3>
                            <a href="/clients/create" 
                               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                                Add Client
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <?php 
                            ob_start();
                            foreach ($recentClients as $client): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-[#fafafa]"><?php echo htmlspecialchars($client->name); ?></div>
                                        <div class="text-sm text-[#a1a1aa]"><?php echo htmlspecialchars($client->email); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo $this->ui()->badge(['type' => $client->status === 'active' ? 'success' : 'default'])
                                            ->withSlot('default', ucfirst($client->status)); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[#71717a]">
                                        <?php echo $client->last_contact_date ? date('M j, Y', strtotime($client->last_contact_date)) : 'Never'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-1">
                <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-6 relative">
                    <!-- Gradient background effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                    
                    <div class="relative">
                        <h3 class="text-lg font-medium text-[#fafafa] mb-4">Recent Activity</h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <?php foreach ($recentContacts as $contact):
                                ?>
                                    <?php echo $this->ui()->activityItem([
                                        'title' => $contact['user_name'],
                                        'description' => "Contacted {$contact['client_name']} - {$contact['type']}",
                                        'time' => date('M j, Y', strtotime($contact['contact_date'])),
                                        'link' => "/clients/{$contact['client_id']}"
                                    ])->withSlot('icon', '<svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>'); ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packages -->
            <div class="col-span-3">
                <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-6 relative">
                    <!-- Gradient background effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                    
                    <div class="relative">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-[#fafafa]">Packages</h3>
                            <a href="/packages/create" 
                               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                                Add Package
                            </a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <?php 
                            ob_start();
                            foreach ($recentPackages ?? [] as $package): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-[#fafafa]"><?php echo htmlspecialchars($package->name); ?></div>
                                        <div class="text-sm text-[#a1a1aa]">$<?php echo number_format($package->price, 2); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
            </div>
        </div>
    </div>
</div>
</body>
</html>
