<?php
/**
 * Dashboard page view - Auth required
 */
$this->setLayout('base');
?>
<!-- Add padding to account for fixed navbar -->
<div class="min-h-screen bg-[#09090b] pt-16">
    <div class="max-w-[95rem] mx-auto py-8 px-4 sm:px-6 lg:px-8 xl:px-10 space-y-8">
        <?php
        // Prepare stats data for the component
        $statsData = [
            [
                'label' => 'Total Clients',
                'value' => $stats['totalClients'],
                'color' => 'bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400',
                'icon' => '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>'
            ],
            [
                'label' => 'Active Clients',
                'value' => $stats['activeClients'],
                'color' => 'bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400',
                'icon' => '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            ],
            [
                'label' => 'Total Packages',
                'value' => $stats['totalPackages'],
                'color' => 'bg-gradient-to-br from-amber-400 via-orange-400 to-yellow-400',
                'icon' => '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>'
            ],
            [
                'label' => 'Recent Contacts',
                'value' => $stats['recentContacts'],
                'color' => 'bg-gradient-to-br from-blue-400 via-cyan-400 to-sky-400',
                'icon' => '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>'
            ],
            [
                'label' => 'Organization Users',
                'value' => $stats['organizationUsers'],
                'color' => 'bg-gradient-to-br from-purple-400 via-fuchsia-400 to-pink-400',
                'icon' => '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>'
            ]
        ];        // Render the main dashboard components
        $this->partial('components/dashboard-stats', ['stats' => $statsData]);
        ?>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 xl:gap-10">
            <!-- Recent Clients -->
            <div class="lg:col-span-2">
                <?php $this->partial('components/recent-clients', ['clients' => $recentClients]); ?>
            </div>
            
            <!-- Recent Activity -->
            <div class="lg:col-span-1">
                <?php $this->partial('components/activity-feed', ['contacts' => $recentContacts]); ?>
            </div>
            
            <!-- Packages -->
            <div class="col-span-3">
                <?php $this->partial('components/packages', ['packages' => $recentPackages]); ?>
            </div>
        </div>
    </div>
</div>
